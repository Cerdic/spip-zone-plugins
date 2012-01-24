<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_asso_categories_dist()
{

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_categorie = $securiser_action();
    $erreur = '';

    include_spip('inc/association_comptabilite');
    $libelle = _request('libelle');
    $valeur = _request('valeur');
    $duree = association_recupere_montant(_request('duree'));
    $cotisation = association_recupere_montant(_request('cotisation'));
    $commentaires = _request('commentaires');

    include_spip('base/association');
    if ($id_categorie) { /* modification */
	categorie_modifier($id_categorie, $cotisation, $valeur, $duree, $libelle, $commentaires);
    } else { /* ajout */
	$id_categorie = categorie_ajouter($cotisation, $valeur, $duree, $libelle, $commentaires);
	if (!$id_categorie)
	    $erreur = _T('Erreur_BdD_ou_SQL');
    }

    return array($id_categorie, $erreur);
}

function categorie_modifier($id_categorie, $cotisation, $valeur, $duree, $libelle, $commentaires)
{
    sql_updateq('spip_asso_categories', array(
	'duree' => $duree,
	'libelle' => $libelle,
	'cotisation' => $cotisation,
	'valeur' => $valeur,
	'commentaires' => $commentaires
    ), "id_categorie=$id_categorie");
}

function categorie_ajouter($cotisation, $valeur, $duree, $libelle, $commentaires)
{
    $id_categorie = sql_insertq('spip_asso_categories', array(
	'duree' => $duree,
	'libelle' => $libelle,
	'cotisation' => $cotisation,
	'valeur' => $valeur,
	'commentaires' => $commentaires
    ));
    return $id_categorie;
}

?>