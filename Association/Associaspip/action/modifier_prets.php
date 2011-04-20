<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James & Jeannot Lapin     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_modifier_prets() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_pret=$securiser_action();

		$id_ressource=$_REQUEST['id_ressource']; // text !
		$id_emprunteur=$_REQUEST['id_emprunteur']; // text !
		$date_sortie=$_REQUEST['date_sortie'];
		$duree=$_REQUEST['duree'];
		$date_retour=$_REQUEST['date_retour'];
		$commentaire_sortie=$_REQUEST['commentaire_sortie'];
		$commentaire_retour=$_REQUEST['commentaire_retour'];
		$statut=$_REQUEST['statut'];
		$montant=$_REQUEST['montant'];
		$journal=$_REQUEST['journal'];
		$imputation=$GLOBALS['association_metas']['pc_prets'];

	include_spip('base/association');
	prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant);
	spip_log("modifier pret $id_pret");
}

function prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant)
{
	sql_updateq('spip_asso_prets', array(
		"duree" => $duree,
		"date_sortie" => $date_sortie,
		"date_retour" => $date_retour,
		"id_emprunteur" => $id_emprunteur,
		"commentaire_sortie" => $commentaire_sortie),
			"id_pret=$id_pret" );

	sql_updateq('spip_asso_comptes', array(
		"journal" => $journal,
		"recette" => $montant,
		"date" => $date_sortie),
			"id_journal=$id_pret");
}

?>
