<?php
include ("../../app/config/config.php");
include ("../../app/config/conexion.php");
include ("../../layout/admin/login.php");
include ("../../layout/admin/datos_usuario.php");

// $id, $nombreusuario, etc. ya vienen resueltos por datos_usuario.php
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cambiar Contraseña|Sistema Biblioteca</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="<?php echo $URL;?>/public/css/adminlte.css" as="style" />

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?php echo $URL;?>/public/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <style>
      .login-page {
          background-image: url('<?php echo $URL;?>/public/assets/img/grupoProyecto/librosEstante.jpeg') !important;
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
      }
      .login-page::before {
          content: '';
          position: fixed;
          inset: 0;
          backdrop-filter: blur(6px);
          -webkit-backdrop-filter: blur(6px);
          background-color: rgba(0, 0, 0, 0.3);
          z-index: 0;
      }
      .login-box {
          position: relative;
          z-index: 1;
      }
      .theme-toggle-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10;
            display: flex;
            gap: 8px;
        }
        .theme-toggle-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000 !important;
            color: #fff;
            border: none;
            transition: transform .2s, background .2s;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.08);
            background: #000 !important;
        }
        [data-bs-theme="dark"] .theme-toggle-btn {
            background: #000 !important;
        }
        [data-bs-theme="dark"] .theme-toggle-btn:hover {
            background: #000 !important;
        }

    </style>
    <link rel="icon" href="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" type="image/png">
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="login-page bg-body-secondary">
    <!-- Botón toggle claro/oscuro -->
    <div class="theme-toggle-fixed">
      <button
        type="button"
        class="btn btn-sm btn-outline-secondary rounded-circle theme-toggle-btn"
        data-bs-theme-value="light"
        title="Modo claro"
      >
        <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
      </button>
      <button
        type="button"
        class="btn btn-sm btn-outline-secondary rounded-circle theme-toggle-btn"
        data-bs-theme-value="dark"
        title="Modo oscuro"
      >
        <i class="bi bi-moon-stars-fill" data-lte-theme-icon="dark"></i>
      </button>
    </div>
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center pb-3">
          <a
              href="<?php echo $URL;?>/user"
              class="link-offset-2 link-opacity-100 link-opacity-50-hover text-center"
              style="text-decoration: none;"
          >
              <h1 class="mb-3" style="color: #5dade2;"><b>Sistema</b> Biblioteca</h1>
          </a>
          <p class="text-muted mb-0">Cambiar contraseña</p>
        </div>
        <div class="card-body login-card-body">
          <p class="login-box-msg">Ingresa tu contraseña actual y la nueva</p>

          <form id="formCambiarPassword">
            <div class="input-group mb-1">
              <div class="form-floating">
                <input name="actual" id="passwordActual" type="password" class="form-control" placeholder="" required />
                <label for="passwordActual">Contraseña actual</label>
              </div>
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>

            <div class="input-group mb-1">
              <div class="form-floating">
                <input name="nueva" id="passwordNueva" type="password" class="form-control" placeholder="" required minlength="6" />
                <label for="passwordNueva">Nueva contraseña</label>
              </div>
              <div class="input-group-text">
                <span class="bi bi-shield-lock-fill"></span>
              </div>
            </div>

            <div class="input-group mb-1">
              <div class="form-floating">
                <input name="confirmar" id="passwordConfirmar" type="password" class="form-control" placeholder="" required minlength="6" />
                <label for="passwordConfirmar">Confirmar nueva contraseña</label>
              </div>
              <div class="input-group-text">
                <span class="bi bi-shield-lock-fill"></span>
              </div>
            </div>

            <div class="d-grid gap-2 mt-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle me-2"></i>Guardar cambios
              </button>
              <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo $URL;?>/user/profile/index.php'">
                <i class="bi bi-x-circle me-2"></i>Cancelar
              </button>
            </div>

          </form>

        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->

    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="<?php echo $URL;?>/public/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::SweetAlert2-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--end::SweetAlert2-->

    <!--begin::Color Mode Toggle-->
    <script>
        (() => {
            'use strict';

            const STORAGE_KEY = 'lte-theme';

            const getStoredTheme = () => localStorage.getItem(STORAGE_KEY);
            const setStoredTheme = (theme) => localStorage.setItem(STORAGE_KEY, theme);

            const prefersDark = () => globalThis.matchMedia('(prefers-color-scheme: dark)').matches;

            const getPreferredTheme = () => {
                const stored = getStoredTheme();
                if (stored) return stored;
                return prefersDark() ? 'dark' : 'light';
            };

            const setTheme = (theme) => {
                const resolved = theme === 'auto' ? (prefersDark() ? 'dark' : 'light') : theme;
                document.documentElement.setAttribute('data-bs-theme', resolved);
            };

            setTheme(getPreferredTheme());

            const showActiveTheme = (theme) => {
                document.querySelectorAll('[data-bs-theme-value]').forEach((el) => {
                    el.classList.remove('active');
                    el.setAttribute('aria-pressed', 'false');
                    const check = el.querySelector('.bi-check-lg');
                    if (check) check.classList.add('d-none');
                });
                const active = document.querySelector(`[data-bs-theme-value="${theme}"]`);
                if (active) {
                    active.classList.add('active');
                    active.setAttribute('aria-pressed', 'true');
                    const check = active.querySelector('.bi-check-lg');
                    if (check) check.classList.remove('d-none');
                }
                document.querySelectorAll('[data-lte-theme-icon]').forEach((icon) => {
                    icon.classList.toggle('d-none', icon.dataset.lteThemeIcon !== theme);
                });
            };

            globalThis.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const stored = getStoredTheme();
                if (!stored || stored === 'auto') setTheme(getPreferredTheme());
            });

            document.addEventListener('DOMContentLoaded', () => {
                showActiveTheme(getPreferredTheme());
                document.querySelectorAll('[data-bs-theme-value]').forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        const theme = toggle.getAttribute('data-bs-theme-value');
                        setStoredTheme(theme);
                        setTheme(theme);
                        showActiveTheme(theme);
                    });
                });
            });
        })();
    </script>
    <!--end::Color Mode Toggle-->

    <!--begin::Form Cambiar Password-->
    <script>
        document.getElementById("formCambiarPassword").addEventListener("submit", function (e) {
            e.preventDefault();

            const actual = document.getElementById("passwordActual").value;
            const nueva = document.getElementById("passwordNueva").value;
            const confirmar = document.getElementById("passwordConfirmar").value;

            if (nueva !== confirmar) {
                Swal.fire('Error', 'La nueva contraseña y su confirmación no coinciden.', 'error');
                return;
            }

            if (nueva.length < 6) {
                Swal.fire('Error', 'La nueva contraseña debe tener al menos 6 caracteres.', 'error');
                return;
            }

            fetch("<?= $URL; ?>/user/profile/controller_update_password.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "actual=" + encodeURIComponent(actual) +
                      "&nueva=" + encodeURIComponent(nueva)
            })
            .then(res => res.text())
            .then(text => {
                console.log("RESPUESTA CRUDA:", text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        Swal.fire('¡Listo!', 'Contraseña actualizada correctamente.', 'success')
                            .then(() => window.location.href = "<?= $URL; ?>/user/profile/index.php");
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo actualizar la contraseña.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'El servidor no devolvió JSON válido (revisá la consola).', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
            });
        });
    </script>
    <!--end::Form Cambiar Password-->

  </body>
  <!--end::Body-->
</html>