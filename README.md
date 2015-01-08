eMapper
==============

**The Extensible Data Mapper library for PHP**

<br/>
**Author**: Emmanuel Antico
<br/>
**Version**: 4.0

<br/>
Changelog
------------------
<br/>
2015-01-09 - Version 4.0.1

  * Extended documentation.

<br/>
Dependencies
--------------
<br/>
- PHP >= 5.4
- [Omocha](https://github.com/emaphp/omocha "") package
- [eMacros](https://github.com/emaphp/eMacros "") package
- [SimpleCache](https://github.com/emaphp/simplecache "") package
 
<br/>
Installation
--------------
<br/>
**Installation through Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "4.0.*"
    }
}
```

<br/>
About
------------

<br/>
*eMapper* is a PHP library aimed to provide a simple, powerful and highly customizable data mapping tool. It comes with some interesting features like:

- **Customized mapping**: Results can be mapped to a desired type through mapping expressions.
- **Indexation and Grouping**: Lists can be indexed or grouped together by a column/attribute name.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses.
- **Entity Managers**: Managers provide a set of ORM features which are common in similar frameworks.
- **Fluent queries**: Fluent queries can generate SQL programatically using a fluent interface.

<br/>
Introduction
-----------

<br/>
>Step 1: Pick an engine

eMapper supports SQLite, PostgreSQL and MySQL (or MariaDB if you prefer). Creating a connection requires creating an instance of the corresponding driver.

```php
//MYSQL
use eMapper\Engine\MySQL\MySQLDriver;

$driver = new MySQLDriver('database', 'localhost', 'user', 'passw');
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
>Step 2: Initialize mapper instance

Now that the connection driver is ready we create an instance of the *Mapper* class.
```php
use eMapper\Mapper;

$mapper = new Mapper($driver);
```

<br/>
>Step 3: Fetching data

eMapper is a type-oriented framework. Everything revolves around queries and *mapping expressions*. The following examples try to give you an idea of how the data mapping engine works.

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

//adding '[]' at the end converts a result to a list of the desired type
$users = $mapper->type('obj[]')->query("SELECT * FROM users WHERE department = %{s}", 'Sales');

//close connection
$mapper->close();
```

<br/>
Mapping 101
-----------

<br/>
#####Simple types

```php
//mapping to an integer ('i', 'int', 'integer')
$total = $mapper->type('i')->query("SELECT COUNT(*) FROM users WHERE gender = %{s}", 'F');

//mapping to a boolean ('b', 'bool', 'boolean')
$subscribed = $mapper->type('b')->query("SELECT is_subscribed FROM users WHERE id = %{i}", 99);

//mapping to a float ('f', 'float', 'real', 'double')
$price = $mapper->type('f')->query("SELECT MAX(price) FROM products WHERE refurbished = %{b}", false);

//mapping to a string ('s', 'str', 'string')
$body = $mapper->type('s')->query("SELECT message FROM posts WHERE slug = %{s}", 'sports');

//dates ('DateTime', 'dt', 'timestamp', 'datetime')
$lastLogin = $mapper->type('dt')->query("SELECT last_login FROM users WHERE id = %{i}", 1984);

//finally, you can tell the exact column to fetch by providing a second argument
$code = $mapper->type('s', 'product_code')->query("SELECT * FROM products WHERE id = %{i}", 101);
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
$book = $mapper
->type('obj')
->query("SELECT * FROM books WHERE isbn = %{s}", "9789507315428");

//using a custom class
namespace Acme\Library;

class Book {
    public $id;
    public $title;
    public $author;
    public $ISBN;
    public $genre;    
}

//...

$book = $mapper
->type('obj:Acme\Library\Book')
->query("SELECT * FROM books WHERE isbn = %{s}", "978-987-566-647-4");
```

*Note*: One important thing to remember when mapping to a structure is that values contained in columns declared using the DATE or DATETIME types are converted to instances of [DateTime](http://ar2.php.net/manual/en/class.datetime.php "").

<br/>
Lists
-----------

<br/>
#####Simple types

```php
//we obtain lists by adding brackets at the end of the expression
$id_list = $mapper->type('int[]')->query("SELECT id FROM users");

$prices = $mapper->type('float[]')->query("SELECT price FROM products");

$isbn_list = $mapper->type('string[]')->query("SELECT isbn FROM books");

$dates = $mapper->type('dt[]')->query("SELECT created_at FROM posts");

//again, a second argument could be provided to define which column to fetch
$refurbished = $mapper->type('bool[]', 'refurbished')->query("SELECT * FROM products");
```

<br/>
#####Arrays and Objects

```php
//same rule applies to arrays and objects
$users = $mapper->type('obj[]')->query("SELECT * FROM users");

//an array type could also be provided when mapping to lists
use eMapper\Result\ArrayType;

$users = $mapper->type('arr[]', ArrayType::ASSOC)->query("SELECT * FROM users");
```

<br/>
Indexes and Groups
-----------------------

<br/>
#####Indexes
```php
//the index column goes between brackets
$books = $mapper->type('array[id]')->query("SELECT * FROM books");

//a default type could be added right next to it
$products = $mapper->type('arr[id:string]')->query("SELECT * FROM products");

//make sure the column is present in the result (this won't work)
$books = $mapper->type('obj[isbn]')->query("SELECT id, name, price FROM books");

//when mapping to arrays, the index should be represented appropriately
use eMapper\Result\ArrayType;

//use first column as index
$products = $mapper->type('arr[0]', ArrayType::NUM)->query("SELECT * FROM products");
```

<br/>
#####Groups
```php
//group by a known column
$products = $mapper->type('arr<category>')->query("SELECT * FROM products");

//a type could also be specified
$books = $mapper->type('obj<publisher:string>')->query("SELECT * FROM books");

//both expressions can be mixed to obtain interesting results
$products = $mapper->type('obj<category>[id:int]')->query("SELECT * FROM products");
```

<br/>
#####Callbacks
```php
//lists could also be indexed using a closure
$products = $mapper->type('array[]')
->indexCallback(function ($product) {
    //return a custom made index
    return $product['code'] . '_' . $product['id'];
})
->query("SELECT * FROM products");

//a group callback does what you expect
//it can also be combined with indexes if needed
$products = $mapper->type('obj[id]')
->groupCallback(function ($product) {
    return substr($product->category, 0, 3);
})
->query("SELECT * FROM products");
```

<br/>
Queries
-------


<br/>
#####Query arguments

```php
//arguments the easy way (type identifier between braces)
$products = $mapper
->type('obj[]')
->query("SELECT * FROM products WHERE price < %{f} AND category = %{s}", 699.99, 'Laptops');

//argument by position (and optional type)
$products = $mapper
->type('obj[]')
->query("SELECT * FROM products WHERE category = %{1} AND price < %{0:f}", 699.99, 'Laptops');
```

<br/>
#####Arrays/Objects as argument

```php
//array as parameter
$parameter = [
    'password'    => sha1('qwerty'),
    'modified_at' => new \DateTime
];

$mapper->query(
    "UPDATE users SET password = #{password}, modified_at = #{modified_at:dt} WHERE name = %{1:string}",
    $parameter,
    'emaphp'
);

//syntax works with objects as well
use Acme\CMS\Comment;

$comment = new Comment();
$comment->userId = 100;
$comment->body = "Hello World";

$mapper->query("INSERT INTO comments (user_id, body) VALUES (#{userId}, #{body})", $comment);
```
*Note*: The syntax for array/object attributes work as long as you provide the array/object as the first argument.

<br/>
Fluent Queries
-------------

<br/>
Fluent queries provide a fluent interface for generating SQL programatically. We obtain a new FluentQuery instance by calling the *newQuery* method in the *Mapper* class.

<br/>
#####Select

```php
/**
 * BASIC USAGE
 */

$query = $mapper->newQuery();

//SELECT * FROM users
$users = $query->from('users')->fetch();

//fetch accepts a mapping expression as an optional argument
$user = $query->from('users')->where('id = 1')->fetch('array');

//type method could be used instead
$user = $query->from('users')->where('id = 1')->type('array')->fetch();

//condition could be expressed using the Column class
use eMapper\Query\Column;

$user = $query->from('users')->where(Column::id()->eq(1))->fetch('obj');

//using arguments
$user = $query->from('users')->where('id = %{i}', 1)->fetch('obj');

/**
 * COLUMNS
 */

$query = $mapper->newQuery();

//define columns to fetch
$users = $query->from('users')->select('id', 'name', 'email')->fetch('obj[]');

//using Column class
$users = $query->from('users')
->select(Column::id(), Column::name(), Column::email())
->fetch('obj[]');

/**
 * LIMIT + OFFSET
 */

$query = $mapper->newQuery();
$users = $query->from('users')->limit(10)->offset(5)->fetch();

/**
 * ORDER BY
 */

$query = $mapper->newQuery();

$users = $query->from('users')->orderBy('name', 'id ASC')->fetch();

//same but with Column class
$users = $mapper->from('users')
->orderBy(Column::name(), Column::id()->type('ASC'))
->fetch();

/**
 * JOINS
 */

$query = $mapper->newQuery();
 
$users = $query->from('users', 'u')
->innerJoin('profiles', 'p', 'u.id = p.user_id')
->select('u.id', 'u.name AS username', 'p.name')
->fetch();

//using the Column class along with the double dash syntax
$users = $query->from('users', 'u')
->innerJoin('profiles', 'p', Column::u__id()->eq(Column::p__user_id()))
->select(Column::u__id(), Column::u__name()->alias('username'), Column::p__name())
->fetch();

/**
 * GROUP BY + HAVING
 */

$query = $mapper->newQuery();

$employees = $query->from('Employees', 'emp')
->innerJoin('Orders', 'ord', 'ord.employee_id = emp.id')
->select('emp.lastname', 'COUNT(ord.id)')
->groupBy('emp.lastname')
->having('COUNT(ord.id) > %{i}', 10)
->fetch();

use eMapper\Query\Func as F;

$employees = $query->from('Employees', 'emp')
->innerJoin('Orders', 'ord', Column::ord__employee_id()->eq(Column::emp__id()))
->select(Column::emp__lastname(), F::COUNT(Column::ord__id()))
->groupBy(Column::emp__lastname())
->having(F::COUNT(Column::ord__id())->gt(10))
->fetch();

/**
 * FUNCTIONS
 */
 
$query = $this->mapper->newQuery();

//SELECT COUNT('*') FROM Products
$total = $query->from('Products')
->select(F::COUNT('*'))
->fetch('i');

//SELECT UCASE(CustomerName) AS Customer,City FROM Customers
$customers = $query->from('Customers')
->select(F::UCASE(Column::CustomerName())->alias('Customer'), Column::City())
->fetch('obj[]');

//SELECT MID(City,1,4) AS ShortCity FROM Customers
$cities = $query->from('Customers')
->select(F::MID(Column::City(), 1, 4)->alias('ShortCity'))
->fetch('str[]');

//SELECT * FROM Customers WHERE LEN(FirstName) < 10
$customers = $query->from('Customers')
->where(F::LEN(Column::FirstName())->lt(10))
->fetch('obj[]');

//SELECT ProductName,ROUND(Price,2) AS RoundedPrice FROM Products
$products = $query->from('Products')
->select(Column::ProductName(), F::ROUND(Column::Price(), 2)->alias('RoundedPrice'))
->fetch('obj[]');

//SELECT ProductName,Price,NOW() AS PerDate FROM Products
$products = $query->from('Products')
->select(Column::ProductName(), Column::Price(), F::NOW()->alias('PerDate'))
->fetch('obj[]');

//SELECT ProductName,Price,FORMAT(NOW(),"YYYY-MM-DD") AS PerDate FROM Products
$products = $query->from('Products')
->select(Column::ProductName(), Column::Price(), F::FORMAT(F::NOW(), '"YYYY-MM-DD"')->alias('PerDate'))
->fetch('obj[]');
```

<br/>
#####Insert

```php
$query = $mapper->newQuery();

//INSERT INTO users VALUES ('emaphp', 'M', '1984-07-05')
$id = $query->insertInto('users')->values('emaphp', 'M', '1984-07-05')->exec();

//values as array
$values = ['emaphp', 'M', '1984-07-05'];
$id = $query->insertInto('users')->valuesArray($values)->exec();

//INSERT INTO users (name, birth_date, sex) VALUES ('emaphp', '1984-07-05', 'M')
$id = $query->insertInto('users')
->columns('name', 'birth_date', 'sex')
->values('emaphp', '1984-07-05', 'M')
->exec();

//using valuesExpr
$id = $query->insertInto('users')
->columns('name', 'birth_date', 'sex')
->valuesExpr('%{s}, %{dt}, %{s}', 'emaphp', '1984-07-05', 'M')
->exec();
```

<br/>
#####Update

```php
use emapper\Query\Column;

$query = $mapper->newQuery();

//UPDATE users SET language = 'sp', last_login = 2015-01-01 00:00:00
//WHERE name = 'emaphp'
$query->update('users')
->set('language', 'sp')
->set('last_login', new \Datetime())
->where(Column::name()->eq('emaphp'))
->exec();

//using setExpr
$query->update('users')
->setExpr('language = %{s}, last_login = %{dt}', 'sp', new \Datetime())
->where('name = %{s}', 'emaphp')
->exec();

//using setValue
$value = [
    'language'   => 'sp',
    'last_login' => new \Datetime()
];

$query->update('users')
->setValue($value)
->where('name = %{s}', 'emaphp')
->exec();
```

<br/>
#####Delete

```php
use emapper\Query\Column;

$query = $mapper->newQuery();

//DELETE FROM staff WHERE role <> 'developer'
$query->deleteFrom('staff')
->where(Column::role()->eq('developer', false))
->exec();

//alternative syntax
$query->deleteFrom('staff')
->where('role <> %{s}', 'developer')
->exec();
```
<br/>
#####Filter methods

Use these methods along with the *Column* class to build conditional expressions. Adding *false* as a last argument produces a negated condition.
<table>
    <tr>
        <th>Name</th>
        <th>Method</th>
        <th>Arguments</th>
        <th>Example</th>
    </tr>
    <tr>
        <td>Equals</td>
        <td>eq</eq>
        <td>1</td>
        <td>Column::name()->eq('emaphp')</td>
    </tr>
    <tr>
        <td>Contains (case sensitive)</td>
        <td>contains</td>
        <td>1</td>
        <td>Column::description()->contains('PHP')</td>
    </tr>
    <tr>
        <td>Contains (case insensitive)</td>
        <td>icontains</td>
        <td>1</td>
        <td>Column::description()->icontains('PHP')</td>
    </tr>
    <tr>
        <td>In</td>
        <td>in</td>
        <td>1</td>
        <td>Column::id()->in([1, 2, 3])</td>
    </tr>
    <tr>
        <td>GreaterThan</td>
        <td>gt</td>
        <td>1</td>
        <td>Column::price()->gt(100)</td>
    </tr>
    <tr>
        <td>GreaterThanEqual</td>
        <td>gte</td>
        <td>1</td>
        <td>Column::price()->gte(100)</td>
    </tr>
    <tr>
        <td>LessThan</td>
        <td>lt</td>
        <td>1</td>
        <td>Column::price()->lt(100)</td>
    </tr>
    <tr>
        <td>LessThanEqual</td>
        <td>lte</td>
        <td>1</td>
        <td>Column::price()->lte(100)</td>
    </tr>
    <tr>
        <td>StartsWith (case sensitive)</td>
        <td>startswith</td>
        <td>1</td>
        <td>Column::name()->startswith('ema')</td>
    </tr>
    <tr>
        <td>StartsWith (case insensitive)</td>
        <td>istartswith</td>
        <td>1</td>
        <td>Column::name()->istartswith('ema')</td>
    </tr>
    <tr>
        <td>EndsWith (case sensitive)</td>
        <td>endsswith</td>
        <td>1</td>
        <td>Column::name()->endswith('php')</td>
    </tr>
    <tr>
        <td>EndsWith (case insensitive)</td>
        <td>iendswith</td>
        <td>1</td>
        <td>Column::name()->iendswith('php')</td>
    </tr>
    <tr>
        <td>Matches (case sensitive)</td>
        <td>matches</td>
        <td>1</td>
        <td>Column::title()->matches('^The')</td>
    </tr>
    <tr>
        <td>Matches (case insensitive)</td>
        <td>imatches</td>
        <td>1</td>
        <td>Column::title()->imatches('^The')</td>
    </tr>
    <tr>
        <td>IsNull</td>
        <td>isnull</td>
        <td><em>None</em></td>
        <td>Column::last_login()->isnull()</td>
    </tr>
    <tr>
        <td>Range</td>
        <td>range</td>
        <td>2</td>
        <td>Column::hits()->range(5,10)</td>
    </tr>
</table>

<br/>
Stored procedures
-----------------

<br/>
In order to call a stored procedure we create a *StoredProcedure* instance by calling the *newProcedure* method in the *Mapper* class. The procedure is then invoked through the *call* method.
<br/>
```php
//CALL Users_Clean
$proc = $mapper->newProcedure('Users_Clean');
$proc->call();

//using the database prefix
$mapper->setPrefix('ACME_');

//CALL ACME_Backup_Orders(100)
$proc = $mapper->newProcedure('Backup_Orders');
$proc->call(100);

//don't append prefix
$proc = $mapper->newProcedure('Get_Order');
$proc->usePrefix(false)->call(1);

//if a procedure returns a value it can also be mapped
$proc = $mapper->newProcedure('Users_Create');
$id = $proc->type('i')->call('emaphp', '1984-07-05', 'M');

//specifyng argument types
$proc = $mapper->newProcedure('Product_Save');
$proc->types();

//wrap procedure name
$proc = $mapper->newProcedure('User_Find');
//CALL `ACME_User_Find`(199)
$user = $proc->call(199);

//PostgreSQL: return set
$proc = $mapper->newProcedure('Profile_GetByEmail');
//SELECT * FROM Profile_GetByEmail
$profile = $proc->returnSet(true)->call('emaphp@github.com')
```


<br/>
Entity Managers
---------------

<br/>
#####Entities and Annotations

Entity managers are objects that behave like DAOs ([Data Access Object](http://en.wikipedia.org/wiki/Data_access_object "")) for a specified class. The first step to create an entity manager is designing an entity class. The following example shows an entity class named *Product*. The *Product* class obtains its values from the *pŕoducts* table, indicated by the *@Entity* annotation. This class defines 5 attributes, and each one defines its type through the *@Type* annotation. If the attribute name differs from the column name we can specify a *@Column* annotation indicating the correct one. As a general rule, all entities must define a primary key attribute. The *Product* class sets its **id** attribute as primary key using the *@Id* annotation.

```php
namespace Acme\Factory;

/**
 * @Entity products
 */
class Product {
    /**
     * @Id
     * @Type integer
     */
    public $id;
    
    /**
     * @Type string
     * @Column pcode
     */
    public $code;

    /**
     * @Type string
     */
    public $description;

    /**
     * @Type string
     */
    public $category;
    
    /**
     * @Type float
     */
    public $price;
}
```
Managers are created through the *newManager* method in the *Mapper* class. This method expects the entity class full name. Managers are capable of getting results using filters without having to write SQL manually. Notice that conditional expressions are now created using the *Attr* class instead of *Column*.

```php
//create a products manager
$products = $mapper->newManager('Acme\Factory\Product');

//get by id
$product = $products->findByPk(4);

//obtain a product by code
use eMapper\Query\Attr;

$product = $products->get(Attr::code()->eq('XXX098'));

//get a list of products by a given category
$list = $products->find(Attr::category()->eq('Laptops'));

//define a filter (category = 'E-Books' AND price < 799.99)
$list = $products
->filter(Attr::category()->eq('E-Books'), Attr::price()->lt(799.99))
->find();

//OR condition (category = 'E-Books' OR price < 799.99)
$list = $products
->orfilter(Attr::category()->eq('E-Books'), Attr::price()->lt(799.99))
->find();

//exclude (category <> 'Fitness')
$list = $products
->exclude(Attr::category()->eq('Fitness'))
->find();

//reverse condition (description NOT LIKE '%Apple%')
$list = $products
->filter(Attr::description()->contains('Apple', false))
->find();
```

<br/>
#####Manager utilities

```php
use eMapper\Query\Attr;

//getting all products indexed by id
$list = $products->index(Attr::id())->find();

//or grouped by category
$list = $products->group(Attr::category())->find();

//callbacks work as well
$list = $products
->indexCallback(function ($product) {
    return $product->getId() . substr($product->getCode(), 0, 5);
})
->find();

//set the amount of objects to obtain
$list = $products
->orderBy(Attr::id())
->limit(15)
->find();

//setting the order type
$list = $products
->orderBy(Attr::id(), Attr::category()->type('ASC'))
->limit(10, 20)
->find();

//count products of a given category
$total = $products
->filter(Attr::category()->eq('Audio'))
->count();

//average price
$avg = $products
->exclude(Attr::category()->eq('Laptops'))
->avg(Attr::price());

//max price (return as integer)
$max = $products
->filter(Attr::code()->startswith('SONY'))
->type('int')
->max(Attr::price());
```

<br/>
#####Storing objects

```php
use Acme\Factory\Product;

//create manager
$products = $mapper->newManager('Acme\Factory\Product');

$product = new Product;
$product->code = 'ACM001';
$product->description = 'Test product';
$product->category = 'Explosives';
$product->price = 149.90;

//save object
$products->save($product);

//if the entity already has an id, 'save' produces an UPDATE query
$product = $products->get(Attr::code()->eq('ACM001'));
$product->price = 129.90;
$products->save($product);
```


<br/>
#####Deleting objects

```php
//create manager
$products = $mapper->newManager('Acme\Factory\Product');

//delete expects an entity as parameter
$product = $products->findByPk(20);
$products->delete($product);

//reduced version
$products->deleteWhere(Attr::id()->eq(20));

//for security reasions, truncating a table requires a special method
$products->truncate();
```

<br/>
Associations
-----------

<br/>
#####Introduction
Associations provide an easy way to fetch data related to an entity. As expected, they also need to be declared using a special set of annotations. Their types include *one-to-one*, *one-to-many*, *many-to-one* and *many-to-many*.

<br/>
#####One-To-One

The next example shows 2 entities (*Profile* and *User*) and how to declare a One-To-One association between them to obtain a *User* entity from a fetched profile.

```php
namespace Acme;

/**
 * @Entity profiles
 */
class Profile {
    /**
     * @Id
     * @Type integer
     */
    public $id;
    
    /**
     * @Column user_id
     */
    public $userId;
    
    /**
     * @Type string
     */
    public $firstname;
    
    /**
     * @Type string
     */
    public $surname;
    
    /*
     * ASSOCIATIONS
     */

    /**
     * @OneToOne User
     * @Attr(userId)
     */
    public $user;
}
```
In this example, the *Profile* class declares a **user** property which defines a one-to-one association with the *User* class. The *@Attr* annotation specifies which property is used to perform the required join. When an attribute is declared on the current class then it must be expressed between parentheses right after the annotation.

```php
namespace Acme;

/**
 * @Entity users
 */
class User {
    /**
     * @Id
     * @Type integer
     */
    public $id;
    
    /**
     * @Column username
     */
    public $name;
    
    /**
     * @Type string
     */
    public $email;
    
    /*
     * ASSOCIATIONS
     */

    /**
     * @OneToOne Profile
     * @Attr userId
     * @Lazy
     */
    public $profile;
}
```
The *User* class defines the *profile* property as a one-to-one association with the *Profile* class. Notice that this time the *@Attr* annotation defines the property name without parentheses, meaning that the required attribute is not declared in the current class. Also, this association is declared as **lazy**, which means that is not evaluated right away. The following example shows how to obtain a *Profile* instance and its associated user.

```php
//obtain the profile with ID = 100
$manager = $mapper->newManager('Acme\Profile');
$profile = $manager->findByPk(100); // returns a Profile instance
$user = $profile->getUser(); // returns the associated User instance
```

Lazy associations returns an instance of *eMapper\ORM\AssociationManager*. This means that invoking the *getProfile* method will return a manager instance, not an entity. In order to get the referred value we append a call to the *fetch* method.

```php
//obtain the user with ID = 100
$manager = $mapper->newManager('Acme\User');
$user = $manager->findByPk(100);
$profile = $user->getProfile(); // returns an AssociationManager instance
$profile = $user->getProfile()->fetch(); // fetch() returns the desired result
```
Associations also provide a mechanism for querying for related attributes. Suppose we want to obtain a profile by its user name. We can do this by using a special syntax that specifies the association property and the comparison attribute separated by a doble underscore.

```php
use eMapper\Query\Attr;

//build manager
$manager = $mapper->newManager('Acme\Profile');

//users.name = 'jdoe'
$profile = $manager->get(Attr::user__name()->eq('jdoe'));
```

<br/>
#####One-To-Many and Many-To-One

Suppose we need to design a pet shop database to store data from a list of clients and their respective pets. The first step after creating the database will be implementing the *Client* and *Pet* entity classes. The *Client* class has a one-to-many association with the *Pet* class provided through the **pets** property. The required attribute (**clientId**) is specified as a value of the *@Attr* annotation. This annotation references the attribute in the *Pet* class that stores the client identifier.
```php
namespace Acme;

/**
 * @Entity clients
 */
class Client {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Type string
     */
    public $surname;

    /*
     * ASSOCIATIONS
     */

    /**
     * @OneToMany Pet
     * @Attr clientId
     */   
    public $pets;
}
```
From the point of view of the *Pet* class this is a many-to-one association. The **owner** association is resolved through the **clientId** attribute , meaning that in this case it has to be specified between parentheses.

```php
namespace Acme;

/**
 * @Entity pets
 */
class Pet {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Column client_id
     */
    public $clientId;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Type string
     */
    public $type;
    
    /**
     * @Column birth_date
     * @Type string
     */
    public $birthDate;
    
    /*
     * ASSOCIATIONS
     */

    /**
     * @ManyToOne Client
     * @Attr(clientId)
     */
    public $owner;
}
```
This small example obtains all clients that have dogs.
```php
use emapper\Query\Attr;

$manager = $mapper->newManager('Acme\Client');

//get all clients that have dogs
$clients = $manager->find(Attr::pets__type()->eq('Dog'));
```
And this one obtains all pets for a given client.
```php
use emapper\Query\Attr;

$manager = $mapper->newManager('Acme\Pet');

//get all pets of Joe
$pets = $manager->find(Attr::owner__name()->eq('Joe'));
```


<br/>
#####Many-To-Many

Many-To-Many associations are kind of special as they need to provide the name of the join table that resolves the association. Suppose that we want to add a **favorites** association from the *User* class to the *Product* class.

```php
namespace Acme;

/**
 * @Entity users
 */
class User {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Column username
     */
    public $name;
    
    /**
     * @Type string
     */
    public $email;
    
    /*
     * ASSOCIATIONS
     */
     
    /**
     * @ManyToMany Product
     * @Join(user_id,prod_id) favorites
     * @Lazy
     */
    public $favorites;
}
```
The *@Join* annotation must indicate which columns are used to perform the join along with the table name. Columns must be specified in the right order; first being the one that references the current entity. The following code shows an example using this association.

```php
use emapper\Query\Attr;

$manager = $mapper->newManager('Acme\User');

//get all users that like Android
$users = $manager->find(Attr::favorites__description()->contains('Android'));
```

<br/>
#####Recursive associations

There are some scenarios in which an entity is associated to itself in more than one way. In this example we'll introduce the *Category* entity class to explore in detail these type of associations.

```php
namespace Acme;

/**
 * @Entity categories
 */
class Category {
    /**
     * @Id
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Column parent_id
     * @Type integer
     */
    public $parentId;
    
    /*
     * ASSOCIATIONS
     */

    /**
     * @ManyToOne Category
     * @Attr(parentId)
     */
    public $parent;
    
    /**
     * @OneToMany Category
     * @Attr parentId
     */
    public $subcategories;
}
```

The *Category* class its related to itself through the **parent** and **subcategories** associations. Both of them need to specify the *parentId* attribute as the join attribute. Obtaining all subcategories
of a given category can be resolved in the following way.

```php
use emapper\Query\Attr;

$manager = $mapper->newManager('Acme\Category');

//get all subcategories of 'Technology'
$categories = $mapper->find(Attr::parent__name()->eq('Technology'));
```


<br/>
#####SQLite

Most RDBMS provide a way to keep reference integrity through some event/trigger facility. This is not the case for SQLite. In order to keep reference integrity 2 special annotations are provided: *@Cascade* and *@Nullable*. This example illustrates the relationship between the *User* and *Profile* class.

```php
namespace Acme;

/**
 * @Entity profiles
 */
class Profile {
    /**
     * @Id
     * @Type int
     * @Column profile_id
     */
    public $id;
    
    /**
     * @Type int
     * @Column user_id
     */
    public $userId;
    
    /**
     * @Type string
     */
    public $firstname;
    
    /**
     * @Type string
     */
    public $lastname;
}
```

When defining the *User* entity we'll append a *@Cascade* annotation to its *profile* attribute.

```php
namespace Acme;

/**
 * @Entity users
 */
class User {
    /**
     * @Id
     * @Type int
     * @Column user_id
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /*
     * @Type string
     * @Unique
     */
    public $email;
    
    /**
     * @OneToOne Profile
     * @Attr userId
     * @Cascade
     */
    public $profile;
}
```
By using the *@Cascade* annotation we instruct the manager to delete the related profile once a user is removed from database.

```php
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;

$mapper = new Mapper(new SQLiteDriver('data.db'));

//delete user, profile is removed as well
$manager = $mapper->newManager('Acme\User');
$user = $manager->findByPk(1);
$manager->delete($user);

$mapper->close();
```

But what if we don't want to remove a profile? In that case the *userId* attribute in the *Profile* class must include the *@Nullable* annotation. The manager checks if this annotation is present in the related entity in order to determine which action must be taken. When *@Nullable* is found then only an update query is executed.

```php
namespace Acme;

/**
 * @Entity profiles
 */
class Profile {
    //...
    
    /**
     * @Type int
     * @Column user_id
     * @Nullable
     */
    public $userId;
}
```

<br/>
#####Addtional options

One-To-Many and Many-To-Many associations support two additional configuration annotations:

  * @Index: Indicates the attribute used for indexation.
  * @OrderBy: Used to obtain a list ordered by the specified attribute.

For example, the **subcategories** association in the *Category* class could be redefined to obtain a list of categories indexed by name and ordered by id. We can achieve this with the following declaration:

```php
namespace Acme;

/**
 * @Entity categories
 */
class Category {
    //...
   
     /**
     * @OneToMany Category
     * @Attr parentId
     * @Index name
     * @OrderBy id DESC
     */
    public $subcategories:   
}
```

<br/>
Storing entities
----------------

<br/>
The *Manager* class provides a *save* method which its pretty self explanatory.
```php
use Acme\Product;

//build entity manager
$manager = $mapper->newManager('Acme\Product');

//create product instance
$product = new Product();
$product->description = 'Android phone';
$product->code = 'PHN087';
$product->price = 178.99;

//store and obtain generated id
$id = $manager->save($product);
```
This method can even store associated entities, which depending on the situation can save a bit of work.

```php
use Acme\User;
use Acme\Profile;

//build entity manager
$manager = $mapper->newManager('Acme\User');

//create user instance
$user = new User();
$user->name = 'emaphp');
$user->email = 'emaphp@localhost.com';

