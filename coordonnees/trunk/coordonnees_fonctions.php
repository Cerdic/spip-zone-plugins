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

/**
 * Lister l'ensemble des types de liaisons d'une coordonnée, ou la chaîne de langue d'un type de liaison donné.
 * 
 * Fonction privée mutualisée utilisée par les fonctions `filtre_coordonnees_lister_types_xxx()`.
 * Si on veut tous les types, ignorer le paramètre `$type` : la fonction renvoie un tableau contenant les couples types/chaînes de langue de la coordonnée.
 * Si on veut un seul type en utilisant le paramètre éponyme, elle renvoie la chaîne de langue du type donné.
 *
 * adresses : RFC2426/CCITT.X520 :         dom home intl parcel postal pref work
 * numéros :  RFC2426/CCITT.X500 :         bbs car cell fax home isdn modem msg pager pcs pref video voice work
 *            RFC6350/CCITT.X520.1988 :    cell fax pager text textphone video voice x-... (iana-token)
 *                                         +dsl
 * emails :   RFC2426/IANA :               internet pref x400
 *            RFC6350/CCITT.X520+RFC5322 : home (perso) intl work (pro)
 *
 * @note
 * Quand la paramètre `$type` n'est pas `NULL`, mais qu'il est vide,
 * ça veut dire qu'on a utilisé `#TYPE|coordonnees_lister_types_xxx` avec un `#TYPE` vide.
 * Dans ce cas là il ne faut rien retourner.
 *
 * @param string $coordonnee
 *     Coordonnée dont on veut retourner les types
 *     adresse | numero | email
 *     à éviter : adr | tel | mel
 * @param string $type
 *     Type de liaison dont on veut retourner la chaîne de langue
 *     A utiliser lorsqu'on ne veut retourner qu'une seule chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
 *     ou type tel quel si on ne trouve pas sa chaîne de langue
 */
function coordonnees_lister_types_coordonnees($coordonnee='', $type=null) {

	// cf. note
	if (!strlen($coordonnee) or (!is_null($type) and !strlen($type))) return;

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
	// Il faut donc établir une correspondance abbréviation/coordonnée
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

	// Par défaut, renvoyer un tableau de tous les types
	if (is_null($type))
		return $liste[$coordonnee];
	// S'il y a un type, renvoyer sa chaîne de langue ou à défaut, tel quel
	else if ($type)
		if ($langue=$liste[$coordonnee][$type])
			return $langue;
		else
			return $type;
	else return;

}


/*
 * Filtre renvoyant les couples types d'adresses/chaînes de langue ou la chaîne de langue d'une type en particulier
 *
 * @note
 * Quand on veut lister les types d'adresses depuis un squelette, utiliser
 * ```#EVAL{null}``` au lieu de ```#REM```
 *
 * @uses coordonnees_lister_types_coordonnees()
 *
 * @filtre
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_adresses($type=null) {
	return coordonnees_lister_types_coordonnees('adresse',$type);
}

/*
 * Filtre renvoyant les couples types de numéros/chaînes de langue ou la chaîne de langue d'une type en particulier
 *
 * @note
 * Quand on veut lister les types de numéros depuis un squelette, utiliser
 * ```#EVAL{null}``` au lieu de ```#REM```
 *
 * @uses coordonnees_lister_types_coordonnees()
 *
 * @filtre
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_numeros($type=null) {
	return coordonnees_lister_types_coordonnees('numero',$type);
}

/*
 * Filtre renvoyant les couples types d'emails/chaînes de langue ou la chaîne de langue d'une type en particulier
 *
 * @note
 * Quand on veut lister les types d'emails depuis un squelette, utiliser
 * ```#EVAL{null}``` au lieu de ```#REM```
 *
 * @uses coordonnees_lister_types_coordonnees()
 *
 * @filtre
 * @param string $type
 *     Type dont on veut retourner la chaîne de langue
 * @return array|int
 *     Couples types/chaînes de langues
 *     ou chaîne de langue d'un type donné
**/
function filtre_coordonnees_lister_types_emails($type=null) {
	return coordonnees_lister_types_coordonnees('email',$type);
}

