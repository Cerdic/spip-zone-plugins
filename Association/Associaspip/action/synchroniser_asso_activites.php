<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_synchroniser_asso_activites() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$evt = association_recuperer_entier('id_evenement');
	$act = array(); // liste des id_activite rajoutes
	$imp = association_recuperer_liste('imp', TRUE); // liste des reponses a importer
	$anciennes_reponses = sql_in_select('id_auteur', 'id_auteur', 'spip_asso_activites', "id_evenement=$evt");
	$nouvelles_reponses = sql_select('id_auteur, date', 'spip_evenements_participants', "id_evenement=$evt AND " . sql_in('reponse', $imp) . " AND NOT $anciennes_reponses" );
	while ($nouvelle_reponse = sql_fetch($nouvelles_reponses)) { // inserer un a un
		$act[] = sql_insertq('spip_asso_activites', array(
			'id_evenement' => $evt,
			'id_auteur' => $nouvelle_reponse['id_auteur'],
			'date_inscription' => $nouvelle_reponse['date'],
		));
	}
	sql_free($nouvelles_reponses);
	return count($act); // on retourne le nombre de membres inseres dans la table
}

?>