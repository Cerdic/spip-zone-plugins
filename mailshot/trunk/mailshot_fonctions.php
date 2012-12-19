<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function mailshot_url_newsletter($id){
	if (!is_numeric($id))
		return "";

	if (!test_plugin_actif("newsletters"))
		return "";

	return generer_url_entite($id,'newsletter');
}

function mailshot_afficher_avancement($current,$total,$failed=0){
	$out = "$current/$total";
	if ($failed)
	$out .= " ($failed fail)";
	return $out;
}


function mailshot_puce_statut($statut,$objet,$id_objet=0,$id_parent=0){
	static $puce_statut = null;
	if (!$puce_statut)
		$puce_statut = charger_fonction('puce_statut','inc');
	return $puce_statut($id_objet, $statut, $id_parent, $objet, false, objet_info($objet,'editable')?_ACTIVER_PUCE_RAPIDE:false);
}


?>