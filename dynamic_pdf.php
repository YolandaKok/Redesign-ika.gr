<?php

 require_once 'helpers/greek_to_uppercase.php';

 //require_once 'login.php';
 //header('Content-Type: text/plain');
 //$conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 define('FPDF_FONTPATH','.');
 require('../fpdf.php');

 $pdf = new FPDF();
 $pdf->AddFont('Calligrapher','','calligra.php');
 $pdf->AddPage();
 $pdf->SetFont('Calligrapher','',35);
 $pdf->Cell(0,10,'Enjoy new fonts with FPDF!');
 $pdf->Output();


?>
