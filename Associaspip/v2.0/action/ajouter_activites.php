<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_activites() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	activites_insert($_REQUEST['date'], $_REQUEST['id_evenement'], $_REQUEST['non_membres'], $_REQUEST['inscrits'], $_REQUEST['email'], $_REQUEST['telephone'], $_REQUEST['adresse'], $_REQUEST['montant'], $_REQUEST['commentaire']);
}

function activites_insert($date, $id_evenement, $non_membres, $inscrits, $email, $telephone, $adresse, $montant, $commentaire)
{
	$n = sql_insertq('spip_asso_activites', array(
		'date' => $date,
		'id_evenement' => $id_evenement,
		'nom' => $nom,
		'id_adherent' => $id_membre,
		'membres' => $membres,
		'non_membres' => $non_membres,
		'inscrits' => $inscrits,
		'email' => $email,
		'telephone' => $telephone,
		'adresse' => $adresse,
		'montant' => $montant,
		'commentaire' => $commentaire));
	spip_log("insertion activite numero: $n");
}
?>
