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
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_edit_plan(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('Edition plan comptable'), "", "");
		$url_asso = generer_url_ecrire('association');
		$url_plan = generer_url_ecrire('plan');
		$url_action_plan=generer_url_ecrire('action_plan');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$action=$_GET['action'];
		$id_plan=$_GET['id'];
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","cree.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Edition plan comptable'));
		
		$query = spip_query( "SELECT * FROM spip_asso_plan WHERE id_plan='$id_plan' ");
		while($data = spip_fetch_array($query)) {
			$code=$data['code'];
			$classe=$data['classe'];
			$intitule=$data['intitule'];
			$reference=$data['reference'];
			$solde_anterieur=$data['solde_anterieur'];
			$date_anterieure=$data['date_anterieure'];
			$actif=$data['actif'];
			$commentaire=$data["commentaire"];
		}
		echo '<form action="'.$url_action_plan.'" method="post">';	
		
		echo '<label for="code"><strong>Code :</strong></label>';
		echo '<input name="code" type="text" value="'.$code.'" id="code" class="formo" />';
		echo '<label for="classe"><strong>Classe :</strong></label>';
		echo '<input name="classe" type="text" value="'.$classe.'" id="classe" class="formo" />';
		echo '<label for="intitule"><strong>Intitul&eacute; :</strong></label>';
		echo '<input name="intitule" type="text" size="50" value="'.$intitule.'" id="intitule" class="formo" />';
		echo '<label for="reference"><strong>R&eacute;f&eacute;rence :</strong></label>';
		echo '<input name="reference" type="text" size="50" value="'.$reference.'" id="reference" class="formo" />';
		echo '<label for="solde_anterieur"><strong>Solde report&eacute; (en euros) :</strong></label>';
		echo '<input name="solde_anterieur" type="text" value="'.$solde_anterieur.'" id="solde_anterieur" class="formo" />';
		echo '<label for="date_anterieure"><strong>Date report (AAA-MM-JJ) :</strong></label>';
		echo '<input name="date_anterieure" type="text" value="'.$date_anterieure.'" id="date_anterieure" class="formo" />';
		echo '<label for="actif"><strong>Compte activ&eacute; :</strong></label>';
		echo '<input name="actif" type="radio" value="oui" id="actif" ';
		if ($actif=="oui" || $action=="ajoute") {echo ' checked="checked"';}
		echo '>'._T('asso:plan_libelle_oui');
		echo '<input name="actif" type="radio" value="non" id="actif" ';
		if ($actif=="non") {echo ' checked="checked"';}
		echo '>'._T('asso:plan_libelle_non');
		echo '<br /><label for="commentaire"><strong>Commentaires :</strong></label>';
		echo '<textarea name="commentaire" cols="38" rows="3" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		echo '<input type="hidden" name="action" value="'.$action.'">';
		echo '<input type="text" name="id" value="'.$id_plan.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo"></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		
		fin_page();
	}
?>
