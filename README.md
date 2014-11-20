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
2015-01-XX - Version 4.0

  * Deprecated: Named Queries
  * Added: Fluent Queries
  * Deprecated: Calling stored procedures through method overloading.
  * Added: StoredProcedure class.
  * Modified: @StatementId renamed to @Statement. Now only accepts expressions containing an entity class and a statement id.
  * Modified: @Scalar renamed to @Cacheable.
  * Modified: @Parameter renamed to @Param.
  * Modified: Mapper::buildManager renamed to newManager.
  * Added: Methods newQuery and newProcedure in Mapper class.
  * Deprecated: @JoinWith and @ForeignKey annotations.
  * Added: @Join annotation.
  * Modified: @OrderBy syntax.

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
- **Indexation and Grouping**: Lists can be indexed or grouped together by a column value.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses writted in *eMacros*.
- **Entity Managers**: Managers provide a set of small ORM features which are common in similar frameworks.
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

eMapper is not ORM-oriented but type-oriented. Everything revolves around queries and *mapping expressions*. The following examples try to give you an idea of how the data mapping engine works.

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
$user = $mapper->type('obj[]')->query("SELECT * FROM users WHERE department = %{s}", 'Sales');

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
$total = $mapper->type('i')->query("SELECT COUNT(*) FROM users WHERE sex = %{s}", 'F');

//mapping to a boolean ('b', 'bool', 'boolean')
$suscribed = $mapper->type('b')->query("SELECT is_suscribed FROM users WHERE id = %{i}", 99);

//mapping to a float ('f', 'float', 'real', 'double')
$price = $mapper->type('f')->query("SELECT MAX(price) FROM products WHERE refurbished = {b}", false);

//mapping to a string ('s', 'str', 'string')
$body = $mapper->type('s')->query("SELECT message FROM posts WHERE slug = %{s}", 'emapper_rocks');

//dates ('DateTime', 'dt', 'timestamp', 'datetime')
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

//using a custom class
$book = $mapper->type('obj:Acme\Library\Book')->query("SELECT * FROM books WHERE isbn = %{s}", "9788437604183");
```

*Note*: One important thing to remember when mapping to a structure is that values contained in columns declared using the DATE or DATETIME types are converted to instances of [DateTime](http://ar2.php.net/manual/en/class.datetime.php "").

<br/>
Lists
-----------

<br/>
#####Simple types

```php
//just add brackets at the end of the expression and you're done
$id_list = $mapper->type('int[]')->query("SELECT id FROM users");

$prices = $mapper->type('float[]')->query("SELECT price FROM products");

$isbn_list = $mapper->type('string[]')->query("SELECT isbn FROM books");

$creation_dates = $mapper->type('dt[]')->query("SELECT created_at FROM posts");

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

// a group callback does what you expect
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
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE price < %{f} AND category = %{s}", 699.99, 'Laptops');

//argument by position (and optional type)
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE category = %{1} AND price < %{0:f}", 699.99, 'Laptops');
```

<br/>
#####Arrays/Objects as argument

```php
//array as parameter
$parameter = ['password' => sha1('qwerty'), 'modified_at' => new \DateTime];
$mapper->query("UPDATE users SET password = #{password}, modified_at = #{modified_at:dt} WHERE name = %{1:string}", $parameter, 'emaphp');

//syntax works with objects as well
use Acme\CMS\Comment;

$comment = new Comment();
$comment->setUserId(100);
$comment->setBody("Hello World");

$mapper->query("INSERT INTO comments (user_id, body) VALUES (#{userId}, #{body});", $comment);
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

//using whereExpr
$user = $query->from('users')
->whereExpr('id = %{i}')
->whereArgs(1)
->fetch('obj');

/**
 * COLUMNS
 */

$query = $mapper->newQuery();

//define columns to fetch
$users = $query->from('users')
->select('id', 'name', 'email')
->fetch('obj[]');

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
->orderBy(Column::name(), Column::id('ASC'))
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
->select(Column::u__id(), Column::u__name('username'), Column::p__name())
->fetch();

