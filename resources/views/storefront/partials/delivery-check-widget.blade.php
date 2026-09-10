{{-- resources/views/storefront/partials/delivery-check-slider.blade.php
     Include ONCE, site-wide, in your main layout (e.g. right before
     </body> in layouts/app.blade.php) — it's a fixed floating trigger +
     slide-in panel, not something you place inline in page content. --}}

<button type="button" class="dcs-trigger" id="dcs-trigger" aria-label="Check delivery to your postcode">
    <i class="fa fa-motorcycle" aria-hidden="true"></i>
    <span class="dcs-trigger-text">Delivery Check</span>
</button>

<div class="dcs-backdrop" id="dcs-backdrop"></div>

<aside class="dcs-panel" id="dcs-panel" aria-hidden="true">

    <div class="dcs-panel-header">
        <h3>Delivery Check</h3>
        <button type="button" class="dcs-close" id="dcs-close" aria-label="Close">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <div class="dcs-panel-body">

        <p class="dcs-intro">Enter your postcode and we'll tell you instantly if you're in our delivery area.</p>

        <div class="dcs-input-row">
            <i class="fa fa-map-marker-alt dcs-input-icon" aria-hidden="true"></i>
            <input
                type="text"
                id="dcs-postcode"
                placeholder="e.g. SW1A 1AA"
                autocomplete="postal-code"
            >
        </div>

        <button type="button" class="dcs-submit" id="dcs-submit">
            <span class="dcs-submit-label">Check Availability</span>
            <span class="dcs-spinner" aria-hidden="true"></span>
        </button>

        <div id="dcs-result" class="dcs-result" role="status" aria-live="polite"></div>

    </div>

</aside>

