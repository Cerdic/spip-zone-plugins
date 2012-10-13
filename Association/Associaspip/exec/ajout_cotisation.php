<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_ajout_cotisation() {
	include_spip('inc/navigation_modules');
	list($id_auteur, $row) = association_passeparam_id('auteur', 'asso_membres');
	if (!autoriser('associer', 'adherents', $id_auteur)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('titre_onglet_membres', 'adherents');
		// info : membre et categorie par defaut
		$categorie = sql_fetsel('*', 'spip_asso_categories', 'id_categorie='. intval($row['categorie']));
		$infos['adherent_libelle_categorie'] = $categorie['libelle'];
		$infos['entete_montant'] = association_formater_prix($categorie['cotisation']);
		$infos['adherent_libelle_validite'] = association_formater_date($row['validite']);
		echo association_totauxinfos_intro(htmlspecialchars(association_formater_nom($row['sexe'], $row['prenom'], $row['nom_famille'])), 'membre', $id_auteur, $infos );
		// datation et raccourcis
		raccourcis_association('', array(
			'voir_adherent' => array('edit-24.gif', array('adherent', "id=$id_auteur") ),
		));
		debut_cadre_association('annonce.gif', 'nouvelle_cotisation');
		echo recuperer_fond('prive/editer/ajouter_cotisation', array (
			'id_auteur' => $id_auteur,
			'nom_prenom' => association_formater_nom($row['sexe'], $row['prenom'], $row['nom_famille']),
			'categorie' => $row['categorie'],
			'validite' => $row['validite'],
		));
		fin_page_association();
	}
}

?>