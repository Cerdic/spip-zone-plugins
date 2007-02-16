<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/presentation");
include_spip("inc/layer");
include_spip("base/forms");
include_spip("inc/forms");

function inc_forms_lier_donnees($type, $id, $script, $deplie=false){
  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	$type_table = forms_type_table_lier($type, $id);
	$prefixi18n = forms_prefixi18n($type_table);
	
	$out = "";
	$out .= "<a name='tables'></a>";
	if (_request('cherche_donnee') || $deplie){
		$bouton = bouton_block_visible("tables_article");
		$debut_block = 'debut_block_visible';
	}
	else{
		$bouton = bouton_block_invisible("tables_article");
		$debut_block = 'debut_block_invisible';
	}

	$icone = find_in_path("img_pack/$type_table-24.gif");
	if (!$icone)
		$icone = find_in_path("img_pack/$type_table-24.png");
	if (!$icone)
		$icone = find_in_path("img_pack/table-24.gif");
	$out .= debut_cadre_enfonce($icone, true, "", $bouton._T("$prefixi18n:tables"));

	$lesdonnees = array();
	//
	// Afficher les donnees liees, rangees par tables
	//
	list($s,$les_donnees) = Forms_formulaire_objet_afficher_donnees($type,$id,$script,$type_table);
	$out .= $s;
	
	$out .= $debut_block("tables_$type",true);
	//
	// Afficher le formulaire de recherche des donnees des tables
	//

	$out .= Forms_formulaire_objet_chercher_donnee($type,$id,$les_donnees, $script, $type_table);
	$out .= fin_block(true);
	
	$out .= fin_cadre_enfonce(true);
	return $out;
}

function forms_type_table_lier($type,$id){
	$type_table = 'table';
	if ($type == 'donnee'){
		$id = explode('-',$id);
		$id_donnee_source = $id[0];
		$champ = $id[1];
		$id_form = 0;
		$res = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
		if($row = spip_fetch_array($res))
			$id_form = $row['id_form'];
		$res = spip_query("SELECT extra_info FROM spip_forms_champs WHERE id_form=".q($id_form)." AND champ="._q($champ));
		if($row = spip_fetch_array($res))
			$type_table = $row['extra_info'];
	}
	return $type_table;
}
function forms_prefixi18n($type_table){
	return $prefixi18n = str_replace("_","",strtolower($type_table));
}

function Forms_formulaire_objet_chercher_donnee($type,$id,$les_donnees, $script, $type_table){
  global $spip_lang_right,$spip_lang_left,$couleur_claire,$couleur_foncee;
	$out = "";
	$recherche = _request('cherche_donnee');
	
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	$redirect = ancre_url(generer_url_ecrire($script,"type=$type&id_$type=$id"),'tables');
	$action = generer_action_auteur("forms_lier_donnees","$id,$type,ajouter");
	
	$out .= "<form action='$action' method='post' class='ajaxAction' >";
	$out .= form_hidden($action);
	$out .= "<input type='hidden' name='redirect' value='$redirect' />";
	$out .= "<input type='hidden' name='idtarget' value='forms_lier_donnees-$id' />";
	$out .= "<input type='hidden' name='redirectajax' value='".generer_url_ecrire('forms_lier_donnees',"type=$type&id_$type=$id")."' />";
	$out .= "<div style='text-align:$spip_lang_left'>";
	$out .= "<input id ='autocompleteMe' type='text' name='cherche_donnee' value='$recherche' class='forml' />";

	$out .= Forms_boite_selection_donnees($recherche?$recherche:((_request('ajouter')!==NULL)?"":$recherche),$les_donnees, $type_table);
	
	$script_rech = generer_url_ecrire("recherche_donnees","type=$type&id_$type=$id",true);
	$out .= "<input type='hidden' name='autocompleteUrl' value='$script_rech' />";

	$out .= "<style type='text/css' media='all'>
.autocompleter
{
	border: 1px solid $couleur_foncee;
	width: 350px;
	background-color: $couleur_claire;
}
.autocompleter ul li
{
	padding: 2px 10px;
	white-space: nowrap;
	font-size: 11px;
}
.selectAutocompleter
{
	background-color: $couleur_foncee;
}</style>";
	
	$out .= "</div>";
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='ajouter' value='"._T('bouton_ajouter')."' class='fondo' />";
	$out .= "</div>";
	$out .= "</form>";
	return $out;
}

