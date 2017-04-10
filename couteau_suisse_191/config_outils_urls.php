<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : https://contrib.spip.net/?article2166   #
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
					 'propres_qs' => 'couteauprive:url_propres_qs')
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

# Utilise par 'page' (toutes les URLs) et 'propre' 'propre2' 'libres' et 'arbo' pour les objets non reconnus
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
	'code:strlen(%s)' => "define('_terminaison_urls_page', %s);",
));
add_variable( array(
	'nom' => 'separateur_urls_page',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_separateur_urls_page', %s);",
));

///////////  define('URLS_ARBO_EXEMPLE', '/article/Titre'); /////////////////

add_variable( array(
	'nom' => 'url_arbo_minuscules',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "define('_url_arbo_minuscules', %s);",
));
add_variable( array(
	'nom' => 'urls_arbo_sans_type',
	'format' => _format_NOMBRE,
	'radio' => array(0 => 'item_oui', 1 => 'item_non'),				
	'defaut' => 1,
	'code:%s' => "\n\$GLOBALS['url_arbo_types']=array('rubrique'=>'','article'=>'','breve'=>'','mot'=>'','auteur'=>'','site'=>'');",
));
add_variable( array(
	'nom' => 'url_arbo_sep_id',
	'format' => _format_CHAINE,
	'defaut' => "'-'",
	'code' => "define('_url_arbo_sep_id', %s);",
));
add_variable( array(
	'nom' => 'terminaison_urls_arbo',
	'format' => _format_CHAINE,
	'defaut' => "'.html'",
	'code' => "define('_terminaison_urls_arbo', %s);",
));

///////////  define('URLS_PROPRES_EXEMPLE', 'Titre-de-l-article -Rubrique-'); /////////////////

add_variable( array(
	'nom' => 'terminaison_urls_propres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_propres', %s);",
));
add_variable( array(
	'nom' => 'debut_urls_propres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_debut_urls_propres', %s);",
));
add_variable( array(
	'nom' => 'marqueurs_urls_propres',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "define('_MARQUEUR_URL', false);"
));

add_variable( array(
	'nom' => 'debut_urls_propres2',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_debut_urls_propres', %s);",
));
add_variable( array(
	'nom' => 'marqueurs_urls_propres2',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "define('_MARQUEUR_URL', false);"
));

add_variable( array(
	'nom' => 'terminaison_urls_libres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_propres', %s);",
));
add_variable( array(
	'nom' => 'debut_urls_libres',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_debut_urls_propres', %s);",
));

add_variable( array(
	'nom' => 'terminaison_urls_propres_qs',
	'format' => _format_CHAINE,
	'defaut' => "''",
	'code:strlen(%s)' => "define('_terminaison_urls_propres', %s);",
));
add_variable( array(
	'nom' => 'marqueurs_urls_propres_qs',
	'format' => _format_NOMBRE,
	'radio' => array(1 => 'item_oui', 0 => 'item_non'),				
	'defaut' => 1,
	'code:!%s' => "define('_MARQUEUR_URL', false);",
));

add_variable( array(
	'nom' => 'urls_minuscules',
	'check' => 'couteauprive:urls_minuscules',
	'defaut' => 0,
));
add_variable( array(
	'nom' => 'urls_avec_id',
	'check' => 'couteauprive:urls_avec_id',
	'defaut' => 0,
));
add_variable( array(
	'nom' => 'urls_avec_id2',
	'check' => 'couteauprive:urls_avec_id2',
	'defaut' => 0,
));
add_variable( array(
	'nom' => 'urls_id_3_chiffres',
	'check' => 'couteauprive:urls_3_chiffres',
	'defaut' => 0,
));
add_variable( array(
	'nom' => 'urls_id_sauf_rubriques',
	'check' => 'couteauprive:urls_id_sauf_rubriques',
	'defaut' => 0,
));

add_outil( array(
	'id' => 'type_urls',
	'code:spip_options' => "%%radio_type_urls3%%%%spip_script%%
switch(\$GLOBALS['type_urls']) {
	case 'page':%%terminaison_urls_page%%%%separateur_urls_page%%break;
	case 'propres':%%debut_urls_propres%%%%terminaison_urls_propres%%%%marqueurs_urls_propres%%break;
	case 'propres2':%%debut_urls_propres2%%%%marqueurs_urls_propres2%%break;
	case 'libres':%%debut_urls_libres%%%%terminaison_urls_libres%%break;
	case 'arbo':%%url_arbo_minuscules%%%%url_arbo_sep_id%%%%terminaison_urls_arbo%%%%urls_arbo_sans_type%%break;
	case 'propres_qs':%%terminaison_urls_propres_qs%%%%marqueurs_urls_propres_qs%%break;
	case 'propres-qs':%%terminaison_urls_propres_qs%%break;
}",
	'categorie' => 'admin',
	'description' => '<:type_urls::>'
		.(defined('_SPIP19300')?'<radio_type_urls3 valeur="propres/propres2/libres/arbo/propres_qs"><:type_urls:1:>[[%urls_avec_id%]][[->%urls_avec_id2%]][[->%urls_id_3_chiffres%]][[->%urls_id_sauf_rubriques%]]</radio_type_urls3>':''),
	'pipelinecode:creer_chaine_url' => "\$id = \$flux['objet']['id_objet']; \$ok = true;
if(%%urls_id_sauf_rubriques%%) {\$ok = \$flux['objet']['type']!='rubrique';}
if(%%urls_id_3_chiffres%%) {\$id = sprintf('%03d', \$id);}
if(%%urls_avec_id2%%) {@define('_CS_URL_SEP','-'); if(\$ok) \$flux['data']=\$id._CS_URL_SEP.\$flux['data'];}
if(%%urls_avec_id%%) {@define('_CS_URL_SEP',','); if(\$ok) \$flux['data'].=_CS_URL_SEP.\$id;}
if(%%urls_minuscules%%) {\$flux['data']=strtolower(\$flux['data']);}",
));

?>