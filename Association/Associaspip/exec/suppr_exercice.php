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

function exec_suppr_exercice()
{
	if (!autoriser('associer', 'exercices')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_exercice = intval(_request('id'));
		onglets_association('exercices_budgetaires_titre', 'association');
		// info
		$exercice = sql_fetsel('*', 'spip_asso_exercices', "id_exercice=$id_exercice" );
		$infos['exercice_entete_debut'] = association_formater_date($exercice['debut'], 'dtstart');
		$infos['exercice_entete_fin'] = association_formater_date($exercice['fin'], 'dtend');
		echo association_totauxinfos_intro($exercice['intitule'], 'exercice', $id_exercice, $infos);
		// datation et raccourcis
		raccourcis_association('exercices');
		debut_cadre_association('calculatrice.gif', 'exercice_budgetaire_titre');
		echo association_bloc_suppression('exercice', $id_exercice);
		fin_page_association();
	}
}

?>