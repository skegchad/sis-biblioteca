CREATE TABLE tb_usuarios(
    id_usuario          INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre_completo     VARCHAR (255) NOT NULL,
    apellidos           VARCHAR (255) NOT NULL,
    cedula              VARCHAR (255) NOT NULL,
    nombre_usuario      VARCHAR (255) NOT NULL,
    password            TEXT NOT NULL,
    foto                VARCHAR (255) NULL,
    cargo               VARCHAR (255) NOT NULL,
    --solo si es estudiante
    curso               VARCHAR (255) NULL,
    paralelo            VARCHAR (255) NULL,

    fyh_creacion        DATETIME        NULL,
    fyh_actualizacion   DATETIME        NULL,
    fyh_eliminacion     DATETIME        NULL,
    estado              VARCHAR (11)    NOT NULL
);

CREATE TABLE tb_libros(
    id_libro            INT (11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR (255) NOT NULL,
    descripcion         TEXT          NOT NULL,
    colaboradores       VARCHAR (255) NOT NULL,
    idioma              VARCHAR (255) NOT NULL,
    disponibilidad      TINYINT(1)    NOT NULL DEFAULT 1,
    temas               VARCHAR (255) NOT NULL,
    tipo                VARCHAR (255) NOT NULL,
    edicion             VARCHAR (255) NOT NULL,
    ano                 VARCHAR (255) NOT NULL,
    cdd                 VARCHAR (255) NOT NULL,
    bloque              VARCHAR (255) NOT NULL,
    categoria           VARCHAR (255) NOT NULL,
    subcategoria        VARCHAR (255) NOT NULL,
    seccion             VARCHAR (255) NOT NULL,
    ejemplares          INT(11)       NOT NULL DEFAULT 0,  -- ✅ numérico
    prestados           INT(11)       NOT NULL DEFAULT 0,
    ruta_pdf            VARCHAR(500)  NULL,
    ruta_foto            VARCHAR(500)  NULL,

    fyh_creacion        DATETIME        NULL,
    fyh_actualizacion   DATETIME        NULL,
    fyh_eliminacion     DATETIME        NULL,
    estado              VARCHAR (11)    NOT NULL DEFAULT '1'
);

