# pdf-lib
A PHP PDF library that is not created to easily add rich content *(HTML etc)* to a PDF file. But rather allowing any valid PDF content to be added to the file without the excess of functionality that **"tries"** to emulate HTML like behavior and limit/complicate simple tasks. This library takes the concept that every page is no more then just a canvas area that can takes 2D graphics rendering commands. Any other functionality there might be is considers meta data.


## Example
```php
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
```

## Nice to have
While a lot can be done, more can be done.
 - Ability to set the compression/filter methods to use (or none) 
 - Append the contents of one file to then end of an other (or even better insert between)
 - Clean-up an incremental updated pdf file
 - Add optimization of render insttructions
 - Add optimization by shared resource dictionary between pages 