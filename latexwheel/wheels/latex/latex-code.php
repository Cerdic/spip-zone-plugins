<?php

function tw_code_latex($code){
	$texte = str_replace($code[1],"\begin{minted}{".strtolower($code[3])."}",$code[0]);
	$texte = str_replace('</code>','\end{minted}',$texte);
	$texte = '<latex>'.base64_encode($texte).'</latex>';
	return $texte;
}

function tw_cadre_latex($code){
	$texte = str_replace($code[1],"\begin{minted}[lineos]{".strtolower($code[3])."}",$code[0]);
	$texte = str_replace('</cadre>','\end{minted}',$texte);
	$texte = '<latex>'.base64_encode($texte).'</latex>';

	return $texte;
}
?>