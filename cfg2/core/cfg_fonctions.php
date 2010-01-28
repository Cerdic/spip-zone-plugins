<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


# CONFIG 


// #CONFIG retourne lire_config()
// 
// Le 3eme argument permet de controler la serialisation du resultat
// (mais ne sert que pour le depot 'meta') qui doit parfois deserialiser
// ex: |in_array{#CONFIG{toto,#ARRAY,1}}.
// Ceci n'affecte pas d'autres depots et |in_array{#CONFIG{toto/,#ARRAY}} sera equivalent
// car du moment qu'il y a un /, c'est le depot 'metapack' qui est appelle.
//
function balise_CONFIG($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$sinon = interprete_argument_balise(2,$p);
	$unserialize = sinon(interprete_argument_balise(3,$p),"false");

	$p->code = 'lire_config(' . $arg . ',' . 
		($sinon && $sinon != "''" ? $sinon : 'null') . ',' . $unserialize . ')';	

	return $p;
}


# CFG_CHEMIN

//
// La balise CFG_CHEMIN retourne le chemin d'une image stockee
// par cfg.
//
// cfg stocke : 'config/vue/champ.ext' (ce qu'affiche #CONFIG)
// #cfg_chemin retourne l'adresse complete : 'IMG/config/vue/champ.ext'
//
function balise_CFG_CHEMIN_dist($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$sinon = interprete_argument_balise(2,$p);
	
	$p->code = '($l = lire_config(' . $arg . ',' . 
		($sinon && $sinon != "''" ? $sinon : 'null') . ')) ? _DIR_IMG . $l : null';		
	
	return $p;
}



# CFG_ARBO

/*
 * Affiche une arborescence du contenu d'un #CONFIG
 *
 * #CFG_ARBO,
 * #CFG_ARBO{ma_meta},
 * #CFG_ARBO{~toto},
 * #CFG_ARBO{ma_meta/mon_casier},
 * #CFG_ARBO{ma_table:mon_id/mon_champ}
 *
 */
function balise_CFG_ARBO_dist($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$p->interdire_scripts = false;
	$p->code = 'cfg_affiche_arborescence(' . $arg . ')';
	return $p;
}

function cfg_affiche_arborescence($cfg='') {

	$sortie = '';
	$hash = substr(md5(rand()*rand()),0,6);

	// integration du js
	$sortie .= "<script type='text/javascript'><!--

				jQuery(document).ready(function(){
					function cfg_arbo(){
						jQuery('#cfg_arbo_$hash ul').hide();
						jQuery('#cfg_arbo_$hash h5 strong').remove();
						jQuery('#cfg_arbo_$hash h5')
						.prepend('<strong>[+] <\/strong>')
						.unbind().toggle(
						  function () {
							jQuery(this).children('strong').text('[-] ');
							jQuery(this).next('ul').show();
						  },
						  function () {
							jQuery(this).children('strong').text('[+] ');
							jQuery(this).next('ul').hide();
						  });
					}
					setTimeout(cfg_arbo,100);
				});
				// --></script>\n";

	$tableau = lire_config($cfg);
	if ($c = @unserialize($tableau)) $tableau = $c;

	if (empty($cfg)) $cfg = 'spip_meta';
	// parcours des donnees
	$sortie .=
		"<div class='cfg_arbo' id='cfg_arbo_$hash'>\n" .
		cfg_affiche_sous_arborescence($cfg, $tableau) .
		"\n</div>\n";


	return $sortie;
}

function cfg_affiche_sous_arborescence($nom, $tableau){
	$sortie = "\n<h5>$nom</h5>\n";
	$sortie .= "\n<ul>";
	if (is_array($tableau)){
		ksort($tableau);
		foreach ($tableau as $tab=>$val){
			if (is_array($val))
				$sortie .= "<li>" . cfg_affiche_sous_arborescence($tab, $val) . "</li>";
			elseif (false !== $v = @unserialize($val))
				$sortie .= "<li>" . cfg_affiche_sous_arborescence($tab, $v) . "</li>";
			else
				$sortie .= "<li>$tab = " . htmlentities($val, ENT_COMPAT, $GLOBALS['meta']['charset']) ."</li>\n";

		}
	} else {
		$sortie .= "<li>$nom = " . htmlentities($val, ENT_COMPAT, $GLOBALS['meta']['charset']) . "</li>";
	}
	$sortie .= "</ul>\n";
	return $sortie;
}

?>
