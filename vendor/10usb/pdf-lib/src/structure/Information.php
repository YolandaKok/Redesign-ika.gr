<?php
namespace pdflib\structure;

use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Text;

class Information {
	/**
	 * 
	 * @var \pdflib\xreferences\FileIO
	 */
	private $io;
	
	/**
	 * 
	 * @param \pdflib\xreferences\FileIO $io
	 */
	public function __construct($io){
		$this->io	= $io;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getTitle(){
		return $this->getValue('Title');
	}
	
	/**
	 * 
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setTitle($value){
		$this->setValue('Title', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getAuthor(){
		return $this->getValue('Author');
	}
	
	/**
	 *
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setAuthor($value){
		$this->setValue('Author', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getSubject(){
		return $this->getValue('Subject');
	}
	
	/**
	 *
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setSubject($value){
		$this->setValue('Subject', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getKeywords(){
		return $this->getValue('Keywords');
	}
	
	/**
	 *
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setKeywords($value){
		$this->setValue('Keywords', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getCreator(){
		return $this->getValue('Creator');
	}
	
	/**
	 *
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setCreator($value){
		$this->setValue('Creator', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getProducer(){
		return $this->getValue('Producer');
	}
	
	/**
	 *
	 * @param string $value
	 * @return \pdflib\structure\Information
	 */
	public function setProducer($value){
		$this->setValue('Producer', $this->isEmpty($value) ? false: new Text($value));
		return $this;
	}
	
	/**
	 * 
	 * @return \DateTime
	 */
	public function getCreated(){
		return '';
	}
	
	/**
	 *
	 * @param \DateTime $value
	 * @return \pdflib\structure\Information
	 */
	public function setCreated($value){
		$this->setValue('CreationDate', $this->isEmpty($value) ? false: new Text('D:'.substr($value->format('YmdHisO'), 0, -2)."'".substr($value->format('O'), -2)."'"));
		return $this;
	}
	
	/**
	 * 
	 * @return \DateTime
	 */
	public function getModDate(){
		return '';
	}
	
	/**
	 *
	 * @param \DateTime $value
	 */
	public function setModified($value){
		$this->setValue('ModDate', $this->isEmpty($value) ? false: new Text('D:'.substr($value->format('YmdHisO'), 0, -2)."'".substr($value->format('O'), -2)."'"));
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return string|boolean
	 */
	private function getValue($name){
		$reference = $this->table->getDictionary()->get('Info');
		
		if(!$reference) return false;
		
		$indirect = $this->table->getIndirect($this->handle, $reference);
		return $indirect->getObject()->get($name, $value);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param string $value
	 */
	private function setValue($name, $value){
		$reference = $this->io->getValue('Info');
		
		if(!$reference){
			$reference = $this->io->allocate(new Dictionary());
			$this->io->setValue('Info', $reference);
		}
		
		$indirect = $this->io->getIndirect($reference);
		if($value === false){
			$indirect->getObject()->remove($name);
		}else{
			$indirect->getObject()->set($name, $value);
		} 
	}
	
	/**
	 * 
	 * @param mixed $value
	 * @return boolean
	 */
	private function isEmpty($value){
		return $value === null || $value === '' || $value === false;
		
	}
}