CREATE TABLE tb_usuarios(
    id_usuario          INT (11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
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

    fyh_creacion        DATETIME      NULL,
    fyh_actualizacion   DATETIME      NULL,
    fyh_eliminacion     DATETIME      NULL,
    estado              VARCHAR (11)  NOT NULL
);

CREATE TABLE tb_libros(
    id_libro            INT (11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR (255) NOT NULL,
    descripcion         TEXT          NOT NULL,
    autor               VARCHAR (150)  NOT NULL,
    idioma              VARCHAR (255) NOT NULL,
    disponibilidad      TINYINT(1)    NOT NULL DEFAULT 1,
    temas               VARCHAR (255) NOT NULL,
    tipo                VARCHAR (255) NOT NULL,
    edicion             VARCHAR (255) NOT NULL,
    ano                 YEAR          NOT NULL,
    cdd                 VARCHAR (255) NOT NULL,
    bloque              VARCHAR (255) NOT NULL,
    categoria           VARCHAR (255) NOT NULL,
    seccion             VARCHAR (255) NOT NULL,
    editorial           VARCHAR (255) NOT NULL,
    ejemplares          INT(11)       NOT NULL DEFAULT 0,  -- ✅ numérico
    prestados           INT(11)       NOT NULL DEFAULT 0,
    ruta_pdf            VARCHAR(500)  NULL,
    ruta_foto           VARCHAR(500)  NULL,

    fyh_creacion        DATETIME      NULL,
    fyh_actualizacion   DATETIME      NULL,
    fyh_eliminacion     DATETIME      NULL,
    estado              VARCHAR (11)  NOT NULL DEFAULT '1'
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    fyh_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subcategorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    fyh_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_sub (categoria_id, nombre),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);


CREATE TABLE tipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tema (tipo_id, nombre),
    FOREIGN KEY (tipo_id) REFERENCES tipos(id) ON DELETE CASCADE
);

CREATE TABLE libro_tema (
    id_libro INT NOT NULL,
    tema_id INT NOT NULL,
    PRIMARY KEY (id_libro, tema_id),
    FOREIGN KEY (id_libro) REFERENCES tb_libros(id_libro) ON DELETE CASCADE,
    FOREIGN KEY (tema_id) REFERENCES temas(id) ON DELETE CASCADE
);

CREATE TABLE prestamos (
    id_prestamo INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_libro INT NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_libro) REFERENCES tb_libros(id_libro) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario) ON DELETE CASCADE,
    fyh_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fyh_devolucion DATETIME NULL,
    estado VARCHAR (255) NULL
);

CREATE TABLE noticias (
    id_noticia INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ruta_foto VARCHAR(255) NOT NULL
);