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
	sinon_interdire_acces(autoriser('voir_membres', 'association', $id_auteur));
	include_spip('association_modules');
/// INITIALISATIONS
	$id_auteur = association_passeparam_id('auteur');
	$full = autoriser('editer_membres', 'association');
	$data = sql_fetsel('m.sexe, m.nom_famille, m.prenom, m.date_validite, m.id_asso, c.libelle, m.commentaire','spip_asso_membres as m LEFT JOIN spip_asso_categories as c ON m.id_categorie=c.id_categorie', "m.id_auteur=$id_auteur");
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
	include_spip('inc/association_comptabilite');
	$ids = association_passeparam_compta();
/// AFFICHAGES_LATERAUX (connexes)
	echo association_navigation_onglets('titre_onglet_membres', 'adherents');
/// AFFICHAGES_LATERAUX : INFOS
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
	echo '<div class="vcard">'. association_tablinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos, 'asso_membre') .'</div>';
/// AFFICHAGES_LATERAUX : RACCOURCIS
	$raccourcis = array(
		array('adherent_titre_liste_actifs', 'grille-24.png', array('adherents', "id=$id_auteur"), array('voir_membres', 'association', 0) ),
		array('adherent_label_modifier_membre', 'edit-24.gif', array('edit_adherent', "id=$id_auteur"), array('editer_membres', 'association') ),
		array("adherent_label_modifier_$statut", 'membre_infos.png', array('auteur_infos', "id_auteur=$id_auteur"), autoriser('voir', 'auteur', $id_auteur) ),
	);
	if ($GLOBALS['association_metas']['pc_cotisations'])
		$raccourcis[] = array('adherent_label_ajouter_cotisation', 'cotis-12.gif', array('ajout_cotisation', "id_auteur=$id_auteur"), array('ajouter_cotisation', 'association', $id_auteu ) );
	if ($GLOBALS['association_metas']['pc_dons'])
		$raccourcis[] = array('ajouter_un_don', 'ajout-24.png', array('ajout_don', "id_auteur=$id_auteur"), array('editer_dons', 'association', $id_auteur) );
	echo association_navigation_raccourcis( $raccourcis, 12);
/// AFFICHAGES_CENTRAUX (corps)
	debut_cadre_association('annonce.gif', 'membre');
/// AFFICHAGES_CENTRAUX : FILTRES
	echo association_form_filtres(array(
		'periode' => array($ids['id_periode'], 'asso_comptes', 'operation'),
	), "adherent",
		'<td><input type="hidden" name="id" value="'.$id_auteur.'" /> : '. association_formater_date($ids['debut_periode'], 'dtstart') .'&mdash;'. association_formater_date($ids['fin_periode'], 'dtend') .'</td>'
	);
/// AFFICHAGES_CENTRAUX : TABLEAU GROUPES + COMMENTAIRE
	if ($full)
		echo propre($data['commentaire']);
	$query_groupes = sql_select('g.*, fonction', 'spip_asso_groupes g LEFT JOIN spip_asso_fonctions l ON g.id_groupe=l.id_groupe', 'g.id_groupe>=100 AND l.id_auteur='.$id_auteur, '', 'g.nom'); // Liste des groupes (on ignore les groupes d'id <100 qui sont dedies a la gestion des autorisations)
	if ( autoriser('voir_groupes', 'association') AND sql_count($query_groupes) ) {
		echo debut_cadre_relief('', TRUE, '', _T('asso:groupes_membre') );
		echo association_bloc_listehtml2('asso_groupes',
			$query_groupes, // requete
			array(
#				'id_groupe' => array('asso:entete_id', 'entier'),
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
/// AFFICHAGES_CENTRAUX : TABLEAUX
	$logasso = array();
	if ($GLOBALS['association_metas']['pc_cotisations'])
		$logasso[] = 1;
	if ($GLOBALS['association_metas']['dons'])
		$logasso[] = 2;
	if ($GLOBALS['association_metas']['ventes'])
		$logasso[] = 3;
	if ($GLOBALS['association_metas']['activites'])
		$logasso[] = 4;
	if ($GLOBALS['association_metas']['prets'])
		$logasso[] = 5;
	foreach ( pipeline('associaspip', array()) as $plugin=>$boutons ) { // Modules ajoutes par d'autres plugins : 'prefixe_plugin'=> array(array, de, boutons)
		if ( test_plugin_actif($plugin) && find_in_path("logasso_$plugin.html", 'prive') )
			$logasso[] = $plugin;
	}
	echo debut_cadre_relief('', TRUE, '', _T('asso:historiques'));
	foreach ( $logasso as $log ) {
		echo recuperer_fond("prive/logasso_$log", array(
			'id_auteur' => $id_auteur,
			'periode_du' => $ids['debut_periode'],
			'periode_au' => $ids['fin_periode'],
		), array('ajax'=>TRUE) );
	}
/// AFFICHAGES_CENTRAUX : FIN
	fin_page_association();
}

?>