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
	
function exec_edit_categorie(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id=intval(_request('id'));
		
		$data = !$id ? '' : sql_fetsel("*", "spip_asso_categories", "id_categorie=$id");
		if ($data) {
			$valeur=$data['valeur'];
			$libelle=$data['libelle'];
			$duree=$data['duree'];
			$cotisation=$data['cotisation'];
			$commentaires=$data["commentaires"];
			$action = 'modifier';
		} else {
			$valeur=$libelle=$duree=$cotisation=$commentaires='';
			$action = 'ajouter';
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		echo association_retour();
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", _T('asso:categories_de_cotisations'));

		$res = '<label for="valeur"><strong>' . _T('asso:categorie')
		. '&nbsp;:</strong></label>'
		. '<input name="valeur" type="text" value="'
		. $valeur.'" id="valeur" class="formo" />'
		. '<label for="libelle"><strong>' . _T('asso:libelle_complet')
		. '&nbsp;:</strong></label>'
		. '<input name="libelle" type="text" value="'
		. $libelle.'" id="libelle" class="formo" />'
		. '<label for="duree"><strong>' . _T('asso:duree_en_mois')
		. '&nbsp;:</strong></label>'
		. '<input name="duree" type="text" value="'
		. $duree.'" id="duree" class="formo" />'
		. '<label for="montant"><strong>' . _T('asso:montant_en_euros')
		. '&nbsp;:</strong></label>'
		. '<input name="cotisation" type="text" value="'
		. $cotisation.'" id="cotisation" class="formo" />'
		. '<label for="commentaires"><strong>' . _T('asso:commentaires') . '&nbsp;:</strong></label>'
		. '<textarea name="commentaires" id="commentaires" class="formo"  rows="3" cols="80">'
		. $commentaires.'</textarea>'
		. '<div style="float:right;">'
		. '<input name="submit" type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_categories' , $id, 'categories', "", "<div>$res</div>");		
		echo fin_cadre_relief(true);  
		echo fin_page_association();
	}
}
?>
