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

function exec_action_adherents()
{
	$action_adherents = _request('action_adherents');
	// pour agir sur les adherents il faut avoir le droit d'edition sur les adherents ainsi que le droit de gestion des groupes si c'est ca qu'on modifie.
	if (!autoriser('editer_membres', 'association') ||
		(($action_adherents=='grouper' || $action_adherents=='degrouper' ) && !autoriser('editer_groupes', 'association', 100)) ) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id_auteurs = _request('id_auteurs');
		if ($action_adherents && $id_auteurs && is_array($id_auteurs)) {
			onglets_association('titre_onglet_membres');
			// info
			echo association_totauxinfos_intro(_T('asso:confirmation'));
			// datation et raccourcis
			raccourcis_association('adherents');
			if ($action_adherents=='desactive') {
				$statut_courant = _request('statut_courant');
				if($statut_courant==='sorti'){
					debut_cadre_association('annonce.gif', 'activation_des_adherents');
					echo '<p>'. _T('asso:adherent_message_detail_activation').'</p>';
					echo '<p>'. _T('asso:adherent_message_confirmer_activation').' : </p>';
				} else {
					debut_cadre_association('annonce.gif', 'desactivation_des_adherents');
					echo '<p>'. _T('asso:adherent_message_detail_desactivation').'</p>';
					echo '<p>'. _T('asso:adherent_message_confirmer_desactivation').' : </p>';
				}
				echo modifier_adherents($id_auteurs,'desactiver', $statut_courant);
			}
			if ($action_adherents=='delete') {
				debut_cadre_association('annonce.gif', 'suppression_des_adherents');
				echo '<p>'. _T('asso:adherent_message_detail_suppression').'</p>';
				echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : </p>';
				echo modifier_adherents($id_auteurs,'supprimer');
			}
			if ($action_adherents=='grouper') {
				debut_cadre_association('annonce.gif', 'rejoindre_un_groupe');
				echo _T('asso:adherent_message_grouper');
				echo modifier_adherents($id_auteurs,'grouper');
			}
			if ($action_adherents=='degrouper') {
				debut_cadre_association('annonce.gif', 'quitter_un_groupe');
				echo _T('asso:adherent_message_degrouper');
				echo modifier_adherents($id_auteurs,'degrouper');
			}
			fin_page_association();
		} else {
			include_spip('inc/headers');
			redirige_par_entete(generer_url_ecrire('adherents'));
		}
	}
}

function modifier_adherents($tab, $action, $statut='')
{
	$res ='';
	/* on ajoute une table des groupes si il y en a */
	if ($action=='grouper' || $action=='degrouper') {
		$res .='<p class="titrem">'._T('asso:groupes_membre').'</p>';
		$query = sql_select('id_groupe, nom','spip_asso_groupes', 'id_groupe>=100', '', 'nom'); /* on ne considere que les groupes d'id >=100, les autres c'est pour la gestion des autorisations */
		if (sql_count($query)) {
			$res .='<table>';
			while($data = sql_fetch($query)) {
				$res .= '<tr><td>'.$data['nom'].'</td><td><input type="checkbox" name="id_groupes[]" value="'.$data['id_groupe'].'" /></td></tr>';
			}
			$res .='</table>';
			$res .='<p class="titrem">'._T('asso:adherents_dp').'</p>';
		}
		if ($action=='grouper') {
			$action_file = 'ajouter_membres_groupe';
		} else {
			$action_file = 'exclure_du_groupe';
		}
	} else {
		$action_file = $action.'_adherents';
	}
	$res .='<table>';
	$in = sql_in('id_auteur', $tab);
	$query = sql_select('sexe, id_auteur, prenom, nom_famille','spip_asso_membres', $in, '', 'nom_famille');
	while($data = sql_fetch($query)) {
		$res .= '<tr><td>' . $data['id_auteur'] . ' <strong>'.association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']).'</strong></td><td><input type="checkbox" name="id_auteurs[]" value="'.$data['id_auteur'].'" checked="checked" /></td></tr>';
	}
	$res .='<tr>';
	$res .='<td colspan="2" class="boutons">';
	$res .='<input type="submit" value="'._T('asso:bouton_confirmer').'" /></td></tr>';
	if ($statut) {
		$res .='<input type="hidden" name="statut_courant" value="'.$statut.'" />';
	}
	$res .='</table>';
	// count est juste du bruit de fond pour la secu
	return redirige_action_post($action_file, count($tab), 'adherents', '', $res);
}

?>
