-- Creación de la tabla de clientes para el CRUD del proyecto
-- Este script se puede ejecutar varias veces sin dar error gracias a IF NOT EXISTS.

-- Crea la base de datos si todavía no existe
CREATE DATABASE IF NOT EXISTS productos_crud;

-- Indica en qué base de datos vamos a trabajar
USE productos_crud;

-- Tabla de clientes del proyecto "Oficina del Agua"
CREATE TABLE IF NOT EXISTS clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- número único que crece solo
    nombre VARCHAR(100) NOT NULL,                   -- obligatorio
    apellido VARCHAR(100) NOT NULL,                 -- obligatorio
    direccion VARCHAR(200) DEFAULT NULL,            -- opcional
    telefono VARCHAR(20) DEFAULT NULL,              -- opcional (texto porque puede llevar guiones o +502)
    email VARCHAR(100) DEFAULT NULL,                -- opcional
    created_at TIMESTAMP NULL DEFAULT NULL,         -- fecha de creación
    updated_at TIMESTAMP NULL DEFAULT NULL          -- fecha de última modificación
);
