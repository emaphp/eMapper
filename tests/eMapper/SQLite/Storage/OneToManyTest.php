<?php
namespace eMapper\SQLite\Storage;

use eMapper\SQLite\StorageTest;
use Acme\Storage\Client;
use Acme\Storage\Pet;
use eMapper\Query\Attr;
use Acme\Storage\Driver;
use Acme\Storage\Car;

/**
 * @group storage
 * @group sqlite
 */
class OneToManyTest extends StorageTest {
	public function testCreateClient() {
		$this->truncateTable('clients');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$client = new Client();
		$client->firstname = 'Eric';
		$client->lastname = 'Cartman';
		$clientId = $manager->save($client);
		$this->assertInternalType('integer', $clientId);
		$this->assertEquals(1, $manager->count());
		$mapper->close();
	}
	
	public function testCreatePet() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Pet');
		
		$pet = new Pet();
		$pet->name = 'Snowball';
		$pet->type = 'Cat';
		
		$client = new Client();
		$client->firstname = 'Eric';
		$client->lastname = 'Cartman';
		$pet->owner = $client;
		$petId = $manager->save($pet);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$this->assertEquals(1, $mapper->newManager('Acme\Storage\Client')->count());
		
		$pet = $manager->findByPk($petId);
		$this->assertInstanceOf('Acme\Storage\Client', $pet->owner);
		$this->assertEquals('Eric', $pet->owner->firstname);
		
		$mapper->close();
	}
	
	public function testCreateEmptyClient() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$pet = new Pet();
		$pet->name = 'Pichu';
		$pet->type = 'dog';
		
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
		$client->pets = [
			$pet
		];
		
		$manager->save($client, 0);
		$this->assertEquals(0, $mapper->newManager('Acme\Storage\Pet')->count());
		$mapper->close();
	}
	
	public function testCreateChilds() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
		
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
		
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
		
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$pets = $mapper->newManager('Acme\Storage\Pet');
		$this->assertEquals(2, $pets->count());
		
		$client = $manager->findByPk($clientId);
		$this->assertInternalType('array', $client->pets);
		$this->assertCount(2, $client->pets);
		$this->assertArrayHasKey('Pichu', $client->pets);
		$this->assertArrayHasKey('Michu', $client->pets);
		
		$mapper->close();
	}
	
	public function testUpdateParent() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
		
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
		
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
		
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//make changes
		$client = $manager->get(Attr::firstname()->eq('Emmanuel'));
		$client->lastname = 'Goldberg';
		$manager->save($client);
		
		//check values
		$client = $manager->findByPk($clientId);
		$this->assertEquals('Goldberg', $client->lastname);
		$this->assertCount(2, $client->pets);
		
		$mapper->close();
	}
	
	public function testUpdateChilds() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
		
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
		
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
		
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//make changes
		$client = $manager->get(Attr::firstname()->eq('Emmanuel'));
		unset($client->pets['Michu']);
		$manager->save($client);
		
		//check values
		$pets = $mapper->newManager('Acme\Storage\Pet');
		$this->assertEquals(1, $pets->count());
		$mapper->close();
	}
	
	public function testAppendChilds() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
	
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
	
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
	
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
	
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//make changes
		$fishy = new Pet();
		$fishy->name = 'Fishy';
		$fishy->type = 'Fish';
		
		$client = $manager->findByPk($clientId);
		$client->pets[] = $fishy;
		$manager->save($client);
		
		$pets = $mapper->newManager('Acme\Storage\Pet');
		$this->assertEquals(3, $pets->count());
		
		$pet = $pets->get(Attr::name()->eq('Fishy'));
		$this->assertEquals($clientId, $pet->clientId);
		$this->assertEquals('Emmanuel', $pet->owner->firstname);
		
		$mapper->close();
	}
	
	public function testUpdateFromChild() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
	
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
	
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
	
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
	
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//make changes
		$pets = $mapper->newManager('Acme\Storage\Pet');
		$pet = $pets->get(Attr::name()->eq('Pichu'));
		$pet->owner->firstname = 'Emma';
		$pets->save($pet);
		
		//check values
		$client = $manager->findByPk($clientId);
		$this->assertEquals('Emma', $client->firstname);
		
		$mapper->close();
	}
	
	public function testDeleteParent() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
	
		$client = new Client();
		$client->firstname = 'Emmanuel';
		$client->lastname = 'Antico';
	
		$pichu = new Pet();
		$pichu->name = 'Pichu';
		$pichu->type = 'Dog';
	
		$michu = new Pet();
		$michu->name = 'Michu';
		$michu->type = 'Cat';
	
		$client->pets = [$pichu, $michu];
		$clientId = $manager->save($client);
		
		//make changes
		$manager->delete($client);
		
		$this->assertEquals(0, $manager->count());
		$this->assertEquals(0, $mapper->newManager('Acme\Storage\Pet')->count());
		
		$mapper->close();
	}
	
	public function testDeleteClient() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Client');
		
		$pet1 = new Pet();
		$pet1->name = 'Pichu';
		$pet1->type = 'dog';
		
		$pet2 = new Pet();
		$pet2->name = 'Michu';
		$pet2->type = 'cat';
		
		$client1 = new Client();
		$client1->firstname = 'Joe';
		$client1->lastname = 'Doe';
		$client1->pets = [
			$pet1
		];
		
		$client2 = new Client();
		$client2->firstname = 'Jane';
		$client2->lastname = 'Doe';
		$client2->pets = [
			$pet2
		];
		
		$manager->save($client1);
		$manager->save($client2);
		$this->assertEquals(2, $manager->count());
		$pets = $mapper->newManager('Acme\Storage\Pet');
		$this->assertEquals(2, $pets->count());
		$manager->delete($client1);
		$this->assertEquals(1, $pets->count());
		$mapper->close();
	}
	
	public function testDeletePet() {
		$this->truncateTable('clients');
		$this->truncateTable('pets');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Pet');
		
		$pet = new Pet();
		$pet->name = 'Pichu';
		$pet->type = 'dog';
		
		$client = new Client();
		$client->firstname = 'Joe';
		$client->lastname = 'Doe';
		
		$pet->owner = $client;
		$manager->save($pet);
		
		$this->assertEquals(1, $manager->count());
		$clients = $mapper->newManager('Acme\Storage\Client');
		$this->assertEquals(1, $clients->count());
		$manager->delete($pet);
		$this->assertEquals(0, $manager->count());
		$this->assertEquals(1, $clients->count());
		
		$mapper->close();
	}
	
	public function testDeleteDriver() {
		$this->truncateTable('drivers');
		$this->truncateTable('cars');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Driver');
		
		$driver = new Driver();
		$driver->name = 'Jake';
		$driver->birthDate = '1978-06-22';
		
		$car = new Car();
		$car->brand = 'Ford';
		$car->model = 'Fiesta';
		$driver->cars = [$car];
		$manager->save($driver);
		
		$cars = $mapper->newManager('Acme\Storage\Car');
		$this->assertEquals(1, $cars->count());
		$manager->delete($driver);
		$this->assertEquals(1, $cars->count());
		$car = $cars->get();
		$this->assertNull($car->driverId);
		
		$mapper->close();
	}
}