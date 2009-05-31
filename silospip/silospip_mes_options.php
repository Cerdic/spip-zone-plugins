<?php


// silospip_mes_options.php

// include_spip('base/abstract_sql');
// include_spip('base/silospip_tables');
// include_spip('inc/silospip_api_globales');
// include_spip('inc/silospip_api_abstract_sql');

define("_SILOSPIP_PREFIX", "silospip");

if (!defined('_DIR_PLUGIN_SILOSPIP')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SILOSPIP',(_DIR_PLUGINS.end($p)).'/');
} 

define("_SILOSPIP_DOMAINE", "spip.bo");
define("_SILOSPIP_CHARSET", "utf-8");

// mode debug dans le log, peut-etre augmente pour avoir tous
// les messages dans spip.log (par exemple)
// voir doc php
//define("_SILOSPIP_LOG_DEBUG", LOG_WARNING);
define("_SILOSPIP_LOG_DEBUG", LOG_DEBUG);
// define("_SILOSPIP_LOG_DEBUG", 4);

define("_SILOSPIP_EXEC_PREFIX", _SILOSPIP_PREFIX."_");
define("_SILOSPIP_EXEC_CONFIGURE", _SILOSPIP_EXEC_PREFIX."config");

define("_SILOSPIP_META_PREFERENCES", 'silospip_preferences');
/**********************

//nombre de processus d'envoi simultanes
@define('_SPIP_LISTE_SEND_THREADS',1);

// virer les echo, a reprendre plus tard correctement
// avis aux specialistes !!
define('_SIGNALER_ECHOS', false); // horrible 

define("_DIR_PLUGIN_SILOSPIP_IMG_PACK", _DIR_PLUGIN_SILOSPIP."img_pack/");

define("_SILOSPIP_PATRONS_DIR", "patrons/");
define("_SILOSPIP_PATRONS_TETE_DIR", _SILOSPIP_PATRONS_DIR."lien_en_tete_courriers/");
define("_SILOSPIP_PATRONS_PIED_DIR", _SILOSPIP_PATRONS_DIR."pieds_courriers/");
define("_SILOSPIP_PATRON_PIED_DEFAUT", "piedmail");
define("_SILOSPIP_PATRON_PIED_IGNORE", "aucun");
define("_SILOSPIP_PATRONS_PIED_DEFAUT", _SILOSPIP_PATRONS_PIED_DIR._SILOSPIP_PATRON_PIED_DEFAUT);
define("_SILOSPIP_PATRONS_TAMPON_DIR", _SILOSPIP_PATRONS_DIR."tampons_courriers/");

// au dela de cette taille, le contenu du champ est considéré
// comme le contenu du patron
// (compat anciennes versions de SPIP-Listes)
define("_SILOSPIP_PATRON_FILENAMEMAX", 30);

define("_SILOSPIP_RUBRIQUE", "messagerie");

define("_SILOSPIP_LOT_TAILLE", 30);

define("_SILOSPIP_LOTS_PERMIS", "1;5;10;30;100");

define("_SILOSPIP_ZERO_TIME_DATE", "0000-00-00 00:00:00");

// documentation: http://www.quesaco.org/Spiplistes-les-etats-du-courrier
define("_SILOSPIP_COURRIER_STATUT_REDAC", "redac"); // en cours de redac
define("_SILOSPIP_COURRIER_STATUT_READY", "ready"); // pret a etre envoye
define("_SILOSPIP_COURRIER_STATUT_ENCOURS", "encour"); // en cours par meleuse
define("_SILOSPIP_COURRIER_STATUT_AUTO", "auto"); // publie de liste
define("_SILOSPIP_COURRIER_STATUT_PUBLIE", "publie"); // publie
define("_SILOSPIP_COURRIER_STATUT_VIDE", "vide"); // moins de 10 car.
define("_SILOSPIP_COURRIER_STATUT_IGNORE", "ignore"); // pas de destinataire
define("_SILOSPIP_COURRIER_STATUT_STOPE", "stope"); // stope par admin
define("_SILOSPIP_COURRIER_STATUT_ERREUR", "erreur"); // en erreur

define("_SILOSPIP_COURRIER_TYPE_NEWSLETTER", "nl");
define("_SILOSPIP_COURRIER_TYPE_LISTEAUTO", "auto");

// champ 'statut' de 'spip_listes' varchar(10)
define("_SILOSPIP_PUBLIC_LIST", "liste");
define("_SILOSPIP_PRIVATE_LIST", "inact");
define("_SILOSPIP_DAILY_LIST", "pub_jour"); // periode = nb jours
define("_SILOSPIP_HEBDO_LIST", "pub_hebdo");
define("_SILOSPIP_WEEKLY_LIST", "pub_7jours"); // debut de semaine
define("_SILOSPIP_MENSUEL_LIST", "pub_mensul"); // mensuelle
define("_SILOSPIP_MONTHLY_LIST", "pub_mois"); // debut de mois
define("_SILOSPIP_YEARLY_LIST", "pub_an");
define("_SILOSPIP_TRASH_LIST", "poublist");

// les statuts des periodique
define("_SILOSPIP_LISTES_STATUTS_PERIODIQUES", 
	_SILOSPIP_DAILY_LIST
	. ";" . _SILOSPIP_HEBDO_LIST
	. ";" . _SILOSPIP_WEEKLY_LIST
	. ";" . _SILOSPIP_MENSUEL_LIST
	. ";" . _SILOSPIP_MONTHLY_LIST
	. ";" . _SILOSPIP_YEARLY_LIST
	);

// les statuts des listes publiees
define("_SILOSPIP_LISTES_STATUTS_OK", 
	_SILOSPIP_PRIVATE_LIST
	. ";" . _SILOSPIP_PUBLIC_LIST
	. ";" . _SILOSPIP_LISTES_STATUTS_PERIODIQUES
	);

// statuts des listes tels qu'affichees en liste 
define("_SILOSPIP_LISTES_STATUTS_TOUS", 
	_SILOSPIP_LISTES_STATUTS_OK
	. ";" . _SILOSPIP_TRASH_LIST
	);

// statuts des courriers tels qu'affiches en liste 
define("_SILOSPIP_COURRIERS_STATUTS"
	,	_SILOSPIP_COURRIER_STATUT_REDAC
	. ";" . _SILOSPIP_COURRIER_STATUT_READY
	//. ";" . _SILOSPIP_COURRIER_STATUT_ENCOURS
	. ";" . _SILOSPIP_COURRIER_STATUT_AUTO
	. ";" . _SILOSPIP_COURRIER_STATUT_PUBLIE
	. ";" . _SILOSPIP_COURRIER_STATUT_VIDE
	. ";" . _SILOSPIP_COURRIER_STATUT_IGNORE
	. ";" . _SILOSPIP_COURRIER_STATUT_STOPE
	. ";" . _SILOSPIP_COURRIER_STATUT_ERREUR
	);

// charsets:
// charsets autorises :
define("_SILOSPIP_CHARSETS_ALLOWED", "iso-8859-1;iso-8859-9;iso-8859-6;iso-8859-15;utf-8");
define("_SILOSPIP_CHARSET_ENVOI", "iso-8859-1"); // pour historique
define("_SILOSPIP_CHARSET_DEFAULT", _SILOSPIP_CHARSET_ENVOI);

define("_SILOSPIP_EXEC_ABONNE_EDIT", _SILOSPIP_EXEC_PREFIX."abonne_edit");
define("_SILOSPIP_EXEC_ABONNES_LISTE", _SILOSPIP_EXEC_PREFIX."abonnes_tous");
define("_SILOSPIP_EXEC_AIDE", _SILOSPIP_EXEC_PREFIX."aide");
define("_SILOSPIP_EXEC_AUTOCRON", _SILOSPIP_EXEC_PREFIX."autocron");
define("_SILOSPIP_EXEC_COURRIER_EDIT", _SILOSPIP_EXEC_PREFIX."courrier_edit");
define("_SILOSPIP_EXEC_COURRIER_GERER", _SILOSPIP_EXEC_PREFIX."courrier_gerer");
define("_SILOSPIP_EXEC_COURRIER_PREVUE", _SILOSPIP_EXEC_PREFIX."courrier_previsu");
define("_SILOSPIP_EXEC_COURRIERS_LISTE", _SILOSPIP_EXEC_PREFIX."courriers_casier"); // ancien listes_toutes
define("_SILOSPIP_EXEC_IMPORT_EXPORT", _SILOSPIP_EXEC_PREFIX."import_export");
define("_SILOSPIP_EXEC_IMPORT_PATRON", _SILOSPIP_EXEC_PREFIX."import_patron");
define("_SILOSPIP_EXEC_LISTE_EDIT", _SILOSPIP_EXEC_PREFIX."liste_edit");
define("_SILOSPIP_EXEC_LISTE_GERER", _SILOSPIP_EXEC_PREFIX."liste_gerer"); //ancien listes
define("_SILOSPIP_EXEC_LISTES_LISTE", _SILOSPIP_EXEC_PREFIX."listes_toutes");
define("_SILOSPIP_EXEC_MAINTENANCE", _SILOSPIP_EXEC_PREFIX."maintenance");

define("_SILOSPIP_ACTION_PREFIX", _SILOSPIP_PREFIX."_");
define("_SILOSPIP_ACTION_SUPPRIMER_ABONNER", _SILOSPIP_ACTION_PREFIX."supprimer_abonne");
define("_SILOSPIP_ACTION_CHANGER_STATUT_ABONNE", _SILOSPIP_ACTION_PREFIX."changer_statut_abonne");
define("_SILOSPIP_ACTION_ABONNER_AUTEUR", _SILOSPIP_ACTION_PREFIX."listes_abonner_auteur");
define("_SILOSPIP_ACTION_LISTE_ABONNES", _SILOSPIP_ACTION_PREFIX."liste_des_abonnes");
define("_SILOSPIP_ACTION_MOD_GERER", _SILOSPIP_ACTION_PREFIX."moderateurs_gerer");

// les formats d'envoi autorises, ou non pour pseudo-desabonne
define("_SILOSPIP_FORMATS_ALLOWED", "html;texte;non");
define("_SILOSPIP_FORMAT_DEFAULT", "html");

*****/ 


