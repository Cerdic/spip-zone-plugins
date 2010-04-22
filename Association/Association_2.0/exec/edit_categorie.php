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
	
	function exec_edit_categorie(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_asso = generer_url_ecrire('association');
		$url_ajouter = generer_url_ecrire('ajouter');
		$url_relance = generer_url_ecrire('essai');
		$url_bienfaiteur = generer_url_ecrire('bienfaiteur');
		$url_vente = generer_url_ecrire('ventes');
		$url_banque = generer_url_ecrire('banque');
		$url_delete = generer_url_ecrire('delete_membre');
		$url_action_categorie=generer_url_ecrire('action_categorie');
		
		$action=$_REQUEST['agir'];
		$id=$_REQUEST['id'];
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$query = spip_query( "SELECT * FROM spip_asso_categories WHERE id_categorie='$id' ");
		while($data = spip_fetch_array($query)) {
			$id_categorie=$data['id_categorie'];
			$valeur=$data['valeur'];
			$libelle=$data['libelle'];
			$duree=$data['duree'];
			$cotisation=$data['cotisation'];
			$commentaires=$data["commentaires"];
		}		
		
		//debut_page(_T(), "", "");
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Cat&eacute;gories de cotisation')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('Cat&eacute;gories de cotisation'));
		
		echo '<form action="'.$url_action_categorie.'" method="post">';	
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
		
		echo '<input type="hidden" name="agir" value="'.$action.'" />';
		echo '<input name="id" type="hidden" value="'.$id_categorie.'" />';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'" />';
				
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		
		echo fin_cadre_relief(true);  
		fin_page();
	}
?>
