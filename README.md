# One Piece Web Scraping & SQL Generator

Proyecto de **Web Scraping** desarrollado en PHP para extraer información estructurada (personajes, tripulaciones, recompensas y frutas del diablo) desde la wiki oficial de *One Piece Fandom*, procesar el árbol de nodos HTML mediante **DOMDocument** y **DOMXPath**, y generar sentencias `INSERT` en archivos `.sql` listas para importar a una base de datos relacional.

---

## 📋 Arquitectura del Proyecto

```text
scraping/
├── docker-compose.yaml        # Configuración del contenedor PHP 8.2 con Apache
├── .gitignore                 # Exclusión de archivos descargados y generados
├── README.md                  # Documentación del proyecto
└── scrap/
    ├── funciones.php          # Biblioteca de funciones (scraper y generadores SQL)
    ├── scrap-descarga.php     # Script para descargar el HTML de la página web
    ├── html/                  # Carpeta local para almacenar el HTML descargado
    ├── parse/
    │   ├── parse-per.php      # Parser de Tripulaciones y Personajes (con recompensas)
    │   └── parse-frut.php     # Parser de Frutas del Diablo y Consumidores
    └── sql/                   # Archivos .sql generados listos para la base de datos
```

---

## 🚀 Guía de Ejecución Paso a Paso

Todo el entorno se encuentra containerizado con Docker para asegurar compatibilidad y portabilidad.

### 1. Iniciar el contenedor

Párate en la raíz del proyecto (`scraping/`) e inicia el servicio en segundo plano:

```bash
docker compose up -d
```

### 2. Acceder a la terminal del contenedor

Ingresa a la consola interactiva del contenedor `scraping-alg`:

```bash
docker exec -it scraping-alg bash
```

*(Estarás situado directamente en `/var/www/html/`).*

### 3. Descargar el HTML de la página

Ejecuta el script de descarga para obtener el código fuente de la web:

```bash
php scrap-descarga.php
```

> 💡 **Nota:** Si deseas alternar entre la página de *Recompensas* o la de *Frutas del diablo*, simplemente descomenta la URL correspondiente en `scrap-descarga.php`.

### 4. Extraer los datos y generar el SQL

Para procesar el HTML descargado y generar las sentencias SQL con las dos tablas correspondientes:

* **Para Tripulaciones y Personajes:**
  ```bash
  php parse/parse-per.php
  ```
  *Genera `sql/one_piece.sql` con las tablas `tripulaciones` y `personajes`.*

* **Para Frutas del Diablo y Consumidores:**
  ```bash
  php parse/parse-frut.php
  ```
  *Genera `sql/frutas_one_piece.sql` con las tablas `frutas` y `consumidores`.*

### 5. Salir del contenedor

```bash
exit
```

---

## 🗄️ Estructura de las Tablas Generadas

### Entidad 1: Personajes y Tripulaciones (`sql/one_piece.sql`)

* **`tripulaciones`**:
  * `nombre` (`VARCHAR`): Nombre oficial de la tripulación u organización pirata.
* **`personajes`**:
  * `nombre` (`VARCHAR`): Nombre del personaje.
  * `recompensa` (`BIGINT` / `DECIMAL`): Valor numérico limpio de la recompensa (sin puntos y con valor `0` para los no revelados).
  * `id_tripulacion` (`VARCHAR`): Relación con la tripulación a la que pertenece.

### Entidad 2: Frutas y Consumidores (`sql/frutas_one_piece.sql`)

* **`frutas`**:
  * `nombre` (`VARCHAR`): Nombre de la Fruta del Diablo.
* **`consumidores`**:
  * `consumidor_anterior` (`VARCHAR`): Usuario previo de la fruta.
  * `consumidor_actual` (`VARCHAR`): Usuario actual que consumió la fruta.
  * `fruta` (`VARCHAR`): Nombre de la fruta asociada.

---

## 🛠️ Tecnologías y Conceptos Aplicados

* **PHP 8.2**: Funciones nativas de manejo de archivos (`fopen`, `fwrite`, `file_get_contents`, `file_put_contents`).
* **DOMDocument & DOMXPath**: Parsing y consulta de nodos HTML utilizando ejes relativos (`.//td`, `preceding::span`).
* **Expresiones Regulares (`preg_match`)**: Limpieza y extracción precisa de valores numéricos de recompensas ignorando referencias y texto auxiliar.
* **Docker & Docker Compose**: Despliegue del entorno de desarrollo.
