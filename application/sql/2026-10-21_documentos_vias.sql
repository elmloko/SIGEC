-- VIA adicionales de un documento (el primer VIA sigue en documentos.nombre_via / cargo_via)
CREATE TABLE IF NOT EXISTS documentos_vias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_documento INT NOT NULL,
    orden TINYINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    cargo VARCHAR(200) NOT NULL DEFAULT '',
    PRIMARY KEY (id),
    KEY idx_documento (id_documento, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
