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

	function exec_edit_compte() {
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_comptes =generer_url_ecrire('action_comptes');
		
		$action=$_REQUEST['agir'];
		$id_compte=$_REQUEST['id'];
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$query = spip_query ("SELECT * FROM spip_asso_comptes  WHERE id_compte=$id_compte") ;
		while ($data = spip_fetch_array($query)) {
			$imputation=$data['imputation'];
			$date=$data['date'];
			$recette=$data['recette'];
			$depense=$data['depense'];
			$journal=$data['journal'];
			$justification=$data['justification'];
		}
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		
		association_onglets();
		
		debut_gauche();
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Modification des comptes'));
		
		echo '<form action="'.$url_action_comptes.'" method="POST">';
		
		echo '<label for="imputation"><strong>Imputation :</strong></label>';
		echo '<select name="imputation" type="text" id="date" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe<>".lire_config('association/classe_banques')." ORDER BY code") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($imputation==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="date"><strong>Date (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date" value="'.$date.'" type="text" id="date" class="formo" />';
		echo '<label for="recette"><strong>Recette :</strong></label>';
		echo '<input name="recette" value="'.$recette.'" type="text" id="recette" class="formo" />';
		echo '<label for="depense"><strong>D&eacute;pense :</strong></label>';
		echo '<input name="depense" value="'.$depense.'"  type="text" id="depense" class="formo" />';
		echo '<label for="journal"><strong>Mode de paiement :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_plan WHERE classe=".lire_config('association/classe_banques')." ORDER BY code") ;
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="justification"><strong>Justification :</strong></label>';
		echo '<input name="justification" value="'.$justification.'" type="text" id="justification" class="formo" />';
		
		echo '<input name="id" type="hidden" value="'.$id_compte.'" >';		
		echo '<input name="agir" type="hidden" value="'.$action.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		  echo fin_gauche(),fin_page();
	}  
?>
