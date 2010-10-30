<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip ('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip ('inc/mail');
//include_spip ('inc/charsets');

function exec_action_relances(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_retour=$_POST['url_retour'];
		
		//On récupère les données globales

		$sujet=$_POST['sujet'];
		$message=$_POST['message'] ;
		$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$count=count ($email_tab);

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
			
		debut_cadre_relief(  "", false, "", $titre = _T('asso:relance_de_cotisations'));
		echo '<p><strong>', _T('asso:vous_vous_appretez_a_envoyer') . " $count ";
		if ($count==1)
			  { echo _T('asso:relance');}
		else
			  { echo _T('asso:relances');}
		echo '</strong></p>';
		echo '<p>'.$sujet.'</p>';
		echo '<fieldset>';
		echo nl2br($message);
		echo '</fieldset>';
		
		$res = '';

		for ( $i=0 ; $i < $count ; $i++ ) {
			$res .= '<input name="id[]" type="hidden" value="'.intval($id_tab[$i]).'" />';
			$res .= '<input name="statut[]" type="hidden" value="'.$statut_tab[$i].'" />';
			$res .= '<input name="email[]" type="hidden" value="'.$email_tab[$i].'">';
		}
		$res .= '<input name="sujet" type="hidden" value="'.$sujet.'" />';
		$res .= '<input name="message" type="hidden" value="'.$message.'" />';
		$res .= '<div style="float:right;"><input type="submit" value="'._T('asso:bouton_envoyer').'" class="fondo" /></div>';

		echo redirige_action_post('modifier_relances', $count, 'adherents', '', "\n<div>$res</div>\n");

		fin_cadre_relief();  
		echo fin_page_association(); 
	}
} 
?>
