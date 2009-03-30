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

function formulaires_forms_traiter_dist($id_form = 0, $id_article = 0, $id_donnee = 0, $id_donnee_liee = 0, $class='', $script_validation = 'valide_form', $message_confirm='forms:avis_message_confirmation',$reponse_enregistree="forms:reponse_enregistree",$forms_obligatoires="",$retour=""){
	include_spip('inc/autoriser');
	$resultat = array();
	
	$row = sql_fetsel("*","spip_forms","id_form=".intval($id_form));

	if (  $row['type_form'] == 'sondage'
	  OR  $row['modifiable']=='oui'
	  OR  $row['multiple']=='non' ){
		forms_poser_cookie_sondage($id_form);
	}
	
	include_spip('action/forms_editer_donnee');
	$url_validation = forms_enregistrer_reponse_formulaire($id_form, $id_donnee, $erreur, $reponse, $script_validation, $id_article?"id_article=$id_article":"");
	if (!$erreur) {
		$formok = _T($reponse_enregistree)."<span class='id_donnee' rel='$id_donnee'></span>";
		if ($id_donnee_liee && $id_donnee){
			sql_insertq("spip_forms_donnees_donnees",array("id_donnee"=>$id_donnee,"id_donnee_liee"=>$id_donnee_liee));
		}
		if ($reponse)
		  $formok .= "<span class='spip_form_ok_confirmation'>"
		    . _T($message_confirm,array('mail'=>$reponse))
		    . "</span>";
		$message_complementaire = pipeline('forms_message_complement_post_saisie',array('args'=>array('id_donnee'=>$id_donnee),'data'=>''));
		if ((!_DIR_RESTREINT OR $row['modifiable']=='oui')
		  AND (
		    ($r=_request('id_donnee'))===NULL 
		    OR intval($r)==0 // id_donnee=new dans l'url par exemple
		    OR $r==$id_donnee // modif d'une donnee
		    OR ($r<0 AND (_DIR_RESTREINT OR !in_array(_request('exec'),$GLOBALS['forms_saisie_km_exec'])))
		  ) ) {
			// reinjecter id_donnee
			set_request('id_donnee',$id_donnee);
			$resultat['editable'] = true;
		}
		else {
			$resultat['editable'] = false;
		}
		if ($message_complementaire)
			$formok .= $message_complementaire;
		if ($url_validation)
			$formok .= "<img src='$url_validation' width='1' height='1' alt='validation de la saisie' />";
		$resultat['message_ok'] = $formok;

		if (!$url_validation AND $retour)
			$resultat['redirect'] = $retour;
	}
	else {
		$resultat['message_erreur'] = join(' ',$erreur);
		$resultat['editable'] = true;
	}

	return $resultat;
}


// le reglage du cookie doit se faire avant l'envoi de tout HTML au client
function forms_poser_cookie_sondage($id_form) {
	if ($id_form = intval($id_form)) {
		$nom_cookie = $GLOBALS['cookie_prefix'].'cookie_form_'.$id_form;
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!($cookie = $_COOKIE[$nom_cookie])) {
			include_spip("inc/acces");
			$cookie = creer_uniqid();
		}
		// pour utilisation dans inc_forms...
		// on utilise directement $_COOKIE
		//$GLOBALS['cookie_form'] = $cookie; 
		include_spip("inc/cookie");
		// Expiration dans 30 jours
		spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time() + 30 * 24 * 3600);
	}
}

?>