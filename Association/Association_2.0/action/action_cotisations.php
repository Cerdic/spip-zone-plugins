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

	function exec_action_cotisations() {
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$action=$_POST['agir'];
		$id_auteur= intval($_POST['id']);
		$nom_famille= $_POST['nom_famille'];
		$prenom= $_POST['prenom'];
		$date= $_POST['date'];
		$journal= $_POST['journal'];
		$montant= $_POST['montant'];
		$categorie= $_POST['categorie'];
		$justification =$_POST['justification'];
		$imputation=lire_config('association/pc_cotisations');
		$validite =$_POST['validite'];
		$url_retour=$_POST['url_retour'];
		
		if($action=="ajoute") {
			spip_query( "INSERT INTO spip_asso_comptes (date, journal, recette, justification, imputation, id_journal) VALUES ("._q($date).", "._q($journal).", "._q($montant).", "._q($justification).", "._q($imputation).", "._q($id_auteur)." )" );
			association_auteurs_elargis_updateq(
				   array(
					 "validite"=> $validite,
					 "date"=> $date,
					 "montant"=> $montant,
					 "statut_interne"=> 'ok'),
				   "id_auteur=$id_auteur");
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>
