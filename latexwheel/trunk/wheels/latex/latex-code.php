<?php
function tw_echappe_code_latex($code){
	return '<html>\begin{english}'.echappe_html($code[0],'latex').'\\end{english}</html>';	
}

function tw_code_latex($code){
	$lang = strtolower($code[2]);
		if ($lang == 'php'){
		$options =	'[startinline]';	// Permet d'avoir le php coloré même sans <?php nécéssite la version 1.7 de minted.sty.
	}
	else 
		$options = '';
	
	if (count($code)>1)
		return echappe_html("<html>\begin{english}\n\begin{minted}$options{".$lang."}\n",'latex');
	else
		return 	echappe_html("\n\\end{minted}\n\\end{english}</html>",'latex');
}

function tw_cadre_latex($code){
	$lang = strtolower($code[2]);
	if ($lang == 'php'){
		$options =	'[linenos,startinline]';
	}
	else 
		$options = '[linenos]';
	if (count($code)>1)
		return echappe_html("<html>\begin{english}\n\begin{minted}$options{".$lang."}\n",'latex');
	else
		return echappe_html("\n\\end{minted}\n\\end{english}</html>",'latex');
}
?>