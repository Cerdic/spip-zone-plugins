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
	elseif ( (is_array($_POST["desactive"]) AND $_POST["desactive"]) OR (is_array($_POST["delete"]) AND $_POST["delete"]) ) {
		exec_action_adherents_args();
	}
}

function exec_action_adherents_args()
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
	if ($_POST["desactive"]) {
		if($_POST['statut_courant']==='sorti'){
			echo debut_cadre_relief("", true, "", propre(_T('asso:activation_des_adherents')));
			echo '<p>'. _T('asso:adherent_message_detail_activation').'</p>';
			echo '<p>'. _T('asso:adherent_message_confirmer_activation').' : </p>';
		}
		else {
			echo debut_cadre_relief("", true, "", propre(_T('asso:desactivation_des_adherents')));
			echo '<p>'. _T('asso:adherent_message_detail_desactivation').'</p>';
			echo '<p>'. _T('asso:adherent_message_confirmer_desactivation').' : </p>';
		}
		echo modifier_adherents($_POST["desactive"],'desactiver', $_POST['statut_courant']);
		echo fin_cadre_relief(true);
	}
	if ($_POST["delete"]) {
		echo debut_cadre_relief("", true, "", propre(_T('asso:suppression_des_adherents')));
		echo '<p>'. _T('asso:adherent_message_detail_suppression').'</p>';
		echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : </p>';
		echo modifier_adherents($_POST["delete"],'supprimer', $_POST['statut_courant']);
		echo fin_cadre_relief(true);
	}	
	echo fin_page_association(); 
}

function modifier_adherents($tab, $mod, $statut)
{
	$res ='<table>';
	foreach ($tab as $id) {
		$id = intval($id);
		$query = sql_select("sexe, id_auteur, prenom, nom_famille",'spip_asso_membres', "id_auteur=$id");
		while($data = sql_fetch($query)) {
			$res .="\n<tr><td>" . $data['id_auteur'] . " <strong>".association_calculer_nom_membre($data['sexe'], $data['prenom'], $data['nom_famille']).'</strong></td><td><input type="checkbox" name='.(($mod==="desactiver")? "drop_des[]":"drop_sup[]").' value="'.$id.'" checked="checked" /></td></tr>';
		}
	}
	$res .='<tr>';
	$res .='<td colspan="2">';
	$res .='<input type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo" /></td></tr>';
	$res .='<input type="hidden" name="statut_courant" value="'.$statut.'" />';
	$res .='</table>';

	// count est juste du bruit de fond pour la secu
	return redirige_action_post($mod.'_adherents', count($tab), 'adherents', "", $res);
}
?>
