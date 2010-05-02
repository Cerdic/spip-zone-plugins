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
	
function exec_edit_categorie(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		$id=intval(_request('id'));
		$url_retour = $_SERVER['HTTP_REFERER'];
		
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
		echo $commencer_page(_T('Cat&eacute;gories de cotisation')) ;
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
		echo bloc_des_raccourcis($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('Cat&eacute;gories de cotisation'));

		$res = '<label for="valeur"><strong>Cat&eacute;gorie :</strong></label>'
		. '<input name="valeur" type="text" value="'
		. $valeur.'" id="valeur" class="formo" />'
		. '<label for="libelle"><strong>Libell&eacute; complet :</strong></label>'
		. '<input name="libelle" type="text" value="'
		. $libelle.'" id="libelle" class="formo" />'
		. '<label for="duree"><strong>Dur&eacute;e (en mois) :</strong></label>'
		. '<input name="duree" type="text" value="'
		. $duree.'" id="duree" class="formo" />'
		. '<label for="montant"><strong>Montant (en euros) :</strong></label>'
		. '<input name="montant" type="text" value="'
		. $cotisation.'" id="montant" class="formo" />'
		. '<label for="commentaires"><strong>Commentaires :</strong></label>'
		. '<textarea name="commentaires" id="commentaires" class="formo" />'
		. $commentaires.'</textarea>'
		. '<div style="float:right;">'
		. '<input name="submit" type="submit" value="'
		. _T('asso:bouton_envoyer')
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_categories' , $id, 'categories', "", "<div>$res</div>");		
		echo fin_cadre_relief(true);  
		echo fin_gauche(), fin_page();
	}
}
?>
