<?php
namespace pdflib\structure;

use pdflib\datatypes\Referenceable;

class Image implements Referenceable {
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
	 * @param \pdflib\structure\ResourceManager $resourceManager
	 * @param \pdflib\datatypes\Referenceable $reference
	 * @param \pdflib\datatypes\Name $localName
	 * @param number $size
	 */
	public function __construct($resourceManager, $reference, $localName){
		$this->resourceManager	= $resourceManager;
		$this->reference		= $reference;
		$this->localName		= $localName;
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
	 * @return \pdflib\datatypes\Name
	 */
	public function getLocalName(){
		return $this->localName;
	}
}