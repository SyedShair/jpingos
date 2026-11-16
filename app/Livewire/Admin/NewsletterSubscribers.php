<?php

namespace App\Livewire\Admin;

use App\Models\NewsletterSubscriber;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscribers extends Component
{
    use WithPagination;

    public string $search = '';

    /** '' = everyone, 'active', or 'unsubscribed'. */
    public string $statusFilter = '';

    protected $queryString = ['search', 'statusFilter'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Deliberately one-way: an admin can take someone OFF the list, but
     * there is no "re-activate" button. Putting an unsubscribed person
     * back on a marketing list without them asking would defeat the
     * point of unsubscribing; they can re-subscribe themselves from the
     * footer form, which the storefront controller handles.
     */
    public function unsubscribe(int $id): void
    {
        NewsletterSubscriber::whereKey($id)->update([
            'is_active'       => false,
            'unsubscribed_at' => now(),
        ]);

        session()->flash('status-message', 'Subscriber unsubscribed.');
    }

    public function delete(int $id): void
    {
        NewsletterSubscriber::whereKey($id)->delete();

        session()->flash('status-message', 'Subscriber deleted.');
    }

    /**
     * CSV of whatever the current search/status filter shows.
     */
    public function export()
    {
        $query = $this->filteredQuery();
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Email', 'Status', 'Subscribed at', 'Unsubscribed at', 'Source', 'IP address']);

            foreach ($query->cursor() as $subscriber) {
                fputcsv($out, [
                    $this->csvSafe($subscriber->email),
                    $subscriber->is_active ? 'Active' : 'Unsubscribed',
                    optional($subscriber->subscribed_at)->format('Y-m-d H:i:s'),
                    optional($subscriber->unsubscribed_at)->format('Y-m-d H:i:s'),
                    $subscriber->source,
                    $subscriber->ip_address,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Email addresses are user-supplied, and "=", "+", "-" and "@" are all
     * legal at the start of an address. Opened in Excel, such a value
     * would run as a formula. A leading apostrophe makes it plain text.
     */
    protected function csvSafe(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }

    protected function filteredQuery()
    {
        return NewsletterSubscriber::query()
            ->when($this->search !== '', function ($q) {
                $q->where('email', 'like', '%' . addcslashes($this->search, '%_\\') . '%');
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'unsubscribed', fn ($q) => $q->where('is_active', false))
            ->latest('id');
    }

    public function render()
    {
        return view('storefront.livewire.admin.newsletter-subscribers', [
            'subscribers'       => $this->filteredQuery()->paginate(20),
            'totalCount'        => NewsletterSubscriber::count(),
            'activeCount'       => NewsletterSubscriber::where('is_active', true)->count(),
            'unsubscribedCount' => NewsletterSubscriber::where('is_active', false)->count(),
            'newThisWeek'       => NewsletterSubscriber::where('subscribed_at', '>=', now()->subDays(7))->count(),
        ]);
    }
}