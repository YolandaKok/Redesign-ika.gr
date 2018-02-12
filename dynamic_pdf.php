<?php

 require_once 'helpers/greek_to_uppercase.php';

 //require_once 'login.php';
 //header('Content-Type: text/plain');
 //$conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);

 require('fpdf181/fpdf.php');

 $pdf = new FPDF();
 $pdf->AddPage();
 $pdf->SetFont('Arial','B',16);
 $pdf->Cell(40,10,'Hello World!');
 $pdf->Output();



?>
