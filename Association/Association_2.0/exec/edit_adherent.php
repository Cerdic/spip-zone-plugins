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

	function exec_edit_adherent() {
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_adherents=generer_url_ecrire('action_adherents');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$id_auteur= intval($_GET['id']);
		$indexation = lire_config('association/indexation');
		$query = association_auteurs_elargis_select("*",'', "id_auteur=$id_auteur");
		while ($data = spip_fetch_array($query)) { 
			$id_adherent=$data['id_adherent'];
			$id_asso=$data['id_asso'];
			$nom_famille=$data['nom_famille'];
			$prenom=$data['prenom'];
			$statut_interne=$data['statut_interne'];
			$categorie=$data['categorie'];
			$validite=$data['validite'];
			$commentaire=$data['commentaire'];
		}
		$action='modifie';
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		
		include_spip ('inc/navigation');
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_auteur;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom_famille.' '.$prenom.'</div>';
		echo '<br /><div>'.association_date_du_jour().'</div>';	
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis ($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_modifier_membre'));
		
		echo '<form action="'.$url_action_adherents.'" method="post">';	
		if (lire_config('association/indexation')=="id_asso"){
			echo '<label for="id_asso"><strong>N&deg; d\'adh&eacute;rent :</strong></label>';
			echo '<input name="id_asso" value="'.$id_asso.'" type="text" id="id_asso" class="formo" />';
		}
		echo '<label for="categorie"><strong>'._T('asso:adherent_libelle_categorie').' :</strong></label>';
		echo '<select name="categorie" id="categorie" class="formo" />';
		$sql = spip_query ("SELECT * FROM spip_asso_categories ORDER BY id_categorie") ;
		while ($var = spip_fetch_array($sql)) {
			echo '<option value="'.$var['id_categorie'].'"';
			if($categorie== $var['id_categorie']){echo ' selected="selected"';}
			echo '> '.$var['libelle'].'</option>';
		}
		echo '</select>';
		echo '<label for="validite"><strong>'._T('asso:adherent_libelle_validite').' :</strong></label>';
		echo '<input name="validite" value="'.$validite.'" type="text" id="validite" class="formo" />';
		echo '<label for="statut_interne"><strong>'._T('asso:adherent_libelle_statut').' :</strong></label>';
		echo '<select name ="statut_interne" id="statut_interne" class="formo" />';
		foreach (array(ok,echu,relance,sorti,lire_config('inscription2/statut_interne')) as $var) {
			echo '<option value="'.$var.'"';
			if ($statut_interne==$var) {echo ' selected="selected"';}
			echo '> '._T('asso:adherent_entete_statut_'.$var).'</option>';
		}
		echo '</select>';
		echo '<label for="commentaire"><strong>'._T('asso:adherent_libelle_remarques').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';		
		echo '<input name="id" type="hidden" value="'.$id_auteur.'" >';		
		echo '<input name="agir" type="hidden" value="'.$action.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="bouton" type="submit" value="';
		if ( isset($action)) {echo  _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" /></div>';
		echo '</form>';
		fin_cadre_relief();
		  echo fin_gauche(),fin_page(); 
	}
?>

