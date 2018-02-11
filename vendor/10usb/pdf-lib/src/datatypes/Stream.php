<?php
namespace pdflib\datatypes;

class Stream extends Indirect {
	private $data;
	
	public function __construct($number, $generation, $object = null){
		parent::__construct($number, $generation, $object ? $object : new Dictionary());
	}
	
	public function append($data){
		$this->data.= $data;
		$this->getObject()->set('Length', new Number(strlen($this->data)));
	}
	
	public function getBody(){
		if(!$this->getObject()->get('Filter')){
			$contents = gzcompress($this->data);
			if(strlen($contents) < strlen($this->data)){
				$this->getObject()->set('Filter', new Name('FlateDecode'));
				$this->getObject()->set('Length', new Number(strlen($this->data)));
				$this->data = $contents;
			}
		}
		
		$lines = parent::getBody();
		$lines[] = 'stream';
		$lines[] = $this->data;
		$lines[] = 'endstream';
		return $lines;
	}
}