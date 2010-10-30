<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_dons() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$journal= _request('journal');
	$date_don = _request('date_don');
	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = _request('id_adherent');
	$argent = _request('argent');
	$colis = _request('colis');
	$valeur = _request('valeur');
	$contrepartie = _request('contrepartie');
	$commentaire = _request('commentaire');

	don_insert($id_adherent, $date_don, $argent, $bienfaiteur, $valeur, $journal, $contrepartie, $colis, $commentaire);
}

// il faudrait retrouver id par bienfaiteur et reciproquement

function don_insert($id_adherent, $date_don, $argent, $bienfaiteur='', $valeur='', $journal='', $contrepartie='', $colis='', $commentaire='')
{
	include_spip('base/association');		
	$id_adherent = intval($id_adherent);
	if (!$bienfaiteur AND $id_adherent)
	  $bienfaiteur = sql_getfetsel('nom_famille', _ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_adherent");
	
	if (!$valeur) $valeur = $argent;
	$date = $date_don ? $date_don : date("Y-m-d");
	$id_don = sql_insertq('spip_asso_dons', array(
					    'date_don' => $date,
					    'bienfaiteur' => $bienfaiteur,
					    'id_adherent' => $id_adherent,
					    'argent' => $argent,
					    'colis' => $colis,
					    'valeur' => $valeur,
					    'contrepartie' => $contrepartie,
					    'commentaire' => $commentaire));
	$qui = $id_adherent ?  " [$bienfaiteur" . "->membre$id_adherent]" : '';
	sql_insertq('spip_asso_comptes', array(
		    'date' => $date,
		    'imputation' => $GLOBALS['association_metas']['pc_dons'],
		    'recette' => $argent,
		    'journal' => $journal,
		    'id_journal' => $id_don,
		    'justification' => "[->don$id_don]$qui"));
}
?>
