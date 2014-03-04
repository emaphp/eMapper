<?php
namespace Acme\Generic;

use Acme\RGBColor;

class GenericProduct {
	protected $id;
	protected $description;
	protected $code;
	protected $price;
	protected $category;
	protected $color;
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function getPrice() {
		return $this->price;
	}
	
	public function setPrice($price) {
		$this->price = $price;
	}
	
	public function getCategory() {
		return $this->category;
	}
	
	public function setCategory($category) {
		$this->category = $category;
	}
	
	public function getColor() {
		return $this->color;
	}
	
	public function setColor($color) {
		$this->color = $color;
	}
}
?>