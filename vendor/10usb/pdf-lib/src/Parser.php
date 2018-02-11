<?php
namespace pdflib;

use pdflib\xreferences\Table;
use pdflib\datatypes\Dictionary;
use pdflib\datatypes\Name;
use pdflib\datatypes\Reference;
use pdflib\datatypes\Indirect;
use pdflib\datatypes\Text;
use pdflib\datatypes\Number;
use pdflib\datatypes\Collection;
use pdflib\datatypes\Nil;

class Parser {
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @return \pdflib\xreferences\Table
	 */
	public static function readTable($handle){
		if($handle->readline() != 'xref') throw new \Exception('Not an xref table');
		$table = new Table();
		
		while(($line = $handle->readline())!==false){
			if(preg_match('/^(\d+)\s(\d+)$/', $line, $matches)){
				$section	= $table->addSection($matches[1]);
				$length		= (int)$matches[2];
				
				for($index = 0; $index < $length; $index++){
					if(($line = $handle->readline())===false) throw new \Exception('Unexpected end of xref');
					if(!preg_match('/^(\d{10})\s(\d{5})\s(f|n)\s*$/', $line, $matches)) throw new \Exception('Unexpected "'.$line.'" expected a xref section entry');
					
					$section->add((int)ltrim($matches[1]), (int)ltrim($matches[2]), $matches[3]=='n');
				}
			}else if($line=='trailer'){
				$dictionary = self::readObject($handle, $handle->readline());
				if(!$dictionary instanceof Dictionary) throw new \Exception('Unexpected object expected Dictionary');
				
				foreach($dictionary as $key=>$value){
					$table->getDictionary()->set($key, $value);
				}
				
				break;
			}else{
				throw new \Exception('Unexpected "'.$line.'" expected a xref line');
			}
		}
		
		$table->finalize();
		return $table;
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @return \pdflib\datatypes\Indirect
	 */
	public static function readIndirect($handle){
		if(!preg_match('/(\d+) (\d+) obj/', $handle->readline(), $matches)) throw new \Exception('Failed to load indirect object');
		$object = self::readObject($handle, $handle->readline());
		
		if($object instanceof Dictionary && $object->get('Length')!==false){
			if($handle->readline() != 'stream') throw new \Exception('Not an stream');
			$indirect = new Stream($matches[1], $matches[2], $object);
			die(':P');
		}else{
			$indirect = new Indirect($matches[1], $matches[2], $object);
		}
		
		if($handle->readline() != 'endobj') throw new \Exception('Not an endobj');
		
		return $indirect;
	}
	
	/**
	 * 
	 * @param \pdflib\Handle $handle
	 * @return \pdflib\datatypes\Object
	 */
	public static function readObject($handle, $buffer, &$offset = 0){
		if(preg_match('/^\<\<\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			
			$dictionary = new Dictionary();
			
			while(!preg_match('/^\>\>\s*/', substr($buffer, $offset), $matches)){
				$key = self::readObject($handle, $buffer, $offset);
				if(!$key instanceof Name) throw new \Exception('Unexpected object expected Name');
				
				$value = self::readObject($handle, $buffer, $offset);
				
				$dictionary->set($key, $value);
				
				if($offset >= strlen($buffer)){
					$buffer = $handle->readline();
					$offset = 0;
				}
			}
			$offset+= strlen($matches[0]);
			return $dictionary;
		}elseif(preg_match('/^\[\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			
			$collection = new Collection();
			
			while(!preg_match('/^\]\s*/', substr($buffer, $offset), $matches)){
				$value = self::readObject($handle, $buffer, $offset);
				
				$collection->push($value);
				
				if($offset >= strlen($buffer)){
					$buffer = $handle->readline();
					$offset = 0;
				}
			}
			
			$offset+= strlen($matches[0]);
			return $collection;
		}elseif(preg_match('/^\/([^\s\(\)\<\>\[\]\{\}\/\%\#]+)\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			return new Name(preg_replace_callback('/#[0-9a-f]{2}/i', function($matches){
				return chr(hexdec($matches[0]));
			}, $matches[1]));
		}elseif(preg_match('/^(\d+) (\d+) R\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			
			return new Reference($matches[1], $matches[2]);
		}elseif(preg_match('/^\((.*?)(\)|\\\\)\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			$text = $matches[1];
			
			if($matches[2] == '\\'){
				$buffer = $handle->readline();
				while(preg_match('/^(.*?)(\)|\\\\)\s*/', $buffer, $matches)){
					$text.= $matches[1];
					if($matches[2]==')') break;
				}
				$offset= strlen($matches[0]);
			}
			
			return new Text($text);
		}elseif(preg_match('/^(\d+(\.\d+)?)\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			
			return new Number($matches[1]);
		}elseif(preg_match('/^null\s*/', substr($buffer, $offset), $matches)){
			$offset+= strlen($matches[0]);
			
			return new Nil();
		}
		
		echo ":(\n";
		echo substr($buffer, $offset);
		exit;
	}
}