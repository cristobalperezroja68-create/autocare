# AutoCare

Sitio web para vender productos de limpieza y cuidado de vehículos y ofrecer servicios de lavado para motos, carros y camionetas. Incluye catálogo, precios de lavado, sistema de login con roles y un panel de administración con CRUD completo.

## Tecnologías
HTML5, CSS3, JavaScript, PHP 8 (PDO), MySQL, Bootstrap 5, SweetAlert2. Se ejecuta en local con XAMPP.

## Instalación
1. Copia la carpeta `autocare` dentro de `htdocs` de XAMPP (debe quedar `htdocs/autocare/index.php`).
2. Inicia **Apache** y **MySQL** desde el panel de XAMPP.
3. En `http://localhost/phpmyadmin` ve a **Importar** y selecciona `sql/autocare_db.sql`.
4. Abre `http://localhost/autocare/`.

Si cambias el nombre de la carpeta, actualiza `BASE_URL` en `config/conexion.php`.

## Acceso de administrador
- Correo: `admin@autocare.com`
- Contraseña: `Admin123*` (cámbiala después del primer ingreso en *Manage Users*)

## Funcionalidades
- Catálogo de productos desde MySQL con categorías, búsqueda y solicitud de compra/consulta.
- Servicios y precios de lavado (moto, carro, camioneta) editables desde el panel.
- Registro, login y logout con `password_hash()`, roles administrador y cliente.
- Panel admin: dashboard, productos (con subida de imágenes), categorías, servicios, precios, usuarios y solicitudes.
- Seguridad: consultas preparadas PDO, tokens CSRF, validación en PHP y JavaScript, validación de imágenes, confirmación antes de eliminar.

## Estructura
```
autocare/
├── index.php, productos.php, servicios.php, precios.php, nosotros.php, contacto.php
├── login.php, registro.php, logout.php, solicitar.php
├── admin/       dashboard, productos, categorias, servicios, precios, usuarios, solicitudes
├── assets/      css, js, img
├── config/      conexion.php
├── includes/    header, navbar, footer, verificar_sesion y componentes
└── sql/         autocare_db.sql
```
