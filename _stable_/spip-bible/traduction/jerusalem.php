<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
Module "Bible de Jérusalem"

*/


function recuperer_passage($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin=''){
	//recuperer le passage dans la bible de Jérusalem
	
	
	$url_base = 'http://www.biblia-cerf.com/BJ/';
	$texte = '';
	$i = $chapitre_debut; 
	
	while ($i<=$chapitre_fin){
	
		include_spip("inc/distant");
		include_spip("inc/charsets");
		$code = charset2unicode(recuperer_page($url_base.strtolower($livre).$i.'.html'),'iso-8859-1');
		//epuration du code
		$tableau=explode('</head>',$code);
		$code=$tableau[1];
		$code = strip_tags($code,'<table>');
		$tableau = explode('<table cellspacing="7">',$code);
		$code=$tableau[1];
		$tableau = explode('</table>',$code);
		$code=$tableau[0];
		
		//var_dump($code).'<br>';
		// réglage des versets de debut/fin
		$i == $chapitre_debut ? $debut = $verset_debut : $debut=1;
		$i == $chapitre_fin ? $fin = $verset_fin : $fin = '';
		
		
		
		$texte .= '<strong>'.$i.'</strong>'. recuperer_passage_dans_chapitre_jerusalem($debut,$fin,$livre,$code,$i).'<br />';
		
		
		$i++;
		
		
	
	}
	return $texte;
}
	
function recuperer_passage_dans_chapitre_jerusalem($debut='',$fin='',$livre='',$chaine='',$chap=''){
	$ex = $livre.'&nbsp;'.$chap.':'.$debut.'-';
	
	
	$tableau=explode($ex,$chaine);
	$chaine=$tableau[1];
	
	if ($fin!=''){
		$ex = $livre.'&nbsp;'.$chap.':'.intval($fin+1).'-';
		$tableau=explode($ex,$chaine);
		$chaine=$tableau[0];
	}
	
	else {
		$tableau=explode($livre.'&nbsp;'.$chap.':',$chaine);
		$dernier = end($tableau);
		$dernier = explode('-',$dernier);
		$fin= $dernier[0];
		
	
	}
	$chaine = str_replace($livre.'&nbsp;'.$chap.':','<br /><sup>',$chaine);	
	
		$i = $debut;
		while ($i<=$fin){
		$chaine = str_replace($i.'-',$i.' </sup>',$chaine);
		$i++;
	}
	
	return '<sup>'.$debut.' </sup>'.$chaine;
}
?>