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
	
	$titre 		= $config->form->param->titre;
	$boite 		= $config->form->param->boite;
	$descriptif = $config->form->param->descriptif;
	$nom   		= $config->form->param->nom;
	$refus   	= $config->form->param->refus;
		
	
	if (!($titre && $boite)){
		$boite=($titre)?$titre: _T('icone_configuration_site') . ' ' . $config->form->nom;
	}

	if (!$config->autoriser()) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_refuse'), $refus ? $refus : " (cfg {$nom} - {$config->form->vue} - {$config->form->param->cfg_id})");
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'cfg'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($boite, 'cfg', $nom);
	echo "<br /><br /><br />\n";

	echo gros_titre(sinon($titre, _T('cfg:configuration_modules')), '', false);	
	echo $config->barre_onglets_cfg();
	
	// colonne gauche
	echo debut_gauche('', true);

	// si un formulaire cfg est demande
	if ($descriptif) echo debut_boite_info(true) . propre($descriptif) . fin_boite_info(true);
	
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'cfg'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'cfg'),'data'=>''));
		
	// affichage des messages envoyes par cfg
	$m = $config->form->messages; $messages = array();
	if (count($m['message_ok'])) 		$messages[] = join('<br />', $m['message_ok']);
	if (count($m['message_erreur'])) 	$messages[] = join('<br />', $m['message_erreur']);
	if (count($m['erreurs'])) 			$messages[] = join('<br />', $m['erreurs']);
	
	if ($messages = trim(join('<br />', $messages))) {
		echo debut_boite_info(true) . propre($messages) . fin_boite_info(true);
	}

	// affichage des liens
	echo $config->lier() . debut_droite("", true);
	
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
