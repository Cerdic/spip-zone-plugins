<?php
/* Plugin Comarquage pour SPIP 1.9
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_admin_comarquage(){
	global $connect_statut,$connect_toutes_rubriques;

	include_spip("inc/presentation");

	if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques) {
		if (($nb=_request('cron'))!=NULL){
			Sirtaqui_syndique (intval($nb));
			envoie_image_vide();
			exit();
		}
	}

	debut_page(_L("Comarquage"), "Comarquage", "Comarquage");

	debut_gauche();

	debut_boite_info();
	echo propre(_L('Cette page r&eacute;capitule les parametres de mise a jour depuis le serveur Service Public de la Documentation Francaise.'));
	fin_boite_info();

	debut_droite();
	gros_titre(_L('Service Public Documentation Francaise'));
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	echo generer_url_post_ecrire('admin_comarquage');
	echo "<div>";

	//$comarquage_xml_server = _request('comarquage_xml_server');
	$comarquage_local_refresh = _request('comarquage_local_refresh');
	$comarquage_local_timeout = _request('comarquage_local_timeout');
	$comarquage_default_xml_file = _request('comarquage_default_xml_file');
	$comarquage_default_xsl_file = _request('comarquage_default_xsl_file');
	$comarquage_code_insee =  _request('comarquage_code_insee');
	if ($comarquage_local_refresh!==NULL){
		//if (substr($comarquage_xml_server,-1)=='/')
			//$comarquage_xml_server = substr($comarquage_xml_server,0,strlen($comarquage_xml_server)-1);
		//ecrire_meta('comarquage_xml_server',$comarquage_xml_server);
		ecrire_meta('comarquage_local_refresh',intval($comarquage_local_refresh));
		ecrire_meta('comarquage_local_timeout',intval($comarquage_local_timeout));
		ecrire_meta('comarquage_default_xml_file',$comarquage_default_xml_file);
		ecrire_meta('comarquage_default_xsl_file',$comarquage_default_xsl_file);
		ecrire_meta('comarquage_code_insee',$comarquage_code_insee);
		ecrire_metas();
	}

	//$comarquage_xml_server=isset($GLOBALS['meta']['comarquage_xml_server'])?$GLOBALS['meta']['comarquage_xml_server']:'';
	$comarquage_local_refresh=isset($GLOBALS['meta']['comarquage_local_refresh'])?$GLOBALS['meta']['comarquage_local_refresh']:'';
	$comarquage_local_timeout=isset($GLOBALS['meta']['comarquage_local_timeout'])?$GLOBALS['meta']['comarquage_local_timeout']:'';
	$comarquage_default_xml_file=isset($GLOBALS['meta']['comarquage_default_xml_file'])?$GLOBALS['meta']['comarquage_default_xml_file']:'';
	$comarquage_default_xsl_file=isset($GLOBALS['meta']['comarquage_default_xsl_file'])?$GLOBALS['meta']['comarquage_default_xsl_file']:'';
	$comarquage_code_insee=isset($GLOBALS['meta']['comarquage_code_insee'])?$GLOBALS['meta']['comarquage_code_insee']:'';
	// Les url des flux sont en dur dans le code
	//echo "<div>";
	//echo "<label for='comarquage_xml_server'><strong>"._L('Serveur XML de la Documentation francaise :')."</strong></label><br/>";
	//echo "<input type='text' label='comarquage_xml_server' name='comarquage_xml_server' value=\"".entites_html($comarquage_xml_server)."\" class='formo' />";
	//echo "</div>";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='comarquage_local_refresh'><strong>"._L('Periodicite de mise a jour en tache de fonds (secondes) [259200] :')."</strong></label><br/>";
	echo "<input type='text' label='comarquage_local_refresh' name='comarquage_local_refresh' value=\"".entites_html($comarquage_local_refresh)."\" class='formo' />";
	echo "</div>";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='comarquage_local_timeout'><strong>"._L('Periodicite maxi de peremption (secondes) [604800] :')."</strong></label><br/>";
	echo "<input type='text' label='comarquage_local_timeout' name='comarquage_local_timeout' value=\"".entites_html($comarquage_local_timeout)."\" class='formo' />";
	echo "</div>";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='comarquage_default_xml_file'><strong>"._L('Fiche XML par defaut [Themes.xml] :')."</strong></label><br/>";
	echo "<input type='text' label='comarquage_default_xml_file' name='comarquage_default_xml_file' value=\"".entites_html($comarquage_default_xml_file)."\" class='formo' />";
	echo "</div>";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='comarquage_default_xsl_file'><strong>"._L('Fiche XSL par defaut [spThemes.xsl] :')."</strong></label><br/>";
	echo "<input type='text' label='comarquage_default_xsl_file' name='comarquage_default_xsl_file' value=\"".entites_html($comarquage_default_xsl_file)."\" class='formo' />";
	echo "</div>";
	echo "<div style='font:arial,helvetica,sans-serif;font-size:small;'>";
	echo "<label for='comarquage_code_insee'><strong>"._L('Code INSEE de la commune [34172] ')."  </strong><em>(<a href=\"http://www.insee.fr/fr/methodes/nomenclatures/cog/\">Rechercher un code sur le site de l'INSEE</a>)</em></label><br/>";
	echo "<input type='text' label='comarquage_code_insee' name='comarquage_code_insee' value=\"".entites_html($comarquage_code_insee)."\" class='formo' />";
	echo "</div>";
	echo "<p style='text-align:right;'>";
	echo "<input type='submit' name='submit' value='"._T('Modifier')."' class='fondo' />";
	echo "</p></div></form>";


	fin_page();
}


?>