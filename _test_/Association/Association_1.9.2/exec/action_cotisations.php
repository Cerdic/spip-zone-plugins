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

	function exec_action_cotisations() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$action=$_POST['action'];
		$id_auteur= $_POST['id'];
		$nom_famille= $_POST['nom_famille'];
		$prenom= $_POST['prenom'];
		$date= $_POST['date'];
		$journal= $_POST['journal'];
		$montant= $_POST['montant'];
		$categorie= $_POST['categorie'];
		$justification =$_POST['justification'];
		$validite =$_POST['validite'];
		$url_retour=$_POST['url_retour'];
			
		if($action=="ajoute") {
			spip_query( "INSERT INTO spip_asso_comptes (date, journal, recette, justification, imputation, id_journal) VALUES ("._q($date).", "._q($journal).", "._q($montant).", "._q($justification).", ".lire_config('association/pc_cotisations').", "._q($id_auteur)." )" );
			spip_query( "UPDATE spip_auteurs_elargis SET statut_interne='ok' WHERE id_auteur="._q($id_auteur) );
			$query=spip_query("SELECT * FROM spip_asso_adherents WHERE id_auteur='$id_auteur' ");
			if($query) {
				spip_query( "UPDATE spip_asso_adherent SET statut_relance='ok', validite="._q($validite)." WHERE id_auteur="._q($id_auteur) );
			} else {
				spip_query( "INSERT INTO spip_asso_adherent (id_auteur, date, montant, categorie, statut_relance, validite) VALUES ("._q($id_auteur ", "._q($date) ", "._q($montant) ", "._q($categorie) ", 'ok', "._q($validite).".)");
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>