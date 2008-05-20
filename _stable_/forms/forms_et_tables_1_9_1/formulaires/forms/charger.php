<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2008 - Distribue sous licence GNU/GPL
 *
 */

function formulaires_forms_charger_dist($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires=""){
	$valeurs = array();
	if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');
	
	$res = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($res)) 
		// pas de saisie, formulaire inconnu !
		return array(false,array());
	else {
		if ($forms_obligatoires!='' && $row['forms_obligatoires']!='') $forms_obligatoires .= ",";
		$forms_obligatoires .= $row['forms_obligatoires'];
		// substituer le formulaire obligatoire pas rempli si necessaire
		if (strlen($forms_obligatoires)){
			$row=Forms_obligatoire($row,$forms_obligatoires);
			$id_form=$row['id_form'];
		}
		$type_form = $row['type_form'];
	}

	include_spip('inc/forms');
	$structure = Forms_structure($id_form,false);
	foreach(array_keys($structure) as $champ)
		$valeurs[$champ] = '';
	
	$id_donnee = $id_donnee?$id_donnee:intval(_request('id_donnee'));
	
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

	if (!_DIR_RESTREINT && $id_donnee)
		$valeurs = array_merge($valeurs,Forms_valeurs($id_donnee,$id_form));
	elseif (_DIR_RESTREINT!="" 
	&& ( ($row['modifiable']=='oui') || ($row['multiple']=='non') )
	){
		global $auteur_session;
		$id_auteur = $auteur_session['id_auteur'] ? intval($auteur_session['id_auteur']) : 0;
		include_spip('inc/forms');
		$cookie = $_COOKIE[Forms_nom_cookie_form($id_form)];
		//On retourne les donnees si auteur ou cookie
		$q = "SELECT donnees.id_donnee " .
			"FROM spip_forms_donnees AS donnees " .
			"WHERE donnees.id_form="._q($id_form)." ".
			"AND donnees.statut='publie' AND (";
		if ($cookie) { 
			$q.="cookie="._q($cookie). ($id_auteur?" OR id_auteur="._q($id_auteur):" AND id_auteur=0");
		}
		else if ($id_auteur)
				$q.="id_auteur="._q($id_auteur);
			else
				$q.="0=1";
		$q .= ") ";
		//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		if ($row['multiple']=='oui') 
		  $q.="AND donnees.id_donnee="._q($id_donnee);
		$res = spip_query($q);
		if($row2 = spip_fetch_array($res)){
			if (($row['multiple']=='non') && ($row['modifiable']=='non')) return "";
			$id_donnee=$row2['id_donnee'];
			$valeurs = array_merge($valeurs,Forms_valeurs($id_donnee,$id_form));
		}
	}

	if ($row['type_form'] == 'sondage'){
		include_spip('inc/forms');
		if ((Forms_verif_cookie_sondage_utilise($id_form)==true)&&(_DIR_RESTREINT!=""))
			$valeurs['affiche_sondage']=' ';
	}
	
	$valeurs['id_article'] = $id_article;
	$valeurs['id_form'] = $id_form;
	$valeurs['id_donnee'] = $id_donnee?$id_donnee:(0-$GLOBALS['auteur_session']['id_auteur']); # GROS Hack pour les jointures a la creation;
	$valeurs['class'] = 'formulaires/'.($class?$class:'forms_structure');

	$valeurs['_hidden'] = 
		// est-ce encore utile ?
		"<input type='hidden' name='id_donnee' value='$id_donnee' />"
		// pour la compat, ne sert plus !
	  . "<input type='hidden' name='ajout_reponse' value='$id_form' />";

	return $valeurs;
}

?>