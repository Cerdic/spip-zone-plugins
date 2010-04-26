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

function exec_edit_adherent() {
		
	$id_auteur= intval($_GET['id']);
	$data = sql_fetch(association_auteurs_elargis_select("*",'', "id_auteur=$id_auteur"));

	include_spip('inc/autoriser');
	if (!autoriser('configurer') OR !$data) {
			include_spip('inc/minipres');
			echo minipres();
	} else {
		
		$url_retour = $_SERVER['HTTP_REFERER'];

		$indexation = lire_config('association/indexation');
		$id_adherent=$data['id_adherent'];
		$id_asso=$data['id_asso'];
		$nom_famille=$data['nom_famille'];
		$prenom=$data['prenom'];
		$statut_interne=$data['statut_interne'];
		$categorie=$data['categorie'];
		$validite=$data['validite'];
		$commentaire=$data['commentaire'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		include_spip ('inc/navigation');
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		if($indexation=="id_asso"){echo $id_asso;} else {echo $id_auteur;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$nom_famille.' '.$prenom.'</div>';
		echo '<br /><div>'.association_date_du_jour().'</div>';	
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION_ICONES."retour-24.png","rien.gif",false);	
		echo bloc_des_raccourcis ($res);
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_modifier_membre'));
		echo edit_adherent($id_auteur, $id_asso, $categorie, $validite, $statut_interne, $commentaire);
		fin_cadre_relief();
		echo fin_gauche(),fin_page(); 
	}
}

function edit_adherent($id_auteur, $id_asso, $categorie, $validite, $statut_interne, $commentaire)
{
	$res = '';
	if (lire_config('association/indexation')=="id_asso"){
			$res .= '<label for="id_asso"><strong>N&deg; d\'adh&eacute;rent :</strong></label>';
			$res .= '<input name="id_asso" value="'.$id_asso.'" type="text" id="id_asso" class="formo" />';
	}

	$res .= '<label for="categorie"><strong>'
	. _T('asso:adherent_libelle_categorie').' :</strong></label>';

	$res .= '<select name="categorie" id="categorie" class="formo" />';

	$sql = sql_select('*', 'spip_asso_categories', '','', "id_categorie") ;
	while ($var = sql_fetch($sql)) {
			$res .= '<option value="'.$var['id_categorie'].'"';
			if($categorie== $var['id_categorie']){$res .= ' selected="selected"';}
			$res .= '> '.$var['libelle'].'</option>';
	}
	$res .= '</select>';

	$res .= '<label for="validite"><strong>'
	. _T('asso:adherent_libelle_validite')
	. ' :</strong></label>'
	. '<input name="validite" value="'
	. $validite
	. '" type="text" id="validite" class="formo" />'
	. '<label for="statut_interne"><strong>'
	. _T('asso:adherent_libelle_statut')
	. ' :</strong></label>';

	$res .= '<select name ="statut_interne" id="statut_interne" class="formo" />';
	foreach (array(ok,echu,relance,sorti,lire_config('inscription2/statut_interne')) as $var) {
		$res .= '<option value="'.$var.'"';
		if ($statut_interne==$var) {$res .= ' selected="selected"';}
		$res .= '>'._T('asso:adherent_entete_statut_'.$var).'</option>';
	}
	$res .= '</select>';

	$res .= '<label for="commentaire"><strong>'._T('asso:adherent_libelle_remarques').' :</strong></label>'
	. '<textarea name="commentaire" id="commentaire" class="formo" />'
	. $commentaire
	. '</textarea>'
	. '<input name="id" type="hidden" value="'
	. $id_auteur
	. '" >'
	. '<div style="float:right;">'
	. '<input name="bouton" type="submit" value="'
	.  _T('asso:bouton_modifie')
	. '" class="fondo" /></div>';

	return redirige_action_post('adherent' , $id_auteur, 'voir_adherent', "id=$id_auteur", $res);
}
?>
