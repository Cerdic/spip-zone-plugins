<?php
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip("inc/plugin_globales_lib");

if (!defined('_DIR_PLUGIN_SPIPLISTES')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIPLISTES',(_DIR_PLUGINS.end($p)).'/');
} 

//nombre de processus d'envoi simultanes
@define('_SPIP_LISTE_SEND_THREADS',1);

// virer les echo, a reprendre plus tard correctement
// avis aux spécialistes !!
define('_SIGNALER_ECHOS', false); // horrible 

define("_DIR_PLUGIN_SPIPLISTES_IMG_PACK", _DIR_PLUGIN_SPIPLISTES."img_pack/");

define("_SPIPLISTES_RUBRIQUE", "messagerie");

define("_SPIPLISTES_LOTS_PERMIS", "1;5;10;30;100");

define("_SPIPLISTES_ZERO_TIME_DATE", "0000-00-00 00:00:00");

define("_SPIPLISTES_STATUT_REDAC", "redac");
define("_SPIPLISTES_STATUT_READY", "ready");
define("_SPIPLISTES_STATUT_ENCOURS", "encour");
define("_SPIPLISTES_STATUT_AUTO", "auto"); // publié de liste
define("_SPIPLISTES_STATUT_PUBLIE", "publie");
define("_SPIPLISTES_STATUT_VIDE", "vide");
define("_SPIPLISTES_STATUT_IGNORE", "ignore");
define("_SPIPLISTES_STATUT_STOPE", "stope");
define("_SPIPLISTES_STATUT_ERREUR", "erreur");

define("_SPIPLISTES_TYPE_NEWSLETTER", "nl");
define("_SPIPLISTES_TYPE_LISTEAUTO", "auto");

define("_SPIPLISTES_PUBLIC_LIST", "liste");
define("_SPIPLISTES_PRIVATE_LIST", "inact");
define("_SPIPLISTES_MONTHLY_LIST", "pub_mois");
define("_SPIPLISTES_TRASH_LIST", "poublist");

// statuts des listes tels qu'affichées en liste 
define("_SPIPLISTES_LISTES_STATUTS", _SPIPLISTES_PRIVATE_LIST.";"._SPIPLISTES_PUBLIC_LIST.";"._SPIPLISTES_MONTHLY_LIST.";"._SPIPLISTES_TRASH_LIST);
// statuts des courriers tels qu'affichés en liste 
define("_SPIPLISTES_COURRIERS_STATUTS"
	,	_SPIPLISTES_STATUT_REDAC
	. ";" . _SPIPLISTES_STATUT_READY
	. ";" . _SPIPLISTES_STATUT_ENCOURS
	. ";" . _SPIPLISTES_STATUT_AUTO
	. ";" . _SPIPLISTES_STATUT_PUBLIE
	. ";" . _SPIPLISTES_STATUT_VIDE
	. ";" . _SPIPLISTES_STATUT_IGNORE
	. ";" . _SPIPLISTES_STATUT_STOPE
	. ";" . _SPIPLISTES_STATUT_ERREUR
	);

// charsets:
// charsets autorisés :
define("_SPIPLISTES_CHARSETS_ALLOWED", "iso-8859-1;iso-8859-9;iso-8859-6;iso-8859-15;utf-8");
define("_SPIPLISTES_CHARSET_ENVOI", "iso-8859-1"); // pour historique
define("_SPIPLISTES_CHARSET_DEFAULT", _SPIPLISTES_CHARSET_ENVOI);

