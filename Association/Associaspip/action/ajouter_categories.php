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

function action_ajouter_categories() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$libelle = _request('libelle');
	$valeur = _request('valeur');
	$duree = _request('duree');
	$cotisation = _request('cotisation');
	$commentaires = _request('commentaires');

	categories_insert($cotisation, $valeur, $duree, $libelle, $commentaires);
}

function categories_insert($cotisation, $valeur, $duree, $libelle, $commentaires)
{
	include_spip('base/association');		
	$id_categorie = sql_insertq('spip_asso_categories', array(
		'duree' => $duree,
		'libelle' => $libelle,
		'cotisation' => $cotisation,
		'valeur' => $valeur,
		'commentaires' => $commentaires));
}
?>