/**
 * Affichage du type de liaison d'une coordonnée
 *
 * Fonction privee mutualisée utilisée par les filtres logo_type_xx
 *
 * @note
 * Nomenclature des fichiers d'images :
 *
 * - avec le paramètre `$coordonnee` : `type_${coordonnee}_${type}.ext`
 *   ex: `type_adresse_home.png`
 * - sans le paramètre `$coordonnee` : `type_${type}.ext`
 *   ex: `type_home.png`
 *
 * @note
 * http://www.alsacreations.com/tuto/lire/1222-microformats-design-patterns.html
 * http://www.alsacreations.com/tuto/lire/1223-microformats-composes.html
 *
 * @param string $coordonnee
 *     Suffixe du du filtre logo_type_${coordonnee} appelant ;
 *     Infixe du logo "type_${coordonnee}_${type}.???" correspondant ;
 *     adresse | numero | email
 *     à éviter : adr | tel | mel
 * @param string $type
 *     Valeur associée transmise par le filtre logo_type_${coordonnee} ;
 *     Suffixe du logo "type_${coordonnee}_${type}.???" correspondant ;
 *     Correspond au "type" de liaison de la la coordonnée (home, work, etc.)
 * @return string
 *     Balise `<IMG>` ou `<ABBR>` (sinon),
 *     avec classes semantiques micro-format et traduction des valeurs clés RFC2426
 */
function logo_type_($coordonnee='', $type='') {

	include_spip('inc/utils');
	include_spip('inc/filtres');
	global $formats_logos;

	// compatibilité : correspondance abbréviation/coordonnée
	$abbr = array(
		'adr'    => 'adresse',
		'tel'    => 'numero',
		'mel'    => 'email',
		'email'  => 'email'
	);
	$coord2abbr = array_flip($abbr);
	if (in_array($coordonnee,$coord2abbr))
		$coordonnee = $abbr[$coordonnee];

	// chaîne de langue
	$type = strtolower($type);
	$langue_coordonnee = coordonnees_lister_types_coordonnees($coordonnee,$type);
	$langue_perso = _T("perso:type_${type}",'',array('force'=>false));
	$langue = ($type) ? ($coordonnee) ? $langue_coordonnee : $langue_perso : '';

	// fichier image
	$fichier_coordonnee = "type_${coordonnee}_${type}";
	$fichier_abbr = "type_" .$coord2abbr[$coordonnee] ."_${type}";
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

/**
 * Filtre d'affichage du type d'une adresse
 *
 * @uses logo_type_()
 * @filtre
 *
 * @param string $type_adresse
 *     Valeur du type de liaison (cf. logo_type_).
 *     Les valeurs nativement prises en compte sont les codes normalisés
 *     CCITT.X520/RFC2426 (section 3.2.1) : dom home intl parcel postal pref work
 * @return string
 *     Balise HTML micro-format (cf. logo_type_)
 */
function filtre_logo_type_adresse($type_adresse) {
	return logo_type_('adresse', $type_adresse);
}
/**
 * @deprecated
 * @uses filtre_logo_type_adresse()
 */
function filtre_logo_type_adr($type_adresse) {
	return filtre_logo_type_adresse($type_adresse);
}

/**
 * Filtre d'affichage du type d'un numero
 *
 * @uses logo_type_()
 * @filtre
 *
 * @param string $type_numero
 *     Valeur du type de liaison (cf. logo_type_).
 *     Les valeurs nativement prises en compte sont les codes normalisés
 *     CCITT.X500/RFC2426 (section 3.3.1) :      bbs car cell fax home isdn modem msg pager pcs pref video voice work
 *     CCITT.X520.1988/RFC6350 (section 6.4.1) : cell fax pager text textphone video voice x-... (iana-token)
 *     ainsi que :                               dsl
 *     (<http://fr.wikipedia.org/wiki/Digital_Subscriber_Line#Familles>)
 * @return string
 *     Balise HTML micro-format (cf. logo_type_)
 */
function filtre_logo_type_numero($type_numero) {
	return logo_type_('numero', $type_numero);
}
/**
 * @deprecated
 * @uses filtre_logo_type_numero()
 */
function filtre_logo_type_tel($type_numero) {
	return filtre_logo_type_numero($type_numero);
}

/**
 * Filtre d'affichage du type d'un courriel
 *
 * @uses logo_type_()
 * @filtre
 *
 * @param string $type_email
 *     Valeur du type de liaison (cf. logo_type_).
 *     Les valeurs nativement prises en compte sont les codes normalisés
 *     IANA/RFC2426 (section 3.3.2) :               internet pref x400
 *     CCITT.X520+RFC5322/RFC6350 (section 6.4.2) : home (perso) intl work (pro)
 * @return string
 *     Balise HTML micro-format (cf. logo_type_)
**/
function filtre_logo_type_email($type_email) {
	return logo_type_('email', $type_email);
}
/**
 * @deprecated
 * @uses filtre_logo_type_email()
 */
function filtre_logo_type_mel($type_email) {
	return filtre_logo_type_email($type_email);
}

?>
