<?php
function tw_echappe_code_latex($code){
	return '<html>\begin{english}'.echappe_html($code[0],'latex').'\end{english}</html>';	
}

function tw_code_latex($code){
	$lang = strtolower($code[2]);
		if ($lang == 'php'){
		$options =	'[startinline]';	// Permet d'avoir le php coloré même sans <?php nécéssite la version 1.7 de minted.sty. À télécharger : http://minted.googlecode.com/hg/minted.sty . Mettre le fichier .sty à côté du fichier latex principal, à compiler. En attendant que l'auteur ait le temps de sortir la release
	}
	else 
		$options = '';
	
	if (count($code)>1)
		return echappe_html("<html>\begin{english}\begin{minted}$options{".$lang."}\n",'latex');
	else
		return 	echappe_html("\n\end{minted}\end{english}</html>",'latex');
}

function tw_cadre_latex($code){
	$lang = strtolower($code[2]);
	if ($lang == 'php'){
		$options =	'[linenos,startinline]';
	}
	else 
		$options = '[linenos]';
	if (count($code)>1)
		return echappe_html("<html>\begin{english}\begin{minted}$options{".$lang."}\n",'latex');
	else
		return echappe_html("\n\end{minted}\end{english}</html>",'latex');
}
?>