/**
 * GROUP BY + HAVING
 */

$query = $mapper->newQuery();

$employees = $query->from('Employees', 'emp')
->innerJoin('Orders', 'ord', 'ord.employee_id = emp.id')
->select('emp.lastname', 'COUNT(ord.id)')
->having('COUNT(ord.id) > 10')
//alternative syntax: ->having('COUNT(ord.id) > %{i}', 10)
->fetch();

use eMapper\Query\Func as F;

$employees = $query->from('Employees', 'emp')
->innerJoin('Orders', 'ord', Column::ord__employee_id()->eq(Column::emp__id()))
->select(Column::emp__lastname(), F::COUNT(Column::ord__id()))
->groupBy(Column::emp__lastname())
->having(F::COUNT(Column::ord__id())->gt(10))
->fetch();
```

<br/>
#####Insert

```php
$query = $mapper->newQuery();

//INSERT INTO users VALUES ('emaphp', 'M', '1984-07-05')
$query->insertInto('users')->values('emaphp', 'M', '1984-07-05')->exec();

//values as array
$values = ['emaphp', 'M', '1984-07-05'];
$query->insertInto('users')->valuesArray($values)->exec();

//INSERT INTO users (name, birth_date, sex) VALUES ('emaphp', '1984-07-05', 'M')
$query->insertInto('users')
->columns('name', 'birth_date', 'sex')
->values('emaphp', '1984-07-05', 'M')
->exec();

//using valuesExpr
$query->insertInto('users')
->columns('name', 'birth_date', 'sex')
->valuesExpr('%{s}, %{dt}, %{s}')
->values('emaphp', '1984-07-05', 'M')
->exec();
```

<br/>
#####Update

```php
use eMapper\Query\Column;

$query = $mapper->newQuery();

//UPDATE users SET language = 'sp', last_login = 2015-01-01 00:00:00
//WHERE name = 'emaphp'
$query->update('users')
->set('language', 'sp')
->set('last_login', new \Datetime())
->where(Column::name()->eq('emaphp'))
->exec();

//using setExpr + whereExpr
$query->update('users')
->setExpr('language = %{s}, last_login = %{dt}')
->setArgs('sp', new \Datetime())
->whereExpr('name = %{s}')
->whereArgs('emaphp')
->exec();
```

<br/>
#####Delete

```php
use eMapper\Query\Column;

$query = $mapper->newQuery();

//DELETE FROM staff WHERE role <> 'developer'
$query->deleteFrom('staff')
->where(Column::role()->eq('developer', false))
->exec();

//using whereExpr
$query->deleteFrom('staff')
->whereExpr('role <> %{s}')
->whereArgs('developer')
->exec();
```


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

//CALL Users_Create(...)
$proc = $mapper->newProcedure('Users_Create');
//if a procedure returns a value it can also be mapped
$id = $proc->usePrefix(false)->type('i')->call('emaphp', '1984-07-05', 'M');
```


<br/>
Entity Managers
---------------

<br/>
#####Entities and Annotations
Entity managers are objects that behave like DAOs ([Data Access Object](http://en.wikipedia.org/wiki/Data_access_object "")) for a specified class. The first step to create an entity manager is designing an entity class. The following example shows an entity class named *Product*. The *Product* class obtains its values from the *pŕoducts* table, indicated by the *@Entity* annotation. This class defines 5 attributes, and each one defines its type through the *@Type* annotation. If the attribute name differs from the column name we can specify a *@Column* annotation indicating the real one. As a general rule, all entities must define a primary key attribute. The *Product* class sets its **id** attribute as primary key using the *@Id* annotation.

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
    private $id;
    
    /**
     * @Type string
     * @Column pcod
     */
    private $code;

    /**
     * @Type string
     */
    private $description;

    /**
     * @Type string
     */
    private $category;
    
    /**
     * @Type float
     */
    private $price;
    
    public function getId() {
        return $this->id;
    }
    
    public function setCode($code) {
        $this->code = $code;
    }
    
    public function getCode()  {
        return $this->code;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }
    
    public function setCategory($category) {
        $this->category = $category;
    }

    public function getCategory(){
        return $this->category;
    }
    
    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }
}
```
Managers are created through the *newManager* method in the *Mapper* class. This method expects the entity class full name. Managers are capable of getting results using filters without having to write SQL manually.

```php
//create a products manager
$products = $mapper->newManager('Acme\\Factory\\Product');

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
use eMapper\Query\Cond as Q;

