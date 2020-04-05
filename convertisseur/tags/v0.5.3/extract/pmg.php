<?php

// Sait-on extraire ce format ?
$GLOBALS['extracteur']['pmg'] = 'extracteur_pmg';

function extracteur_pmg($fichier, &$charset) {
	$charset = 'utf-8';
	if (lire_fichier($fichier, $texte)) {
		return convertir_extraction_pmg($texte);
	}
}

function convertir_extraction_pmg($c) {
	$item = convertir_pmg($c);
	$texte = extracteur_preparer_insertion($item);
	return $texte ;
}

function convertir_pmg($u) {

	// On recoit directement des <artikel>

	include_spip('inc/charsets');
	include_spip('inc/filtres');

	// debug xml
	$m['xml'] = "<pre style='border:1px solid #cccccc; padding:5px;height:300px;overflow:auto'>" . entites_html($u) . "</pre>" ;

	// passer en utf-8 en nettoyant les entites
	$u = unicode2charset(html2unicode($u)) ;

	// espaces utf-8 , inutile car on à l'option /u
	//$u = str_replace("\xC2\xA0", " ", $u);

	$u = trim($u);

	// id pdf <artikel-pdf>
	$m['id_artikel'] = trim(str_replace(".pdf","", textebrut(extraire_balise($u, "artikel_pdf"))));

	// pages
	//	<seite_start>
	//	<seite_ende>

	$m['pages'] = trim(textebrut(extraire_balise($u, "seite_start")) . " " . textebrut(extraire_balise($u, "seite_ende")));
	$p = explode(" ",$m['pages']) ;
	if($p[0] < 10 and !preg_match("/^0/", $m['pages']))
		$m['pages'] = "0" . $m['pages'] ;

	// <datum>11012018</datum>
	$datum = textebrut(extraire_balise($u, "datum")) ;
	$m['date'] = substr($datum, 4 ,4) . "-" . substr($datum, 2 ,2) . "-" . substr($datum, 0 ,2) . " 00:00:00" ;

	// url <weblink>...</weblink>
	$m['url'] = textebrut(extraire_balise($u, "weblink")) ;

	// illustrations
	//<abbildung>
	//	<foto>p846705.jpg</foto>
	//	<fotograf>MAJDI MOHAMMED/ap</fotograf>
	//	<beschriftung>Anti-Trump-Proteste in Nablus, 22. Dezember 2017</beschriftung>
	//</abbildung>"
	//<abbildung>
	//<infografik>p846706.jpg</infografik>
	//</abbildung>

	/*
	<ins class='documents'>{"id_document":"21703","id_vignette":"0","titre":"Qui reconna\u00eet la Palestine ?","descriptif":"","fichier":"png\/3b-reconnaissance.png","taille":"90179","date":"2018-01-23 12:05:39","distant":"non","extension":"png","contenu":"","extrait":"non","id_source":null,"vieux_url":"","statut":"prop","date_publication":"0000-00-00 00:00:00","brise":"0","credits":"","media":"image","id_objet":"58351","objet":"article","vu":"non","rang_lien":"0"}</ins>

// Si carte :
<ins class='mots_cles'>04. type::carte ou graphique</ins>

	*/

	$illustrations = extraire_balises($u,"abbildung");
	foreach($illustrations as $i){
		$fichier = textebrut(extraire_balise($i,"foto"));
		$credit = textebrut(extraire_balise($i,"fotograf"));
		$legende = textebrut(extraire_balise($i,"beschriftung"));
		$infographie = textebrut(extraire_balise($i,"infografik"));
		$taille= "1" ;

		if($infographie)
			$m['infographie'] = "oui" ;

		$file = $infographie.$fichier ;

		$m['documents'] = "{\"fichier\":\"$file\",\"taille\":\"$taille\",\"distant\":\"non\",\"extension\":\"jpg\",\"credits\":\"$credit\",\"descriptif\":\"$legende\"}" ;

		$u = str_replace($i,"",$u);
	}

	// <titel>Die Wahhabiten</titel> <untertitel>Monarchie und Religion in Saudi-Arabien </untertitel>

	$m['titre'] = $datum = textebrut(extraire_balise($u, "titel")) ;
	$m['soustitre'] = $datum = textebrut(extraire_balise($u, "untertitel")) ;

	// <autor_name>
	$auteurs = extraire_balises($u, "autor_name") ;
	foreach($auteurs as &$a)
		$a = textebrut($a);
	$m['auteurs'] = implode("@@",$auteurs) ;

	// recaler le traducteur
	// <absatz>Aus dem Französischen von Claudia Steinitz<i/></absatz>
	preg_match('~<absatz>Aus de.*von.*</absatz>~Uu',$u,$trad);
	$u = str_replace($trad[0],"",$u);
	$m['traducteur'] = textebrut($trad[0]);
	//var_dump($trad);

	// recaler l'auteur au debut
	// <absatz>von <strong>Nabil Mouline</strong></absatz>
	$u = preg_replace('~<absatz>von <strong>(.*)</strong></absatz>~Uu',"",$u);
	// variante
	$u = preg_replace('~<absatz>\R*\s*<strong>von (.*)</strong>\R*\s*</absatz>~Uums',"",$u);

	// recaler la bio
	preg_match('~<absatz>'.$auteurs[0].'.*</absatz>~Uu',$u,$bio);
	$u = str_replace($bio[0],"",$u);
	$m['signature'] = textebrut($bio[0]);

	// recaler les notes
	// <absatz><sup>1</sup> Siehe Nabil Mouline, „Traditionalismus und Herrschaft“, <i>Le Monde diplomatiqu</i>e, April 2015.</absatz>
	$u = preg_replace('~<sup>(\d+)</sup>~Uu',"(\\1)",$u);

	// paragraphes
	$u = str_replace("<absatz>","",$u);
	$u = str_replace("</absatz>","\n\n",$u);

	// encadrés
	//<box>
	//	<box-titel>Im Licht des Völkerrechts</box-titel>
	//	<box-text>
	//	</box-text>
	//</box>

	$encadres = extraire_balises($u,"box");
	foreach($encadres as $e){
		$titre_encadre = textebrut(extraire_balise($e,"box_titel"));
		$texte_encadre = trim(preg_replace(",</?box_text>,","",extraire_balise($e,"box_text"))) . "\n" ;
		$u = str_replace($e,"\n\n<quote>\n{{{$titre_encadre}}}\n\n$texte_encadre\n</quote>",$u);
	}

	// Inter
	// <zwischentitel>Symbolische Reförmchen stören die Ulemas nicht</zwischentitel>
	$u = str_replace("<zwischentitel>","{{{",$u);
	$u = str_replace("</zwischentitel>","}}}\n\n",$u);

	$m['texte'] = trim(preg_replace(",</?text>,","",extraire_balise($u,"text"))) . "\n" ;

	foreach($champs  = array("texte", "chapo", "signature", "credit") as $t){
			// texte spip
			if($m[$t]){

				// itals
				$m[$t] = str_replace("<i>","{",$m[$t]);
				$m[$t] = str_replace("</i>","}",$m[$t]);

				// gras
				$m[$t] = str_replace("<strong>","{{",$m[$t]);
				$m[$t] = str_replace("</strong>","}}",$m[$t]);
				// espaces en debut de lignes
				$m[$t] = preg_replace(",^ +,im", "", $m[$t]);
			}
	}

	// envoyer dans un pipeline pour traitements persos.

	// traitements persos en pipeline maison sur $c avant d'écrire le fichier converti
	if(find_in_path('convertisseur_perso.php'))
		include_spip("convertisseur_perso");
	if (function_exists('nettoyer_conversion')){
		$m = nettoyer_conversion($m);
	}

	return $m ;
}

