<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * la fonction appelee par le core, une simple "factory" de la classe cfg
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_cfg_dist($class = null)
{
	include_spip('inc/filtres');

	$cfg = cfg_charger_classe('cfg','inc');
	$config = &new $cfg(
		($nom = sinon(_request('cfg'), '')),
		($cfg_id = sinon(_request('cfg_id'),''))
		);
	
	// si le fond cfg demande une redirection, 
	// (et provient de cette redirection), il est possible
	// qu'il y ait un message a afficher
	if ($config->form->param->rediriger 
		&& $messages = $GLOBALS['meta']['cfg_message_'.$GLOBALS['auteur_session']['id_auteur']]){
			include_spip('inc/meta');
			effacer_meta('cfg_message_'.$GLOBALS['auteur_session']['id_auteur']);
			if (defined('_COMPAT_CFG_192')) ecrire_metas();
			$config->form->messages = unserialize($messages);
	}

	$config->form->traiter();
	
	afficher_page_cfg($config);

	return;
}


function afficher_page_cfg(&$config){
	include_spip("inc/presentation");

	if (!$config->autoriser()) {
		echo $config->acces_refuse();
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'cfg'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($config->get_boite(), 'cfg', $config->get_nom());
	echo "<br /><br /><br />\n";

	echo gros_titre(sinon($config->get_titre(), _T('cfg:configuration_modules')), '', false);	
	echo $config->barre_onglets();
	
	// colonne gauche
	echo debut_gauche('', true);

	// si un formulaire cfg est demande
	echo $config->descriptif();
	
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'cfg'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'cfg'),'data'=>''));
		
	// affichage des messages envoyes par cfg
	echo $config->messages();

	// affichage des liens
	echo $config->lier();
	
	echo debut_droite("", true);
	
	// centre de la page	
	if (!$formulaire = $config->formulaire()) {
		// Page appellee sans formulaire valable
		echo "<img src='"._DIR_PLUGIN_CFG.'cfg.png'."' style='float:right' alt='' />\n";
		echo "<h3>" . _T("cfg:choisir_module_a_configurer") . "</h3>";
	} else {
		// Mettre un cadre_trait_couleur autour du formulaire, sauf si demande express de ne pas le faire
		if ($config->form->param->presentation == 'auto') {
			echo debut_cadre_trait_couleur('', true, '', $boite);
			echo $formulaire;
			echo fin_cadre_trait_couleur(true);
		} else {
			echo $formulaire;
		}
	}
	
	// pied
	echo fin_gauche() . fin_page();
}

?>
