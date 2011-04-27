<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip ('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip ('inc/mail');
//include_spip ('inc/charsets');

function exec_action_relances(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_retour=$_POST['url_retour'];
		
		//On récupère les données globales

		$sujet=$_POST['sujet'];
		$message=$_POST['message'] ;
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
		$count=count ($id_tab);

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

		/* on fait passer en hidden un tableau id_auteur => statut_interne contenant uniquement les auteurs selectionnes */
		foreach ($id_tab as $id_auteur) {
			$res .= '<input name="statut['.$id_auteur.']" type="hidden" value="'.$statut_tab[$id_auteur].'" />';
		}

		$res .= '<input name="sujet" type="hidden" value="'.$sujet.'" />';
		$res .= '<input name="message" type="hidden" value="'.htmlentities($message, ENT_QUOTES, 'UTF-8').'" />';
		$res .= '<div style="float:right;"><input type="submit" value="'._T('asso:bouton_envoyer').'" class="fondo" /></div>';

		echo redirige_action_post('modifier_relances', $count, 'adherents', '', "\n<div>$res</div>\n");

		fin_cadre_relief();  
		echo fin_page_association(); 
	}
} 
?>