$list = $products
->filter(Q::where(Attr::category()->eq('E-Books'), Attr::price()->lt(799.99)))
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

//order and limit clauses
$list = $products
->orderBy(Attr::id())
->limit(15)
->find();

//setting the order type
$list = $products
->orderBy(Attr::id('ASC'), Attr::category())
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

//max price (returns as integer)
$max = $products
->filter(Attr::code()->startswith('SONY'))
->max(Attr::price(), 'int');
```

<br/>
#####Storing objects

```php
use Acme\Factory\Product;

//create manager
$products = $mapper->newManager('Acme\Factory\Product');

$product = new Product;
$product->setCode('ACM001');
$product->setDescription('Test product');
$product->setCategory('Explosives');
$product->setPrice(149.90);

//save object
$products->save($product);

//if the entity already has an id 'save' produces an UPDATE query
$product = $products->get(Attr::code()->eq('ACM001'));
$product->setPrice(129.90);
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

The next example shows 2 entities (*Profile* and *User*) and how to declare a One-To-One association to obtain a *User* entity from a fetched profile.

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
    private $id;
    
    /**
     * @Column user_id
     */
    private $userId;
    
    /**
     * @Column firstname
     */
    private $firstName;
    
    /**
     * @Type string
     */
    private $surname;
    
    /**
     * @OneToOne User
     * @Attr(userId)
     */
    private $user;
    
    //...
}
```
In this example, the *Profile* class declares a **user** property which defines a one-to-one association with the *User* class. The *@Attr* annotation specifies which property is used to perform the required join.

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
    private $id;
    
    /**
     * @Column username
     */
    private $name;
    
    /**
     * @OneToOne Profile
     * @Attr userId
     * @Lazy
     */
    private $profile;
    
    //...
}
```
The *User* class defines the **profile** property as a one-to-one associaton with the *Profile* class. Notice that this time the *@Attr* annotation defines the property name as a value, not as an argument. Also, this association is declared as **lazy**, which means that is not evaluated right away. The following example shows how to obtain a *Profile* instance and its associated user.

```php
//obtain the profile with ID = 100
$manager = $mapper->newManager('Acme\Profile');
$profile = $manager->findByPk(100); // returns a Profile instance
$user = $profile->getUser(); // returns the associated User instance
```

Lazy associations returns an instance of *eMapper\AssociationManager*. This means that invoking the *getProfile* method will return a manager instance. In order to get the referred value we append a call to the *fetch* method.

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

Suppose we need to design a pet shop database to store data from a list of clients and their respective pets. The first step after creating the database will be implementing the *Client* and *Pet* entity classes. The *Client* class has a one-to-many association to the *Pet* class provided through the **pets** property. The required attribute (**clientId**) is specified as a value of the *@Attr* annotation. This annotation references the attribute in the *Pet* class that stores the client identifier.
```php
namespace Acme;

/**
 * @Entity clients
 */
class Client {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $name;
    
    /**
     * @Type string
     */
    private $surname;
 
    /**
     * @OneToMany Pet
     * @Attr clientId
     */   
    private $pets;
}
```
From the point of view of the *Pet* class this is a many-to-one association. The **owner** association is resolved through the **clientId** attribute of the current class, meaning that in this case it has to be specified as an argument of the *@Attr* annotation.

