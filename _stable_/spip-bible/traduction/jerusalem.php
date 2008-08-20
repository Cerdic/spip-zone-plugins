<?php
/*
Maïeul Rouquette Licence GPL 3
Spip-Bible
Module "Bible de Jérusalem"

*/


function recuperer_passage($livre='',$chapitre_debut='',$verset_debut='',$chapitre_fin='',$verset_fin=''){
	//recuperer le passage dans la bible de Jérusalem
	
	$petit_livre=array('Ab','Phm','2jn','3jn','Jude');
	foreach ($petit_livre as $i){
		if (strtolower($i)==strtolower($livre));{
			$petit=true;
			$verset_debut=$chapitre_debut;
			$verset_fin=$chapitre_fin;
			$chapitre_fin=1;
			$chapitre_debut=1;
			break;
			}
		}
	
	
	$url_base = 'http://www.biblia-cerf.com/BJ/';
	$texte = '';
	$i = $chapitre_debut; 
	
	while ($i<=$chapitre_fin){
	
		include_spip("inc/distant");
		include_spip("inc/charsets");
		$code = importer_charset(recuperer_page($url_base.strtolower($livre).$i.'.html'),'iso-8859-1');
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
		$verset_debut =='' and $i==$chapitre_debut ? $debut=1 : $debut=$debut;
		$verset_fin =='' and $i==$chapitre_fin ? $debut=1 : $debut=$debut;
		
		
		if ($petit){
			$texte .=  recuperer_passage_dans_livre($debut,$fin+1,$livre,$code);
		}
		else{
			$texte .= '<strong>'.$i.'</strong>'. recuperer_passage_dans_chapitre($debut,$fin,$livre,$code,$i).'<br />';
		}
		
		$i++;
		
		
	
	}
	return $texte;
}
	
function recuperer_passage_dans_chapitre($debut='',$fin='',$livre='',$chaine='',$chap=''){
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

function recuperer_passage_dans_livre($debut,$fin,$livre,$code){
	
	$ex1 = $livre.'&nbsp;'.$debut.'-';
	$ex2 = $livre.'&nbsp;'.$fin.'-';
	
	$tableau=explode($ex1,$code);
	$code = '<sup>'.$debut.' </sup>'.$tableau[1];
	$tableau=explode($ex2,$code);
	$code=$tableau[0];
	$code=str_replace($livre,'<sup>',$code);
	
	$i=$debut+1;
	
	while ($i<$fin){
		$code=str_replace($i.'-',$i.' </sup>',$code);
		$i++;
	}
	
	return $code;

}
?>