function Forms_formulaire_objet_afficher_donnees($type,$id, $script, $type_table='table'){
	$out = "";
	$prefixi18n = forms_prefixi18n($type_table);

	$les_donnees = array();
	$liste = array();
	$forms = array();
	$retour = self();
	
	$res = spip_query("SELECT id_donnee FROM spip_forms_donnees_{$type}s AS d WHERE d.id_$type="._q($id));
	while ($row = spip_fetch_array($res)){
		list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee']);
		if (!count($t))
			list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee'], false);
		if (count($t)){
			$liste[$id_form][$row['id_donnee']]=$t;
			$forms[$id_form] = $titreform;
		}
	}
	
	if (count($liste)) {
		$out .= "<div class='liste liste-donnees'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		foreach($liste as $id_form=>$donnees){
			$vals = array();
			$vals[] = "";
			$vals[] = "<a href='".generer_url_ecrire("donnees_tous","id_form=$id_form&retour=".urlencode($retour))."'>".$forms[$id_form]."</a>";
			$vals[] = "";
			$table[] = $vals;
			foreach($donnees as $id_donnee=>$champs){
				$les_donnees[] = $id_donnee;
				$vals = array();
				$vals[] = $id_donnee;
				$vals[] = "<a href='".generer_url_ecrire("donnees_edit","id_form=$id_form&id_donnee=$id_donnee&retour=".urlencode($retour))."'>"
					.implode(", ",$champs)."</a>";
				$redirect = ancre_url(generer_url_ecrire($script,"type=$type&id_$type=$id"),'tables');
				$action = generer_action_auteur("forms_lier_donnees","$id,$type,retirer,$id_donnee",urlencode($redirect));
				$action = ancre_url($action,"forms_lier_donnees-$id");
				$redirajax = generer_url_ecrire("forms_lier_donnees","type=$type&id_$type=$id");
				$vals[] = "<a href='$action' rel='$redirajax' class='ajaxAction' >"
					. _T("$prefixi18n:lien_retirer_donnee")."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'")
					. "</a>";
				$table[] = $vals;
			}
		}
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles, false);
	
		$out .= "</table></div>\n";
	}
	$les_donnees = implode (',',$les_donnees);
	return array($out,$les_donnees) ;
}

function Forms_boite_selection_donnees($recherche, $les_donnees, $type_table){
	$out = "";
	$liste_res = Forms_liste_recherche_donnees($recherche,$les_donnees,$type_table);
	if (count($liste_res)){
		$out .= "<select name='id_donnee_liee' class='fondl' style='width:100%' size='10'>";
		foreach($liste_res as $titre=>$donnees){
			$out .= "<option value=''>$titre</option>";
			foreach($donnees as $id_donnee=>$champs){
				$out .= "<option value='$id_donnee'>&nbsp;&nbsp;&nbsp;";
				$out .= implode (", ",$champs);
				$out .= "</option>";
			}
		}
		$out .= "</select>";
	}
	$out .= "<input id='_id_donnee_liee' type='hidden' name='_id_donnee_liee' value='' />";
	return $out;
}

function Forms_liste_recherche_donnees($recherche,$les_donnees,$type_table){
	$table = array();
	if ($recherche!==NULL){
		include_spip('base/abstract_sql');
		$in = calcul_mysql_in('id_donnee',$les_donnees,'NOT');
		if (!strlen($recherche))
			$res = spip_query("SELECT id_donnee FROM spip_forms_donnees AS d
			  JOIN spip_forms AS f ON f.id_form=d.id_form
			  WHERE f.type_form="._q($type_table)." AND $in GROUP BY id_donnee");
		else {
			$res = spip_query("SELECT c.id_donnee FROM spip_forms_donnees_champs AS c
			JOIN spip_forms_donnees AS d ON d.id_donnee = c.id_donnee
			JOIN spip_forms AS f ON d.id_form = f.id_form
			WHERE f.type_form="._q($type_table)." AND $in AND valeur LIKE "._q("$recherche%")." GROUP BY id_donnee");
			if (spip_num_rows($res)<10){
				$res = spip_query("SELECT c.id_donnee FROM spip_forms_donnees_champs AS c
				JOIN spip_forms_donnees AS d ON d.id_donnee = c.id_donnee
				JOIN spip_forms AS f ON d.id_form = f.id_form
				WHERE f.type_form="._q($type_table)." AND $in AND valeur LIKE "._q("%$recherche%")." GROUP BY id_donnee");
			}
		}
		while ($row = spip_fetch_array($res)){
			list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee']);
			if (!count($t))
				list($id_form,$titreform,$t) = Forms_liste_decrit_donnee($row['id_donnee'],false);
			if (count($t))
				$table[$titreform][$row['id_donnee']]=$t;
		}
	}
	return $table;
}

function Forms_liste_decrit_donnee($id_donnee, $specifiant=true){
	$t = array();$titreform="";
	if ($specifiant) $specifiant = "c.specifiant='oui' AND ";
	else $specifiant="";
	$res2 = spip_query("SELECT c.titre,dc.valeur,f.titre AS titreform,f.id_form FROM spip_forms_donnees_champs AS dc 
	JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee
	JOIN spip_forms_champs AS c ON c.champ=dc.champ AND c.id_form=d.id_form
	JOIN spip_forms AS f ON f.id_form=d.id_form
	WHERE $specifiant dc.id_donnee="._q($id_donnee)." AND f.linkable='oui' ORDER BY c.rang");
	/*var_dump("SELECT c.titre,dc.valeur FROM spip_forms_donnees_champs AS dc 
	JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee
	JOIN spip_forms_champs AS c ON c.champ=dc.champ AND c.id_form=d.id_form
	WHERE c.specifiant='oui' AND dc.id_donnee="._q($row['id_donnee'])." ORDER BY c.rang");*/
	while ($row2 = spip_fetch_array($res2)){
		$t[$row2['titre']] = $row2['valeur'];
		$titreform = $row2['titreform'];
		$id_form = $row2['id_form'];
	}
	return array($id_form,$titreform,$t);
}
?>