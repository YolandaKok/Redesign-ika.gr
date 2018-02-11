<?php
namespace pdflib\structure;

use pdflib\datatypes\Referenceable;
use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Name;
use pdflib\datatypes\Reference;
use pdflib\datatypes\Collection;
use pdflib\datatypes\Number;

class Page implements Referenceable {
	/**
	 *
	 * @var \pdflib\xreferences\FileIO
	 */
	private $io;
	/**
	 *
	 * @var \pdflib\datatypes\Indirect
	 */
	private $indirect;
	
	/**
	 *
	 * @var \pdflib\structure\ResourceManager
	 */
	private $resourceManager;
	
	/**
	 * 
	 * @param \pdflib\xreferences\FileIO $io
	 * @param \pdflib\datatypes\Indirect $data
	 * @param \pdflib\structure\ResourceManager $resourceManager
	 */
	public function __construct($io, $indirect, $resourceManager){
		$this->io				= $io;
		$this->indirect			= $indirect;
		$this->resourceManager	= $resourceManager;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Referenceable::getNumber()
	 */
	public function getNumber(){
		return $this->indirect->getNumber();
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \pdflib\datatypes\Referenceable::getGeneration()
	 */
	public function getGeneration(){
		return $this->indirect->getGeneration();
	}
	
	/**
	 * 
	 * @param unknown $width
	 * @param unknown $height
	 * @return \pdflib\structure\Page
	 */
	public function setSize($width = null, $height = null){
		$box = new Collection();
		$box->push(new Number(0));
		$box->push(new Number(0));
		$box->push(new Number($width === null ? $this->getWidth() : $width));
		$box->push(new Number($height === null? $this->getHeight() : $height));
		$this->indirect->getObject()->set('MediaBox', $box);
		return $this;
	}
	
	/**
	 * 
	 * @return number
	 */
	public function getWidth(){
		$box = $this->indirect->getObject()->get('MediaBox');
		return $box->get(2)->getValue();
	}
	
	/**
	 * 
	 * @return number
	 */
	public function getHeight(){
		$box = $this->indirect->getObject()->get('MediaBox');
		return $box->get(3)->getValue();
	}
	
	/**
	 * TODO make it compatible with contents being an array
	 * @return \pdflib\structure\Canvas
	 */
	public function getCanvas(){
		$reference = $this->indirect->getObject()->get('Contents');
		if(!$reference){
			$reference = $this->io->allocateStream();
			$this->indirect->getObject()->set('Contents', $reference);
		}
		
		$stream = $this->io->getIndirect($reference);
		
		return new Canvas($this->getWidth(), $this->getHeight(), $stream);
	}
	
	/**
	 *
	 * @param string $name
	 * @param number $size
	 * @return \pdflib\structure\Font
	 */
	public function getFont($name, $size){
		$resources = $this->indirect->getObject()->get('Resources');
		// TODO if no Resource check parents, otherwise throw exception
		if(!$dictionary = $resources->get('Font')){
			$resources->set('Font', $dictionary= new Dictionary());
		}
		
		$reference = $this->resourceManager->getFont($name);
		$localName = $this->resourceManager->getFontLocalName($dictionary, $reference);
		
		return new Font($this->resourceManager, $reference, $localName, $size);
	}
	
	/**
	 *
	 * @param string $name
	 * @param number $size
	 * @return \pdflib\structure\Image
	 */
	public function getImage($path){
		$resources = $this->indirect->getObject()->get('Resources');
		// TODO if no Resource check parents, otherwise throw exception
		if(!$dictionary = $resources->get('XObject')){
			$resources->set('XObject', $dictionary= new Dictionary());
		}
		
		$reference = $this->resourceManager->getImage($path);
		$localName = $this->resourceManager->getImageLocalName($dictionary, $reference);
		
		return new Image($this->resourceManager, $reference, $localName);
	}
}