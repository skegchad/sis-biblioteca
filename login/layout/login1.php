<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Administrador|Sistema Biblioteca</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE 4 | Login Page v2" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant"
    />
    
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="<?php echo $URL;?>/public/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

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
    </style>
    <link rel="icon" href="<?php echo $URL; ?>/public/assets/img/grupoProyecto/cenaculo.png" type="image/png">
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center pb-3">
    <a
        href="../index2.html"
        class="link-offset-2 link-opacity-100 link-opacity-50-hover text-center"
        style="text-decoration: none;"
    >
        <h1 class="mb-3" style="color: #5dade2;"><b>Sistema</b> Biblioteca</h1>
            </a>
            <div class="d-flex justify-content-center align-items-center gap-3">
                <img 
                    src="<?php echo $URL;?>/public/assets/img/grupoProyecto/cenaculo.png" 
                    alt="Logo Cenáculo"
                    style="width: 140px; height: 140px; object-fit: contain;"
                />
                <div style="width: 1px; height: 70px; background-color: #dee2e6;"></div>
                <img 
                    src="<?php echo $URL;?>/public/assets/img/grupoProyecto/libros.png" 
                    alt="Logo Biblioteca"
                    style="width: 140px; height: 140px; object-fit: contain;"
                />
            </div>
        </div>
        <div class="card-body login-card-body">
          <p class="login-box-msg">Inicia sesión</p>