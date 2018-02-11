<?php
namespace pdflib\xreferences;

use pdflib\Parser;

class Entry {
	/**
	 * The offset where the indirect object can be found
	 * @var integer
	 */
	private $offset;
	
	/**
	 * The number where the next free entry can be found
	 * @var integer
	 */
	private $generation;
	
	/**
	 * If this entry is free or used
	 * @var boolean
	 */
	private $used;
	
	/**
	 * The reference object of this entry when loaded in memory otherwise null
	 * @var \pdflib\datatypes\Indirect|null
	 */
	private $indirect;
	
	/**
	 * 
	 * @param integer $offset
	 * @param integer $generation
	 * @param boolean $used
	 * @param \pdflib\datatypes\Indirect|null $reference
	 */
	public function __construct($offset, $generation, $used, $indirect){
		$this->offset		= $offset;
		$this->generation	= $generation;
		$this->used			= $used;
		$this->indirect		= $indirect;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function getOffset(){
		return $this->offset;
	}
	
	/**
	 * 
	 * @return integer
	 */
	public function getGeneration(){
		return $this->generation;
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function isUsed(){
		return $this->used;
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @return \pdflib\datatypes\Indirect|null
	 */
	public function getIndirect($handle){
		if(!$this->indirect && $this->used){
			if($this->offset <= 0) throw new \Exception('Something internal went wrong, blame the programmer!');
			$handle->seek($this->offset);
			$this->indirect = Parser::readIndirect($handle);
		}
		return $this->indirect;
	}
	
	public function isModified(){
		if(!$this->indirect) return false;
		return $this->indirect->isModified();
	}
	
	/**
	 * Writes the object data to the stream and moves the offset to the end of 
	 * @param \pdflib\Handle $handle
	 */
	public function flush($handle){
		if(!$this->indirect) return false;
		if(!($this->offset <= 0 || $this->indirect->isModified())) return false;
		
		
		$handle->seek($handle->getOffset());
		
		$this->offset = $handle->tell();
		$handle->writeline(sprintf('%d %d obj', $this->indirect->getNumber(), $this->indirect->getGeneration()));
		foreach($this->indirect->getBody() as $line) $handle->writeline($line);
		$handle->writeline('endobj');
		$handle->writeline('');
		
		$handle->setOffset($handle->tell());
		
		// Clean up memory on flush
		ob_start();
		debug_zval_dump($this->indirect);
		$contents = ob_get_contents();
		ob_end_clean();
		if(preg_match('/(.+?)\((.+?)\)\#(\d+)\s*\((\d+)\)\s*refcount\((\d+)\)/i', $contents, $matches)){
			if($matches[5] <= 2) $this->indirect = null;
		}
	}
}