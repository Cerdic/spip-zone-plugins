<?php


if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('balise/formulaire_inscription');
include_spip('inc/profil_etendu');

function balise_FORMULAIRE_PROFIL_ETENDU ($p) {
  return calculer_balise_dynamique($p,'FORMULAIRE_PROFIL_ETENDU',array());
}

function balise_FORMULAIRE_PROFIL_ETENDU_stat($args, $filtres) {
/*	$mode = calcul_mode_inscription();
	if (!test_mode_inscription($mode))
		return '';
	else 
	return array($mode,$args[0],$args[1]);
*/	return array($args[0],$args[1]);
}
//function balise_FORMULAIRE_PROFIL_ETENDU_dyn($mode,$type_profil='',$lien='') {
function balise_FORMULAIRE_PROFIL_ETENDU_dyn($type_profil='',$lien='') {
	include_spip("inc/texte");
	include_spip("inc/filtres");
	if ($type_profil=='') $type_profil='profil_etendu';
	if (($GLOBALS['auteur_session']['statut']!="1comite")
	  &&($GLOBALS['auteur_session']['statut']!="0minirezo")
	  &&($GLOBALS['auteur_session']['statut']!="6forum")){
	  		// Si une inscription est autorisee, on enregistre le demandeur
		// comme 'nouveau' et on lui envoie ses codes par email ; lors de
		// sa premiere connexion il obtiendra son statut final (auth->activer())
		//if (!test_mode_inscription($mode)) return _T('pass_rien_a_faire_ici');
	
		// recuperer les donnees envoyees
		$mail_inscription = trim(_request('mail_inscription'));
		$nom_inscription = _request('nom_inscription');
	
		if (!$nom_inscription) 
			$message = '';
		elseif (!email_valide($mail_inscription))
			$message = _T('info_email_invalide').$mail_inscription;
		else {
			$nom = _request('nom_inscription');
			$mail = _request('mail_inscription');
			$commentaire = _T('pass_forum_bla');
			$commentaire = message_inscription_profil($type_profil,$mail_inscription,
						       $nom_inscription,
						       false,
						       'form_forum_voici1');
			if (is_array($commentaire)) {
				if (function_exists('envoyer_inscription'))
					$f = 'envoyer_inscription';
				else 
					$f = 'envoyer_inscription_dist';
				$commentaire = $f($commentaire, $nom, 'forum',0);
				$message = $commentaire ? $commentaire : _T('form_forum_identifiant_mail');
			}
			else
				$message = $commentaire ? $commentaire : _T('form_forum_identifiant_mail');
			
		}

		if ($message==_T('form_forum_identifiant_mail')) return $message;
		else $message.="<br/>";
		$etendu_form=etendu_form(array(),$type_profil,'');
		return array("formulaires/profil_inscription", 0,
				array('focus' => 'nom_inscription',
					'target' => _request('target'),
					'message' => $message,
					'mode' => 'forum',
					'form' => $etendu_form[0],
					'self' => ($lien ? $lien : self()),//generer_url_public('profil')),
					));
	}
	else {
		$message='';
		$fields=creer_profil($type_profil);
		if (_request("update_".$type_profil)){
			$fields=etendu_recup_saisie($type_profil);
			$message=enregistrer_profil($type_profil,$fields);
		}

		$etendu_form=etendu_form($fields,$type_profil,'');
		$match_form=implode('|',$etendu_form[1]);
		if ($match_form=='') $match_form='(defaut)';
		else $match_form='^('.$match_form.')$';
		//return $match_form;
		return array("formulaires/profil_etendu", 0,
					array('form' => $etendu_form[0],
						'match_forms' => $match_form,
//						'extra' => $r['extra'],
						'type' => $type_profil,
						'legend' => _T("forms:".$type_profil),
						'message' => $message,
					'self' => ($lien ? $lien : self()),//generer_url_public('profil')),
					));
		
	}
	
}

function creer_profil($type_profil,$id=0){
	if ($id==0) $id=$GLOBALS['auteur_session']['id_auteur'];
	$champs = array_keys(etendu_champs($type_profil));
	//array_keys($GLOBALS['champs_etendus'][$type_profil]);
	$qs="select ".join($champs,', ')." from spip_".$type_profil." where id_auteur=".$id;
	$result=spip_query($qs);
	if (!($fields=spip_fetch_array($result))){
		$result=spip_query("INSERT INTO spip_".$type_profil."(id_auteur,maj) values(".$id.",now())");
	}
	return $fields;
}

