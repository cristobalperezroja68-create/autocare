-- AutoCare database. Import through phpMyAdmin (Import tab).
-- Default admin:  admin@autocare.com  /  Admin123*   (change it after the first login)
CREATE DATABASE IF NOT EXISTS autocare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autocare_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS solicitudes, precios_lavado, servicios, productos, categorias, usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  descripcion TEXT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  imagen VARCHAR(255) NULL,
  categoria_id INT UNSIGNED NOT NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_prod_cat FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE servicios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE precios_lavado (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo_vehiculo ENUM('moto','carro','camioneta') NOT NULL,
  servicio_id INT UNSIGNED NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  UNIQUE KEY uq_vehiculo_servicio (tipo_vehiculo, servicio_id),
  CONSTRAINT fk_precio_serv FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Extra table so the "Inquire / Buy", "Request service" and Contact buttons really save data
CREATE TABLE solicitudes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NULL,
  tipo ENUM('producto','servicio','contacto') NOT NULL,
  referencia VARCHAR(200) NOT NULL,
  cantidad INT UNSIGNED NOT NULL DEFAULT 1,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  email VARCHAR(120) NULL,
  mensaje VARCHAR(500) NULL,
  estado ENUM('pendiente','atendida','cancelada') NOT NULL DEFAULT 'pendiente',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sol_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre,email,password,rol) VALUES
('Administrator','admin@autocare.com','$2y$12$myjgQhR6Q2NWdLrERY39webEsW4K9NK2/NGfZQ0iRPuJYE0QrrGXm','admin');

INSERT INTO categorias (nombre,descripcion) VALUES
('Car shampoos with wax','Gentle shampoos that clean and add shine'),
('Tire shine products','Gels and sprays for a deep black finish'),
('Interior cleaners','Dashboard, plastics and carpets'),
('Leather and fabric cleaners','Cleaners and conditioners for seats'),
('Degreasers','Engine bay, wheels and heavy grime'),
('Microfiber towels','Drying, polishing and detailing towels'),
('Specialized automotive care','Sealants, clay bars and protection');

INSERT INTO productos (nombre,descripcion,precio,stock,categoria_id) VALUES
('Wax Car Shampoo 1L','Concentrated pH-neutral shampoo with carnauba wax. Rich foam and long-lasting shine.',22000,40,1),
('Foam Wash Shampoo 500ml','High-foam shampoo for foam cannons and buckets. Safe on paint and plastics.',18000,35,1),
('Tire Shine Gel 500ml','Water-based gel for a deep satin black finish that lasts for weeks.',25000,28,2),
('Tire Shine Spray 750ml','Quick-apply spray that restores color to tires and rubber trims.',28000,22,2),
('Interior Cleaner 500ml','Removes dust, stains and fingerprints from dashboard, doors and plastics.',20000,30,3),
('Leather Cleaner & Conditioner 300ml','Cleans, nourishes and protects leather seats from cracking.',35000,15,4),
('Fabric Upholstery Cleaner 500ml','Deep-cleaning formula for fabric seats, mats and roof liners.',27000,18,4),
('Engine Degreaser 1L','Powerful degreaser for engine bays, chassis and wheel arches.',30000,25,5),
('Microfiber Towel Pack x3','Ultra-soft 40x40 cm towels, 350 GSM. Scratch-free drying and polishing.',24000,50,6),
('Ceramic Spray Sealant 250ml','Easy spray-on protection with hydrophobic effect for up to 6 months.',65000,0,7),
('Clay Bar Kit','Removes embedded contaminants before wax or sealant. Includes lubricant spray.',48000,10,7);

INSERT INTO servicios (nombre,descripcion,activo) VALUES
('Basic Wash','Exterior hand wash with pH-neutral shampoo, wheel rinse and towel drying.',1),
('Complete Wash','Everything in Basic plus interior vacuum, dashboard cleaning, window cleaning and tire shine.',1),
('Premium Wash','Everything in Complete plus wax and polish, deep upholstery cleaning and engine bay cleaning.',1);

INSERT INTO precios_lavado (tipo_vehiculo,servicio_id,precio) VALUES
('moto',1,15000),('moto',2,25000),('moto',3,35000),
('carro',1,25000),('carro',2,40000),('carro',3,55000),
('camioneta',1,35000),('camioneta',2,50000),('camioneta',3,70000);
