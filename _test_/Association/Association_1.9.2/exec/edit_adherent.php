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
	include_spip ('inc/navigation_modules');

	function exec_edit_adherent() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$url_action_adherents=generer_url_ecrire('action_adherents');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$id_auteur= $_GET['id'];
		$indexation = lire_config('association/indexation');
		$query = spip_query( "SELECT * FROM spip_asso_adherents INNER JOIN spip_auteurs_elargis ON spip_asso_adherents.id_auteur=spip_auteurs_elargis.id_auteur WHERE spip_asso_adherents.id_auteur='$id_auteur' ");
		while ($data = spip_fetch_array($query)) { 
			$id_adherent=$data['id_adherent'];
			$id_asso=$data['id_asso'];
			$nom_famille=$data['nom_famille'];
			$prenom=$data['prenom'];
			$statut_interne=$data['statut_interne'];
			$categorie=$data['categorie'];
			$validite=$data['validite'];
			$utilisateur1=$data['utilisateur1'];
			$utilisateur2=$data['utilisateur2'];
			$utilisateur3=$data['utilisateur3'];
			$utilisateur4=$data['utilisateur4'];
		}
		$action='modifie';
		
		debut_page(_T('asso:titre_gestion_pour_association'), "", "");
		
		include_spip ('inc/navigation');
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'._T('asso:adherent_libelle_numero').'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_adherent;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom_famille.' '.$prenom.'</div>';
		echo '<br /><div>'.association_date_du_jour().'</div>';	
		fin_boite_info();
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_liste_actifs'));
		
		echo '<form action="'.$url_action_adherents.'" method="post">';	
		
		echo '<fieldset><legend>'._T('asso:adherent_titre_modifier_membre').'</legend>';
		if (lire_config('association/indexation')=="id_asso"){
			echo	'<label for="id_asso"><strong>N&deg; d\'adh&eacute;rent :</strong></label>';
			echo '<input name="id_asso" value="'.$id_asso.'" type="text" id="id_asso" class="formo" />';
		}
		echo '<label for="categorie"><strong>'._T('asso:adherent_libelle_categorie').' :</strong></label>';
		echo '<select name="categorie" id="categorie" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_categories ORDER BY id_categorie") ;
		while ($var = spip_fetch_array($sql)) {
			echo '<option value="'.$var['valeur'].'"';
			if($categorie== $var['valeur']){echo ' selected="selected"';}
			echo '> '.$var['libelle'].'</option>';
		}
		echo '</select>';
		echo	'<label for="validite"><strong>'._T('adherent_libelle_validite').' :</strong></label>';
		echo '<input name="validite" value="'.$validite.'" type="text" id="validite" class="formo" />';
		echo	'<label for="statut_interne"><strong>'._T('adherent_libelle_statut').' :</strong></label>';
		echo '<select name ="statut_interne" id="statut_interne" class="formo">';
		foreach (array(ok,echu,relance,sorti,lire_config('inscription2/statut_interne')) as $var) {
			echo '<option value="'.$var.'"';
			if ($statut_interne==$var) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$var).'</option>';
		}
		echo '</select>';
		echo '</fieldset>';
		
		echo '<fieldset><legend>'._T('asso:adherent_titre_donnees_complementaires').'</legend>';
		echo	'<label for="utilisateur1"><strong>'._T('asso:adherent_libelle_utilisateur1').' :</strong></label>';
		echo '<input name="utilisateur1" type="text" value="'.$utilisateur1.'" type="text" id="utilisateur1" class="formo" />';
		echo	'<label for="utilisateur2"><strong>'._T('asso:adherent_libelle_utilisateur2').' :</strong></label>';
		echo '<input name="utilisateur2" type="text" value="'.$utilisateur2.'" type="text" id="utilisateur2" class="formo" />';
		echo	'<label for="utilisateur3"><strong>'._T('asso:adherent_libelle_utilisateur3').' :</strong></label>';
		echo '<input name="utilisateur3" type="text" value="'.$utilisateur3.'" type="text" id="utilisateur3" class="formo" />';
		echo	'<label for="utilisateur4"><strong>'._T('asso:adherent_libelle_utilisateur4').' :</strong></label>';
		echo '<input name="utilisateur4" type="text" value="'.$utilisateur4.'" type="text" id="utilisateur4" class="formo" />';
		echo '</fieldset>';
		
		echo '<input name="id" type="hidden" value="'.$id_auteur.'" >';		
		echo '<input name="action" type="hidden" value="'.$action.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		
		fin_cadre_relief();
		fin_page();
	}
?>

