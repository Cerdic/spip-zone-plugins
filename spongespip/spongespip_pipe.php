<?php
	if (!defined('_DIR_PLUGIN_SPONGESPIP')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_SPONGESPIP',(_DIR_PLUGINS.end($p)).'/');
	} 

	function spongespip_ajouterBoutons($boutons_admin) {
		// si on est admin
		if (($GLOBALS['connect_statut'] == "1comite")||($GLOBALS['connect_statut'] == "0minirezo")){
		  // on voit le bouton dans la barre "naviguer"
  		//Pour que les admins & les rédacteurs aient accès aux stats
		  $boutons_admin['accueil']->sousmenu["spongespip"]= new Bouton(
			_DIR_PLUGIN_SPONGESPIP."tag.png",  // icone
			_T('spongespip:Spongespip')	// titre
			);
		}
		return $boutons_admin;
	}

function spongespip_onglets(){
	//global $id_auteur, $connect_id_auteur, $connect_statut, $statut_auteur, $options;
	
	echo debut_onglet();

		echo onglet(_T('spongespip:graphiques'), generer_url_ecrire("spongespip&onglet=stats_mois"), "spongespip", "test", _DIR_PLUGIN_SPONGESPIP."icones/graphs.png");
		echo onglet(_T('spongespip:pages_vues'), generer_url_ecrire("spongespip&onglet=pages"), "spongespip", $onglet, _DIR_PLUGIN_SPONGESPIP."icones/pages.png");
		echo onglet(_T('spongespip:hotes'), generer_url_ecrire("spongespip&onglet=hotes"), "spongespip", $onglet, _DIR_PLUGIN_SPONGESPIP."icones/hosts.png");
		echo onglet(_T('spongespip:referents'), generer_url_ecrire("spongespip&onglet=referers"), "spongespip", $onglet, _DIR_PLUGIN_SPONGESPIP."icones/referers.png");
		echo onglet(_T('spongespip:plateformes'), generer_url_ecrire("spongespip&onglet=plateformes"), "spongespip", $onglet, _DIR_PLUGIN_SPONGESPIP."icones/plateformes.png");
		echo onglet(_T('spongespip:mots_cles'), generer_url_ecrire("spongespip&onglet=mots_cles"), "spongespip", $onglet, _DIR_PLUGIN_SPONGESPIP."icones/mots_cles.png");
	echo fin_onglet();
}

function spongespip_inserer_entete($flux){
		if (_request('exec')=='spongespip')
	{
			$flux .= '<link href="'._DIR_PLUGIN_SPONGESPIP.'style/style.css" rel="stylesheet" type="text/css" title="spongespip" /> ';
			$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SPONGESPIP.'javascript/spongespip.js.php"></script>';

		}
	
	return $flux;
}

?>