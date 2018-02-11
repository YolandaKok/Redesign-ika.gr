<?php
namespace pdflib\structure;

use pdflib\datatypes\Referenceable;

class Font implements Referenceable {
	/**
	 *
	 * @var \pdflib\structure\ResourceManager
	 */
	private $resourceManager;
	
	/**
	 *
	 * @var \pdflib\datatypes\Referenceable
	 */
	private $reference;
	
	/**
	 * 
	 * @var \pdflib\datatypes\Name
	 */
	private $localName;
	
	/**
	 * 
	 * @var number
	 */
	private $size;
	
	/**
	 * 
	 * @param \pdflib\structure\ResourceManager $resourceManager
	 * @param \pdflib\datatypes\Referenceable $reference
	 * @param \pdflib\datatypes\Name $localName
	 * @param number $size
	 */
	public function __construct($resourceManager, $reference, $localName, $size){
		$this->resourceManager	= $resourceManager;
		$this->reference		= $reference;
		$this->localName		= $localName;
		$this->size				= $size;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Referenceable::getNumber()
	 */
	public function getNumber(){
		return $this->reference->getNumber();
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Referenceable::getGeneration()
	 */
	public function getGeneration(){
		return $this->reference->getGeneration();
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getName(){
		return $this->resourceManager->getFontName($this->reference);
	}
	
	/**
	 * 
	 * @return \pdflib\datatypes\Name
	 */
	public function getLocalName(){
		if(!$this->localName) throw new \Exception('Unrenderable font'); 
		return $this->localName;
	}
	
	/**
	 * 
	 * @return number
	 */
	public function getSize(){
		return $this->size;
	}
	
	/**
	 * 
	 * @param string $text
	 * @return number
	 */
	public function getTextWith($text){
		return $this->resourceManager->getFontTextWidth($this->reference, $text) * $this->size;
	}
}