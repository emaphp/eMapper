eMapper
==============

**The Extensible Mapper Package for PHP**

<br/>
**Autor**: Emmanuel Antico
<br/>
**Versión**: 3.0.0

<br/>
Ultimas modificaciones
------------------
<br/>
2014-03-01 - Versión 3.0.0

  * Obsoleto: Models.
  * Agregado: Result maps.
  * Agregado: Entidades.
  * Agregado: Soporte para bases de datos SQLite y PostgreSQL.
  * Agregado: SQL dinámico.
  * Agregado: Atributos dinámicos.
  * Agregado: Agrupamiento.
  * Agregado: Funciones de indexado y agrupamiento.
  * Corregido: Varios bugs de la versión anterior.

<br/>
Dependencias
--------------
<br/>
- PHP >= 5.4
- Paquete [annotations](https://github.com/marcioAlmada/annotations "")
- Paquete [eMacros](https://github.com/emaphp/eMacros "")
 
<br/>
Instalación
--------------
<br/>
**Instalación a través de Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "dev-master"
    }
}
```

<br/>
Introducción
------------

<br/>
*eMapper* es una librería PHP que apunta a proveer una herramienta de mapeo de datos simple, poderosa y altamente customizable. Viene con algunas características interesantes no incluidas en otros frameworks:

- **Mapeo customizado**: Los resultados pueden mapearse a un tipo particular a través de una *expresión de mapeo*.
- **Indexado y Agrupamiento**: Los elementos dentro de una lista pueden ser indexados y/o agrupados por una valor de columna.
- **Tipos customizados**: Es posible definir tipos y manejadores de tipo customizados.
- **Caché**: Los datos mapeados pueden almacenarse en caché utilizando APC o Memcache.
- **SQL Dinámico**: Las consultas pueden contener expresiones escritas en el lenguaje *eMacros*.


<br/>
Primeros pasos
-----------

<br/>
Para comenzar a trabajar con *eMapper* deberemos primero crear una instancia de alguna de las clases de mapeo disponibles en la librería. La clase a instanciar dependerá del servidor de base de datos que estemos utilizando.

<br/>
**MySQL**

<br/>
```php
//incluir autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

//mapper MySQL/MariaDB
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
```
<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Parámetros de clase MySQLMapper</th>
        </tr>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>database</td>
            <td>Cadena / Objeto</td>
            <td>Nombre de la base de datos / Instancia mysqli</td>
            <td><em>ninguno</em></td>
        </tr>
        <tr>
            <td>host</td>
            <td>Cadena</td>
            <td>Nombre/Dirección del host</td>
            <td>mysqli.default_host (php.ini)</td>
        </tr>
        <tr>
            <td>user</td>
            <td>Cadena</td>
            <td>Nombre de usuario</td>
            <td>mysqli.default_user (php.ini)</td>
        </tr>
        <tr>
            <td>password</td>
            <td>Cadena</td>
            <td>Contraseña de usuario</td>
            <td>mysqli.default_pw (php.ini)</td>
        </tr>
        <tr>
            <td>port</td>
            <td>Cadena</td>
            <td>Puerto de conexión</td>
            <td>mysqli.default_port (php.ini)</td>
        </tr>
        <tr>
            <td>socket</td>
            <td>Cadena</td>
            <td>Socket de conexión</td>
            <td>mysqli.default_socket (php.ini)</td>
        </tr>
        <tr>
            <td>charset</td>
            <td>Cadena</td>
            <td>Set de caracteres (seteado con mysqli::set_charset)</td>
            <td>'UTF-8'</td>
        </tr>
        <tr>
            <td>autocommit</td>
            <td>Booleano</td>
            <td>Autocommit de consultas</td>
            <td>TRUE</td>
        </tr>
    </tbody>
</table>

<br/>
**SQLite**

<br/>
```php
//incluir autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\SQLite\SQLiteMapper;

//mapper SQLite
$mapper = new SQLiteMapper('company.db');
```

<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Parámetros de clase SQLiteMapper</th>
        </tr>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>filename</td>
            <td>Cadena / Objeto</td>
            <td>Archivo de base de datos / Instancia SQLite3</td>
            <td><em>ninguno</em></td>
        </tr>
        <tr>
            <td>flags</td>
            <td>Entero</td>
            <td>Flags de conexión</td>
            <td>SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE</td>
        </tr>
        <tr>
            <td>encription_key</td>
            <td>Cadena</td>
            <td>Clave de encriptado</td>
            <td><em>null</em></td>
        </tr>
    </tbody>
</table>

<br/>
**PostgreSQL**

<br/>
```php
//incluir autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\PostgreSQL\PostgreSQLMapper;

//mapper PostgreSQL
$mapper = new PostgreSQLMapper('dbname=company user=test password=test');
```
<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Parámetros de clase SQLiteMapper</th>
        </tr>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>connection_string</td>
            <td>Cadena / Recurso</td>
            <td>String de conexión / Conexión a base de datos</td>
            <td><em>ninguno</em></td>
        </tr>
        <tr>
            <td>connect_type</td>
            <td>Entero</td>
            <td>Tipo de conexión</td>
            <td><em>ninguno</em></td>
        </tr>
    </tbody>
</table>

<br/>

Al instanciar una clase mapper estaremos creando una instancia del objeto con los valores de configuración que hayamos suministrado. La conexión a la correspondiente base de datos se realizará recién al momento de enviar una consulta. Alternativamente, podemos utilizar el metodo *connect* para verificar la correcta conexión al servidor.

<br/>
Arreglos
-------

<br/>
**Obtener una lista de filas como arreglo**

<br/>
Este primer ejemplo muestra como a través del método **query** enviamos una consulta al servidor de base de datos. El resultado obtenido es luego mapeado al tipo por defecto: una lista de arreglos. Cada arreglo dentro de la lista posee claves numéricas y asociativas.
```php
//obtener lista de usuarios como lista de arreglos
$usuarios = $mapper->query("SELECT * FROM usuarios");

//...

//cerrar conexión
$mapper->close();
```

<br/>
**Obtener una fila como un arreglo asociativo**

<br/>
El tipo de dato a devolver por una consulta se define a través del método **type**. Este método recibe una expresión de mapeo indicando el tipo de dato esperado. Configurar la manera de convertir un resultado a un tipo dado requiere hacer uso del encadenamiento de métodos (*method chaining*). Para obtener una fila como un arreglo definimos el tipo de dato como *array* (o *arr*). Cuando mapeamos a arreglo podemos también definir el tipo de arreglo como segundo parámetro.
```php
use eMapper\Result\ResultInterface;

//obtener datos de usuario por id como arreglo asociativo
$usuario = $mapper
->type('array', ResultInterface::ASSOC)
->query("SELECT * FROM usuarios WHERE id_usuario = 1");
```

<br/>

Objetos
-------

<br/>
**Obtener una fila como objeto**

<br/>
Para obtener una instancia de *stdClass* de una fila declaramos el tipo a devolver como *object* (o también *obj*).

```php
//obtener usuario por id como objeto (stdClass)
$usuario = $mapper->type('object')->query("SELECT * FROM usuarios WHERE id_usuario = 1");
```

<br/>
**Obtener una fila como una instancia de clase**

<br/>
Es posible también indicar la clase del objeto a devolver dentro de la expresión de mapeo. Para demostrar esta funcionalidad, hemos diseñado una clase *Usuario* dentro del paquete *Acme*.
```php
namespace Acme;

