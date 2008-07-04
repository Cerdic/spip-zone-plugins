<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


# CONFIG 


//
// #CONFIG etendue interpretant les /, ~ et table:
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx est un tableau serialise dans spip_meta comme avec exec=cfg&cfg=xxx
//
// si xxx demarre par ~ on utilise la colonne 'extra' 
// ('cfg' sera prochainement la colonne par defaut) de spip_auteurs
// cree pour l'occasion. 
//   ~ tout court veut dire l'auteur connecte,
//   ~123 celui de l'auteur 123

// Pour utiliser une autre colonne que 'cfg', il faut renseigner @colonne
//   ~@extra/champ ou 
//   ~id_auteur@prefs/champ
//
// Pour recuperer des valeurs d'une table particuliere,
// il faut utiliser 'table:id/champ' ou 'table@colonne:id/champ'
//   table:123 contenu de la colonne 'cfg' de l'enregistrement id 123 de "table"
//   rubriques@extra:3/qqc  rubrique 3, colonne extra, champ 'qqc'
//
// "table" est un nom de table ou un raccourci comme "article"
// on peut croiser plusieurs id comme spip_auteurs_articles:6:123
// (mais il n'y a pas d'extra dans spip_auteurs_articles ...)
// Le 2eme argument de la balise est la valeur defaut comme pour la dist
//
// pour histoire
// Le 3eme argument permet de controler la serialisation du resultat
// (mais ne sert que pour le depot 'meta') qui ne doit pas deserialiser tout le temps
// même si c'est possible lorsqu'on le demande avec #CONFIG...
//
function balise_CONFIG($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$sinon = interprete_argument_balise(2,$p);
	$unserialize = sinon(interprete_argument_balise(3,$p),"false");

	// cas particulier historique (192 n'a pas #ARRAY) : |in_array{#CONFIG{toto,'',''}}
	// a remplacer par  |in_array{#CONFIG{toto/,#ARRAY}}
	// ou par |in_array{#CONFIG{toto,#ARRAY,1}}
	// il sert aussi a lire $GLOBALS['meta']['param'] qui serait un array()...
	if (($sinon === "''") AND ($unserialize === "''") AND (false === strpos('::',$arg))){
		$sinon = "array()";
		$unserialize = true;
		$arg = "'metapack::'.".$arg;
	}
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
	
	// integration du css
	// Suppression de cette inclusion des css arbo au profit d'une inclusion d'un fichier cfg.css dans le header prive
// 	$sortie .= "<style type='text/css'>\n"
// 			.  ".cfg_arbo{}\n"
// 			.  ".cfg_arbo h5{padding:0.2em 0.2em; margin:0.2em 0; cursor:pointer;}\n"
// 			.  ".cfg_arbo ul{border:1px solid #ccc; margin:0; padding:0.2em 0.5em; list-style-type:none;}\n"
// 			.  "</style>\n";

	// integration du js	
	$sortie .= "<script type='text/javascript'><!--
				
				$(document).ready(function(){
					function cfg_arbo(){
						jQuery('#cfg_arbo_$hash ul').hide();
						jQuery('#cfg_arbo_$hash h5')
						.prepend('<strong>[+] <\/strong>')
						.toggle(
						  function () {
							$(this).children('strong').text('[-] ');
							$(this).next('ul').show();
						  },
						  function () {
							$(this).children('strong').text('[+] ');
							$(this).next('ul').hide();
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
				$sortie .= cfg_affiche_sous_arborescence($tab, $val);
			elseif (false !== $v = @unserialize($val))
				$sortie .= cfg_affiche_sous_arborescence($tab, $v);
			else
				$sortie .= "<li>$tab = " . htmlentities($val) ."</li>\n";
			
		}
	} else {
		$sortie .= "<li>$nom = " . htmlentities($tableau) . "</li>";
	}
	$sortie .= "</ul>\n";
	return $sortie;	
}

?>
