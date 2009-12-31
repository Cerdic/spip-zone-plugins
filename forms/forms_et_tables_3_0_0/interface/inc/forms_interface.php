<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

/**
 * Enter description here...
 *
 * @param unknown_type $type_table
 * @return unknown
 */
function forms_prefixi18n($type_table){
	$prefixi18n = in_array($type_table,array('sondage',''))?'form':$type_table;
	return $prefixi18n = str_replace("_","",strtolower($prefixi18n));
}

function forms_afficher_liste_donnees_liees($type_source, $id, $type_lie, $type_table, $script, $bloc_id, $arg_ajax, $retour){
	// article, donnee
	// donnee, donnee_liee
	// donnee_liee, donnee
	$lieeliante = ($type_source=='donnee_liee')?'liante':'liee';
	$linkable = strncmp($type_source,'donnee',6)!=0;
	$in_type_table="";
	if ($type_table){
		include_spip("base/abstract_sql");
		include_spip("base/forms_base_api_v2");
		$in_type_table = calcul_mysql_in('d.id_form',implode(",",forms_lister_tables($type_table)))." AND";
	}

	$out = "";
	$iid = intval($id);

	$les_donnees = "0";
	$nombre_donnees = 0;
	$liste = array();
	$forms = array();
	$types = array();
	$prefixi18n = array();

	$champ_donnee_liee = "id_$type_lie";
	$champ_donnee_source = "id_$type_source";
	$table_liens = strncmp($type_source,"donnee",6)==0?"spip_forms_donnees_donnees":"spip_forms_donnees_{$type_source}s";

	$res = spip_query("SELECT dl.$champ_donnee_liee
	  FROM $table_liens AS dl
	  JOIN spip_forms_donnees AS d ON d.id_donnee=dl.$champ_donnee_liee
	  WHERE $in_type_table dl.$champ_donnee_source="._q($iid));
	$nombre_donnees = $cpt = spip_num_rows($res);
	while ($row = spip_fetch_array($res,SPIP_NUM))	$les_donnees.=",".$row[0];

	$tranches = ($cpt>1000)?2*_TRANCHES:_TRANCHES;
	$tmp_var = $bloc_id;
	$nb_aff = floor(1.5 * $tranches);
	if ($cpt > $nb_aff) {
		$nb_aff = $tranches;
		$tranches = afficher_tranches_requete($cpt, $tmp_var, generer_url_ecrire($script,$arg_ajax), $nb_aff);
	} else $tranches = '';

	$deb_aff = _request($tmp_var);
	$deb_aff = ($deb_aff !== NULL ? intval($deb_aff) : 0);

	$limit = (($deb_aff < 0) ? '' : " LIMIT $deb_aff, $nb_aff");

	$res = spip_query(
	"SELECT dl.$champ_donnee_liee
	FROM $table_liens AS dl
	JOIN spip_forms_donnees AS d ON d.id_donnee=dl.$champ_donnee_liee
	WHERE $in_type_table dl.$champ_donnee_source="._q($iid)."
	ORDER BY d.id_form $limit");
	while ($row = spip_fetch_array($res)){
		list($id_form,$titreform,$type_form,$t) = forms_liste_decrit_donnee($row[$champ_donnee_liee],true,$linkable);
		if (!count($t))
			list($id_form,$titreform,$type_form,$t) = forms_liste_decrit_donnee($row[$champ_donnee_liee], false,$linkable);
		if (count($t)){
			$liste[$id_form][$row[$champ_donnee_liee]]=$t;
			$forms[$id_form] = $titreform;
			$types[$id_form] = $type_form;
		}
	}
	foreach($types as $type_form)
		$prefixi18n[$type_form] = forms_prefixi18n($type_form);
	if ($lieeliante=='liee')
		$type_autoriser = strncmp($type_source,'donnee',6)==0?'donnee':$type_source;
	else
		$type_autoriser = 'donnee';

	if (count($liste) OR $tranches) {
		$out .= "<div class='liste liste-donnees'>";
		$out .= $tranches;
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		foreach($liste as $id_form=>$donnees){
			$vals = array();
			$vals[] = "";
			$vals[] = "<a href='".generer_url_ecrire("donnees_tous","id_form=$id_form&retour=".urlencode($retour))."'>".$forms[$id_form]."</a>";
			$vals[] = "";
			$table[] = $vals;
			foreach($donnees as $id_donnee=>$champs){
				$vals = array();
				$vals[] = $id_donnee;
				if ($lieeliante=='liee')
					$auth_modifier = autoriser('modifier',$type_autoriser,$iid,NULL,array('id_donnee_liee'=>$id_donnee));
				else
					$auth_modifier = autoriser('modifier',$type_autoriser,$id_donnee,NULL,array('id_form'=>$id_form,'id_donnee_liee'=>$iid));
				$vals[] =
				  ($auth_modifier?"<a href='".generer_url_ecrire("donnees_edit","id_form=$id_form&id_donnee=$id_donnee&retour=".urlencode($retour))."'>":"")
				  .implode(", ",$champs)
				  .($auth_modifier?"</a>":"");
				$redirect = ancre_url((_DIR_RESTREINT?"":_DIR_RESTREINT_ABS).self(),'tables');
				$action = "";
				if ($lieeliante=='liee'){
					if (autoriser("delier_donnee",$type_autoriser,$iid,NULL,array('id_donnee_liee'=>$id_donnee)))
						$action = generer_action_auteur("forms_lier_donnees","$id,$type_source,retirer,$id_donnee",urlencode($redirect));
				}
				else
					if (autoriser("delier_donnee",$type_autoriser,$id_donnee,NULL,array('id_form'=>$id_form,'id_donnee_liee'=>$iid)))
						$action = generer_action_auteur("forms_lier_donnees","$id_donnee,$type_lie,retirer,$id",urlencode($redirect));
				if ($action){
					$action = ancre_url($action,$bloc_id);
					$redirajax = generer_url_ecrire($script,$arg_ajax);
					$vals[] = "<a href='$action' rel='$redirajax' class='ajaxAction' >"
						. _T($prefixi18n[$types[$id_form]].":lien_retirer_donnee_$lieeliante")."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'")
						. "</a>";
				}
				else $vals[] = "";
				$table[] = $vals;
			}
		}
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles, false);

		$out .= "</table></div>\n";
	}
	return array($out,$les_donnees,$nombre_donnees) ;
}


	function forms_extraire_reponse($id_donnee){
		// Lire les valeurs entrees
		if (substr(spip_mysql_version(), 0, 1) == 3) {
			$result = spip_query("SELECT * FROM spip_forms_donnees_champs  AS r, spip_forms_champs AS ch, spip_forms_donnees AS d
			WHERE ch.champ=r.champ AND d.id_donnee = r.id_donnee AND d.id_form = ch.id_form AND r.id_donnee=".intval($id_donnee)." ORDER BY ch.rang");
		}
		else {
			$result = spip_query("SELECT * FROM spip_forms_donnees_champs AS r
				JOIN spip_forms_champs AS ch ON ch.champ=r.champ
				JOIN spip_forms_donnees AS d ON d.id_donnee = r.id_donnee
				WHERE d.id_form = ch.id_form AND r.id_donnee=".intval($id_donnee)." ORDER BY ch.rang");
		}
		$valeurs = array();
		$retour = urlencode(self());
		$libelles = array();
		$values = array();
		$url = array();
		while ($row = spip_fetch_array($result)) {
			$rang = $row['rang'];
			$champ = $row['champ'];
			$libelles[$champ]=$row['titre'];
			$type = $row['type'];
			if ($type == 'fichier') {
				$values[$champ][] = $row['valeur'];
				$url[$champ][] = generer_url_ecrire("forms_telecharger","id_donnee=$id_donnee&champ=$champ&retour=$retour");
			}
			else if (in_array($type,array('select','multiple'))) {
				if ($row3=spip_fetch_array(spip_query("SELECT titre FROM spip_forms_champs_choix WHERE id_form="._q($row['id_form'])." AND champ="._q($champ)." AND choix="._q($row['valeur']))))
					$values[$champ][]=$row3['titre'];
				else
					$values[$champ][]= $row['valeur'];
				$url[$champ][] = '';
			}
			else if ($type == 'mot') {
				$id_groupe = intval($row['extra_info']);
				$id_mot = intval($row['valeur']);
				if ($row3 = spip_fetch_array(spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe="._q($id_groupe)." AND id_mot="._q($id_mot)))){
					$values[$champ][]=$row3['titre'];
					$url[$champ][]= generer_url_ecrire("mots_edit","id_mot=$id_mot");
				}
				else {
					$values[$champ][]= $row['valeur'];
					$url[$champ][] = '';
				}
			}
			else {
				$values[$champ][] = $row['valeur'];
				$url[$champ][] = '';
			}
		}
		return array($libelles,$values,$url);
	}
	
	//
	// Afficher un pave formulaires dans la colonne de gauche
	// (edition des articles)

	function forms_afficher_insertion_formulaire($id_article) {
		global $connect_id_auteur, $connect_statut;
		global $couleur_foncee, $couleur_claire, $options;
		global $spip_lang_left, $spip_lang_right;

		$s = "";
		// Ajouter un formulaire

		$out = "";
		$out .= "<div class='verdana2'>";
		$out .= _T("forms:article_inserer_un_formulaire_detail");
		$out .= "</div>";

		$query = "SELECT id_form, titre FROM spip_forms ORDER BY titre";
		$result = spip_query($query);
		if (spip_num_rows($result)) {
			$out .= "<br />\n";
			$out .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$out .= "<div class='plan-articles'>";
			while ($row = spip_fetch_array($result)) {
				$id_form = $row['id_form'];
				$titre = typo($row['titre']);

				$link = generer_url_ecrire('forms_edit',"id_form=$id_form&retour=".urlencode(self()));
				$out .= "<a href='".$link."'>";
				$out .= $titre."</a>\n";
				$out .= "<div class='arial1' style='text-align:$spip_lang_right;color: black; padding-$spip_lang_left: 4px;' "."title=\""._T("forms:article_recopier_raccourci")."\">";
				$out .= "<strong>&lt;form".$id_form."&gt;</strong>";
				$out .= "</div>";
			}
			$out .= "</div>";
			$out .= "</div>";
		}

		// Creer un formulaire
		include_spip('inc/autoriser');
		if (autoriser('creer','form')) {
			$out .= "\n<br />";
			$link = generer_url_ecrire('forms_edit',"new=oui&retour=".urlencode(self()));
			$out .= icone_horizontale(_T("forms:icone_creer_formulaire"),
				$link, "../"._DIR_PLUGIN_FORMS."img_pack/form-24.png", "creer.gif", false);
		}

		$s .= cadre_depliable(_DIR_PLUGIN_FORMS."img_pack/form-24.png",_T("forms:article_inserer_un_formulaire"),true,$out,"ajouter_form");

		return $s;
	}
?>