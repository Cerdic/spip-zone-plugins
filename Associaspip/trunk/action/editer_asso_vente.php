<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_editer_asso_vente_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_vente = $securiser_action();
	$erreur = '';
	$date_vente = association_recuperer_date('date_vente');
	$article = _request('article');
	$code = _request('code');
	$id_auteur = association_recuperer_entier('id_auteur');
	$acheteur = _request('nom');
	if (!$acheteur) {
		$acheteur = association_formater_idnom($id_auteur, array('spip_asso_membres'), '');
	}
	$quantite = association_recuperer_montant('quantite');
	$date_envoi = association_recuperer_date('date_envoi');
	$frais_envoi = association_recuperer_montant('frais_envoi');
	$prix_unite =  association_recuperer_montant('prix_unitaire');
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
		'nom' => _request('nom'),
		'id_auteur' => $id_auteur,
		'quantite' => $quantite,
		'date_envoi' => $date_envoi,
		'frais_envoi' => $frais_envoi,
		'prix_unitaire' => $prix_unite,
		'commentaire' => _request('commentaire'),
	);
    include_spip('base/association');
	$id_compte = association_recuperer_entier('id_compte');
	$journal = _request('journal');
	$num_vente = "$id_vente : '$code'&nbsp;&times;&nbsp;$quantite";
	$ref_vente = "->vente$id_vente] &mdash; ". ($id_auteur?"[$acheteur"."->membre$id_auteur]":$acheteur) ;
	include_spip('inc/association_comptabilite');
	if ($id_vente) { // modification
		// on modifie les operations comptables associees a la vente
		if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { // si ventes et frais d'envoi sont associes a la meme reference, on modifie une seule operation
			$erreur = comptabilite_operation_modifier($date_vente, $quantite*$prix_unite+$frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
		} else { // sinon on en modifie deux
			$err1 = comptabilite_operation_modifier($date_vente, $quantite*$prix_unite, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente, $id_compte);
			$association_imputation = charger_fonction('association_imputation', 'inc');
			$err2 = comptabilite_operation_modifier($date_envoi, $frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('asso:config_libelle_frais_envoi'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente, sql_getfetsel('id_compte', 'spip_asso_comptes', $association_imputation('pc_frais_envoi', $id_vente)) );
			$erreur = ($err1?$err1:$err2);
		}
		if (!$erreur) // on modifie les informations relatives a la vente
			sql_updateq('spip_asso_ventes', $modifs, "id_vente=$id_vente" );
	} else { // ajout
		// on ajoute les informations relatives a la vente
		$id_vente = sql_insertq('spip_asso_ventes', $modifs);
		if (!$id_vente) { // la suite serait aleatoire sans cette cle...
			$erreur = _T('asso:erreur_sgbdr');
		} else { // on ajoute les operations comptables associees a la vente
			if ($GLOBALS['association_metas']['pc_ventes']==$GLOBALS['association_metas']['pc_frais_envoi']) { // si ventes et frais d'envoi sont associes a la meme reference, on ajoute une seule operation : le cout de revient...
				comptabilite_operation_ajouter($date_vente, $quantite*$prix_unite+$frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente);
			} else { // sinon on en insere deux (meme si les frais sont nuls on les insere afin de pouvoir les modifier ulterieurement)
				comptabilite_operation_ajouter($date_vente, $quantite*$prix_unite, 0, '['. _T('asso:titre_num', array('titre'=>_T('local:vente'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_ventes'], $journal, $id_vente); // revente/achat
				comptabilite_operation_ajouter($date_en, $frais_envoi, 0, '['. _T('asso:titre_num', array('titre'=>_T('asso:config_libelle_frais_envoi'),'num'=>$num_vente) ) .$ref_vente, $GLOBALS['association_metas']['pc_frais_envoi'], $journal, $id_vente); // livraison/port
			}
		}
	}

	return array($id_vente, $erreur);
}

?>