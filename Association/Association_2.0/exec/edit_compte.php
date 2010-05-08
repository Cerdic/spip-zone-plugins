<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/autoriser');

function exec_edit_compte() {
		
	$id_compte= intval(_request('id'));

	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		
		association_onglets();
		
		echo debut_gauche("", true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);

		$data = !$id_compte ? '' :sql_fetsel('*', 'spip_asso_comptes', "id_compte=$id_compte") ;
		if ($data) {
		$imputation=$data['imputation'];
		$date=$data['date'];
		$recette=$data['recette'];
		$depense=$data['depense'];
		$journal=$data['journal'];
		$justification=$data['justification'];
		} else {
		$imputation=$recette=$depense=$journal=$justification='';
		$date = date('Y-m-d');
		}

		debut_cadre_relief(  "", false, "", $titre = _T('association:modification_des_comptes'));
		
		$res = '<label for="imputation"><strong>' . _T('asso:imputation') . '</strong></label>';
		$res .= '<select name="imputation" type="text" id="date" class="formo" />';
		$sql = sql_select('*', 'spip_asso_plan', "classe<>". sql_quote(lire_config('association/classe_banques')), "", "code") ;
		while ($banque = sql_fetch($sql)) {
			$res .= '<option value="'.$banque['code'].'" ';
			if ($imputation==$banque['code']) { $res .= ' selected="selected"'; }
			$res .= '>'.$banque['intitule'].'</option>';
		}
		$res .= '</select>';
		$res .= '<label for="date"><strong>' . _T('asso:date_aaaa_mm_jj') . '</strong></label>';
		$res .= '<input name="date" value="'.$date.'" type="text" id="date" class="formo" />';
		$res .= '<label for="recette"><strong>' . _T('asso:recette') . '</strong></label>';
		$res .= '<input name="recette" value="'.$recette.'" type="text" id="recette" class="formo" />';
		$res .= '<label for="depense"><strong>' . _T('asso:depense') . '</strong></label>';
		$res .= '<input name="depense" value="'.$depense.'"  type="text" id="depense" class="formo" />';
		$res .= '<label for="journal"><strong>'._T('association:prets_libelle_mode_paiement').'&nbsp;:</strong></label>';

		$sel = '';
		$sql = sql_select('*', 'spip_asso_plan', "classe=".sql_quote(lire_config('association/classe_banques')), "", "code") ;
		while ($banque = sql_fetch($sql)) {
			$sel .= '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { $sel .= ' selected="selected"'; }
			$sel .= '>'.$banque['intitule'].'</option>';
		}
		if ($sel) 
			$res .= '<select name="journal" type="text" id="journal" class="formo" />' . $sel . '</select>';

		$action = ($id_compte ? 'modifier' : 'ajouter');

		$res .= '<label for="justification"><strong>'
		. _T('asso:justification')
		. '&nbsp;:</strong></label>'
		. '<input name="justification" value="'
		. $justification
		. '" type="text" id="justification" class="formo" />'
		. '<div style="float:right;"><input type="submit" value="'
		. _T('asso:bouton_'. $action)
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_comptes', $id_compte, 'comptes', '', "<div>$res</div>");

		fin_cadre_relief();  
		echo fin_gauche(),fin_page();
	}
}
?>
