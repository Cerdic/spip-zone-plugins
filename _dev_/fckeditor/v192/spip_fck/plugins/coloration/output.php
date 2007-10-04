<?php
include('geshi/geshi.php');

$geshi = new GeSHi('', $_POST['lang']);

$geshi->set_source( stripslashes( $_POST['source'] ) );

switch ($_POST['lines']) 
{
       case "0" : 
            $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
       break;
       case "1" : 
            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
       break;
       case "2" : 
            $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,$_POST['interligne']);
       break;
}       

$geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;',true); 

$geshi->start_line_numbers_at($_POST['premnum']); 

$geshi->set_tab_width($_POST['tabulation']);

//$geshi->set_header_type(GESHI_HEADER_DIV);
//$geshi->set_header_type(GESHI_HEADER_PRE);
$geshi->set_header_type(GESHI_HEADER_NONE);

echo $geshi->parse_code();
?>