define("_SPIPLISTES_EXEC_PREFIX", "spiplistes_");
define("_SPIPLISTES_EXEC_ABONNE_EDIT", _SPIPLISTES_EXEC_PREFIX."abonne_edit");
define("_SPIPLISTES_EXEC_ABONNES_LISTE", _SPIPLISTES_EXEC_PREFIX."abonnes_tous");
define("_SPIPLISTES_EXEC_AIDE", _SPIPLISTES_EXEC_PREFIX."aide");
define("_SPIPLISTES_EXEC_AUTOCRON", _SPIPLISTES_EXEC_PREFIX."autocron");
define("_SPIPLISTES_EXEC_CONFIGURE", _SPIPLISTES_EXEC_PREFIX."config");
define("_SPIPLISTES_EXEC_COURRIER_EDIT", _SPIPLISTES_EXEC_PREFIX."courrier_edit");
define("_SPIPLISTES_EXEC_COURRIER_GERER", _SPIPLISTES_EXEC_PREFIX."courrier_gerer");
define("_SPIPLISTES_EXEC_COURRIER_PREVUE", _SPIPLISTES_EXEC_PREFIX."courrier_previsu");
//define("_SPIPLISTES_EXEC_COURRIER_REDAC", _SPIPLISTES_EXEC_PREFIX."courrier_rediger"); //ne sert plus (CP-20071012)
define("_SPIPLISTES_EXEC_COURRIERS_LISTE", _SPIPLISTES_EXEC_PREFIX."courriers_casier"); // ancien listes_toutes
define("_SPIPLISTES_EXEC_IMPORT_EXPORT", _SPIPLISTES_EXEC_PREFIX."import_export");
define("_SPIPLISTES_EXEC_IMPORT_PATRON", _SPIPLISTES_EXEC_PREFIX."import_patron");
define("_SPIPLISTES_EXEC_LISTE_EDIT", _SPIPLISTES_EXEC_PREFIX."liste_edit");
define("_SPIPLISTES_EXEC_LISTE_GERER", _SPIPLISTES_EXEC_PREFIX."liste_gerer"); //ancien listes
define("_SPIPLISTES_EXEC_LISTES_LISTE", _SPIPLISTES_EXEC_PREFIX."listes_toutes");
define("_SPIPLISTES_EXEC_MAINTENANCE", _SPIPLISTES_EXEC_PREFIX."maintenance");

define("_SPIPLISTES_ACTION_PREFIX", "spiplistes_");
define("_SPIPLISTES_ACTION_SUPPRIMER_ABONNER", _SPIPLISTES_ACTION_PREFIX."supprimer_abonne");
define("_SPIPLISTES_ACTION_CHANGER_STATUT_ABONNE", _SPIPLISTES_ACTION_PREFIX."changer_statut_abonne");

// les formats d'envoi autorisés, ou non pour pseudo-désabonné
define("_SPIPLISTES_FORMATS_ALLOWED", "html;texte;non");

define("_SPIPLISTES_META_PREFERENCES", 'spiplistes_preferences');

define("_SPIPLISTES_TIME_1_DAY", (3600 * 24));

//Balises Spip-listes

