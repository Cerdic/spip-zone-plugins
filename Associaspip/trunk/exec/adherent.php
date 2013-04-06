<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_adherent() {
	include_spip('association_modules');
	include_spip('inc/adherent');
	$id_auteur = association_passeparam_id('auteur');
	$full = autoriser('editer_membres', 'association');
	if (!autoriser('voir_membres', 'association', $id_auteur)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$data = sql_fetsel('m.sexe, m.nom_famille, m.prenom, m.date_validite, m.id_asso, c.libelle, m.commentaire','spip_asso_membres as m LEFT JOIN spip_asso_categories as c ON m.id_categorie=c.id_categorie', "m.id_auteur=$id_auteur");
		include_spip('inc/association_comptabilite');
		$nom_membre = association_formater_nom($data['sexe'], $data['prenom'], $data['nom_famille']);
		$validite = $data['date_validite'];
		$adresses = association_formater_adresses(array($id_auteur));
		$emails = association_formater_emails(array($id_auteur));
		$telephones = association_formater_telephones(array($id_auteur));
		$sites = association_formater_urls(array($id_auteur));
		$categorie = $data['libelle']?$data['libelle']:_T('asso:pas_de_categorie_attribuee');
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case '0minirezo':
				$statut='auteur'; break;
			case '1comite':
				$statut='auteur'; break;
			default :
				$statut='visiteur'; break;
		}
		echo association_navigation_onglets('titre_onglet_membres', 'adherents');
		// INFOS
		if ($full) {
			$infos['adherent_libelle_categorie'] = $categorie;
		}
		$infos['adherent_libelle_validite'] = association_formater_date($data['date_validite']);
		if ($GLOBALS['association_metas']['id_asso']) {
			$infos['adherent_libelle_reference_interne'] = ($data['id_asso']?$data['id_asso']:_T('asso:pas_de_reference_interne_attribuee')) ;
		}
		if (isset($adresses[$id_auteur]))
			$infos['coordonnees:adresses'] = $adresses[$id_auteur];
		if (isset($emails[$id_auteur]))
			$infos['coordonnees:emails'] = $emails[$id_auteur];
		if (isset($telephones[$id_auteur]))
			$infos['coordonnees:numeros'] =  $telephones[$id_auteur];
		if (isset($sites[$id_auteur]))
			$infos['coordonnees:pages'] =  $sites[$id_auteur];
		echo '<div class="vcard">'. association_totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos, 'asso_membre') .'</div>';
		// datation et raccourcis
		$raccourcis = array(
			array('adherent_titre_liste_actifs', 'grille-24.png', array('adherents', "id=$id_auteur"), array('voir_membres', 'association', 0) ),
			array('adherent_label_modifier_membre', 'edit-24.gif', array('edit_adherent', "id=$id_auteur"), array('editer_membres', 'association') ),
			array("adherent_label_modifier_$statut", 'membre_infos.png', array('auteur_infos', "id_auteur=$id_auteur"), autoriser('voir', 'auteur', $id_auteur) ),
		);
		if ($GLOBALS['association_metas']['pc_cotisations'])
			$raccourcis[] = array('adherent_label_ajouter_cotisation', 'cotis-12.gif', array('ajout_cotisation', "id_auteur=$id_auteur"), array('ajouter_cotisation', 'association', $id_auteur) );
		if ($GLOBALS['association_metas']['pc_dons'])
			$raccourcis[] = array('ajouter_un_don', 'ajout-24.png', array('edit_don', "id_auteur=$id_auteur"), array('editer_dons', 'association', $id_auteur) );
		echo association_navigation_raccourcis( $raccourcis, 12);
		debut_cadre_association('annonce.gif', 'membre');
		if ( autoriser('voir_groupes', 'association') )
			echo propre($data['commentaire']);
		$query_groupes = sql_select('g.*, fonction', 'spip_asso_groupes g LEFT JOIN spip_asso_groupes_liaisons l ON g.id_groupe=l.id_groupe', 'g.id_groupe>=100 AND l.id_auteur='.$id_auteur, '', 'g.nom'); // Liste des groupes (on ignore les groupes d'id <100 qui sont dedies a la gestion des autorisations)
		if (sql_count($query_groupes)) {
			echo debut_cadre_relief('', TRUE, '', _T('asso:groupes_membre') );
			echo association_bloc_listehtml2('asso_groupes',
				$query_groupes, // requete
				array(
					'id_groupe' => array('asso:entete_id', 'entier'),
					'nom' => array('asso:groupe', 'texte'),
					'fonction' => array('asso:fonction', 'texte'),
				), // entetes et formats des donnees
				array(
					array('list', 'membres_groupe', 'id=$$')
				), // boutons d'action
				'id_groupe' // champ portant la cle des lignes et des boutons
			);
			echo fin_cadre_relief(TRUE);
		}

		if ($GLOBALS['association_metas']['recufiscal']) {
			$t =  _T('asso:liens_vers_les_justificatifs');
			echo debut_cadre_relief('', TRUE, '', $t);
			$t = voir_adherent_recu_fiscal($id_auteur);
			echo $t ? $t : _T('asso:aucun');
			echo fin_cadre_relief(TRUE);
		}
		if ($GLOBALS['association_metas']['pc_cotisations']) {
			$t = _T('asso:adherent_titre_historique_cotisations');
			echo debut_cadre_relief('', TRUE, '', $t);
			echo voir_adherent_cotisations($id_auteur, $full);
			echo fin_cadre_relief(TRUE);
		}
		if ($GLOBALS['association_metas']['activites']) {
			$t = _T('asso:adherent_titre_historique_activites');
			echo debut_cadre_relief('', TRUE, '', $t);
			echo voir_adherent_activites($id_auteur);
			echo fin_cadre_relief(TRUE);
		}
		if ($GLOBALS['association_metas']['ventes']) {
			$t = _T('asso:adherent_titre_historique_ventes');
			echo debut_cadre_relief('', TRUE, '', $t);
			echo voir_adherent_ventes($id_auteur);
			echo fin_cadre_relief(TRUE);
		} if ($GLOBALS['association_metas']['dons']) {
			$t = _T('asso:adherent_titre_historique_dons');
			echo debut_cadre_relief('', TRUE, '', $t);
			echo voir_adherent_dons($id_auteur, $full);
			echo fin_cadre_relief(TRUE);
		}
		if ($GLOBALS['association_metas']['prets'])  {
			$t = _T('asso:adherent_titre_historique_prets');
			echo debut_cadre_relief('', TRUE, '', $t);
			echo voir_adherent_prets($id_auteur);
			echo fin_cadre_relief(TRUE);
		}
		fin_page_association();
	}
}

?>