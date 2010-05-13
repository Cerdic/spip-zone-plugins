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
	
function exec_edit_pret(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_prets=generer_url_ecrire('action_prets');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$action=$_REQUEST['agir'];
		$id_pret= intval(_request('id_pret'));
		$data = !$id_pret ? '' : sql_fetsel('*', 'spip_asso_prets', "id_pret=$id_pret");
		if ($data) {
			$id_ressource=intval($data['id_ressource']);
			$duree=$data['duree'];
			$id_emprunteur=$data['id_emprunteur'];
			$commentaire_sortie=$data['commentaire_sortie'];
			$commentaire_retour=$data['commentaire_retour'];
			$date_retour=$data['date_retour'];
			$date_sortie=$data['date_sortie'];
		} else {

			$id_ressource= $id_pret;
			$date_retour=$date_sortie=date('Y-m-d');
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:prets_titre_edition_prets')) ;
		
		association_onglets();
		
		echo debut_gauche('',true);
		
		echo debut_boite_info(true);
		$query = sql_select("*", "spip_asso_ressources", "id_ressource=$id_ressource" ) ;
		while ($data = sql_fetch($query)) {
			$statut=$data['statut'];
			echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
			echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
			echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
			echo $data['intitule'];
			echo '</p>';
		}
		echo fin_boite_info(true);
		
		
		echo association_retour();

		
		echo debut_droite('', true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:prets_titre_edition_prets'));
		
		$query = sql_select("*", "spip_asso_ressources", "id_ressource=$id_ressource");
		while($data = sql_fetch($query)) {
			$statut=$data['statut']; 
			$pu=$data['pu'];
		}			
		
		$query = sql_select("*", "spip_asso_comptes", "id_journal=$id_pret ");
		while($data = sql_fetch($query)) {
			$journal=$data['journal']; 
			$montant=$data['recette'];
		}
		
		if( $action=="ajoute" ){ 
			$montant=$pu; 
			$date_sortie=date('Y-m-d');
		} 
		
		echo '<form action="'.$url_action_prets.'" method="post">';			
		
		// Cadre Réservation
		echo '<fieldset>';
		echo '<legend>'._T('asso:prets_entete_reservation').'</legend>';
		echo '<label for="date_sortie"><strong>'._T('asso:prets_libelle_date_sortie').' :</strong></label>';
		echo '<input name="date_sortie" type="text" value="'.$date_sortie.'" id="date_sortie" class="formo" />';
		echo '<label for="duree"><strong>'._T('asso:prets_libelle_duree').' :</strong></label>';
		echo '<input name="duree" type="text" value="'.$duree.'" id="duree" class="formo" />';
		echo '<label for="id_emprunteur"><strong>'._T('asso:prets_libelle_num_emprunteur').' :</strong></label>';
		echo '<input name="id_emprunteur" type="text" value="'.$id_emprunteur.'" id="id_emprunteur" class="formo" />';
		echo '<label for="commentaire_sortie"><strong>'._T('asso:prets_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire_sortie" id="commentaire_sortie" class="formo" />'.$commentaire_sortie.'</textarea>';
		echo '</fieldset>';
		
		//Cadre retour
		echo '<fieldset>';
		echo '<legend>'. _T('asso:prets_entete_retour').'</legend>';
		echo '<label for="date_retour"><strong>'._T('asso:prets_libelle_date_retour').' :</strong></label>';
		echo '<input name="date_retour" type="text" value="';
		if ($date_retour==0) { echo '&nbsp';} else {echo $date_retour;}
		echo '" id="date_retour" class="formo" />';
		echo '<label for="montant"><strong>'._T('asso:prets_libelle_montant').' :</strong></label>';
		echo '<input name="montant" type="text" value="'.$montant.'" id="montant" class="formo" />';
		echo '<label for="journal"><strong>'._T('asso:prets_libelle_mode_paiement').' :</strong></label>';
		echo '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = sql_select("*", "spip_asso_plan", "classe=".sql_quote(lire_config('association/classe_banques')), '', "code") ;
		while ($banque = sql_fetch($sql)) {
			echo '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { echo ' selected="selected"'; }
			echo '>'.$banque['intitule'].'</option>';
		}
		echo '</select>';
		echo '<label for="commentaire_retour"><strong>'._T('asso:prets_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire_retour" id="commentaire_retour" class="formo" />'.$commentaire_retour.'</textarea>';
		echo '</fieldset>';
		
		echo '<input name="id_pret" type="text" value="'.$id_pret.'" />';
		echo '<input name="id_ressource" type="text" value="'.$id_ressource.'" />';		
		echo '<input name="url_retour" type="text" value="'.$url_retour.'">';
		echo '<input name="agir" type="text" value="'.$action.'">';
		
		echo '<div style="float:right;"><input type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';	
		
		fin_cadre_relief();  
		fin_page();
	}
?>