function balise_MELEUSE_CRON($p) {
   $p->code = "''";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_SITE() {
   $date_art=spip_query("SELECT date,titre FROM spip_articles WHERE statut='publie' ORDER BY date DESC LIMIT 0,1");
   $date_art=spip_fetch_array($date_art);
   $date_art= $date_art['date'];
   
   $date_bre=spip_query("SELECT date_heure,titre FROM spip_breves WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_bre=spip_fetch_array($date_bre);
   $date_bre= $date_bre['date_heure'];
   
   $date_modif= ($date_bre>$date_art)? $date_bre : $date_art ;   
   return  $date_modif;
}

function balise_DATE_MODIF_SITE($p) {
   $p->code = "calcul_DATE_MODIF_SITE()";
   $p->statut = 'php';
   return $p;
}


function calcul_DATE_MODIF_FORUM() {
   $date_f=spip_query("SELECT date_heure,titre FROM spip_forum WHERE statut='publie' ORDER BY date_heure DESC LIMIT 0,1");
   $date_f=spip_fetch_array($date_f);
   $date_f= $date_f['date_heure'];
   
   return  $date_f;
}

function balise_DATE_MODIF_FORUM($p) {
   $p->code = "calcul_DATE_MODIF_FORUM()";
   $p->statut = 'php';
   return $p;
}

//utiliser le cron pour envoyer les messages en attente
function spiplistes_taches_generales_cron($taches_generales){
	$taches_generales['spiplistes_cron'] = 10 ;
	return $taches_generales;
}

$spiplistes_v = $GLOBALS['meta']['spiplistes_version'] ;

//afficher la version de spip_listes dans le pied de page
if($spiplistes_v == 1.91)
$GLOBALS['spiplistes_version'] = "SPIP-listes 1.9.1";
if($spiplistes_v >= 1.92)
$GLOBALS['spiplistes_version'] = "SPIP-listes $spiplistes_v";

/* CP: tableau issu de SPIP-Listes-V (à nettoyer en fin d'optimisation)
	Tableau des objets de navigations dans l'espace privé
*/
$spiplistes_items = array(
	// les courriers
	_SPIPLISTES_STATUT_REDAC => array(
		'puce' => _DIR_IMG_PACK."puce-blanche.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_brouillon-24.png'
		, 'alt' => _T('spiplistes:message_en_cours')
		, 'nav_t' => _T('spiplistes:En_redaction') // nav_t = titre dans naviguer rapide (boite gauche)
		, 'tab_t' => _T('spiplistes:Courriers_en_cours_de_redaction') // tab_t = titre du tableau dans spip_listes
		, 'desc' => null // description, sous-titre
	)
	, _SPIPLISTES_STATUT_READY => array(
		'puce' => _DIR_IMG_PACK."puce-orange.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_brouillon-24.png'
		, 'alt' => _T('spiplistes:message_redac')
		, 'nav_t' => _T('spiplistes:Prets_a_envoi')
		, 'tab_t' => _T('spiplistes:Courriers_prets_a_etre_envoye')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_ENCOURS => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce_verte_encour.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_encour-24.png'
		, 'alt' => _T('spiplistes:message_en_cours')
		, 'nav_t' => _T('spiplistes:En_cours')
		, 'tab_t' => _T('spiplistes:aff_encours')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_AUTO => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_publie-24.png'
		, 'alt' => _T('spiplistes:message_arch')
		, 'nav_t' => _T('spiplistes:Publies')
		, 'tab_t' => _T('spiplistes:Courriers_auto_publies')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_PUBLIE => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-grise.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_publie-24.png'
		, 'alt' => _T('spiplistes:message_arch')
		, 'nav_t' => _T('spiplistes:Publies')
		, 'tab_t' => _T('spiplistes:Courriers_publies')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_STOPE => array(
		// courrier stopé en cours d'envoi
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-stop.png"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_stop-24.png'
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Stoppes')
		, 'tab_t' => _T('spiplistes:Courriers_stope')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_VIDE => array(
		// courrier sans contenu
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_vide-24.png'
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Vides')
		, 'tab_t' => _T('spiplistes:Courriers_vides')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_IGNORE => array(
		// courrier sans abonné
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Sans_destinataire')
		, 'tab_t' => _T('spiplistes:Courriers_sans_destinataire')
		, 'desc' => null
		)
	, _SPIPLISTES_STATUT_ERREUR => array(
		// courrier en erreur (liste manquante)
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'alt' => _T('spiplistes:Envoi_abandonne')
		, 'nav_t' => _T('spiplistes:Sans_destinataire')
		, 'tab_t' => _T('spiplistes:Courriers_sans_liste')
		, 'desc' => null
		)
	// les listes
	, _SPIPLISTES_PRIVATE_LIST  => array(
		'puce' => _DIR_IMG_PACK."puce-rouge.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'alt' => _T('spiplistes:Liste_prive')
		, 'nav_t' => _T('spiplistes:Listes_privees')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_interne')
		, 'desc' => _T('spiplistes:Listes_diffusion_interne_desc')
		)
	, _SPIPLISTES_PUBLIC_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-verte.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'alt' => _T('spiplistes:Liste_publique')
		, 'nav_t' => _T('spiplistes:Listes_publiques')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_publique')
		, 'desc' => _T('spiplistes:Listes_diffusion_publique_desc')
		)
	, _SPIPLISTES_MONTHLY_LIST => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-bleue.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'alt' => _T('spiplistes:Liste_mensuelle')
		, 'nav_t' => _T('spiplistes:Publiques_mensuelles')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_mensuelle')
		, 'desc' => _T('spiplistes:Listes_diffusion_mensuelle_desc')
		)
	, _SPIPLISTES_TRASH_LIST => array(
		'puce' => _DIR_IMG_PACK."puce-poubelle.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_listes-24.png'
		, 'alt' => _T('spiplistes:Listes_suspendues')
		, 'nav_t' => _T('spiplistes:Listes_suspendues')
		, 'tab_t' => _T('spiplistes:Listes_diffusion_suspendue')
		, 'desc' => _T('spiplistes:Listes_diffusion_suspendue_desc')
		)
	// l'inconnu ???
	, 'default' => array(
		'puce' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK."puce-inconnu.gif"
		, 'icon' => _DIR_PLUGIN_SPIPLISTES_IMG_PACK.'courriers_ignore-24.png'
		, 'alt' => _T('spiplistes:Inconnu')
		, 'nav_t' => _T('spiplistes:Inconnu')
		, 'tab_t' => _T('spiplistes:Inconnu')
		, 'desc' => null
		)
	);
	
include_spip('base/spip-listes');
include_spip('inc/options_spip_listes');
?>