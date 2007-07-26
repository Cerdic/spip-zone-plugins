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

	function exec_action_prets(){
		global $connect_statut, $connect_toutes_rubriques;
		
		$url_action_prets=generer_url_ecrire('action_prets');
		$action=$_REQUEST['action'];
		$id_pret=$_REQUEST['id_pret'];
		$id_ressource=$_REQUEST['id_ressource'];
		$date_sortie=$_POST['date_sortie'];
		$duree=$_POST['duree'];
		$date_retour=$_POST['date_retour'];
		$id_emprunteur=$_POST['id_emprunteur'];
		$commentaire_sortie=$_POST['commentaire_sortie'];
		$commentaire_retour=$_POST['commentaire_retour'];
		$statut=$_POST['statut'];
		$montant=$_POST['montant'];
		$journal=$_POST['journal'];
		$imputation=lire_config('association/pc_prets');
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE PRET
		
		if ($action == "supprime") {
			
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			debut_page(_T('asso:prets_titre_suppression_prets'), "", "");
			
			debut_gauche();
			
			debut_boite_info();
			$query = spip_query ( "SELECT * FROM spip_asso_ressources WHERE id_ressource='$id_ressource'" ) ;
			while ($data = spip_fetch_array($query)) {
				$statut=$data['statut'];
				echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
				echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
				echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
				echo $data['intitule'];
				echo '</p>';
			}
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","cree.gif");	
			fin_raccourcis();
			
			debut_droite();
			
			debut_cadre_relief(  "", false, "", $titre = _T('asso:prets_titre_suppression_prets'));
			echo '<p><strong>'._T('asso:prets_danger_suppression',array('id_pret' => $id_pret)).'</strong></p>';
			echo '<form action="'.$url_action_prets.'&action=drop"  method="post">';
			echo '<input type=hidden name="id_pret" value="'.$id_pret.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			echo '<p style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			fin_cadre_relief();  
			
			fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE PRET
		
		if ($action == "drop") {
			
			spip_query( "DELETE FROM spip_asso_prets WHERE id_pret='$id_pret' " );
			spip_query ("DELETE FROM spip_asso_comptes WHERE id_journal='$id_pret' " );
			spip_query( "UPDATE spip_asso_ressources SET statut='ok' WHERE id_ressource='$id_ressource' " );
			header ('location:'.$url_retour);
		}
		
		//  MODIFICATION PRET
		
		if ($action =="modifie") { 
			spip_query( "UPDATE spip_asso_prets SET date_sortie="._q($date_sortie).", duree="._q($duree).", date_retour="._q($date_retour).", id_emprunteur="._q($id_emprunteur).", commentaire_sortie="._q($commentaire_sortie)." WHERE id_pret='$id_pret' " );
			spip_query( "UPDATE spip_asso_comptes SET date="._q($date_sortie).", journal="._q($journal).",recette="._q($montant).") " );
			header ('location:'.$url_retour);
		}
		
		//  AJOUT PRET
		
		if ($action == "ajoute") {
			$query=spip_query( "INSERT INTO spip_asso_prets (id_ressource, date_sortie, duree, date_retour, id_emprunteur, commentaire_sortie, commentaire_retour) VALUES ("._q($id_ressource).", "._q($date_sortie).", "._q($duree).", "._q($date_retour).", "._q($id_emprunteur).", "._q($commentaire_sortie).", "._q($commentaire_retour)." )" );
			if($query){
				$sql=spip_query( "SELECT MAX(id_pret) AS id_pret FROM spip_asso_prets");
				while ($data = spip_fetch_array($sql)){
					$id_pret=$data['id_pret'];
					$justification='Pr&ecirc;t n&deg;'.$id_ressource.'/'.$id_pret;
				}
				spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ("._q($date_sortie).", "._q($journal).", "._q($montant).", "._q($justification).", "._q($imputation).", "._q($id_pret)." )" );
				spip_query( "UPDATE spip_asso_ressources SET statut='reserve' " );
			}
			header ('location:'.$url_retour);
		}
	}
?>
