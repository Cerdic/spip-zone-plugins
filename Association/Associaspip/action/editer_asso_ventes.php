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

function action_editer_asso_ventes()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_vente = $securiser_action();
	$erreur = '';
	$date_vente = association_recuperer_date('date_vente');
	$article = _request('article');
	$code = _request('code');
	$id_acheteur = intval(_request('id_acheteur'));
	$acheteur = _request('acheteur');
	if (!$acheteur AND $id_acheteur) {
		$data =  sql_fetsel('sexe, nom_famille, prenom', 'spip_asso_membres', "id_auteur=$id_acheteur");
		$acheteur = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
	}
	$quantite = association_recuperer_montant('quantite');
	$date_envoi = association_recuperer_date('date_envoi');
	$frais_envoi = association_recuperer_montant('frais_envoi');
	$prix_unite =  association_recuperer_montant('prix_vente');
	if (test_plugin_actif('CATALOGUE') && intval($code)==$code) { // le plugin "Catalogue est actif" : certains champs peuvent ne pas etre saisis...
		if ($code>0) { // c'est une variante
			$variante = sql_fetsel('*', 'spip_cat_variantes', 'id_cat_variante='.$code);
			if (!$article)
				$article = '[->art'.$variante['id_article'].'] : '.$variante['titre'];
			if (!$prix_unite)
				$prix_unite = $variante['prix']*(1+$variante['tva']);
		} else { // c'est une option
			$option = sql_fetsel('*', 'spip_cat_options', 'id_cat_option='.abs($code));
			if (!$article)
				$article = '[->art'.$option['id_article'].'] &amp; '.$option['titre'];
#			if (!$prix_unite) // normalement le prix de l'option s'ajoute au prix de base...
				$prix_unite += $option['prix']*(1+$option['tva']);
		}
	}
	$modifs = array(
		'date_vente' => $date_vente,
		'article' => $article,
		'code' => $code,
		'acheteur' => _request('acheteur'),
		'id_acheteur' => $id_acheteur,
		'quantite' => $quantite,
		'date_envoi' => $date_envoi,
		'frais_envoi' => $frais_envoi,
		'prix_vente' => $prix_unite,
		'commentaire' => _request('commentaire'),
	);
    include_spip('base/association');
	$id_compte = intval(_request('id_compte'));
	$journal = _request('journal');
	include_spip('inc/association_comptabilite');
	if ($id_vente) { // modification
		// on modifie les operations comptables associees a la vente
		if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { // si ventes et frais d'envoi sont associes a la meme reference, on modifie une seule operation
			association_modifier_operation_comptable($date_vente, $quantite*$prix_unite+$frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>"$id_vente : '$code' &times;&nbsp;$quantite") ) ."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
		} else { // sinon on en modifie deux
			association_modifier_operation_comptable($date_vente, $quantite*$prix_unite, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>"$id_vente : '$code' &times;&nbsp;$quantite") ) ."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$critere = $association_imputation('pc_frais_envoi');
			$critere .= ($critere?' AND ':'') ."id_journal=$id_vente";
			$erreur = association_modifier_operation_comptable($date_envoi, $frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('asso:config_libelle_frais_envoi'),'num'=>$id_vente) ) ."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente, sql_getfetsel('id_compte', 'spip_asso_comptes', $critere));
		}
		// on modifie les informations relatives a la vente
		sql_updateq('spip_asso_ventes', $modifs, "id_vente=$id_vente" );
	} else { // ajout
		// on ajoute les informations relatives a la vente
		$id_vente = sql_insertq('spip_asso_ventes', $modifs);
		if (!$id_vente) { // la suite serait aleatoire sans cette cle...
			$erreur = _T('asso:erreur_sgbdr');
		} else { // on ajoute les operations comptables associees a la vente
			if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { // si ventes et frais d'envoi sont associes a la meme reference, on ajoute une seule operation
				association_ajouter_operation_comptable($date_vente, $quantite*$prix_unite+$frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>"$id_vente : '$code' &times;&nbsp;$quantite") ) ."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
			} else { // sinon on en insere deux
				association_ajouter_operation_comptable($date_vente, $quantite*$prix_unite, 0, "[ref&nbsp;'$code' &times;&nbsp;$quantite"."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
				association_ajouter_operation_comptable($date_en, $frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('asso:config_libelle_frais_envoi'),'num'=>$id_vente) ) ."->vente$id_vente] &mdash; ". ($id_acheteur?"[$acheteur"."->membre$id_acheteur]":$acheteur), $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente);
			}
		}
	}

	return array($id_vente, $erreur);
}

?>