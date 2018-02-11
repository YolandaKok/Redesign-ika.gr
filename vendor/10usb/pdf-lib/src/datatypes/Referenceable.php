<?php
namespace pdflib\datatypes;

interface Referenceable {
	public function getNumber();
	public function getGeneration();
}