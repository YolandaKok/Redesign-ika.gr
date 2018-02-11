<?php
namespace pdflib;

use pdflib\xreferences\Table;
use pdflib\xreferences\FileIO;
use pdflib\structure\Information;
use pdflib\structure\Catalog;
use pdflib\structure\ResourceManager;

class File {
	/**
	 * Name of the file to be read from or written to
	 * @var string
	 */
	private $name;
	
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
	 *The reference table and subtables
	 * @var \pdflib\xreferences\Table
	 */
	private $xreference;
	
	/**
	 *
	 * @var \stdClass
	 */
	private $defaults;
	
	/**
	 *
	 * @var \stdClass
	 */
	private $resourceManager;
	
	/**
	 * 
	 * @param string $filename
	 */
	public function __construct($name = 'php://temp'){
		$this->name				= $name;
		$this->handle			= null;
		$this->offset			= 0;
		$this->xreference		= new Table();
		$this->xreference->addSection(0)->add(0, 65535, null);
		$this->defaults			= new \stdClass();
		$this->defaults->Fonts	= realpath(__DIR__.'/../data/defaults.json');
		$this->resourceManager	= null;
	}
	
	/**
	 * Opens the stream
	 */
	public function load(){
		$this->handle = new Handle($this->name);
		$this->handle->seek(0, true);
		$this->handle->setOffset($this->handle->tell())->setInitial($this->handle->getLineEnding());
		
		$this->handle->seek(-28, true);
		if(!preg_match('/startxref(?:\r\n|\n|\r)(\d+)(\r\n|\n|\r)%%EOF/', $this->handle->read(28), $matches)) throw new \Exception('Failed to load file');
		$this->handle->setLineEnding($matches[2]);
		
		$this->handle->seek($matches[1]);
		$this->xreference	= new Table();
		$this->xreference->setPrevious(Parser::readTable($this->handle));
	}
	
	/**
	 * Closes the stream
	 */
	public function close(){
		$this->flush(true);
		$this->handle		= null;
		$this->xreference	= new Table();
	}
	
	/**
	 * Flushed all uncommitted data to the stream
	 * @param boolean $finalize Should the trailer be made permanent
	 */
	public function flush($finalize = false){
		if(!$this->handle){
			$this->handle = new Handle($this->name, true);
			$this->handle->writeline('%PDF-1.4');
			$this->handle->setOffset($this->handle->tell());
		}
		$this->xreference->flush($this->handle);
		
		if($finalize){
			$this->handle->seek(0, true);
			$this->handle->setOffset($this->handle->tell());
		}
	}
	
	/**
	 * 
	 * @return resource
	 */
	public function getHandle(){
		return $this->handle->getHandle();
	}
	
	/**
	 * Returns the contents of the pdf file
	 * @return string
	 */
	public function getContents(){
		$this->flush();
		return $this->handle->getContents();
	}
	
	/**
	 * 
	 * @return \pdflib\structure\Information
	 */
	public function getInformation(){
		return new Information(new FileIO($this->xreference, $this->handle, $this->defaults));
	}
	
	/**
	 *
	 * @return \pdflib\structure\Catalog
	 */
	public function getCatalog(){
		$io = new FileIO($this->xreference, $this->handle, $this->defaults);
		
		if(!$this->resourceManager){
			$this->resourceManager = new ResourceManager($io);
		}
		
		return new Catalog($io, $this->resourceManager);
	}
}