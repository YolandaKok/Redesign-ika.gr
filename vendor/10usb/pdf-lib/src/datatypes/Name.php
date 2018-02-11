<?php
namespace pdflib\datatypes;

class Name implements Object {
	private $value;
	
	public function __construct($value){
		$this->value = $value;
		if(strlen($this->output()) > 127) throw new \Exception('Name "'.$this->output().'" to long, max length 127');
	}
	
	public function output(){
		return '/'.preg_replace_callback('/\s|[\(\)\<\>\[\]\{\}\/\%\#]/', function($matches){
			return sprintf('#%02X', ord($matches[0]));
		}, $this->value);
	}
	
	public function __toString(){
		return $this->value;
	}
}