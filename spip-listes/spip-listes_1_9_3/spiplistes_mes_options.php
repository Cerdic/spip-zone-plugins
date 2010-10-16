<?php


// spiplistes_mes_options.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if(!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');
// la declaration des tables en spiplites 192 est dans 'spip-listes.php'. Elle se trouve dans 'spiplistes_tables.php' en 193
// include_spip ('base/spip-listes');
include_spip('base/spiplistes_tables');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api_abstract_sql');

define('_SPIPLISTES_PREFIX', 'spiplistes');

if (!defined('_DIR_PLUGIN_SPIPLISTES')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIPLISTES',(_DIR_PLUGINS.end($p)).'/');
} 


//nombre de processus d'envoi simultanes
@define('_SPIP_LISTE_SEND_THREADS',1);

// virer les echo, a reprendre plus tard correctement
// avis aux specialistes !!
define('_SIGNALER_ECHOS', false); // horrible 

// mode debug dans le log, peut-etre augmente pour avoir tous
// les messages dans spip.log (par exemple)
// voir doc php
define('_SPIPLISTES_LOG_DEBUG', LOG_DEBUG);
define('_SPIPLISTES_PREFIX_LOG', 'MEL: ');

define('_DIR_PLUGIN_SPIPLISTES_IMG_PACK', _DIR_PLUGIN_SPIPLISTES.'img_pack/');

// nombre max de chiffres ajoutes au login (creation abo)
define("_SPIPLISTES_MAX_LOGIN_NN", 32766);

define("_SPIPLISTES_PATRONS_DIR", "patrons/");
define("_SPIPLISTES_PATRONS_TETE_DIR", _SPIPLISTES_PATRONS_DIR."lien_en_tete_courriers/");
define("_SPIPLISTES_PATRONS_PIED_DIR", _SPIPLISTES_PATRONS_DIR."pieds_courriers/");
define("_SPIPLISTES_PATRON_PIED_DEFAUT", "piedmail");
define("_SPIPLISTES_PATRON_PIED_IGNORE", "aucun");
define("_SPIPLISTES_PATRONS_PIED_DEFAUT", _SPIPLISTES_PATRONS_PIED_DIR._SPIPLISTES_PATRON_PIED_DEFAUT);
define("_SPIPLISTES_PATRONS_TAMPON_DIR", _SPIPLISTES_PATRONS_DIR."tampons_courriers/");
define("_SPIPLISTES_PATRONS_MESSAGES_DIR", _SPIPLISTES_PATRONS_DIR."messages_abo/");

// au dela de cette taille, le contenu du champ est considéré
// comme le contenu du patron
// (compat anciennes versions de SPIP-Listes)
define("_SPIPLISTES_PATRON_FILENAMEMAX", 30);

define("_SPIPLISTES_RUBRIQUE", "messagerie");

define("_SPIPLISTES_LOT_TAILLE", 30);

define("_SPIPLISTES_LOTS_PERMIS", "1;5;10;30;100");

define("_SPIPLISTES_ZERO_TIME_DATE", "0000-00-00 00:00:00");

// documentation: http://www.quesaco.org/Spiplistes-les-etats-du-courrier
define('_SPIPLISTES_COURRIER_STATUT_REDAC', 'redac'); // en cours de redac
define('_SPIPLISTES_COURRIER_STATUT_READY', 'ready'); // pret a etre envoye
define('_SPIPLISTES_COURRIER_STATUT_ENCOURS', 'encour'); // en cours par meleuse
define('_SPIPLISTES_COURRIER_STATUT_AUTO', 'auto'); // publie de liste
define('_SPIPLISTES_COURRIER_STATUT_PUBLIE', 'publie'); // publie
define('_SPIPLISTES_COURRIER_STATUT_VIDE', 'vide'); // moins de 10 car.
define('_SPIPLISTES_COURRIER_STATUT_IGNORE', 'ignore'); // pas de destinataire
define('_SPIPLISTES_COURRIER_STATUT_STOPE', 'stope'); // stope par admin
define('_SPIPLISTES_COURRIER_STATUT_ERREUR', 'erreur'); // en erreur

define('_SPIPLISTES_COURRIER_TYPE_NEWSLETTER', 'nl');
define('_SPIPLISTES_COURRIER_TYPE_LISTEAUTO', 'auto');

