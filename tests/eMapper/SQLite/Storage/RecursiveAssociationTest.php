<?php
namespace eMapper\SQLite\Storage;

use eMapper\SQLite\StorageTest;
use Acme\Storage\Category;
use eMapper\Query\Attr;

/**
 * @group storage
 * @group sqlite
 */
class RecursiveAssociationTest extends StorageTest {
	public function testCreateParent() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
		
		$category = new Category();
		$category->name = 'Smartphones';
		
		$parent = new Category();
		$parent->name = 'Phones';
		
		$category->parent = $parent;
		$childId = $manager->save($category);
		
		//check values
		$this->assertEquals(2, $manager->count());
		
		//check child
		$child = $manager->findByPk($childId);
		$this->assertEquals($childId, $category->id);
		$this->assertEquals('Smartphones', $child->name);
		$parentId = $child->parentId;
		
		//check parent
		$parent = $manager->findByPk($parentId);
		$this->assertEquals('Phones', $parent->name);
		
		$mapper->close();
	}
	
	public function testCreateChilds() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
		
		$parent = new Category();
		$parent->name = 'Hardware';
		
		$child1 = new Category();
		$child1->name = 'GPUs';
		
		$child2 = new Category();
		$child2->name = 'Keyboards';
		
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		//check values
		$this->assertEquals(3, $manager->count());
		
		$parent = $manager->findByPk($parentId);
		$this->assertInternalType('array', $parent->subcategories);
		$this->assertCount(2, $parent->subcategories);
		$this->assertArrayHasKey('GPUs', $parent->subcategories);
		$this->assertArrayHasKey('Keyboards', $parent->subcategories);
		
		$child1 = $parent->subcategories['GPUs'];
		$this->assertEquals($parentId, $child1->parentId);
		
		$child2 = $parent->subcategories['Keyboards'];
		$this->assertEquals($parentId, $child2->parentId);
		
		$mapper->close();
	}
	
	public function testDeleteParent() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
		
		$parent = new Category();
		$parent->name = 'Hardware';
		
		$child1 = new Category();
		$child1->name = 'GPUs';
		
		$child2 = new Category();
		$child2->name = 'Keyboards';
		
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		//make changes
		$parent = $manager->findByPk($parentId);
		$manager->delete($parent);
		
		//check values
		$this->assertEquals(2, $manager->count());
		$categories = $manager->index('name')->find();
		$this->assertArrayHasKey('GPUs', $categories);
		$this->assertArrayHasKey('Keyboards', $categories);
		$this->assertNull($categories['GPUs']->parent);
		$this->assertNull($categories['Keyboards']->parent);
	}
	
	public function testUpdateChild() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
		
		$parent = new Category();
		$parent->name = 'Hardware';
		
		$child1 = new Category();
		$child1->name = 'GPUs';
		
		$child2 = new Category();
		$child2->name = 'Keyboards';
		
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		$category = $manager->get(Attr::name()->eq('Keyboards'));
		$category->name = 'Mouses';
		$manager->save($category);
		
		//check values
		$this->assertEquals(3, $manager->count());
		
		$parent = $manager->findByPk($parentId);
		$this->assertInternalType('array', $parent->subcategories);
		$this->assertCount(2, $parent->subcategories);
		$this->assertArrayHasKey('GPUs', $parent->subcategories);
		$this->assertArrayHasKey('Mouses', $parent->subcategories);
		
		$child1 = $parent->subcategories['GPUs'];
		$this->assertEquals($parentId, $child1->parentId);
		
		$child2 = $parent->subcategories['Mouses'];
		$this->assertEquals($parentId, $child2->parentId);
		
		$mapper->close();
	}
	
	public function testRemoveChild() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
	
		$parent = new Category();
		$parent->name = 'Hardware';
	
		$child1 = new Category();
		$child1->name = 'GPUs';
	
		$child2 = new Category();
		$child2->name = 'Keyboards';
	
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);

		//make changes
		$parent = $manager->findByPk($parentId);
		unset($parent->subcategories['Keyboards']);
		$manager->save($parent);
		
		//check values
		$this->assertEquals(3, $manager->count());
		$parent = $manager->findByPk($parentId);
		$this->assertInternalType('array', $parent->subcategories);
		$this->assertCount(1, $parent->subcategories);
		$this->assertArrayHasKey('GPUs', $parent->subcategories);
		$this->assertArrayNotHasKey('Keyboards', $parent->subcategories);
		
		$mapper->close();
	}
	
	public function testUpdateParent() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
	
		$parent = new Category();
		$parent->name = 'Hardware';
	
		$child1 = new Category();
		$child1->name = 'GPUs';
	
		$child2 = new Category();
		$child2->name = 'Mouses';
	
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		//make changes
		$gpus = $manager->get(Attr::name()->eq('GPUs'));
		$mouses = $manager->get(Attr::name()->eq('Mouses'));
		$mouses->parent = $gpus;
		$manager->save($mouses);
		
		//check values
		$this->assertEquals(3, $manager->count());
		
		$parent = $manager->findByPk($parentId);
		$this->assertCount(1, $parent->subcategories);
		$this->assertArrayHasKey('GPUs', $parent->subcategories);
		
		$gpus = $manager->get(Attr::name()->eq('GPUs'));
		$this->assertCount(1, $gpus->subcategories);
		$this->assertArrayHasKey('Mouses', $gpus->subcategories);
		
		$mapper->close();
	}
	
	public function testRemoveParent() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
	
		$parent = new Category();
		$parent->name = 'Hardware';
	
		$child1 = new Category();
		$child1->name = 'GPUs';
	
		$child2 = new Category();
		$child2->name = 'Mouses';
	
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		//make changes
		$mouses = $manager->get(Attr::name()->eq('Mouses'));
		$mouses->parent = null;
		$manager->save($mouses);
		
		//check values
		$this->assertEquals(3, $manager->count());
		
		$parent = $manager->findByPk($parentId);
		$this->assertInternalType('array', $parent->subcategories);
		$this->assertCount(1, $parent->subcategories);
		$this->assertArrayHasKey('GPUs', $parent->subcategories);
		$this->assertArrayNotHasKey('Mouses', $parent->subcategories);
		
		$mouses = $manager->get(Attr::name()->eq('Mouses'));
		$this->assertNull($mouses->parent);
		
		$mapper->close();
	}
	
	public function testRemoveChilds() {
		$this->truncateTable('categories');
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Category');
	
		$parent = new Category();
		$parent->name = 'Hardware';
	
		$child1 = new Category();
		$child1->name = 'GPUs';
	
		$child2 = new Category();
		$child2->name = 'Keyboards';
	
		$parent->subcategories = [$child1, $child2];
		$parentId = $manager->save($parent);
		
		//make change
		$parent = $manager->findByPk($parentId);
		$parent->subcategories = [];
		$manager->save($parent);
		
		//check values
		$this->assertEquals(3, $manager->count());
		$parent = $manager->findByPk($parentId);
		$this->assertEmpty($parent->subcategories);
		
		$child = $manager->get(Attr::name()->eq('GPUs'));
		$this->assertNull($child->parentId);
		
		$child = $manager->get(Attr::name()->eq('Hardware'));
		$this->assertNull($child->parentId);
		
		$mapper->close();
	}
}