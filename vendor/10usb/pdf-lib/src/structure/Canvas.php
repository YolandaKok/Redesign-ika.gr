<?php
namespace pdflib\structure;

use pdflib\datatypes\Text;

class Canvas {
	const LINECAP_BUTT		= 0;
	const LINECAP_ROUND		= 1;
	const LINECAP_SQUARE	= 2;
	
	const LINEJOIN_MITER	= 0;
	const LINEJOIN_ROUND	= 1;
	const LINEJOIN_BEVEL	= 2;
	
	/**
	 * 
	 * @var number $width
	 * @var number $height
	 */
	private $width, $height;
	
	/**
	 * 
	 * @var \pdflib\datatypes\Stream
	 */
	private $stream;
	
	/**
	 * 
	 * @param number $width
	 * @param number $height
	 * @param \pdflib\datatypes\Stream $stream
	 */
	public function __construct($width, $height, $stream){
		$this->width	= $width;
		$this->height	= $height;
		$this->stream	= $stream;
	}
	
	/**
	 * 
	 * @param number $r
	 * @param number $g
	 * @param number $b
	 * @return \pdflib\structure\Canvas
	 */
	public function setStrokeColor($r, $g, $b){
		$this->stream->append(sprintf("%.3F %.3F %.3F RG\n", $r / 255, $g / 255, $b / 255));
		return $this;
	}
	
	/**
	 * 
	 * @param number $r
	 * @param number $g
	 * @param number $b
	 * @return \pdflib\structure\Canvas
	 */
	public function setFillColor($r, $g, $b){
		$this->stream->append(sprintf("%.3F %.3F %.3F rg\n",$r / 255, $g / 255, $b / 255));
		return $this;
	}
	
	/**
	 * 
	 * @param number $width
	 * @return \pdflib\structure\Canvas
	 */
	public function setLineWidth($width){
		$this->stream->append(sprintf("%.2F w\n", $width));
		return $this;
	}
	
	/**
	 * 
	 * @param integer $style
	 * @return \pdflib\structure\Canvas
	 */
	public function setLineCap($style){
		$this->stream->append(sprintf("%d J\n", $style));
		return $this;
	}
	
	/**
	 * 
	 * @param integer $style
	 * @return \pdflib\structure\Canvas
	 */
	public function setLineJoin($style){
		$this->stream->append(sprintf("%d j\n", $style));
		return $this;
	}
	
	/**
	 * 
	 * @param number $on
	 * @param number $off
	 * @param number $start
	 * @return \pdflib\structure\Canvas
	 */
	public function setLineDash($on, $off, $start = 0){
		if($on == 0 && $off == 0){
			$this->stream->append("[ ] 0 d\n");
		}elseif($on == $off){
			if($start >= $on + $off) throw new \Exception('Start can\'t be larger then the sum of on and off');
			$this->stream->append(sprintf("[%.2F] %.2F d\n", $on, $start));
		}else{
			if($start >= $on + $off) throw new \Exception('Start can\'t be larger then the sum of on and off');
			$this->stream->append(sprintf("[%.2F %.2F] %.2F d\n", $on, $off, $start));
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \pdflib\structure\Canvas
	 */
	public function save(){
		$this->stream->append("q\n");;
		return $this;
		
	}
	
	/**
	 * 
	 * @return \pdflib\structure\Canvas
	 */
	public function restore(){
		$this->stream->append("Q\n");;
		return $this;
	}
	
	/**
	 * 
	 * @param number $x1
	 * @param number $y1
	 * @param number $x2
	 * @param number $y2
	 * @return \pdflib\structure\Canvas
	 */
	public function line($x1, $y1, $x2, $y2){
		$this->stream->append(sprintf("%.2F %.2F m %.2F %.2F l S\n", $x1, $this->height - $y1, $x2, $this->height - $y2));
		return $this;
	}
	
	/**
	 * 
	 * @param number $x
	 * @param number $y
	 * @param number $w
	 * @param number $h
	 * @param boolean $filled
	 * @param boolean $border
	 * @return \pdflib\structure\Canvas
	 */
	public function rectangle($x, $y, $w, $h, $filled=true, $border = false){
		if($filled && $border){
			$this->stream->append(sprintf("%.2F %.2F %.2F %.2F re B\n", $x, $this->height - $y, $w, -$h));
		}elseif($filled){
			$this->stream->append(sprintf("%.2F %.2F %.2F %.2F re f\n", $x, $this->height - $y, $w, -$h));
		}elseif($border){
			$this->stream->append(sprintf("%.2F %.2F %.2F %.2F re S\n", $x, $this->height - $y, $w, -$h));
		}
		return $this;
	}
	
	/**
	 * 
	 * @param number $x
	 * @param number $y
	 * @param number $w
	 * @param number $h
	 * @param \pdflib\structure\Image $image
	 * @return \pdflib\structure\Canvas
	 */
	public function image($x, $y, $w, $h, $image){
		$this->stream->append(sprintf("q %.2F 0 0 %.2F %.2F %.2F cm %s Do Q\n", $w, $h, $x, $this->height - ($y + $h), $image->getLocalName()->output()));
		return $this;
	}
	
	/**
	 * 
	 * @param \pdflib\structure\Font $font
	 * @return \pdflib\structure\Canvas
	 */
	public function setFont($font){
		$this->stream->append(sprintf("BT %s %.2F Tf ET\n", $font->getLocalName()->output(), $font->getSize()));
		return $this;
	}
	
	/**
	 * 
	 * @param number $left
	 * @param number $top
	 * @param string $text
	 * @return \pdflib\structure\Canvas
	 */
	public function text($left, $top, $text){
		$text = new Text($text);
		$this->stream->append(sprintf("BT %.2F %.2F Td %s Tj ET\n", $left, $this->height - $top, $text->output()));;
		return $this;
	}
	
	/**
	 * 
	 * @param number $left
	 * @param number $top
	 * @return \pdflib\structure\Canvas
	 */
	public function moveTo($left, $top){
		$this->stream->append(sprintf("%.2F %.2F m\n", $left, $this->height - $top));;
		return $this;
	}
	
	/**
	 * 
	 * @param number $left
	 * @param number $top
	 * @return \pdflib\structure\Canvas
	 */
	public function lineTo($left, $top){
		$this->stream->append(sprintf("%.2F %.2F l\n", $left, $this->height - $top));;
		return $this;
	}
	
	/**
	 * 
	 * @return \pdflib\structure\Canvas
	 */
	public function closePath(){
		$this->stream->append("h\n");;
		return $this;
	}
	
	/**
	 * 
	 * @param boolean $evenOdd
	 * @return \pdflib\structure\Canvas
	 */
	public function fill($evenOdd = false){
		$this->stream->append($evenOdd ? "f*\n" : "f\n");;
		return $this;
	}
	
	/**
	 * 
	 * @return \pdflib\structure\Canvas
	 */
	public function stroke(){
		$this->stream->append("S\n");;
		return $this;
	}
	
	/**
	 * 
	 * @param boolean $evenOdd
	 * @return \pdflib\structure\Canvas
	 */
	public function fillAndStroke($evenOdd = false){
		$this->stream->append($evenOdd ? "B*\n" : "B\n");;
		return $this;
	}
	
	/**
	 * 
	 * @param boolean $evenOdd
	 * @return \pdflib\structure\Canvas
	 */
	public function clip($evenOdd = false){
		$this->stream->append($evenOdd ? "n W*\n" : "n W\n");;
		return $this;
	}
}