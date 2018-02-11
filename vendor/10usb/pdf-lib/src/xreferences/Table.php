<?php
namespace pdflib\xreferences;

use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Reference;
use pdflib\datatypes\Indirect;
use pdflib\datatypes\Referenceable;
use pdflib\datatypes\Number;
use pdflib\datatypes\Stream;

class Table {
	
	/**
	 * 
	 * @var \pdflib\datatypes\Dictionary
	 */
	private $dictionary;
	
	/**
	 * 
	 * @var string
	 */
	private $hash;
	
	/**
	 *
	 * @var \pdflib\references\Section[]
	 */
	private $sections;
	
	/**
	 * 
	 * @var \pdflib\references\Table
	 */
	private $previous;
	
	/**
	 * 
	 */
	public function __construct(){
		$this->dictionary	= null;
		$this->hash			= null;
		$this->sections		= [];
		$this->previous		= null;
	}
	
	/**
	 * 
	 */
	public function finalize(){
		$this->hash = md5($this->getDictionary()->output());
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function isModified(){
		return count($this->sections) > 0 || md5($this->getDictionary()->output()) != $this->hash;
	}
	
	/**
	 * 
	 * @param integer $number
	 * @return \pdflib\xreferences\Section
	 */
	public function addSection($number){
		$section = new Section($number);
		$this->sections[] = $section;
		return $section;
	}
	
	/**
	 * 
	 * @param \pdflib\references\Table $previous
	 */
	public function setPrevious($previous){
		$this->previous = $previous;
	}
	
	/**
	 * 
	 * @return \pdflib\references\Table
	 */
	public function getPrevious(){
		return $this->previous;
	}
	
	/**
	 * 
	 * @return \pdflib\datatypes\Dictionary
	 */
	public function getDictionary(){
		if(!$this->dictionary){
			if($this->previous){
				$this->dictionary = clone $this->previous->getDictionary();
				$this->dictionary->set('Size', new Number(0));
			}else{
				$this->dictionary = new Dictionary();
				$this->dictionary->set('Size', new Number(0));
			}
			$this->finalize();
		}
		return $this->dictionary;
	}
	
	/**
	 *
	 * @return \pdflib\references\Table
	 */
	public function getSize(){
		$size = 0; 
		
		foreach($this->sections as $section){
			$size+= $section->getSize();
		}
		
		if($this->previous){
			$size+= $this->previous->getSize();
		}
		
		return $size;
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Indirect[] $modifications
	 * @return \pdflib\datatypes\Indirect[]
	 */
	public function getModifications(&$modifications = []){
		foreach($this->sections as $section){
			foreach($section->getEntries() as $entry){
				if($entry->isModified()) $modifications[] = $entry->getIndirect(null);
			}
		}
		if($this->previous){
			$this->previous->getModifications($modifications);
		}
		
		return $modifications;
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 */
	public function flush($handle){
		if($this->previous){
			$modifications = $this->previous->getModifications();
			
			foreach($modifications as $modification){
				$exists = false;
				foreach($this->sections as $section){
					if($section->contains($modification)){
						$exists = true;
					}
				}
				
				if(!$exists){
					$appended = false;
					foreach($this->sections as $section){
						if($section->canAppend($modification)){
							$section->add(0, $modification->getGeneration(), true, $modification);
							$appended = true;
						}
					}
					
					if(!$appended){
						$this->addSection($modification->getNumber())->add(0, $modification->getGeneration(), true, $modification);
					}
				}
				
			}
		}
		
		foreach($this->sections as $section){
			$section->flush($handle);
		}
		
		if($this->isModified()){
			
			$this->getDictionary()->set('Size', new Number($this->getSize()));
			
			$handle->seek($handle->getOffset());
			$startxref = $handle->tell();
			$handle->writeline('xref');
			foreach($this->sections as $section){
				$handle->writeline(sprintf('%d %d', $section->getNumber(), $section->getSize()));
				foreach($section->getEntries() as $entry){
					$handle->writeline(substr(
												sprintf('%010d %05d %s  ', $entry->getOffset(), $entry->getGeneration(), $entry->isUsed() ? 'n' : 'f'),
												0,
												20 - strlen($handle->getLineEnding())
											));
				}
			}
			$handle->writeline('trailer');
			$handle->writeline($this->getDictionary()->output());
			$handle->writeline('startxref');
			$handle->writeline($startxref);
			$handle->write('%%EOF');
		}
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Object $object
	 * @return \pdflib\datatypes\Reference
	 */
	public function allocate($object){
		if(!$this->sections) $this->addSection($this->getSize());
		$section = end($this->sections);
		
		$indirect = new Indirect($section->getNumber() + $section->getSize(), 0, $object);
		$this->sections[0]->add(0, $indirect->getGeneration(), true, $indirect);
		
		return new Reference($indirect->getNumber(), $indirect->getGeneration());
	}
	
	/**
	 * 
	 * @return \pdflib\datatypes\Reference
	 */
	public function allocateStream($object = null){
		if(!$this->sections) $this->addSection($this->getSize());
		$section = end($this->sections);
		
		if($object){
			$indirect = new Stream($section->getNumber() + $section->getSize(), 0, $object);
		}else{
			$indirect = new Stream($section->getNumber() + $section->getSize(), 0);
		}
		$this->sections[0]->add(0, $indirect->getGeneration(), true, $indirect);
		
		return new Reference($indirect->getNumber(), $indirect->getGeneration());
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @param \pdflib\datatypes\Referenceable $reference
	 * @return \pdflib\datatypes\Indirect|null
	 */
	public function getIndirect($handle, $reference){
		if(!$reference instanceof Referenceable) throw new \Exception('Unexpected value expected Reference');
		
		/** @var Section $section */
		foreach($this->sections as $section){
			if($section->contains($reference)){
				return $section->getIndirect($handle, $reference);
			}
		}
		
		if($this->previous){
			return $this->previous->getIndirect($handle, $reference);
		}
		return null;
	}
}