// champ 'statut' de 'spip_listes' varchar(10)
define('_SPIPLISTES_PUBLIC_LIST', 'liste');
define('_SPIPLISTES_PRIVATE_LIST', 'inact');
define('_SPIPLISTES_DAILY_LIST', 'pub_jour'); // periode = nb jours
define('_SPIPLISTES_HEBDO_LIST', 'pub_hebdo');
define('_SPIPLISTES_WEEKLY_LIST', 'pub_7jours'); // debut de semaine
define('_SPIPLISTES_MENSUEL_LIST', 'pub_mensul'); // mensuelle
define('_SPIPLISTES_MONTHLY_LIST', 'pub_mois'); // debut de mois
define('_SPIPLISTES_YEARLY_LIST', 'pub_an');
define('_SPIPLISTES_TRASH_LIST', 'poublist');

// les statuts des periodique
define('_SPIPLISTES_LISTES_STATUTS_PERIODIQUES', 
	_SPIPLISTES_DAILY_LIST
	. ';' . _SPIPLISTES_HEBDO_LIST
	. ';' . _SPIPLISTES_WEEKLY_LIST
	. ';' . _SPIPLISTES_MENSUEL_LIST
	. ';' . _SPIPLISTES_MONTHLY_LIST
	. ';' . _SPIPLISTES_YEARLY_LIST
	);

// les statuts des listes publiees
define('_SPIPLISTES_LISTES_STATUTS_OK', 
	_SPIPLISTES_PRIVATE_LIST
	. ';' . _SPIPLISTES_PUBLIC_LIST
	. ';' . _SPIPLISTES_LISTES_STATUTS_PERIODIQUES
	);

// statuts des listes tels qu'affichees en liste 
define('_SPIPLISTES_LISTES_STATUTS_TOUS', 
	_SPIPLISTES_LISTES_STATUTS_OK
	. ';' . _SPIPLISTES_TRASH_LIST
	);

// statuts des courriers tels qu'affiches en liste 
define('_SPIPLISTES_COURRIERS_STATUTS'
	,	_SPIPLISTES_COURRIER_STATUT_REDAC
	. ';' . _SPIPLISTES_COURRIER_STATUT_READY
	//. ';' . _SPIPLISTES_COURRIER_STATUT_ENCOURS
	. ';' . _SPIPLISTES_COURRIER_STATUT_AUTO
	. ';' . _SPIPLISTES_COURRIER_STATUT_PUBLIE
	. ';' . _SPIPLISTES_COURRIER_STATUT_VIDE
	. ';' . _SPIPLISTES_COURRIER_STATUT_IGNORE
	. ';' . _SPIPLISTES_COURRIER_STATUT_STOPE
	. ';' . _SPIPLISTES_COURRIER_STATUT_ERREUR
	);

// charsets:
// charsets autorises :
define('_SPIPLISTES_CHARSETS_ALLOWED', "iso-8859-1;iso-8859-9;iso-8859-6;iso-8859-15;utf-8");
define("_SPIPLISTES_CHARSET_ENVOI", "iso-8859-1"); // pour historique
define("_SPIPLISTES_CHARSET_DEFAULT", _SPIPLISTES_CHARSET_ENVOI);

define('_SPIPLISTES_EXEC_PREFIX', _SPIPLISTES_PREFIX.'_');
define('_SPIPLISTES_EXEC_ABONNE_EDIT', _SPIPLISTES_EXEC_PREFIX.'abonne_edit');
define('_SPIPLISTES_EXEC_ABONNES_LISTE', _SPIPLISTES_EXEC_PREFIX.'abonnes_tous');
define('_SPIPLISTES_EXEC_AIDE', _SPIPLISTES_EXEC_PREFIX.'aide');
define('_SPIPLISTES_EXEC_AUTOCRON', _SPIPLISTES_EXEC_PREFIX.'autocron');
define('_SPIPLISTES_EXEC_CONFIGURE', _SPIPLISTES_EXEC_PREFIX.'config');
define('_SPIPLISTES_EXEC_COURRIER_EDIT', _SPIPLISTES_EXEC_PREFIX.'courrier_edit');
define('_SPIPLISTES_EXEC_COURRIER_GERER', _SPIPLISTES_EXEC_PREFIX.'courrier_gerer');
define('_SPIPLISTES_EXEC_COURRIER_PREVUE', _SPIPLISTES_EXEC_PREFIX.'courrier_previsu');
define('_SPIPLISTES_EXEC_COURRIERS_LISTE', _SPIPLISTES_EXEC_PREFIX.'courriers_casier'); // ancien listes_toutes
define('_SPIPLISTES_EXEC_IMPORT_EXPORT', _SPIPLISTES_EXEC_PREFIX.'import_export');
define('_SPIPLISTES_EXEC_IMPORT_PATRON', _SPIPLISTES_EXEC_PREFIX.'import_patron');
define('_SPIPLISTES_EXEC_LISTE_EDIT', _SPIPLISTES_EXEC_PREFIX.'liste_edit');
define('_SPIPLISTES_EXEC_LISTE_GERER', _SPIPLISTES_EXEC_PREFIX.'liste_gerer'); //ancien listes
define('_SPIPLISTES_EXEC_LISTES_LISTE', _SPIPLISTES_EXEC_PREFIX.'listes_toutes');
define('_SPIPLISTES_EXEC_MAINTENANCE', _SPIPLISTES_EXEC_PREFIX.'maintenance');

