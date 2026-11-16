<?php
include ("../app/config/config.php");
include ("../app/config/conexion.php");
include ("../layout/admin/login.php");
include ("../layout/admin/datos_usuario.php");

if($cargo=="Administrador"){
    $msj="Ir a página de administrador";
    $rutaAdmin= $URL."/admin";
}else{
    $msj="Cerrar Sesión";
    $rutaAdmin= $URL."/login/controller_logout.php";
}
include ("../layout/user/part1.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección Biblioteca Aislada</title>
    
    <style>

        .biblio-section-container, 
        .biblio-section-container * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        
        .biblio-section-container {
            width: 100%;
            max-width: 740px; 
            margin: 16px auto;
            padding: 0 12px;
            background: transparent;
        }

       
        .biblio-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            align-items: stretch;
        }

        @media (min-width: 600px) {
            .biblio-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
        }


        .biblio-card-text {
            background-color: #4A90E2; 
            border-radius: 16px;
            padding: 20px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .biblio-card-text h2 {
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.45;
            letter-spacing: 0.2px;
        }

        @media (min-width: 600px) {
            .biblio-card-text h2 {
                font-size: 0.88rem;
                line-height: 1.5;
            }
        }

       
        .biblio-card-img {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
            min-height: 180px;
            background-color: #cbd5e1;
        }

        .biblio-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 16px;
        }
       /* ===== Recordatorio (ocupa todo el ancho) ===== */
.biblio-reminder {
    display: block;
    width: 100%;
    margin-top: 28px;
}

.biblio-reminder p {
    width: 100%;
    text-align: center;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #1f2937;
}

/* Si todavía tienes el SVG de la flecha, esto lo oculta */
.biblio-reminder-icon {
    display: none;
}

/* ===== Horario (diseño original + centrado vertical) ===== */
.biblio-schedule {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    align-items: center;
    margin-top: 28px;
}

@media (min-width: 600px) {
    .biblio-schedule {
        grid-template-columns: 1.3fr 1fr;
        align-items: center;
    }
}

.biblio-schedule-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.biblio-schedule h3 {
    font-size: 0.95rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #111827;
    margin-bottom: 12px;
}

.biblio-schedule ul {
    list-style: disc;
    padding-left: 20px;
}

.biblio-schedule li {
    text-align: center;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #1f2937;
    margin-bottom: 16px;
}

.biblio-schedule li:last-child {
    margin-bottom: 0;
}

.biblio-schedule-img {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    min-height: 200px;
    background-color: #cbd5e1;
}

.biblio-schedule-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
    </style>
</head>
<body>

<div class="biblio-section-container">
        <div class="biblio-grid">
            
           
            <div class="biblio-card-text">
                <h2>
                    ¿BUSCAS UN LIBRO PARA LLEVAR A CASA?<br>
                    EN LA BIBLIOTECA TENEMOS UNA GRAN COLECCIÓN DE LIBROS DISPONIBLES PARA PRÉSTAMOS.<br>
                    PARA LLEVARTE UNO, SOLO NECESITAS ACERCARTE A LA BIBLIOTECA Y SOLICITARLO EN EL ÁREA DE ATENCIÓN.
                </h2>
            </div>

            
            <div class="biblio-card-img">
                <img 
                    src="../public/assets/img/grupoProyecto/Entrada biblioteca.jpeg" 
                    alt="Área de atención en biblioteca" 
                />
            </div>

        </div>
        <!-- Recordatorio -->
<div class="biblio-reminder">
    <p>
        <strong>Recuerda:</strong> Los libros deben ser devueltos en la fecha acordada y
        en el mismo estado en que se realizó el préstamo, para que de esa manera
        otros estudiantes puedan disfrutar de ellos.
    </p>
    
</div>

<!-- Horario de préstamos -->
<div class="biblio-schedule">
    <div class="biblio-schedule-text">
        <h3>Horario de préstamos:</h3>
        <ul>
            <li>
                El préstamo de los libros se realizará solamente los días
                viernes en el horario de 7:10 a.m. hasta las 12:45 p.m.
            </li>
            <li>
                Pueden solicitarlo a cualquier hora dentro del horario
                anteriormente mencionado, teniendo en cuenta que debe ser
                fuera de sus horas de clase, o en cierta ocasión que tenga
                permiso autorizado por su docente.
            </li>
        </ul>
    </div>

    <div class="biblio-schedule-img">
        <img
            src="../public/assets/img/grupoProyecto/pila%20de%20libros.png"
            alt="Pila de libros de la biblioteca"
        />
    </div>
</div>
   

</body>
</html>


<?php include("../ai/chat_widget.php"); ?>
