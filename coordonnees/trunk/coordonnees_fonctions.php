<?php
/**
 * Fonctions utiles au plugin Coordonnées
 *
 * @plugin     Coordonnees
 * @copyright  2013
 * @author     Marcimat / Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Coordonnees\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Fonction privée mutualisée renvoyant les couples types/chaînes de langue pour une coordonnée
 * Ou bien la chaîne de langue d'un type donné
 * Utilisée par les fonctions filtre_coordonnees_lister_types_xxx()
 *
 * adresses : RFC2426/CCITT.X520 :         dom home intl parcel postal pref work
 * numéros :  RFC2426/CCITT.X500 :         bbs car cell fax home isdn modem msg pager pcs pref video voice work
 *            RFC6350/CCITT.X520.1988 :    cell fax pager text textphone video voice x-... (iana-token)
 *                                         +dsl
 * emails :   RFC2426/IANA :               internet pref x400
 *            RFC6350/CCITT.X520+RFC5322 : home (perso) intl work (pro)
 * 
 * @param int $coordonnee
 *     coordonnée dont on veut retourner les types
 *     adresse | numero | email
 *     à éviter : adr | tel | mel
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function coordonnees_lister_types_coordonnees($coordonnee='', $type='') {

	if (!strlen($coordonnee)) return;

	// On veut définir une liste avec pour chaque objet coordonnée, ses types et les chaînes de langue correspondantes
	// Or les chaînes de langue suivent la norme : type_{coordonnee}_{type}
	// On s'en sert pour se simplifier la tâche en remplissant automatiquement le tableau

	// Définition des types
	$types = array(
		'adresse' => array('work','home','pref','postal','dom','intl','parcel'),
		'numero'  => array('voice','work','home','msg','pref','fax','cell','dsl','video','pager','bbs','modem','car','isdn','pcs'),
		'email'   => array('work','home','internet','pref','x400')
	);

	// Attention, les chaînes de langue ne sont pas "type_adresse_xxx", mais "type_adr_xxx" etc.
	// Il faut donc établir une correspondance abbréviation/coordonnée pour les chaînes de langue
	$abbr = array(
		'adr'    => 'adresse',
		'tel'    => 'numero',
		'mel'    => 'email',
		'email'  => 'email'
	);
	$coord2abbr = array_flip($abbr);

	// Vérification au cas-où
	if (!in_array($coordonnee,$abbr) and !in_array($coordonnee,$coord2abbr)) return;

	// Pour compatibilité si on utilise les abbréviations : adr etc.
	if (in_array($coordonnee,$coord2abbr))
		$coordonnee = $abbr[$coordonnee];

	// Remplissage de la liste
	foreach ($types as $coord=>$types_coord)
		foreach ($types_coord as $type_coord)
			$liste[$coord][$type_coord] = _T('coordonnees:type_'.$coord2abbr[$coordonnee].'_'.$type_coord);

	// Envoyer aux plugins pour qu'ils complètent (ou altèrent) la liste
	$liste = pipeline('types_coordonnees', $liste);

	if ($type)
		return $liste[$coordonnee][$type];
	else 
		return $liste[$coordonnee];
}


/*
 * Filtre renvoyant les types d'adresses possibles, et leurs chaînes de langue
 *
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_adresses($type='') {
	return coordonnees_lister_types_coordonnees('adresse',$type);
}

/*
 * Filtre renvoyant les types de numéros possibles, et leurs chaînes de langue
 *
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_numeros($type='') {
	return coordonnees_lister_types_coordonnees('numero',$type);
}

/*
 * Filtre renvoyant les types d'emails possibles, et leurs chaînes de langue
 *
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_emails($type='') {
	return coordonnees_lister_types_coordonnees('email',$type);
}

/*
 * Fonction privee mutualisée utilisée par les filtres logo_type_xx
 * Renvoit soit une balise <img> si elle est trouvée, soit une balise <abbr>
 * 
 * nomenclature des fichiers :
 * - avec le paramètre $coordonnee : type_{$coordonnee}_{$type}.ext
 *   ex: type_adresse_home.png
 * - sans le paramètre $coordonnee : type_{$type}.ext
 *   ex: type_home.png
 * 
 * @param string $coordonnee
 *     coordonnée dont on veut retourner le logo
 *     adresse | numero | email
 *     à éviter : adr | tel | mel
 * @param string $type
 *     le type de coordonnée (dom, home, work etc.)
 * @return string
 *     balise <img> ou <abbr>
**/
function logo_type_($coordonnee='', $type='') {

	include_spip('inc/utils');
	include_spip('inc/filtres');
	global $formats_logos;

	// compatibilité : correspondance abbréviation/coordonnée
	$abbr = array(
		'adr'    => 'adresse',
		'tel'    => 'numero',
		'mel'    => 'email'
	);
	$coord2abbr = array_flip($abbr);
	if (in_array($coordonnee,$coord2abbr))
		$coordonnee = $abbr[$coordonnee];

	// on récupère les couples types/chaînes de langues
	$type = strtolower($type);
	$types = coordonnees_lister_types_coordonnees($coordonnee);
	if (!is_array($types)) $types = array();

	// chaîne de langue
	$langue_coordonnee = $types[$type];
	$langue_perso = _T("perso:type_${type}",'',array('force'=>false));
	$langue = ($type) ? ($coordonnee) ? $langue_coordonnee : $langue_perso : '';

	// fichier image
	$fichier_coordonnee = "type_${coordonnee}_${type}";
	$fichier_abbr = "type_" .$abbr[$coordonnee] ."_${type}";
	$fichier_perso = "type_${type}";
	foreach ($formats_logos as $ext) {
		if ($coordonnee) {
			if     ($image = chemin_image($fichier_coordonnee.'.'.$ext)) break;
			elseif ($image = chemin_image($fichier_abbr.'.'.$ext)) break;
		} else {
			if     ($image = chemin_image($fichier_perso.'.'.$ext)) break;
		}
	}

	if($langue){
		if (isset($image))
			return inserer_attribut(filtre_balise_img_dist($image,$type),'title',$langue);
		elseif ($type)
			return inserer_attribut(inserer_attribut(wrap($langue,'<abbr>'),'title',$type),'class','type');
		else
			return '';
	} else
		return '';
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'une adresse
 *
 * @param string $type_adresse    RFC2426/CCITT.X520 : dom home intl parcel postal pref work
 * @return string                 balise <img> ou <abbr>
**/
function filtre_logo_type_adresse($type_adresse) {
	return logo_type_('adresse', $type_adresse);
}
function filtre_logo_type_adr($type_adresse) {
	return logo_type_('adresse', $type_adresse);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un numero de tel
 *
 * @param string $type_tel    RFC2426/CCITT.X500 : bbs car cell fax home isdn modem msg pager pcs pref video voice work
 *                            RFC6350/CCITT.X520.1988 : cell fax pager text textphone video voice x-... (iana-token)
 *                            + : dsl
 * @return string             balise <img> ou <abbr>
**/
function filtre_logo_type_numero($type_numero) {
	return logo_type_('numero', $type_numero);
}
function filtre_logo_type_tel($type_numero) {
	return logo_type_('numero', $type_numero);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un email
 *
 * @param string $type_adresse    RFC2426/IANA : internet pref x400
 * @return string                 balise <img> ou <abbr>
**/
function filtre_logo_type_email($type_email) {
	return logo_type_('email', $type_email);
}

/*
 * filtre renvoyant une balise <img> ou <abbr> d'apres le type d'un mel (email)
 *
 * @param string $type_adresse    RFC6350/CCITT.X520+RFC5322 : home (perso) intl work (pro)
 * @return string                 balise <img> ou <abbr>
**/
function filtre_logo_type_mel($type_email) {
	return logo_type_('email', $type_email);
}

?>
