<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

function exec_agir_ventes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_agir_ventes=generer_url_ecrire('agir_ventes');
		
		$id_vente=intval($_REQUEST['id']);
		$action=$_REQUEST['agir'];
		$url_retour=$_POST['url_retour'];
		
		$date_vente=$_POST['date_vente'];
		$article=$_POST['article'];
		$code=$_POST['code'];
		$acheteur=$_POST['acheteur'];
		$id_acheteur=intval($_POST['id_acheteur']);
		$quantite=$_POST['quantite'];
		$date_envoi=$_POST['date_envoi'];
		$frais_envoi=$_POST['frais_envoi'];
		$don=$_POST['don'];
		$prix_vente=$_POST['prix_vente'];
		$journal=$_POST['journal'];
		$justification='vente n&deg; '.$id_vente.' - '.$article;
		$commentaire=$_POST['commentaire'];
		$recette=$quantite*$prix_vente;

		//AJOUT VENTE
		if ($action=="ajoute"){
			if (!ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette, $depense)) {

				include_spip('inc/minipres');
				echo minipres(_T('avis_operation_impossible'));
			} else 	header ('location:'.$url_retour);
		}
		
		//MODIFICATION VENTE
		else if ($action=="modifie"){
			ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification);
			header ('location:'.$url_retour);
		}
		
		//SUPPRESSION PROVISOIRE VENTES
		elseif (isset($_POST['delete'])) {
		
			$delete_tab=(isset($_POST["delete"])) ? $_POST["delete"]:array();
			$count=count ($delete_tab);
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
			association_onglets();
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png"));
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('Action sur les ventes associatives'));
			
			echo '<p><strong>', _L('Vous vous appr&ecirc;tez &agrave; effacer') . " $count " . (($count==1) ? _L('vente') : _L('ventes'));
			echo '</strong></p>';
			echo '<table>';
			echo '<form action="'.$url_agir_ventes.'"  method="post">';
			for ( $i=0 ; $i < $count ; $i++ ) {	
				$id = $delete_tab[$i];
				echo '<input type="hidden" name="drop[]" value="'.$id.'" checked="checked" />' ; "\n";
			}
			echo '<tr>';
			echo '<td><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></td></tr>';	
			echo '</form>';  
			echo '</table>'; 
			fin_cadre_relief();  
			echo fin_gauche(), fin_page();
		}
		
		//  SUPPRESSION DEFINITIVE VENTES	
		elseif (isset($_POST['drop'])) {
			
			$url_retour = generer_url_ecrire('ventes');
			$drop_tab=(isset($_POST['drop'])) ? $_POST['drop']:array();
			$count=count ($drop_tab);
			
			for ( $i=0 ; $i < $count ; $i++ ) {
				$id = intval($drop_tab[$i]);
				sql_delete('spip_asso_ventes', "id_vente=$id" );
				$imputation=lire_config('association/pc_ventes');
				sql_delete('spip_asso_comptes', "id_journal=$id AND imputation='$imputation'");
			}
			header ('location:'.$url_retour);
		}
	}
}

function ventes_modifier($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $id_vente, $journal, $justification)
{
	sql_updateq('spip_asso_ventes', array(
		"date_vente" => $date_vente,
		"article" => $article,
		"code" => $code,
		"acheteur" => $acheteur,
		"id_acheteur" => $id_acheteur,
		"quantite" => $quantite,
		"date_envoi" => $date_envoi,
		"frais_envoi" => $frais_envoi,
		"don" => $don,
		"prix_vente" => $prix_vente,
		"commentaire" => $commentaire),
		    "id_vente=$id_vente" );

	sql_update('spip_asso_comptes', array(
		"date" => $date_vente,
		"journal" => $journal,
		"recette" => $prix_vente,
		"depense" => $frais_envoi,
		"justification" => $justification),
		   "id_journal=$id_vente AND imputation=".sql_quote(lire_config('association/pc_ventes')));

}

function ventes_insert($date_vente, $article, $code, $acheteur, $id_acheteur, $quantite, $date_envoi, $frais_envoi, $don, $prix_vente, $commentaire, $journal, $recette, $depense)
{
	$id_vente = sql_insertq('spip_asso_ventes', array(
		'date_vente' => $date_vente,
		'article' => $article,
		'code' => $code,
		'acheteur' => $acheteur,
		'id_acheteur' => $id_acheteur,
		'quantite' => $quantite,
		'date_envoi' => $date_envoi,
		'frais_envoi' => $frais_envoi,
		'don' => $don,
		'prix_vente' => $prix_vente,
		'commentaire' => $commentaire));

	if ($id_vente) {
		$justification='vente n&deg; '.$id_vente.' - '.$article;

		if (sql_insertq('spip_asso_comptes', array(
			'date' => $date_vente,
			'journal' => $journal,
			'recette' => $recette,
			'depense' => $depense,
			'justification' => $justification,
			'imputation' => lire_config('association/pc_ventes'),
			'id_journal' => $id_vente)))
		  return true;
	}
	return false;
}

?>
