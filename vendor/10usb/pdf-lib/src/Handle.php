<?php
namespace pdflib;

class Handle {
	/**
	 *The handle of the file when an active stream is open
	 * @var resource
	 */
	private $handle;
	
	/**
	 * The offset in the file from where data is still mutable
	 * @var number
	 */
	private $offset;
	
	/**
	 * 
	 * @var string|null
	 */
	private $initial;
	
	/**
	 * 
	 * @var unknown
	 */
	private $lineEnding;
	
	/**
	 *
	 * @param string $filename
	 */
	public function __construct($name, $overwrite = false){
		$this->handle		= fopen($name, $overwrite ? 'w+' : 'r+');
		if(!$this->handle) throw new \Exception('Failed to open "'.$name.'" for '.($overwrite ? 'w+' : 'r+'));
		$this->offset		= 0;
		$this->initial		= null;
		$this->lineEnding	= "\n";
	}
	
	/**
	 * 
	 */
	public function __destruct(){
		fclose($this->handle);
	}
	
	/**
	 *
	 * @return resource
	 */
	public function getHandle(){
		return $this->handle;
	}
	
	/**
	 *
	 *
	 * @return number
	 */
	public function getOffset(){
		return $this->offset;
	}
	
	/**
	 *
	 * @param unknown $offset
	 * @return \pdflib\Handle
	 */
	public function setOffset($offset){
		$this->offset = $offset;
		return $this;
	}
	
	/**
	 * 
	 * @param string $data
	 * @return \pdflib\Handle
	 */
	public function setInitial($data){
		$this->initial = $data;
		return $this;
	}
	
	/**
	 *
	 *
	 * @return number
	 */
	public function getLineEnding(){
		return $this->lineEnding;
	}
	
	/**
	 *
	 * @param unknown $offset
	 * @return \pdflib\Handle
	 */
	public function setLineEnding($lineEnding){
		$this->lineEnding = $lineEnding;
		return $this;
	}
	/**
	 * Moves the current position in the stream
	 * @param number $offset
	 * @param boolean $end
	 */
	public function seek($offset, $end = false){
		fseek($this->handle, $offset, $end ? SEEK_END : SEEK_SET);
	}
	
	/**
	 * Returns the current position in the stream
	 * @return number
	 */
	public function tell(){
		return ftell($this->handle);
	}
	
	/**
	 * Reads the amount in length from the stream
	 * @param number $length
	 * @return string
	 */
	public function read($length){
		return fread($this->handle, $length);
	}
	
	/**
	 * Reads a line form the stream
	 * @return string
	 */
	public function readline(){
		if(($line = fgets($this->handle))==false) return false;
		if(!preg_match('/^(.*?)(?:\r\n|\n|\r)?$/', $line, $matches)) throw new \Exception('Failed readline');
		return $matches[1]; ;
	}
	
	/**
	 * Writes the data to stream
	 * @param string $data
	 */
	public function write($data){
		if($this->initial !== null && ftell($this->handle) == $this->offset){
			fwrite($this->handle, $this->initial);
			$this->initial = null;
		}
		fwrite($this->handle, $data);
	}
	
	/**
	 * Writes a line to the stream with the current line-ending
	 * @param string $data
	 */
	public function writeline($data){
		$this->write($data.$this->lineEnding);
	}
	
	/**
	 * Returns the contents of the pdf file
	 * @return string
	 */
	public function getContents(){
		return stream_get_contents($this->handle, -1, 0);
	}
	
}