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


if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_editer_asso_categories_dist()
{

    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_categorie = $securiser_action();
    $erreur = '';
    $champs = array(
	'libelle' => _request('libelle'),
	'valeur' => _request('valeur'),
	'duree' => association_recuperer_montant('duree'),
	'cotisation' => association_recuperer_montant('cotisation'),
	'commentaire' => _request('commentaire'),
    );
    include_spip('base/association');
    if ($id_categorie) { // modification
	sql_updateq('spip_asso_categories', $champs, "id_categorie=$id_categorie");
    } else { // ajout
	$id_categorie = sql_insertq('spip_asso_categories', $champs);
	if (!$id_categorie)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_categorie, $erreur);
}

?>