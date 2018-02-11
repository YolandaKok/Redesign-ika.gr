<?php

 require_once 'helpers/greek_to_uppercase.php';

 require_once 'login.php';
 use pdflib\File;
 header('Content-Type: text/plain');
 require_once '../autoloader.php';
 $conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 $file = new File('test.pdf');
 $file->getInformation()
 	->setTitle('My PDF Library')
 	->setSubject('How to create a pdf library')
 	->setAuthor('10usb');

 $catalog = $file->getCatalog()->setSize(595.276, 841.890);


 $page = $catalog->addPage();

 $canvas = $page->getCanvas();

 $canvas->setStrokeColor(255, 0, 255);
 $canvas->setLineWidth(5);
 $canvas->line(30, 30, 50, 100);


 $canvas->setFillColor(50, 50, 50);
 $canvas->setFont($page->getFont('Helvetica', 11));
 $canvas->text(50, 50, "PDF Library");
 $canvas->text(50, 70, "You start with...");

 $file->flush();

?>
