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
	
	function exec_edit_categorie(){
		global $connect_statut, $connect_toutes_rubriques;
		
		debut_page(_T('Cat&eacute;gories de cotisation'), "", "");
		$url_asso = generer_url_ecrire('association');
		$url_ajouter = generer_url_ecrire('ajouter');
		$url_relance = generer_url_ecrire('essai');
		$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
		$url_vente = generer_url_ecrire('ventes');
		$url_banque = generer_url_ecrire('banque');
		$url_delete = generer_url_ecrire('delete_membre');
		$url_action_categorie=generer_url_ecrire('action_categorie');
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
		
		debut_cadre_relief(  "", false, "", $titre = _T('Cat&eacute;gories de cotisation'));
		
		$action=$_REQUEST['action'];
		$id=$_REQUEST['id'];
			
		$query = spip_query( "SELECT * FROM spip_asso_categories WHERE id_categorie='$id' ");
		while($data = spip_fetch_array($query)) {
			$id_categorie=$data['id_categorie'];
			$valeur=$data['valeur'];
			$libelle=$data['libelle'];
			$duree=$data['duree'];
			$cotisation=$data['cotisation'];
			$commentaires=$data["commentaires"];
		}		
		echo '<form action="'.$url_action_categorie.'" method="post">';	
		echo '<input name="id" type="hidden" value="'.$id_categorie.'" />';
		echo '<label for="valeur"><strong>Cat&eacute;gorie :</strong></label>';
		echo '<input name="valeur" type="text" value="'.$valeur.'" id="valeur" class="formo" />';
		echo '<label for="libelle"><strong>Libell&eacute; complet :</strong></label>';
		echo '<input name="libelle" type="text" value="'.$libelle.'" id="libelle" class="formo" />';
		echo '<label for="duree"><strong>Dur&eacute;e (en mois) :</strong></label>';
		echo '<input name="duree" type="text" value="'.$duree.'" id="duree" class="formo" />';
		echo '<label for="montant"><strong>Montant (en euros) :</strong></label>';
		echo '<input name="montant" type="text" value="'.$cotisation.'" id="montant" class="formo" />';
		echo '<label for="commentaires"><strong>Commentaires :</strong></label>';
		echo '<textarea name="commentaires" id="commentaires" class="formo" />'.$commentaires.'</textarea>';
		echo '<input type="hidden" name="action" value="modifie">';
		
		echo '<div style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_envoyer').'" class="fondo" />
		<input name="action" type="hidden" value="'.$action.'"></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}
?>
