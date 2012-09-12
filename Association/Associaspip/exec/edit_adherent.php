<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip ('inc/navigation_modules');

function exec_edit_adherent()
{
	if (!autoriser('editer_membres', 'association')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_auteur = intval(_request('id'));
		$data = sql_fetsel('*','spip_asso_membres', "id_auteur=$id_auteur");
		if (!$data) {
			include_spip('inc/minipres');
			echo minipres(_T('zxml_inconnu_id') . $id_auteur);
		} else {
			onglets_association('titre_onglet_membres', 'adherents');
			include_spip('inc/association_coordonnees');
			$nom_membre = association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']);
			$adresses = association_formater_adresses(array($id_auteur));
			$emails = association_formater_emails(array($id_auteur));
			$telephones = association_formater_telephones(array($id_auteur));
			$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur='.$id_auteur);
			switch($statut)	{
				case '0minirezo':
					$statut='auteur'; break;
				case '1comite':
					$statut='auteur'; break;
				default :
					$statut='visiteur'; break;
			}
			// INFOs
			if ($adresses[$id_auteur])
				$infos['adresses'] = $adresses[$id_auteur];
			if ($emails[$id_auteur])
				$infos['emails'] = $emails[$id_auteur];
			if ($telephones[$id_auteur])
				$infos['numeros'] =  $telephones[$id_auteur];
			echo '<div class="vcard">'. association_totauxinfos_intro('<span class="fn">'.htmlspecialchars($nom_membre).'</span>', $statut, $id_auteur, $infos, 'coordonnees') .'</div>';
			// datation et raccourcis
			raccourcis_association('');
			debut_cadre_association('annonce.gif', 'adherent_titre_modifier_membre');
			echo recuperer_fond('prive/editer/editer_asso_membres', array (
				'id_auteur' => $id_auteur,
			));
			fin_page_association();
		}
	}
}

?>
