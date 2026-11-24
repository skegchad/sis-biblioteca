-- Ejecutar una sola vez sobre la base de datos existente.
-- El esquema de instalaciones nuevas ya incluye estos campos.
ALTER TABLE tb_libros
    ADD COLUMN paginas_pdf_cache INT UNSIGNED NULL,
    ADD COLUMN paginas_pdf_cache_key CHAR(64) NULL,
    ADD COLUMN color_portada_cache CHAR(7) NULL,
    ADD COLUMN color_portada_cache_key CHAR(64) NULL,
    ADD INDEX idx_libros_estado_categoria (estado, categoria(100));
