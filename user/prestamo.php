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
    </div>
   

</body>
</html>


<?php include("../ai/chat_widget.php"); ?>
