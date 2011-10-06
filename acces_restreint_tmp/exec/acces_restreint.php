<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint_gestion');
include_spip('inc/presentation');

function exec_acces_restreint(){
	if (!autoriser('administrer','zone',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('accesrestreint:page_zones_acces'));
	
	echo gros_titre(_T('accesrestreint:titre_zones_acces'),'',false);
	echo debut_gauche("acces_restreint",true);
	
	echo debut_boite_info(true);
	echo propre(_T('accesrestreint:info_page'));	
	echo fin_boite_info(true);
	
	if (autoriser('webmestre')) {
		$res = icone_horizontale(_L('Configuration des acc&#232;s .htaccess'), generer_url_ecrire("acces_restreint_config"), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", "cadenas-24.gif",false);
		echo bloc_des_raccourcis($res);
	}
	
	echo debut_droite("acces_restreint",true);
	echo recuperer_fond('prive/acces_restreint',$_GET);
	if (autoriser('modifier','zone'))
		echo "<div>".icone_inline(_T('accesrestreint:creer_zone'),
		  generer_url_ecrire("zones_edit","new=oui"),
		  _DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif",
		  "creer.gif",'right')."</div>";

	echo fin_gauche(),fin_page();
}

?>