class Usuario {
    public $id_usuario;
    public $nombre;
    public $password;
    public $email;
}
```
La clase de un objeto debe indicarse inmediatamente despues del tipo de la siguiente manera.

```php
//obtener usuario por id instancia de Acme\User
$usuario = $mapper
->type('obj:Acme\User')
->query("SELECT * FROM usuarios WHERE id_usuario = 1");
```
<br/>
Escalares
-------

<br/>
**Obtener valor de columna como cadena de texto**

<br/>
Las expresiones de mapeo también soportan tipos de dato simples.
```php
//obtener nombre de usuario con id 1
$nombre = $mapper
->type('string')
->query("SELECT nombre FROM usuarios WHERE id_usuario = 1");
```
<br/>
**Obtener valor de columna como entero**

<br/>
Por defecto, los datos de tipo escalar se obtienen leyendo desde la primera columna. Este comportamiento puede modificarse especificando el nombre de la columna desde donde obtener el valor como segundo parámetro.

```php
//obtener id de usuario con nombre 'jperez'
$id = $mapper
->type('int', 'id_usuario')
->query("SELECT * FROM usuarios WHERE nombre = 'jperez'");
```

<br/>
Fechas
-----

<br/>
**Obtener valor de columna como DateTime**

<br/>
El tipo *DateTime* (cuyo alias es *dt*) nos permite obtener instancias de la clase *DateTime* desde una columna.

```php
//obtener fecha de venta
$fecha_venta = $mapper->type('dt')->query("SELECT fecha_venta FROM ventas WHERE id_venta = 324");
```
Columnas de tipo DATETIME y TIMESTAMP son convertidas a instancias de *DateTime* automaticamente.

```php
//obtener datos de usuario
$usuario = $mapper->type('arr')->query("SELECT * FROM usuarios WHERE id_usuario = 2");

//mostrar ultimo login
echo $usuario['ultimo_login']->format('d/m/Y H:i:s');
```


<br/>
Listas
-----

<br/>
**Obtener un listado de objetos**

Podemos también obtener listas de un determinado tipo agregando corchetes al final de la expresión de mapeo.

```php
//obtener usuarios como listado de objetos
$usuarios = $mapper
->type('object[]')
->query("SELECT * FROM usuarios ORDER BY id_usuario ASC");
```

<br/>
**Obtener una lista de enteros**

<br/>
Esta sintaxis también es soportada al mapear a tipos de dato simples. Por ejemplo, *integer[]* devuelve un listado de enteros.

```php
//obtener ids de usuario como lista
$ids = $mapper->type('integer[]')->query("SELECT id_usuario FROM usuarios");
```

<br/>
**Obtener un listado de cadenas**

<br/>
También es posible definir la columna desde la cual obtener los valores.

```php
//obtener nombre de usuario como lista
$nombres = $mapper->type('str[]', 'nombre')->query("SELECT * FROM usuarios");
```

<br/>
Indexado
-------------

<br/>
**Obtener uns lista de objetos indexada por una columna**

<br/>
Las listas de arreglos y objetos pueden ser indexadas por una columna especificando el nombre de la misma entre corchetes en la expresión de mapeo. El siguiente ejemplo obtiene una lista donde cada índice es el valor correspondiente a la columna *id_usuario* para esa fila.

```php
//obtener un listado de objetos indexado por id_usuario
$usuarios = $mapper->type('object[id_usuario]')
->query("SELECT * FROM usuarios");
```

<br/>
**Obtener un listado de arreglos indexados por una columna**

<br/>
Esta sintaxis es también soportada al mapear a arreglos.

```php
use eMapper\Result\ResultInterface;

//obtener un listado de arreglos asociativos indexados por id_usuario
$usuarios = $mapper
->type('array[id_usuario]', ResultInterface::ASSOC)
->query("SELECT * FROM usuarios");
```

Resulta útil recordar que solo es posible indexar por columnas presentes en el set de columnas devueltas por el resultado. Esto implica que al mapear a un arreglo con índices numéricos debemos expresar la columna como un entero.

```php
use eMapper\Result\ResultInterface;

//obtener un listado de arreglos numéricos indexados por la primera columna
$usuarios = $mapper
->type('array[0]', ResultInterface::NUM)
->query("SELECT * FROM usuarios");
```

<br/>
**Obtener un listado de objetos indexados por una columna con un tipo customizado**

<br/>
Es posible especificar el tipo de dato por el cual indexar a continuación del nombre de la columna.

```php
//obtener listado de usuarios indexados por id convertido a cadena de texto
$usuarios = $mapper
->type('object[id_usuario:string]')
->query("SELECT * FROM usuarios");
```


<br/>
**Función de indexado**

Podemos generar un índice por cada elemento en la lista aplicando una *función de indexado* a través del método *index*. La función recibirá como argumento cada una de las filas devueltas por la consulta.

```php
//generar índice con email de usuario
$usuarios = $mapper->type('object[]')->index(function ($usuario) {
    return strstr($usuario->email, '@', true);
})->query("SELECT * FROM usuarios");
```

<br/>
Agrupado
-------

<br/>
**Agrupar por columna**

<br/>
El agrupamiendo permite organizar un listado de resultados con una característica en común entre varios arreglos. La sintaxis utilizada es muy similar a la vista para indexado de filas. El siguiente ejemplo muestra como agrupar un conjunto de resultados por la columna *categoria*.

```php
//obtener un listado de productos agrupados por categoría
$productos = $mapper
->type('obj<categoria:string>')
->query("SELECT * FROM productos");

//print_r($productos['software']);
//print_r($productos['hardware']);
//...
```

<br/>
**Indexado + Agrupamiento**

<br/>
El indexado y agrupamiento puede ser combinados para obtener listados de mayor precisión.

```php
//obtener productos agrupados por categoria e indexados por id
$productos = $mapper
->type('array<categoria>[id_producto:int]')
->query("SELECT * FROM productos");
```


<br/>
**Función de agrupamiento**

Podemos también definir la lógica de agrupamiento de una determinada fila a través de una *función de agrupamiento*.

```php
//obtener productos agrupados por código
$productos = $mapper->type('obj[id_producto]')->group(function ($producto) {
    //obtener las primeras 3 letras
    return substr($producto->codigo, 0, 3);
})->query("SELECT * FROM productos");
```

<br/>
Consultas
-------

<br/>
**Enviando parámetros a una consulta**

<br/>
Al invocar a la función **query** podemos especificar un número arbitrario de argumentos. Cada uno de estos argumentos puede ser referenciado desde la consulta con una expresión encabezada por el caracter **%** seguido de un **identificador de tipo** entre llaves.

```php
//obtener usuario con id 1
$usuario = $mapper->type('obj')->query("SELECT * FROM usuarios WHERE id_usuario = %{int}", 1);
```
Este ejemplo muestra como utilizar este tipo de expresiones para generar una consulta de inserción de datos.

```php
//valores a insertar
$nombre = 'jperez';
$password = sha1('qwerty321');
$admin = false;
$imagen = file_get_contents('foto.jpg');

