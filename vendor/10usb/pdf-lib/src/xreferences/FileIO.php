<?php
namespace pdflib\xreferences;

use pdflib\datatypes\Referenceable;

class FileIO {
	/**
	 *
	 * @var \pdflib\xreferences\Table
	 */
	private $table;
	
	/**
	 *
	 * @var \pdflib\Handle
	 */
	private $handle;
	
	/**
	 *
	 * @var \stdClass
	 */
	private $defaults;
	
	/**
	 *
	 * @param \pdflib\xreferences\Table $table
	 * @param \pdflib\Handle $handle
	 */
	public function __construct($table, $handle, $defaults){
		$this->table	= $table;
		$this->handle	= $handle;
		$this->defaults	= $defaults;
	}
	
	/**
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getDefault($name){
		return $this->defaults->{$name};
	}
	
	/**
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return \pdflib\structure\FileIO
	 */
	public function setDefault($name, $value){
		$this->defaults->{$name} = $value;
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return \pdflib\datatypes\Object|boolean
	 */
	public function getValue($name){
		return $this->table->getDictionary()->get($name);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param \pdflib\datatypes\Object $value
	 * @return \pdflib\structure\FileIO
	 */
	public function setValue($name, $value){
		$this->table->getDictionary()->set($name, $value);
		return $this;
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Referenceable $reference
	 * @return \pdflib\datatypes\Indirect|null
	 */
	public function getIndirect($reference){
		if(!$reference instanceof Referenceable) throw new \Exception('Unexpected value expected a Referenceable');
		return $this->table->getIndirect($this->handle, $reference);
	}
	
	/**
	 *
	 * @param \pdflib\datatypes\Object $object
	 * @return \pdflib\datatypes\Reference
	 */
	public function allocate($object){
		return $this->table->allocate($object);
	}
	
	/**
	 *
	 * @return \pdflib\datatypes\Reference
	 */
	public function allocateStream($object = null){
		return $this->table->allocateStream($object);
	}
}