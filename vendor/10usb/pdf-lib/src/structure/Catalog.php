<?php
namespace pdflib\structure;

use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Name;
use pdflib\datatypes\Collection;
use pdflib\datatypes\Number;

class Catalog {
	/**
	 *
	 * @var \pdflib\xreferences\FileIO
	 */
	private $io;
	
	/**
	 * 
	 * @var \pdflib\structure\ResourceManager
	 */
	private $resourceManager;
	
	/**
	 * 
	 * @param \pdflib\xreferences\FileIO $io
	 * @param \pdflib\structure\ResourceManager $resourceManager
	 */
	public function __construct($io, $resourceManager){
		$this->io				= $io;
		$this->resourceManager	= $resourceManager;
	}
	
	/**
	 * 
	 * @param number $width
	 * @param number $height
	 * @return \pdflib\structure\Catalog
	 */
	public function setSize($width, $height){
		$box = new Collection();
		$box->push(new Number(0));
		$box->push(new Number(0));
		$box->push(new Number($width));
		$box->push(new Number($height));
		$this->io->setDefault('MediaBox', $box);
		return $this;
	}
	
	/**
	 * 
	 * @param number $width
	 * @param number $height
	 * @return boolean
	 */
	public function getSize(&$width, &$height){
		/** @var \pdflib\datatypes\Collection $default */
		if(!($default = $this->io->getDefault('MediaBox'))){
			$width	= false;
			$height	= false;
			return false;
		}
		$width	= $default->get(2);
		$height	= $default->get(3);
		return true;
	}
	
	/**
	 * 
	 * @param \pdflib\structure\Page $before
	 * @return \pdflib\structure\Page
	 */
	public function addPage($before = null){
		if(!$this->io->getDefault('MediaBox')) throw new \Exception('No default size set for new pages');
		
		$root = $this->getRoot();
		
		$reference = $root->get('Pages');
		if(!$reference){
			$object = new Dictionary();
			$object->set('Type', new Name('Pages'));
			$object->set('Kids', new Collection());
			$object->set('Count', new Number(0));
			$reference = $this->io->allocate($object);
			$root->set('Pages', $reference);
		}
		$pages = $this->io->getIndirect($reference)->getObject();
		
		$object = new Dictionary();
		$object->set('Type', new Name('Page'));
		$object->set('Parent', $reference);
		$object->set('Resources', $resouces = new Dictionary());
		$resouces->set('ProcSet', $procSet = new Collection());
		$procSet->push(new Name('PDF'));
		$procSet->push(new Name('Text'));
		$procSet->push(new Name('ImageB'));
		$procSet->push(new Name('ImageC'));
		$procSet->push(new Name('ImageI'));
		$object->set('MediaBox', clone $this->io->getDefault('MediaBox'));
		
		$reference = $this->io->allocate($object);
		$pages->get('Kids')->push($reference);
		
		$pages->set('Count', new Number($pages->get('Count')->getValue() + 1));
		
		return new Page($this->io, $this->io->getIndirect($reference), $this->resourceManager);
	}
	
	/**
	 * 
	 * @param integer $index
	 * @return \pdflib\structure\Page|boolean
	 */
	public function getPage($index){
		return false;
	}
	
	/**
	 * Returns a font for calculation purposes
	 * @param string $name
	 * @param number $size
	 * @return \pdflib\structure\Font
	 */
	public function getFont($name, $size){
		$reference= $this->resourceManager->getFont($name);
		return new Font($this->resourceManager, $reference, null, $size);
	}
	
	/**
	 * 
	 * @return \pdflib\datatypes\Dictionary
	 */
	private function getRoot(){
		$reference = $this->io->getValue('Root');
		
		if(!$reference){
			$object = new Dictionary();
			$object->set('Type', new Name('Catalog'));
			$reference = $this->io->allocate($object);
			$this->io->setValue('Root', $reference);
		}
		
		return $this->io->getIndirect($reference)->getObject();
	}
}