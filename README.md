# Productos + Clientes CRUD — CodeIgniter 3

Proyecto de familiarización con el stack **CodeIgniter 3** para el módulo
"Oficina del Agua". Contiene dos CRUDs sobre la misma estructura:

- **Productos** — prueba de concepto provista por el docente (crear, listar,
  editar, eliminar).
- **Clientes** — CRUD construido por el equipo siguiendo el mismo patrón,
  como ejercicio de práctica antes del proyecto del módulo.

Todo corre con **Docker**: PHP 8.3 con Apache en un contenedor y MariaDB 11.4
en otro, conectados entre sí.

## Stack utilizado

| Tecnología | Uso |
|---|---|
| PHP 8.3 | Lenguaje (extensión `mysqli`) |
| CodeIgniter 3 | Framework MVC |
| MariaDB 11.4 | Motor de base de datos |
| Apache 2.4 | Servidor web (dentro de la imagen `php:8.3-apache`) |
| Composer | Gestor de dependencias (`vlucas/phpdotenv`, `codeigniter/framework`) |
| Bootstrap 5 (CDN) | Estilos de las vistas |
| Docker Compose | Orquestación de los contenedores `web` y `db` |

## Requisitos

- **Docker Desktop** corriendo (es la forma principal de ejecutarlo)
- O bien, sin Docker: PHP 8.1+, Composer y MySQL/MariaDB locales

## Instalación

```bash
composer install
```

Instala el núcleo de CodeIgniter y `vlucas/phpdotenv` dentro de `vendor/`.

> Nota: puede aparecer un error inofensivo de un script (`sed: invalid command`)
> al instalar una dependencia de testing (`vfsstream`): es un script pensado
> para GNU `sed` que falla fuera de Linux; no afecta a la aplicación.

## Configuración

El proyecto lee la conexión a la BD desde variables de entorno:

1. Copia `.env.example` a `.env` y ajusta tus datos si corres localmente:

```env
DB_HOSTNAME=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=productos_crud
```

2. Con Docker **no hace falta tocar `.env`**: el `docker-compose.yml` define
   las mismas variables (`DB_HOSTNAME: db`, etc.) y esas tienen prioridad,
   porque phpdotenv se carga en modo inmutable (no pisa variables ya existentes).

La lectura ocurre en `index.php` (carga el `.env` antes de arrancar el
framework) y `application/config/database.php` consume los valores con `getenv()`.

## Base de datos

- **Motor:** MariaDB 11.4.
- **Conexión:** se configura en `application/config/database.php`
  (hostname, usuario, contraseña, base, driver `mysqli`).
- **Estructura de tablas:** este proyecto no usa migraciones; las tablas se
  definen con scripts SQL en `database/`:
  - `database/schema.sql` → tabla `productos`
  - `database/schema_cliente.sql` → tabla `clientes`

### Con Docker

Al primer arranque, MariaDB ejecuta automáticamente todo lo que esté en
`docker-entrypoint-initdb.d/` (ahí está montado `schema.sql`). Para `clientes`,
importa su script manualmente:

```powershell
Get-Content database\schema_cliente.sql -Raw | docker compose exec -T db mariadb -uroot -psecret
```

Verifica:

```powershell
docker compose exec db mariadb -uroot -psecret productos_crud -e "DESCRIBE clientes;"
```

⚠️ Los scripts de inicialización **solo corren cuando el volumen `db_data`
está vacío**. Si cambias un schema después, borra el volumen:
`docker compose down -v` y vuelve a subir.

### Sin Docker

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/schema_cliente.sql
```

## Ejecución

```powershell
docker compose up -d --build
```

URLs de la aplicación:

- Productos: <http://localhost:8000/index.php/productos>
- Clientes: <http://localhost:8000/index.php/clientes>

Sin Docker: `php -S localhost:8000` desde la raíz (mismas URLs).

Comandos útiles:

```powershell
docker compose logs -f web   # errores de PHP/Apache en vivo
docker compose down          # apagar (conserva los datos)
docker compose down -v       # apagar y borrar la BD
```

---

## Estructura del proyecto

| ¿Qué buscas? | Dónde está |
|---|---|
| Rutas | `application/config/routes.php` (controlador por defecto) |
| Controladores | `application/controllers/` (`Productos.php`, `Clientes.php`) |
| Modelos | `application/models/` (`Producto_model.php`, `Cliente_model.php`) |
| Vistas | `application/views/` (+ `views/templates/` header/footer compartidos) |
| Configuraciones | `application/config/` (`config.php`, `database.php`, `autoload.php`...) |
| Migraciones | CI3 no las usa aquí: las tablas viven en scripts SQL de `database/` |
| Dependencias | `composer.json` → instaladas en `vendor/` |

Otros archivos clave en la raíz: `index.php` (front controller: carga `.env`
y arranca el framework), `Dockerfile` y `docker-compose.yml`.

## Flujo de una petición

Ejemplo: guardar un cliente nuevo.

```
Navegador envía POST /index.php/clientes/crear
        ↓
routes.php resuelve el controlador Clientes → método crear()
        ↓
form_validation valida los campos contra sus reglas
        ↓
Cliente_model->crear([...]) → Query Builder genera INSERT → MariaDB
        ↓
flashdata (mensaje) + redirect('clientes')
        ↓
GET /index.php/clientes → index() → obtener_todos() → vista index.php
        ↓
