<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	//$id=3;
	//$str = "'onclick=$(\'.ajax-id-myModalContainer'.$id.'\')";
	//$str.="$('.ajax-id-myModalContainer.$id."').ajaxReload({args:{modale:'voir'}});";
	//$str.="return false;"
//	eval("$str='AAA';");
//echo $str;
//echo "aaa";
echo "A";
$test="\$a=2*3+5;";
eval($test);
echo $a;
echo "B";

$id=3;
$str = "'onclick=\"$(\'.ajax-id-myModalContainer$id\').ajaxReload({args:{modale:\'voir\'}}); return false;\"'";
$str="\$str=$str;";
//eval($str);
eval($str);
echo $str;
//echo $str;
//echo "<br>";
//eval($str).'A';
?>