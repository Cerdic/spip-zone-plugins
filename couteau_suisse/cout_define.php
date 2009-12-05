<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// Les constantes utilisees dans la description des outils ont la forme @_CS_MACONSTANTE@

function cout_define($contexte) {
$rss_source = 'http://zone.spip.org/trac/spip-zone/log/_plugins_/couteau_suisse?format=rss&mode=stop_on_copy&limit=20';
switch($contexte) {
	case 'distant':
		// RSS de trac
		@define('_CS_RSS_SOURCE', $rss_source);
		// Doc de spip-contrib.net
		@define('_URL_CONTRIB', 'http://www.spip-contrib.net/?article');
		// Revisions du CS
		@define('_URL_CS_PLUGIN_XML', 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/couteau_suisse/plugin.xml?format=txt');
		// on met a jour le flux rss toutes les 2 heures
		// contrib ici qui devra passer en fond et utiliser le cache de SPIP !
		define('_CS_RSS_UPDATE', 2*3600);
		define('_CS_RSS_COUNT', 15);
		break;

	// contexte general lie au descriptions d'outils
	case 'description_outils':
		define('_VAR_OUTIL', '@@CS_VAR_OUTIL@@');
		@define('_CS_CHOIX', _T('couteauprive:votre_choix'));
		@define('_CS_ASTER', '<sup>(*)</sup>');
		@define('_CS_PLUGIN_JQUERY192', defined('_SPIP19300')?'':_T('couteauprive:detail_jquery3'));
		break;
		
	case 'couleurs':
		@define('_CS_EXEMPLE_COULEURS', '<br /><span style="font-weight:normal; font-size:85%;"><span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert&nbsp;olive</span>, <span style="background-color:navy; color:white;">navy/bleu&nbsp;marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert&nbsp;clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu&nbsp;clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu&nbsp;azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu&nbsp;violet</span>, <span style="background-color:chocolate;">chocolate/brun&nbsp;clair</span>, <span style="background-color:cornsilk;">cornsilk/rose&nbsp;clair</span>, <span style="background-color:darkgreen;">darkgreen/vert&nbsp;fonce</span>, <span style="background-color:darkorange;">darkorange/orange&nbsp;fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve&nbsp;fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu&nbsp;ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune&nbsp;paille</span>, <span style="background-color:yellow;">yellow/jaune</span></span><span style="font-size:50%;"><br />&nbsp;</span>');
		@define('_CS_EXEMPLE_COULEURS2', "\n-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>\n-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.");
		@define('_CS_EXEMPLE_COULEURS3', "\n-* <code>Lorem ipsum [fond rouge]dolor[/fond rouge] sit amet</code>\n-* <code>Lorem ipsum [bg red]dolor[/bg red] sit amet</code>.");
		break;
	case 'cs_comportement':
		@define('_CS_DIR_TMP', cs_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP));
		@define('_CS_FILE_OPTIONS', cs_canonicalize(
			str_replace('../', '', _DIR_RESTREINT_ABS)
			.cs_spip_file_options(3)
		));
		break;
	case 'auteur_forum':
		@define('_CS_FORUM_NOM', preg_replace(',:$,','',_T('forum_votre_nom')));
		@define('_CS_FORUM_EMAIL', preg_replace(',:$,','',_T('forum_votre_email')));
		break;
	case 'en_travaux':
		@define('_CS_TRAVAUX_TITRE', '<i>'._T('info_travaux_titre').'</i>');
		@define('_CS_NOM_SITE', '<i>'.$GLOBALS['meta']['nom_site'].'</i>');
		break;
	case 'webmestres':
		def_liste_adminsitrateurs();
		break;
	case 'boites_privees':
		// RSS de trac
		@define('_CS_RSS_SOURCE', $rss_source);
		break;

}} // function cout_define($contexte)

// Qui sont les webmestres et les administrateurs ?
function def_liste_adminsitrateurs() {
	include_spip('inc/autoriser');
	include_spip('inc/texte');
	$webmestres = array();
	$s = spip_query("SELECT * FROM spip_auteurs WHERE statut='0minirezo'");
	$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array'; // compatibilite SPIP 1.92
	while ($qui = $fetch($s)) {
		$nom = typo($qui['nom']." (id_auteur=$qui[id_auteur])");
		if (autoriser('webmestre','','',$qui)) $webmestres[$qui['id_auteur']] = $nom;
		else if (autoriser('configurer','plugins','',$qui)) $admins[$qui['id_auteur']] = $nom;
	}
	@define('_CS_LISTE_WEBMESTRES', join(', ', $webmestres));
	@define('_CS_LISTE_ADMINS', join(', ', $admins));
}

?>