<?php


function eval_critique_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/eval_critique.css').'" type="text/css" media="all" />';
	return $flux;
}

?>
