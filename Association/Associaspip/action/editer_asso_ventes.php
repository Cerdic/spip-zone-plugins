<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function action_editer_asso_ventes(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_vente = $securiser_action();
	$erreur = '';
	include_spip('inc/association_comptabilite');
	$id_compte = intval(_request('id_compte'));
	$date_vente = _request('date_vente');
	$article = _request('article');
	$code = _request('code');
	$id_acheteur = intval(_request('id_acheteur'));
	$acheteur = _request('acheteur');
	if (!$acheteur AND $id_acheteur) {
		$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_acheteur");
		$acheteur = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
	}
	$quantite = association_recupere_montant(_request('quantite'));
	$date_envoi = _request('date_envoi');
	$frais_envoi = association_recupere_montant(_request('frais_envoi'));
	$prix_unite =  association_recupere_montant(_request('prix_vente'));
	$journal = _request('journal');
	$commentaire = _request('commentaire');
	if (test_plugin_actif('CATALOGUE') && intval($code)==$code) { // le plugin "Catalogue est actif" : certains champs peuvent ne pas etre saisis...
		if ($code>0) { // c'est une variante
			$variante = sql_fetsel('*', 'spip_cat_variantes', 'id_cat_variante='.$code);
			if (!$article)
				$article = $variante['id_article'];
			if ($prix_unite==0)
				$prix_unite = $variante['prix']*(1+$variante['tva']);
		} else { // c'est une option
			$option = sql_fetsel('*', 'spip_cat_options', 'id_cat_option='.abs($code));
			if (!$article)
				$article = $option['id_article'];
			if ($prix_unite==0)
				$prix_unite = $option['prix']*(1+$option['tva']);
		}
	}
	$prix_total = $quantite*$prix_unite;

	if ($id_vente) { /* modification */
		sql_updateq('spip_asso_ventes', array(
			'date_vente' => $date_vente,
			'article' => $article,
			'code' => $code,
			'acheteur' => $acheteur,
			'id_acheteur' => $id_acheteur,
			'quantite' => $quantite,
			'date_envoi' => $date_envoi,
			'frais_envoi' => $frais_envoi,
			'prix_vente' => $prix_unite,
			'commentaire' => $commentaire,
		), "id_vente=$id_vente" );
		if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { /* si ventes et frais d'envoi sont associes a la meme reference, on modifie une seule operation */
			association_modifier_operation_comptable($date_vente, $prix_total+$frais_envoi, 0, "[vente$id_vente->vente$id_vente] : $quantite &times; '$code' -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
		} else { /* sinon on en modifie deux */
			association_modifier_operation_comptable($date_vente, $prix_total, 0, "[vente$id_vente->vente$id_vente] : $quantite &times; '$code' -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_frais_envoi');
			$critere .= ($critere?' AND ':'') ."id_journal=$id_vente";
			association_modifier_operation_comptable($date_vente, $frais_envoi, 0, "[vente$id_vente->vente$id_vente] : frais d'envoi -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente, sql_getfetsel('id_compte', 'spip_asso_comptes', $critere));
		}
	} else { /* ajout */
		$id_vente = sql_insertq('spip_asso_ventes', array(
			'date_vente' => $date_vente,
			'article' => $article,
			'code' => $code,
			'acheteur' => $acheteur,
			'id_acheteur' => $id_acheteur,
			'quantite' => $quantite,
			'date_envoi' => $date_envoi,
			'frais_envoi' => $frais_envoi,
			'prix_vente' => $prix_unite,
			'commentaire' => $commentaire,
		) );
		if (!$id_vente) { // la suite serait aleatoire sans cette cle...
			$erreur = _T('Erreur_BdD_ou_SQL');
		} else { // on ajoute les operations comptables associees a la vente
			if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { /* si ventes et frais d'envoi sont associes a la meme reference, on ajoute une seule operation */
				association_ajouter_operation_comptable($date_vente, $prix_total+$frais_envoi, 0, "[vente$id_vente->vente$id_vente] : $quantite &times; '$code' -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
			} else { /* sinon on en insere deux */
				association_ajouter_operation_comptable($date_vente, $prix_total, 0, "[vente$id_vente->vente$id_vente] : $quantite &times; '$code' -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
				association_ajouter_operation_comptable($date_vente, $frais_envoi, 0, "[vente$id_vente->vente$id_vente] : frais d'envoi -- ". ($id_acheteur?"[$acheteur->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente);
			}
		}
	}

	return array($id_vente, $erreur);
}

?>