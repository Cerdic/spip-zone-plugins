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

function action_synchroniser_asso_membres() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	if (_request('tous')) {
		$where = "statut <> '5poubelle'";
	} else {
		$liste_statuts = association_recuperer_liste('imp', TRUE);
		$where = sql_in('statut', $liste_statuts) ." OR (statut='nouveau' AND ". sql_in('bio', $liste_statuts) .')'; // cas des redacteurs jamais connectes : leur statut est dans le champ bio
	}
	if (!_request('forcer')) { // on recupere les id de tous les membres deja presents pour ne pas les traiter
		$id_membres = sql_select('id_auteur', 'spip_asso_membres');
		if ($id_membres) {
			$liste_membres = array();
			while ($id_membre = sql_fetch($id_membres)) {
				$liste_membres[] = $id_membre['id_auteur'];
			}
			$where = '('.$where.') AND '. sql_in('id_auteur', $liste_membres, 'NOT');
		}
		sql_free($id_membres);
	}

	$auteurs = sql_select('id_auteur', 'spip_auteurs', $where);
	$nb_modifs = sql_count($auteurs);

	if ($auteurs) {
		include_spip('association_pipelines');
		while ($auteur = sql_fetch($auteurs)) {
			update_spip_asso_membre($auteur['id_auteur']);
		}
	}
	sql_free($auteurs);

	return $nb_modifs; // on retourne le nombre de membres inseres dans la table
}

?>