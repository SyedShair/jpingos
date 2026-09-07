<!--start switcher-->
<button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
  <i class="material-icons-outlined">tune</i>Customize
</button>

<div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
  <div class="offcanvas-header border-bottom h-70">
    <div class="">
      <h5 class="mb-0">Theme Customizer</h5>
      <p class="mb-0">Customize your theme</p>
    </div>
    <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
      <i class="material-icons-outlined">close</i>
    </a>
  </div>
  <div class="offcanvas-body">
    <div>
      <p>Theme variation</p>

      <div class="row g-3">
        <div class="col-12 col-xl-6">
          <input type="radio" class="btn-check theme-option" name="theme-options" id="BlueTheme" value="blue-theme" checked>
          <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
            <span class="material-icons-outlined">contactless</span>
            <span>Blue</span>
          </label>
        </div>
        <div class="col-12 col-xl-6">
          <input type="radio" class="btn-check theme-option" name="theme-options" id="LightTheme" value="light-theme">
          <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
            <span class="material-icons-outlined">light_mode</span>
            <span>Light</span>
          </label>
        </div>
        <div class="col-12 col-xl-6">
          <input type="radio" class="btn-check theme-option" name="theme-options" id="DarkTheme" value="dark-theme">
          <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
            <span class="material-icons-outlined">dark_mode</span>
            <span>Dark</span>
          </label>
        </div>
        <div class="col-12 col-xl-6">
          <input type="radio" class="btn-check theme-option" name="theme-options" id="SemiDarkTheme" value="semi-dark">
          <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
            <span class="material-icons-outlined">contrast</span>
            <span>Semi Dark</span>
          </label>
        </div>
        <div class="col-12 col-xl-6">
          <input type="radio" class="btn-check theme-option" name="theme-options" id="BoderedTheme" value="bordered-theme">
          <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
            <span class="material-icons-outlined">border_style</span>
            <span>Bordered</span>
          </label>
        </div>
      </div><!--end row-->

    </div>
  </div>
</div>
<!--start switcher-->

<script>
(function () {
    /**
     * Maxton ships one CSS file per theme (sass/blue-theme.css,
     * sass/dark-theme.css, sass/semi-dark.css, sass/bordered-theme.css) and
     * all of them are loaded on every page. Which one actually takes effect
     * is controlled purely by the value of the data-bs-theme attribute on
     * <html> — e.g. sass/dark-theme.css only applies its rules when
     * <html data-bs-theme="dark-theme">. There's no separate light-theme.css:
     * "Light" just means main.css's own default look with no theme override,
     * so we clear the attribute for that option instead of pointing it at a
     * file that doesn't exist.
     */
    var STORAGE_KEY = 'MaxtonTheme';
    var root = document.documentElement;

    function applyTheme(theme) {
        if (theme === 'light-theme') {
            root.removeAttribute('data-bs-theme');
        } else {
            root.setAttribute('data-bs-theme', theme);
        }
    }

    function selectRadioFor(theme) {
        var input = document.querySelector('.theme-option[value="' + theme + '"]');
        if (input) {
            input.checked = true;
        } else if (theme === 'light-theme') {
            // No stored value yet, or attribute was cleared — nothing to check.
        }
    }

    function setTheme(theme, persist) {
        applyTheme(theme);
        selectRadioFor(theme);

        if (persist) {
            try {
                localStorage.setItem(STORAGE_KEY, theme);
            } catch (e) {
                // localStorage unavailable (private browsing, etc.) — theme
                // will just reset to default on next load, which is fine.
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var saved = null;
        try {
            saved = localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            // ignore
        }
        if (saved) {
            selectRadioFor(saved);
        }

        document.querySelectorAll('.theme-option').forEach(function (input) {
            input.addEventListener('change', function () {
                if (this.checked) {
                    setTheme(this.value, true);
                }
            });
        });
    });
})();
</script>