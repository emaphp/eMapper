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
2014-??-?? - Versión 3.0.0

  * Obsoleto: Models.
  * Agregado: Result maps (Un result map permite definir que columnas mapear desde una fila).
  * Agregado: Entities (Las Entities permiten mapear objetos a través de annotations).
  * Agregado: Soporte para bases de datos SQLite y PostgreSQL.
  * Agregado: SQL dinámico (Permite anidar expresiones-S dentro de una consulta través de la librería eMacros).
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
- [eMacros](https://github.com/emaphp/eMacros "")
 
<br/>
Instalación
--------------
<br/>
**Instalación a través de Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "3.*"
    }
}
```

<br/>
Introducción
------------

<br/>
***eMapper*** es una librería PHP que apunta a proveer una herramienta de mapeo de datos simple, poderosa y altamente customizable. Viene con algunas características interasantes no incluidas en otros frameworks:

- **Mapeo customizado**: Los resultados pueden mapearse a un tipo particular a través de una *expresión de mapeo*.
- **Indexado y Agrupamiento**: Los elementos dentro de una lista pueden ser indexados y/o agrupados por una valor de columna.
- **Tipos customizados**: Es posible definir tipos de datos de usuario y manejadores de tipo.
- **Caché**: Los datos mapeados pueden almacenarse en caché utilizando APC o Memcache.
- **SQL Dinámico**: Las consultas pueden contener expresiones escritas en el lenguaje eMacros.


<br/>
Primeros pasos
-----------

<br/>
**MySQL**

<br/>
Para comenzar crearemos una instancia de la clase *MySQLMapper*, la cual se encuetra declarada dentro del paquete *eMapper\Engine\MySQL*. Esta clase realiza conexiones a bases de datos MySQL y MariaDB. El constructor de la misma recibe el nombre de la base de datos, nombre de host y credenciales de usuario.
```php
//incluir autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

//instanciar clase
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
```

<br/>
**SQLite**

<br/>
**PostgreSQL**


<br/>
Arreglos
-------

<br/>
**Obtener una lista de filas como arreglo**

<br/>
Este primer ejemplo muestra como a través del método *query* enviamos una consulta al servidor de base de datos. El resultado obtenido es luego mapeado al tipo por defecto: una lista de arreglos. Cada arreglo dentro de la lista posee claves numéricas y asociativas.
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
Para indicar el tipo de dato a devolver por una consulta se utiliza el método *type*. Este método recibe una expresión de mapeo indicando el tipo de dato esperado. Para obtener una fila como un arreglo definimos el tipo de dato como *array* (o *arr*). eMapper se caracteriza para hacer un uso exhaustivo del encadenamiento de métodos (*method chaining*) para configurar la manera en que un resultado es mapeado. Cuando mapeamos a arreglo podemos también definir el tipo de arreglo como segundo parámetro.
```php
//obtener datos de usuario por id como arreglo asociativo
$usuario = $mapper
->type('array', MYSQLI_ASSOC)
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
<?php
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
$nombre = $mapper->type('str[]', 'nombre')->query("SELECT * FROM usuarios");
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
$usuarios = $mapper
->type('object[id_usuario]')
->query("SELECT * FROM usuarios");
```
<br/>
**Obtener un listado de arreglos indexados por una columna**

Esta sintaxis es también soportada al mapear a arreglos.

```php
//obtener un listado de arreglos asociativos indexados por id_usuario
$usuarios = $mapper
->type('array[id_usuario]', MYSQLI_ASSOC)
->query("SELECT * FROM usuarios");
```
Resulta útil recordar que solo es posible indexar por columnas presentes en el set de columnas devueltas por el resultado. Esto implica que al mapear a un arreglo con índices numéricos debemos expresar la columna como un entero.
```php
//obtener un listado de arreglos numéricos indexados por la primera columna
$usuarios = $mapper
->type('array[0]', MYSQLI_NUM)
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
//obtain products grouped by category and indexed by id
$products = $mapper
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
Al invocar a la función ***query*** podemos especificar un número arbitrario de argumentos. Cada uno de estos argumentos puede ser referenciado desde la consulta con una expresión encabezada por el caracter ***%*** seguido de un **especificador de tipo** entre llaves.

