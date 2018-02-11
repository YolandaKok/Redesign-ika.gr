<?php
namespace pdflib\structure;

use pdflib\datatypes\Name;
use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Reference;
use pdflib\datatypes\Referenceable;
use pdflib\datatypes\Number;

class ResourceManager {
	/**
	 *
	 * @var \pdflib\xreferences\FileIO
	 */
	private $io;
	
	/**
	 *
	 * @var array
	 */
	private $fonts;
	
	/**
	 *
	 * @var array
	 */
	private $images;
	
	/**
	 * 
	 * @param \pdflib\xreferences\FileIO $io
	 */
	public function __construct($io){
		$this->io		= $io;
		$this->fonts	= [];
		$this->images	= [];
		
		$reference = $this->io->getValue('Root');
		if($reference){
			$root = $this->io->getIndirect($reference)->getObject();
			$reference = $root->get('Pages');
			if($reference){
				$pages = $this->io->getIndirect($reference)->getObject();
				if($pages->get('Resources')) $this->extract($pages->get('Resources'));
				$this->search($pages);
			}
		}
	}
	
	/**
	 * 
	 * @param string $name
	 */
	public function getFont($name){
		foreach($this->fonts as $font){
			if($font->name == $name){
				return $font->reference;
			}
		}
		
		$fonts = json_decode(file_get_contents($this->io->getDefault('Fonts')));
		foreach($fonts as $font){
			if($font->name==$name){
				$object = new Dictionary();
				$object->set('Type', new Name('Font'));
				$object->set('BaseFont', new Name($name));
				$object->set('Subtype', new Name('Type1'));
				$object->set('Encoding', new Name('WinAnsiEncoding'));
				$reference = $this->io->allocate($object);
				
				$font->localNames	= [];
				$font->reference	= $reference;
				$this->fonts[] 		= $font;
				return $reference;
			}
		}
		throw new \Exception('Font "'.$name.'" not in repository');
	}
	
	/**
	 * 
	 * @param  \pdflib\datatypes\Dictionary $dictionary
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @return \pdflib\datatypes\Name
	 */
	public function getFontLocalName($dictionary, $reference){
		// Check if the dictionary already contains the reference
		foreach($dictionary as $localName=>$value){
			if($value->getNumber() == $reference->getNumber() && $value->getGeneration() == $reference->getGeneration()){
				return $localName;
			}
		}
		
		// Get cached font
		$font = $this->getFontByReference($reference);
		if(!$font) throw new \Exception('Failed to load font');
		
		// Lets see if the already used names can be used
		foreach($font->localNames as $localName){
			if(!$dictionary->get($localName)){
				$dictionary->set($localName, $reference);
				return $localName;
			}
		}
		
		// if all else fails generate new name
		$index = count($this->fonts);
		do {
			if(!$dictionary->get('F'.$index)){
				$localName = new Name('F'.$index);
				$dictionary->set($localName, $reference);
				$font->localNames[] = $localName;
				return $localName;
			}
		}while($index++ < 100);
		
		throw new \Exception('Failed to generate new font name');
	}
	
	/**
	 *
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @param string $text
	 * @return number
	 */
	public function getFontTextWidth($reference, $text){
		$font = $this->getFontByReference($reference);
		
		$width = 0;
		for($index = 0; isset($text[$index]); $index++){
			$charValue = ord($text[$index]);
			$width+= $font->widths[$charValue];
		}
		
		return $width / 1000;
	}
	
	/**
	 *
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @return string
	 */
	public function getFontName($reference){
		$font = $this->getFontByReference($reference);
		return $font->name;
	}
	
