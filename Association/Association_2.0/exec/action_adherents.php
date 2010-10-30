<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_action_adherents() {
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
	} elseif (is_array($_POST["delete"]) AND $_POST["delete"]) 
		exec_action_adherents_args($_POST['delete']);
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
	echo debut_cadre_relief("", true, "", propre(_T('asso:supprresion_des_adherents')));
	echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : </p>';
	echo supprimer_adherents($_POST["delete"]);
	echo fin_cadre_relief(true);
	echo fin_page_association(); 
}

function supprimer_adherents($delete_tab)
{
	$res ='<table>';
	foreach ($delete_tab as $id) {
		$id = intval($id);
		$query = sql_select("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id");
		while($data = sql_fetch($query)) {
			$res .="\n<tr><td><strong>".$data['nom_famille'].' '.$data['prenom'].'</strong></td><td><input type="checkbox" name="drop[]" value="'.$id.'" checked="checked" /></td></tr>';
		}
	}
	$res .='<tr>';
	$res .='<td colspan="2">';
	$res .='<input type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo" /></td></tr>';
	$res .='</table>';

	// count est juste du bruit de fond pour la secu
	return redirige_action_post('supprimer_adherents', count($delete_tab), 'adherents', "", $res);
		
}
?>
