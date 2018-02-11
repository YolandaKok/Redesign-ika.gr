<?php
use pdflib\File;

header('Content-Type: text/plain');

require_once '../autoloader.php';

$start = microtime(true);

$file = new File('test.pdf');
$file->getInformation()
	->setTitle('My PDF Library')
	->setSubject('How to create a pdf library')
	->setAuthor('10usb');

$catalog = $file->getCatalog()->setSize(595.276, 841.890);

// Add a page but overrule the size
$page = $catalog->addPage()->setSize(450, 450);

// Get a canvas object from the page and start redering on it
$canvas = $page->getCanvas();

$canvas->image(30, 30, 48, 48, $page->getImage('logo.png'));


$canvas->setFont($font = $page->getFont('Times-BoldItalic', 26));

$canvas->setFillColor(192, 192, 192);
$canvas->text(90.8, 30.8+ $font->getSize(), "PDF Library");

$canvas->setFillColor(66, 66, 66);
$canvas->text(90, 30 + $font->getSize(), "PDF Library");

$canvas->setFillColor(92, 92, 92);
$canvas->setFont($font = $page->getFont('Helvetica', 11));
$canvas->text(90, 64 + $font->getSize(), "pdf-lib a simple layer around a PDF file made by 10usb");


$canvas->setStrokeColor(0, 0, 0);
$canvas->setLineWidth(1.5);
$canvas->line(30, 86, $page->getWidth() - 30, 86);


$lines = [];
$lines[] = 'A PHP PDF library that is not created to easily add rich content (HTML etc) to a';
$lines[] = 'PDF file. But rather allowing any valid PDF content to be added to the file without';
$lines[] = 'the excess of functionality that "tries" to emulate HTML like behavior and';
$lines[] = 'limit/complicate simple tasks. This library takes the concept that every page is no';
$lines[] = 'more then just a canvas area that can takes 2D graphics rendering commands.';
$lines[] = 'Any other functionality there might be is considers meta data.';

$canvas->setFillColor(66, 66, 66);
foreach($lines as $index=>$line){
	$canvas->text(30, 112 + $index * $font->getSize() * 1.5, $line);
}

/* Page 1: Images & Rectangles */
$page = $catalog->addPage();
$canvas = $page->getCanvas();

$canvas->setFont($font = $page->getFont('Times-BoldItalic', 22));

$canvas->setFillColor(192, 192, 192);
$canvas->text(30.8, 30.8+ $font->getSize(), "Images");

$canvas->setFillColor(66, 66, 66);
$canvas->text(30, 30 + $font->getSize(), "Images");


$canvas->image(30, 80, 535, 300, $page->getImage('DSC_0489.JPG'));


$canvas->setFillColor(192, 192, 192);
$canvas->text(30.8, 400.8 + $font->getSize(), "Rectangles");

$canvas->setFillColor(66, 66, 66);
$canvas->text(30, 400 + $font->getSize(), "Rectangles");


$canvas->setStrokeColor(66, 66, 66);
$canvas->setFillColor(192, 192, 192);

$canvas->rectangle(30, 450, 535, 200);

$canvas->setFillColor(66, 66, 192);
$canvas->rectangle(30 + (535 - 250) / 2, 550, 250, 200, true, true);
$canvas->rectangle(30 + (535 - 450) / 2, 475, 450, 325, false, true);

/* Page 2: Lines & Polygons */
$page = $catalog->addPage();
$canvas = $page->getCanvas();

$canvas->setFont($font = $page->getFont('Times-BoldItalic', 22));

$canvas->setFillColor(192, 192, 192);
$canvas->text(30.8, 30.8+ $font->getSize(), "Lines");

$canvas->setFillColor(66, 66, 66);
$canvas->text(30, 30 + $font->getSize(), "Lines");

$canvas->setStrokeColor(66, 66, 66);
$canvas->setLineWidth(26);

$canvas->setLineCap(0);
$canvas->line(100, 90, 450, 90);

$canvas->setLineCap(1);
$canvas->line(100, 130, 450, 130);

$canvas->setLineCap(2);
$canvas->line(100, 170, 450, 170);

$canvas->setStrokeColor(255, 255, 255);
$canvas->setLineWidth(1);
$canvas->setLineCap(0);
$canvas->line(100 + 1, 90, 450 - 1, 90);
$canvas->line(100 + 1, 130, 450 - 1, 130);
$canvas->line(100 + 1, 170, 450 - 1, 170);

$canvas->setStrokeColor(66, 66, 66);
$canvas->setLineWidth(26);

$canvas->setLineDash(3, 3);
$canvas->line(100, 210, 450, 210);

$canvas->setLineCap(1);
$canvas->setLineDash(0, 39);
$canvas->line(100 + 13, 250, 450, 250);

$canvas->setLineCap(0);
$canvas->setLineDash(26, 26);
$canvas->line(100, 290, 450, 290);

$canvas->setLineDash(0, 0);


$canvas->setFillColor(192, 192, 192);
$canvas->text(30.8, 400.8 + $font->getSize(), "Polygons");

$canvas->setFillColor(66, 66, 66);
$canvas->text(30, 400 + $font->getSize(), "Polygons");

$canvas->setStrokeColor(66, 66, 66);
$canvas->setFillColor(128, 128, 128);
$canvas->setLineWidth(1.5);

// Filled polygon
$radius = 70;
for($index = 0; $index < 5; $index++){
	$radians = pi() * 2 / 5 * $index * 2;
	if($index == 0){
		$canvas->moveTo(100 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}else{
		$canvas->lineTo(100 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}
}
$canvas->fill();

// Stroked polygon
$radius = 70;
for($index = 0; $index < 5; $index++){
	$radians = pi() * 2 / 5 * $index * 2;
	if($index == 0){
		$canvas->moveTo(220 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}else{
		$canvas->lineTo(220 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}
}
$canvas->closePath();
$canvas->stroke();

// Filled with even odd rule polygon
$radius = 70;
for($index = 0; $index < 5; $index++){
	$radians = pi() * 2 / 5 * $index * 2;
	if($index == 0){
		$canvas->moveTo(340 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}else{
		$canvas->lineTo(340 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}
}
$canvas->fill(true);

// Filled & Stroked with even odd rule polygon and line dashed
$radius = 70;
for($index = 0; $index < 5; $index++){
	$radians = pi() * 2 / 5 * $index * 2;
	if($index == 0){
		$canvas->moveTo(460 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}else{
		$canvas->lineTo(460 + cos($radians) * $radius, 500 + sin($radians) * $radius);
	}
}
$canvas->closePath();
$canvas->setLineDash(5, 3);
$canvas->fillAndStroke(true);




$file->flush();

if(isset($_GET['stats'])){
	printf("generated in: %ss\n", round(microtime(true) - $start, 4));
	printf("memory use: %sMB\n", round(memory_get_peak_usage() / 1024 / 1024, 2));
	exit;
}


header('Content-Type: application/pdf');
header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
header('Pragma: public');
header('Expires: '.date('D, d M Y H:i:s z'));
header('Content-Disposition: inline; filename="basic.pdf');
echo $file->getContents();