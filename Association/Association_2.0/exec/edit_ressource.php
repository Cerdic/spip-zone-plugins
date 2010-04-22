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
	
	function exec_edit_ressource(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_edition_ressources')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<p>Gestion des emprunts et des pr&ecirc;ts</p>';
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-png","rien.gif",false);	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_edition_ressources'));
		
		$action=$_REQUEST['agir'];
		$id=$_REQUEST['id'];
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$query = spip_query( "SELECT * FROM spip_asso_ressources WHERE id_ressource='$id' ");
		while($data = spip_fetch_array($query)) {
			$id_ressource=$data['id_ressource'];
			$code=$data['code'];
			$intitule=$data['intitule'];
			$date_acquisition=$data['date_acquisition'];
			$id_achat=$data['id_achat'];
			$pu=$data['pu'];
			$statut=$data['statut'];
			$commentaire=$data["commentaire"];
		}		
		echo '<form action="'.$url_action_ressources.'&agir='.$action.'" method="post">';	
		echo '<input name="id" type="hidden" value="'.$id_ressource.'" />';
		echo '<label for="code"><strong>'._T('asso:ressources_libelle_code').' :</strong></label>';
		echo '<input name="code" type="text" value="'.$code.'" id="code" class="formo" />';
		echo '<label for="intitule"><strong>'._T('asso:ressources_libelle_intitule').' :</strong></label>';
		echo '<input name="intitule" type="text" value="'.$intitule.'" id="intitule" class="formo" />';
		echo '<label for="date_acquisition"><strong>'._T('asso:ressources_libelle_date_acquisition').' :</strong></label>';
		echo '<input name="date_acquisition" type="text" value="'.$date_acquisition.'" id="date_acquisition" class="formo" />';
		echo '<label for="pu"><strong>'._T('asso:ressources_libelle_prix_location').' :</strong></label>';
		echo '<input name="pu" type="text" value="'.$pu.'" id="pu" class="formo" />';	
		echo '<label for="statut"><strong>'._T('asso:ressources_libelle_statut').' :</strong></label><br />';
		foreach ( array(ok,reserve,suspendu,sorti) as $var) {
			echo '<input name="statut" type="radio" name="statut" value="'.$var.'"';
			if ($statut==$var) {echo ' checked="checked" ';}
			echo ' id="statut"> '._T('asso:ressources_libelle_statut_'.$var);
		}
		echo '<br /><label for="commentaire"><strong>'._T('asso:ressources_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		echo '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}
		echo '" class="fondo" />
		<input name="url_retour" type="hidden" value="'.$url_retour.'">
		<input name="agir" type="hidden" value="'.$action.'"></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
?>
