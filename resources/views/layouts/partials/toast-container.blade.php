<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
  @if (session('status'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert"
      aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
      <div class="d-flex">
        <div class="toast-body">{{ session('status') }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  @endif
</div>

<script>
  window.showToast = function (message, type = 'success') {
    const bgClass = {
      success: 'text-bg-success',
      error:   'text-bg-danger',
      warning: 'text-bg-warning',
      info:    'text-bg-info',
    }[type] || 'text-bg-success';

    const el = document.createElement('div');
    el.className = `toast align-items-center ${bgClass} border-0`;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    el.querySelector('.toast-body').textContent = message;

    document.querySelector('.toast-container').appendChild(el);
    const toast = new bootstrap.Toast(el, { delay: 4000 });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  document.querySelectorAll('.toast-container .toast').forEach((el) => {
    new bootstrap.Toast(el, { delay: parseInt(el.dataset.bsDelay || 4000, 10) }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  });

  document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (payload) => {
      const data = Array.isArray(payload) ? payload[0] : payload;
      window.showToast(data?.message ?? '', data?.type ?? 'success');
    });
  });
</script>