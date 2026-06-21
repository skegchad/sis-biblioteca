CREATE TABLE tb_admin(
    id_admin            INT (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombres             VARCHAR (255) NOT NULL,
    apellidos           VARCHAR (255) NOT NULL,
    cedula              VARCHAR (255) NOT NULL,
    fyh_nacimiento      DATETIME NOT NULL,
    password            VARCHAR (255) NOT NULL,
    cargo               VARCHAR (255) NOT NULL,

    fyh_creacion        DATETIME        NULL,
    fyh_actualizacion   DATETIME        NULL,
    fyh_eliminacion     DATETIME        NULL,
    estado              VARCHAR (11)    NOT NULL
);
