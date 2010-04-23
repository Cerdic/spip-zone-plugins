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

	function exec_action_dons() {
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_asso = generer_url_ecrire('association');
		$url_action_dons = generer_url_ecrire('action_dons');
		$url_retour=$_POST['url_retour'];
		
		$id_don = intval($_REQUEST['id']);
		$id_adherent=intval($_POST['id_adherent']);
		$action=$_REQUEST['agir'];
		$date_don = $_POST['date_don'];
		$bienfaiteur= $_POST['bienfaiteur'];
		$argent= $_POST['argent'];
		$journal=$_POST['journal'];
		$colis= $_POST['colis'];
		$valeur= $_POST['valeur'];
		$contrepartie= $_POST['contrepartie'];
		$justification='don n&deg; '.$id_don.' - '.$bienfaiteur;
		$commentaire=$_POST['commentaire'];
		
		//AJOUT DON
		if ($action=="ajoute"){
			spip_query( "INSERT INTO spip_asso_dons (date_don, bienfaiteur, id_adherent, argent, colis, valeur, contrepartie, commentaire ) VALUES ( "._q($date_don).", "._q($bienfaiteur).", "._q($id_adherent).", "._q($argent).", "._q($colis).", "._q($valeur).", "._q($contrepartie).", "._q($commentaire)." )");
			$query=spip_query( "SELECT MAX(id_don) AS id_don FROM spip_asso_dons");
			while ($data = spip_fetch_array($query)) {
				$id_don=$data['id_don'];
				$justification='don n&deg; '.$id_don.' - '.$bienfaiteur;
			}
			spip_query( "INSERT INTO spip_asso_comptes (date, journal,recette,justification,imputation,id_journal) VALUES ("._q($date_don).", "._q($journal).", "._q($argent).", "._q($justification).", ".lire_config('association/pc_dons').", "._q($id_don).")" );
			header ('location:'.$url_retour);
			exit;
		}
		
		//MODIFICATION DON
		if ($action=="modifie"){
			spip_query( " UPDATE spip_asso_dons SET date_don="._q($date_don).", bienfaiteur="._q($bienfaiteur).", id_adherent="._q($id_adherent).", argent="._q($argent).", colis="._q($colis).", valeur="._q($valeur).", contrepartie="._q($contrepartie).", commentaire="._q($commentaire)." WHERE id_don=$id_don " );
			spip_query( "UPDATE spip_asso_comptes SET date="._q($date_don).", journal="._q($journal).",recette="._q($argent).", justification="._q($justification)."  WHERE id_journal=$id_don AND imputation=".lire_config('association/pc_dons'));
			header ('location:'.$url_retour);
			exit;
		}
		
		//SUPPRESSION PROVISOIRE DONS
		if ($action=="supprime"){
			$url_retour = $_SERVER['HTTP_REFERER'];
				
			 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Gestion pour Association')) ;
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			
			$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("", true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('Action sur les dons'));
			echo '<div align="center">';
			echo '<p><strong>Vous vous appr&ecirc;tez &agrave; effacer un don !</strong></p>';
			echo '<form action="'.$url_action_dons.'" method="post">';
			echo '<input name="agir" type="hidden" value="drop">';
			echo '<input name="id" type="hidden" value="'.$id_don.'">';
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<div style="text-align:right;"><input name="submit" type="submit" value="Confirmer" class="fondo">';
			echo '</div>';		
			fin_cadre_relief();  
			  echo fin_gauche(),fin_page(); 
		}
		
		//  SUPPRESSION DEFINITIVE DONS
		if ($action=="drop") {
			spip_query( "DELETE FROM spip_asso_dons WHERE id_don='$id_don' ");
			spip_query( "DELETE FROM spip_asso_comptes WHERE id_journal=$id_don AND imputation=".lire_config('association/pc_dons'));  
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>
