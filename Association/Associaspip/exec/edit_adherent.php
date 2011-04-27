<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_edit_adherent() {
		
	$id_auteur= intval(_request('id'));

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'adherents')) {
			include_spip('inc/minipres');
			echo minipres();
	} else exec_edit_adherent_args($id_auteur);
}
		
function exec_edit_adherent_args($id_auteur)
{
	$data = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur");
	if (!$data) {
		include_spip('inc/minipres');
		echo minipres(_T('zxml_inconnu_id') . $id_auteur);
	} else {
		$nom_famille=$data['nom_famille'];
		$prenom=$data['prenom'];
		$statut_interne=$data['statut_interne'];
		$categorie=$data['categorie'];
		$validite=$data['validite'];
		$commentaire=$data['commentaire'];
		$adh = generer_url_ecrire('voir_adherent',"id=$id_auteur");
	
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		include_spip ('inc/navigation');
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'.propre(_T('asso:adherent_libelle_numero')).'<br />';
		echo '<span class="spip_xx-large">';
		echo $id_auteur;
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">',
			"<a href='$adh' title=\"",
			_T('asso:adherent_label_voir_membre'),
			"\">",
			htmlspecialchars($nom_famille.' '.$prenom),
			 "</a></td></div>\n";
		echo '<br /><div>'.association_date_du_jour().'</div>';	
		echo fin_boite_info(true);
		
		echo association_retour();
	
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:adherent_titre_modifier_membre'));
		echo edit_adherent($id_auteur, $categorie, $validite, $statut_interne, $commentaire);
		fin_cadre_relief();
		echo fin_page_association(); 
	}
}

function edit_adherent($id_auteur, $categorie, $validite, $statut_interne, $commentaire)
{
	$res = '';
	
	$sel = '';
	$sql = sql_select('*', 'spip_asso_categories', '','', "id_categorie") ;
	while ($var = sql_fetch($sql)) {
			$sel .= '<option value="'.$var['id_categorie'].'"';
			if($categorie== $var['id_categorie']){$sel .= ' selected="selected"';}
			$sel .= '> '.$var['libelle']."</option>\n";
	}

	if ($sel) {
		$res .= '<label for="categorie"><strong>'
		. _T('asso:adherent_libelle_categorie').' :</strong></label>'
		. '<select name="categorie" id="categorie" class="formo">'
		. $sel
		. "</select>\n";
	}

	$res .= '<label for="validite"><strong>'
	. _T('asso:adherent_libelle_validite')
	. ' :</strong></label>'
	. '<input name="validite" value="'
	. $validite
	. '" type="text" id="validite" class="formo" />';

	$sel = '';
	foreach ($GLOBALS['association_liste_des_statuts2'] as $var) {
		$sel .= '<option value="'.$var.'"';
		if ($statut_interne==$var) {$sel .= ' selected="selected"';}
		$sel .= '>'._T('asso:adherent_entete_statut_'.$var)."</option>\n";
	}

	if ($sel) {

		$res .= '<label for="statut_interne"><strong>'
		  . _T('asso:adherent_libelle_statut')
		  . ' :</strong></label>'
		  . '<select name ="statut_interne" id="statut_interne" class="formo">'
		  . $sel
		  . '</select>';
	}

	$res .= '<label for="commentaire"><strong>'
	. _T('asso:adherent_libelle_commentaires').' :</strong></label>'
	. '<textarea name="commentaire" id="commentaire" class="formo" rows="3" cols="80" >'
	. $commentaire
	. '</textarea>'
	. "\n<div style='float:right;'>"
	. '<input type="submit" value="'
	.  _T('asso:bouton_modifie')
	. '" class="fondo" /></div>';

	return redirige_action_post('adherent' , $id_auteur, 'voir_adherent', "id=$id_auteur", $res);
}
?>
