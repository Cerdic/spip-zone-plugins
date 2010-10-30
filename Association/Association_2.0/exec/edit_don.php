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
		
		$id_don= intval(_request('id'));
		$action=_request('agir');
		if (!$action) $action = $id_don ? 'modifier' : 'ajouter';
		
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
		$titre = _T('asso:dons_titre_mise_a_jour');
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($titre) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		if ($id_don) {
		  echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">DON<br><span class="spip_xx-large">'.$id_don.'</span></div>';
		}
		echo association_date_du_jour();
		echo fin_boite_info(true);
		
		echo association_retour();
		
		echo debut_droite("", true);
		
		debut_cadre_relief(  "", false, "", $titre);

		$res = '<label for="date_don"><strong>' . _T('asso:date_aaaa_mm_jj') . '</strong></label>';
		$res .= '<input name="date_don" type="text" value="'.$date_don.'" id="date_don" class="formo" />';
		$res .= '<label for="bienfaiteur"><strong>' . _T('asso:nom_du_bienfaiteur') . '</strong></label>';
		$res .= '<input name="bienfaiteur" type="text" value="'.$bienfaiteur.'" id="bienfaiteur" class="formo" />';
		$res .= '<label for="id_adherent"><strong>' . _T('asso:nd_de_membre') . '</strong></label>';
		$res .= '<input name="id_adherent" type="text" value="'.$id_adherent.'" id="id_adherent" class="formo" />';
		$res .= '<label for="argent"><strong>' . _T('asso:don_financier_en_e__') . '</strong></label>';
		$res .= '<input name="argent" type="text" value="'.$argent.'" id="argent" class="formo" />'
		. association_mode_de_paiement($journal, _T('asso:prets_libelle_mode_paiement'))
		. "<label for='colis'><strong>" . _T('asso:colis') . "&nbsp;:</strong></label>\n"
		. "<input name='colis' type='text' value='$colis' id='colis' class='formo' />\n"
		. '<label for="valeur"><strong>' . _T('asso:contre_valeur_en_e__') . "</strong></label>\n"
		. '<input name="valeur" type="text" value="'.$valeur.'" id="valeur" class="formo" />'
		. '<label for="contrepartie"><strong>Geste de l\'association :</strong></label>'
		. '<input name="contrepartie" type="text" size="50" value="'.$contrepartie.'" id="contrepartie" class="formo" />'
		. '<label for="commentaire"><strong>' . _T('asso:remarques') . "</strong></label>\n"
		. '<textarea name="commentaire" id="commentaire" class="formo" rows="3" cols="80">'.$commentaire."</textarea>\n"
		. '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {$res .= _L($action);}
		else {$res .= _T('asso:bouton_envoyer');}
		$res .= '" class="fondo" /></div>';

		echo redirige_action_post($action . '_dons' , $id_don, 'dons', "", "<div>$res</div>");
		
		fin_cadre_relief();  
		echo fin_page_association();
	}
}

?>
