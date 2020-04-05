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

function exec_edit_adherent() {
	$r = association_controle_id('auteur', 'asso_membres', 'editer_membres');
	if ($r)  {
		list($id_auteur, $data) = $r;
		include_spip ('association_modules');
/// AFFICHAGES_LATERAUX (connexes)
		echo association_navigation_onglets('titre_onglet_membres', 'adherents');
/// AFFICHAGES_LATERAUX : INTRO : info auteur
		$nom_membre = association_formater_nom($data['sexe'], $data['prenom'], $data['nom_famille']);
		$adresses = association_formater_adresses(array($id_auteur));
		$emails = association_formater_emails(array($id_auteur));
		$telephones = association_formater_telephones(array($id_auteur));
		$sites = association_formater_urls(array($id_auteur));
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case '0minirezo':
			case '1comite':
				$statut='auteur';
				break;
			default :
				$statut='visiteur';
				break;
		}
		if ($adresses[$id_auteur])
			$infos['coordonnees:adresses'] = $adresses[$id_auteur];
		if ($emails[$id_auteur])
			$infos['coordonnees:emails'] = $emails[$id_auteur];
		if ($telephones[$id_auteur])
			$infos['coordonnees:numeros'] =  $telephones[$id_auteur];
		if ($sites[$id_auteur])
			$infos['coordonnees:pages'] =  $sites[$id_auteur];
		echo '<div class="vcard">'. association_tablinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos ) ."</div>\n";
/// AFFICHAGES_LATERAUX : RACCOURCIS
		echo association_navigation_raccourcis(array(
			array('adherent_titre_liste_actifs', 'grille-24.png', array('adherents', "id=$id_auteur"), array('voir_membres', 'association', 0) ),
			array('voir_adherent', 'annonce.gif', array('adherent', "id_auteur=$id_auteur"), array('voir_membres', 'association', $id_auteur) ),
#			array("adherent_label_modifier_$statut", 'membre_infos.png', array('auteur_infos', "id_auteur=$id_auteur"), autoriser('voir', 'auteur', $id_auteur) ),
		) );
/// AFFICHAGES_CENTRAUX (corps)
		debut_cadre_association('annonce.gif', 'adherent_titre_modifier_membre');
/// AFFICHAGES_CENTRAUX : FORMULAIRE
		echo recuperer_fond('prive/editer/editer_asso_membre', array (
			'id_auteur' => $id_auteur,
		));
/// AFFICHAGES_CENTRAUX : FIN
		fin_page_association();
	}
}

?>