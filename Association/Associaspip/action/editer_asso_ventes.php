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
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

function action_editer_asso_ventes(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_vente=$securiser_action();

	include_spip('base/association');
	include_spip('inc/association_comptabilite');
		
	$id_compte=intval(_request('id_compte'));
	
	$date_vente = _request('date_vente');
	$article = _request('article');
	$code = _request('code');
	$acheteur = _request('acheteur');
	$id_acheteur = intval(_request('id_acheteur'));
	$quantite = association_recupere_montant(_request('quantite'));

	$date_envoi = _request('date_envoi');
	$frais_envoi = association_recupere_montant(_request('frais_envoi'));
	$prix_vente =  association_recupere_montant(_request('prix_vente'));
	
	$journal = _request('journal');
	$justification='[vente n&deg; '.$id_vente.'->vente'.$id_vente.'] - '.$article;
	$commentaire=$_POST['commentaire'];
	$recette=$quantite*$prix_vente;

	/* modification */
	if ($id_vente) {
		ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification, $recette, $id_compte);
	} else { /* ajout */
		$id_vente = ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette);

	}

	return array($id_vente, '');
}

function ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification, $recette, $id_compte)
{
	include_spip('inc/association_comptabilite');
	sql_updateq('spip_asso_ventes', array(
		"date_vente" => $date_vente,
		"article" => $article,
		"code" => $code,
		"acheteur" => $acheteur,
		"id_acheteur" => $id_acheteur,
		"quantite" => $quantite,
		"date_envoi" => $date_envoi,
		"frais_envoi" => $frais_envoi,
		"prix_vente" => $prix_vente,
		"commentaire" => $commentaire),
		    "id_vente=$id_vente" );

	if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) {
		/* si ventes et frais d'envoi sont associes a la meme reference, on modifie une seule operation */
		association_modifier_operation_comptable($date_vente, $recette+$frais_envoi, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
	} else { /* sinon on en modifie deux */
		association_modifier_operation_comptable($date_vente, $recette, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
		association_modifier_operation_comptable($date_vente, $frais_envoi, 0, $justification.' - frais d\'envoi', $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente, sql_getfetsel("id_compte", "spip_asso_comptes", "imputation=".$GLOBALS['association_metas']['pc_frais_envoi']." AND id_journal=$id_vente"));
	}
}

function ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette)
{
	include_spip('inc/association_comptabilite');
	$id_vente = sql_insertq('spip_asso_ventes', array(
		'date_vente' => $date_vente,
		'article' => $article,
		'code' => $code,
		'acheteur' => $acheteur,
		'id_acheteur' => $id_acheteur,
		'quantite' => $quantite,
		'date_envoi' => $date_envoi,
		'frais_envoi' => $frais_envoi,
		'prix_vente' => $prix_vente,
		'commentaire' => $commentaire));

	$justification='[vente n&deg; '.$id_vente.'->vente'.$id_vente.'] - '.$article;
	if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) {
		/* si ventes et frais d'envoi sont associes a la meme reference, on ajoute une seule operation */
		association_ajouter_operation_comptable($date_vente, $recette+$frais_envoi, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
	} else { /* sinon on en insere deux */
		association_ajouter_operation_comptable($date_vente, $recette, 0, $justification, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
		association_ajouter_operation_comptable($date_vente, $frais_envoi, 0, $justification.' - frais d\'envoi', $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente);
	}
	return $id_vente;
}

?>
