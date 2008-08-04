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
	include_spip('inc/defs_supprimees');
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

	function exec_edit_compte() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$url_faire_comptes =generer_url_ecrire('faire_comptes');
		
		$faire=$_REQUEST['faire'];
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
		echo $commencer_page);
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Modification des comptes'));
		
		echo '<form action="'.$url_faire_comptes.'" method="POST">';
		
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
		echo '<input name="faire" type="hidden" value="'.$faire.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($faire)) {echo _T('asso:bouton_'.$faire);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}  
?>
