<?php

 require_once 'helpers/greek_to_uppercase.php';

 //require_once 'login.php';
 use pdflib\File;
 header('Content-Type: text/plain');
 require_once '../autoloader.php';
 //$conn = new mysqli($cleardb_server,$cleardb_username,$cleardb_password,$cleardb_db);
 $file = new File('test.pdf');
 $file->getInformation()
 	->setTitle('My PDF Library')
 	->setSubject('How to create a pdf library')
 	->setAuthor('10usb');
?>
