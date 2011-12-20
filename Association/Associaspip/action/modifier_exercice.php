<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */


if (!defined("_ECRIRE_INC_VERSION"))
	return;

function action_modifier_exercice() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_exercice = $securiser_action();

	$intitule = _request('intitule');
	$commentaire = _request('commentaire');
	$debut = _request('debut');
	$fin = _request('fin');

	include_spip('base/association');
	sql_updateq('spip_asso_exercices', array(
		'intitule' => $intitule,
		'commentaire' => $commentaire,
		'debut' => $debut,
		'fin' => $fin),
		"id_exercice=$id_exercice");
}

?>