```php
namespace Acme;

/**
 * @Entity pets
 */
class Pet {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Column client_id
     */
    private $clientId;
    
    /**
     * @Type string
     */
    private $name;
    
    /**
     * @Type string
     */
    private $type;
    
    /**
     * @Column birth_date
     * @Type string
     */
    private $birthDate;
    
    /**
     * @ManyToOne Client
     * @Attr(clientId)
     */
    private $owner;
    //...
}
```
This small example obtains all clients that have dogs.
```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\Client');

//get all clients that have dogs
$clients = $manager->find(Attr::pets__type()->eq('Dog'));
```
And this one obtains all pets for a given client.
```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\Pet');

//get all pets of Joe Doe
$pets = $manager->find(Attr::owner__name()->eq('Joe'),
                       Attr::owner__surname()->eq('Doe'));
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
    private $id;
    
    /**
     * @Column username
     */
    private $name;
    
    /**
     * @Type string
     */
    private $mail;
    
    /**
     * @ManyToMany Product
     * @Join(->user_id,prod_id->) favorites
     * @Lazy
     */
    private $favorites;
}
```
The *@JoinWith* annotation must provide the join table name as argument and the column that references the current entity as its value. In this case, this association is resolved using the *favorites* table and the user identifier stored in the *user_id* column on that table. The *@ForeignKey* annotation must then define the column name in the join table that references the target entity (*Product*). The following code shows an example on how to query for a user favorited products.

```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\User');

//get all users that like Android
$users = $manager->find(Attr::favorites__description()->contains('Android'));
```

<br/>
#####Recursive associations

There are some scenarios in which an entity is associated to itself in more than one way. In this example we'll introduce the *Category* entity class to explore in detail this type of associations.

```php
namespace Acme;

/**
 * @Entity categories
 */
class Category {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $name;
    
    /**
     * @Column parent_id
     * @Type integer
     * @Nullable
     */
    private $parentId;
    
    /**
     * @ManyToOne Category
     * @Attr(parentId)
     */
    private $parent;
    
    /**
     * @OneToMany Category
     * @Attr parentId
     */
    private $subcategories;
    //...
}
```

The *Category* class its related to itself through the **parent** and **subcategories** associations. Both of them need to specify the *parentId* attribute as the join attribute. Obtaining all subcategories
of a given category can be resolved in the following way.

```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\Category');

//get all subcategories of 'Technology'
$categories = $mapper->find(Attr::parent__name()->eq('Technology'));
```
You may have noticed that the *parentId* attribute has an additional annotation. The *@Nullable* annotation specifies that the *parent_id* column could also take null values. It is important to add this annotation when having many-to-one associations related to that attribute as it determines if an entity must be deleted if a foreing key is not properly set. In short, this allows the existence of entities without a parent category.


<br/>
#####Configuration

One-To-Many and Many-To-Many associations support two additional configuration annotations:

  * @Index: Indicates the attribute used for indexation.
  * @OrderBy: Used to obtain a list ordered by the specified attribute.

For example, the **subcategories** association in the *Category* class could be redefined to obtain a list of categories indexed by name and ordered by id. We can achieve this with the following declaration:

```php
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
    private $subcategories:
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
$product->setDescription('Android phone');
$product->setCode('PHN087');
$product->setPrice(178.99);

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
$user->setName('emaphp');
$user->setEmail('emaphp@localhost.com');

//create profile instance
$profile = new Profile();
$profile->setFirstName('Emmanuel');
$profile->setLastName('Antico');
$profile->setGender('M');

//assign profile and store
$user->setProfile($profile);
$manager->save($user);
```

When the primary key attribute of an entity is already set, the *save* method does an update query.

```php
use Acme\Product;
use eMapper\Query\Attr;

//build entity manager
$manager = $mapper->newManager('Acme\Product');

//update product price and store
$product = $manager->get(Attr::code()->eq('PHN087'));
$product->setPrice(149.99);
$manager->save($product);
```

By default, this saves the entity along with all associated values. There are some scenarios though in which this default behaviour is not necessary and could produce some unnecesary overhead. Let's take the  *Profile* -> *User* association example.

