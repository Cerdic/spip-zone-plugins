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
include_spip('inc/forms');
include_spip('inc/forms_edit');
include_spip('inc/forms_type_champs');
include_spip('inc/autoriser');
// TODO : charger la bonne langue !

function Forms_update_edition_champ($id_form,$champ) {
	$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ));
	if ($row = spip_fetch_array($res)){
		$type = $row['type'];
		$extra_info = "";
		if ($type == 'url')
			if ($champ_verif=_request('champ_verif')) $extra_info = $champ_verif;
		if ($type == 'mot') {
			if ($id_groupe = intval(_request('groupe_champ')))
				$extra_info = $id_groupe;
		}
		if ($type == 'fichier') {
			$extra_info = intval(_request('taille_champ'));
		}
		spip_query("UPDATE spip_forms_champs SET extra_info="._q($extra_info)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
		if ($type == 'select' || $type == 'multiple') {
			if (_request('ajout_choix')) {
				$titre = _T("forms:nouveau_choix");
				include_spip('inc/charsets');
				$titre = unicode2charset(html2unicode($titre));
				$choix = Forms_insere_nouveau_choix($id_form,$champ,$titre);
			}
			$res2 = spip_query("SELECT choix FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
			while ($row2 = spip_fetch_array($res2)){
				if ($titre = _request($row2['choix']))
					spip_query("UPDATE spip_forms_champs_choix SET titre="._q($titre)." WHERE id_form="._q($id_form)." AND champ="._q($champ)." AND choix="._q($row2['choix']));
			}
		}
	}
}

function Forms_update($id_form){
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$champconfirm = _request('champconfirm');
	$texte = _request('texte');
	$type_form = _request('type_form');
	$moderation = _request('moderation');
	$public = _request('public');

	$modif_champ = _request('modif_champ');
	$ajout_champ = _request('ajout_champ');
	$nom_champ = _request('nom_champ');
	$champ_obligatoire = _request('champ_obligatoire');
	$champ_public = _request('champ_public');
	$champ_specifiant = _request('champ_specifiant');
	$aide_champ = _request('aide_champ');
	$wrap_champ = _request('wrap_champ');
	$supp_choix = _request('supp_choix');
	$supp_champ = _request('supp_champ');
	
	//
	// Modifications des donnees de base du formulaire
	//
	
	$nouveau_champ = $champ_visible = NULL;
	// creation
	if ($id_form == 'new' && $titre) {
		spip_query("INSERT INTO spip_forms (titre) VALUES ("._q($titre).")");
		$id_form = spip_insert_id();
	}
	// maj
	if (intval($id_form) && $titre) {
		$query = "UPDATE spip_forms SET ".
			"descriptif="._q($descriptif).", ".
			"type_form="._q($type_form).", ".
			"email="._q(serialize($email)).", ".
			"champconfirm="._q($champconfirm).", ".
			"texte="._q($texte).", ".
			"moderation="._q($moderation).", ".
			"public="._q($public)." ".
			"WHERE id_form="._q($id_form);
		$result = spip_query($query);
	}
	// lecture
	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if ($row = spip_fetch_array($result)) {
		$id_form = $row['id_form'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$type_form = $row['type_form'];
		$moderation = $row['moderation'];
		$public = $row['public'];
		$email = unserialize($row['email']);
		$champconfirm = $row['champconfirm'];
		$texte = $row['texte'];
	}
	
	if ($id_form && Forms_form_editable($id_form)) {
		$champ_visible = NULL;
		// Ajout d'un champ
		if (($type = $ajout_champ) && Forms_type_champ_autorise($type)) {
			$titre = _T("forms:nouveau_champ");
			include_spip('inc/charsets');
			$titre = unicode2charset(html2unicode($titre));
			$champ = Forms_insere_nouveau_champ($id_form,$type,$titre);
			$champ_visible = $nouveau_champ = $champ;
		}
		// Modif d'un champ
		if ($champ = $modif_champ) {
			if ($row = spip_fetch_array(spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ)))) {
				if (_request('switch_select_multi')){
					if ($row['type']=='select') $newtype = 'multiple';
					if ($row['type']=='multiple') $newtype = 'select';
					$newchamp = Forms_nouveau_champ($id_form,$newtype);
					spip_query("UPDATE spip_forms_champs SET type="._q($newtype).", champ="._q($newchamp)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
					spip_query("UPDATE spip_forms_champs_choix SET champ="._q($newchamp)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
					$champ = $newchamp;
					$champ_visible = $champ;
				}
				else {
					spip_query("UPDATE spip_forms_champs SET titre="._q($nom_champ).", obligatoire="._q($champ_obligatoire)
						.", specifiant="._q($champ_specifiant).", public="._q($champ_public)
						.", aide="._q($aide_champ).", html_wrap="._q($wrap_champ)." WHERE id_form="._q($id_form)." AND champ="._q($champ));
					Forms_update_edition_champ($id_form, $champ);
					// switch select to multi ou inversement, apres avoir fait les mises a jour
				}
			}
		}
		// Cas particulier : suppression d'un choix
		// hum (id_form,choix) est il unique ?
		if ($choix = $supp_choix){
			if ($row = spip_fetch_array(spip_query("SELECT champ FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND choix="._q($choix)))) {
				spip_query("DELETE FROM spip_forms_champs_choix WHERE choix="._q($choix)." AND id_form="._q($id_form)." AND champ="._q($row['champ']));
			}
		}
		// Suppression d'un champ
		if ($champ = $supp_champ) {
			spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ));
			spip_query("DELETE FROM spip_forms_champs WHERE id_form="._q($id_form)." AND champ="._q($champ));
		}
	}
	return array($id_form,$champ_visible,$nouveau_champ);
}

function action_forms_edit(){
	global $auteur_session;
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = str_replace("&amp;","&",urldecode(_request('redirect')));
	//$redirect = parametre_url($redirect,'var_ajaxcharset',''); // si le redirect sert, pas d'ajax !
	if ($redirect==NULL) $redirect="";
	include_spip("inc/actions");
	if (verifier_action_auteur("forms_edit-$arg",$hash,$id_auteur)==TRUE) {
		$arg=explode("-",$arg);
		$id_form = $arg[0];
		if ((intval($id_form) && autoriser('modifier','form',$id_form))
			|| (($id_form=='new') && (autoriser('creer','form'))) ) {
			list($id_form,$champ_visible,$nouveau_champ) = Forms_update($id_form);
			if ($redirect) $redirect = parametre_url($redirect,"id_form",$id_form);
			if ($redirect && $champ_visible) $redirect = parametre_url($redirect,"champ_visible",$champ_visible,'&');
			if ($redirect && $nouveau_champ) $redirect = parametre_url($redirect,"nouveau_champ",$nouveau_champ,'&');
		}
	}
	redirige_par_entete($redirect);
}

?>