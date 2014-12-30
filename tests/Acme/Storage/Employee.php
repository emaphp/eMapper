<?php
namespace Acme\Storage;

/**
 * @Entity employees
 */
class Employee {
	/**
	 * @Id
	 * @Type int
	 * @Column employee_id
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $firstname;
	
	/**
	 * @Type string
	 */
	public $lastname;
	
	/**
	 * @Type string
	 */
	public $department;
	
	/**
	 * @ManyToMany Task
	 * @Join(emp_id, task_id) emp_tasks
	 */
	public $tasks;
}