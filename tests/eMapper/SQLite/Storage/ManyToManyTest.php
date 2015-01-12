<?php
namespace eMapper\SQLite\Storage;

use eMapper\SQLite\StorageTest;
use Acme\Storage\Employee;
use Acme\Storage\Task;
use eMapper\Query\Attr;

/**
 * @group sqlite
 * @group storage
 */
class ManyToManyTest extends StorageTest {
	public function testCreatEmployee() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks = [$task1, $task2];
		$empId = $manager->save($employee);
		
		$this->assertEquals(1, $manager->count());
		$tasks = $mapper->newManager('Acme\Storage\Task');
		$this->assertEquals(2, $tasks->count());
		
		//check relation
		$task = $tasks->get(Attr::name()->eq('Test'));
		$this->assertCount(1, $task->employees);
		$this->assertEquals($empId, $task->employees[0]->id);
		
		$task = $tasks->get(Attr::name()->eq('Rest'));
		$this->assertCount(1, $task->employees);
		$this->assertEquals($empId, $task->employees[0]->id);
		
		//check join table
		$query = $mapper->newQuery();
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testUpdateTask() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks = [$task1, $task2];
		$empId = $manager->save($employee);
		
		$tasks = $mapper->newManager('Acme\Storage\Task');
		$task = $tasks->get();
		$task->name = 'Edited';
		$tasks->save($task);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$tasks = $mapper->newManager('Acme\Storage\Task');
		$this->assertEquals(2, $tasks->count());
		
		//check join table
		$query = $mapper->newQuery();
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testUpdateEmployee() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
	
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks = [$task1, $task2];
		$empId = $manager->save($employee);
		
		$employee = $manager->findByPk($empId);
		$employee->department = 'Q&A';
		$manager->save($employee);

		//check values
		$this->assertEquals(1, $manager->count());
		$tasks = $mapper->newManager('Acme\Storage\Task');
		$this->assertEquals(2, $tasks->count());
		
		//check join table
		$query = $mapper->newQuery();
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testAppendTask() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		$employee->tasks[] = $task1;
		$empId = $manager->save($employee);
		
		//make changes
		$employee = $manager->findByPk($empId);
		$this->assertCount(1, $employee->tasks);
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks[] = $task2;
		$manager->save($employee);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$this->assertEquals(2, $mapper->newManager('Acme\Storage\Task')->count());
		
		$query = $mapper->newQuery();
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testDeleteTask() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
		
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
		
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
		
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
		
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
		
		$empId1 = $manager->save($emp1);
		$empId2 = $manager->save($emp2);
		
		//make changes
		$tasks = $mapper->newManager('Acme\Storage\Task');
		$this->assertEquals(3, $tasks->count());
		$tasks->delete($task2);
		$this->assertEquals(2, $tasks->count());
		
		$query = $mapper->newQuery();
		$this->assertEquals(3, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		$this->assertEquals(1, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId1)->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId2)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testDeleteEmployee() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
		
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
		
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
		
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
		
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
		
		$empId1 = $manager->save($emp1);
		$empId2 = $manager->save($emp2);
		
		//make changes
		$manager->delete($emp1);
		
		//check values
		$this->assertEquals(1, $manager->count());
		$this->assertEquals(3, $mapper->newManager('Acme\Storage\Task')->count());
		
		$query = $mapper->newQuery();
		$this->assertEquals(2, $query->from('emp_tasks')->select('count(*)')->fetch('i'));
		$this->assertEquals(0, $query->from('emp_tasks')->select('count(*)')->where('emp_id = %{i}', $empId1)->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('count(*)')->where('emp_id = %{i}', $empId2)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testRemoveTaskFromParent() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks = [$task1, $task2];
		$empId = $manager->save($employee);
		
		$employee = $manager->findByPk($empId);
		$employee->department = 'Q&A';
		$empId = $manager->save($employee);
		
		//make changes
		$employee = $manager->findByPk($empId);
		unset($employee->tasks[0]);
		$manager->save($employee);
		
		$this->assertEquals(2, $mapper->newManager('Acme\Storage\Task')->count());
		$query = $mapper->newQuery();
		$this->assertEquals(1, $query->from('emp_tasks')->select('count(*)')->where('emp_id = %{i}', $empId)->fetch('i'));
		
		$mapper->close();
	}
	
	public function testRemoveEmployee() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$employee = new Employee();
		$employee->firstname = 'Emmanuel';
		$employee->lastname = 'Antico';
		$employee->department = 'Development';
		
		$task1 = new Task();
		$task1->name = 'Test';
		$task1->startingDate = new \DateTime();
		$task1->started = true;
		
		$task2 = new Task();
		$task2->name = 'Rest';
		$task2->startingDate = new \DateTime();
		$task2->started = false;
		$employee->tasks = [$task1, $task2];
		$empId = $manager->save($employee);
		
		$employee = $manager->findByPk($empId);
		$employee->department = 'Q&A';
		$empId = $manager->save($employee);
		
		//make changes
		$manager->delete($employee);
		
		//check values
		$this->assertEquals(0, $manager->count());
		$this->assertEquals(2, $mapper->newManager('Acme\Storage\Task')->count());
		
		$query = $mapper->newQuery();
		$this->assertEquals(0, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		
		$mapper->close();
	}
	
	public function testCrossedReference() {
		$this->truncateTable('employees');
		$this->truncateTable('tasks');
		$this->truncateTable('emp_tasks');
		
		$mapper = $this->getMapper();
		$manager = $mapper->newManager('Acme\Storage\Employee');
		
		$task1 = new Task();
		$task1->name = 'Task 1';
		$task1->started = true;
		$task1->startingDate = new \Datetime;
		
		$task2 = new Task();
		$task2->name = 'Task 2';
		$task2->started = false;
		$task2->startingDate = new \Datetime;
		
		$task3 = new Task();
		$task3->name = 'Task 3';
		$task3->started = true;
		$task3->startingDate = new \Datetime;
		
		$emp1 = new Employee();
		$emp1->firstname = 'Joe';
		$emp1->lastname = 'Doe';
		$emp1->department = 'Sales';
		$emp1->tasks = [$task1, $task2];
		
		$emp2 = new Employee();
		$emp2->firstname = 'Jane';
		$emp2->lastname = 'Doe';
		$emp2->department = 'IT';
		$emp2->tasks = [$task1, $task3];
		
		$empId1 = $manager->save($emp1);
		$empId2 = $manager->save($emp2);
		
		$this->assertEquals(2, $manager->count());
		$this->assertEquals(3, $mapper->newManager('Acme\Storage\Task')->count());
		$query = $mapper->newQuery();
		$this->assertEquals(4, $query->from('emp_tasks')->select('Count(*)')->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId1)->fetch('i'));
		$this->assertEquals(2, $query->from('emp_tasks')->select('Count(*)')->where('emp_id = %{i}', $empId2)->fetch('i'));
		
		$mapper->close();
	}
}