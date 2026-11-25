<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

/**
 * Restaurant order receipt (ESC/POS), laid out like the print9.pdf sample:
 *
 *   shop name · order # / type · date / time · "Order Invoice"
 *   customer block · Item/Price/Qty/Total table · sub-totals · Grand Total
 *   boxed PAYMENT block with COLLECT amount · thank-you · phone · QR code
 *
 * Save as app/Services/OrderReceiptPrinter.php
 */
class OrderReceiptPrinter
{
    /** Characters per line in Font A by paper width (mm). If an 80mm roll leaves a gap on the right, change 42 to 48. */
    private const COLS = [58 => 32, 80 => 42];

    /** Set true to print item rows in double height (easier to read in a kitchen). */
    private const BIG_ITEMS = false;

    /** UK currency symbol. In Windows-1252, £ is byte 0xA3. */
    private const CURRENCY = '£';

    /**
     * Star TSP100III ESC/POS code table:
     * 32 (0x20) = Windows-1252.
     * The printer must be configured for ESC/POS emulation.
     */
    private const STAR_CP1252_TABLE = 32;

    private Printer $p;
    private int $cols;    // characters per line, Font A
    private int $colsB;   // characters per line, Font B (smaller text)

    public function print(Order $order, int $paperWidth = 80, ?string $share = null): void
    {
        $order->loadMissing(['items', 'latestPayment']);

        $this->cols  = self::COLS[$paperWidth] ?? 42;
        $this->colsB = intdiv($this->cols * 4, 3);   // Font B is 4/3 as dense as Font A
        $share ??= config('restaurant.printer_share', 'StarTSP');

        // Keep the simple profile to avoid unsupported automatic code-page conversion.
        // We select Star's CP1252 table directly and send pre-encoded bytes with textRaw().
        $profile   = CapabilityProfile::load('simple');
        $connector = new WindowsPrintConnector($share);
        $this->p   = new Printer($connector, $profile);

        try {
            // Star ESC/POS command: ESC GS t n, with n=32 (Windows-1252).
            $this->p->getPrintConnector()->write("\x1B\x1D\x74" . chr(self::STAR_CP1252_TABLE));

            $this->render($order);
            $this->p->feed(3);
            $this->p->cut();
        } finally {
            $this->p->close();
        }
    }

