<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * ??? 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_FORMS_collecte;
$balise_FORMS_collecte = array('id_form','id_article','id_donnee', 'id_donnee_liee');

function balise_FORMS ($p) {
	return calculer_balise_dynamique($p,'FORMS', array('id_form', 'id_article', 'id_donnee','id_donnee_liee', 'class'));
}

function balise_FORMS_stat($args, $filtres) {
	return $args;
}

function balise_FORMS_dyn($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires="") {
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	$url = self();
	// nettoyer l'url qui est passee par htmlentities pour raison de securites
	$url = str_replace("&amp;","&",$url);
	if ($retour=='') $retour = $url;

	$res = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if (!$row = spip_fetch_array($res)) return;
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

	$id_donnee = $id_donnee?$id_donnee:intval(_request('id_donnee'));
	$erreur = array();
	$reponse = '';
	$formok = '';
	$valeurs = array('0'=>'0');
	$affiche_sondage = '';
	$formactif = (
	  (
		   (_DIR_RESTREINT==_DIR_RESTREINT_ABS )
		  OR in_array(_request('exec'),$GLOBALS['forms_actif_exec'])
		)
		AND 
		(!($id_donnee>0)
		  OR autoriser('modifier','donnee',$id_donnee,NULL,array('id_form'=>$id_form))
		));
	$formactif = $formactif?' ':'';

	$flag_reponse = (_request('ajout_reponse') == $id_form) && (_request('nobotnobot')=='');
	if ($flag_reponse) {
		include_spip('inc/forms');
		$url_validation = Forms_enregistrer_reponse_formulaire($id_form, $id_donnee, $erreur, $reponse, $script_validation, $id_article?"id_article=$id_article":"");
		if (!$erreur) {
			$formok = _T($reponse_enregistree);
			if ($id_donnee_liee && $id_donnee){
				spip_query("INSERT INTO spip_forms_donnees_donnees (id_donnee,id_donnee_liee) VALUES ("._q($id_donnee).","._q($id_donnee_liee).")");
			}
			if ($reponse)
			  $reponse = _T($message_confirm,array('mail'=>$reponse));
			if (!_DIR_RESTREINT 
			  AND (($r=_request('id_donnee'))===NULL OR $r==$id_donnee OR ($r<0 AND !in_array(_request('exec'),$GLOBALS['forms_saisie_km_exec']))) )
				$valeurs = Forms_valeurs($id_donnee,$id_form);
			else
				$id_donnee = 0;
		}
		else {
			// on reinjecte get et post dans $valeurs
			foreach($_GET as $key => $val)
				$valeurs[$key] = interdire_scripts($val);
			foreach($_POST as $key => $val)
				$valeurs[$key] = interdire_scripts($val);
		}
	}
	elseif (!_DIR_RESTREINT && $id_donnee=_request('id_donnee'))
		$valeurs = Forms_valeurs($id_donnee,$id_form);
	elseif (_DIR_RESTREINT!="" && $row['modifiable']=='oui'){
		global $auteur_session;
		$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
		$cookie = $_COOKIE[Forms_nom_cookie_form($id_form)];
		//On retourne les donnees si auteur ou cookie
		$q = "SELECT donnees.id_donnee " .
			"FROM spip_forms_donnees AS donnees " .
			"WHERE donnees.id_form="._q($id_form)." ".
			"AND donnees.statut='publie' ";
		if ($cookie) $q.="AND (cookie="._q($cookie)." OR id_auteur="._q($id_auteur).") ";
		else
			if ($id_auteur)
				$q.="AND id_auteur="._q($id_auteur)." ";
			else
				$q.="AND 0=1 ";
		//si unique, ignorer id_donnee, si pas id_donnee, ne renverra rien
		if ($row['multiple']=='oui') $q.="AND donnees.id_donnee="._q($id_donnee);
		$res = spip_query($q);
		if($row2 = spip_fetch_array($res)){
			$id_donnee=$row2['id_donnee'];
			$valeurs = Forms_valeurs($id_donnee,$id_form);
		}
	}

	if ($row['type_form'] == 'sondage'){
		include_spip('inc/forms');
		if ((Forms_verif_cookie_sondage_utilise($id_form)==true)&&(_DIR_RESTREINT!=""))
			$affiche_sondage=' ';
	}
	include_spip('inc/filtres');
	include_spip('inc/forms_lier_donnees');
	return array('formulaires/forms', 0, 
		array(
			'erreur_message'=>isset($erreur['@'])?$erreur['@']:'',
			'erreur'=>serialize($erreur),
			'reponse'=>filtrer_entites($reponse),
			'id_article' => $id_article,
			'id_form' => $id_form,
			'id_donnee' => $id_donnee?$id_donnee:(0-$GLOBALS['auteur_session']['id_auteur']), # GROS Hack pour les jointures a la creation
			'self' => parametre_url($url,'id_donnee',$id_donnee<0?0:$id_donnee),
			'valeurs' => serialize($valeurs),
			'url_validation' => str_replace("&amp;","&",$url_validation),
			'affiche_sondage' => $affiche_sondage,
			'formok' => filtrer_entites($formok),
			'formvisible' => $formok?(_DIR_RESTREINT!=_DIR_RESTREINT_ABS):true,
			'formactif' => $formactif,
			'class' => 'formulaires/'.($class?$class:'forms_structure')
		));
}

?>
