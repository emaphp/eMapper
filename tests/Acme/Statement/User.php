<?php
namespace Acme\Statement;

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
	 * @Column user_name
	 */
	public $name;
}