    private function render(Order $order): void
    {
        $p          = $this->p;
        $c          = $this->cols;
        $setting    = Setting::current();
        $isDelivery = $order->order_type === 'delivery';
        $isDining   = $order->order_type === 'dining';
        $typeLabel  = $isDining ? 'Dining' : ucfirst($order->order_type);
        $payment    = $order->latestPayment;
        $phone      = $setting->phone ?: config('restaurant.phone');

        /* =====================================================
           HEADER — shop name, optional address / VAT
        ===================================================== */
        $shop = ltrim(trim((string) ($setting->site_name ?: config('app.name', 'Restaurant'))), '0');
        $shop = trim($shop) !== '' ? trim($shop) : 'Restaurant';

        // Everything is centred in software (padding spaces), so it stays centred
        // even if the printer ignores its own justification command.
        $p->setJustification(Printer::JUSTIFY_LEFT);

        $doubleWide = mb_strlen($shop) * 2 <= $c;
        $p->setEmphasis(true);
        $p->selectPrintMode(
            $doubleWide
                ? Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH
                : Printer::MODE_DOUBLE_HEIGHT
        );
        foreach (explode("\n", wordwrap($shop, $doubleWide ? intdiv($c, 2) : $c, "\n", true)) as $line) {
            $this->t($this->center($line, $c, $doubleWide ? 2 : 1) . "\n");
        }
        $p->selectPrintMode();
        $p->setEmphasis(false);

        $this->small();
        if ($setting->address) {
            foreach (explode("\n", wordwrap((string) $setting->address, $this->colsB, "\n", true)) as $line) {
                $this->t($this->center($line, $this->colsB) . "\n");
            }
        }
        if ($setting->vat_number) {
            $this->t($this->center('VAT No: ' . $setting->vat_number, $this->colsB) . "\n");
        }
        $this->normal();

        $this->t("\n");

        /* =====================================================
           ORDER META — order # + type, date + time
        ===================================================== */
        $p->setEmphasis(true);
        $this->t($this->row('Order #' . $order->order_number, $typeLabel));
        $p->setEmphasis(false);
        $this->t($this->row('Date ' . $order->created_at->format('d/m/Y'), $order->created_at->format('H:i')));

        if ($order->is_pre_order && $order->pre_order_date) {
            $when = Carbon::parse($order->pre_order_date)->format('D d/m/Y');
            if ($order->pre_order_time) {
                $when .= ' ' . Carbon::parse($order->pre_order_time)->format('H:i');
            }
            $this->t($this->rule('-'));
            $p->setEmphasis(true);
            $this->t($this->row($isDining ? 'TABLE BOOKED' : 'SCHEDULED', $when));
            $p->setEmphasis(false);
        }

        /* =====================================================
           "O r d e r   I n v o i c e" — spaced-out title
        ===================================================== */
        $this->t("\n");
        $p->setEmphasis(true);
        $this->t($this->center($this->spaced('Order Invoice'), $c) . "\n");
        $p->setEmphasis(false);
        $this->t("\n");

        /* =====================================================
           CUSTOMER
        ===================================================== */
        $p->setEmphasis(true);
        $this->t(strtoupper($typeLabel) . "\n");
        $p->setEmphasis(false);

        $this->t(trim($order->first_name . ' ' . $order->last_name) . "\n");
        $this->t($order->phone . "\n");

        if ($isDelivery) {
            $addr = $order->address . ($order->apartment ? ', ' . $order->apartment : '');
            $this->t($this->wrap($addr, $c));
            $this->t(trim($order->city . ' ' . strtoupper((string) $order->postcode)) . "\n");
        }
        if ($order->notes) {
            $this->t("\n");
            $p->setEmphasis(true);
            $this->t($this->wrap('Note: ' . $order->notes, $c));
            $p->setEmphasis(false);
        }

        /* =====================================================
           ITEMS — Item | Price | Qty | Total
        ===================================================== */
        $this->t($this->rule('-'));

        $showPrice = $c >= 40;                         // 58mm paper: drop the Price column
        $priceW = 7;
        $qtyW   = 3;
        $totW   = 8;
        $nameW  = $c - $qtyW - $totW - 2 - ($showPrice ? $priceW + 1 : 0);

        $p->setEmphasis(true);
        $this->t(
            $this->padRight('Item', $nameW)
            . ($showPrice ? ' ' . $this->lpad('Price', $priceW) : '')
            . ' ' . $this->lpad('Qty', $qtyW)
            . ' ' . $this->lpad('Total', $totW) . "\n"
        );
        $p->setEmphasis(false);
        $this->t($this->rule('='));

        foreach ($order->items as $item) {
            if (self::BIG_ITEMS) {
                $p->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            }

            $nameLines = explode("\n", wordwrap((string) $item->name, $nameW, "\n", true));

            $this->t(
                $this->padRight((string) array_shift($nameLines), $nameW)
                . ($showPrice ? ' ' . $this->lpad($this->money($item->unit_price), $priceW) : '')
                . ' ' . $this->lpad((string) $item->quantity, $qtyW)
                . ' ' . $this->lpad($this->money($item->line_total), $totW) . "\n"
            );
            foreach ($nameLines as $extra) {
                $this->t($extra . "\n");
            }

            if (self::BIG_ITEMS) {
                $p->selectPrintMode();
            }

            // Deal + options in the smaller font, like the sample
            $hasSub = $item->deal_name || ! empty($item->options);
            if ($hasSub) {
                $this->small();

                if ($item->deal_name) {
                    $this->t($this->wrap('  Deal: ' . $item->deal_name, $this->colsB));
                }
                foreach ($item->options ?? [] as $option) {
                    $delta = (float) ($option['price_delta'] ?? 0);
                    $this->t($this->row(
                        '  + ' . ($option['name'] ?? ''),
                        $delta > 0 ? $this->money($delta) : '',
                        $this->colsB
                    ));
                }

                $this->normal();
            }
        }

        /* =====================================================
           TOTALS — Sub-Total, Discount, Delivery Fee, Grand Total
        ===================================================== */
        $this->t("\n");
        $this->t($this->row('Sub-Total', $this->money($order->subtotal)));

        if ((float) $order->discount_total > 0) {
            $this->t($this->row(
                'Discount' . ($order->coupon_code ? ' (' . $order->coupon_code . ')' : ''),
                '-' . $this->money($order->discount_total)
            ));
        }
        if ($isDelivery) {
            $fee = (float) $order->delivery_fee;
            $this->t($this->row('Delivery Fee', $fee > 0 ? $this->money($fee) : 'FREE'));
        }

        $this->t($this->dash());
        $p->setEmphasis(true);
        $p->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
        $this->t($this->row('Grand Total', $this->money($order->total)));
        $p->selectPrintMode();
        $p->setEmphasis(false);
        $this->t("\n");

        /* =====================================================
           PAYMENT — boxed block
        ===================================================== */
        if ($payment) {
            $labels = ['cod' => 'CASH ON DELIVERY', 'card' => 'CARD'];

            $this->t($this->boxEdge());

            $p->setEmphasis(true);
            $this->t($this->box('PAYMENT'));
            $p->setEmphasis(false);

            $this->t($this->boxRow('Method', $labels[$payment->method] ?? strtoupper((string) $payment->method)));
            $this->t($this->boxRow('Status', strtoupper((string) $payment->status)));

            if ($payment->transaction_id) {
                $this->t($this->boxRow('Txn', (string) $payment->transaction_id));
            }
            if ($payment->paid_at) {
                $this->t($this->boxRow('Paid', $payment->paid_at->format('d/m/Y H:i')));
            }

            $collect = $payment->method === 'cod' && $payment->status === 'pending';
            $isPaid  = $payment->status === 'paid';

            if ($collect || $isPaid) {
                $this->t($this->boxDash());
                $p->setEmphasis(true);
                $p->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
                $this->t($this->boxCenter($collect ? 'COLLECT ' . $this->money($order->total) : 'PAID'));
                $p->selectPrintMode();
                $p->setEmphasis(false);
            }

            $this->t($this->boxEdge());
            $this->t("\n");
        }

        /* =====================================================
           FOOTER — thank-you, phone, QR code
        ===================================================== */
        $p->setEmphasis(true);
        $this->t($this->center($isDining ? 'Enjoy your meal.' : 'Thank you for your order.', $c) . "\n");
        $p->setEmphasis(false);

        if ($phone) {
            $this->small();
            $this->t($this->center('Tel: ' . $phone, $this->colsB) . "\n");
            $this->normal();
        }

        // QR code (skipped silently if the printer/mode doesn't support it).
        // It is the only part that uses the printer's own centring.
        try {
            $p->feed(1);
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->qrCode(
                route('storefront.order.confirmation', $order->order_number),
                Printer::QR_ECLEVEL_M,
                6
            );
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $this->small();
            $this->t($this->center('Scan to view your order', $this->colsB) . "\n");
            $this->normal();
        } catch (\Throwable $e) {
            report($e);
            $p->setJustification(Printer::JUSTIFY_LEFT);
        }
    }