//create profile instance
$profile = new Profile();
$profile->firstName = 'Emmanuel';
$profile->lastName'Antico';
$profile->setGender('M');

//assign profile and store
$user->profile = $profile;
$manager->save($user);
```

When the primary key attribute of an entity is already set, the *save* method does an update query.

```php
use Acme\Product;
use emapper\Query\Attr;

//build entity manager
$manager = $mapper->newManager('Acme\Product');

//update product price and store
$product = $manager->get(Attr::code()->eq('PHN087'));
$product->price = 149.99;
$manager->save($product);
```

By default, this saves the entity along with all associated values. There are some scenarios though in which this behaviour is not necessary and could produce some unnecesary overhead. Let's take the  *Profile* -> *User* association as an example.

```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\Profile');

$profile = $manager->findByPk(100);
$profile->firstName = 'Ishmael';

//save profile along with the related user
$manager->save($profile);
```
Obtaining a profile in this way will also evaluate the **user** association defined in the *Profile* class. As explained before, saving this profile not only updates the *profiles* table but also the associated user. In order to reduce the number of queries to perform we'll set the *association depth* to 0 through the **depth** method.
```php
//get profile without associated data
$profile = $manager->depth(0)->findByPk(100);
```
When the association depth is set to 0 no related data is obtained. That means that doing something like:
```php
$profile->getUser();
```
will return a NULL value. The *save* method also expects a depth parameter (default to 1) that we can manipulate to define if related data must be updated along with the entity. This means that if the related data is not updated we can use the *depth* method to optimize the amount of data to obtain. Then, we can store the modified entity and add a second argument to avoid storing any associated data. The example above clarifies this process.

```php
$manager = $mapper->newManager('Acme\Profile');
$profile = $manager->depth(0)->findByPk(100); //get profile
$profile->firstName = 'Ishmael'; //apply changes
$manager->save($profile, 0); //save
```

<br/>
Dynamic SQL
-----------

<br/>
#####Introduction
Queries could also contain logic expressions which are evaluated againts current arguments. These expressions (or S-expressions) are written in [eMacros](https://github.com/emaphp/eMacros ""), a language based on [lisphp](https://github.com/lisphp/lisphp ""). Dynamic expressions are included between the delimiters *[?* and *?]*. The next example shows a query that sets the condition dynamically by checking the argument type.

```php
//dynamic query
$query = "SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]";

