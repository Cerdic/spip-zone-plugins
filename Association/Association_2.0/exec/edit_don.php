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

function exec_edit_don(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_action_dons = generer_url_ecrire('action_dons');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		$action=$_REQUEST['agir'];
		$id_don= intval(_request('id'));
		
		$data = !$id_don ? '' : sql_fetsel("*", "spip_asso_dons", "id_don=$id_don ");
		if ($data) {
			$date_don=$data['date_don'];
			$bienfaiteur=$data['bienfaiteur'];
			$id_adherent=$data['id_adherent'];
			$argent=$data['argent'];
			$colis=$data['colis'];
			$valeur=$data['valeur'];
			$journal=$data['journal'];
			$contrepartie=$data['contrepartie'];
			$commentaire=$data['commentaire'];
		} else {
		  $bienfaiteur=$id_adherent=$argent=$colis=$valeur=$journal=$contrepartie=$commentaire='';
		  $date_don=date('Y-m-d');
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		if ($id_don) {
		  echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">DON<br><span class="spip_xx-large">'.$id_don.'</span></div>';
		}
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		$res= icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis ($res);
		
		echo debut_droite("", true);
		
		debut_cadre_relief(  "", false, "", $titre = _T('Mise &agrave; jour des dons'));

		$res = '<label for="date_don"><strong>Date (AAAA-MM-JJ) :</strong></label>';
		$res .= '<input name="date_don" type="text" value="'.$date_don.'" id="date_don" class="formo" />';
		$res .= '<label for="bienfaiteur"><strong>Nom du bienfaiteur :</strong></label>';
		$res .= '<input name="bienfaiteur" type="text" value="'.$bienfaiteur.'" id="bienfaiteur" class="formo" />';
		$res .= '<label for="id_adherent"><strong>N&deg; de membre :</strong></label>';
		$res .= '<input name="id_adherent" type="text" value="'.$id_adherent.'" id="id_adherent" class="formo" />';
		$res .= '<label for="argent"><strong>Don financier (en &euro;) :</strong></label>';
		$res .= '<input name="argent" type="text" value="'.$argent.'" id="argent" class="formo" />';
		$res .= '<label for="journal"><strong>Mode de paiement :</strong></label>';
		$res .= '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = sql_select('*', 'spip_asso_plan', "classe=".sql_quote(lire_config('association/classe_banques')), "",  "code") ;
		while ($banque = sql_fetch($sql)) {
			$res .= '<option value="'.$banque['code'].'" ';
			if ($journal==$banque['code']) { $res .= ' selected="selected"'; }
			$res .= '>'.$banque['intitule'].'</option>';
		}
		$res .= '</select>';
		$res .= '<label for="colis"><strong>Colis :</strong></label>';
		$res .= '<input name="colis" type="text" value="'.$colis.'" id="colis" class="formo" />';
		$res .= '<label for="valeur"><strong>Contre-valeur (en &euro;) :</strong></label>';
		$res .= '<input name="valeur" type="text" value="'.$valeur.'" id="valeur" class="formo" />';
		$res .= '<label for="contrepartie"><strong>Geste de l\'association :</strong></label>';
		$res .= '<input name="contrepartie" type="text" size="50" value="'.$contrepartie.'" id="contrepartie" class="formo" />';
		$res .= '<label for="commentaire"><strong>Remarques :</strong></label>';
		$res .= '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		
		$res .= '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {$res .= _L($action);}
		else {$res .= _T('asso:bouton_envoyer');}
		$res .= '" class="fondo" /></div>';

		echo redirige_action_post($action . '_dons' , $id_don, 'dons', "", "<div>$res</div>");
		
		fin_cadre_relief();  
		fin_page();
	}
}
?>