<style>
    .dcs-trigger {
        position: fixed;
        left: 24px;
        bottom: 24px;
        z-index: 1200;
        display: flex;
        align-items: center;
        gap: 10px;
        height: 54px;
        padding: 0 20px 0 18px;
        background: #D12026;
        color: #fff;
        border: none;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: .02em;
        box-shadow: 0 10px 26px rgba(209, 32, 38, .4);
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dcs-trigger::before,
    .dcs-trigger::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #D12026;
        z-index: -1;
        animation: dcs-blink 2.2s ease-out infinite;
    }

    .dcs-trigger::after {
        animation-delay: .6s;
    }

    @keyframes dcs-blink {
        0% {
            opacity: .55;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(1.55);
        }
    }

    .dcs-trigger:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(209, 32, 38, .5);
    }

    .dcs-trigger i {
        font-size: 18px;
    }

    .dcs-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        z-index: 1300;
        opacity: 0;
        pointer-events: none;
        transition: opacity .3s ease;
    }

    .dcs-backdrop.open {
        opacity: 1;
        pointer-events: auto;
    }

    .dcs-panel {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 400px;
        max-width: 100vw;
        background: linear-gradient(165deg, #1a1214 0%, #100c0d 55%, #0c0a0b 100%);
        color: #fff;
        z-index: 1400;
        display: flex;
        flex-direction: column;
        transform: translateX(-100%);
        transition: transform .38s cubic-bezier(.32, .72, 0, 1);
        box-shadow: 12px 0 40px rgba(0, 0, 0, .5);
    }

    .dcs-panel.open {
        transform: translateX(0);
    }

    .dcs-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 24px;
        border-bottom: 1px solid #262626;
        flex-shrink: 0;
    }

    .dcs-panel-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }

    .dcs-close {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #262626;
        color: #fff;
        border: none;
        font-size: 15px;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }

    .dcs-close:hover {
        background: #D12026;
        transform: rotate(90deg);
    }

    .dcs-panel-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }

    .dcs-intro {
        color: #a0a0a0;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 22px;
    }

    .dcs-input-row {
        position: relative;
        margin-bottom: 14px;
    }

    .dcs-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 15px;
    }

    .dcs-input-row input {
        width: 100%;
        height: 52px;
        padding: 0 16px 0 42px;
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 10px;
        color: #fff;
        font-size: 16px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .dcs-input-row input:focus {
        outline: none;
        border-color: #D12026;
        box-shadow: 0 0 0 3px rgba(209, 32, 38, .18);
    }

    .dcs-input-row input::placeholder {
        color: #666;
    }

    .dcs-submit {
        width: 100%;
        height: 52px;
        background: #D12026;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: .02em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background .15s ease, transform .1s ease;
    }

    .dcs-submit:hover {
        background: #a91a1f;
    }

    .dcs-submit:active {
        transform: scale(.98);
    }

    .dcs-submit:disabled {
        opacity: .7;
        cursor: default;
    }

    .dcs-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, .35);
        border-top-color: #fff;
        border-radius: 50%;
        animation: dcs-spin .7s linear infinite;
    }

    .dcs-submit.is-loading .dcs-spinner {
        display: inline-block;
    }

    @keyframes dcs-spin {
        to { transform: rotate(360deg); }
    }

    .dcs-result {
        margin-top: 18px;
        padding: 0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transform: translateY(-6px);
        transition: max-height .3s ease, opacity .3s ease, transform .3s ease, padding .3s ease;
    }

    .dcs-result.is-visible {
        max-height: 200px;
        opacity: 1;
        transform: translateY(0);
        padding: 16px;
    }

    .dcs-result.is-available {
        background: rgba(76, 175, 80, .12);
        border: 1px solid rgba(76, 175, 80, .3);
        color: #6fcf73;
    }

    .dcs-result.is-unavailable {
        background: rgba(209, 32, 38, .12);
        border: 1px solid rgba(209, 32, 38, .3);
        color: #ff6b6f;
    }

    @media (max-width: 480px) {
        .dcs-panel {
            width: 100%;
            top: auto;
            bottom: 0;
            height: auto;
            max-height: 85vh;
            border-radius: 20px 20px 0 0;
            transform: translateY(100%);
            box-shadow: 0 -12px 40px rgba(0, 0, 0, .5);
        }

        .dcs-panel.open {
            transform: translateY(0);
        }

        .dcs-trigger-text {
            display: none;
        }

        .dcs-trigger {
            width: 54px;
            padding: 0;
            justify-content: center;
        }
    }
</style>

<script>
(function () {
    "use strict";

    const trigger = document.getElementById('dcs-trigger');
    const backdrop = document.getElementById('dcs-backdrop');
    const panel = document.getElementById('dcs-panel');
    const closeBtn = document.getElementById('dcs-close');
    const input = document.getElementById('dcs-postcode');
    const submitBtn = document.getElementById('dcs-submit');
    const result = document.getElementById('dcs-result');

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function openPanel() {
        panel.classList.add('open');
        backdrop.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(() => input.focus(), 300);
    }

    function closePanel() {
        panel.classList.remove('open');
        backdrop.classList.remove('open');
        panel.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    trigger.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);
    backdrop.addEventListener('click', closePanel);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePanel();
    });

    function showResult(message, state) {
        result.textContent = message;
        result.classList.remove('is-available', 'is-unavailable', 'is-visible');
        void result.offsetWidth;
        result.classList.add('is-visible', state);
    }

    function runCheck() {
        const postcode = input.value.trim();

        if (!postcode) {
            showResult('Please enter a postcode.', 'is-unavailable');
            input.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.classList.add('is-loading');

        fetch('{{ route('delivery-check.check') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ postcode }),
        })
        .then(res => res.json())
        .then(data => {
            showResult(data.message, data.available ? 'is-available' : 'is-unavailable');
        })
        .catch(() => {
            showResult('Something went wrong — please try again.', 'is-unavailable');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('is-loading');
        });
    }

    submitBtn.addEventListener('click', runCheck);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            runCheck();
        }
    });
})();
</script>