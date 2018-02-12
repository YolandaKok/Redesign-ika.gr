<?php

 require_once 'helpers/greek_to_uppercase.php';

 //require_once 'login.php';
 //header('Content-Type: text/plain');
 //$conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 namespace PDFLib\Test;
 use PDFLib\PDFLib;
 class ExampleDocument extends PDFLib
 {
     /**
      * @inherit
      */
     public function __construct()
     {
         parent::__construct();
         $this->SetAutoPageBreak(true, $this->bMargin + $this->FontSizePt);
         $this->SetTopMargin($this->tMargin + $this->FontSizePt);
     }
     /**
      * @inherit
      */
     public function Header()
     {
         $this->SetDefaultFont();
         $this->SetY($this->tMargin - $this->FontSizePt);
         $this->Cell(0, 0, "This text is within the header!", 0, 1, "C");
     }
   } 

?>
