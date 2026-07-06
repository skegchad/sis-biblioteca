<?php

include ('../../app/config/config.php');
include ('../../app/config/conexion.php');
?>
<?php
include 'part1.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Biblioteca - Información de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #000;
        }
        /* Estilos del menú superior azul */
        .navbar-custom {
            background-color: #79b4ec; 
            padding: 12px 0;
        }
        .navbar-custom .nav-link {
            color: #000 !important;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.95rem;
            padding: 8px 20px !important;
        }
        /* Pestaña activa blanca y redondeada como tu diseño */
        .navbar-custom .nav-link.active {
            background-color: #fff;
            color: #b83b4b !important; /* Texto guinda/rojo para resaltar */
            border-radius: 8px 8px 0 0;
        }
        /* Banner Informativo Azul */
        .info-banner {
            background-color: #5fa8e9;
            color: white;
            border-radius: 20px;
            padding: 35px;
            font-weight: bold;
            font-size: 1.1rem;
            line-height: 1.6;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        /* Estilos de las secciones de texto */
        .section-title {
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        .custom-list li {
            margin-bottom: 15px;
            line-height: 1.6;
            font-size: 1.05rem;
        }
        /* Imagen con efecto recortado/rasgado (puedes ajustarlo luego con tu imagen real) */
        .torn-img {
            border-radius: 10px;
            width: 100%;
            max-width: 450px;
            object-fit: cover;
        }
    </style>
</head>
<body>


    <div class="container px-4">
        
        <div class="row align-items-center mb-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="info-banner">
                    ¿BUSCAS UN LIBRO PARA LLEVAR A CASA?<br>
                    EN LA BIBLIOTECA TENEMOS UNA GRAN COLECCIÓN DE LIBROS DISPONIBLES PARA PRÉSTAMOS. PARA LLEVARTE UNO, SOLO NECESITAS ACERCARTE A LA BIBLIOTECA Y SOLICITARLO EN EL ÁREA DE ATENCIÓN.
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=600" alt="Área de atención" class="img-fluid torn-img shadow">
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-lg-9 text-center">
                <p class="fs-5" style="line-height: 1.6;">
                    <strong>Recuerda:</strong> Los libros deben ser devueltos en la fecha acordada y en el mismo estado que se realizó el préstamo, para que de esa manera otros estudiantes puedan disfrutar de ellos.
                </p>
            </div>
        </div>

        <div class="row align-items-center g-5 mb-5">
            <div class="col-md-7">
                <h3 class="section-title text-uppercase"><i class="bi bi-clock-history text-primary"></i> Horario de Préstamos:</h3>
                <ul class="custom-list list-unstyled ps-2">
                    <li class="d-flex align-items-start gap-2">
                        <span class="fs-4 text-primary" style="line-height:1;">•</span>
                        <div>El préstamo de los libros se realizarán solamente los días viernes en el horario de 7:10 a.m. hasta las 12:45 p.m.</div>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <span class="fs-4 text-primary" style="line-height:1;">•</span>
                        <div>Pueden solicitarlo a cualquier hora dentro del horario anteriormente mencionado, teniendo en cuenta que debe ser fuera de sus horas de clase, o en cierta ocasión que tenga permiso autorizado por su docente.</div>
                    </li>
                </ul>
            </div>
            <div class="col-md-5 text-center">
                <img src="https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?q=80&w=500" alt="Libros" class="img-fluid rounded shadow" style="max-height: 300px; width: 100%; object-fit: cover;">
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>