function enregistrer_profil($type_profil,$fields,$id=0){
	if ($id==0)$id=$GLOBALS['auteur_session']['id_auteur'];
	$set='UPDATE spip_'.$type_profil." SET ";
	foreach ($fields as $key => $value)
		$set.=$key."='".addslashes($value)."', ";
	/*include_ecrire("inc_extra.php3");
	$extra = extra_recup_saisie("auteurs");
	$add_extra = ", extra = '".addslashes($extra)."'";
	$query="update spip_auteurs set $set$add_extra, maj=now() where id_auteur=".$id;
	*/
	$set.="maj=now() where id_auteur=".$id;
	if ($result=spip_query($set)) $message="Modifications sauvegard&eacute;es";
	else $message="Erreur &agrave; l'enregistrement";

	
}
function message_inscription_profil($type_profil,$mail, $nom, $mode, $id=0) {

	$inscrip=message_inscription($mail, $nom, $mode, $id);
	if (is_array($inscrip)){
		creer_profil($type_profil,$inscrip['id_auteur']);
		$fields=etendu_recup_saisie($type_profil);
		$message.=enregistrer_profil($type_profil,$fields,$inscrip['id_auteur']);
	}
	return $inscrip;
}

// recupere les valeurs postees pour reconstituer l'extra
function etendu_recup_saisie($type_profil) {
	$champs = $GLOBALS['champs_etendus'][$type_profil];
	if (is_array($champs)) {
		$extra = array();
		foreach ($champs as $champ => $param) {
			list($style, $filtre, , $choix,) = explode("|", $param);
			list(, $filtre) = explode(",", $filtre);
//			spip_log("etendu_recup_saisie:".$champ."=>"._request("etendu_".$champ));
			switch ($style) {
			case "multiple":
				$choix =  explode(",", $choix);
				$extra[$champ] = array();
				for ($i=0; $i < count($choix); $i++) {
					if ($filtre && function_exists($filtre))
						 $extra[$champ."_multi_".$i] = $filtre(_request("etendu_".$champ.$i));
					else
						$extra[$champ."_multi_".$i] = _request("etendu_".$champ.$i);
				}
				break;

			case 'case':
			case 'checkbox':
				if (_request("etendu_$champ") == 'on')
					$extra[$champ] = 'oui';
				else
					$extra[$champ] = 'non';
				break;
			default:
				if ($filtre && function_exists($filtre))
				$extra[$champ]=$filtre(_request("etendu_".$champ));
				else $extra[$champ]=_request("etendu_".$champ);
				break;
			}
		}
		return $extra;
	} else
		return '';
}
// a partir de la liste des champs, generer la liste des input
function etendu_form($extra, $type_profil, $ensemble='') {
$titre_form=array();
	// quels sont les extras de ce type d'objet
	if (!$champs = $GLOBALS['champs_etendus'][$type_profil])
		$champs = array();


	// quels sont les extras proposes...
	// ... si l'ensemble est connu
	if ($ensemble && isset($GLOBALS['champs_etendus_proposes'][$type_profil][$ensemble]))
		$champs_proposes = explode('|', $GLOBALS['champs_etendus_proposes'][$type_profil][$ensemble]);
	// ... sinon, les champs proposes par defaut
	else if (isset($GLOBALS['champs_etendus_proposes'][$type_profil]['tous'])) {
		$champs_proposes = explode('|', $GLOBALS['champs_etendus_proposes'][$type_profil]['tous']);
	}

	// sinon tous les champs extra du type
	else {
		$champs_proposes =  array();
		reset($champs);
		while (list($ch, ) = each($champs)) $champs_proposes[] = $ch;
	}

	// bug explode
	if($champs_proposes == explode('|', '')) $champs_proposes = array();

	// maintenant, on affiche les formulaires pour les champs renseignes dans $extra
	// et pour les champs proposes
	reset($champs_proposes);
	while (list(, $champ) = each($champs_proposes)) {
		$desc = $champs[$champ];
		list($form, $filtre, $prettyname, $choix, $valeurs) = explode("|", $desc);

		if (!$prettyname) $prettyname = ucfirst($champ);
		$prettyname=$filtre($prettyname);
//		$label = "<label for=''>$prettyname&nbsp;:</label>";

		switch($form) {

			case "case":
			case "checkbox":
				$affiche .= "<label for='etendu_$champ' class='checkbox'>$prettyname&nbsp;<input type='checkbox' class='truc' name='etendu_$champ' id='etendu_$champ'";
				if ($extra[$champ] == 'oui')
					$affiche .= " CHECKED ";
				$affiche .= " value='on'/>";
				$affiche .= "</label>";
				break;
			case "list":
			case "liste":
			case "select":
				$affiche .= "<label for='etendu_$champ'>$prettyname</label>";
				$choix = explode(",",$choix);
				if (!is_array($choix)) {
					$affiche .= "Pas de choix d&eacute;finis.\n";
					break;
				}

				// prendre en compte les valeurs des champs
				// si elles sont renseignees
				$valeurs = explode(",",$valeurs);
				if($valeurs == explode(",",""))
					$valeurs = $choix ;

				$affiche .= "<select name='etendu_$champ' id='etendu_$champ' >\n";
				$i = 0 ;
				while (list(, $choix_) = each($choix)) {
					$val = $valeurs[$i] ;
					$affiche .= "<option value=\"$val\"";
					if ($val == entites_html($extra[$champ]))
						$affiche .= " SELECTED";
					$affiche .= ">".$filtre($choix_)."</option>\n";
					$i++;
				}
				$affiche .= "</select>";
				break;

			case "radio":
				$affiche .= $prettyname;
				$choix = explode(",",$choix);
				if (!is_array($choix)) {
					$affiche .= "Pas de choix d&eacute;finis.\n";
					break;
				}
				$valeurs = explode(",",$valeurs);
				if($valeurs == explode(",",""))
					$valeurs = $choix ;

				$i=0;
				while (list(, $choix_) = each($choix)) {
					$affiche .= "<input type='radio' class='radio' name='etendu_$champ' id='etendu_$champ_$i' ";
					$val = $valeurs[$i] ;
					if (entites_html($extra["$champ"])== $val)
						$affiche .= " CHECKED";

					// premiere valeur par defaut
					if (!$extra["$champ"] AND $i == 0)
						$affiche .= " CHECKED";

					$affiche .= " value='$val'/><label for='etendu_$champ_$i' class='radio'>".$filtre($choix_)."</label>\n";
					$i++;
				}
				break;

			case "radio_form":
				$affiche .= $prettyname;
				$choix = explode(",",$choix);
				if (!is_array($choix)) {
					$affiche .= "Pas de choix d&eacute;finis.\n";
					break;
				}
				$valeurs = explode(",",$valeurs);
				if($valeurs == explode(",",""))
					$valeurs = $choix ;

				$i=0;
				while (list(, $choix_) = each($choix)) {
					$affiche .= "<input type='radio' class='radio' name='etendu_$champ' id='etendu_$champ_$i' ";
					$val = $valeurs[$i] ;
					if (entites_html($extra["$champ"])== $val){
						$affiche .= " CHECKED";
						$titre_form[]=$val;
					}

					// premiere valeur par defaut
					if (!$extra["$champ"] AND $i == 0)
						$affiche .= " CHECKED";

					$affiche .= " value='$val'/><label for='etendu_$champ_$i' class='radio'>".$filtre($choix_)."</label>\n";
					$i++;
				}
				break;
			case "hidden_form":
				$affiche .= "<input type='hidden' name='etendu_$champ'\n";
				$affiche .= " value=\"".$prettyname."\">\n";
				$titre_form[]=$prettyname;
				break;

			// A refaire car on a pas besoin de renvoyer comme pour checkbox
			// les cases non cochees
			case "multiple":
				$affiche .= "<label for=''>$prettyname</label>";
				$choix = explode(",",$choix);
				if (!is_array($choix)) {
					$affiche .= "Pas de choix d&eacute;finis.\n";
					break; }
				for ($i=0; $i < count($choix); $i++) {
					$affiche .= "<label class='checkbox' for='etendu_$champ$i'>";
					$affiche .= "<input type='checkbox' name='etendu_$champ$i' id='etendu_$champ$i'";
					if (entites_html($extra[$champ."_multi_".$i])=="on")
						$affiche .= " CHECKED";
					$affiche .= "/>";
					$affiche .= $filtre($choix[$i]);
					$affiche .= "</label>\n";
				}
				break;

			case "bloc":
			case "block":
				$affiche .= "<label for='etendu_$champ'>$prettyname</label>";
				$affiche .= "<textarea name='etendu_$champ' id='etendu_$champ' class='formo' rows='5' cols='40'>".entites_html($extra[$champ])."</textarea>\n";
				break;

			case "masque":
				$affiche .= "<label for='etendu_$champ'>$prettyname</label>";
				$affiche .= "<span style='color:#555'>".interdire_scripts($extra[$champ])."</span>\n";
				break;

			case "ligne":
			case "line":
			default:
				$affiche .= "<label for='etendu_$champ'>$prettyname</label>";
				$affiche .= "<INPUT TYPE='text' NAME='etendu_$champ' CLASS='formo'\n";
				$affiche .= " VALUE=\"".entites_html($extra[$champ])."\" SIZE='40'>\n";
				break;
		}

//		$affiche .= "\n";
//		$affiche .= "<p>\n";
	}
	return array($affiche,$titre_form);

}

?>