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

 $query = "SELECT * FROM applications WHERE idapplications=$app_id";
 $res= $conn->query($query);
 $res->data_seek(0);
 $row = $res->fetch_assoc();
 $name = $row['imp_name'];
 //$name = grstrtoupper($name);
 require('tfpdf/tfpdf.php');

 $pdf = new tFPDF();
 $pdf->AddPage();

 // Add a Unicode font (uses UTF-8)
 $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
 $pdf->SetFont('DejaVu','',14);

 // Load a UTF-8 string from a file and print it
 $pdf->Write(8,$name);

 // Select a standard font (uses windows-1252)
 $pdf->SetFont('Arial','',14);
 $pdf->Ln(10);
 $pdf->Write(5,'The file size of this PDF is only 12 KB.');

 $pdf->Output();


?>
