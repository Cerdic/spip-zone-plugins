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
	
function exec_edit_plan(){

	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_asso = generer_url_ecrire('association');
		$url_plan = generer_url_ecrire('plan');
		$url_action_plan=generer_url_ecrire('action_plan');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$id_plan= intval(_request('id'));
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Edition plan comptable')) ;		
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		
		$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Edition plan comptable'));
		
		$data = !$id_plan ? '' : sql_fetsel("*", "spip_asso_plan", "id_plan=$id_plan");
		if ($data) {
			$code=$data['code'];
			$classe=$data['classe'];
			$intitule=$data['intitule'];
			$reference=$data['reference'];
			$actif=$data['actif'];
			$commentaire=$data["commentaire"];
			$solde_anterieur=$data['solde_anterieur'];
			$date_anterieure=$data['date_anterieure'];
			$action = 'modifier';
		} else {
			$code=$classe=$intitule=$reference=$actif=$commentaire=$solde_anterieur='';
			$date_anterieure= date('Y-m-d');
			$action = 'ajouter';
		}
		$checked = ($actif=="oui" || $action=="ajouter");

		$res = '<label for="code"><strong>' . _L('Code :') . '</strong></label>'
		. '<input name="code" type="text" value="'
		. $code
		. '" id="code" class="formo" />'
		. '<label for="classe"><strong>' . _L('Classe :') . '</strong></label>'
		. '<input name="classe" type="text" value="'
		. $classe
		. '" id="classe" class="formo" />'
		. '<label for="intitule"><strong>' . _L('Intitul&eacute; :') . '</strong></label>'
		. '<input name="intitule" type="text" value="'
		. $intitule
		. '" id="intitule" class="formo" />'
		. '<label for="reference"><strong>' . _L('R&eacute;f&eacute;rence :') . '</strong></label>'
		. '<input name="reference" type="text" value="'
		. $reference
		. '" id="reference" class="formo" />'
		. '<label for="solde_anterieur"><strong>' . _L('Solde report&eacute; (en euros) :') . '</strong></label>'
		. '<input name="solde_anterieur" type="text" value="'
		. $solde_anterieur
		. '" id="solde_anterieur" class="formo" />'
		. '<label for="date_anterieure"><strong>' . _L('Date report (AAA-MM-JJ) :') . '</strong></label>'
		. '<input name="date_anterieure" type="text" value="'
		. $date_anterieure
		. '" id="date_anterieure" class="formo" />'
		. '<label for="actif"><strong>' . _L('Compte activ&eacute; :') . '</strong></label>'
		. '<input name="actif" type="radio" value="oui" id="actif"';
		if ($checked) {$res .= ' checked="checked"';}
		$res .= ' />'._T('asso:plan_libelle_oui')
		. '<input name="actif" type="radio" value="non"';
		if (!$checked) {$res .= ' checked="checked"';}
		$res .= ' />'._T('asso:plan_libelle_non')
		. '<br /><label for="commentaire"><strong>' . _L('Commentaires :') . '</strong></label>'
		. '<textarea name="commentaire" id="commentaire" class="formo" />'
		. $commentaire
		. '</textarea>'
		. '<div style="float:right;">'
		. '<input type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_plans' , $id_plan, 'plan', "", "<div>$res</div>");

		fin_cadre_relief();  	
		echo fin_gauche(), fin_page();
	}
}
?>
