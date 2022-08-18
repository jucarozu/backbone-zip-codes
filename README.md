# Backbone Zip Codes
API REST para obtener la información de códigos postales de México.

## Requerimientos

* Nginx
* PHP
* Composer
* Postgres

## Instrucciones

### Opción 1: Local

1. En Postgres, crear base de datos 'backbone-zip-codes'
2. Ejecutar en la línea de comandos:
   * cd src
   * composer install
   * cp .env.example .env
   * php artisan key:generate
3. Añadir datos de conexión a la base de datos en el archivo .env
4. Ejecutar migraciones y seeders:
   * php artisan app:init
5. Iniciar servidor
   * php artisan serve --host=localhost --port=9000

#### Tests:

1. En Postgres, crear base de datos 'backbone-zip-codes-tests'
2. Ejecutar en la línea de comandos:
   * cd src 
   * php artisan test

### Opción 2: Cloud

https://backbone-zip-codes.herokuapp.com/api/zip-codes/{zip_code}

## Documentación

https://documenter.getpostman.com/view/2735177/VUqmwemS

## Tecnologías utilizadas

### Backend

* PHP
* Laravel

### Base de datos

* Postgres

## Solución

El planteamiento de la solución incluyó los siguientes aspectos:

1. Diseño de base de datos relacional con las siguientes tablas y sus respectivos campos:
   * federal_entities
     * id
     * key
     * name
     * code
   * municipalities
     * id
     * key
     * name
     * federal_entity_id
   * zip_codes
     * id
     * zip_code
     * locality
     * municipality_id
   * settlement_types
     * id
     * key
     * name
   * settlements
     * id
     * key
     * name
     * zone_type
     * settlement_type_id
     * zip_code_id
2. Uso de migraciones y seeders para la creación de la estructura de la base de datos y la inserción masiva de los datos desde un archivo de texto obtenido a partir la fuente de información proporcionada por el gobierno mexicano.
3. La técnica empleada para la inserción masiva está orientada a optimizar el rendimiento en el proceso, para lo cual se tuvieron en cuenta los siguientes puntos:
   * Mapear las filas del archivo de texto para crear una colección con claves y valores fácil de procesar a la hora de hacer las inserciones en las tablas.
   * Hacer uso de encadenamiento de métodos de colección para evitar el uso de variables innecesarias y hacer más legible el código.
   * Construir cada registro de entidad federativa, municipio, código postal, tipo de asentamiento y asentamiento a partir de las filas del archivo y dividirlos en grupos de 1000 haciendo uso de chunks para inserciones masivas en las tablas correspondientes, para evitar exceder el uso de memoria y disminuir los tiempos de la operación. Si hay registros duplicados, se omiten. Además, se valida con el método upsert de Eloquent si el registro actual ya se ha insertado.
   * Obtener todos los registros recién insertados y construir un array donde el índice se compone por el campo 'key' de la tabla (el cual se obtiene del archivo de texto) y el valor es el ID de cada registro recién insertado (llave primaria), para que luego puedan ser obtenidos como llave foránea en la inserción de los registros de la siguiente tabla dependiente (por ejemplo, en el caso donde la tabla padre es federal_entities y la tabla hija es municipalities).
   * Siguiendo con el punto anterior, con el fin de garantizar que se esté accediendo al ID correcto, se crean tantas dimensiones en el array como se necesiten para lograr que se usen como índices los campos que conforman la llave única de la tabla actual (por ejemplo, para definir el índice de municipalities, se usan tanto el campo 'key' de federal_entities como el campo 'key' de municipalities, dado que en este caso pueden existir municipios con la misma clave en diferentes entidades federativas). 
4. En el diseño de la solución para el endpoint que retorna la información de un código postal enviado como parámetro de ruta, se tuvieron en cuenta los siguientes puntos:
   * Separar las responsabilidades al hacer uso de un servicio que se encarga de obtener la información del código postal dado para evitar que dicha lógica quede en el controlador, el cual solamente se encarga de procesar la petición HTTP y devolver una respuesta.
   * Hacer uso de caché para agilizar los tiempos de respuesta al consultar la información de cada código postal.
   * Implementar un API JSON resource para definir el formato de la respuesta dada cumpliendo con el estándar JSON:API.
5. Para el feature test, se usó Fluent Assertable JSON para verificar que la respuesta dada sea exactamente la esperada.
6. Para la CI/CD, se usaron GitHub Actions para verificar que en cada push se simule el entorno y pasen los tests.
7. De manera general, se implementaron las siguientes prácticas:
   * Uso de un helper personalizado para reemplazar los acentos en los campos de tipo texto durante el proceso de inserción en base de datos.
   * Uso de accesores en los modelos para devolver los campos de tipo texto en mayúsculas.
   * Uso de un comando personalizado de Artisan 'app:init', para el proceso de inicialización de la base de datos.
   * Uso de excepciones personalizadas para realizar validaciones en el proceso de carga del archivo de texto.
   * Uso de variables de entorno y archivos de configuración.
