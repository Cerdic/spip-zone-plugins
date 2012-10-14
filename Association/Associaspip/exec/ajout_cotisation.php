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
	list($id_auteur, $membre) = association_passeparam_id('auteur', 'asso_membres');
	if (!autoriser('associer', 'adherents', $id_auteur)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('titre_onglet_membres', 'adherents');
		// info : membre et categorie par defaut
		$categorie = sql_fetsel('*', 'spip_asso_categories', 'id_categorie='. intval($membre['id_categorie']));
		$infos['adherent_libelle_categorie'] = $categorie['libelle'];
		$infos['entete_montant'] = association_formater_prix($categorie['prix_cotisation']);
		$infos['adherent_libelle_validite'] = association_formater_date($membre['date_validite'], 'dtend');
		echo association_totauxinfos_intro(htmlspecialchars(association_formater_nom($membre['sexe'], $membre['prenom'], $membre['nom_famille'])), 'membre', $id_auteur, $infos );
		// datation et raccourcis
		raccourcis_association('', array(
			'voir_adherent' => array('edit-24.gif', array('adherent', "id=$id_auteur") ),
		));
		debut_cadre_association('annonce.gif', 'nouvelle_cotisation');
		echo recuperer_fond('prive/editer/ajouter_cotisation', array (
			'id_auteur' => $id_auteur,
			'nom_prenom' => association_formater_nom($membre['sexe'], $membre['prenom'], $membre['nom_famille']),
			'categorie' => $membre['id_categorie'],
			'validite' => $membre['date_validite'],
		));
		fin_page_association();
	}
}

?>