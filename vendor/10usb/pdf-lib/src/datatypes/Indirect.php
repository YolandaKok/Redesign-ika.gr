<?php
namespace pdflib\datatypes;

class Indirect implements Referenceable {
	private $number;
	private $generation;
	private $object;
	private $hash;
	
	public function __construct($number, $generation, $object = null){
		$this->number		= $number;
		$this->generation	= $generation;
		$this->object		= $object;
		$this->hash			= md5($object ? $object->output() : ''); 
	}
	
	public function getNumber(){
		return $this->number;
	}
	
	public function getGeneration(){
		return $this->generation;
	}
	
	public function getObject(){
		return $this->object;
	}
	
	public function getBody(){
		return [$this->object->output()];
	}
	
	public function isModified(){
		return md5($this->object? $this->object->output() : '') != $this->hash;
	}
}