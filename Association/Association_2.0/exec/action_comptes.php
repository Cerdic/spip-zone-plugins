<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

	function exec_action_comptes(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_comptes=generer_url_ecrire('action_comptes');
		
		$id_compte= intval($_REQUEST['id']);
		$date=$_POST['date'];
		$imputation=$_POST['imputation'];
		$recette=$_POST['recette'];
		$depense=$_POST['depense'];
		$justification=$_POST['justification'];
		$journal=$_POST['journal'];
		
		$action = $_REQUEST['agir'];
		$url_retour=$_POST['url_retour'];
		
		//AJOUT OPERATION
		if ($action=="ajoute") {
			spip_query( "INSERT INTO spip_asso_comptes (date, imputation, recette, depense, journal, justification) VALUES ('$date', '$imputation' ,'$recette', '$depense', '$journal', '$justification')");
			header ('location:'.$url_retour);
			exit;
		}
		
		//MODIFICATION OPERATION
		if ($action =="modifie") { 
			spip_query( " UPDATE spip_asso_comptes SET date='$date', recette='$recette', depense='$depense', justification='$justification', journal='$journal' WHERE id_compte=$id_compte" );
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE OPERATION
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('Gestion pour Association')) ;

			association_onglets();
			echo debut_gauche('', true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo bloc_des_raccourcis(icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif", false));
			echo debut_droite('', true);
			
			debut_cadre_relief(  "", false, "", $titre = _T('Op&eacute;rations comptables'));
			echo '<p><strong>' . _L('Vous vous appr&ecirc;tez &agrave; effacer la ligne de compte n&deg; '. $id_compte . ' !') . '</strong></p>';

			$res = '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';

			echo redirige_action_post('supprimer_comptes', $id_compte, 'comptes', '', $res);
			fin_cadre_relief();  
			
			fin_page();
			exit;
		}
		
		//---------------------------- 
		//VALIDATION PROVISOIRE COMPTE		
		if (isset($_POST['valide'])) {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('Gestion pour Association')) ;
			
			association_onglets();
			
			echo debut_gauche('', true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo bloc_des_raccourcis(icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif", false));	
			
			echo debut_droite('', true);
			
			debut_cadre_relief("", false, "", $titre = _L('Op&eacute;rations comptables'));
			echo '<p>' . _L('Vous vous appr&ecirc;tez &agrave; valider les op&eacute;rations&nbsp;:') .  '</p>';

			$res = '<table>';
			$query = sql_select('*', 'spip_asso_comptes', sql_in("id_compte", $_POST['valide']));
			while($data = sql_fetch($query)) {
					$res .= '<tr>';
					$res .= '<td><strong>'.association_datefr($data['date']).'</strong>';
					$res .= '<td><strong>'.$data['justification'].'</strong>';
					$res .= '<td>';
					$res .= '<input type=checkbox name="definitif[]" value="'.$data['id_compte'].'" checked="checked" />';
			}
			$res .= '</table>';
			$res .= '<p>' . _L('Apr&egrave;s confirmation vous ne pourrez plus modifier ces op&eacute;rations !') . '</p>';
			
			$res .= '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';

			// count est du bruit de fond de secu
			echo redirige_action_post('valider_comptes', count($_POST['valide']), 'comptes', "", $res);

			fin_cadre_relief();  
			
			fin_page();
		}
	} 
?>
