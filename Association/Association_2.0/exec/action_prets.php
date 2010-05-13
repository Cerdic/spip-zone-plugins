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
	
function exec_action_prets(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_action_prets=generer_url_ecrire('action_prets');
		
		$action=$_REQUEST['agir'];
		$id_pret=intval($_REQUEST['id_pret']);
		$id_ressource=$_REQUEST['id_ressource']; // text !
		$id_emprunteur=$_POST['id_emprunteur']; // text !
		$date_sortie=$_POST['date_sortie'];
		$duree=$_POST['duree'];
		$date_retour=$_POST['date_retour'];
		$commentaire_sortie=$_POST['commentaire_sortie'];
		$commentaire_retour=$_POST['commentaire_retour'];
		$statut=$_POST['statut'];
		$montant=$_POST['montant'];
		$journal=$_POST['journal'];
		$imputation=lire_config('association/pc_prets');
		$url_retour=$_POST['url_retour'];
		
		//SUPPRESSION PROVISOIRE PRET
		if ($action == "supprime") {
			
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('asso:prets_titre_suppression_prets')) ;
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			
			$data = sql_fetsel("*", "spip_asso_ressources", "id_ressource=" . _q($id_ressource) ) ;
			$statut=$data['statut'];
			echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:ressources_num').'<br />';
			echo '<span class="spip_xx-large">'.$data['id_ressource'].'</span></div>';
			echo '<p>'._T('asso:ressources_libelle_code').': '.$data['code'].'<br />';
			echo $data['intitule'];
			echo '</p>';
			echo fin_boite_info(true);
		
			echo association_retour();
			
			echo debut_droite("",true);
			
			echo debut_cadre_relief(  "", false, "", $titre = _T('asso:prets_titre_suppression_prets'));
			echo '<p><strong>'._T('asso:prets_danger_suppression',array('id_pret' => $id_pret)).'</strong></p>';
			echo '<form action="'.$url_action_prets.'&agir=drop"  method="post">';
			
			echo '<input type=hidden name="id_pret" value="'.$id_pret.'">';
			echo '<input type=hidden name="url_retour" value="'.$url_retour.'">';
			
			echo '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';
			echo '</form>';
			
			fin_cadre_relief();  
			echo fin_gauche(), fin_page();

		} elseif ($action == "drop") {
			
			sql_delete('spip_asso_prets', "id_pret=$id_pret" );
			sql_delete('spip_asso_comptes', "id_journal=$id_pret" );
			sql_update('spip_asso_ressources',
				   array('statut'=>'ok'),
				   "id_ressource=" . _q($id_ressource) );
			header ('location:'.$url_retour);

		} elseif ($action =="modifie") { 
			prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant);
			header ('location:'.$url_retour);

		} elseif ($action == "ajoute") {
			prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $imputation, $commentaire_sortie,$commentaire_retour);
			header ('location:'.$url_retour);
		}
	}
}

function prets_modifier($duree, $date_sortie, $date_retour, $id_emprunteur, $commentaire_sortie, $id_pret, $journal, $montant)
{
	sql_updateq('spip_asso_prets', array(
		"duree" => $duree,
		"date_sortie" => $date_sortie,
		"date_retour" => $date_retour,
		"id_emprunteur" => $id_emprunteur,
		"commentaire_sortie" => $commentaire_sortie),
			"id_pret=$id_pret" );

	sql_updateq('spip_asso_comptes', array(
		"journal" => $journal,
		"recette" => $montant,
		"date" => $date_sortie),
			"id_journal=$id_pret");
}

function prets_insert($id_ressource, $id_emprunteur, $date_sortie, $duree, $date_retour, $journal, $montant, $imputation, $commentaire_sortie,$commentaire_retour)
{
	$id_pret = sql_insertq('spip_asso_prets', array(
		'id_ressource' => $id_ressource,
		'date_sortie' => $date_sortie,
		'duree' => $duree,
		'date_retour' => $date_retour,
		'id_emprunteur' => $id_emprunteur,
		'commentaire_sortie' => $commentaire_sortie,
		'commentaire_retour' => $commentaire_retour));

	if ($id_pret)
		$id_pret = sql_insertq('spip_asso_comptes', array(
			'date' => $date_sortie,
			'journal' => $journal,
			'recette' => $montant,
			'justification' => _T('asso:pret_nd').$id_ressource.'/'.$id_pret,
			'imputation' => $imputation,
			'id_journal' => $id_pret));

	if ($id_pret)
		sql_updateq('spip_asso_ressources',
			    array('statut' => 'reserve'),
			    "id_ressource=$id_ressource");
	spip_log("prets_insert: $id_pret");
}

?>
