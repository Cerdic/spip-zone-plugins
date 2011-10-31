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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_action_adherents() {
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
			include_spip('inc/minipres');
			echo minipres();
	}
	else { 
		$id_auteurs = _request('id_auteurs');
		$action_adherents = _request('action_adherents');
				
		if ($action_adherents && $id_auteurs && is_array($id_auteurs)) {
			exec_action_adherents_args($id_auteurs, $action_adherents);
		} else {
			include_spip('inc/headers');
			redirige_par_entete(generer_url_ecrire('adherents'));
		}
	}
}

function exec_action_adherents_args($id_auteurs, $action_adherents)
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
	association_onglets();
	echo debut_gauche("",true);
	echo debut_boite_info(true);
	echo association_date_du_jour();	
	echo fin_boite_info(true);
	echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  generer_url_ecrire('adherents'), "retour-24.png"));
	echo debut_droite("",true);	
	if ($action_adherents == "desactive") {
		$statut_courant = _request('statut_courant');
		if($statut_courant==='sorti'){
			echo debut_cadre_relief("", true, "", propre(_T('asso:activation_des_adherents')));
			echo '<p>'. _T('asso:adherent_message_detail_activation').'</p>';
			echo '<p>'. _T('asso:adherent_message_confirmer_activation').' : </p>';
		}
		else {
			echo debut_cadre_relief("", true, "", propre(_T('asso:desactivation_des_adherents')));
			echo '<p>'. _T('asso:adherent_message_detail_desactivation').'</p>';
			echo '<p>'. _T('asso:adherent_message_confirmer_desactivation').' : </p>';
		}
		echo modifier_adherents($id_auteurs,'desactiver', $statut_courant);
		echo fin_cadre_relief(true);
	}
	if ($action_adherents== "delete") {
		echo debut_cadre_relief("", true, "", propre(_T('asso:suppression_des_adherents')));
		echo '<p>'. _T('asso:adherent_message_detail_suppression').'</p>';
		echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : </p>';
		echo modifier_adherents($id_auteurs,'supprimer');
		echo fin_cadre_relief(true);
	}

	if ($action_adherents== "grouper") {
		echo debut_cadre_relief("", true, "", propre(_T('asso:rejoindre_un_groupe')));
		echo _T('asso:adherent_message_grouper');
		echo modifier_adherents($id_auteurs,'grouper');
		echo fin_cadre_relief(true);
	}

	if ($action_adherents== "degrouper") {
		echo debut_cadre_relief("", true, "", propre(_T('asso:quitter_un_groupe')));
		echo _T('asso:adherent_message_degrouper');
		echo modifier_adherents($id_auteurs,'degrouper');
		echo fin_cadre_relief(true);
	}

	
	echo fin_page_association(); 
}

function modifier_adherents($tab, $action, $statut='')
{
	$res ='';

	/* on ajoute une table des groupes si il y en a */
	if ($action=="grouper" || $action=="degrouper") {
		$res .='<p class="titrem">'._T('asso:groupes_dp').'</p>';
		$query = sql_select("id_groupe, nom",'spip_asso_groupes', '', '', 'nom');
		if (sql_count($query)) {
			$res .='<table>';
			while($data = sql_fetch($query)) {
				$res .= '<tr><td>'.$data['nom'].'</td><td><input type="checkbox" name="id_groupes[]" value="'.$data['id_groupe'].'" /></td></tr>';
			}
			$res .='</table>';
			$res .='<p class="titrem">'._T('asso:adherents_dp').'</p>';		
		}
		if ($action=="grouper") {
			$action_file = "ajouter_membres_groupe";
		} else  {
			$action_file = "exclure_du_groupe";
		}
	} else {
		$action_file = $action.'_adherents';
	}
	
	$res .='<table>';
	$in = sql_in('id_auteur', $tab);
	$query = sql_select("sexe, id_auteur, prenom, nom_famille",'spip_asso_membres', $in, '', 'nom_famille');

	while($data = sql_fetch($query)) {
		$res .="<tr><td>" . $data['id_auteur'] . " <strong>".association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']).'</strong></td><td><input type="checkbox" name="id_auteurs[]" value="'.$data['id_auteur'].'" checked="checked" /></td></tr>';
	}

	$res .='<tr>';
	$res .='<td colspan="2">';
	$res .='<input type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo" /></td></tr>';
	if ($statut) {$res .='<input type="hidden" name="statut_courant" value="'.$statut.'" />';}
	$res .='</table>';
	


	// count est juste du bruit de fond pour la secu
	return redirige_action_post($action_file, count($tab), 'adherents', "", $res);
}
?>
