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
	
	function exec_edit_pret(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('Edition d\'un pr&ecirc;t'), "", "");
		$url_action_prets=generer_url_ecrire('action_prets');
		$url_retour = $_SERVER['HTTP_REFERER'];
			
		debut_gauche();
		
		debut_boite_info();
		echo '<p></p>';
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale("Retour", $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","cree.gif");	
		fin_raccourcis();
		
		if ($connect_statut == '0minirezo') {
		include_spip ('inc/navigation');
		}
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('Pr&ecirc;t'));
		
		$action=$_REQUEST['action'];
		if ($action=="ajoute"){$id_ressource=$_REQUEST['id'];}
		else {$id_pret=$_REQUEST['id'];}
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$query = spip_query( "SELECT * FROM spip_asso_prets WHERE id_pret='$id_pret' ");
		while($data = spip_fetch_array($query)) {
			$id_ressource=$data['id_ressource'];
			$date_sortie=$data['date_sortie'];
			$duree=$data['duree'];
			$date_retour=$data['date_retour'];
			$id_emprunteur=$data['id_emprunteur'];
			$commentaire_sortie=$data['commentaire_sortie'];
			$commentaire_retour=$data['commentaire_retour'];
		}	
		
		$query = spip_query( "SELECT * FROM spip_asso_ressources WHERE id_ressource='$id_ressource' ");
		while($data = spip_fetch_array($query)) {
			$statut=$data['statut']; 
			$pu=$data['pu'];
		}			
		
		$query = spip_query( "SELECT * FROM spip_asso_comptes WHERE id_journal='$id_pret' ");
		while($data = spip_fetch_array($query)) {
			$journal=$data['journal']; 
			$montant=$data['recette'];
		}
		
		if( $action=="ajoute" ){ 
			$montant=$pu; 
			$date_sortie=date('Y-m-d');
		} 
		
		echo '<form action="'.$url_action_prets.'&action='.$action.'" method="post">';			
		echo '<input name="id_pret" type="hidden" value="'.$id_pret.'" />';
		echo '<input name="id_ressource" type="hidden" value="'.$id_ressource.'" />';		
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		echo '<input name="action" type="hidden" value="'.$action.'">';
		
		// Cadre Réservation
		echo '<fieldset>';
		echo '<legend>'._T('R&eacute;servation').'</legend>';
		echo '<label for="date_sortie"><strong>Date de sortie (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date_sortie" type="text" value="'.$date_sortie.'" id="date_sortie" class="formo" />';
		echo '<label for="duree"><strong>Dur&eacute;e (en jours) :</strong></label>';
		echo '<input name="duree" type="text" value="'.$duree.'" id="duree" class="formo" />';
		echo '<label for="id_emprunteur"><strong>N&deg; de l\'emprunteur :</strong></label>';
		echo '<input name="id_emprunteur" type="text" value="'.$id_emprunteur.'" id="id_emprunteur" class="formo" />';
		echo '<label for="commentaire_sortie"><strong>Commentaires :</strong></label>';
		echo '<textarea name="commentaire_sortie" id="commentaire_sortie" class="formo" />'.$commentaire_sortie.'</textarea>';
		echo '</fieldset>';
		
		//Cadre retour
		echo '<fieldset>';
		echo '<legend>'. _T('Retour').'</legend>';
		echo '<label for="date_retour"><strong>Date de retour (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date_retour" type="text" value="';
		if ($date_retour==0) { echo '&nbsp';} else {echo $date_retour;}
		echo '" id="date_retour" class="formo" />';
		echo '<label for="montant"><strong>Montant (en euros) :</strong></label>';
		echo '<input name="montant" type="text" value="'.$montant.'" id="montant" class="formo" />';
		echo '<label for="journal"><strong>Mode de paiement :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_banques ORDER BY id_banque");
		while ($banque = spip_fetch_array($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="commentaire_retour"><strong>Commentaires :</strong></label>';
		echo '<textarea name="commentaire_retour" id="commentaire_retour" class="formo" />'.$commentaire_retour.'</textarea>';
		echo '</fieldset>';
		
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';	

		
		fin_cadre_relief();  
		fin_page();
	}
?>
