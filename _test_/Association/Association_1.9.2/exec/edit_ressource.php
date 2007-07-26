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
	
	function exec_edit_ressource(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('asso:ressources_titre_edition_ressources'), "", "");
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo '<p></p>';
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","cree.gif");	
		fin_raccourcis();
		
		if ($connect_statut == '0minirezo') {
		include_spip ('inc/navigation');
		}
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_edition_ressources'));
		
		$action=$_REQUEST['action'];
		$id=$_REQUEST['id'];
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$query = spip_query( "SELECT * FROM spip_asso_ressources WHERE id_ressource='$id' ");
		while($data = spip_fetch_array($query)) {
			$id_ressource=$data['id_ressource'];
			$code=$data['code'];
			$intitule=$data['intitule'];
			$date_acquisition=$data['date_acquisition'];
			$id_achat=$data['id_achat'];
			$pu=$data['pu'];
			$statut=$data['statut'];
			$commentaire=$data["commentaire"];
		}		
		echo '<form action="'.$url_action_ressources.'&action='.$action.'" method="post">';	
		echo '<input name="id" type="hidden" value="'.$id_ressource.'" />';
		echo '<label for="code"><strong>'._T('asso:ressources_libelle_code').' :</strong></label>';
		echo '<input name="code" type="text" value="'.$code.'" id="code" class="formo" />';
		echo '<label for="intitule"><strong>'._T('asso:ressources_libelle_intitule').' :</strong></label>';
		echo '<input name="intitule" type="text" value="'.$intitule.'" id="intitule" class="formo" />';
		echo '<label for="date_acquisition"><strong>'._T('asso:ressources_libelle_date_acquisition').' :</strong></label>';
		echo '<input name="date_acquisition" type="text" value="'.$date_acquisition.'" id="date_acquisition" class="formo" />';
		//echo '<label for="id_achat"><strong>Achat n&deg; :</strong></label>';
		//echo '<input name="id_achat" type="text" value="'.$cotisation.'" id="montant" class="formo" />';
		echo '<label for="pu"><strong>'._T('asso:ressources_libelle_prix_location').' :</strong></label>';
		echo '<input name="pu" type="text" value="'.$pu.'" id="pu" class="formo" />';	
		echo '<label for="statut"><strong>'._T('asso:ressources_libelle_statut').' :</strong></label><br />';
		foreach ( array(ok,reserve,suspendu,sorti) as $var) {
			echo '<input name="statut" type="radio" name="statut" value="'.$var.'"';
			if ($statut==$var) {echo ' checked="checked" ';}
			echo ' id="statut"> '._T('asso:ressources_libelle_statut_'.$var);
		}
		echo '<br /><label for="commentaire"><strong>'._T('asso:ressources_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" />
		<input name="url_retour" type="hidden" value="'.$url_retour.'">
		<input name="action" type="hidden" value="'.$action.'"></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
