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
// #SAISIE{input,toto,oui}
// = 
// #SAISIE{input}{nom=toto}{obligatoire=oui}
// =
// #INCLURE{fond=saisies/_base}
//	{type_saisie=input}
//	{nom=toto}{valeur=#ENV{toto}}
//	{erreurs}
//
function balise_SAISIE_dist($p) {

	include_spip('inc/interfaces');
	$param = array();
	
	$type_saisie = array_shift($p->param);
	// ajouter {erreurs} {fond=saisies/_base} et {type_saisie=xxx}
	array_unshift($p->param, balise_saisie_param('erreurs'));
	array_unshift($p->param, balise_saisie_param('fond', 'saisies/_base'));
	array_unshift($p->param, balise_saisie_param('type_saisie', '', $type_saisie[1]));
	
	// cas #SAISIE{input,toto,oui}
	if (isset($type_saisie[2])) {
		array_unshift($p->param, balise_saisie_param('nom', '', $type_saisie[2]));
	}
	if (isset($type_saisie[3])) {
		array_unshift($p->param, balise_saisie_param('obligatoire', '', $type_saisie[3]));
	}
	
	// cas #SAISIE{input}{nom=toto}{obligatoire=oui}
	// retrouver le nom {nom=xx} pour le passer a valeur {valeur=#ENV{xx}}
	foreach ($p->param as $c=>$q) {
		if ((strpos($q[1][0]->texte, 'nom') === 0)
		and preg_match("/^nom\s*=/" , $q[1][0]->texte)) {
			$me = $q[1];
			// {nom=toto}
			if (count($me) == 1) {
				list(,$nom) = explode('=', $me[0]->texte, 2); // supprimer nom=
				$nom = balise_saisie_param(trim($nom));
			}
			// {nom=#BALISE...}
			else {
				$nom = array(0=>'', 1=>array($me[1]));
			}
			// ajouter la trouvaille
			$valeur = new Champ;
			$valeur->type = 'champ';
			$valeur->nom_champ='ENV';
			$valeur->param = array($nom);
			array_unshift($p->param, balise_saisie_param('valeur', '', array($valeur)));
			break;
		}
	}

//	print_r($p); die();
	
	if(function_exists('balise_INCLURE'))
		return balise_INCLURE($p);
	else
		return balise_INCLURE_dist($p);	
}

// balise_saisie_param(nom) = {nom}
// balise_saisie_param(nom, 'coucou') = {nom=coucou}
// balise_saisie_param(nom, '', $params) = {nom=#BALISE}
function balise_saisie_param($nom, $valeur=null, $balise=null) {
	$s = new Texte;
	$s->type="texte";
	if (is_null($valeur)) {
		$s->texte = $nom;
	} else {
		$s->texte = "$nom=$valeur";
	}
	$s->ligne=0;
	if (!$balise) {
		return array(0=>'', 1=>array(0=>$s));
	} else {
		$res = array_merge(array(0=>$s), $balise);
		return array(0=>'', 1=>$res);
	}
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
				$sortie .= "<li>" . cfg_affiche_sous_arborescence($tab, $val) . "</li>";
			elseif (false !== $v = @unserialize($val))
				$sortie .= "<li>" . cfg_affiche_sous_arborescence($tab, $v) . "</li>";
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
