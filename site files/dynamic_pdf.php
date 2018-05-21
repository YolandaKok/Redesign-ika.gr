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
 $surname = $row['imp_surname'];
 $birthday = $row['imp_birthday'];
 $isChild = $row['isChild'];
 $isHusband_Wife = $row['isHusband_Wife'];

 require('tfpdf/tfpdf.php');

 $pdf = new tFPDF();
 $pdf->AddPage();

 // Add a Unicode font (uses UTF-8)
 $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
 $pdf->SetFont('DejaVu','',24);
 $pdf->Write(8,'ΙΔΡΥΜΑ ΚΟΙΝΩΝΙΚΩΝ ΑΣΦΑΛΙΣΕΩΝ');
 $pdf->Ln(10);
 $pdf->Ln(10);
 $pdf->SetFont('DejaVu','',20);
 $pdf->Write(8,'Δήλωση Έμμεσα Ασφαλισμένου');
 $pdf->Ln(10);
 $pdf->Ln(10);
 $pdf->Write(8,'Όνομα: ');
 $pdf->Write(8,$name);
 $pdf->Ln(10);
 $pdf->Write(8, 'Επώνυμο: ');
 $pdf->Write(8,$surname);
 $pdf->Ln(10);
 $pdf->Write(8, 'Ημερομηνία Γέννησης: ');
 $pdf->Write(8,$birthday);
 $pdf->Ln(10);
 $pdf->Write(8, 'Συγγένεια: ');
 if($isChild) {
  $pdf->Write(8, 'Παιδί');
 }
 else {
  $pdf->Write(8, 'Σύζυγος');
 }
 // Load a UTF-8 string from a file and print it

 // Select a standard font (uses windows-1252)
 $pdf->Output();


?>
