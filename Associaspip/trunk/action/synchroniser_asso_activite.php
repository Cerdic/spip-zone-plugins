<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_synchroniser_asso_activite() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$evt = association_recuperer_entier('id_evenement');
	$lia = array(); // Liste des Inscriptions Ajoutees
	switch ( _request('dir') ) { // direction de la synchro
		case 'imp' : // import: evenements_participants -> asso_activites
			$imp = association_recuperer_liste('imp', TRUE); // liste des reponses a importer
			$sauf = sql_in_select('id_auteur', 'id_auteur', 'spip_asso_activites', "id_evenement=$evt"); // anciennes inscriptions
			$q = sql_select('id_auteur, date', 'spip_evenements_participants', "id_evenement=$evt AND " . sql_in('reponse', $imp) . " AND NOT $sauf" ); // nouvelles inscriptions
			while ( $new = sql_fetch($q) ) // inserer un a un (c'est moins performant que d'inserer en lot mais le code et la requete en est plus simple)
				$lia[] = sql_insertq('spip_asso_activites', array(
					'id_evenement' => $evt,
					'id_auteur' => $new['id_auteur'],
					'date_inscription' => $new['date'],
				));
			sql_free($q);
			break;
		case 'exp' : // export: asso_activites -> evenements_participants
			$rep = _request('exp'); // reponse supposee
			sql_free($q);
			$q = sql_select('id_auteur, date_inscription', 'spip_asso_activites', "id_evenement=$evt"); // inscriptions
			while ( $act = sql_fetch($q) ) // inserer un par un (c'est moins performant que d'inserer en lot, mais d'une part on ne plante pas tout si on tente d'inserer en doublon et d'autre part le compte est celui des insertions reussies)
				$lia[] = sql_insertq('spip_evenements_participants', array(
					'id_evenement' => $evt,
					'id_auteur' => $act['id_auteur'],
					'date' => $act['date_inscription'],
				));
			sql_free($q);
			break;
		default: // nop
			break;
	}
	return count($act); // on retourne le nombre d'insertions faites
}

?>