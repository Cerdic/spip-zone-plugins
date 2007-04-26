<?php

// surcharge pour le calcul de la balise #INTRODUCTION

if (defined('_INTRODUCTION_LGR')) {
 if (!function_exists('introduction')) {

	if (!defined('_INTRODUCTION_SUITE')) define('_INTRODUCTION_SUITE', '&nbsp;(...)');

	function introduction($type, $texte, $chapo, $descriptif) {
		switch ($type) {
			case 'articles':
				# si descriptif contient juste des espaces ca produit une intro vide, 
				# c'est une fonctionnalite, pas un bug
				if (strlen($descriptif))
					return propre($descriptif);
				else if (substr($chapo, 0, 1) == '=')	// article virtuel
					return '';
				else
					return PtoBR(propre(supprimer_tags(couper_intro($chapo."\n\n\n".$texte, round(500*_INTRODUCTION_LGR/100), _INTRODUCTION_SUITE))));
				break;
			case 'breves':
				return PtoBR(propre(supprimer_tags(couper_intro($texte, round(300*_INTRODUCTION_LGR/100), _INTRODUCTION_SUITE))));
				break;
			case 'forums':
				return PtoBR(propre(supprimer_tags(couper_intro($texte, round(600*_INTRODUCTION_LGR/100), _INTRODUCTION_SUITE))));
				break;
			case 'rubriques':
				if (strlen($descriptif))
					return propre($descriptif);
				else
					return PtoBR(propre(supprimer_tags(couper_intro($texte, round(600*_INTRODUCTION_LGR/100), _INTRODUCTION_SUITE))));
				break;
		}
	} // introduction()
 } //!function_exists('introduction') 
 else spip_log("Erreur - introduction() existe déjà et ne peut pas être surchargée par Tweak SPIP !");
} //defined('_INTRODUCTION_LGR')
?>