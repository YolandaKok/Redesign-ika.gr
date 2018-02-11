<?php
namespace pdflib\datatypes;

class Nil implements Object {
	public function output(){
		return 'null';
	}
}