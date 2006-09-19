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
				$commentaire = $f($commentaire, $nom, 'forum');
				$message = $commentaire ? $commentaire : _T('form_forum_identifiant_mail');
			}
			
		}

	
		return array("formulaires/formulaire_profil_inscription", $GLOBALS['delais'],
				array('focus' => 'nom_inscription',
					'target' => _request('target'),
					'message' => $message,
					'mode' => 'forum',
					'form' => etendu_form(array(),$type_profil,''),
					'self' => ($lien ? $lien : generer_url_public('profil')),
					));
	}
	else {
		$message='';
		if (_request("update_".$type_profil)){
			$fields=etendu_recup_saisie($type_profil);
			$message=enregistrer_profil($type_profil,$fields);
		}
		else {
			if ((_request("installation")=='oui')&&($GLOBALS['auteur_session']['statut']=='0minirezo')){
				creer_table_profil($type_profil);
			}
			$fields=creer_profil($type_profil);
		}
		return array("formulaires/formulaire_profil_etendu", $GLOBALS['delais'],
					array('form' => etendu_form($fields,$type_profil,''),
//						'extra' => $r['extra'],
						'type' => $type_profil,
						'legend' => _T("forms:".$type_profil),
						'message' => $message,
					'self' => ($lien ? $lien : generer_url_public('profil')),
					));
		
	}
	
}
function creer_table_profil($type_profil){
	$q="CREATE TABLE spip_".$type_profil." (";
	$champs=etendu_champs($type_profil);
	foreach (array_keys($champs) as $champ){
		$q.="`".$champ."` ";
		if ((($champs[$champ]=="radio")||($champs[$champ]=="select"))&&(is_array($GLOBALS['enum_conf'][$champ])))
			$q.="ENUM('".join(array_keys($GLOBALS['enum_conf'][$champ]),"','")."'),";
		elseif ($champs[$champ]=="bloc")
			$q.="TEXT,";
		elseif ($champs[$champ]=="checkbox")
			$q.="ENUM('oui','non') NOT NULL default 'non',";
		else $q.="varchar(255) default NULL,";	
	}
	$q.="`id_auteur` int(11) NOT NULL default '0',maj DATETIME)";
//	spip_log("installation formulaire etendu:".$type_profil);
	$result=spip_query($q);
}
function creer_profil($type_profil,$id=0){
	if ($id==0)$id=$GLOBALS['auteur_session']['id_auteur'];
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
	//spip_log("set:".$set);
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
//		$label = "<label for=''>$prettyname&nbsp;:</label>";

		switch($form) {

			case "case":
			case "checkbox":
				$affiche .= "<label for='etendu_$champ' class='checkbox'><input type='checkbox' class='truc' name='etendu_$champ' id='etendu_$champ'";
				if ($extra[$champ] == 'oui')
					$affiche .= " CHECKED ";
				$affiche .= " value='on'/>";
				$affiche .= "$prettyname</label>";
				break;
			case "list":
			case "liste":
			case "select":
				$affiche .= "<label for='etendu_$champ'>$prettyname&nbsp;:</label>";
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
					$affiche .= ">$choix_</option>\n";
					$i++;
				}
				$affiche .= "</select>";
				break;

			case "radio":
				$affiche .= "<label for=''>$prettyname&nbsp;:</label>";
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

					$affiche .= " value='$val'/><label for='etendu_$champ_$i' class='radio'>$choix_</label>\n";
					$i++;
				}
				break;

			// A refaire car on a pas besoin de renvoyer comme pour checkbox
			// les cases non cochees
			case "multiple":
				$affiche .= "<label for=''>$prettyname&nbsp;:</label>";
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
					$affiche .= $choix[$i];
					$affiche .= "</label>\n";
				}
				break;

			case "bloc":
			case "block":
				$affiche .= "<label for='etendu_$champ'>$prettyname&nbsp;:</label>";
				$affiche .= "<textarea name='etendu_$champ' id='etendu_$champ' class='forml' rows='5' cols='40'>".entites_html($extra[$champ])."</textarea>\n";
				break;

			case "masque":
				$affiche .= "<label for='etendu_$champ'>$prettyname&nbsp;:</label>";
				$affiche .= "<span style='color:#555'>".interdire_scripts($extra[$champ])."</span>\n";
				break;

			case "ligne":
			case "line":
			default:
				$affiche .= "<label for='etendu_$champ'>$prettyname&nbsp;:</label>";
				$affiche .= "<INPUT TYPE='text' NAME='etendu_$champ' CLASS='forml'\n";
				$affiche .= " VALUE=\"".entites_html($extra[$champ])."\" SIZE='40'>\n";
				break;
		}

//		$affiche .= "\n";
//		$affiche .= "<p>\n";
	}
	return $affiche;

}

?>