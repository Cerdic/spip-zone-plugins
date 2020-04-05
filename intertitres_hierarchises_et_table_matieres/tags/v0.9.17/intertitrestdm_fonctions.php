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

/*
 * règles : https://fr.wikipedia.org/wiki/Guillemet#Autres_langues_et_autres_pays
 * entités : https://www.w3.org/wiki/Common_HTML_entities_used_for_typography
*/
function bien_guillemeter($extrait, $languepays='fr') {
	switch ($languepays) {
	case 'fr': // français
	case 'pt': // Portugal
		$niveau1_ouvrant = '&laquo;&nbsp;';
		$niveau1_fermant = '&nbsp;&raquo;';
		$niveau2_ouvrant = '&ldquo;';
		$niveau2_fermant = '&rdquo;';
		$niveau3_ouvrant = '&rsquo;';
		$niveau3_fermant = '&lsquo;';
		break;
	case 'en': // anglais
	case 'eo': // esperanto
	case 'nl': // néerlandais
	case 'br': // Brésil
		$niveau1_ouvrant = '&ldquo;';
		$niveau1_fermant = '&rdquo;';
		$niveau2_ouvrant = '&lsquo;';
		$niveau2_fermant = '&rsquo;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	case 'es': // Espagne
		$niveau1_ouvrant = '&laquo;';
		$niveau1_fermant = '&raquo;';
		$niveau2_ouvrant = '&ldquo;';
		$niveau2_fermant = '&rdquo;';
		$niveau3_ouvrant = '&lsquo;';
		$niveau3_fermant = '&rsquo;';
		break;
	case 'it': // italien
		$niveau1_ouvrant = '&laquo;&nbsp;';
		$niveau1_fermant = '&nbsp;&raquo;';
		$niveau2_ouvrant = '&ldquo;';
		$niveau2_fermant = '&rdquo;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	case 'da': // dannois
	case 'de': // allemand
	case 'hr': // croate
	case 'sl': // slovène
		$niveau1_ouvrant = '&raquo;';
		$niveau1_fermant = '&laquo;';
		$niveau2_ouvrant = '&gt;';
		$niveau2_fermant = '&lt;';
		$niveau3_ouvrant = '&sbquo;';
		$niveau3_fermant = '&lsquo;';
		break;
	case 'bg': // bulgare
	case 'cs': // tchèque
		$niveau1_ouvrant = '&bdquo;';
		$niveau1_fermant = '&ldquo;';
		$niveau2_ouvrant = '&gt;';
		$niveau2_fermant = '&lt;';
		$niveau3_ouvrant = '&sbquo;';
		$niveau3_fermant = '&lsquo;';
		break;
	case 'hu': // hongrois
	case 'pl': // polonais
		$niveau1_ouvrant = '&bdquo;';
		$niveau1_fermant = '&rdquo;';
		$niveau2_ouvrant = '&gt;';
		$niveau2_fermant = '&lt;';
		$niveau3_ouvrant = '&sbquo;';
		$niveau3_fermant = '&lsquo;';
		break;
	case 'no': // norvégien
		$niveau1_ouvrant = '&laquo;&nbsp;';
		$niveau1_fermant = '&nbsp;&raquo;';
		$niveau2_ouvrant = '&lt;';
		$niveau2_fermant = '&gt;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	case 'fi': // finnois
	case 'sv': // suédois
		$niveau1_ouvrant = '&ldquo;';
		$niveau1_fermant = '&ldquo;';
		$niveau2_ouvrant = '&lsquo;';
		$niveau2_fermant = '&lsquo;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	case 'ch': // Suisse
	case 'li': // Liechtenstein
		$niveau1_ouvrant = '&laquo;';
		$niveau1_fermant = '&raquo;';
		$niveau2_ouvrant = '&ldquo;';
		$niveau2_fermant = '&rdquo;';
		$niveau3_ouvrant = '&rsquo;';
		$niveau3_fermant = '&lsquo;';
		break;
	case 'be': // biélorusse
	case 'ru': // russe
	case 'uk': // ukrainien
		$niveau1_ouvrant = '&laquo;';
		$niveau1_fermant = '&raquo;';
		$niveau2_ouvrant = '&bdquo;';
		$niveau2_fermant = '&ldquo;';
		$niveau3_ouvrant = '&sbquo;';
		$niveau3_fermant = '&rsquo;';
		break;
	case 'jp': // japonais
		$niveau1_ouvrant = '&#x300c;';
		$niveau1_fermant = '&#x300d;';
		$niveau2_ouvrant = '&#xff62;';
		$niveau2_fermant = '&#xff63;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	case 'zh': // mandarin
		$niveau1_ouvrant = '&#x300a;';
		$niveau1_fermant = '&#x300b;';
		$niveau2_ouvrant = '&#x2329;';
		$niveau2_fermant = '&#x232a;';
		$niveau3_ouvrant = '&Prime;';
		$niveau3_fermant = '&Prime;';
		break;
	default: // pandunia ; informatique
		$niveau1_ouvrant = '&Prime;';
		$niveau1_fermant = '&Prime;';
		$niveau2_ouvrant = '&prime;';
		$niveau2_fermant = '&prime;';
		$niveau3_ouvrant = '`';
		$niveau3_fermant = '`';
		break;
	}
	// décalage des niveaux 2 d'abord
	$extrait = str_replace(
		array($niveau2_ouvrant, $niveau2_fermant),
		array($niveau3_ouvrant, $niveau3_fermant),
		htmlentities($extrait, ENT_NOQUOTE|ENT_SUBSTITUTE)
	) ;
	// décalage des niveaux 1 ensuite
	$extrait = str_replace(
		array($niveau1_ouvrant, $niveau1_fermant),
		array($niveau2_ouvrant, $niveau2_fermant),
		$extrait ) ;
	// encadrement de l'ensemble enfin
	return $niveau1_ouvrant.$extrait.$niveau1_fermant;
}

function extrait_emphaseforte($texte, $type_guillemets='fr') {
	// protection des tites
	$texte = preg_replace('/(\{\{\{)(.*?)(\}\}\})/', '', $texte);
	// c'est parti
	preg_match_all('/\{\{(.*?)\}\}/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list($key, $val) = each($matches[1])) {
		$sortie .= bien_guillemeter($val, $type_guillemets) . ' ; ';
	};
	return $sortie;
}

function extrait_emphase($texte, $type_guillemets='fr') {
	// protection des titres et emphases fortes
	$texte = preg_replace('/(\{\{)(.*?)(\}\})/', '', $texte);
	// c'est parti
	preg_match_all('/\{(.*?)\}/', $texte, $matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while (list($key, $val) = each($matches[1])) {
		$sortie .= bien_guillemeter($val, $type_guillemets) . ' ; ';
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
