<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

/**
 * fonction hack pour inserer les crayons sur un squelette morceau de page
 *
 * @param string $out
 * @return string
 */
function forms_inserer_crayons($out){
	$out = pipeline('affichage_final', "</head>".$out);
	$out = str_replace("</head>","",$out);
	return $out;
}


// Crayons sur les donnes
function forms_donnee_valeur_colonne_table($table,$champs,$id_donnee){
	include_spip("inc/forms");

	$vals = array();
	foreach($champs as $champ){
		$valeur = forms_valeurs($id_donnee,NULL,$champ);
		if (!count($valeur))
			$valeur = array($champ => '');
		$vals = array_merge($vals,$valeur);
	}
	return $vals;
}
function forms_donnee_revision($id_donnee,$c=NULL){
	include_spip('action/forms_editer_donnee');
	return forms_revision_donnee($id_donnee,$c);
}
function forms_champ_valeur_colonne_table($table,$champ,$id){
	$id = explode('-',$id);
	$id_form = $id[0];
	$form_champ = $id[1];
	
	if (!preg_match(',^\w+$,',$champ)
	 OR !$row = sql_fetsel($champ,"spip_forms_champs","id_form=".intval($id_form)." AND ".sql_in("champ",$form_champ)))
		return false;

	return $row;
}

// Crayons sur les champs
function forms_champ_revision($id,$c=NULL){
	$id = explode('-',$id);
	$id_form = $id[0];
	$form_champ = $id[1];

	$set = array();
	foreach(array('titre','obligatoire','specifiant','public','aide','html_wrap') as $champ){
		if ($v = _request($champ,$c)){
			$set[$champ]=$v;
		}
	}

	if (count($set))
		sql_updateq("spip_forms_champs",$set,"id_form=".intval($id_form)." AND champ=".sql_quote($form_champ));
	return true;
}

// Crayons sur le form
function form_revision($id,$c=NULL){
	$id = explode('-',$id);
	$id_form = $id[0];

	$set = array();
	foreach(array('titre','descriptif','texte','html_wrap') as $champ){
		if ($v = _request($champ,$c)){
			$set[$champ]=$v;
		}
	}

	if (count($set))
		sql_updateq("spip_forms",$set,"id_form=".intval($id_form));
	return true;
}

//
// VUE
//
function vues_forms_donnee($type, $champ, $id_donnee, $content){
	if( !$row = sql_fetsel("d.id_form,f.type_form","spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form","d.id_donnee=".intval($id_donnee)))
		return "";
	$type_form = $row['type_form'];
	$id_form = $row['id_form'];

  // chercher vues/article_toto.html
  // sinon vues/toto.html
  if (find_in_path( ($fond = 'vues/' . $type_form . '_donnee_' . $champ) . '.html')
  OR find_in_path( ($fond = 'vues/forms_donnee_' . $champ) .'.html')
  OR find_in_path( ($fond = 'vues/' . $type_form . '_donnee') .'.html')
  OR $fond = 'vues/forms_donnee') {
		$contexte = array(
		    'id_form' => $id_form,
		    'id_donnee' => $id_donnee,
		    'champ' => $champ,
		    'lang' => $GLOBALS['spip_lang']
		);
		$contexte = array_merge($contexte, $content);
		return recuperer_fond($fond, $contexte);
  }
}
?>
