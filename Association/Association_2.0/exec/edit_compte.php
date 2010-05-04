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
include_spip('inc/autoriser');

function exec_edit_compte() {
		
	$id_compte= intval(_request('id'));
	$action= _request('agir');

	if (!autoriser('configurer') OR !preg_match('/^\w+$/', $action)) {
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

		debut_cadre_relief(  "", false, "", $titre = _T('Modification des comptes'));
		
		$res = '<div>';
		$res .= '<label for="imputation"><strong>Imputation :</strong></label>';
		$res .= '<select name="imputation" type="text" id="date" class="formo" />';
		$sql = sql_select('*', 'spip_asso_plan', "classe<>". sql_quote(lire_config('association/classe_banques')), "", "code") ;
		while ($banque = sql_fetch($sql)) {
			$res .= '<option value="'.$banque['code'].'" ';
			if ($imputation==$banque['code']) { $res .= ' selected="selected"'; }
			$res .= '>'.$banque['intitule'].'</option>';
		}
		$res .= '</select>';
		$res .= '<label for="date"><strong>Date (AAAA-MM-JJ) :</strong></label>';
		$res .= '<input name="date" value="'.$date.'" type="text" id="date" class="formo" />';
		$res .= '<label for="recette"><strong>Recette :</strong></label>';
		$res .= '<input name="recette" value="'.$recette.'" type="text" id="recette" class="formo" />';
		$res .= '<label for="depense"><strong>D&eacute;pense :</strong></label>';
		$res .= '<input name="depense" value="'.$depense.'"  type="text" id="depense" class="formo" />';
		$res .= '<label for="journal"><strong>Mode de paiement :</strong></label>';
		$res .= '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = sql_select('*', 'spip_asso_plan', "classe=".sql_quote(lire_config('association/classe_banques')), "", "code") ;
		while ($banque = sql_fetch($sql)) {
			$res .= '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { $res .= ' selected="selected"'; }
			$res .= '>'.$banque['intitule'].'</option>';
		}
		$res .= '</select>';
		$res .= '<label for="justification"><strong>Justification :</strong></label>';
		$res .= '<input name="justification" value="'.$justification.'" type="text" id="justification" class="formo" />';
		
		$res .= '<div style="float:right;">';
		$res .= '<input type="submit" value="';
		if ( isset($action)) {$res .= _T('asso:bouton_'.$action);}
		else {$res .= _T('asso:bouton_envoyer');}
		$res .= '" class="fondo" /></div>';
		$res .= '</div>';

		echo redirige_action_post($action . '_comptes', $id_compte, 'comptes', '', $res);

		fin_cadre_relief();  
		echo fin_gauche(),fin_page();
	}
}
?>
