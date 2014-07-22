eMapper
==============

**The Extensible Data Mapper library for PHP**

<br/>
**Author**: Emmanuel Antico
<br/>
**Version**: 3.1.0

<br/>
Changelog
------------------
<br/>
2014-07-XX - Version 3.1.0 

  * Added: Entity Managers (ORM).
  * Modified: Class annotations. Now eMapper depends on emapper/annotations, a slightly modified version of minime/annotations. 
  * Modified: Dynamic SQL syntax delimiters.

<br/>
Dependencies
--------------
<br/>
- PHP >= 5.4
- [eMapper-annotations](https://github.com/emaphp/eMapper-annotations "") package
- [eMacros](https://github.com/emaphp/eMacros "") package
 
<br/>
Installation
--------------
<br/>
**Installation through Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "3.1"
    }
}
```

<br/>
About
------------

<br/>
*eMapper* is a PHP library aimed to provide a simple, powerful and highly customizable data mapping tool. It comes with some interesting features like:

- **Customized mapping**: Results can be mapped to a desired type through mapping expressions.
- **Indexation and Grouping**: Lists can be indexed or grouped together by a column value.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses writted in *eMacros*.
- **Entity Managers**: Managers provide a set of small ORM features which are common in similar frameworks.

<br/>
Introduction
-----------

<br/>
>Step 1: Pick an engine

eMapper supports SQLite, MySQL and PostgreSQL (for now). Creating a connection requires creating an instance of the corresponding driver.

```php
//MYSQL
use eMapper\Engine\MySQL\MySQLDriver;

$driver = new MySQLDriver('database', 'localhost', 'mysql', '123456');
//...

//SQLite
use eMapper\Engine\SQLite\SQLiteDriver;

$driver = new SQLiteDriver('database.sqlite');
//...

//PostgreSQL
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;

$driver = new PostgreSQLDriver('host=localhost port=5432 dbname=database user=postgres password=123456');
```

<br/>
>Step 2: The Data Mapper

Now that the connection driver is ready we create an intance of the *Mapper* class.
```php
use eMapper\Mapper;

$mapper = new Mapper($driver);
```

<br/>
>Step 3: Have fun

eMapper is not ORM-oriented but type-oriented. Everything revolves around queries an *mapping expressions*. The following examples try to give you an idea of how the data mapping engine works.

```php
//by default, results are mapped to an array of arrays
$users = $mapper->query("SELECT * FROM users");

foreach ($users as $user) {
    echo $user['name'];
    //...
}

//to indicate the desired type we overload the mapper instance by calling the type method
$username = $mapper->type('string')->query("SELECT name FROM users WHERE id = 100");

//queries can receive a variable number of arguments
$users = $mapper->query("SELECT * FROM users WHERE surname = %{s} AND id > %{i}", "Doe", 1000);

//'arr' (or 'array') returns a row as an associative array
$user = $mapper->type('arr')->query("SELECT * FROM users WHERE name = %{s}", 'emaphp');

//using 'obj' as mapping expression will convert a row to an instance of stdClass
$user = $mapper->type('obj')->query("SELECT * FROM users WHERE id = %{i}", 42);
```

<br/>
Mapping 101
-----------

<br/>
#####Simple types

```php
//mapping to an integer ('i', 'int', 'integer')
$total = $mapper->type('i')->query("SELECT COUNT(*) FROM users WHERE sex = %{s}", 'F');

//mapping to a boolean ('b', 'bool', 'boolean')
$suscribed = $mapper->type('b')->query("SELECT is_suscribed FROM users WHERE id = %{i}", 99);

//mapping to a float ('f', 'float', 'real', 'double')
$price = $mapper->type('f')->query("SELECT MAX(price) FROM products WHERE refurbished = {b}", false);

//mapping to a string ('s', 'str', 'string')
$body = $mapper->type('s')->query("SELECT message FROM messages WHERE slug = %{s}", 'emapper_rocks');

//dates ('DateTime', 'dt', 'timestamp', 'datetime'
$lastLogin = $mapper->type('dt')->query("SELECT last_login FROM users WHERE id = %{i}", 1984);

//finally, you can tell the exact column to fetch by providing a second argument
$code = $mapper->type('s', 'serial')->query("SELECT * FROM products WHERE id = %{i}", 101);
```

<br/>
#####Arrays

```php
//by default, an array contains both numeric and associative indexes
$movie = $mapper->type('array')->query("SELECT * FROM movies WHERE id = %{i}", 55);

//but you can change this by adding a second argument
use eMapper\Result\ArrayType;

//numeric only
$movie = $mapper->type('array', ArrayType::NUM)->query("SELECT * FROM movies WHERE id = %{i}", 56);

//associative only
$movie = $mapper->type('array', ArrayType::ASSOC)->query("SELECT * FROM movies WHERE id = %{i}", 57);
```

<br/>
#####Objects

```php
//mapping to a stdClass instance ('obj', 'object')
$book = $mapper->type('obj')->query("SELECT * FROM books WHERE isbn = %{s}", "9789507315428");

//setting to a custom class
$book = $mapper->type('obj:Acme\\Library\\Book')->query("SELECT * FROM books WHERE isbn = %{s}", "9788437604183");
```

*Note*: One important thing to remember when mapping to a structure is that values contained in columns declared using the DATE or DATETIME types are converted to instances of [DateTime](http://ar2.php.net/manual/en/class.datetime.php "").

<br/>
Lists
-----------

<br/>
Indexation and Grouping
-----------------------

<br/>
Queries
-------

<br/>
Statements
----------

<br/>
Entity Managers
---------------

<br/>
Stored procedures
-----------------

<br/>
Cache
-----

<br/>
Dynamic SQL
-----------

<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
