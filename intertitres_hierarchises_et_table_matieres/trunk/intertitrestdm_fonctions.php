<?php
/*
 *   +----------------------------------+
 *    Nom du Filtre :    extrait_titres,extrait_emphaseforte...
 *   +----------------------------------+
 *    Date : 19 décembre 2006
 *    Auteur :  Bertrand Marne (extraction à sciencesnat point org)
 *   +-------------------------------------+
 *   Fonctions de ces filtres :
 *   Ces filtres extraient des infos des articles comme:
 *   Les titres de parties, les mots en emphase ou les URL
 *   Il sert à faire ressortir les éléments sémantiques (taggés
 *   par les raccourcis Spip, donc s'utilise avec #TEXTE*)
 *   +-------------------------------------+
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
*/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function extrait_titres($texte) {
	preg_match_all('/\{\{\{(.*?)\}\}\}/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list ($key, $val) = each($matches[1])) {
		$sortie .= '-'.$val."\n";
	};
	return $sortie;
}

function extrait_emphaseforte($texte, $guillemets='fr') {
	// protection des tites
	$texte = preg_replace('/(\{\{\{)(.*?)(\}\}\})/', '', $texte);
	// c'est parti
	preg_match_all('/\{\{(.*?)\}\}/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list($key, $val) = each($matches[1])) {
		$sortie .= '«'.$val.'»; ';
	};
	return $sortie;
}

function extrait_emphase($texte, $guillemets='fr') {
	// protection des titres et emphases fortes
	$texte = preg_replace('/(\{\{)(.*?)(\}\})/', '', $texte);
	// c'est parti
	preg_match_all('/\{(.*?)\}/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list($key, $val) = each($matches[1])) {
		$sortie .= '«'.$val.'»; ';
	};
	return $sortie;
}

function extrait_liens($texte) {
	// protection des codes
	$texte = preg_replace('/(<code>)(.*?)(<\/code>)/', '', $texte);
	// protection des ancres
	$texte = preg_replace('/(\[.*?\<-])/', '', $texte);
	// protection des notes
	$texte = preg_replace('/(\[\[)(.*?)(\]\])/', '', $texte);
	// c'est parti
	preg_match_all('/(\[.*?\])/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list($key, $val) = each($matches[1])) {
		$sortie .= $val."\n\n";
	};
	return $sortie;
}

/*
 * pour le 3ieme intertitre produit par ce balisage SPIP : {{{** foo bar }}}
 * le plugin seul produit la ligne HTML suivante : 
 *  <h4 class="spip"><a id="foo-bar-2" name="foo-bar-2"></a><a id="a2.1" name="a2.1"> foo bar </h4>
 * tandis qu'en presence du plugin Sommaire Automatique on a ce HTML (en une ligne et non deux) : 
 *  <h4 class="spip" id="foo-bar"><a id="foo-bar-2" name="foo-bar-2"></a><a id="a2.1" name="a2.1"> foo bar
 *  <a class="sommaire-back sommaire-back-6" href="#s-foo-bar" title="Retour au sommaire"></a></h4>
 */
function extrait_un_titre($texte, $ancre='   #  ? ! ') {
	spip_log('titre goo', 'itdm');
	if ( preg_match("#<h(\d) class=\"spip\".+<a id=\"a$ancre\" name=\"a$ancre\"></a>(.*?)</h\\1>#",
		$texte, $matches) ) {
		spip_log('titre id: a'.$ancre, 'itdm');
		return textebrut($matches[2]);
	}
	preg_match("#<h(\d) class=\"spip\".+id=\".*$ancre.*\".*>(.*?)</h\\1>#",
		$texte, $matches);
	spip_log('titre id: '.$ancre, 'itdm');
	return textebrut($matches[2]);
}

function extrait_de_texte($texte, $debut=0, $taille=20) {
	$mots = explode(' ', textebrut($texte));
	$extrait = implode(' ', array_slice($mots, $debut, $taille));
	return $extrait;
}

/*
 * pour le 3ieme intertitre produit par ce balisage SPIP : {{{## foo bar }}}
 * le plugin seul produit la ligne HTML suivante : 
 *  <h4 class="spip"><a id="foo-bar-2" name="foo-bar-2"></a><a id="a2.1" name="a2.1"> 1.2- foo bar </h4>
 * tandis qu'en presence du plugin Sommaire Automatique on a ce HTML (en une ligne et non deux) : 
 *  <h4 class="spip" id="t1-2-foo-bar"><a id="foo-bar-2" name="foo-bar-2"></a><a id="a2.1" name="a2.1"> 1.2- foo bar
 *  <a class="sommaire-back sommaire-back-6" href="#s-t1-2-foo-bar" title="Retour au sommaire"></a></h4>
 */
function extrait_partie($texte, $ancre='   #  ? ! ', $debut=0, $taille='') {
	spip_log('partie goo', 'itdm');
	if ( preg_match('#<h(\d) class="spip".+<a id="a' . $ancre . '" name="a' . $ancre . 
		'"></a>.*</h\\1>(.*?)<h\\1 class="spip".*>#s', $texte, $matches) ) {
		spip_log('partie id: a'.$ancre, 'itdm');
		$partie = $matches[2];
	} else {
		preg_match('#<h(\d) class="spip".+id=".*' . $ancre .
			'.*".+</h\\1>(.*?)<h\\1 class="spip".*>#s', $texte, $matches);
		spip_log('partie id: '.$ancre, 'itdm');
		$partie = $matches[2];
	}
	if ( !$taille ) {
		$taille = str_word_count($partie);
	}
	$extrait = extrait_de_texte($partie, $debut, $taille);
	return $extrait;
}

function nettoie_des_modeles($texte) {
	// retire les modeles du plugin pour éviter les plantages circulaires
	$texte = preg_replace('/<(extrait|extrait_partie|renvoi|table_des_matieres)(.*?)>/', '', $texte);
	// retire les notes du texte, pour éviter les doublons de notes !
	$texte = preg_replace('/(\[\[)(.*?)(\]\])/', '', $texte);
	return $texte;
}

function table_des_matieres($texte, $tdm, $url) {
	return IntertitresTdm_table_des_matieres($texte, $tdm, $url);
}
