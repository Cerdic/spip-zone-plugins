<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_edit_adherent()
{
	if (!autoriser('editer_membres', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('inc/navigation_modules');
		list($id_auteur, $data) = association_passeparam_id('auteur', 'asso_membres');
		onglets_association('titre_onglet_membres', 'adherents');
		include_spip('inc/association_coordonnees'); // deja inclus normalement...
		$nom_membre = association_formater_nom($data['sexe'], $data['prenom'], $data['nom_famille']);
		$adresses = association_formater_adresses(array($id_auteur));
		$emails = association_formater_emails(array($id_auteur));
		$telephones = association_formater_telephones(array($id_auteur));
		$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
		switch($statut)	{
			case '0minirezo':
				$statut='auteur';
				break;
			case '1comite':
				$statut='auteur';
				break;
			default :
				$statut='visiteur';
				break;
		}
		// INFOs
		if ($adresses[$id_auteur])
			$infos['coordonnees:adresses'] = $adresses[$id_auteur];
		if ($emails[$id_auteur])
			$infos['coordonnees:emails'] = $emails[$id_auteur];
		if ($telephones[$id_auteur])
			$infos['coordonnees:numeros'] =  $telephones[$id_auteur];
		echo '<div class="vcard">'. association_totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos ) .'</div>';
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('annonce.gif', 'adherent_titre_modifier_membre');
		echo recuperer_fond('prive/editer/editer_asso_membres', array (
			'id_auteur' => $id_auteur,
		));
		fin_page_association();
	}
}

?>