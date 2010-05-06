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
	} else {
		
		$url_action_ressources=generer_url_ecrire('action_ressources');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_edition_ressources')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<p>', _L('Gestion des emprunts et des pr&ecirc;ts') . '</p>';
		echo fin_boite_info(true);
	
		echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png"));
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_edition_ressources'));
		
		$id = intval(_request('id'));
		
		$data = !$id ? '' : sql_fetsel("*", "spip_asso_ressources", "id_ressource='$id' ");
		if ($data) {
			$code=$data['code'];
			$intitule=$data['intitule'];
			$date_acquisition=$data['date_acquisition'];
			$id_achat=$data['id_achat'];
			$pu=$data['pu'];
			$statut=$data['statut'];
			$commentaire=$data["commentaire"];
			$action = 'modifier';
		} else {
			$code=$intitule=$id_achat=$pu=$statut=$commentaire='';
			$date_acquisition=date('Y-m-d');
			$action = 'ajouter';
		}

		$res = '<label for="code"><strong>'
		._T('asso:ressources_libelle_code')
		.' :</strong></label>'
		. '<input name="code" type="text" value="'
		.$code
		.'" id="code" class="formo" />'
		. '<label for="intitule"><strong>'
		._T('asso:ressources_libelle_intitule')
		.' :</strong></label>'
		. '<input name="intitule" type="text" value="'
		.$intitule
		.'" id="intitule" class="formo" />'
		. '<label for="date_acquisition"><strong>'
		._T('asso:ressources_libelle_date_acquisition')
		.' :</strong></label>'
		. '<input name="date_acquisition" type="text" value="'
		.$date_acquisition
		.'" id="date_acquisition" class="formo" />'
		. '<label for="pu"><strong>'
		._T('asso:ressources_libelle_prix_location')
		.' :</strong></label>'
		. '<input name="pu" type="text" value="'
		.$pu
		.'" id="pu" class="formo" />'	
		. '<label for="statut"><strong>'
		. _T('asso:ressources_libelle_statut')
		.' :</strong></label><br />';

		foreach ( array('ok','reserve','suspendu','sorti') as $var) {
			$res .= '<input name="statut" type="radio" name="statut" value="'.$var.'"';
			if ($statut==$var) {$res .= ' checked="checked" ';}
			$res .= ' id="statut" /> '._T('asso:ressources_libelle_statut_'.$var);
		}
		$res .= '<br /><label for="commentaire"><strong>'
		. _T('asso:ressources_libelle_commentaires')
		.' :</strong></label>'
		. '<textarea name="commentaire" id="commentaire" class="formo" />'
		.$commentaire
		.'</textarea>'
		. '<div style="float:right;"><input name="submit" type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" />';

		echo redirige_action_post($action . '_ressources', $id, 'ressources', '', "<div>$res</div>");
		
		fin_cadre_relief();  
		echo fin_gauche(), fin_page();
	}
}
?>