HTML final al navegador
```

## Operaciones CRUD

| Operación | ¿Dónde se implementa? | ¿Cómo funciona? |
|---|---|---|
| **Crear** | `Clientes::crear()` + vista `crear.php` | Valida con `form_validation`; si pasa, `Cliente_model->crear()` hace `INSERT` vía Query Builder y redirige con mensaje flash |
| **Consultar** | `Clientes::index()` + vista `index.php` | `obtener_todos()` hace `SELECT * ORDER BY nombre` y pasa el resultado a la tabla |
| **Actualizar** | `Clientes::editar($id)` + vista `editar.php` | Busca por id (404 si no existe); valida; `actualizar()` hace `UPDATE WHERE id`; si falla validación repuebla el formulario con `set_value()` |
| **Eliminar** | `Clientes::eliminar($id)` desde `index.php` | Formulario POST propio (CSRF + confirm JS); `eliminar()` hace `DELETE WHERE id` y redirige |

## Problemas encontrados y soluciones

1. **"Access denied for user 'root'" al conectar** — El volumen de MariaDB
   había quedado inicializado con otra contraseña (los scripts de inicio solo
   corren la primera vez). Solución: `docker compose down -v` y volver a subir
   para reinicializar con la contraseña actual.

2. **Warning `mkdir(): Invalid path` en sesiones** — En el contenedor no existe
   `session.save_path`. Solución: en `config.php`,
   `$config['sess_save_path'] = sys_get_temp_dir();` (portable entre Docker y Windows).

3. **Error SQL `1064` al importar el schema** — Usé `//` como comentario,
   que no es válido en SQL (y `--` exige un espacio después). Solución:
   comentarios con `-- texto`.

4. **Los botones apuntaban a una IP interna de Docker (`172.18.0.3`)** —
   `$config['base_url'] = ''` hacía que CI3 autodetectara la IP del contenedor.
   Solución: fijar `'http://localhost:8000/'` en `config.php`.

5. **404 de Apache al hacer clic en "Nuevo cliente"** — Las vistas usaban
   `base_url()`, que NO agrega `index.php` a la URL. Sin mod_rewrite, esas rutas
   no existen. Solución: usar `site_url()` para los enlaces de navegación
   (sí incluye el `index_page`).

6. **Docker daemon no corría tras cerrar/reiniciar** — Basta abrir Docker
   Desktop y esperar a que esté "Running"; luego `docker compose up -d`.

## Buenas prácticas investigadas

1. **Validación siempre del lado del servidor** (`form_validation`): la del
   navegador se puede saltar fácilmente; la del servidor protege los datos.
2. **Token CSRF en todos los formularios** (`form_open()` lo inyecta solo):
   evita que sitios externos envíen formularios en nombre del usuario.
3. **Escapar la salida** (`html_escape()`): evita XSS — que un campo guardado
   con `<script>` se ejecute en otros navegadores.
4. **Query Builder en vez de SQL concatenado**: los valores van escapados por
   el framework, reduciendo riesgo de inyección SQL.
5. **Credenciales fuera del código** (`.env` + `getenv()`): no publicar
   contraseñas en el repo; mismo enfoque que Laravel.
6. **Convención sobre configuración de CI3**: nombres de clase/archivo
   consistentes (`Cliente_model` ↔ `cliente_model.php`) hacen que el autoload
   del framework funcione sin registrar nada.

## Reflexión técnica

**1. ¿Qué fue lo que más te costó entender del framework?**
Entender qué se carga automáticamente en cada petición y qué no:
`autoload.php` trae librerías/helpers globales (`database`, `session`, `url`,
`form`), mientras que modelos y vistas se cargan manualmente donde se usan.
También me costó distinguir `base_url()` vs `site_url()` — casi iguales, pero
solo la segunda agrega `index.php` a la URL.

**2. ¿Qué parte de la estructura te pareció más importante?**
La carpeta `application/`: concentra TODO lo nuestro (controllers, models,
views, config). La carpeta `system/` es el framework y no se toca. Saber eso
divide el problema en "mi código" vs "el framework".

**3. Explica con tus propias palabras cómo funciona una petición.**
PHP no mantiene la app viva: en cada visita arranca de cero. `index.php` carga
el `.env` y el framework, el router decide qué controlador atiende la URL,
el método del controlador valida y pide datos al modelo, el modelo habla con
MariaDB usando Query Builder, el controlador pasa esos datos a una vista y la
vista devuelve HTML. Al responder, la memoria se limpia; lo único que sobrevive
está en la BD o en la sesión.

**4. Menciona 3 buenas prácticas y por qué son importantes.**
Validación server-side (la del cliente se salta), token CSRF (evita envíos
falsificados desde otros sitios) y escapar la salida con `html_escape()`
(evita XSS). Detalle completo en la sección anterior.

**5. Menciona un problema técnico y cómo lo solucionaste.**
El más didáctico fue el de `base_url` vacío: los botones generaban URLs hacia
la IP interna del contenedor y luego 404. Lo diagnosticé comparando el HTML
generado (con curl) contra lo esperado, entendí la diferencia entre
`base_url()` y `site_url()`, y fijé la URL correcta en `config.php`.

**6. ¿Qué aprendiste que te servirá para el proyecto del módulo?**
Levantar el entorno completo con Docker Compose (app + BD + import automático
de schemas), diagnosticar con logs (`docker compose logs -f web`) y con curl
en vez de adivinar en el navegador, y el flujo completo MVC: ahora puedo
copiar el patrón clientes para cualquier entidad nueva (contadores, tarifas...)
cambiando tabla, campos y validaciones.