	/**
	 *
	 * @param string $path
	 */
	public function getImage($path){
		if(!file_exists(realpath($path))) throw new \Exception(sprinf('File "%s" not found'));
		$path = realpath($path);
		
		foreach($this->images as $image){
			if($image->path == $path){
				return $image->reference;
			}
		}
		
		
		$info = getimagesize($path);
		
		$object = new Dictionary();
		$object->set('Type', new Name('XObject'));
		$object->set('Subtype', new Name('Image'));
		$object->set('Width', new Number($info[0]));
		$object->set('Height', new Number($info[1]));
		
		switch($info['channels']){
			case 3: $object->set('ColorSpace', new Name('DeviceRGB')); break;
			case 4: $object->set('ColorSpace', new Name('DeviceCMYK')); break;
			case 1: $object->set('ColorSpace', new Name('DeviceGray')); break;
			default: $object->set('ColorSpace', new Name('DeviceRGB')); break;
		}
		$object->set('BitsPerComponent', new Number(isset($info['bits']) ? $info['bits'] : 8));
		$object->set('Filter', new Name('DCTDecode'));
		$reference = $this->io->allocateStream($object);
		
		$stream = $this->io->getIndirect($reference);
		
		switch($info[2]){
			case IMAGETYPE_JPEG:
				$handle = imagecreatefromjpeg($path);
				ob_start();
				imagejpeg($handle);
				$contents = ob_get_contents();
				ob_end_clean();
				
				
				if($contents > filesize($path)){
					$stream->append(file_get_contents($path));
				}else{
					$stream->append($contents);
				}
				imagedestroy($handle);
			break;
			case IMAGETYPE_PNG:
				$handle = imagecreatefrompng($path);
				ob_start();
				imagejpeg($handle);
				$stream->append(ob_get_contents());
				ob_end_clean();
				
				$mask = new Dictionary();
				$mask->set('Type', new Name('XObject'));
				$mask->set('Subtype', new Name('Image'));
				$mask->set('Width', new Number($info[0]));
				$mask->set('Height', new Number($info[1]));
				$mask->set('ColorSpace', new Name('DeviceGray'));
				$mask->set('BitsPerComponent', new Number(8));
				$maskReference = $this->io->allocateStream($mask);
				$object->set('SMask', $maskReference);
				
				$stream = $this->io->getIndirect($maskReference);
				for($y = 0; $y < $info[1]; $y++){
					for($x = 0; $x < $info[0]; $x++){
						$pixel = imagecolorat($handle, $x, $y);
						$value = round((127 - (($pixel >> 24) & 0xFF)) / 127 * 255);
						$stream->append(pack('C', $value));
					}
				}
				imagedestroy($handle);
			break;
			default: throw new Exception('Unknown image format');
		}
		
		$image = new \stdClass();
		$image->path		= $path;
		$image->localNames	= [];
		$image->reference	= $reference;
		$this->images[] 		= $image;
		return $reference;
	}

	/**
	 *
	 * @param  \pdflib\datatypes\Dictionary $dictionary
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @return \pdflib\datatypes\Name
	 */
	public function getImageLocalName($dictionary, $reference){
		// Check if the dictionary already contains the reference
		foreach($dictionary as $localName=>$value){
			if($value->getNumber() == $reference->getNumber() && $value->getGeneration() == $reference->getGeneration()){
				return $localName;
			}
		}
		
		// Get cached font
		$image = $this->getImageByReference($reference);
		if(!$image) throw new \Exception('Failed to load image');
		
		// Lets see if the already used names can be used
		foreach($image->localNames as $localName){
			if(!$dictionary->get($localName)){
				$dictionary->set($localName, $reference);
				return $localName;
			}
		}
		
		// if all else fails generate new name
		$index = count($this->images);
		do {
			if(!$dictionary->get('I'.$index)){
				$localName = new Name('I'.$index);
				$dictionary->set($localName, $reference);
				$image->localNames[] = $localName;
				return $localName;
			}
		}while($index++ < 100);
		
		throw new \Exception('Failed to generate new font name');
	}
	
	/**
	 *
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @return \stdClass|boolean
	 */
	private function getFontByReference($reference){
		foreach($this->fonts as $font){
			if($font->reference->getNumber() == $reference->getNumber() && $font->reference->getGeneration() == $reference->getGeneration()){
				return $font;
			}
		}
		return false;
	}
	
	/**
	 *
	 * @param  \pdflib\datatypes\Referenceable $reference
	 * @return \stdClass|boolean
	 */
	private function getImageByReference($reference){
		foreach($this->images as $font){
			if($font->reference->getNumber() == $reference->getNumber() && $font->reference->getGeneration() == $reference->getGeneration()){
				return $font;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Dictionary $branch
	 */
	private function search($branch){
		foreach($branch->get('Kids') as $reference){
			$child = $this->io->getIndirect($reference)->getObject();
			if($child->get('Resources')) $this->extract($child->get('Resources'));
			if($child->get('Kids')) $this->search($child);
		}
	}
	
	/**
	 * 
	 * @param \pdflib\datatypes\Dictionary|\pdflib\datatypes\Referenceable $resources
	 */
	private function extract($resources){
		if($resources instanceof Referenceable){
			$this->extract($this->io->getIndirect($resources)->getObject());
		}else{
			$fonts = $resources->get('Font');
			if($fonts){
				foreach ($fonts as $localName=>$reference){
					$font = $this->getFontByReference($reference);
					if(!$font){
						$descriptor = $this->io->getIndirect($reference)->getObject();
						
						$font = new \stdClass();
						$font->name			= "{$descriptor->get('BaseFont')}";
						$font->localNames	= [];
						$font->reference	= $reference;
						$this->fonts[] = $font;
					}
					
					$hasName = false;
					foreach($font->localNames as $name){
						if($name == "$localName"){
							$hasName = true;
							break;
						}
					}
					
					if(!$hasName){
						$font->localNames[] = $localName;
					}
				}
			}
		}
	}
}