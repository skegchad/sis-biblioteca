
<style>
    .seccion-contacto{
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
 
    .contacto-titulo{
        text-align: center;
        margin-bottom: 30px;
    }
 
    #gmap{
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }
 
    #gmap iframe{
        width: 100%;
        height: 400px;
        border: 0;
        display: block;
    }
 
    .gmap-enlace{
        text-align: center;
        margin-top: 12px;
    }
 
    .contacto-grid{
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 25px;
        margin-top: 35px;
    }
 
    .contacto-form-card,
    .contacto-info-card{
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        padding: 28px;
    }
 
    .contacto-form-card h2,
    .contacto-info-card h2{
        margin-top: 0;
        margin-bottom: 20px;
    }
 
    .status{
        display: none;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 500;
    }
 
    .status.alert-success{
        background: #d1e7dd;
        color: #0f5132;
    }
 
    .contacto-form-card .form-control{
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 16px;
        font-family: inherit;
        font-size: 15px;
        box-sizing: border-box;
    }
 
    .contacto-form-card .form-control:focus{
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13,110,253,.15);
    }
 
    .contacto-form-card textarea.form-control{
        resize: vertical;
        min-height: 140px;
    }
 
    .btn-enviar{
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        float: right;
        transition: background .25s;
    }
 
    .btn-enviar:hover{
        background: #0b5ed7;
    }
 
    .contacto-info-card address{
        font-style: normal;
    }
 
    .contacto-info-card address p{
        margin: 10px 0;
        color: #333;
    }
 
    @media (max-width: 768px){
        .contacto-grid{
            grid-template-columns: 1fr;
        }
        #gmap iframe{
            height: 280px;
        }
    }
</style>
 
<section class="seccion-contacto">
 
    <h2 class="contacto-titulo"><strong>Nuestra Ubicación</strong></h2>
 
    <div id="gmap">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d249.2002250090705!2d-79.93770586251365!3d-2.0744233950375697!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x902d0d7594f621b9%3A0xeba11836b5901282!2sUnidad%20Educativa%20Padres%20Somascos%20%22El%20Cen%C3%A1culo%22!5e0!3m2!1ses-419!2sec!4v1733889777724!5m2!1ses-419!2sec"
            allowfullscreen
            loading="lazy">
        </iframe>
    </div>
    <p class="gmap-enlace">
        <a href="https://www.google.com.ni/maps/@12.1101789,-85.3676322,17z?hl=es" target="_blank"><b>Ver en Mapa</b></a>
    </p>
 
    <div class="contacto-grid">
 
        <div class="contacto-form-card">
            <h2>Escríbenos</h2>
            <div class="status alert-success"></div>
            <form id="main-contact-form" action="<?php echo $URL; ?>/layout/user/validar_mensaje.php" method="post">
				<input type="text" name="nombre" class="form-control" required placeholder="Nombre" value="<?php echo $nombre;?>" disabled>
                <input type="text" name="nombre" class="form-control" required placeholder="Nombre" value="<?php echo $nombreusuario;?>" disabled>
                <input type="email" name="email" class="form-control" required placeholder="Email">
                <input type="text" name="asunto" class="form-control" required placeholder="Asunto">
                <textarea name="mensaje" required class="form-control" placeholder="Escribe tu mensaje"></textarea>
                <button type="submit" class="btn-enviar">Enviar Mensaje</button>
                <div style="clear:both;"></div>
            </form>
        </div>
 
        <div class="contacto-info-card">
            <h2>Información de Biblioteca</h2>
            <address>
                <p>Biblioteca Virtual</p>
                <p>Unidad Educativa Padres Somascos "El Cenáculo"</p>
                <p>Pascuales</p>
                <p>Teléfono: 0996634129</p>
                <p>Celular: 0983848448</p>
                <p>Email: Elcenaculo@ymail.com</p>
            </address>
        </div>
 
    </div>
 
</section>
 
</div><!-- cierre de #wrapper abierto en part1.php -->
 
<script src="<?php echo $URL; ?>/public/js/jquery.js"></script>
<script src="<?php echo $URL; ?>/public/js/bootstrap.js"></script>
<script src="<?php echo $URL; ?>/public/js/custom.js"></script>
 
<?php include("../ai/chat_widget.php"); ?>