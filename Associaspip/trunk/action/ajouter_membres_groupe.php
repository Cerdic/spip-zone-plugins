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

function action_ajouter_membres_groupe() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_groupe = $securiser_action();
	$id_auteurs = association_recuperer_liste('id_auteurs', TRUE);
	$id_groupes = _request('id_groupes', TRUE);

	// cette action peut etre appelee selon deux modes
	if (is_array($id_groupes)) { // mode d'appel 2 : depuis la page action_adherents et on a potentiellement plusieurs id_groupe a recuperer dans un tableau
		foreach ($id_groupes as $id_groupe) {
			insertion_membres($id_groupe, $id_auteurs);
		}
	} else { // mode d'appel 1 : depuis la page d'edition d'un groupe et dans ce cas on recupere l'id_groupe avec securiser_action
		insertion_membres($id_groupe, $id_auteurs);
	}
	return;
}

function insertion_membres($id_groupe, $id_auteurs) {
	// l'interface d'ajout de membres depuis la liste des adherents permet d'ajouter a un groupe des membres qui en font deja partie
	// il faut donc filter les ajouts pour en exclure ceux qui y sont deja sinon c'est erreur SQL et les ajouts ne se font pas
	$membres = array();
	$query = sql_select('id_auteur', 'spip_asso_fonctions', 'id_groupe='.$id_groupe);
	while ($row = sql_fetch($query)) {
		$membres[] = $row['id_auteur'];
	}
	sql_free($query);

	$insert_data = array();
	foreach ($id_auteurs as $id_auteur) {
		if (!in_array($id_auteur, $membres)) {
			$insert_data[] = array('id_groupe' => $id_groupe, 'id_auteur' => $id_auteur);
		}
	}
	if (count($insert_data)) {
		sql_insertq_multi('spip_asso_fonctions', $insert_data);
	}
}

?>