define('_SPIPLISTES_ACTION_PREFIX', _SPIPLISTES_PREFIX.'_');
define('_SPIPLISTES_ACTION_SUPPRIMER_ABONNER', _SPIPLISTES_ACTION_PREFIX.'supprimer_abonne');
define('_SPIPLISTES_ACTION_CHANGER_STATUT_ABONNE', _SPIPLISTES_ACTION_PREFIX.'changer_statut_abonne');
define('_SPIPLISTES_ACTION_ABONNER_AUTEUR', _SPIPLISTES_ACTION_PREFIX.'listes_abonner_auteur');
define('_SPIPLISTES_ACTION_LISTE_ABONNES', _SPIPLISTES_ACTION_PREFIX.'liste_des_abonnes');
define('_SPIPLISTES_ACTION_MOD_GERER', _SPIPLISTES_ACTION_PREFIX.'moderateurs_gerer');

// les formats d'envoi autorises, ou non pour pseudo-desabonne
define('_SPIPLISTES_FORMATS_ALLOWED', 'html;texte;non');
define('_SPIPLISTES_FORMAT_DEFAULT', 'html');

define('_SPIPLISTES_META_PREFERENCES', 'spiplistes_preferences');

// tampon
define('_SPIPLISTES_TAMPON_CLES', 'editeur_nom,editeur_adresse,editeur_rcs,editeur_siret,editeur_url,editeur_logo');

define('_SPIPLISTES_TIME_1_DAY', (3600 * 24));

// utiliser plugin FACTEUR si present
define('_SPIPLISTES_UTILISER_FACTEUR', 'non');

