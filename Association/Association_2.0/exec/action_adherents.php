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
	
	function exec_action_adherents() {
		
		include_spip('inc/autoriser');
		if (autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$id_auteur=intval($_POST['id']);
		if (lire_config('association/indexation')=="id_asso"){ $id_asso=intval($_POST['id_asso']);}
		$categorie=$_POST['categorie'];
		$validite=$_POST['validite'];
		$commentaire=$_POST['commentaire'];
		$statut_interne=$_POST['statut_interne'];
		$action=$_POST['agir'];
		$url_retour=$_POST['url_retour'];
	
		//MODIFICATION ADHERENT
		
		if ($action=="modifie") {
		  association_auteurs_elargis_updateq(
				   array("id_asso"=> $id_asso,
					 "commentaire"=> $commentaire,
					 "validite"=> $validite,
					 "categorie"=> $categorie,
					 "statut_interne"=> $statut_interne),
				   "id_auteur=$id_auteur");
		  header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE ADHERENT
		  $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
		association_onglets();
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo $action;
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = propre(_T('asso:adherent_libelle_suppression')));
			
		
		if (isset($_POST['delete'])) {
			$url_retour = $_SERVER['HTTP_REFERER'];
			
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			
			echo '<p>'. _T('asso:adherent_message_confirmer_suppression').' : <br>';
			echo '<table>';
			echo '<form action="#"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = $delete_tab[$i];
				$query = association_auteurs_elargis_select("*",'', "id_auteur=$id");
				while($data = spip_fetch_array($query)) {
					echo '<tr>';
					echo '<td><strong>'.$data['nom_famille'].' '.$data['prenom'].'</strong>';
					echo '<td>';
					echo '<input type=checkbox name="drop[]" value="'.$id.'" checked>';
				}
			}
			echo '<tr>';
			echo '<td colspan="2"><input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<input name="submit" type="submit" value="'._T('asso:adherent_bouton_confirmer').'" class="fondo"></td></tr>';
			echo '<table>';
			echo '</p>';
			fin_cadre_relief();
			  echo fin_gauche(),fin_page(); 
			exit;
		}
		
		//  SUPPRESSION DEFINITIVE ADHERENTS
		//---------------------------- 
		if (isset($_POST['drop'])) {
			
			$url_retour=$_POST['url_retour'];
			
			$drop_tab=(isset($_POST["drop"])) ? $_POST["drop"]:array();
			$count=count ($drop_tab);
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = intval($drop_tab[$i]);
				association_auteurs_elargis_delete("id_auteur=$id");
				spip_query("DELETE FROM spip_auteurs WHERE id_auteur=$id");
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>
