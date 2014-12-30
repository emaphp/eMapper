<?php
namespace Acme\Storage;

/**
 * @Entity tasks
 */
class Task {
	/**
	 * @Id
	 * @Type int
	 * @Column task_id
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Type timestamp
	 */
	public $startingDate;
	
	/**
	 * @Type boolean
	 */
	public $started;
	
	/**
	 * @ManyToMany Employee
	 * @Join(task_id, emp_id) emp_tasks
	 */
	public $employees;
}