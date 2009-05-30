<?php
function json($text){
	$text = str_replace('\\','\\\\', $text);
	$text = str_replace('/','\\/', $text);
	$text = str_replace('"','\\"',$text);
	$text = str_replace("\b",'\\b',$text);
	$text = str_replace("\f",'\\f',$text);
	$text = str_replace("\n",'\\n',$text);
	$text = str_replace("\r",'\\r',$text);
	$text = str_replace("\t",'\\t',$text);
	return $text;
}	
?>