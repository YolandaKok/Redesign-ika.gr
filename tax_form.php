<?php

 require_once 'helpers/greek_to_uppercase.php';

 require_once 'login.php';
 $conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 // Check Connection
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
 $surname = $row['lastname'];

 $app_date = date("d-m-Y");
 $completed = 1;
 mysqli_query($conn, "SET NAMES 'utf8'");
 $query="SELECT * FROM applications WHERE users_id=$app_id";
 $res= $conn->query($query);
 $anything_found = mysqli_num_rows($res);
 if($anything_found==0)
  $query = "INSERT INTO applications (completed, users_id, app_date) VALUES ('$completed', '$app_id', '$app_date')";
 $res= $conn->query($query);



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
  $pdf->Write(8,'Βεβαίωση για φορολογική χρήση');
  $pdf->Ln(10);
  $pdf->Ln(10);
  $pdf->Write(8,'Βεβαιώνουμε ότι ο / η  συνταξιούχος με όνομα'.$name);
  $pdf->Ln(10);
  $pdf->Write(8, 'Λαμβάνει μηνιαία το ποσό των '.$row['money'].'Ευρώ');
  $total = $row['money'] * 12;
  $pdf->Ln(10);
  $pdf->Write("Ετήσιο Εισόδημα: ".$total." Ευρώ");
  $pdf->Ln(10);
  $tax = $total * 6/100;
  $total = $total - $tax;
  $pdf->Write("Φόρος: ".$tax." Ευρώ");
  $pdf->Ln(10);
  $pdf->Write("Καθαρό Εισόδημα: ".$total." Ευρώ");
  $pdf->Output();
?>
