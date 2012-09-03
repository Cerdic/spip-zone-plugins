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

include_spip ('inc/navigation_modules');

function exec_ajout_participation()
{
	$id_activite = intval(_request('id'));
	if (!autoriser('associer', 'activites', $id_activite)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		if ($id_activite)
			$id_evenement = sql_getfetsel('id_evenement', 'spip_asso_activites', "id_activite=$id_activite");
		else
			$id_evenement = intval(_request('id_evenement'));
		onglets_association('titre_onglet_activite');
		// INTRO : Rappel Infos Evenement
		$evenement = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement");
		$infos['evenement_date_debut'] = association_formater_date($evenement['date_debut'],'dtstart').' '.substr($data['date_debut'],10,6);
		$infos['evenement_date_fin'] = association_formater_date($evenement['date_fin'],'dtend').' '.substr($data['date_debut'],10,6);
		$infos['evenement_lieu'] = $evenement['lieu'];
		echo association_totauxinfos_intro($evenement['titre'], 'evenement', $id_evenement, $infos, 'agenda');
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association(($id_activite?'activites.gif':'panier_in.gif'), 'activite_titre_ajouter_inscriptions');
		echo recuperer_fond('prive/editer/ajouter_participation', array (
			'id_activite' => $id_activite,
		));
	}
}

?>