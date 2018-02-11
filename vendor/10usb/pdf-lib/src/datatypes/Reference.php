<?php
namespace pdflib\datatypes;

class Reference implements Object, Referenceable {
	private $number;
	private $generation;
	
	public function __construct($number, $generation){
		$this->number		= $number;
		$this->generation	= $generation;
	}
	
	public function output(){
		return $this->number.' '.$this->generation.' R';
	}
	
	public function getNumber(){
		return $this->number;
	}
	
	public function getGeneration(){
		return $this->generation;
	}
	
	public static function get($referencaable){
		if(!$referencaable instanceof Reference) return $referencaable;
		if(!$referencaable instanceof Referenceable) throw new \Exception('Instance if not type Referenceable');
		return new Reference($referencaable->getNumber(), $referencaable->getGeneration());
	}
}