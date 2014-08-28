eMapper
==============

**The Extensible Data Mapper library for PHP**

<br/>
**Author**: Emmanuel Antico
<br/>
**Version**: 3.2.0

<br/>
Changelog
------------------
<br/>
2014-08-28 - Version 3.2.0 

  * Added: Associations.

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
        "emapper/emapper" : "3.2"
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
>Step 2: The Data Mapper

Now that the connection driver is ready we create an intance of the *Mapper* class.
```php
use eMapper\Mapper;

$mapper = new Mapper($driver);
```

<br/>
>Step 3: Have fun

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
->index_callback(function ($product) {
    //return a custom made index
    return $product['code'] . '_' . $product['id'];
})
->query("SELECT * FROM products");

// a group callback does what you expect
//it can also be combined with indexes if needed
$products = $mapper->type('obj[id]')
->group_callback(function ($product) {
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
Named Queries
-------------

<br/>
#####Statements

```php
//declaring a statement
$mapper->stmt("findUserByPk", "SELECT * FROM users WHERE user_id = %{i}");

//statements are invoked with the execute method
$mapper->type('obj[]')->execute('findUserByPk', 100);
```

<br/>
#####Configuration

```php
//the stmt method supports a third argument with the predefined configuration for that query
use eMapper\SQL\Statement;

//declare statement
$mapper->stmt('findProductsByCategory',
              "SELECT * FROM products WHERE category = %{s}",
              Statement::type('obj[]'));

//get products as an object list
$products = $mapper->execute('findProductsByCategory', 'Audio');

//override default configuration and return result as an indexed list of arrays
$products = $mapper->type('array[id]')->execute('findProductsByCategory', 'Smartphones');
```

<br/>
#####Namespaces

```php
//namespaces provide a more organized way of declaring statements
use eMapper\SQL\SQLNamespace;
use eMapper\SQL\Statement;

class UsersNamespace extends SQLNamespace {
    public function __construct() {
        //set namespace id through parent constructor
        parent::__construct('users');
        
        $this->stmt('findByPk',
                    "SELECT * FROM users WHERE id = %{i}",
                    Statement::type('obj'));
                    
        $this->stmt('findByName',
                    "SELECT * FROM users WHERE name = %{s}",
                    Statement::type('obj'));
                    
        $this->stmt('findRecent'
                    "SELECT * FROM users WHERE created_at >= subdate(NOW(), INTERVAL 3 DAY)",
                    Statement::type('obj[id:int]'));
    }
}

//add namespace
$mapper->addNamespace(new UsersNamespace());

//namespace id must be specified as a prefix
$user = $mapper->execute('users.findByPK', 4);
```

<br/>
Stored procedures
-----------------

<br/>
#####Invoking stored procedures

The *Mapper* class uses [method overloading](http://php.net//manual/en/language.oop5.overloading.php "") to translate an invokation to an non-existant method into a stored procedure call.

```php
//MySQL: CALL Users_Clean()
//PostgreSQL: SELECT Users_Clean()
$mapper->Users_Clean();

//if database prefix is set it is used as a prefix
$mapper->setPrefix('COM_');

//MySQL: CALL COM_Users_Clean()
//PostgreSQL: SELECT COM_Users_Clean()
$mapper->Users_Clean();
```

<br/>
#####Mapping values
```php
//simple values con be obtained like usual
$total = $mapper->type('i')->Users_CountActive();

//engines may differ in how to treat a structured value
$user = $mapper->type('obj')->Users_FindByPK(3); //works in MySQL only

//PostgreSQL needs an additional configuration value
//SQL: SELECT * FROM Users_FindByPK(3)
$user = $mapper->type('obj')->option('proc.as_table', true)->Users_FindByPK(3);
```

<br/>
Entity Managers
---------------

<br/>
#####Entities and Annotations
Writing all your queries can turn very frustating real soon. Luckily, eMapper provides a small set of ORM features that can save you a lot of time. Entity managers are objects that behave like DAOs ([Data Access Object](http://en.wikipedia.org/wiki/Data_access_object "")) for a specified class. The first step to create an entity manager is designing an entity class. The following example shows an entity class named *Product*. The *Product* class obtains its values from the *pŕoducts* table, indicated by the *@Entity* annotation. This class defines 5 attributes, and each one defines its type through the *@Type* annotation. If the attribute name differs from the column name we can specify a *@Column* annotation indicating the real one. As a general rule, all entities must define a primary key attribute. The *Product* class sets its **id** attribute as primary key using the *@Id* annotation.

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
Managers are created through the *buildManager* method in the *Mapper* class. This method expects the entity class full name. Managers are capable of getting results using filters without having to write SQL manually.

```php
//create a products manager
$products = $mapper->buildManager('Acme\\Factory\\Product');

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
use eMapper\Query\Q;

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
->index_callback(function ($product) {
    return $product->getId() . substr($product->getCode(), 0, 5);
})
->find();

//order and limit clauses
$list = $products
->order_by(Attr::id())
->limit(15)
->find();

//setting the order type
$list = $products
->order_by(Attr::id('ASC'), Attr::category())
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
$products = $mapper->buildManager('Acme\Factory\Product');

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
$products = $mapper->buildManager('Acme\Factory\Product');

//delete expects an entity as parameter
$product = $products->findByPk(20);
$products->delete($product);

//reduced version
$products->deleteWhere(Attr::id()->eq(20));

//for security reasions, truncating a table requires a special method
$products->truncate();
```

<br/>
Entity Namespaces
-----------------

<br/>
#####Introduction

Entity namespaces are classes that generate statements from an entity class automatically. These classes receive their respective ids from the *@DefaultNamespace* annotation in the entity class declaration. When not specified, the namespace takes the value indicated by the *@Entity* annotation.

```php
namespace Acme\Factory;

/**
 * @Entity products
 * @DefaultNamespace products
 */
class Product {
    /**
     * @Id
     * @Type integer
     */
    private $id;
    
    /**
     * @Unique
     * @Type string
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
    
    //...
}

```

<br/>
#####Adding a entity namespace

```php
//add an entity namespace
use eMapper\SQL\EntityNamespace;
$mapper->addEntityNamespace(new EntityNamespace('Acme\Factory\Product'));

//find by primary key
$product = $mapper->execute('products.findByPk', 2);

//find all
$products = $mapper->execute('products.findAll');

//find by
$products = $mapper->execute('products.findByCategory', 'Laptops');

//override mapping expression
$products = $mapper->type('obj:Acme\Factory\Product[id]')->execute('descriptionIsNull');
```

<br/>
#####Statement list


<table>
    <thead>
        <tr>
            <th>Statement ID</th>
            <th>Example</th>
            <th>Returned Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>findByPk</td>
            <td><em>products.findByPk</em></td>
            <td>Instance (NULL if not found)</td>
        </tr>
        <tr>
            <td>findAll</td>
            <td><em>products.findAll</em></td>
            <td>List</td>
        </tr>
        <tr>
            <td>findBy{PROPERTY}</td>
            <td><em>products.findByCode</em></td>
            <td>Instance if property is @Id or @Unique, a list otherwise</td>
        </tr>
        <tr>
            <td>{PROPERTY}[Not]Equals</td>
            <td><em>products.codeEquals</em><br/><em>products.priceNotEquals</em></td>
            <td>Instance if property is @Id or @Unique, a list otherwise</td>
        </tr>
        <tr>
            <td>{PROPERTY}[Not][I]Contains</td>
            <td><em>products.categoryContains</em><br/><em>products.descriptionNotContains</em><br/><em>products.categoryIContains</em></td>
            <td>List</td>
        </tr>
        <tr>
            <td>{PROPERTY}[Not][I]StartsWith</td>
            <td><em>products.categoryStartsWith</em><br/><em>products.descriptionNotStartsWith</em><br/><em>products.categoryIStartsWith</em></td>
            <td>List</td>
        </tr>
        <tr>
            <td>{PROPERTY}[Not][I]EndsWith</td>
            <td><em>products.categoryEndsWith</em><br/><em>products.descriptionNotEndsWith</em><br/><em>products.categoryIEndsWith</em></td>
            <td>List</td>
        </tr>
        <tr>
            <td>{PROPERTY}[Not]In</td>
            <td><em>products.idIn</em><br/><em>products.idNotIn</em></td>
            <td>List</td>
        </tr>
    <tbody>
        <td>{PROPERTY}[Not]GreaterThan[Equal]</td>
        <td><em>products.idGreaterThan</em><br/><em>products.priceGreaterThanEqual</em><br/><em>products.priceNotGreaterThan</em></td>
        <td>List</td>
    </tbody>
    <tbody>
        <td>{PROPERTY}[Not]LessThan[Equal]</td>
        <td><em>products.idLessThan</em><br/><em>products.priceLessThanEqual</em><br/><em>products.priceNotLessThan</em></td>
        <td>List</td>
    </tbody>
    <tbody>
        <td>{PROPERTY}[Not]IsNull</td>
        <td><em>products.descriptionIsNull</em><br/><em>products.descriptionIsNotNull</em></td>
        <td>List</td>
    </tbody>
    <tbody>
        <td>{PROPERTY}[Not]Between</td>
        <td><em>products.priceBetween</em><br/><em>products.priceNotBetween</em></td>
        <td>List</td>
    </tbody>
    <tbody>
        <td>{PROPERTY}[Not][I]Matches</td>
        <td><em>products.categoryMatches</em><br/><em>products.codeNotMatches</em><br/><em>products.descriptionIMatches</em></td>
        <td>List</td>
    </tbody>
</table>

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
In this example, the *Product* class declares a **user** property which defines a one-to-one association with the *User* class. The *@Attr* annotation specifies which property is used to perform the required join.

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
$manager = $mapper->buildManager('Acme\Profile');
$profile = $manager->findByPk(100); // returns a Profile instance
$user = $profile->getUser(); // returns the associated User instance
```

Lazy associations returns an instance of *eMapper\AssociationManager*. This means that invoking the *getProfile* method will return a manager instance. In order to get the referred value we append a call to the *fetch* method.

```php
//obtain the user with ID = 100
$manager = $mapper->buildManager('Acme\User');
$user = $manager->findByPk(100);
$profile = $user->getProfile(); // returns an AssociationManager instance
$profile = $user->getProfile()->fetch(); // fetch() returns the desired result
```
Associations also provide a mechanism to query for related attributes. Suppose we want to obtain a profile by its user name. We can do this by using a special syntax that specifies the association property and the comparison attribute separated by a doble underscore.

```php
use eMapper\Query\Attr;

//build manager
$manager = $mapper->buildManager('Acme\Profile');

//users.name = 'jdoe'
$profile = $manager->get(Attr::user__name()->eq('jdoe'));
```

<br/>
#####One-To-Many and Many-To-One

Suppose we need to design a pet shop database to store data from a list of clients and their respective pets. The first step after creating the database will be implementing the *Client* and *Pet* entity classes. The *Client* class has a one-to-many association to the *Pet* class provided through the **pets** property. The required attribute (**clientId**) is specified as a value of the *@Attr* annotation.This annotation references to the attribute in the *Pet* class that stores the client identifier.
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
This small example obtains all clients that have dogs.
```php
use eMapper\Query\Attr;

$manager = $mapper->buildManager('Acme\Client');

//get all clients that have dogs
$clients = $manager->find(Attr::pets__type()->eq('Dog'));
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
This small example obtains all pets for a given client.
```php
use eMapper\Query\Attr;

$manager = $mapper->buildManager('Acme\Pet');

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
     * @JoinWith(favorites) prod_id
     * @Column usr_id
     * @Lazy
     */
    private $favorites;
}
```
The *@JoinWith* annotation must provide the join table name as argument and the column that references the associated entity as value. In this case, this association is resolved using the *favorites* table and the product identifier is stored using the *prod_id* column. The *@Column* annotation must then declare which column in the *favorites* table identifies the current entity. The following code shows an example on how to query for a user favorited products.

```php
use eMapper\Query\Attr;

$manager = $mapper->buildManager('Acme\User');

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
     * @Type integer
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

$manager = $mapper->buildManager('Acme\Category');

//get all subcategories of 'Technology'
$categories = $mapper->find(Attr::parent__name()->eq('Technology'));
```



<br/>
Dynamic SQL
-----------

<br/>
#####Introduction
Queries could also contain logic expressions which are evaluated againts current arguments. These expressions (or S-expressions) are written in [eMacros](https://github.com/emaphp/eMacros ""), a language based on lisphp. Dynamic expressions are included between the delimiters [? and ?]. The next example shows a query that sets the condition dynamically according to the argument type.
```sql
SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]
```

```php
//add named query
$mapper->stmt('findUser', "SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]");

//find by id
$user = $mapper->type('obj')->execute('findUser', 99);

//find by name
$user = $mapper->type('obj')->execute('findUser', 'emaphp');
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
//add named query
$mapper->stmt('obtainUsers', "SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]");

//get all users
$mapper->type('obj[]')->execute('obtainUsers');

//the option method creates an instance with an additional configuration value
//get ordered users
$mapper->type('obj[]')->option('order', 'last_login')->execute('obtainUsers');
```

<br/>
#####Typed expressions
A value returned by a dynamic SQL expression can be associated to a type by adding the type identifier right after the first delimiter. This example simulates a search using the LIKE operator with the value returned by a dynamic expression that returns a string.
```sql
SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]
```

```php
//add named query
$mapper->stmt('searchUsers', "SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]");

//search by name
$users = $mapper->map('obj[]')->execute('searchUsers', 'ema');
```

<br/>
Dynamic Attributes
------------------

<br/>
#####Introduction
While associations are pretty useful by themselves you may want to go a step further. A dynamic attibute is a property that is the result of a query or calculation. This means that attributes could either be associated to a macro or a custom user query.

<br/>
#####Querys

This example introduces a new entity class named *Sale*. A sale is obviously related to a product. Let's say that we want to obtain that product using the corresponding column. In order to do this, we add a **product** property which includes a special *@Query* annotation. This annotation expects a string containing the query that solves this association. Notice that *Sale* must define a **productId** property in order to store the value sent to the query.

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
 * @Self
 * @Type obj:Acme\Factory\Product
 */
private $product; //Here @Self is not required because the default
                  //behaviour already uses the entity as unique argument
                  
/**
 * @Query "SELECT * FROM products WHERE id = %{i}"
 * @Parameter(productId)
 * @Type obj:Acme\Factory\Product
 */
private $product; //Here productId is the only argument for this query
                  //This requires a modification in the query argument expression
```

In other words, *@Self* must be added if the specified query receives the current instance and an additional value as arguments. The next example adds a **relatedProducts** property in the *Product* class that includes 2 arguments: the current partial instance and a integer value that sets the amount of objects to return.

```php
namespace Acme\Factory;

class Product {
    //...
    
    /**
     * @Query "SELECT * FROM products WHERE category = #{category} LIMIT %{i}"
     * @Self
     * @Parameter 10
     * @Type obj::Acme\Factory\Product[id]
     */
    private $relatedProducts;
}
```

<br/>
#####Statements

Named queries could also be called from a dynamic attribute by adding the *@StatementId* annotation.

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
     * @StatementId products.findByPk
     * @Parameter(productId)
     * @Type obj:Acme\Factory\Product
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
     * @Parameter(category)
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

The *@If* and *@IfNot* annotations are used to define conditional attributes. These attributes are evaluated if the given expression evaluates to true with @If and false with *@IfNot*. Conditions must be expressed alaso as macros.

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
     * @If (== (#type) 'member')
     * @StatementId "users.loginHistory"
     * @Parameter(id)
     */
    private $loginHistory; //get login history if user is a member
    
    /**
     * @IfNot (or (== (#type) 'member') (== (#type) 'guest'))
     * @StatementId 'admin.findNotifications'
     * @Parameter(id)
     */
    private $abuseNotifications; //get admin notifications if not a member nor guest
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
     * @Parameter(id)
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
Currently, eMapper supports APC, Memcache and Memcached. Before setting a cache provider make sure the required extension is correctly installed.

<br/>
#####Providers
```php
//APC
use eMapper\Cache\APCProvider;

$provider = new APCProvider;
$mapper->setCacheProvider($provider);

//Memcache
use eMapper\Cache\MemcacheProvider;

$provider = new MemcacheProvider('localhost', 11211);
$mapper->setCacheProvider($provider);

//Memcached
use eMapper\Cache\MemcachedProvider;

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