```php
//obtain user with id = 1
$user = $mapper->type('obj')->query("SELECT * FROM usuarios WHERE id_usuario = %{int}", 1);
```
Este ejemplo muestra como utilizar este tipo de expresiones para generar una consulta de inserción de datos.

```php
//valores a insertar
$nombre = 'jperez';
$password = sha1('qwerty321');
$admin = false;
$imagen = file_get_contents('foto.jpg');

//insertar datos ('s' => 'string', 'b' => 'boolean', 'x' => 'blob')
$mapper->query("INSERT INTO usuarios (nombre, password, admin, imagen) VALUES (%{s}, %{s}, %{b}, %{x})", $nombre, $password, $admin, $imagen);
```

<br/>
**Arreglos como parámetros**

<br/>
En caso de que el argumento sea una arreglo, los valores del mismo son convertidos al tipo especificado y luego concatenados. Esto resulta útil al realizar búsquedas con el operador **IN**.
```php
//ejecutar consulta: SELECT * FROM productos WHERE codigo IN ('MXP412', 'TRY235', 'OFR255')
$products = $mapper->query("SELECT * FROM productos WHERE codigo IN (%{s})", array('MXP412', 'TRY235','OFR255'));
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
->query("SELECT * FROM usuarios WHERE id_usuario = %{0[id]} OR nombre = %{0[1]:str} OR nombre = %{0[2]:str}", $args);
```

<br/>
**Rangos**

