<?php

 require_once 'helpers/greek_to_uppercase.php';

 require_once 'login.php';
 //header('Content-Type: text/plain');
 $conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 if ($conn->connect_error) die ($conn->connect_error);
 // Escape user inputs for security
 // Take arguments from POST method
 $app_id = $_GET["id"];
 // Select from users where username and password
 mysqli_query($conn, "SET NAMES 'utf8'");

 $query = "SELECT * FROM users WHERE id=$app_id";
 $res= $conn->query($query);
 $res->data_seek(0);
 $row = $res->fetch_assoc();
 $name = $row['firstname'];
 $name = grstrtoupper($name);
 $surname = $row['lastname'];
 //$surname = grstrtoupper($surname);
 require('fpdf181/fpdf.php');
 //$surname = 'hh';
 $pdf = new FPDF();
 $pdf->AddPage();
 $pdf->SetFont('Symbol','B',16);
 $pdf->Cell(40,10,$name);
 $pdf->Output();



?>