//insertar datos ('s' => 'string', 'b' => 'boolean', 'x' => 'blob')
$mapper->query("INSERT INTO usuarios (nombre, password, admin, imagen)
                VALUES (%{s}, %{s}, %{b}, %{x})", $nombre, $password, $admin, $imagen);
```

<br/>
**Arreglos como parámetros**

<br/>
En caso de que el argumento sea una arreglo, los valores del mismo son convertidos al tipo especificado y luego concatenados. Esto resulta útil al realizar búsquedas con el operador **IN**.
```php
$valores = ['MXP412', 'TRY235','OFR255'];
//ejecutar consulta: SELECT * FROM productos WHERE codigo IN ('MXP412', 'TRY235', 'OFR255')
$products = $mapper->query("SELECT * FROM productos WHERE codigo IN (%{s})", $valores);
```

<br/>
**Especificando parámetros por orden de aparición**

<br/>
También es posible referenciar a un argumento por su orden de aparición. En lugar de especificar solo el tipo, utilizamos el número de argumento a insertar y (opcionalmente) un identificador de tipo.
```php
//primer argumento: %{0}
$products = $mapper
->type('obj[product_id]')
->query("SELECT * FROM productos WHERE id_producto = %{1} OR codigo = %{0:s}", 'PHN00098', 3);
```
Podemos también decir desde que subíndice obtener el valor a insertar. Un subíndice debe especificarse inmediatamente despues del número de argumento y colocarse entre corchetes.

```php
$args = array('id' => 1, 'jdoe', 'david');

$users = $mapper
->type('obj[]')
->query("SELECT * FROM usuarios
         WHERE id_usuario = %{0[id]} OR nombre = %{0[1]:str} OR nombre = %{0[2]:str}", $args);
```

<br/>
**Rangos**

Un rango nos permite insertar un subconjunto de elementos de una lista. Para especificar un rango utilizamos 2 valores: el índice desde donde empezar a insertar y el largo total del subconjunto (de manera similar a la función [array_slice](http://www.php.net/manual/en/function.array-slice.php "")).

```php
$list = array(45, 23, '43', '164', 43);

//obtener una sublista con los elementos '43' y '164'
$users = $mapper->type('obj[]')
->query("SELECT * FROM usuarios WHERE id_usuario IN (%{0[2..2]:i})", $list);
```

En caso de que uno de los valores del rango no sea especificado esto tendrá distintos significados:

* [..3] Obtiene los primeros 3 elementos.
* [1..] Obtiene todos los elementos excepto el primero.
* [..] Obtiene la lista completa.

<br/>
Este tipo de expresiones también puede utilizarse con cadenas de texto.

```php
$nombre = "XXXjperezXXX";

//obtener usuario con nombre 'jperez'
$usuario = $mapper->type('obj')
->query("SELECT * FROM usuarios WHERE nombre = %{0[3..6]}", $nombre);
```

<br/>
**Objetos y arreglos como argumento**

<br/>
Las consultas también soportan una sintaxis especial que les permite obtener valores desde arreglos y objetos especificando el nombre de propiedad/clave. Las expresiones de este tipo deben encabezarse con un símbolo ***#*** e ir seguidas del nombre de la propiedad (o clave) entre llaves. Esta sintaxis también permite especificar el tipo, subíndice y rango de la propiedad. Recordar que este tipo de expresiones requieren que el arreglo/objeto sea pasado como primer argumento de la consulta.

```php
//datos de usuario
$usuario = new stdClass();
$usuario->nombre = 'jperez';
$usuario->password = sha1('jperez321');
$usuario->admin = false;
$usuario->imagen = file_get_contents('foto.jpg');

//insert data
$mapper->query("INSERT INTO usuarios (nombre, password, admin, imagen)
                VALUES (#{nombre}, #{password:s}, #{admin}, #{imagen:blob})", $user);
```

<br/>
**Prefijo de base de datos**

<br/>
Por lo general, las tablas dentro de una base de datos son creadas utilizando un prefijo. Para definir el prefijo de una base de datos utilizamos el método *setPrefix*. La expresión **@@** puede utilizarse luego para insertar el prefijo previamente definido y así evitar tener que ingresar el mismo por cada tabla dentro de una consulta.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('proyecto');

//setear prefijo
$mapper->setPrefix('PRJ_');

//SQL: SELECT * FROM PRJ_items WHERE activo = TRUE
$items = $mapper->query("SELECT * FROM @@items WHERE activo = TRUE");
```

<br/>
Result maps
----------

<br/>
Un result map es una clase que permite definir que propiedades serán mapeadas hacia un objeto/arreglo. Utilizar un result map resulta ideal para casos en donde por algún motivo los valores de una columna deben ser almacenados utilizando otro nombre o con un tipo particular. Para definir el tipo de una propiedad y el nombre de la columna referenciada se utilizan annotations. El siguiente código muestra la implementación de un result map que define 4 propiedades. Las annotations **@type** y **@column** se utilizan para definir el tipo a utilizar y el nombre de la columna desde donde tomar el valor respectivamente. En caso de no definir el nombre de la columna entonces se asumirá que es idéntico al de la propiedad. Si el tipo no viene definido entonces se utilizará aquel asociado con la columna.

```php
namespace Acme\Result;

class UserResultMap {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
    public $nombre;
    
    /**
     * @column password
     */
    public $clave;
    
    /**
     * @type blob
     * @column imagen
     */
    public $avatar;
}
```

Para aplicar un result map a la lógica de mapeo debemos encadenar una invocación al método **result_map** pasando como argumento el nombre completo de la clase.

```php
$usuario = $mapper
->type('obj')
->result_map('Acme\Result\UserResultMap')
->query("SELECT * FROM usuarios WHERE id_usuario = 1");
```

El valor devuelto será una instancia de *stdClass* con las propiedades *id*, *nombre*, *clave* y *avatar*.

<br/>
Entidades
----------

<br/>
Una entidad es una clase que, de la misma manera que un result map, define cuales propiedades deben ser mapeadas de acuerdo a la estructura de una clase. A diferencia de un result map, una entidad puede utilizarse dentro de una expresión de mapeo directamente. 

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Producto {
    /**
     * @column id_producto
     */
    public $id;
    
    /**
     * @type str
     */
    public $codigo;
    
    /**
     * @column fecha_modificacion
     * @type string
     */
    public $fechaModificacion;
}
```

Otra de las diferencias con respecto a los result map es que las entidades deben ser declaradas utilizando la annotation **@entity**. La entidad *Producto* mostrada como ejemplo define 3 campos públicos: *id*, *codigo* y *fechaModificacion*. Este último campo lo hemos definido de tipo *string* para así evitar almacenarlo como instancia de *DateTime*, algo útil en casos donde sea necesario exportar un determinado valor a JSON. Mapear a una entidad no requiere mayor esfuerzo.

```php
$productos = $mapper
->type('obj:Acme\Entity\Producto[id]')
->query("SELECT * FROM productos");
```
Es necesario notar que al mapear a listas de entidades el índice a especificar debe ser la propiedad y no la columna asociada. Lo mismo ocurre en los casos donde se utilice un result map.

<br/>
En caso de declarar las propiedades de una entidad como privadas/protegidas debemos configurar los métodos setter y getter de cada una.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Perfil {
    /**
     * @column id_perfil
     * @setter setIdPerfil
     * @getter getIdPerfil
     */
    private $idPerfil;

    /**
     * @column id_usuario
     * @setter setIdUsuario
     * @getter getIdUsuario
     */
    private $idUsuario;
    
    /**
     * @type string
     * @setter setTipo
     * @getter getTipo
     */
    private $tipo;
    
    public function setIdPerfil($idPerfil) {
        $this->idPerfil = $idPerfil;
    }
    
    public function getIdPerfil() {
        return $this->idPerfil;
    }
    
    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }
    
    public function getIdUsuario() {
        return $this->idUsuario;
    }
    
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    public function getTipo() {
        return $this->tipo;
    }
}
```

<br/>
Statements
----------

<br/>
**Creación de statements**

<br/>
Una statement es una consulta a la cual se le asocia un identificador de tipo cadena y que se almacena dentro de un objeto mapper. Existen 2 métodos para creación de statements. El primero consiste en generar una instancia de la clase *eMapper\SQL\Statement* y luego agregarla utilizando el método *addStatement*. Al instanciar una statement debemos suministrar el identificador de la misma y la consulta propiamente dicha.

```php
use eMapper\SQL\Statement;

//crear statement
$stmt = new Statement('findAllUsers', "SELECT * FROM usuarios");

//guardar statement
$mapper->addStatement($stmt);
```
El otro método de creación consiste en invocar al método *stmt* del objeto mapper con los mismos parámetros utilizados previamente.

```php
//crear statement
$mapper->stmt('findAllUsers', "SELECT * FROM usuarios");
```
El método *stmt* retorna una referencia al objeto donde se almacena la statement, por lo que pueden encadenarse varias llamadas al mismo método.

```php
//crear statements
$mapper
->stmt('findAllUsers', "SELECT * FROM usuarios")
->stmt('findAllProducts', "SELECT * FROM productos")
->stmt('findAllSales', "SELECT * FROM ventas");
```

<br/>
**Ejecutar statements**

<br/>
La ejecución de una statement se realiza invocando al método *execute* con el identificador de la statement como parámetro. Al igual que el método *query*, podemos agregar un número arbitrario de argumentos.

```php
//agregar statement
$mapper->stmt('findUserByPK', "SELECT * FROM usuarios WHERE id_usuario = %{i}");

//ejecutar statement
$usuario = $mapper->type('obj')->execute('findUserByPK', 5);
```

<br/>
**Configuración**

<br/>
Tanto el constructor de la clase *Statement* como el método *stmt* soportan un tercer parámetro para definir las opciones por defecto de una statement. Podemos generar una instancia de configuración a través de los métodos estáticos *config* y *type* de la clase *Statement*. 


```php
use eMapper\SQL\Statement;

//definir tipo por defecto a lista de objetos
$stmt = new Statement('findAllProducts', "SELECT * FROM productos", Statement::type('obj[]'));
$mapper->addStatement($stmt);

//setear result map y tipo por defecto
$mapper->stmt('findAllUsers',
              "SELECT * FROM users",
              Statement::config()->result_map('Acme\Result\UserResultMap')->type('obj[id]'));

//invocar statements
$productos = $mapper->execute('findAllProducts'):
//...
$usuarios = $mapper->execute('findAllUsers');
```
El método *type* es una forma simplificada de definir el tipo por defecto de una consulta, mientras que *config* puede recibir como parámetro un arreglo de opciones soportadas por la librería. La configuración de la statement *findAllUsers* puede también definirse de la siguiente manera.

```php
Statement::config(['map.result' => 'Acme\Result\UserResultMap', 'map.type' => 'obj[id]']);
```
El listado de opciones de configuración puede verse en el *Apéndice II - Opciones de configuración*.

<br/>
Namespaces
----------

<br/>
**Organización de statements**

<br/>
Los *namespaces* son objetos destinados a poder organizar con mayor facilidad un listado de statements. Estos resultan de mucha utilidad en proyectos medianos y grandes, donde se crean un gran número de consultas. Para declarar un namespace debemos crear una instancia de la clase *eMapper\SQL\SQLNamespace*. El constructor de esta clase require que especifiquemos una cadena de texto destinado a identificar univocamente a ese namespace. Una vez creado podemos agregar un número arbitrario de statements utilizando la misma metodología vista anteriormente con las clases mapper. Para agregar un namespace a un objeto mapper utilizamos el método **addNamespace**.

```php
use eMapper\SQL\Statement;
use eMapper\SQL\SQLNamespace;

//declarar namespace
$ns = new SQLNamespace('usuarios');

//agregar statement
$stmt = new Statement('findAll', "SELECT * FROM usuarios");
$ns->addStatement($stmt);

//método alternativo
$ns->stmt('findByPK', "SELECT * FROM usuarios WHERE id_usuario = %{i}");

//agregar namespace
$mapper->addNamespace($ns);
```
Para ejecutar una statement dentro de un namespace debemos especificar el identificador del namespace junto con el de la statement. Para señalar el comienzo y el final de un identificador utilizamos el caracter '.'.

```php
$usuarios = $mapper->type('arr[]')->execute('usuarios.findAll');
//...
$usuario = $mapper->type('obj')->execute('usuarios.findByPK', 7);
```


<br/>
**Anidamiento**

<br/>
Un namespace puede contener otros namespaces en caso de que la complejidad del proyecto así lo requiera. Además del método **addNamespace** contamos también con **ns**. Este método retorna una referencia al namespace generado, lo cual resulta útil para encadenar invocaciones al método **stmt** y definir un grupo de statements rapidamente.

```php
use eMapper\SQL\Statement;
use eMapper\SQL\SQLNamespace;

//declarar namespace
$usersNamespace = new SQLNamespace('usuarios');

//namespace anidado
$profilesNamespace = new StatementNamespace('perfiles');
$profilesNamespace->stmt('findByUserId',
                         "SELECT * FROM perfiles WHERE id_usuario = %{i}",
                         Statement::type('obj[]'));

//anidar namespace
$usersNamespace->addNamespace($profilesNamespace);

//método alternativo
$usersNamespace->ns('admin')
->stmt('delete', "DELETE FROM usuarios WHERE id_usuario = %{i}")
->stmt('ban', "UPDATE usuarios SET estado = 'baneado' WHERE id_usuario = %{i}");

$mapper->addNamespace($usersNamespace);

//...
$perfil = $mapper->execute('usuarios.perfiles.findByUserId', 4);
//...
$mapper->execute('usuarios.admin.ban', 7);
```

<br/>
**Namespace customizados**

<br/>
Otra forma de organizar statements es crear nuestras propias clases de anidamiento. De esta forma evitamos declarar varias statements en el mismo archivo. Los namespaces customizados deben extender de la clase *eMapper\SQL\SQLNamespace*.

```php
namespace Acme\SQL;

use eMapper\SQL\SQLNamespace;
use eMapper\SQL\Statement;

class UsersNamespace extends SQLNamespace {
	public function __construct() {
		parent::__construct('usuarios');
		
		$this->stmt('findByUsername',
		            "SELECT * FROM usuarios WHERE nombre = %{s}");
		            
		$this->stmt('findByPK',
		            "SELECT * FROM usuarios WHERE id_usuario = %{i}",
		            Statement::type('obj'));
		            
		$this->stmt('findAll',
		            "SELECT * FROM usuarios",
		            Statement::type('obj[id_usuario]'));
	}
}
```


<br/>
Rutinas almacenadas
-----------------

<br/>
***NOTA:*** *Por el momento, esta feature solo esta disponible para la clase MySQLMapper.*

<br/>
**Invocando rutinas almacenadas**

<br/>
Las rutinas almacenadas son procedimientos del lado de la base de datos destinados a manejar lógica de persistencia. Estas rutinas pueden ser invocadas desde un objeto mapper gracias a una feature especial que traduce la invocación de un método no declarado en una invocación a una rutina. Esta feature logra esto a través de la sobrecarga de métodos de clase, también llamado [overloading](http://php.net/manual/en/language.oop5.overloading.php "").

```php
//SQL: CALL FindUserByUsername('jperez')
$usuario = $mapper->type('object')->FindUserByUsername('jperez');
```

<br/>
**Tipos**

En ocasiones será necesario especificar el tipo correspondiente a cada parámetro. Para definir el tipo de cada argumento debemos encadenar una llamada al método **sptypes**. Este método recibe un listado de parámetros que corresponde a los identificadores de tipo a utilizar por cada argumento.

```php
//SQL: CALL InsertNewUser('juana', 'clave123', TRUE)
$id_usuario = $mapper
->type('int')
->sptypes('s', 's', 'b')
->InsertNewUser('juana', 'clave123', 1);
```

<br/>
**Prefijo de base de datos**

<br/>
Es muy probable que las rutinas almacenadas se hayan declarado utilizando el prefijo de la base de datos. Por defecto, siempre que se invoca una stored procedure se agrega al principio de la misma el prefijo de la base de datos que haya sido definido. Podemos controlar este comportamiento a través del método **usePrefix**.

```php
use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//setear prefijo de base de datos
$mapper->setPrefix('EMP_');

//agregar prefijo
$mapper->usePrefix(true);

//SQL: CALL EMP_InsertImage('My Image', x...);
$id_imagen = $mapper
->type('integer')
->sptypes('string', 'blob')
->InsertImage('My Image', file_get_contents('image.png'));
```

<br/>
Configuración
--------------

<br/>
Un objeto mapper almacena internamente un arreglo de configuración donde se van agregando cada una de las opciones definidas por el usuario. Cada vez que se invoca un determinado método (**result_map**, **type**, etc) un nueva instancia del objeto es creada. Esta nueva instancia es identica a la original con la excepción de que posee un nuevo valor de configuración, correspondiente al método invocado. Esto tiene la ventaja de que la instancia original no se modifica por lo que puede seguir utilizandose dentro del script. El ejemplo a continuación utiliza el método **option** para generar una nueva instancia con el valor de configuración *map.type* seteado a *'integer'*. El valor de configuración *map.type* define el tipo al cual será mapeado un resultado. Para un listado más detallado de las opciones de configuración soportadas consulte el *Apéndice II - Opciones de configuración*.

```php
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$cuatro = $mapper->option('map.type', 'integer')->query("SELECT 2 + 2");
```
<br/>
Para almacenar valores en la instancia original debe utilizarse el método **set**. Este método recibe el nombre de la opción a setear y su correspondiente valor. Los valores almacenados pueden ser recuperados a través del método **get**.
```php
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('mi_valor', 'foo');
$foo = $mapper->get('mi_valor');
```
<br/>
Los valores de configuración también pueden ser referenciados desde adentro de una consulta. Este tipo de expresiones van encabezadas con el símbolo **@** seguido por el nombre de la opción entre llaves.

```php
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('tabla', 'usuarios');
$usuario = $mapper->type('obj')->query("SELECT * FROM @{tabla} WHERE user_id = 2");
```
En caso de que el valor de configuración no exista entonces se deja ese espacio en blanco.

<br/>
SQL Dinámico
-----------

<br/>
SQL Dinámico es una feature especial que nos permite agregar lógica en una consulta para que su estructura se modifique de acuerdo al listado de argumentos suministrados. Esta capacidad de automodificación se logra utilizando un lenguaje basado en [eMacros](https://github.com/emaphp/eMacros ""). El siguiente ejemplo ilustra una consulta que utiliza una de estas expresiones para definir la condición de búsqueda.

```php
$usuario = $mapper
->type('obj')
->query("SELECT * FROM usuarios WHERE [[ (if (int? (%0)) 'id_usuario = %{i}' 'nombre = %{s}') ]]", 5);
```
Las expresiones dinámicas son incluidas dentro de corchetes dobles. El ejemplo incluye un pequeño programa que inserta la condición de búsqueda según el tipo del primer argumento.

```lisp
(if (int? (%0)) "id_usuario = %{i}" "nombre = %{s}")
```
La función *if* evalua una condición y devuelve uno de los 2 argumentos de acuerdo a si esta es verdadera o falsa. La macro *int?* verifica si el argumento dado es de tipo entero. En este caso, el argumento pasado es el valor retornado por la macro *%0*, es decir, el primer argumento de la consulta. En caso de que el argumento sea de tipo entero entonces la condición contendrá el string *'id_usuario = %{i}'*. Caso contrario, la búsqueda se realizará por nombre (*'nombre = %{s}'*). La ejecución de la consulta, entonces, realizará la búsqueda de un usuario con ID igual a 5. Otro sería el caso utilizando un argumento de tipo cadena.

```php
$usuario = $mapper->type('obj')
->query("SELECT * FROM usuarios WHERE [[ (if (int? (%0)) 'id_usuario = %{i}' 'nombre = %{s}') ]]", 'jperez');
```

<br/>
**Diferencias con eMacros**

<br/>
El dialecto utilizado para expresiones dinámicas es ligeramente distinto al definido por eMacros. Las diferencias son menores aunque importantes.

 - **Valores de configuración**: Las funciones encabezadas por el caracter **@** permiten acceder a valores de configuración previamente definidos para determinar su existencia y valor.

```php
//setear columna de ordenamiento
$mapper->set('orden.columna', 'id_producto');

//(@orden.columna) => 'id_producto'
//(@orden.tipo?) => false
$productos = $mapper->type('obj[]')
->query("SELECT * FROM productos
         ORDER BY [[ (@orden.columna) ]] [[ (if (@orden.tipo?) (@orden.tipo) 'DESC') ]]");
```
 - **Funciones no incluidas**: La mayoria de las funciones propias de PHP no se hallan declaradas en el ambiente de ejecución por defecto. Cada vez que se trata de invocar una función no declarada se comprueba la existencia de la misma. De hallarse se intentará invocarla con los parámetros suministrados. Ejemplos de funciones no declaradas pero invocables son **count**, **str_replace**, **nl2br**, etc. Las funciones de clases/objetos no se encuentran incluidas por defecto. Tampoco las de importado de funciones y paquetes. Las funciones de salida **echo**, **var-dump** y **print-r** no se incluyen.
 - **Paquetes incluidos**: El ambiente de ejecución incluye solamente los paquetes **DatePackage** y **RegexPackage**. Incluir más paquetes requerirá declarar un entorno personalizado.


<br/>
**Expresiones dinámicas tipificadas**

<br/>
En ocasiones resulta util que el valor devuelto por una expresión dinámica sea convertido a un tipo en particular. Esto es posible utilizando expresiones tipificadas. Estas expresiones van incluidas dentro de llaves dobles. El siguiente ejemplo muestra un caso de uso de una expresión tipificada para introducir una cadena como criterio de búsqueda.

```php
$usuario = $mapper
->type('obj')
->query("SELECT * FROM usuarios WHERE nombre LIKE {{ (. '%' (%0) '%') }}", 'perez');
```
El tipo a utilizar para la conversión debe ser definido al principio de la expresión, justo despues de las llaves de apertura. En caso de no declarar el tipo se asumirá que el valor debe ser convertido a *string*.

```php
$productos = $mapper
->query("SELECT * FROM productos WHERE precio > {{:int (/ (%0) 2) }}", 45);
```

<br/>
**Entornos de ejecución**

<br/>
Un entorno de ejecución es una clase que define que funciones pueden ser invocadas dentro de una expresión dinámica. Estos entornos tienen asociado un ID de tipo cadena. El entorno por defecto tiene por ID *default*. Es posible definir el entorno de un objeto mapper a través del método *setEnvironment*. En el ejemplo a continuación se muestra la creación de 2 objetos mapper con entornos independientes.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Engine\SQLite\SQLiteMapper;

$mysql = new MySQLMapper('database');
$mysql->setEnvironment('mysql_env');

//...

$sqlite = new SQLiteMapper('database.db');
$sqlite->setEnvironment('sqlite_env);
```

<br/>
**Entornos personalizados**

<br/>
El método *setEnvironment* acepta un segundo argumento con la clase del entorno de ejecución a utilizar (por defecto *eMapper\Dynamic\Enviroment\DynamicSQLEnvironment*). Para construir un entorno de ejecución personalizado podemos extender de esta clase o directamente desde *eMapper\Environment\Environment*. El siguiente ejemplo muestra un entorno personalizado que incluye los paquetes **StringPackage** y **ArrayPackage**.

```php
namespace Acme\SQL\Environment;

use eMacros\Environment\Environment;
use eMapper\Configuration\Configuration;
use eMacros\Package\RegexPackage;
use eMacros\Package\DatePackage;
use eMacros\Package\ArrayPackagePackage;
use eMacros\Package\StringPackage;
use eMapper\Dynamic\Package\CorePackage;

class CustomSQLEnvironment extends Environment {
    use Configuration;
    
    public function __construct() {
		$this->import(new RegexPackage());
		$this->import(new DatePackage());
		$this->import(new ArrayPackage());
		$this->import(new StringPackage());
		$this->import(new CorePackage());
	}
}
```
El trait *Configuration* permite que el ambiente almacene las opciones de configuración definidos para esa instancia. Es interesante notar que la clase **CorePackage** no es la incluida en *eMacros*. Es importante incluir este paquete en reemplazo del otro dado que de otra forma las funciones de acceso a propiedades no funcionarán del todo bien con entidades.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mysql = new MySQLMapper('database');
$mysql->setEnvironment('mysql_env', 'Acme\SQL\Environment\CustomSQLEnvironment');
```

<br/>
Caché
-----

<br/>
**Almacenando valores en caché**

<br/>
Para gestionar el acceso a memoria caché, *eMapper* utiliza un tipo de clases conocido como *cache providers*. Un *cache provider* realiza la inserción y recuperación de valores utilizando las librerías *APC*, *Memcache* y *Memcached*. Para poder hacer uso de las funciones de almacenamiento en caché es necesario primero definir el *provider* de la instancia mapper. Esto se realiza a través del método *setCacheProvider* . El siguiente ejemplo inicializa una instancia de *MySQLMapper* y luego le asocia un objeto de clase *APCProvider*.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Cache\APCProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//setear provider
$mapper->setCacheProvider(new APCProvider());
```
La inserción y recuperación de valores hacia y desde caché se realiza encadenando una llamada al método **cache**. Este método recibe una cadena de texto que identificará al valor almacenado. Opcionalmente, podemos también definir durante cuantos segundos se preservará el valor en la memoria. 

```php
//almacenar lista de usuarios en cache con clave 'USERS_ALL' por 5 minutos
$usuarios = $mapper
->cache('USERS_ALL', 300)
->query("SELECT * FROM usuarios");
```
Al utilizar un *cache provider* durante una consulta el comportamiento del objeto mapper se modifica de la siguiente manera:

 - Si un valor con la clave ingresada es encontrado entonces ese valor es devuelto por la consulta.
 - Si ningún valor con esa clave es encontrado entonces la consulta se realiza como de costumbre. En caso de que el valor devuelto sea distinto de NULL y no sea una lista vacia, el valor es almacenado en caché utilizando la configuración ingresada. Ese valor es luego devuelto.


<br/>
El siguiente ejemplo utiliza como *cache provider* una instancia de *MemcacheProvider*. Esta clase requiere setear la dirección y puerto del servidor durante su construcción. En el ejemplo también puede verse el uso de una clave de caché dinámica. Esta clave toma el primer argumento de la consulta para definir el identificador del valor a almacenar. El ejemplo producirá un valor con la clave *USER_6*.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Cache\MemcacheProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//setear provider
$mapper->setCacheProvider(new MemcacheProvider('65.132.12.4', 13412));

//obtener usuario y almacenar con memcache
$usuario = $mapper
->cache('USER_%{i}', 120)
->type('array')
->query("SELECT * FROM usuarios WHERE id_usuario = %{i}", 6);
```

<br/>
Tipos definidos por usuario
--------------------

<br/>
**eMapper** permite asociar el valor de una determinada columna a un manejador de tipo creado por el usuario. Para agregar un tipo definido por usuario es necesario implementar un manejador de tipo que extienda de la clase *eMapper\Type\TypeHandler*. Nuestro manejador deberá entonces implementar los métodos *setParameter* y *getValue* para insertar valores en una consulta y leerlos desde una columna. En los próximos ejemplos se ilustra la implementación de un manejador de tipos destinado a gestionar instancias de la clase *Acme\RGBColor*. Esta clase representa un color RGB con los componentes rojo, verde y azul. 

```php
namespace Acme\Type;

class RGBColor {
	public $red;
	public $green;
	public $blue;

	public function __construct($red, $green, $blue) {
		$this->red = $red;
		$this->green = $green;
		$this->blue = $blue;
	}
}
```

<br/>
**La clase RGBColorTypeHandler**

<br/>
Nuestro manejador de tipo estará encargado de convertir una instancia de *RGBColor* a su correspondiente representación en hexadecimal. Luego, tomar esa representación desde la base de datos y generar una instancia de *RGBColor* nuevamente. La implementación de nuestro manejador queda de la siguiente manera.

```php
namespace Acme\Type;

use eMapper\Type\TypeHandler;

class RGBColorTypeHandler extends TypeHandler {
	/**
	 * Convierte una instancia de RGBColor a su correspondiente representación en hexadecimal
	 */
	public function setParameter($parameter) {
	    //traducir componente rojo
	    if ($parameter->red < 16) {
	        $hexred = '0' . dechex($parameter->red);
	    }
	    else {
    	    $hexred = dechex($parameter->red % 256);
	    }
	    
        //traducir componente verde
	    if ($parameter->green < 16) {
	        $hexgreen = '0' . dechex($parameter->green);
	    }
	    else {
    	    $hexgreen = dechex($parameter->green % 256);
	    }
	    
        //traducir componente azul
	    if ($parameter->blue < 16) {
	        $hexblue = '0' . dechex($parameter->blue);
	    }
	    else {
    	    $hexblue = dechex($parameter->blue % 256);
	    }
	    
		return $hexred . $hexgreen . $hexblue;
	}

	/**
	 * Genera una instancia de RGBColor a partir de la representación en hexadecimal
	 */
	public function getValue($value) {
		return new RGBColor(hexdec(substr($value, 0, 2)),
		                    hexdec(substr($value, 2, 2)),
		                    hexdec(substr($value, 4, 2)));
	}
}
```

<br/>
**Declarar un tipo definido por usuario**

<br/>
Los tipos definidos por usuario se agregan a través del método *addType*. Este método espera el nombre completo de la clase que representa al tipo y una instancia del manejador asociado. Opcionalmente, podemos agregar un tercer parámetro con el alias del tipo agregado.

```php
use Acme\RGBColorTypeHandler;

$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
```
<br/>
El alias *color* puede ahora ser utilizado como identificador de tipo.

```php
use Acme\Type\RGBColor;

$color = new \stdClass;
$color->nombre = 'rojo';
$color->rgb = new RGBColor(255, 0, 0);

$mapper->query("INSERT INTO paleta (nombre, rgb) VALUES (#{nombre}, #{rgb:color})", $color);
```

<br/>
Tanto *Acme\RGBColor* como *color* pueden utilizarse como expresiones de mapeo al invocar a **type**.

```php
$rojo = $mapper->type('color')->query("SELECT rgb FROM paleta WHERE nombre = 'rojo'");
```

<br/>
**Inserción de valor en consulta**

<br/>
Por defecto, si el método *setParameter* de un manejador de tipo retorna una cadena entonces el valor será escapado e insertado entre comillas. En ocasiones este valor no debe pasar por este proceso sino insertarse directamente. Este es el caso de la clase *BlobTypeHandler*, que retorna una cadena 'TRUE' o 'FALSE' de acuerdo al valor pasado. Para estos casos existe la annotation de configuración **@unquoted**. Esta annotation determina que el valor retornado por *setParameter* no será incluido entre comillas.

```php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class BooleanTypeHandler extends TypeHandler {
	protected function cast_to_boolean($value) {
		if (is_string($value) && (strtolower($value) == 'f' || strtolower($value) == 'false')) {
			return false;
		}
	
		return (bool) $value;
	}
	
	public function getValue($value) {
		return $this->cast_to_boolean($value);
	}
	
	public function castParameter($parameter) {
		return $this->cast_to_boolean($parameter);
	}
	
	public function setParameter($parameter) {
		return ($parameter) ? 'TRUE' : 'FALSE';
	}
}
```
El método *castParameter* es un método interno que se invoca con el objetivo de chequear el tipo del parámetro previamente a ser insertado. Este método puede ser utilizado para notificar que determinado valor no es válido o directamente convertirlo a una expresión válida.


<br/>
Atributos dinámicos
----------

<br/>
Un atributo dinámico es una propiedad de un entidad o result map al cual se le asocia el valor retornado por una consulta. Estos atributos deben definirse con un grupo especial de annotations y pueden definir la expresión de mapeo asociada con **@type**.

**Definir un atributo dinámico**

<br/>
Para asociar una propiedad con la ejecución de una consulta utilizamos la annotation **@query**. Esta annotation espera una cadena de texto con la consulta propiamente dicha.
```php
namespace Acme\Entity;

/**
 * @entity
 */
class Usuario {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
     public $nombre;
    
    /**
     * @query "SELECT * FROM perfiles WHERE id_usuario = #{id} ORDER BY tipo"
     * @type obj[]
     */
    public $perfiles;
}
```
La consulta especificada recibe la instancia actual de *Usuario* y utiliza el valor de la propiedad *id* para realizar una búsqueda de los perfiles asociados. El resultado de esta consulta es mapeado luego a un arreglo de objetos.

<br/>
**Parametrización**

<br/>
Podemos especificar un número variable de argumentos para una determinada consulta a través de la annotation **@arg**. Supongamos ahora que los perfiles deben ser filtrados por tipo, de modo que este sea igual a 1. Para eso debemos agregar un argumento auxiliar a la consulta. Dado que aún necesitamos la instancia de *Usuario* debemos también agregar una annotation especial para decir que la instancia debe ser utilizada como argumento.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Usuario {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
     public $nombre;
    
    /**
     * @query "SELECT * FROM perfiles WHERE id_usuario = #{id} AND tipo = %{1}"
     * @arg-self
     * @arg 1
     * @type obj[]
     */
    public $perfiles;
}
```
La annotation **@arg-self** especifica que la instancia actual de *Usuario* sea utilizada como argumento. 
No es necesario agregar esta annotation en los casos donde no se especifican argumentos auxiliares. El segundo argumento es pasado como entero directamente, para de este modo realizar el filtrado de resultados.

<br/>
**Propiedades como parámetros**

<br/>
Lo más seguro es que la consulta para obtener los perfiles de un determinado usuario haya sido agregada como statement. La declaración de la misma sería algo similar a lo siguiente. 

```php
use eMapper\SQL\StatementNamespace;
use eMapper\SQL\Statement;

$profilesNamespace = new StatementNamespace('profiles');
$profilesNamespace->stmt('findByUserIdAndType',
                         "SELECT * FROM perfiles WHERE id_usuario = #{i} AND tipo = %{1}",
                         Statement::type('obj[id_perfil]'));
                         
$mapper->addNamespace($profilesNamespace);
```
Para invocar statements se utiliza la annotation **@stmt**. Esta annotation espera una cadena de texto con el nombre completo de la statement a ejecutar. En este caso, la statement a invocar no espera una instancia de *Usuario* como primer argumento sino un entero. Para enviar el valor de una propiedad como argumento hacemos uso de una expresión especial.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Usuario {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
     public $nombre;
    
    /**
     * @stmt 'profiles.findByUserIdAndType'
     * @arg #id
     * @arg 1
     */
    public $perfiles;
}
```
El envio de una propiedad como argumento se realiza agregando una annotation **@arg** con el valor de la propiedad a enviar encabezada por el símbolo **#**.

<br/>
**Rutinas almacenadas**

<br/>
La invocación a una rutina almacenada se realiza utilizando la annotation **@procedure**. A diferencia de **@query** y **@stmt**, las invocaciones a rutinas no utilizan la instancia actual como argumento.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Usuario {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
     public $nombre;
    
    /**
     * @procedure Profiles_FindByUserIdType
     * @arg #id:int
     * @arg 1
     * @type obj[]
     * @result-map Acme\Result\ProfileResultMap
     */
    public $perfiles;
}
```

La propiedad *perfiles* agrega una annotation extra para setear el result map a utilizar para esa consulta. **@result-map** espera una cadena de texto con el nombre completo de la clase a usar para el mapeo de datos. También hemos agregado un identificador de tipo al primer argumento de la consulta. En este caso la propiedad *id* ya esta asociada al tipo entero, pero puede llegar a resultar de utilidad como reemplazo del seteo de tipos a través de **sptypes**.

<br/>
**Macros**

<br/>
La annotation **@eval** permite asociar una propiedad al valor devuelto por una macro de usuario. Esta annotation define una expresion similar a las utilizadas en las consultas con SQL dinámico. Las macros de usuario no definen argumentos auxiliares y siempre son invocadas con la instancia actual como único argumento. Este ejemplo utiliza una macro de usuario para calcular la edad de un usuario basandose en la fecha de nacimiento.

```php
/**
 * @entity
 */
class Usuario {
    /**
     * @column id_usuario
     */
    public $id;
    
    /**
     * @type string
     */
     public $nombre;
    
    /**
     * @column fecha_nacimiento
     * @type dt
     */
    public $fechaNacimiento;
    
    /**
     * Edad de usuario
     * @eval (as-int (diff-format (#fechaNacimiento) (now) "%y"))
     */
    public $edad;
}
```

<br/>
**Condiciones**

<br/>
También es posible asociarle a un atributo una condición utilizando la annotation **@cond**. Si un atributo define una condición entonces solo se realizará la consulta (o evaluación de macro) si la condición resulta verdadera. Las condiciones se expresan utilizando macros de usuario. Esta entidad de ejemplo agrega una propiedad que determina si la categoria de un producto es igual a "software" previo a la ejecución de una statement.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Producto {
    /**
     * @column id_producto
     */
    public $id;
    
    /**
     * @type string
     */
     public $categoria;
    
    /**
     * @cond (== (#categoria) "software")
     * @stmt 'products.findSupportedOS'
     * @arg #id
     * @type obj[]
     */
    public $sistemasSoportados;
}
```

<br/>
Excepciones
----------

<br/>
Si bien *eMapper* hace uso de las excepciones ya incluidas en SPL, existen algunos escenarios donde se utilizan excepciones propias de la libreria. Estos escenarios son los siguientes:

 - Error en la consulta realizada
 - Error de conexión a servidor
 - Error durante el procesamiento de la consulta/resultado

<br/>
La clase asociada a cada uno de estos escenarios depende del servidor de base de datos que estemos utilizando aunque todas ellas tienen en común que extienden de *eMapper\Exception\MapperException*.

<br/>
**MySQL**

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Engine\MySQL\Exception\MySQLMapperException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;

$mapper = new MySQLMapper('db');

try {
    $mapper->query($query);
}
catch (MySQLQueryException $qe) {
    echo 'Error en consulta! ' . $qe->getMessage();
    echo '<br/>Al intentar correr ' . $qe->getQuery();
}
catch (MySQLConnectionException $ce) {
    echo 'Fallo de conexión! ' . $ce->getMessage();
}
catch (MySQLMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
**SQLite**

```php
use eMapper\Engine\SQLite\SQLiteMapper;
use eMapper\Engine\SQLite\Exception\SQLiteMapperException;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;

$mapper = new SQLiteMapper('filename.db');

try {
    $mapper->query($query);
}
catch (SQLiteQueryException $qe) {
    echo 'Error en consulta! ' . $qe->getMessage();
    echo '<br/>Al intentar correr ' . $qe->getQuery();
}
catch (SQLiteConnectionException $ce) {
    echo 'Fallo de conexión! ' . $ce->getMessage();
}
catch (SQLiteMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
**PostgreSQL**

```php
use eMapper\Engine\PostgreSQL\PostgreSQLMapper;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLMapperException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLQueryException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLConnectionException;

$mapper = new PostgreSQLMapper('dbname=test');

try {
    $mapper->query($query);
}
catch (PostgreSQLQueryException $qe) {
    echo 'Error en consulta! ' . $qe->getMessage();
    echo '<br/>Al intentar correr ' . $qe->getQuery();
}
catch (PostgreSQLConnectionException $ce) {
    echo 'Fallo de conexión! ' . $ce->getMessage();
}
catch (PostgreSQLMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
Apéndice I - Features adicionales
---------------------------

<br/>
**Obtener resultados sin procesar**

<br/>
A través del método **sql** podemos realizar consultas a la base de datos obteniendo el resultado sin procesamiento adicional. El tipo del resultado dependerá de la clase mapper que estemos utilizando

```php
//invocar método sql
$resultado = $mapper->sql("SELECT id_usuario, nombre FROM usuarios WHERE id_usuario = %{i}", 5);

//mysql
while (($row = $resultado->fetch_array()) != null) {
    //...
}

//liberar resultado
$mapper->free_result($resultado);
```

<br/>
**Overriding de consulta**

El método *query_override* permite reescribir la consulta que es enviada al servidor de base de datos de acuerdo a una determinada lógica. Este método recibe una función cuyo primer argumento es la consulta a realizar. Al retornar un valor podemos sobreescribir la consulta que será enviada finalmente.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$orden = 'nombre ASC';
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//ordenar usuarios
$usuarios = $mapper
->type('obj[]')
->query_override(function ($query) use ($orden) {
    //aplicar orden
    return $query . ' ORDER BY ' . $orden;
})
->query("SELECT * FROM usuarios");
```

<br/>
**Funciones de manejo de resultado**

A través del método *no_rows* podemos asociar la ejecución de una función auxiliar en los casos donde un resultado no devuelva ninguna fila. Esta función recibe como argumento el resultado obtenido.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//obtener usuarios
$usuarios = $mapper->type('obj[]')
->no_rows(function ($result) {
    throw new \UnexpectedValueException('No se encontraron usuarios :(');
})
->query("SELECT * FROM usuarios");
```

<br/>
**El método each**

<br/>
El método **each** nos permite aplicar una función de usuario a cada una de las filas devueltas por una consulta. La función definida recibirá 2 argumentos: el valor correspondiente a la fila actual y una instancia del objeto mapper. El ejemplo a continuación calcula de la edad de cada usuario devuelto por la consulta y la almacena en la propiedad *edad*.

```php
//traer usuarios
$usuarios = $mapper->each(function (&$usuario, $mapper) {
    $usuario->edad = (int) $usuario->fecha_nacimiento->diff(new \DateTime())->format('%y');
})->query("SELECT id_usuario, nombre, fecha_nacimiento FROM usuarios LIMIT 10");

```
<br/>
**Filtros**

<br/>
El método **filter** permite filtrar aquellas filas que no cumplan con determinada condición. El funcionamiento de los filtros es similar a aplicar una función de usuario a través de [array_filter](http://www.php.net/manual/en/function.array-filter.php "").


```php
//filtrar usuarios sin foto
$usuarios = $mapper->filter(function ($usuario) {
    return isset($usuario->imagen);
})->execute('users.findAll');
```
En caso de aplicarse a un solo elemento (que no vengo dentro de una lista) y la condición se evalue a falso entonces el valor devuelto será NULL.


<br/>
**Cadenas no escapadas**

El tipo *ustring* (con alias *ustr* y *us*) permite insertar cadenas de texto sin escapar dentro de la consulta. Cabe señalar que esta feature requiere especial cuidado dado que es posible sufrir ataques de *inyección SQL* si no se la usa adecuadamente.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//obtener usuarios ordenados por id
$usuarios = $mapper
->type('obj[]')
->query("SELECT * FROM usuarios ORDER BY %{ustring} %{ustring}", 'id_usuario', 'ASC');
```


<br/>
Apéndice II - Opciones de configuración
----------------------------------

<br/>
Los valores de configuración son datos que manejan el funcionamiento interno de un objeto mapper durante la ejecución de una consulta. Estos pueden ser modificados invocando métodos como **type** y **cache** o seteados de antemano con **set**. Las siguientes tablas muestran la lista completa de opciones soportadas por la libreria.

<br/>
**MySQL**

<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.name</td>
            <td>Cadena</td>
            <td>Nombre de la base de datos</td>
        </tr>
        <tr>
            <td>db.host</td>
            <td>Cadena</td>
            <td>Nombre/Dirección de host</td>
        </tr>
        <tr>
            <td>db.user</td>
            <td>Cadena</td>
            <td>Nombre de usuario</td>
        </tr>
        <tr>
            <td>db.password</td>
            <td>Cadena</td>
            <td>Contraseña de usuario</td>
        </tr>
        <tr>
            <td>db.port</td>
            <td>Cadena</td>
            <td>Puerto de conexión a base de datos</td>
        </tr>
        <tr>
            <td>db.socket</td>
            <td>Cadena</td>
            <td>Socket de base de datos</td>
        </tr>
        <tr>
            <td>db.charset</td>
            <td>Cadena</td>
            <td>Charset de base de datos (seteado utilizando mysqli::set_charset)</td>
        </tr>
        <tr>
            <td>db.autocommit</td>
            <td>Booleano</td>
            <td>Opción de auto commit de base de datos (seteado utilizando mysqli::autocommit)</td>
        </tr>
    </tbody>
</table>

<br/>
**SQLite**

<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.filename</td>
            <td>Cadena</td>
            <td>Ruta de archivo de base de datos</td>
        </tr>
        <tr>
            <td>db.flags</td>
            <td>Entero</td>
            <td>Flags de conexión (por defecto SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)</td>
        </tr>
            <td>db.encription_key</td>
            <td>Cadena</td>
            <td>Clave de encriptación</td>
        </tr>
    </tbody>
</table>


<br/>
**PostgreSQL**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.connection_string</td>
            <td>Cadena</td>
            <td>Cadena de coneción a base de datos</td>
        </tr>
        <tr>
            <td>db.connection_type</td>
            <td>Entero</td>
            <td>Tipo de conexión</td>
        </tr>
    </tbody>
</table>

<br/>
**Genericas**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.prefix</td>
            <td>Cadena</td>
            <td>Prefijo de base de datos</td>
            <td><em>Cadena vacia</em></td>
        </tr>
    <tbody>
</table>

<br/>
**Mapeo de datos**

<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>map.type</td>
            <td>Cadena</td>
            <td>Expresión de mapeo</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>map.params</td>
            <td>Array</td>
            <td>Parámetros auxiliares de mapeo (utilizados para mapeo a arreglos)</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>map.result</td>
            <td>Cadena</td>
            <td>Ruta completa a clase de configuración mapeo</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>map.parameter</td>
            <td>Cadena</td>
            <td>Ruta completa a clase de parametrización</td>
            <td><em>Ninguno</em></td>
        </tr>
    <tbody>
</table>

<br/>
**Caché**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache.key</td>
            <td>Cadena</td>
            <td>Identificador de valor en caché</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>cache.ttl</td>
            <td>Entero</td>
            <td>Tiempo de vida de valor en caché</td>
            <td><em>Ninguno</em> (se utiliza 0 en caso de no especificarse)</td>
        </tr>
    </tbody>
</table>

<br/>
**Callbacks**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>callback.each</td>
            <td>Callable</td>
            <td>Callback de iteración de elementos</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>callback.filter</td>
            <td>Callable</td>
            <td>Callback de filtrado de elementos</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>callback.no_rows</td>
            <td>Callable</td>
            <td>Callback de manejo de resultado vacío</td>
            <td><em>Ninguno</em></td>
        </tr>
         <tr>
            <td>callback.query</td>
            <td>Callable</td>
            <td>Callback de overriding de consulta</td>
            <td><em>Ninguno</em></td>
        </tr>
        <tr>
            <td>callback.index</td>
            <td>Callback</td>
            <td>Callback de indexado de elementos</td>
            <td><em>Ninguno</em></td>
         </tr>
         <tr>
            <td>callback.group</td>
            <td>Callable</td>
            <td>Callback de agrupado de elementos</td>
            <td><em>Ninguno</em></td>
        </tr>
    </tbody>
</table>

<br/>
**Rutinas almacenadas**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>procedure.use_prefix</td>
            <td>Booleano</td>
            <td>Determina si el prefijo de base de datos debe utilizarse en invocaciones a stored procedures</td>
            <td>TRUE</td>
        </tr>
        <tr>
            <td>procedure.types</td>
            <td>Arreglo</td>
            <td>Define el tipo asociado de cada argumento en una invocación a stored procedure</td>
            <td><em>Ninguno</em></td>
        </tr>
    </tbody>
</table>

<br/>
**Entornos de ejecución**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>environment.id</td>
            <td>Cadena</td>
            <td>Identificador de entorno de ejecución</td>
            <td>'default'</td>
        </tr>
        <tr>
            <td>environment.class</td>
            <td>Cadena</td>
            <td>Nombre completo de clase entorno</td>
            <td>'eMapper\Dynamic\Environment\DynamicSQLEnvironment'</td>
        </tr>
    </tbody>
</table>

<br/>
**Internas**
<table width="95%">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Valor por defecto</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>depth.limit</td>
            <td>Entero</td>
            <td>Límite de anidamiento de relación</td>
            <td>1</td>
        </tr>
        <tr>
            <td>depth.current</td>
            <td>Entero</td>
            <td>Nivel de anidamiento de instancia actual</td>
            <td>0</td>
        </tr>
    </tbody>
</table>

<br/>
Licencia
--------------
<br/>
Esta librería se distribuye bajo la licencia BSD 2-Clause.