if(spiplistes_spip_est_inferieur_193()) { 
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
if(spiplistes_spip_est_inferieur_193() && !function_exists('autoriser_webmestre')) 
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
		// spiplistes_log("resultat de autoriser_webmestre() ".gettype($r)." ". ($r ? "OK" : "niet"));
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
function spiplistes_taches_generales_cron($taches_generales) {
	$taches_generales['spiplistes_cron'] = 10 ;
	return $taches_generales;
}

/* CP: tableau issu de SPIP-Listes-V (a nettoyer en fin d'optimisation)
	Tableau des objets de navigations dans l'espace prive
*/
$spiplistes_items = array(
	// les courriers
	_SPIPLISTES_COURRIER_STATUT_REDAC => array(
		'puce' => _DIR_IMG_PACK."puce-blanche.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_brouillon-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:message_en_cours')
		, 'nav_t' => _T('spiplistes:En_redaction') // nav_t = titre dans naviguer rapide (boite gauche)
		, 'tab_t' => _T('spiplistes:Courriers_en_cours_de_redaction') // tab_t = titre du tableau dans spip_listes
		, 'desc' => null // description, sous-titre
	)
	, _SPIPLISTES_COURRIER_STATUT_READY => array(
		'puce' => _DIR_IMG_PACK."puce-orange.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_brouillon-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:message_redac')
		, 'nav_t' => _T('spiplistes:Prets_a_envoi')
		, 'tab_t' => _T('spiplistes:Courriers_prets_a_etre_envoye')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_ENCOURS => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce_verte_encour.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_encour-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:message_en_cours')
		, 'nav_t' => _T('spiplistes:En_cours')
		, 'tab_t' => _T('spiplistes:Courriers_en_cours_denvoi')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_AUTO => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_publie-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:message_arch')
		, 'nav_t' => _T('spiplistes:publies_auto')
		, 'tab_t' => _T('spiplistes:Courriers_auto_publies')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_PUBLIE => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_publie-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:message_arch')
		, 'nav_t' => _T('spiplistes:Publies')
		, 'tab_t' => _T('spiplistes:Courriers_publies')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_STOPE => array(
		// courrier stope en cours d'envoi
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-stop.png"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_stop-24.png'
		, 'icon_color' => "f00"
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Stoppes')
		, 'tab_t' => _T('spiplistes:Courriers_stope')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_VIDE => array(
		// courrier sans contenu
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_vide-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Vides')
		, 'tab_t' => _T('spiplistes:Courriers_vides')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_IGNORE => array(
		// courrier sans abonne
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Sans_destinataire')
		, 'tab_t' => _T('spiplistes:Courriers_sans_destinataire')
		, 'desc' => null
		)
	, _SPIPLISTES_COURRIER_STATUT_ERREUR => array(
		// courrier en erreur (liste manquante)
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Sans_destinataire')
		, 'tab_t' => _T('spiplistes:Courriers_sans_liste')
		, 'desc' => null
		)
	// les listes
	, _SPIPLISTES_PRIVATE_LIST  => array(
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "ff0"
		, 'alt' => _T('spiplistes:Liste_prive')
		, 'nav_t' => _T('spiplistes:Listes_privees')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_privees')
		, 'desc' => _T('spiplistes:Listes_diffusion_privees_desc')
		)
	, _SPIPLISTES_PUBLIC_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-verte.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "9dba00"
		, 'alt' => _T('spiplistes:Liste_publique')
		, 'nav_t' => _T('spiplistes:Listes_publiques')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_publique')
		, 'desc' => _T('spiplistes:Listes_diffusion_publiques_desc')
		)
	, _SPIPLISTES_WEEKLY_LIST => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('spiplistes:Liste_hebdo')
		, 'nav_t' => _T('spiplistes:Publiques_hebdos')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_hebdo')
		, 'desc' => _T('spiplistes:Listes_diffusion_publiques_desc')
		)
	, _SPIPLISTES_MONTHLY_LIST => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('spiplistes:Liste_mensuelle')
		, 'nav_t' => _T('spiplistes:Publiques_mensuelles')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_mensuelle')
		, 'desc' => _T('spiplistes:Listes_diffusion_publiques_desc')
		)
	, _SPIPLISTES_YEARLY_LIST => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "00f"
		, 'alt' => _T('spiplistes:Liste_annuelle')
		, 'nav_t' => _T('spiplistes:Publiques_annuelles')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_annuelle')
		, 'desc' => _T('spiplistes:Listes_diffusion_publiques_desc')
		)
	, _SPIPLISTES_TRASH_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-poubelle.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'icon_color' => "000"
		, 'alt' => _T('spiplistes:Listes_suspendues')
		, 'nav_t' => _T('spiplistes:Listes_suspendues')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_suspendue')
		, 'desc' => _T('spiplistes:Listes_diffusion_suspendue_desc')
		)
	// l'inconnu ???
	, 'default' => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'icon_color' => "9cc"
		, 'alt' => _T('spiplistes:Inconnu')
		, 'nav_t' => _T('spiplistes:Inconnu')
		, 'tab_t' => _T('spiplistes:Inconnu')
		, 'desc' => null
		)
	);
	
	$spiplistes_items[_SPIPLISTES_DAILY_LIST] = $spiplistes_items[_SPIPLISTES_PUBLIC_LIST];
	$spiplistes_items[_SPIPLISTES_HEBDO_LIST] = $spiplistes_items[_SPIPLISTES_WEEKLY_LIST];
	$spiplistes_items[_SPIPLISTES_MENSUEL_LIST] = $spiplistes_items[_SPIPLISTES_MONTHLY_LIST];
	$spiplistes_items[_SPIPLISTES_MONTHLY_LIST]['nav_t'] = _T('spiplistes:Listes_1_du_mois');
	$spiplistes_items[_SPIPLISTES_MONTHLY_LIST]['tab_t'] = _T('spiplistes:Liste_diffusee_le_premier_de_chaque_mois');
	
	$spiplistes_version = $meta['spiplistes_version'];
	$spiplistes_real_version = spiplistes_real_version_get(_SPIPLISTES_PREFIX);
	$spiplistes_base_version = $meta['spiplistes_base_version'];
	$spiplistes_real_base_version = spiplistes_real_version_base_get(_SPIPLISTES_PREFIX);
	
	if(
		($spiplistes_version && ($spiplistes_real_version > $spiplistes_version))
		||
		($spiplistes_base_version && ($spiplistes_real_base_version > $spiplistes_base_version))
		) {
		// faire upgrade auto
		include_spip('base/spiplistes_upgrade');
		spiplistes_upgrade();
	}
/*
spiplistes_log("version: ".$spiplistes_version . " "
	. "real_version: ".$spiplistes_real_version . " "
	. "base_version: ".$spiplistes_base_version . " "
	. "real_base_version: ".$spiplistes_real_base_version
	, _SPIPLISTES_LOG_DEBUG);
*/