Un rango nos permite insertar un subconjunto de elementos de una lista. Para especificar un rango utilizamos 2 valores: el índice desde donde empezar a insertar y el largo total del subconjunto (de manera similar a la función [array_slice](http://www.php.net/manual/en/function.array-slice.php "")).

```php
$list = array(45, 23, '43', '164', 43);

//obtener una sublista con los elementos '43' y '164'
$users = $mapper
->type('obj[]')
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
$user = $mapper
->type('obj')
->query("SELECT * FROM usuarios WHERE nombre = %{0[3..6]}", $nombre);
```

<br/>
**Objetos y argumentos como argumento**

<br/>
Las consultas también soportan una sintaxis especial que les permite obtener valores desde arreglos y objetos especificando el nombre de propiedad/clave. Las expresiones de este tipo deben encabezarse con un símbolo ***#*** e ir seguidas de la propiedad (o clave) entre llaves. Esta sintaxis también permite especificar el tipo, subíndice y rango de la propiedad. Recordar que este tipo de expresiones requieren que el arreglo/objeto sea pasado como primer argumento.

```php
//datos de usuario
$usuario = new stdClass();
$usuario->nombre = 'jperez';
$usuario->password = sha1('jperez321');
$usuario->admin = false;
$usuario->imagen = file_get_contents('foto.jpg');

//insert data
$mapper->query("INSERT INTO usuarios (nombre, password, admin, imagen) VALUES (#{nombre}, #{password:s}, #{admin}, #{imagen:blob})", $user);
```

<br/>
Result maps
----------

<br/>
Entities
----------

<br/>
Statements
----------

<br/>
**Executing statements**

<br/>
A statement object represents a query which can be identified by a string ID. Statements are created by calling the *stmt* method and are invoked through the *execute* method.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//add statement
$mapper->stmt('findAllUsers', "SELECT * FROM users");

//execute statement
$users = $mapper->map('obj[]')->execute('findAllUsers');

$mapper->close();
?>
```
<br/>
It is possible to provide a default set of options by adding a third parameter. Statement options are generated through 2 static methods available in the *eMapper\Statement\Statement* class.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Statement\Statement;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//add statements
$mapper->stmt('findAllUsers', "SELECT * FROM users", Statement::config()->map('obj[user_id]'));
$mapper->stmt('findUserByPK', "SELECT * FROM users WHERE user_id = %{i}", Statement::map('obj'));

//execute statements
$users = $mapper->execute('findAllUsers');
$user = $mapper->execute('findUserByPK', 3);

$mapper->close();
?>
```
The main difference between these two methods is that *config* accepts an array containing a list of options as a parameter. Check the *Appendix II - Configuration options* for a complete list of available options.

<br/>
Namespaces
----------

<br/>
**Creating namespaces**

<br/>
A namespace is an object that contains statements. The main purpose of working with namespaces is allowing the programmer to manage statements trees separately. This can be helpful is mid-sized projects where a lot of queries are created and we need a way to organize them.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Statement\Statement;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//create 'users' namespace
$usersNamespace = $mapper->ns('users');

//method ns returns a reference to the new namespace
$usersNamespace
->stmt('findAll', "SELECT * FROM users", Statement::map('obj[user_id]'))
//calls to the stmt method can be chained
->stmt('findByPK', "SELECT * FROM users WHERE user_id = %{i}", Statement::map('obj'))
//this creates an inner namespace within 'users'
->ns('admin')
->stmt("delete", "DELETE FROM users WHERE user_id = %{i}");

//execute statements
$users = $mapper->execute('users.findAll');
$user = $mapper->execute('users.findByPK', 3);
$mapper->execute('users.admin.delete', 0);

$mapper->close();
?>
```

<br/>
**Custom namespaces**

<br/>
Another way to organize statements is creating custom namespaces classes to avoid keeping all of them on the same file. Custom namespaces must extend the *eMapper\Statement\StatementNamespace* class.

```php
<?php
namespace Acme;

use eMapper\Statement\StatementNamespace;
use eMapper\Statement\Statement;

class UsersNamespace extends StatementNamespace {
	public function __construct() {
		parent::__construct('users');
		
		$this->stmt('findByUsername',
		            "SELECT * FROM users WHERE user_name = %{s}");
		            
		$this->stmt('findByPK',
		            "SELECT * FROM users WHERE user_id = %{i}",
		            Statement::map('obj'));
		            
		$this->stmt('findAll',
		            "SELECT * FROM users",
		            Statement::config()->map('obj[user_id]'));
	}
}
?>
```
Namespaces are added through the *addNamespace* method.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\UsersNamespace;

//create mapper
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addNamespace(new UsersNamespace());

//get user
$user = $mapper->execute('users.findByPK', 4);

$mapper->close();
```

<br/>
Raw results
-----------

<br/>
**Obtaining results as resources**

<br/>
Using the ***sql*** method we can obtain ***mysqli_result*** objects directly from queries. This method ignores all configuration methods appended before it.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper->sql("SELECT user_id, username FROM users WHERE user_id = %{i}", 5);

while (($row = $result->fetch_array()) != null) {
    //...
}

//avoid 'synchronized' bug in older mysqli
$mapper->free_result($result);
$mapper->close();
?>
```

<br/>
Stored procedures
-----------------

<br/>
**Calling stored procedures**

<br/>
Stored procedures are routines available in MySQL that manage persistence logic. These routines can be invoked directly from a mapper instance thanks to a feature that automatically translates a non-declared method invocation to a query through *overloading* ([http://php.net/manual/en/language.oop5.overloading.php](http://php.net/manual/en/language.oop5.overloading.php "")). The generated query will contain the procedure name and all parameters provided.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//SQL: CALL FindUserByUsername('jdoe')
$user = $mapper->map('object')->FindUserByUsername('jdoe');

$mapper->close();
?>
```
We can specify parameter types through the ***ptypes*** method if necessary. Each one of the arguments corresponds to a parameter type. The code below calls a stored procedure specifying the argument types (username:string, password:string, is_admin:boolean). The result will then be mapped to an integer.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//SQL: CALL InsertNewUser('anndoe', 'pass123', TRUE)
$user_id = $mapper
->map('int')
->ptypes('s', 's', 'b')
->InsertNewUser('anndoe', 'pass123', 1);

$mapper->close();
?>
```
In most cases, stored procedures are declared using the database prefix. In order to avoid specifying the stored procedure prefix for every call, we can set the option *db.prefix* to store the current database prefix. Whenever we try to execute a stored procedure this prefix will be automatically appended in front of it.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set database prefix
$mapper->setPrefix('EMP_');
//OR $mapper->set('db.prefix', 'EMP_');

//SQL: CALL EMP_InsertImage('My Image', x...);
$image_id = $mapper
->map('integer')
->ptypes('string', 'blob')
->InsertImage('My Image', file_get_contents('image.png'));

$mapper->close();
?>
```


<br/>
Dynamic SQL
-----------

<br/>
**Calling a custom callback from within a query**

<br/>
It is possible to define custom callbacks in order to populate a SQL query with a custom string. These callbacks are defined by invoking the *dynamic* method with a callback id (as string) and a Closure object. Callbacks are called from within the query by inserting the callback id between double brackets. These callbacks receive one array containing all query arguments.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper
->map('obj')
->dynamic('condition', function ($args) {
    //put condition according with argument type
    if (is_int($args[0])) {
        return 'user_id = %{i}';
    }
    else {
        return 'user_name = %{s}';
    }
})->query("SELECT * FROM user_id WHERE [[condition]]", 1);

```
You can add an arbitrary number of callbacks. These are stored inside the mapper configuration array using their callback id with the prefix 'dynamic.' as key. You could also provide a default value in case a callback happens to be undefined. Default values are inserted right after the callback id and a double pipe (||). If the callback is defined then the default value is sent as a second parameter.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

//define order column
$order_column = 'user_name';

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper
->map('obj')
->dynamic('order', function ($args, $default) use ($order_column) {
    //return user_name ASC
    return isset($order_column) ? $order_column . ' ASC' : $default;
})
//order by id by default
->query("SELECT * FROM users ORDER BY [[order||user_id ASC]]");

```
<br/>
Cache
-----

<br/>
**Using cache providers**

<br/>
Cache providers provide a generic way to store and retrieve values using libraries like **APC**, **Memcache** and **Memcached**. The first step consist in defining the cache provider to use through the *setProvider* method. Then, we append an invocation to the ***cache*** method before running a query/statement/procedure. This method expects the *cache identification key* and its *expiration time* (TTL: time to live).
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Cache\APCProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setProvider(new APCProvider());

//store user list in apc cache for 5 minutes
$users = $mapper->cache('USERS_ALL', 300)->query("SELECT * FROM users");

$mapper->close();
?>
```
When using the cache extension the behaviour of the mapper object is modified in the following way:

 - If a value with the given key is found in cache then that value is returned.
 - If no value is found, ***query*** is executed as usual. If the query returns 1 or more rows then the result is mapped and then stored in cache. That value is then returned.

<br/>
This example uses Memcache instead of APC and also illustrates how to create a **dynamic cache key**. The parameters used to run the query are also used to build the cache key. As a result, the returned value will be stored using the key *USER_6*.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Cache\MemcacheProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setProvider(new MemcacheProvider('65.132.12.4', 13412));

//get user and store in cache
$user = $mapper
->cache('USER_%{i}', 120)
->map('array')
->query("SELECT * FROM users where user_id = %{i}", 6);

$mapper->close();
?>
```

<br/>
Configuration
--------------
<br/>
In order to accomplish its functionality, a mapper class generates copies of itself dynamically whenever certain methods are invoked. This means that when calling methods like ***cache***, ***map***, etc. the object clones itself and applies a new configuration. These configuration values are transient, which means that they don't apply to the main instance from they were created. For example, the ***map*** method sets the option *'map.type'* to the requested value type. Alternatively, we can declare transient values using the ***option*** method.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$four = $mapper->option('map.type', 'integer')->query("SELECT 2 + 2");
?>
```
<br/>
An mapper instance can also store customized configuration values of any type. To store a configuration value within an object we call the ***set*** method specifying both name and value.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('my_value', 'foo');
?>
```
Values defined with the ***set*** method are permanent. This means that all instances generated from that object will keep those values. Additionally, values generated with this method can be obtained by calling ***get*** with the configuration key name.

<br/>
Configuration values can also be used within queries. We do this by surrounding the configuration key between braces after a leading *'@'* character.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('my_table', 'users');
$user = $mapper->map('obj')->query("SELECT * FROM @{my_table} WHERE user_id = 2");
?>
```
If a given configuration value cannot be converted to string the expression is left blank.

<br/>
The 'each' method
-----------------

<br/>
The ***each*** method allow us to apply a user defined function to every row returned from a query. This method sets the configuration property 'callback.each', which must be assigned to a valid callback. The user function receives the row obtained and the mapper instance. This example illustrates how to use this method in order to dynamically generate 2 extra attributes on an obtained row.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//find user
$user = $mapper
->each(function (&$user, $mapper) {
    $user->age = (int) $user->birth_date->diff(new \DateTime())->format('%y');
    $user->profile = $mapper->execute('profiles.findByUserId', $user->user_id);
})
->execute('users.findByPK', 3);

$mapper->close();
?>
```
<br/>
Filters
-------

<br/>
The ***filter*** method sets a callback that determines which objects are removed from an obtained list. Using filters is pretty similar to apply a user-defined function to an array through the [array_filter](http://www.php.net/manual/en/function.array-filter.php "") function.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//find users according to a filter
$users = $mapper
->each(function (&$user, $mapper) {
    $user->age = (int) $user->birth_date->diff(new \DateTime())->format('%y');
    $user->profile = $mapper->execute('profiles.findByUserId', $user->user_id);
})
->filter(function ($user) {
    //filter users that are 60 years old (or older)
    return $user->age < 60;
})
->execute('users.findAll');

$mapper->close();
?>
```
If a filter is applied to a non-list and the filter evaluates to false then NULL is returned.

<br/>
Custom type handlers
--------------------

<br/>
Custom type handlers provide the logic to store and retrieve user defined types to/from a column. These classes must extend the *eMapper\Type\Typehandler* class and implement these functionalities:

 - Read a value from a column and obtain a valid class instance.
 - Generate a valid expression from an instance to put inside a query.

<br/>
The next examples will illustrate the implementation of a custom type class named *Acme\RGBColor*. Instances of this class will be stored in a column defined as CHAR(6). The column will hold the hexadecimal representation of a color, that is, the proper string which describes its red, green and blue components.

<br/>
**Custom type: the RGBColor class**

```php
<?php
namespace Acme;

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
**Type handler: the RGBColorTypeHandler class**

```php
<?php
namespace Acme;

use eMapper\Type\TypeHandler;
use Acme\RGBColor;

class RGBColorTypeHandler extends TypeHandler {
	/**
	 * Translates a RGBColor instance to a valid string expression
	 */
	public function setParameter($parameter) {
	    //get red component
	    if ($parameter->red < 16) {
	        $hexred = '0' . dechex($parameter->red);
	    }
	    else {
    	    $hexred = dechex($parameter->red % 256);
	    }
	    
        //get green component
	    if ($parameter->green < 16) {
	        $hexgreen = '0' . dechex($parameter->green);
	    }
	    else {
    	    $hexgreen = dechex($parameter->green % 256);
	    }
	    
        //get blue component
	    if ($parameter->blue < 16) {
	        $hexblue = '0' . dechex($parameter->blue);
	    }
	    else {
    	    $hexblue = dechex($parameter->blue % 256);
	    }
	    
		return $hexred . $hexgreen . $hexblue;
	}

	/**
	 * Generates a new RGBColor instance from a string
	 */
	public function getValue($value) {
		return new RGBColor(hexdec(substr($value, 0, 2)),
		                    hexdec(substr($value, 2, 2)),
		                    hexdec(substr($value, 4, 2)));
	}

	/**
	 * Tries to convert a value to a RGBColor instance
	 */
	public function castParameter($parameter) {
		if (!($parameter instanceof RGBColor)) {
			return null;
		}

		return $parameter;
	}
}
```
<br/>
Some things to notice about type handlers:

- The method *setParameter* receives a RGBColor instance and builds a string expression that is then inserted on a query string.
- The method *getValue* obtains a column content and is responsible of generating a new RGBColor instance from it.
- Additionally, we can convert an incoming query parameter to a RGBColor instance. The method *castParameter* is called whenever a query parameter uses *Acme\RGBColor* as a type specifier. The actual implementation chooses to avoid any parameter that is not a valid RGBColor instance and returning null as a replacement.

<br/>
**Declaring a custom type**

<br/>
Customs types are declared through the *addType* method. This methods expects the custom type full name, its type handler and an optional alias.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\RGBColorTypeHandler;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
?>
```

<br/>
By creating an alias we can now use 'color' as a type specifier. This is in fact not necessary as we already define a type handler for the class itself.

```php
<?php
$red = new \stdClass;
$red->name = 'red';
$red->rgb = new RGBColor(255, 0, 0);

$mapper->query("INSERT INTO palette (name, rgb) VALUES (#{name}, #{rgb:color})", $red);
?>
```

<br/>
The real advantage comes when used as a mapping expression.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\RGBColorTypeHandler;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');

$red = $mapper->map('color')->query("SELECT rgb FROM palette WHERE name = 'red'");
?>
```

<br/>
Exceptions
----------

<br/>
All library exceptions extend the ***MySQLMapperException*** class.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;

try {
    //ERROR: empty user
	$mapper = new MySQLMapper('my_db', 'localhost', '', 'my_pass');
	
	//run query
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT * FROM users");
	
	$mapper->close();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```
<br/>
If the connection failed due to a wrong configuration value, then a ***MySQLConnectionException*** is thrown.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;
use eMapper\Exception\MySQL\MySQLConnectionException;

try {
    //ERROR: fake_user does not exists!
	$mapper = new MySQLMapper('my_db', 'localhost', 'fake_user', 'my_pass');
	
	//run query
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT * FROM users");
	
	$mapper->close();
}
catch (MySQLConnectionException $ce) {
	echo "Connection error: " . $me->getMessage();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```

The ***MySQLQueryException*** is thrown only when a query syntax error is found.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;
use eMapper\Exception\MySQL\MySQLQueryException;

try {
	$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
	
	//ERROR: no columns
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT FROM users");
	
	$mapper->close();
}
catch (MySQLQueryException $qe) {
	echo "SQL query error: " . $qe->getMessage();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```

<br/>
Apéndice I - Features adicionales
---------------------------

<br/>
**Overriding de consulta**

El método *query_callback* permite reescribir la consulta que es enviada al servidor de base de datos de acuerdo a una determinada lógica. Este método recibe una función cuyo primer argumento es la consulta a realizar. Al retornar un valor podemos sobreescribir la consulta que será enviada finalmente.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$order = 'user_name ASC';
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//ordenar usuarios
$usuarios = $mapper->type('obj[]')
->query_callback(function ($query) {
    //aplicar orden
    return $query . ' ORDER BY ' . $order;
})
->query("SELECT * FROM usuarios");

```

<br/>
**Resultado vacío**

A través del método *no_rows* podemos asociar la ejecución de una función auxiliar en los casos donde un resultado no devuelva ninguna fila. Esta función recibe como argumento el resultado obtenido.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//obtener usuarios
$usuarios = $mapper->type('obj[]')
->no_rows(function ($result) {
    throw new \UnexpectedValueException('No users have been found :(');
})
->query("SELECT * FROM users");
```

<br/>
**Cadenas no escapadas**

El tipo *ustring* (con alias *ustr* y *us*) permite insertar cadenas de texto sin escapar dentro de la consulta. Cabe señalar que esta feature requiere especial cuidado dado que es posible sufrir ataques de *inyección SQL* si no se la usa adecuadamente.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users ordered by id
$users = $mapper
->type('obj[]')
->query("SELECT * FROM users ORDER BY %{ustring} %{ustring}", 'user_id', 'ASC');
```


<br/>
Appendix II - Valores de configuración
----------------------------------

<br/>
Configuration options are values that manage a mapper object behavior during a query/statement/procedure execution. They can be configured through class methods like *map*, *cache*, etc. or can be manipulated directly with ***set***. This is the list of all predefined keys available with their respective descriptions.

<br/>
**Database properties**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.name</td>
            <td>string</td>
            <td>Database name.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.host</td>
            <td>string</td>
            <td>Database host.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.user</td>
            <td>string</td>
            <td>Database user</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.password</td>
            <td>string</td>
            <td>Database user password.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.port</td>
            <td>string</td>
            <td>Database port.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>
            </td>
        </tr>
        <tr>
            <td>db.socket</td>
            <td>string</td>
            <td>Database socket.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.autocommit</td>
            <td>boolean</td>
            <td>Database autocommit feature.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>true</em>.
            </td>
        </tr>
        <tr>
            <td>db.prefix</td>
            <td>string</td>
            <td>Database prefix.</td>
            <td>
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Mapping**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>map.type</td>
            <td>string</td>
            <td>Mapping expression.</td>
            <td>
            Initialized with <em>map</em> method.
            <br/>
            Default value: <em>array[]</em>.
            </td>
        </tr>
        <tr>
            <td>map.model</td>
            <td>eMapper\Model\Model</td>
            <td>Mapping model to use.</td>
            <td>
            Initialized with <em>model</em> method .
            <br/>
            Evaluated during array/object result mapping.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>map.params</td>
            <td>array</td>
            <td>Mapping parameters.</td>
            <td>
            Initialized with <em>map</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Cache**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache.provider</td>
            <td>eMapper\Cache\CacheProvider</td>
            <td>Cache provider to use.</td>
            <td>
            Initialized with <em>setProvider</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>cache.key</td>
            <td>string</td>
            <td>Cache key to use.</td>
            <td>
            Initialized with <em>cache</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>cache.ttl</td>
            <td>integer</td>
            <td>Cache TTL (time to live).</td>
            <td>
            Numerical.
            <br/>
            Initialized with <em>cache</em> method.
            <br/>
            Default value: 0.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Callbacks**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
     <tr>
            <td>callback.each</td>
            <td>callable</td>
            <td>Defines a callback which receives all mapped elements from a query.</td>
            <td>
            Initialized with <em>each</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.filter</td>
            <td>callable</td>
            <td>Sets a callback which defines a filter to apply to all elements on a list.</td>
            <td>
            Similar to apply <em>array_filter</em> to a list of rows.
            <br/>
            Initialized with <em>filter</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.no_rows</td>
            <td>Closure</td>
            <td>Defines a callback which is called if the given query returns an empty result.</td>
            <td>
            Initialized with <em>no_rows</em> method.
            <br/>
            Can override return value.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
         <tr>
            <td>callback.query</td>
            <td>Closure</td>
            <td>Defines a callback which receives the generated query.</td>
            <td>
            Initialized with <em>query_callback</em> method.
            <br/>
            Can override query to run by returning a value.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.result</td>
            <td>Closure</td>
            <td>Defines a callback which receives the obtained result.</td>
            <td>
            Initialized with <em>result_callback</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
         </tr>
         <tr>
            <td>dynamic.*</td>
            <td>Closure</td>
            <td>Defines a Dynamic SQL callback.</td>
            <td>
            These callbacks are invoked from within the query by adding their callback id between double brackets.
            <br/>
            Default value: <em>none</em>
            </td>
        </tr>
    </tbody>
</table>

<br/>
**Stored procedure properties**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>procedure.use_prefix</td>
            <td>boolean</td>
            <td>Determines whether the procedure name uses the database prefix.</td>
            <td>
            Default value: <em>false</em>.
            </td>
        </tr>
        <tr>
            <td>procedure.types</td>
            <td>array</td>
            <td>Sets procedure parameter types.</td>
            <td>
            Initialized through the <em>ptypes</em> method.
        </tr>
    </tbody>
</table>
<br/>
Licencia
--------------
<br/>
Esta librería se distribuye bajo la licencia BSD 2-Clause.