/*******

// tampon
define("_SILOSPIP_TAMPON_CLES", "editeur_nom,editeur_adresse,editeur_rcs,editeur_siret,editeur_url,editeur_logo");

define("_SILOSPIP_TIME_1_DAY", (3600 * 24));


if(silospip_spip_est_inferieur_193()) { 
	@define('SPIP_BOTH', MYSQL_BOTH);
	@define('SPIP_ASSOC', MYSQL_ASSOC);
	@define('SPIP_NUM', MYSQL_NUM);
}

//Balises Spip-listes

function balise_MELEUSE_CRON($p) {
   $p->code = "''";
   $p->statut = 'php';
   return $p;
}

function calcul_DATE_MODIF_SITE () {
   $date_art = sql_getfetsel(
		'date'
		, "spip_articles"
		, "statut=".sql_quote('publie')
		, ''
		, array("date DESC")
		, 1
		);
   $date_bre = sql_getfetsel(
		'date_heure'
		, "spip_breves"
		, "statut=".sql_quote('publie')
		, ''
		, array("date_heure DESC")
		, 1
		);
   $date_modif = ($date_bre > $date_art)? $date_bre : $date_art;
   return($date_modif);
}

function balise_DATE_MODIF_SITE($p) {
   $p->code = "calcul_DATE_MODIF_SITE()";
   $p->statut = 'php';
   return $p;
}

// exemple d'utilisation de la balise: patrons/nouveautes_forum.html
function calcul_DATE_MODIF_FORUM() {
   $date_f = sql_getfetsel(
		'date_heure'
		, "spip_forum"
		, "statut=".sql_quote('publie')
		, ''
		, array("date_heure DESC")
		, 1
		);
   return($date_f);
}

function balise_DATE_MODIF_FORUM($p) {
   $p->code = "calcul_DATE_MODIF_FORUM()";
   $p->statut = 'php';
   return $p;
}

// CP-20080906 : compatibilité SPIP 192d
// autoriser_webmestre_dist() considere que _ID_WEBMESTRES est defini
// mais c'est une option en 192d ?!
if(silospip_spip_est_inferieur_193() && !function_exists('autoriser_webmestre')) 
{
	function autoriser_webmestre($faire, $type, $id, $qui, $opt) 
	{
		$def_webmestre =
			defined('_ID_WEBMESTRES')
			? in_array($qui['id_auteur'], explode(':', _ID_WEBMESTRES))
			: true
			;
		$r =
			$def_webmestre
			&& ($qui['statut'] == '0minirezo')
			&& !$qui['restreint']
			;
		// silospip_log("resultat de autoriser_webmestre() ".gettype($r)." ". ($r ? "OK" : "niet"));
		return($r);
	} 
}

// autorise les admins et l'utilisateur a modifier son format de reception
function autoriser_abonne_modifierformat ($faire = '', $type = '', $id_objet = 0, $qui = NULL, $opt = NULL) {
	return(
		$GLOBALS['auteur_session']['id_auteur'] == $id
		|| $GLOBALS['auteur_session']['statut'] == '0minirezo'
	);
}

//CP-20080610 :: autoriser la moderation d'une liste
function autoriser_liste_moderer ($faire = '', $type = '', $id_objet = 0, $qui = NULL, $opt = NULL) {
	global 
		$connect_statut
		, $connect_toutes_rubriques
		;

	$result = false;
	if(($type == 'liste') && ($faire == "moderer")) {
		if(!$qui) {
			$qui = $GLOBALS['auteur_session']['id_auteur'];
		}
		$sql_where = array("id_auteur=".$qui['id_auteur']);
		if($id_objet > 0) {
			$sql_where[] = "id_liste=".sql_quote($id_objet);
		}
		$result = 
			(($connect_statut == '0minirezo') && $connect_toutes_rubriques)
			|| (
				sql_getfetsel(
					"id_auteur"
					, 'spip_auteurs_mod_listes'
					, $sql_where
					, '', '', 1
				)
			)
			;
	}
	return($result);
}

//utiliser le cron pour envoyer les messages en attente
function silospip_taches_generales_cron($taches_generales) {
	$taches_generales['silospip_cron'] = 10 ;
	return $taches_generales;
}

/* CP: tableau issu de SPIP-Listes-V (a nettoyer en fin d'optimisation)
	Tableau des objets de navigations dans l'espace prive
*/
/**************************************
$silospip_items = array(
	// les courriers
	_SILOSPIP_COURRIER_STATUT_REDAC => array(
		'puce' => _DIR_IMG_PACK."puce-blanche.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_brouillon-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:message_en_cours')
		, 'nav_t' => _T('silospip:En_redaction') // nav_t = titre dans naviguer rapide (boite gauche)
		, 'tab_t' => _T('silospip:Courriers_en_cours_de_redaction') // tab_t = titre du tableau dans spip_listes
		, 'desc' => null // description, sous-titre
	)
	, _SILOSPIP_COURRIER_STATUT_READY => array(
		'puce' => _DIR_IMG_PACK."puce-orange.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_brouillon-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:message_redac')
		, 'nav_t' => _T('silospip:Prets_a_envoi')
		, 'tab_t' => _T('silospip:Courriers_prets_a_etre_envoye')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_ENCOURS => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce_verte_encour.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_encour-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:message_en_cours')
		, 'nav_t' => _T('silospip:En_cours')
		, 'tab_t' => _T('silospip:Courriers_en_cours_denvoi')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_AUTO => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_publie-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:message_arch')
		, 'nav_t' => _T('silospip:publies_auto')
		, 'tab_t' => _T('silospip:Courriers_auto_publies')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_PUBLIE => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_publie-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:message_arch')
		, 'nav_t' => _T('silospip:Publies')
		, 'tab_t' => _T('silospip:Courriers_publies')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_STOPE => array(
		// courrier stope en cours d'envoi
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-stop.png"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_stop-24.png'
		, 'icon_color' => "f00"
		, 'alt' => _T('silospip:Envoi_abandonne')
		, 'nav_t' => _T('silospip:Stoppes')
		, 'tab_t' => _T('silospip:Courriers_stope')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_VIDE => array(
		// courrier sans contenu
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_vide-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('silospip:Envoi_abandonne')
		, 'nav_t' => _T('silospip:Vides')
		, 'tab_t' => _T('silospip:Courriers_vides')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_IGNORE => array(
		// courrier sans abonne
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('silospip:Envoi_abandonne')
		, 'nav_t' => _T('silospip:Sans_destinataire')
		, 'tab_t' => _T('silospip:Courriers_sans_destinataire')
		, 'desc' => null
		)
	, _SILOSPIP_COURRIER_STATUT_ERREUR => array(
		// courrier en erreur (liste manquante)
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('silospip:Envoi_abandonne')
		, 'nav_t' => _T('silospip:Sans_destinataire')
		, 'tab_t' => _T('silospip:Courriers_sans_liste')
		, 'desc' => null
		)
	// les listes
	, _SILOSPIP_PRIVATE_LIST  => array(
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "ff0"
		, 'alt' => _T('silospip:Liste_prive')
		, 'nav_t' => _T('silospip:Listes_privees')
		, 'tab_t' => _T('silospip:Listes_diffusion_privees')
		, 'desc' => _T('silospip:Listes_diffusion_privees_desc')
		)
	, _SILOSPIP_PUBLIC_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-verte.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('silospip:Liste_publique')
		, 'nav_t' => _T('silospip:Listes_publiques')
		, 'tab_t' => _T('silospip:Listes_diffusion_publique')
		, 'desc' => _T('silospip:Listes_diffusion_publiques_desc')
		)
	, _SILOSPIP_WEEKLY_LIST => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('silospip:Liste_hebdo')
		, 'nav_t' => _T('silospip:Publiques_hebdos')
		, 'tab_t' => _T('silospip:Listes_diffusion_hebdo')
		, 'desc' => _T('silospip:Listes_diffusion_publiques_desc')
		)
	, _SILOSPIP_MONTHLY_LIST => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('silospip:Liste_mensuelle')
		, 'nav_t' => _T('silospip:Publiques_mensuelles')
		, 'tab_t' => _T('silospip:Listes_diffusion_mensuelle')
		, 'desc' => _T('silospip:Listes_diffusion_publiques_desc')
		)
	, _SILOSPIP_YEARLY_LIST => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('silospip:Liste_annuelle')
		, 'nav_t' => _T('silospip:Publiques_annuelles')
		, 'tab_t' => _T('silospip:Listes_diffusion_annuelle')
		, 'desc' => _T('silospip:Listes_diffusion_publiques_desc')
		)
	, _SILOSPIP_TRASH_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-poubelle.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('silospip:Listes_suspendues')
		, 'nav_t' => _T('silospip:Listes_suspendues')
		, 'tab_t' => _T('silospip:Listes_diffusion_suspendue')
		, 'desc' => _T('silospip:Listes_diffusion_suspendue_desc')
		)
	// l'inconnu ???
	, 'default' => array(
		'puce' => _DIR_PLUGIN_SILOSPIP_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SILOSPIP_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "9cc"
		, 'alt' => _T('silospip:Inconnu')
		, 'nav_t' => _T('silospip:Inconnu')
		, 'tab_t' => _T('silospip:Inconnu')
		, 'desc' => null
		)
	);
	
	$silospip_items[_SILOSPIP_DAILY_LIST] = $silospip_items[_SILOSPIP_PUBLIC_LIST];
	$silospip_items[_SILOSPIP_HEBDO_LIST] = $silospip_items[_SILOSPIP_WEEKLY_LIST];
	$silospip_items[_SILOSPIP_MENSUEL_LIST] = $silospip_items[_SILOSPIP_MONTHLY_LIST];
	$silospip_items[_SILOSPIP_MONTHLY_LIST]['nav_t'] = _T('silospip:Listes_1_du_mois');
	$silospip_items[_SILOSPIP_MONTHLY_LIST]['tab_t'] = _T('silospip:Liste_diffusee_le_premier_de_chaque_mois');
	
	$silospip_version = $meta['silospip_version'];
	$silospip_real_version = silospip_real_version_get(_SILOSPIP_PREFIX);
	$silospip_base_version = $meta['silospip_base_version'];
	$silospip_real_base_version = silospip_real_version_base_get(_SILOSPIP_PREFIX);
	
	if(
		($silospip_version && ($silospip_real_version > $silospip_version))
		||
		($silospip_base_version && ($silospip_real_base_version > $silospip_base_version))
		) {
		// faire upgrade auto
		include_spip('base/silospip_upgrade');
		silospip_upgrade();
	}

*************************/
/*
silospip_log("version: ".$silospip_version . " "
	. "real_version: ".$silospip_real_version . " "
	. "base_version: ".$silospip_base_version . " "
	. "real_base_version: ".$silospip_real_base_version
	, _SILOSPIP_LOG_DEBUG);
*/

?>
