<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_prets() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_pret = $securiser_action();

	$id_ressource=$_REQUEST['id_ressource']; // text !
	$id_emprunteur=$_POST['id_emprunteur']; // text !
	$date_sortie=$_POST['date_sortie'];
	$duree=$_POST['duree'];
	$date_retour=$_POST['date_retour'];
	$commentaire_sortie=$_POST['commentaire_sortie'];
	$commentaire_retour=$_POST['commentaire_retour'];
	$statut=$_POST['statut'];
	$montant=$_POST['montant'];
	$journal=$_POST['journal'];
	$imputation=$GLOBALS['association_metas']['pc_prets'];

	prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $imputation, $commentaire_sortie,$commentaire_retour);
}

function prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $imputation, $commentaire_sortie,$commentaire_retour)
{
	$id_pret = sql_insertq('spip_asso_prets', array(
		'id_ressource' => $id_ressource,
		'date_sortie' => $date_sortie,
		'duree' => $duree,
		'date_retour' => $date_retour,
		'id_emprunteur' => $id_emprunteur,
		'commentaire_sortie' => $commentaire_sortie,
		'commentaire_retour' => $commentaire_retour));

	if ($id_pret)
		$id_pret = sql_insertq('spip_asso_comptes', array(
			'date' => $date_sortie,
			'journal' => $journal,
			'recette' => $montant,
			'justification' => _T('asso:pret_nd').$id_ressource.'/'.$id_pret,
			'imputation' => $imputation,
			'id_journal' => $id_pret));

	if ($id_pret)
		sql_updateq('spip_asso_ressources',
			    array('statut' => 'reserve'),
			    "id_ressource=$id_ressource");
	spip_log("prets_insert: $id_pret");
}

?>
