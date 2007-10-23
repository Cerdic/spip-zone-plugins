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

	function exec_action_adherents() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$id_auteur=$_POST['id'];
		if (lire_config('association/indexation')=="id_asso"){ $id_asso=$_POST['id_asso'];}
		$categorie=$_POST['categorie'];
		$validite=$_POST['validite'];
		$utilisateur1=$_POST['utilisateur1'];
		$utilisateur2=$_POST['utilisateur2'];
		$utilisateur3=$_POST['utilisateur3'];
		$utilisateur4=$_POST['utilisateur4'];
		$statut_interne=$_POST['statut_interne'];
		$action=$_POST['action'];
		$url_retour=$_POST['url_retour'];
		
		//MODIFICATION ADHERENT
		if ($action=="modifie") {
			spip_query("UPDATE spip_asso_adherents SET categorie="._q($categorie).", id_asso="._q($id_asso).", utilisateur1="._q($utilisateur1).", utilisateur2="._q($utilisateur2).", utilisateur3="._q($utilisateur3).", utilisateur4="._q($utilisateur4).", validite="._q($validite)." WHERE id_auteur="._q($id_auteur) );
			spip_query("UPDATE spip_auteurs_elargis SET statut_interne="._q($statut_interne)." WHERE id_auteur="._q($id_auteur) );
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>