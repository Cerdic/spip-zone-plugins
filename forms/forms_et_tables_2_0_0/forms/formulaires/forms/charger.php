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

function formulaires_forms_charger_dist($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires="",$retour=""){
	$valeurs = array();

	include_spip('inc/autoriser');
	include_spip('base/abstract_sql');
	
	if (!$row = sql_fetsel("*","spip_forms","id_form=".intval($id_form)))
		// pas de saisie, formulaire inconnu !
		return array(false,array());
	else {
		if ($forms_obligatoires!='' && $row['forms_obligatoires']!='') $forms_obligatoires .= ",";
		$forms_obligatoires .= $row['forms_obligatoires'];
		// substituer le formulaire obligatoire pas rempli si necessaire
		if (strlen($forms_obligatoires)){
			$row=forms_obligatoire($row,$forms_obligatoires);
			$id_form=$row['id_form'];
		}
		$type_form = $row['type_form'];
	}

	include_spip('inc/forms');
	$structure = forms_structure($id_form,false);
	foreach(array_keys($structure) as $champ)
		$valeurs[$champ] = '';

	$id_donnee = $id_donnee?$id_donnee:intval(_request('id_donnee'));

	
	/* doublonne maintenant avec le pipeline formulaires_charger */
	$valeurs = pipeline('forms_pre_remplit_formulaire',array('args'=>array('id_form'=>$id_form,'id_donne'=>$id_donnee),'data'=>$valeurs));
	
	$valeurs['formvisible'] = true;
	$valeurs['affiche_sondage'] = '';
	if (
	  (
		   (_DIR_RESTREINT==_DIR_RESTREINT_ABS )
		  OR in_array(_request('exec'),$GLOBALS['forms_actif_exec'])
		)
		AND 
		(!($id_donnee>0)
		  OR autoriser('modifier','donnee',$id_donnee,NULL,array('id_form'=>$id_form))
		))
		$valeurs['formactif'] = ' ';
	else
		$valeurs['formactif'] = '';

	$valeurs['id_article'] = $id_article;
	$valeurs['id_form'] = $id_form;
	$valeurs['id_donnee'] = $id_donnee;
	if (!$valeurs['id_donnee'] AND isset($GLOBALS['visiteur_session']['id_auteur']))
		$valeurs['id_donnee'] = (0-$GLOBALS['visiteur_session']['id_auteur']); # GROS Hack pour les jointures a la creation;
	$valeurs['class'] = 'formulaires/'.($class?$class:'forms_structure');

	$valeurs['_hidden'] = 
		// est-ce encore utile ?
		"<input type='hidden' name='id_donnee' value='$id_donnee' />"
		// pour la compat, ne sert plus !
	  . "<input type='hidden' name='ajout_reponse' value='$id_form' />";

	if (test_espace_prive() AND $id_donnee)
		$valeurs = array_merge($valeurs,forms_valeurs($id_donnee,$id_form));
	elseif (_DIR_RESTREINT!="" 
	&& ( ($row['modifiable']=='oui') || ($row['multiple']=='non') )
	){
		global $auteur_session;
		$id_auteur = $auteur_session['id_auteur'] ? intval($auteur_session['id_auteur']) : 0;
		include_spip('inc/forms');
		$cookie = $_COOKIE[forms_nom_cookie_form($id_form)];
		//On retourne les donnees si auteur ou cookie
		$where_cookie="";
		if ($cookie) { 
			$where_cookie.="cookie=".sql_quote($cookie). ($id_auteur?" OR id_auteur=".intval($id_auteur):" AND id_auteur=0");
		}
		else if ($id_auteur)
				$where_cookie.="id_auteur=".intval($id_auteur);
			else
				$where_cookie.="0=1";
		$q .= ") ";
		if($row2 = sql_fetsel("donnees.id_donnee","spip_forms_donnees AS donnees","donnees.id_form=".intval($id_form)." AND donnees.statut='publie' "
		  ."AND ($where_cookie)"
			//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		  . ($row['multiple']=='oui'?" AND donnees.id_donnee=".intval($id_donnee):""))
		  ){
			if (($row['multiple']=='non') && ($row['modifiable']=='non')) return array(false,$valeurs);
			$id_donnee=$row2['id_donnee'];
			$valeurs = array_merge($valeurs,forms_valeurs($id_donnee,$id_form));
		}
	}

	if ($row['type_form'] == 'sondage'){
		include_spip('inc/forms');
		if ((forms_verif_cookie_sondage_utilise($id_form)==true)&&(_DIR_RESTREINT!=""))
			$valeurs['affiche_sondage']=' ';
	}
	
	return $valeurs;
}



function forms_obligatoire($row,$forms_obligatoires){
	include_spip('inc/forms');
	$returned=$row;
	$id_auteur = isset($GLOBALS['visiteur_session']['id_auteur']) ? intval($GLOBALS['visiteur_session']['id_auteur']) : 0;
	$form_tab=explode(',',$forms_obligatoires);
	$chercher=true;
	$i=0;
	while ($chercher && $i<count($form_tab)){
		$form_id=$form_tab[$i];
		$cookie = $_COOKIE[forms_nom_cookie_form($form_id)];
		$where_cookie = "";
		if ($cookie)
			$where_cookie="(cookie=".sql_quote($cookie)." OR id_auteur=".intval($id_auteur).")";
		else
			if ($id_auteur)
				$where_cookie="id_auteur=".intval($id_auteur);
			else
				$where_cookie="0=1";
		if (!sql_countsel("spip_forms_donnees","statut='publie' AND id_form=".intval($form_id)." AND $where_cookie")){
			$returned = sql_fetsel("*","spip_forms","id_form=".intval($form_id));
			$chercher=false;
		}
		$i++;
	}
	return $returned;
}
?>