```php
use eMapper\Query\Attr;

$manager = $mapper->newManager('Acme\Profile');

$profile = $manager->findByPk(100);
$profile->setFirstName('Ishmael');

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
will return a NULL value. The *save* method also expects a depth parameter (default to 1) that we can manipulate to define if related data must be updated along with the entity. To simplify, if the related data is not updated we can use the *depth* method to optimize the amount of data to obtain. Then, we can store the modified entity and add a second argument to avoid storing any associated data. Finally, our example will look like the following.

```php
$manager = $mapper->newManager('Acme\Profile');
$profile = $manager->depth(0)->findByPk(100);
$profile->setFirstName('Ishmael');
$manager->save($profile, 0); //save only the profile data
```

<br/>
Dynamic SQL
-----------

<br/>
#####Introduction
Queries could also contain logic expressions which are evaluated againts current arguments. These expressions (or S-expressions) are written in [eMacros](https://github.com/emaphp/eMacros ""), a language based on [lisphp](https://github.com/lisphp/lisphp ""). Dynamic expressions are included between the delimiters *[?* and *?]*. The next example shows a query that sets the condition dynamically according to the argument type.
```sql
SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]
```

```php
$query = "SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]";

//find by id
$user = $mapper->type('obj')->query($query, 99);

//find by name
$user = $mapper->type('obj')->query($query, 'emaphp');
```

<br/>
#####eMacros 101

Just to give you a basic approach of how S-expressions work here's a list of small examples. Refer to eMacros documentation for more.
```lisp
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

