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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/navigation_modules');

function exec_action_exercice()
{
	if (!autoriser('associer', 'exercices')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_exercice = intval(_request('id'));
		onglets_association('exercices_budgetaires_titre');
		// info
		$exercice = sql_fetsel('*', 'spip_asso_exercices', "id_exercice=$id_exercice" );
		$infos['exercice_entete_debut'] = association_datefr($exercice['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_datefr($exercice['fin'], 'dtend');
		echo totauxinfos_intro(sql_getfetsel('intitule', 'spip_asso_exercices', "id_exercice=$id_exercice" ), 'exercice', $id_exercice, $infos);
		// datation et raccourcis
		icones_association(array('exercices'));
		debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
		echo bloc_confirmer_suppression('exercice', $id_exercice);
		fin_page_association();
	}
}

?>