    /**
     * Convert UTF-8 application text to Windows-1252 and send it without
     * escpos-php attempting a second encoding conversion.
     *
     * Supports normal English names and common Western European accents.
     * Characters that cannot be represented in CP1252 are transliterated.
     */
    private function t(string $s): void
    {
        if ($s === '') {
            return;
        }

        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);

        if ($encoded === false) {
            throw new \RuntimeException('Receipt text could not be converted from UTF-8 to Windows-1252.');
        }

        $this->p->textRaw($encoded);
    }

    /* ---------- font helpers ---------- */

    private function small(): void
    {
        $this->p->setFont(Printer::FONT_B);
    }

    private function normal(): void
    {
        $this->p->setFont(Printer::FONT_A);
    }

    /* ---------- layout helpers ---------- */

    private function money($amount): string
    {
        return self::CURRENCY . number_format((float) $amount, 2);
    }

    private function rule(string $char): string
    {
        return str_repeat($char, $this->cols) . "\n";
    }

    /** "- - - - -" dashed line, full width. */
    private function dash(): string
    {
        return substr(str_repeat('- ', (int) ceil($this->cols / 2)), 0, $this->cols) . "\n";
    }

    private function padRight(string $s, int $w): string
    {
        return $s . str_repeat(' ', max(0, $w - mb_strlen($s)));
    }

    private function lpad(string $s, int $w): string
    {
        return str_repeat(' ', max(0, $w - mb_strlen($s))) . $s;
    }

    /**
     * Centre text on a line using padding spaces (does not rely on the printer's
     * justification command). $width = characters per line in the current font;
     * $scale = 2 when the line is printed double-width (every character, including
     * the padding spaces, is then twice as wide).
     */
    private function center(string $text, ?int $width = null, int $scale = 1): string
    {
        $width ??= $this->cols;
        $pad = max(0, intdiv($width - mb_strlen($text) * $scale, 2 * $scale));

        return str_repeat(' ', $pad) . $text;
    }

    /** "O r d e r   I n v o i c e" */
    private function spaced(string $text): string
    {
        return implode('   ', array_map(
            fn (string $word) => implode(' ', mb_str_split($word)),
            explode(' ', $text)
        ));
    }

    /** Left text + right text on one full-width line. */
    private function row(string $left, string $right, ?int $width = null): string
    {
        $width ??= $this->cols;
        $space = $width - mb_strlen($left) - mb_strlen($right);

        if ($space < 1) {
            $left  = mb_substr($left, 0, max(0, $width - mb_strlen($right) - 1));
            $space = 1;
        }

        return $left . str_repeat(' ', $space) . $right . "\n";
    }

    private function wrap(string $text, int $width): string
    {
        $lines = explode("\n", wordwrap($text, max(1, $width), "\n", true));

        return implode("\n", $lines) . "\n";
    }

    /* ---------- payment box helpers ---------- */

    /** +------------------------------------+ */
    private function boxEdge(): string
    {
        return '+' . str_repeat('-', $this->cols - 2) . "+\n";
    }

    /** |  - - - - - - - - - - - - - - - - - |  */
    private function boxDash(): string
    {
        $inner = $this->cols - 4;

        return '| ' . substr(str_repeat('- ', (int) ceil($inner / 2)), 0, $inner) . " |\n";
    }

    private function box(string $inner): string
    {
        $w = $this->cols - 4;

        return '| ' . $this->padRight(mb_substr($inner, 0, $w), $w) . " |\n";
    }

    private function boxRow(string $left, string $right): string
    {
        $w     = $this->cols - 4;
        $space = max(1, $w - mb_strlen($left) - mb_strlen($right));

        return $this->box($left . str_repeat(' ', $space) . $right);
    }

    private function boxCenter(string $text): string
    {
        $w   = $this->cols - 4;
        $pad = max(0, intdiv($w - mb_strlen($text), 2));

        return $this->box(str_repeat(' ', $pad) . $text);
    }
}