; get property value
(#description)

; get configuration value
(@default_order)

; if then else
(if (null? (#id)) "No id found!" (#id)) ; returns id when not null
```

<br/>
#####Configuration values

This example adds an ORDER clause if the configuration key 'order' is set.
```sql
SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]
```

```php
$query = "SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]";

//get all users
$mapper->type('obj[]')->query($query);

//the option method creates an instance with an additional configuration value
//get ordered users
$mapper->type('obj[]')->option('order', 'last_login')->query($query);
```

<br/>
#####Typed expressions
A value returned by a dynamic SQL expression can be associated to a type by adding the type identifier right after the first delimiter. This example simulates a search using the LIKE operator with the value returned by a dynamic expression that returns a string.
```sql
SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]
```

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
A dynamic attibute is a property that is the result of a query or calculation. Dynamic attributes allow us to retrieve non-associated data for a given entity.

<br/>
#####Querys

This example introduces a new entity class named *Sale*. A sale is obviously related to a product. Let's say that we want to obtain that product without declaring an association. In order to do this, we add a **product** property which includes a special *@Query* annotation. This annotation expects a string containing the query that solves this association. Notice that *Sale* must define a **productId** property in order to store the value to send to the query.

```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Column product_id
     */
    private $productId;
    
    /**
     * @Query "SELECT * FROM products WHERE id = #{productId}"
     * @Type obj:Acme\Factory\Product
     */
    private $product;
    
    //...
}
```
When used along with *@Query*, the *@Type* annotation specifies the mapping expression to use.

<br/>
#####Adding parameters

The *@Parameter* annotation can be used to define a list of arguments for a dynamic attribute. These arguments can be either a property of the current entity or a constant value. A special annotation *@Self* indicates that the current instance is used as first argument. Knowing this we can redefine the product property in two ways.

```php
/**
 * @Query "SELECT * FROM products WHERE id = #{productId}"
 * @Param(self)
 * @Type obj:Acme\Factory\Product
 */
private $product;
//Here @Self is not required because the default
//behaviour already uses the entity as unique argument
                  
/**
 * @Query "SELECT * FROM products WHERE id = %{i}"
 * @Param(productId)
 * @Type obj:Acme\Factory\Product
 */
private $product;
//Here productId is the only argument for this query
//This requires a modification in the query argument expression
```

In other words, *@Self* must be added if the specified query receives the current instance and an additional value as arguments. The next example adds a **relatedProducts** property in the *Product* class that includes 2 arguments: the current partial instance and a integer value that sets the amount of objects to return.

```php
namespace Acme\Factory;

class Product {
    //...
    
    /**
     * @Query "SELECT * FROM products WHERE category = #{category} LIMIT %{i}"
     * @Param(self)
     * @Param 10
     * @Type obj::Acme\Factory\Product[id]
     */
    private $relatedProducts;
}
```

<br/>
#####Statements

Named queries could also be called from a dynamic attribute by adding the *@Statement* annotation.

```php
namespace Acme\Factory;

/**
 * @Entity sales
 */
class Sale {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Column product_id
     */
    private $productId;
    
    /**
     * @Statement Product.findByPk
     * @Param(productId)
     */
    private $product;
    
    //...
}
```

<br/>
#####Stored procedures

Procedures are defined through the *@Procedure* annotation. There's not much to say about them except that they ignore the *@Self* annotation for obvious reasons.

```php
/**
 * @Entity products
 */
class Product {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $category;
    
    /**
     * @Procedure Products_AvgPriceByCategory
     * @Param(category)
     * @Type float
     */
    private $averagePrice;
    
    //...
}
```

<br/>
#####Macros
*@Eval* evaluates a macro against current entity. Examples of usage of these type of attributes are getting a person's fullname or calculating his age.
```php
/**
 * @Entity people
 */
class Person {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $name;
    
    /**
     * @Column surname
     */
    private $lastname;
    
    /**
     * @Column birth_date
     * @Type dt
     */
    private $birthDate;
    
    /**
     * @Eval (. (#name) ' ' (#lastname))
     */
    private $fullname; //build person's full name
    
    /**
     * @Eval (as-int (diff-format (#birthDate) (now) "%y"))
     */
    private $age; //calculate person's age
    
    //...
}
```

<br/>
#####Conditional attributes

The *@If* and *@IfNot* annotations are used to define conditional attributes. These attributes are evaluated if the given expression evaluates to true with @If and false with *@IfNot*. Conditions must be expressed also as macros.

```php
/**
 * @Entity users
 */
class User {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $type;

    /**
     * Get login history if user is a member
     * @If (== (#type) 'member')
     * @Query "..."
     * @Param(id)
     */
    private $loginHistory;
    
    /**
     * Get admin notifications if not a member nor guest
     * @IfNot (or (== (#type) 'member') (== (#type) 'guest'))
     * @Query "..."
     * @Param(id)
     */
    private $abuseNotifications;
}
```
Additionally, the *@IfNotNull* annotation evaluates a dynamic attribute if the specified attribute is not null.
```php
/**
 * @Entity categories
 */
class Category {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Type string
     */
    private $name;
    
    /**
     * @Type int
     * @Column parent_id
     */
    private $parentId;
    
    /**
     * @IfNotNull(parentId)
     * @Statement Category.findByPk
     * @Param(paremeterId)
     */
    private $parent;
}
```

<br/>
#####Options
The *@Option* annotation set a custom option value for the current attribute. An option must define its name by setting it between parentheses just before the value.
```php
/**
 * @Entity users
 */
class User {
    /**
     * @Id
     */
    private $id;
    
    /**
     * @Query "SELECT * FROM contacts WHERE user_id = %{i} [? (. 'ORDER BY ' (@order)) ?]"
     * @Param(id)
     * @Option(order) 'contact_type'
     */
    private $contacts;
    
    //...
}
```

<br/>
Cache
-----

<br/>
#####Introduction
eMapper provides value caching through [SimpleCache](https://github.com/emaphp/simplecache ""), a small PHP library that supports APC and Memcache. Before setting a cache provider make sure the required extension is correctly installed.

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

Type handlers are classes that manage how a value is stored and retrieved from a column. To introduce how a type handler works we'll introduce a custom type called *RGBColor*.

```php
namespace Acme;

//the RGBColor class is a three component class that
//stores the amount of red, green and blue in a color
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
    public function setParameter($color) {
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
//add type
use Acme\RGBColorTypeHandler;
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
/**
 * @Entity vehicles
 */
class Car {
    /**
     * @Id
     * @Type integer
     */
    private $id;
    
    /**
     * @Type string
     */
    private $manufacturer;
    
    /**
     * @Type rgb
     */
    private $color;
    
    //...
}

```

<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
