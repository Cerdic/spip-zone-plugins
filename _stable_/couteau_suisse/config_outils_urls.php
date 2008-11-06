<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// section incluse par config_outils.php et specialement dediee a la configuration des URLs

// ici on a besoin de boutons radio : 'page', 'html', 'propres', 'propres2, 'arbo', 'libres', 'standard' et 'propres-qs'
add_variable( array(
	'nom' => 'radio_type_urls3',
	'format' => _format_CHAINE,
	'radio' => defined('_SPIP19300')
				// a partir de SPIP 2.0
				?array('page' => 'couteauprive:url_page',
					 'html' => 'couteauprive:url_html', 
					 'propres' => 'couteauprive:url_propres',
					 'propres2' => 'couteauprive:url_propres2',
					 'libres'=> 'couteauprive:url_libres',
					 'arbo'=> 'couteauprive:url_arbo',
					 'standard' => 'couteauprive:url_standard',
					 'propres_qs' => 'couteauprive:url_propres-qs')
				// max SPIP 1.92
				:array('page' => 'couteauprive:url_page',
					 'html' => 'couteauprive:url_html', 
					 'propres' => 'couteauprive:url_propres',
					 'propres2' => 'couteauprive:url_propres2',
					 'standard' => 'couteauprive:url_standard',
					 'propres-qs' => 'couteauprive:url_propres-qs'),
	'radio/ligne' => 4,
	'defaut' => "'page'",
	'code' => "\$GLOBALS['type_urls']=%s;\n",
));

# Utilise par 'page' (toutes les URLs) et 'propre' 'propre2' et 'arbo' pour les objets non reconnus
# fonction d'appel dans inc/utils.php : get_spip_script()

add_variable( array(
	'nom' => 'spip_script',
	'format' => _format_CHAINE,
	'defaut' => "'spip.php'",
	'code' => "define('_SPIP_SCRIPT', %s);\n",
));

///////////  define('URLS_PAGE_EXEMPLE', 'spip.php?article12'); /////////////////

#######
# on peut indiquer '.html' pour faire joli
#define ('_terminaison_urls_page', '');
# ci-dessous, ce qu'on veut ou presque (de preference pas de '/')
# attention toutefois seuls '' et '=' figurent dans les modes de compatibilite
#define ('_separateur_urls_page', '');
# on peut indiquer '' si on a installe le .htaccess
#define ('_debut_urls_page', get_spip_script('./').'?');
#######

add_variable( array(
	'nom' => 'terminaison_urls_page',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_page', %s);\n",
));
add_variable( array(
	'nom' => 'separateur_urls_page',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code' => "define('_separateur_urls_page', %s);\n",
));

///////////  define('URLS_ARBO_EXEMPLE', '/article/Titre'); /////////////////

add_variable( array(
	'nom' => 'url_arbo_minuscules',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 1,
	'code:%s' => "define('_url_arbo_minuscules', %s);\n",
));
add_variable( array(
	'nom' => 'urls_arbo_sans_type',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "\$GLOBALS['url_arbo_types']=array('rubrique'=>'rubrique','article'=>'article','breve'=>'breve','mot'=>'mot','auteur'=>'auteur','site'=>'site');\n"
));
add_variable( array(
	'nom' => 'url_arbo_sep_id',
	'format' => _format_CHAINE,
	'defaut' => "'-'",
	'code' => "define('_url_arbo_sep_id', %s);\n",
));
add_variable( array(
	'nom' => 'terminaison_urls_arbo',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_arbo', %s);\n",
));

///////////  define('URLS_PROPRES_EXEMPLE', 'Titre-de-l-article -Rubrique-'); /////////////////

add_variable( array(
	'nom' => 'terminaison_urls_propres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_propres', %s);\n",
));
add_variable( array(
	'nom' => 'debut_urls_propres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_debut_urls_propres', %s);\n",
));
add_variable( array(
	'nom' => 'marqueurs_urls_propres',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "define('_MARQUEUR_URL', false);\n"
));
add_variable( array(
	'nom' => 'forum_lgrmaxi',
	'format' => _format_NOMBRE,
	'defaut' => 0,
	'code:%s' => "define('_FORUM_LONGUEUR_MAXI', %s);",
));

add_outil( array(
	'id' => 'type_urls',
	'code:spip_options' => "%%radio_type_urls3%%%%spip_script%%
%%terminaison_urls_page%%%%separateur_urls_page%%
%%url_arbo_minuscules%%%%url_arbo_sep_id%%%%urls_arbo_sans_type%%%%terminaison_urls_arbo%%
%%debut_urls_propres%%%%terminaison_urls_propres%%%%marqueurs_urls_propres%%",
	'categorie' => 'admin',
));

?>