//find by id
$user = $mapper->type('obj')->query($query, 99);

//find by name
$user = $mapper->type('obj')->query($query, 'emaphp');
```

<br/>
#####eMacros 101

Just to give you a basic approach of how S-expressions work here's a list of small examples. Refer to *eMacros* documentation for more.
```scheme
; simple math
(+ 4 10) ; 14
(- 10 4) ; 6
(* 5 3 2) ; 30

; sum first two arguments
(+ (%0) (%1))

; concat
(. "Hello" " " "World!")

; is int?
(int? 6) ; true

; is string?
(string? true) ; false

; cast to float
(as-float "65.32")

; check if argument has attribute
(#name?)

; obtain attribute value
(#description)

; check if configuration key exists
(@limit?)

; get configuration value
(@order)

; if then else
(if (null? (#id)) 'WHERE 1' 'WHERE id=#{id}')
```

<br/>
#####Configuration values

This example adds an ORDER clause if the configuration key 'order' is set.

```php
$query = "SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]";

//get all users
$mapper->type('obj[]')->query($query);

//the option method returns an instance with an additional configuration value
//get ordered users
$mapper->type('obj[]')->option('order', 'last_login')->query($query);
```

<br/>
#####Typed expressions
A value returned by a dynamic SQL expression can be associated to a type by adding the type identifier right after the first delimiter. This example simulates a search using the LIKE operator with the value returned by a dynamic expression that returns a string.

```php
$query = "SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]";

//search by name
$users = $mapper->map('obj[]')->query($query, 'ema');
```

<br/>
Dynamic Attributes
------------------

<br/>
#####Introduction
Dynamic attributes provide us with an alternate method for fetching related data from an entity instance.

<br/>
#####Queries

This example introduces a new entity class named *Sale*. A sale is related to a product by its *productId* property. Let's say that we want to obtain that product without declaring an association. In order to do this, we add a **product** property which includes a special *@Query* annotation. This annotation expects a string containing the query that solves this association.

```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Column product_id
     */
    public $productId;
    
    /**
     * @Query "SELECT * FROM products WHERE id = #{productId}"
     * @Type obj:Acme\Factory\Product
     */
    public $product;
}
```
When used along with *@Query*, the *@Type* annotation specifies the mapping expression to use.

<br/>
#####Adding parameters

The *@Param* annotation can be used to define a list of arguments for a dynamic attribute. These arguments can be either a property of the current entity or a constant value. The annotation *@Param(self)* indicates that the current instance is used as first argument. If no additional parameters are added then it can be ignored. Knowing this we can redefine the product property in two ways.

```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    //...
    
    /**
     * Using Param(self) to send current entity as argument...
     * 
     * @Query "SELECT * FROM products WHERE id = #{productId}"
     * @Param(self)
     * @Type obj:Acme\Factory\Product
     */
    public $product;
}
```
Or the alternative syntax specifying the attribute name to use.

```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    //...
    
    /**
     * ...or using the attribute name as an argument of Param.
     * 
     * @Query "SELECT * FROM products WHERE id = %{i}"
     * @Param(productId)
     * @Type obj:Acme\Factory\Product
     */
    public $product;
}
```

The next example adds a **relatedProducts** property in the *Product* class that includes 2 arguments: the current partial instance and a integer value that sets the amount of objects to return.

```php
namespace Acme\Factory;

/**
 * @Entity products
 */
class Product {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Type string
     */
    public $category;
    
    /**
     * @Query "SELECT * FROM products WHERE category = #{category} LIMIT %{i}"
     * @Param(self)
     * @Param 10
     * @Type obj::Acme\Factory\Product[id]
     */
    public $relatedProducts;
}
```

<br/>
#####Statements

Statements provide a more generic way to obtain values by using a special syntax that includes an entity class plus a statement id separated by a dot. The statement id defines a search criteria according to a list of supported expressions. For example, *User.findByPk* obtains a *User* entity by primary key. The required argument is provided through the *@Param* annotation.


```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    /**
     * @Id
     */
    public $id;
    
    /**
     * @Column product_id
     */
    public $productId;
    
    /**
     * @Statement Product.findByPk
     * @Param(productId)
     */
    public $product;
}
```

<br/>
**List of supported statements**

<table>
    <tr>
        <th>Statement ID</th>
        <th>Example</th>
        <th>Arguments</th>
        <th>Returns</th>
    </tr>
    <tr>
        <td>findByPk</td>
        <td>User.findByPk</td>
        <td>1</td>
        <td>Entity</td>
    </tr>
    <tr>
        <td>findAll</td>
        <td>Product.findAll</td>
        <td><em>None</em></td>
        <td>List</td>
    </tr>
    <tr>
        <td>findBy{PROPERTY}</td>
        <td>
            User.findByEmail<br/>
            Product.findByCode
        </td>
        <td>1</td>
        <td>If PROPERTY is @Id or @Unique: Entity. A list otherwise.</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not]Equals</td>
        <td>
            Product.descriptionEquals<br/>
            User.emailNotEquals
        </td>
        <td>1</td>
        <td>Without NOT + PROPERTY is @Id or @Unique: Entity. A list otherwise.</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not][I]Contains</td>
        <td>
            Profile.surnameContains<br/>
            Product.codeICointains<br/>
            User.emailNotContains
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not][I]StartsWith</td>
        <td>
            Product.categoryStartsWith<br/>
            Product.categoryIStartsWith<br/>
            Profile.firstnameNotStartsWith
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not][I]EndsWith</td>
        <td>
            Product.categoryEndsWith<br/>
            Product.categoryIEndsWith<br/>
            Profile.firstnameNotEndsWith
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}Is[Not]Null</td>
        <td>
            Product.descriptionIsNull<br/>
            User.lastLoginIsNotNull
        </td>
        <td><em>None</em></td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not]GreaterThan[Equal]</td>
        <td>
            Product.idGreaterThan<br/>
            Product.priceGreaterThanEqual<br/>
            Product.priceNotGreaterThan
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not]LessThan[Equal]</td>
        <td>
            Product.idLessThan<br/>
            Product.priceLessThanEqual<br/>
            Product.priceNotLessThan
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not]Between</td>
        <td>
            Product.priceBetween<br/>
            Product.priceNotBetween
        </td>
        <td>2</td>
        <td>List</td>
    </tr>
    <tr>
        <td>{PROPERTY}[Not][I]Matches</td>
        <td>
            Product.categoryMatches<br/>
            Product.codeNotMatches<br/>
            Product.descriptionIMatches
        </td>
        <td>1</td>
        <td>List</td>
    </tr>
</table>

<br/>
#####Stored procedures

In order to bind an attribute to a procedure execution we add a *@Procedure* annotation specifying its name. Additional arguments can be supplied through *@Param*.


```php
namespace Acme\Factory;

/**
 * @Entity products
 */
class Product {
    /**
     * @Id
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $category;
    
    /**
     * @Procedure Products_AvgPriceByCategory
     * @Param(category)
     * @Type float
     */
    public $averagePrice;
}
```

<br/>
#####Macros
*@Eval* evaluates a S-expression against current entity. This powerful feature can be used to obtain a person's age and fullname.
```php
namespace Acme;

/**
 * @Entity people
 */
class Person {
    /**
     * @Id
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Column surname
     */
    public $lastname;
    
    /**
     * @Column birth_date
     * @Type dt
     */
    public $birthDate;
    
    /**
     * @Eval (. (#name) ' ' (#lastname))
     */
    public $fullname;
    
    /**
     * @Eval (as-int (diff-format (#birthDate) (now) "%y"))
     */
    public $age;
}
```

<br/>
#####Conditional attributes

The *@If* and *@IfNot* annotations are used to define conditional attributes. These attributes are evaluated if the given expression evaluates to true with *@If* and false with *@IfNot*. Conditions must also be expressed as macros.

```php
namespace Acme;

/**
 * @Entity posts
 */
class Post {
    /**
     * @Id
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $type;

    /**
     * @Type string
     */
    public $body;
    
    /**
     * @If (== (#type) 'pool')
     * @Statement Option.findByPostId
     * @Param(id)
     */
    public $options;
}
```
Additionally, the *@IfNotNull* annotation evaluates a dynamic attribute if the specified attribute is not null.
```php
namespace Acme;

/**
 * @Entity categories
 */
class Category {
    /**
     * @Id
     * @Typs int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Type int
     * @Column parent_id
     */
    public $parentId;
    
    /**
     * @IfNotNull(parentId)
     * @Statement Category.findByPk
     * @Param(parentId)
     */
    public $parent;
}
```

<br/>
#####Options
The *@Option* annotation set a custom option value for the current attribute. An option must define its name by setting it between parentheses just before the value.
```php
namespace Acme;

/**
 * @Entity users
 */
class User {
    /**
     * @Id
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     */
    public $name;
    
    /**
     * @Type string
     */
    public $email;

    /**
     * @Query "SELECT * FROM contacts WHERE user_id = %{i} [? (. 'ORDER BY ' (@order)) ?]"
     * @Param(id)
     * @Option(order) 'contact_type'
     */
    public $contacts;
}
```

<br/>
Cache
-----

<br/>
#####Introduction
eMapper provides value caching through [SimpleCache](https://github.com/emaphp/simplecache ""), a small PHP library supporing Memcache adn APC cache providers. Before setting a cache provider make sure the required extension is correctly installed.

<br/>
#####Providers
```php
//APC
use SimpleCache\APCProvider;

$provider = new APCProvider;
$mapper->setCacheProvider($provider);

//Memcache
use SimpleCache\MemcacheProvider;

$provider = new MemcacheProvider('localhost', 11211);
$mapper->setCacheProvider($provider);

//Memcached
use SimpleCache\MemcachedProvider;

$provider = new MemcachedProvider('mem');
$provider->addServer('localhost', 11211);
$mapper->setProvider($provider);
```

<br/>
#####Storing values
```php
//get users and store values with the key 'USERS_ALL' (time to live: 60 sec)
//if the key is found in cache no query takes place (write once, run forever)
$users = $mapper->cache('USERS_ALL', 60)->query("SELECT * FROM users");

//cache keys also receive query arguments
//store values with the key 'USER_6'
$user = $mapper->type('obj')->cache('USER_%{i}', 120)->query("SELECT * FROM users WHERE id = %{i}", 6);
```

<br/>
Custom types
------------

<br/>
#####Introduction

Type handlers are classes that manage how a value is stored and retrieved from a column. The following code examples introduce a custom type *RGBColor* which is used to manage a color palette.

```php
namespace Acme;

/**
 * The RGBColor class is a three component class that
 * stores the amount of red, green and blue in a color
 */
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
#####Type handlers

A type handler extends the *eMapper\Type\TypeHandler* class and implements the *setParameter* and *getValue* methods.

```php
namespace Acme;

use eMapper\Type\TypeHandler;

class RGBColorTypeHandler extends TypeHandler {
    /**
     * Converts a RGBColor instance to a string expression
     */
    public function setParameter(RGBColor $color) {
        $red = ($color->red < 16) ? '0' . dechex($color->red) : dechex($color->red % 256);
		$green = ($color->green < 16) ? '0' . dechex($color->green % 256) : dechex($color->green);
		$blue = ($color->blue < 15) ? '0' . dechex($color->blue) : dechex($color->blue % 256);
		return $red . $green . $blue;
    }
    
    /**
     * Generates a new RGBColor instance from a string value
     */
    public function getValue($value) {
        return new RGBColor(hexdec(substr($value, 0, 2)),
                            hexdec(substr($value, 2, 2)),
                            hexdec(substr($value, 4, 2)));
    }
}
```

<br/>
#####Types and aliases
```php
use Acme\RGBColorTypeHandler;

//add type
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());

//mapping to RGBColor
$color = $mapper->type('Acme\RGBColor')->query("SELECT color FROM palette WHERE id = 1");

//using an alias
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'rgb');

//mapping to RGBColor using the previously defined alias
$color = $mapper->type('rgb')->query("SELECT color FROM palette WHERE id = 1");
```

<br/>
#####Using custom types

```php
namespace Acme;

/**
 * @Entity vehicles
 */
class Car {
    /**
     * @Id
     * @Type integer
     */
    public $id;
    
    /**
     * @Type string
     */
    public $manufacturer;
    
    /**
     * @Type rgb
     */
    public $color;
}
```

<br/>
Appendix I: Additional features
------------

<br/>
#####Query debugging

The *debug* method receives a Closure instance which can be used to inspect a query before being sent to the database.

```php
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;

$mapper = new Mapper(new SQLiteDriver('data.db'));

//append call to debug method
$result = $mapper->debug(function($query) {
    //print query
    echo $query. "\n";
})->type('obj')->query('SELECT * FROM products WHERE code = %{i}', 'WER745');

$mapper->close();
```
This method is supported by fluent queries and manager instances as well.

<br/>
#####Resultmaps

Resultmaps are a convenient way of mapping objects and arrays without relying on entities. We declare a resultmap as a class using the appropiate annotations for each property.

```php
namespace Acme;

class UserResultMap {
    /**
     * @Type int
     */
    public $id;
    
    /**
     * @Type string
     * @Column firstname
     */
    public $name;
    
    /**
     * @Column last_login
     */
    public $lastLogin;
}
```

We instruct a mapper instance to use a resultmap by appending a call to the *resultmap* method. This method espects the resultmap class fullname as an argument.

```php
use eMapper\Mapper;
use eMapper\Engine\SQLite\SQLiteDriver;

$mapper = new Mapper(new SQLiteDriver('data.db'));

//the obtained array has the 'id', 'name' and 'lastLogin' keys defined
$user = $mapper
->resultmap('Acme\UserResultMap')
->type('arr')
->query("SELECT * FROM users WHERE id = %{i}", 7);

$mapper->close();
```

<br/>
Appendix II: Annotations
------------

<br/>
#####Class annotations

<table>
    <tr>
        <th>Annotation</th>
        <th>Description</th>
        <th>Example</th>
    </tr>
    <tr>
        <td>@Entity</td>
        <td>Used to indicate that a class is an entity. Value must declare the associated table name.</td>
        <td>@Entity users</td>
    </tr>
</table>

<br/>
#####Property annotations

<table>
    <tr>
        <th>Annotation</th>
        <th>Description</th>
        <th>Example</th>
    </tr>
    <tr>
        <td>@Id</td>
        <td>Indicates that a property is a primary key.</td>
        <td>-</td>
    </tr>
     <tr>
        <td>@Type</td>
        <td>Indicates the type of the current property.</td>
        <td>@Type string</td>
    </tr>
     <tr>
        <td>@Column</td>
        <td>Indicates the column that is referenced by the property.</td>
        <td>@Column user_id</td>
    </tr>
     <tr>
        <td>@Unique</td>
        <td>Indicates that a property is unique.</td>
        <td>-</td>
    </tr>
    <tr>
        <td>@Nullable</td>
        <td>Indicates that a property can store NULL values.</td>
        <td>-</td>
    </tr>
    <tr>
        <td>@ReadOnly</td>
        <td>Indicates that a property is read-only and therefore will not be used for INSERT queries.</td>
        <td>-</td>
    </tr>
    <tr>
        <td>@OnDuplicate</td>
        <td>Checks if the value for the current property is already present. Value must indicate which action to take if the value is found, being "ignore" or "update" the possible options.</td>
        <td>@OnDuplicate ignore</td>
    </tr>
</table>

<br/>
#####Associations
<br/>
<table>
    <tr>
        <th>Annotation</th>
        <th>Description</th>
        <th>Example</th>
    </tr>
    <tr>
        <td>@OneToOne</td>
        <td>Indicates that a property is a one-to-one association. Must indicate which entity class is referenced. Requires @Attr.</td>
        <td>@OneToOne Profile</td>
    </tr>
    <tr>
        <td>@OneToMany</td>
        <td>Indicates that a property is a one-to-many association. Must indicate which entity class is referenced. Requires @Attr.</td>
        <td>@OneToMany Post</td>
    </tr>
    <tr>
        <td>@ManyToOne</td>
        <td>Indicates that a property is a many-to-one association. Must indicate which entity class is referenced. Requires @Attr.</td>
        <td>@ManyToOne Group</td>
    </tr>
    <tr>
        <td>@ManyToMany</td>
        <td>Indicates that a property is a many-to-many association. Must indicate which entity class is referenced. Requires @Join.</td>
        <td>@ManyToMany Product</td>
    </tr>
     <tr>
        <td>@Attr</td>
        <td>Indicates which property is used for an association. If the property is declared within the same class then it must be expressed between parentheses.</td>
        <td>@Attr(clientId)<br/>@Attr userId</td>
    </tr>
    <tr>
        <td>@Join</td>
        <td>Indicates the join table used for a many-to-many association. It also must declare the column names used for joining, first being the one that references current entity.</td>
        <td>@Join(user_id,task_id) users_tasks</td>
    </tr>
    <tr>
        <td>@Index</td>
        <td>Indicates which property is used for indexation.</td>
        <td>@Index productId</td>
    </tr>
     <tr>
        <td>@OrderBy</td>
        <td>Indicates which property is used for order. Multiple annotations could be included.</td>
        <td>@OrderBy price<br/>@OrderBy countryId ASC</td>
    </tr>
     <tr>
        <td>@Cache</td>
        <td>Indicates if the associated value is stored in cache. Must include a string key. It also can include an amount of time between parentheses.</td>
        <td>@Cache(120) USER_%{i}</td>
    </tr>
     <tr>
        <td>@Cascade</td>
        <td>Indicates that associated values must be updated accordingly once an entity is deleted. Used to keep reference integrity in SQLite.</td>
        <td>-</td>
    </tr>
</table>

<br/>
#####Dynamic attributes
<br/>
<table>
    <tr>
        <th>Annotation</th>
        <th>Description</th>
        <th>Example</th>
    </tr>
    <tr>
        <td>@Query</td>
        <td>Used to bind an attribute with the value returned by a query.</td>
        <td>@Query "SELECT * FROM users"</td>
    </tr>
    <tr>
        <td>@Statement</td>
        <td>Binds an attribute to a statement execution. Value must indicate the entity class and a statement id separated by a "." character.</td>
        <td>@Statement Product.findByCode</td>
    </tr>
    <tr>
        <td>@Procedure</td>
        <td>Binds an attribute to a stored procedure execution.</td>
        <td>@Procedure Products_FindByCategory</td>
    </tr>
    <tr>
        <td>@Eval</td>
        <td>Binds an attribute to a S-expression (macro). Does not support @Param. @Cacheable by default.</td>
        <td>@Eval (. (#surname) ', ' (#firstname))</td>
    </tr>
    <tr>
        <td>@Param</td>
        <td>Adds an argument to a dynamic attribute. The special form @Param(self) indicates that the whole instance is used as an argument. Attribute values must be expressed between parentheses.</td>
        <td>@Param 100<br/>@Param(userId)</td>
    </tr>
    <tr>
        <td>@Type</td>
        <td>Indicates the mapping expression for @Query, @Statement and @Procedure.</td>
        <td>@Type int<br/>@Type obj:Acme\Vehicle[id]</td>
    </tr>
    <tr>
        <td>@Cache</td>
        <td>Indicates the cache options for the given result. Must include a string key and the amount of time to keep in cache between parentheses.</td>
        <td>@Cache(120) PRODUCT_%{s}</td>
    </tr>
    <tr>
        <td>@Option</td>
        <td>Includes a custom option. Must indicate the option name between parentheses.</td>
        <td>@Option(order) 'category'</td>
    </tr>
    <tr>
        <td>@Cacheable</td>
        <td>Indicates that the value returned by a dynamic attribute can be stored in cache.</td>
        <td>-</td>
    </tr>
     <tr>
        <td>@If, @IfNot, @IfNotNull</td>
        <td>Used to indicate that a dynamic attribute must only be evaluated if a condition evaluates to true. @If and @IfNot evaluates a macro against current instance while @IfNotNull verifies that an attribute is not NULL.</td>
        <td>@If (== (#category) 'laptops')<br/>@IfNot (== (#status) 'ready')<br/>@IfNotNull(userId)</td>
    </tr>
</table>

<br/>
Appendix III: Configuration keys
------------

<br/>
Methods like *type*, *indexCallback* and *resultmap* define a list of internal options that are later interpreted by a *Mapper* instance. Here is the full list of these internal options along with their corresponding methods.

<table>
    <tr>
        <th>Option</th>
        <th>Method</th>
        <th>Description</th>
        <th>Expected value</th>
    </tr>
    <tr>
        <td>map.type</td>
        <td>type</td>
        <td>Sets mapping expression.</td>
        <td>string</td>
    </tr>
    <tr>
        <td>map.params</td>
        <td>type (second argument)</td>
        <td>Additional mapping arguments (used only to define a column name when mapping to a simple type).</td>
        <td>string</td>
    </tr>
    <tr>
        <td>map.result</td>
        <td>resultmap</td>
        <td>Sets the resultmap.</td>
        <td>string</td>
    </tr>
    <tr>
        <td>callback.each</td>
        <td>each</td>
        <td>Sets a callback that iterates over the obtained results.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>callback.filter</td>
        <td>filterCallback</td>
        <td>Sets the filter callback to apply to the obtained results.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>callback.empty</td>
        <td>emptyCallback</td>
        <td>Sets the callback to execute if no results are found.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>callback.debug</td>
        <td>debug</td>
        <td>Sets the debugging callback.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>callback.index</td>
        <td>indexCallback</td>
        <td>Sets the indexation callback.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>callback.group</td>
        <td>groupCallback</td>
        <td>Sets the grouping callback.</td>
        <td>Closure</td>
    </tr>
    <tr>
        <td>cache.key</td>
        <td>cache</td>
        <td>Sets the cache key.</td>
        <td>string</td>
    </tr>
    <tr>
        <td>cache.ttl</td>
        <td>cache (second argument)</td>
        <td>Cache TTL (time to live) in seconds.</td>
        <td>integer</td>
    </tr>
</table>

<br/>
License
--------------
<br/>
This code is licensed under the MIT license.
