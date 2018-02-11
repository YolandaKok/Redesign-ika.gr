<?php
namespace pdflib\xreferences;

use pdflib\datatypes\Reference;
use pdflib\datatypes\Referenceable;

class Section {
	/**
	 * 
	 * @var integer
	 */
	private $number;
	
	/**
	 * 
	 * @var \pdflib\xreferences\Entry[]
	 */
	private $entries;
	
	/**
	 * 
	 * @param integer $number
	 */
	public function __construct($number){
		$this->number = $number;
		$this->entries = [];
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function getNumber(){
		return $this->number;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function getSize(){
		return count($this->entries);
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Referenceable $reference
	 */
	public function contains($reference){
		if(!$reference instanceof Referenceable) throw new \Exception('Unexpected value expected Referenceable');
		
		return $reference->getNumber() >= $this->number && $reference->getNumber() < ($this->number + count($this->entries));
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @param \pdflib\datatypes\Referenceable $reference
	 * @return NULL|\pdflib\datatypes\Indirect|null
	 */
	public function getIndirect($handle, $reference){
		if(!$this->contains($reference)) return null;
		
		return $this->entries[$reference->getNumber() - $this->number]->getIndirect($handle);
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Referenceable $referenable
	 * @return boolean
	 */
	public function canAppend($referenable){
		return $referenable->getNumber() == ($this->number + count($this->entries));
	}
	
	/**
	 * 
	 * @param integer $offset
	 * @param integer $generation
	 * @param boolean $used
	 * @param  \pdflib\datatypes\Indirect|null $indirect
	 * @return \pdflib\xreferences\Entry
	 */
	public function add($offset, $generation, $used, $indirect = null){
		$entry = new Entry($offset, $generation, $used, $indirect);
		$this->entries[] = $entry;
		return $entry;
	}
	
	/**
	 * 
	 * @return \pdflib\xreferences\Entry[]
	 */
	public function getEntries(){
		return $this->entries;
	}
	
	/**
	 *
	 * @param \pdflib\Handle $handle
	 */
	public function flush($handle){
		foreach($this->entries as $index=>$entry){
			$entry->flush($handle,$this->number + $index);
		}
	}
}