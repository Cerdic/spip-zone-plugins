<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_cfg_dist()
{
	$out = "";
	
	include_spip('inc/filtres');
	include_spip('inc/cfg');
	$config = &new cfg(
		($nom = sinon(_request('cfg'), '')),
		($cfg_id = sinon(_request('cfg_id'),''))
		);
	
	// traitements du formulaire poste
	// seulement s'il provient d'un formulaire CFG
	// et non d'un formulaire CVT dans un fond CFG
	if (_request('arg'))
		$config->traiter();
	
	//
	// affichages
	//
	include_spip("inc/presentation");

	if (!$config->autoriser()) {
		echo $config->acces_refuse();
		exit;
	}

	$out .= "<h1>".$config->get_titre()."</h1>";
	#$out .=  $config->barre_onglets();
	$out .=  $config->barre_hierarchie();
	$out .=  "<br /><br />\n";
	
	// colonne gauche
	$out .= "\n<!--#navigation-->";
	// si un formulaire cfg est demande
	if ($s = $config->logo() . $config->descriptif()) {
		$out .= debut_boite_info(true) . $s . fin_boite_info(true);
	}
	$out .= "\n<!--/#navigation-->";

	
	// colonne droite
	$out .= "\n<!--#extra-->";
	// affichage des messages envoyes par cfg
	if ($s = $config->messages()) $out .= debut_boite_info(true) . $s . fin_boite_info(true);

	// affichage des liens
	if ($s = $config->liens()) $out .= debut_boite_info(true) . $s . fin_boite_info(true);
	if ($s = $config->liens_multi()) $out .= debut_boite_info(true) . $s . fin_boite_info(true);
	$out .= "\n<!--/#extra-->";


	// centre de la page	
	if ($config->get_presentation() == 'auto') {
		$out .= debut_cadre_trait_couleur('', true, '', $config->get_boite());
		$out .= $config->formulaire();
		$out .= fin_cadre_trait_couleur(true);
	} else {
		$out .=  $config->formulaire();
	}

	return $out;
}

?>
