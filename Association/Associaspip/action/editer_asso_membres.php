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

function action_editer_asso_membres()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$modifs = array(
		'commentaire' => _request('commentaire'),
		'validite' => association_recuperer_date('validite'),
		'categorie' => _request('categorie'),
		'statut_interne' => _request('statut_interne'),
		'nom_famille' => _request('nom_famille'),
#		'fonction' => _request('fonction'),
	);
	// pour ne pas ecraser les champs quand ils sont desactives
	if ($GLOBALS['association_metas']['civilite'])
		$modifs['sexe'] = _request('sexe');
	if ($GLOBALS['association_metas']['prenom'])
		$modifs['prenom'] = _request('prenom');
	if ($GLOBALS['association_metas']['id_asso'])
		$modifs['id_asso'] = _request('id_asso');
	include_spip('base/association');
	// on passe par modifier_contenu pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere
	include_spip('inc/modifier');
	modifier_contenu(
		'asso_membre', // table a modifier
		$id_auteur, // identifiant
		'', // parametres
		$modifs // champs a modifier
	);

	return (array($id_auteur,''));
}

?>