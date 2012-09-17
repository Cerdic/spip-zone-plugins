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

function action_synchroniser_asso_activites() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$evt = intval(_request('id_evenement'));
	$act = array(); // liste des id_activite rajoutes
	$imp = _request('imp');
	$anciennes_reponses = sql_in_select('id_auteur', 'id_adherent', 'spip_asso_activites', "id_evenement=$evt");
	$nouvelles_reponses = sql_select('id_auteur, date', 'spip_evenements_participants', "id_evenement=$evt AND " . sql_in('reponse', (is_array($imp)?$imp:array($imp)) ) . " AND NOT $anciennes_reponses" );
	while ($nouvelle_reponse = sql_fetch($nouvelles_reponses)) { // inserer un a un
		$act[] = sql_insertq('spip_asso_activites', array(
			'id_evenement' => $evt,
			'id_adherent' => $nouvelle_reponse['id_auteur'],
			'date_inscription' => $nouvelle_reponse['date'],
		));
	}
	sql_free($nouvelles_reponses);
	return count($act); // on retourne le nombre de membres inseres dans la table
}

?>