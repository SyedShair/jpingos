{{--
  Shared Dropzone + gallery-management script, included by both
  menu/create.blade.php and menu/edit.blade.php.

  Expects two variables from the including view:
    $galleryMode   – 'create' | 'edit'
    $draftToken    – present only in create mode
    $item          – the MenuItem (existing, with ->images loaded, in edit mode)
--}}

@push('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
  <style>
    #dropzone-gallery {
      border: 2px dashed var(--bs-border-color, #dee2e6);
      background: rgba(0,0,0,.015);
      min-height: 160px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      cursor: pointer;
      transition: border-color .15s ease;
    }
    #dropzone-gallery:hover,
    #dropzone-gallery.dz-drag-hover {
      border-color: var(--bs-primary, #0d6efd);
    }
    #dropzone-gallery .dz-message { margin: 0; color: #6c757d; }
    #dropzone-gallery .dz-preview { display: none; } /* we render our own thumbnail grid below */

    .gallery-thumb {
      position: relative;
      width: 110px;
      height: 110px;
      border-radius: 12px;
      overflow: hidden;
      border: 2px solid transparent;
      cursor: grab;
    }
    .gallery-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gallery-thumb.is-primary { border-color: var(--bs-primary, #0d6efd); }
    .gallery-thumb .thumb-actions {
      position: absolute; inset: 0;
      background: rgba(0,0,0,.35);
      display: flex; align-items: flex-start; justify-content: space-between;
      padding: 4px;
      opacity: 0; transition: opacity .15s ease;
    }
    .gallery-thumb:hover .thumb-actions { opacity: 1; }
    .gallery-thumb .thumb-btn {
      background: rgba(255,255,255,.9);
      border: none; border-radius: 50%;
      width: 24px; height: 24px;
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; line-height: 1; cursor: pointer;
    }
    .gallery-thumb .thumb-btn.star.active { color: #fd7e14; }
    .gallery-thumb .cover-badge {
      position: absolute; bottom: 4px; left: 4px;
      background: var(--bs-primary, #0d6efd); color: #fff;
      font-size: 10px; padding: 1px 6px; border-radius: 10px;
    }
    .gallery-thumb.dz-uploading::after {
      content: '';
      position: absolute; inset: 0;
      background: rgba(255,255,255,.6);
    }
  </style>
@endpush

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
  <script>
    Dropzone.autoDiscover = false;

    (function () {
      const mode = @json($galleryMode);
      @if ($galleryMode === 'create')
        const draftToken = @json($draftToken);
        const uploadUrl = @json(route('menu.images.temp.store', ['draftToken' => '__TOKEN__']));
        const dropzoneUrl = uploadUrl.replace('__TOKEN__', draftToken);
        let uploadOrder = [];
      @else
        const itemId = @json($item->id);
        const dropzoneUrl = @json(route('menu.images.store', $item));
        const reorderUrl = @json(route('menu.images.reorder', $item));
      @endif

      const thumbsContainer = document.getElementById('gallery-thumbs');
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

      function renderThumb({ key, url, isPrimary }) {
        const el = document.createElement('div');
        el.className = 'gallery-thumb' + (isPrimary ? ' is-primary' : '');
        el.dataset.key = key;
        el.innerHTML = `
          <img src="${url}" alt="">
          <div class="thumb-actions">
            <button type="button" class="thumb-btn star ${isPrimary ? 'active' : ''}" title="Set as cover photo">★</button>
            <button type="button" class="thumb-btn remove" title="Remove">✕</button>
          </div>
          ${isPrimary ? '<span class="cover-badge">Cover</span>' : ''}
        `;
        thumbsContainer.appendChild(el);
        return el;
      }

      function clearPrimaryBadges() {
        thumbsContainer.querySelectorAll('.gallery-thumb').forEach(t => {
          t.classList.remove('is-primary');
          t.querySelector('.star')?.classList.remove('active');
          t.querySelector('.cover-badge')?.remove();
        });
      }

      @if ($galleryMode === 'edit')
        // Pre-populate the grid with the dish's existing images.
        @foreach ($item->images as $image)
          renderThumb({
            key: @json($image->id),
            url: @json($image->url),
            isPrimary: @json($image->is_primary),
          });
        @endforeach
      @endif

      const dropzone = new Dropzone('#dropzone-gallery', {
        url: dropzoneUrl,
        paramName: 'file',
        maxFilesize: 4, // MB
        acceptedFiles: 'image/*',
        addRemoveLinks: false,
        headers: { 'X-CSRF-TOKEN': csrfToken },
        dictDefaultMessage: 'Drop dish photos here or click to upload<br><small class="text-muted">JPG or PNG, up to 4MB each</small>',
      });

      dropzone.on('success', function (file, response) {
        @if ($galleryMode === 'create')
          uploadOrder.push(response.filename);
          document.getElementById('image_order').value = uploadOrder.join(',');
          const isFirst = thumbsContainer.children.length === 0;
          if (isFirst) clearPrimaryBadges();
          file._thumbEl = renderThumb({ key: response.path, url: response.url, isPrimary: isFirst });
        @else
          if (response.is_primary) clearPrimaryBadges();
          file._thumbEl = renderThumb({ key: response.id, url: response.url, isPrimary: response.is_primary });
        @endif
        dropzone.removeFile(file); // we render our own thumb, don't need dropzone's preview node
      });

      dropzone.on('error', function (file, message) {
        alert(typeof message === 'string' ? message : 'Upload failed. Please try a smaller image.');
        dropzone.removeFile(file);
      });

      // Delegate clicks on thumbnails (star = set cover, ✕ = remove)
      thumbsContainer.addEventListener('click', function (e) {
        const thumb = e.target.closest('.gallery-thumb');
        if (!thumb) return;
        const key = thumb.dataset.key;

        if (e.target.classList.contains('star')) {
          @if ($galleryMode === 'create')
            clearPrimaryBadges();
            thumb.classList.add('is-primary');
            thumb.querySelector('.star').classList.add('active');
            thumb.insertAdjacentHTML('beforeend', '<span class="cover-badge">Cover</span>');
            // Move to front of upload order so it's saved as primary.
            uploadOrder = [key, ...uploadOrder.filter(f => f !== key)];
            document.getElementById('image_order').value = uploadOrder.join(',');
          @else
            fetch(`/menu/images/${key}/primary`, {
              method: 'PATCH',
              headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).then(() => {
              clearPrimaryBadges();
              thumb.classList.add('is-primary');
              thumb.querySelector('.star').classList.add('active');
              thumb.insertAdjacentHTML('beforeend', '<span class="cover-badge">Cover</span>');
            });
          @endif
        }

        if (e.target.classList.contains('remove')) {
          if (!confirm('Remove this photo?')) return;

          @if ($galleryMode === 'create')
            fetch(`/menu/images/temp/${draftToken}/${key}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).then(() => {
              uploadOrder = uploadOrder.filter(f => f !== key);
              document.getElementById('image_order').value = uploadOrder.join(',');
              thumb.remove();
            });
          @else
            fetch(`/menu/images/${key}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).then(() => thumb.remove());
          @endif
        }
      });

      @if ($galleryMode === 'edit')
        // Drag-to-reorder, persisted immediately.
        Sortable.create(thumbsContainer, {
          animation: 150,
          onEnd: function () {
            const order = Array.from(thumbsContainer.children).map(el => el.dataset.key);
            fetch(reorderUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
              },
              body: JSON.stringify({ order }),
            });
          },
        });
      @endif
    })();
  </script>
@endpush
