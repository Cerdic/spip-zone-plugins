<?php

if (!defined('_INTRODUCTION_SUITE')) define('_INTRODUCTION_SUITE', '&nbsp;(...)');
if (!defined('_INTRODUCTION_LGR')) define('_INTRODUCTION_LGR', 100);

// fonction appelant une liste de fonctions qui permettent de nettoyer un texte original de ses raccourcis indesirables
function cs_introduire($texte) {
	$liste = array_unique($GLOBALS['cs_introduire']);
	foreach($liste as $f)
		if (function_exists($f)) $texte = $f($texte);
	return $texte;
}

function cs_introduction($type, $texte, $chapo, $descriptif, $id) {
	$intro_suite = '@@CS_SUITE@@';
	switch ($type) {
		case 'articles':
			# si descriptif contient juste des espaces ca produit une intro vide, 
			# c'est une fonctionnalite, pas un bug
			if (strlen($descriptif))
				return propre($descriptif);
			else if (substr($chapo, 0, 1) == '=')	// article virtuel
				return '';
			else
				$result = PtoBR(propre(supprimer_tags(couper_intro(cs_introduire($chapo."\n\n\n".$texte), round(500*_INTRODUCTION_LGR/100), $intro_suite))));
			break;
		case 'breves':
			$result = PtoBR(propre(supprimer_tags(couper_intro(cs_introduire($texte), round(300*_INTRODUCTION_LGR/100), $intro_suite))));
			break;
		case 'forums':
			$result = PtoBR(propre(supprimer_tags(couper_intro(cs_introduire($texte), round(600*_INTRODUCTION_LGR/100), $intro_suite))));
			break;
		case 'rubriques':
			if (strlen($descriptif))
				return propre($descriptif);
			else
				$result = PtoBR(propre(supprimer_tags(couper_intro(cs_introduire($texte), round(600*_INTRODUCTION_LGR/100), $intro_suite))));
			break;
	}
	// si les points de suite ont ete ajoutes
	if (strpos($result, '@@CS_SUITE@@') !== false) {
		// precaution sur le tout paragrapher !
		$mem = $GLOBALS['toujours_paragrapher'];  
		$GLOBALS['toujours_paragrapher'] = false;  
		// des points de suite bien propres
		$intro_suite = propre(_INTRODUCTION_SUITE);
		// si les points de suite sont cliquables
		if ($id && _INTRODUCTION_LIEN == 1) {
			if (substr($intro_suite, 0, 6) == '<br />') 
				$intro_suite = propre("<br />[".substr($intro_suite, 6)."->art$id]");
				else $intro_suite = propre("&nbsp;[{$intro_suite}->art$id]");
		}
		$GLOBALS['toujours_paragrapher'] = $mem; 
		$result = str_replace('@@CS_SUITE@@', $intro_suite, $result);
	}
	return $result;
} // introduction()

if (!function_exists('balise_INTRODUCTION')) {

	// #INTRODUCTION
	function balise_INTRODUCTION($p) {
		$type = $p->type_requete;
		$_texte = champ_sql('texte', $p);
		if ($type == 'articles') {
		  $_chapo = champ_sql('chapo', $p);
		  $_descriptif =  champ_sql('descriptif', $p);
		  $_id = champ_sql('id_article', $p);
		} else {
		  $_chapo = "''";
		  $_descriptif =  "''";
		  $_id = 0;
		}
	//	$p->code = "calcul_introduction('$type', $_texte, $_chapo, $_descriptif)";
		$p->code = "cs_introduction('$type', $_texte, $_chapo, $_descriptif, $_id)";
	
		#$p->interdire_scripts = true;
		return $p;
	}
	
} //!function_exists('balise_INTRODUCTION') 
else spip_log("Erreur - balise_INTRODUCTION() existe déjà et ne peut pas être surchargée par le Couteau Suisse !");

?>