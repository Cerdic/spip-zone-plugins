<?php
error_reporting(E_WARNING);

$pdf_dir='contrib/ezpdf/';
include $pdf_dir.'class.ezpdf.php';	// inclusion du code de la bibliothèque

$pdf =& new Cezpdf();
$pdf->selectFont($pdf_dir.'/fonts/Helvetica');
$pdf->ezStartPageNumbers(70, 20, 8, 'left', 'Page {PAGENUM} sur {TOTALPAGENUM}');

//--------------------------------------------------
// you will have to change these to your settings
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'officebac';
$query = 'select * from odb_repartition';
//--------------------------------------------------
// open the connection to the db server
$link = mysql_connect($host,$user,$password);
// change to the right database
mysql_select_db($database);
// initialize the array
$data = array();
// do the SQL query
$result = mysql_query($query);
// step through the result set, populating the array, note that this could
// also have been written:
// while($data[] = mysql_fetch_assoc($result)) {}
while($data[] = mysql_fetch_array($result, MYSQL_ASSOC)) {}
// make the table
$pdf->ezTable($data);
// do the output, this is my standard testing output code, adding ?d=1
// to the url puts the pdf code to the screen in raw form, good for
// checking
// for parse errors before you actually try to generate the pdf file.
$d=$_GET['d'];
if (isset($d) && $d){
   $pdfcode = $pdf->output(1);
   $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
   echo '<html><body>';
   echo trim($pdfcode);
   echo '</body></html>';
} else {
   $pdf->ezStream();
}

?>
