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
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_asso = generer_url_ecrire('association');
		$url_plan = generer_url_ecrire('plan');
		$url_action_plan=generer_url_ecrire('action_plan');
		
		$id_plan= intval(_request('id'));
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:edition_plan_comptable')) ;		
		association_onglets();
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite("",true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('asso:edition_plan_comptable'));
		
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

		$res = '<label for="code"><strong>' . _T('asso:code') . '</strong></label>'
		. '<input name="code" type="text" value="'
		. $code
		. '" id="code" class="formo" />'
		. '<label for="classe"><strong>' . _T('asso:classe') . '&nbsp;:</strong></label>'
		. '<input name="classe" type="text" value="'
		. $classe
		. '" id="classe" class="formo" />'
		. '<label for="intitule"><strong>' . _T('asso:intitule') . '&nbsp;;</strong></label>'
		. '<input name="intitule" type="text" value="'
		. $intitule
		. '" id="intitule" class="formo" />'
		. '<label for="reference"><strong>' . _T('asso:reference') . '&nbsp;:</strong></label>'
		. '<input name="reference" type="text" value="'
		. $reference
		. '" id="reference" class="formo" />'
		. '<label for="solde_anterieur"><strong>' . _T('asso:solde_reporte_en_euros') . '</strong></label>'
		. '<input name="solde_anterieur" type="text" value="'
		. $solde_anterieur
		. '" id="solde_anterieur" class="formo" />'
		. '<label for="date_anterieure"><strong>' . _T('asso:date_report_aaa_mm_jj') . '</strong></label>'
		. '<input name="date_anterieure" type="text" value="'
		. $date_anterieure
		. '" id="date_anterieure" class="formo" />'
		. '<label for="actif"><strong>' . _T('asso:compte_active') . '</strong></label>'
		. '<input name="actif" type="radio" value="oui" id="actif"';
		if ($checked) {$res .= ' checked="checked"';}
		$res .= ' />'._T('asso:plan_libelle_oui')
		. '<input name="actif" type="radio" value="non"';
		if (!$checked) {$res .= ' checked="checked"';}
		$res .= ' />'._T('asso:plan_libelle_non')
		. '<br /><label for="commentaire"><strong>' . _T('asso:commentaires') . '&nbsp;:</strong></label>'
		. '<textarea name="commentaire" id="commentaire" class="formo" rows="4" cols="80">'
		. $commentaire
		. "</textarea>\n"
		. '<div style="float:right;">'
		. '<input type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_plans' , $id_plan, 'plan', "", "<div>$res</div>");

		fin_cadre_relief();  	
		echo fin_page_association();
	}
}
?>
