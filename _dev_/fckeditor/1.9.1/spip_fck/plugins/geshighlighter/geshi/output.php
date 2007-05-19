<?php
include('geshi.php');

$g = new GeSHi('', $_POST['lang']);

$g->set_source( stripslashes( $_POST['source'] ) );

$g->line_numbers = $_POST['lines'] != '' ? GESHI_NORMAL_LINE_NUMBERS : GESHI_NO_LINE_NUMBERS;

echo $g->parse_code();
?>