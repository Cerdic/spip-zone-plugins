<?php
/*
 * Plugin numero
 * aide a la numerotation/classement des objets dans l'espace prive
 *
 * Auteurs :
 * Cedric Morin, Nursit.com
 * (c) 2008-2014 - Distribue sous licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/numeroter");

function formulaires_numeroter_objet_charger_dist($objet,$id_objet){

	$valeurs = array(
		'_objet' => $objet,
		'_id_objet' => $id_objet,
		'precedent' => '',
	);

	return $valeurs;
}

function formulaires_numeroter_objet_traiter_dist($objet,$id_objet){
	$precedent = intval(_request('precedent'));

	// recuperer le titre/parent de l'objet
	$d = numero_info_objet($objet,$id_objet);
	$cond = array($d['primary']."=".intval($id_objet));
	$res = numero_requeter_titre($objet,$cond);
	$row_o = sql_fetch($res);
	$id_parent = $row_o['id_parent'];

	// renumeroter la fratrie
	numero_numeroter_objets($objet,$id_parent);

	// recuperer le titre/parent de avant
	$rang = 1;
	if ($precedent){
		$cond = array($d['primary']."=".intval($precedent));
		$res = numero_requeter_titre($objet,$cond);
		$row_a = sql_fetch($res);
		$rang = recuperer_numero($row_a['titre']);
		$rang = intval($rang)+1;
	}
	$titre = supprimer_numero($row_o['titre']);
	$titre = "$rang. $titre";

	numero_titrer_objet($objet,$id_objet,$titre);
	// renumeroter la fratrie
	numero_numeroter_objets($objet,$id_parent);

	$res = array('editable'=>true);
	return $res;
}
