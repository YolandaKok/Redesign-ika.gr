<?php
namespace pdflib\datatypes;

class Text implements Object {
	private $value;
	
	public function __construct($value){
		$this->value = $value;
	}
	
	public function output(){
		if(preg_match('/[^!-~ ]|\n|\r|\t|\(|\)|\\\\/', $this->value)){
			return '<'.end(unpack('H*', $this->value)).'>';
		}
		
		return '('.str_replace(["\\", "\n", "\r", "\t", "(", ")"], ['\\\\', '\n', '\r', '\t', '\(', '\)'], $this->value).')'; 
	}
	
	public function __toString(){
		return $this->value;
	}
}