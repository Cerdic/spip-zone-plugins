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
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function action_editer_asso_prets()
{

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_categorie=$securiser_action();

    $libelle = _request('libelle');
    $valeur = _request('valeur'));
    $duree = association_recupere_montant(_request('duree'));
    $cotisation = association_recupere_montant(_request('cotisation'));
    $commentaires = _request('commentaires');

    include_spip('base/association');
    if ($id_categorie) { /* modification */
	categories_modifier($id_categorie, $cotisation, $valeur, $duree, $libelle, $commentaires);
    } else { /* ajout */
	$id_categorie = categories_inserer($cotisation, $valeur, $duree, $libelle, $commentaires);

    }

    return array($id_categorie, '');
}

function categories_modifier($id_categorie, $cotisation, $valeur, $duree, $libelle, $commentaires)
{
    sql_updateq('spip_asso_categories', array(
	'duree' => $duree,
	'libelle' => $libelle,
	'cotisation' => $cotisation,
	'valeur' => $valeur,
	'commentaires' => $commentaires
    ), "id_categorie=$id_categorie");
}

function categories_inserer($cotisation, $valeur, $duree, $libelle, $commentaires)
{
    $id_categorie = sql_insertq('spip_asso_categories', array(
	'duree' => $duree,
	'libelle' => $libelle,
	'cotisation' => $cotisation,
	'valeur' => $valeur,
	'commentaires' => $commentaires
    ));
}

?>
