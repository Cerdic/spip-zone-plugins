<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2_CONFIRMATION', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION_stat($args, $filtres) {
	//initialiser mode d'inscription
	$mode = $args[0];
	if(!$mode)
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
	return array($mode);}

function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION_dyn($mode) {
	//recuperer les infos inserees dans l'environnement
	
	$id = _request('id');
	$mode = _request('mode');
	$cle = _request('cle');
	$pass = _request('pass');
	
	if($id != '' and $mode != '' and $cle != '' and $pass == ''){
		$n = confirmation_inscription2($id, $mode, $cle);
		if ($n == 'pass'){			

		return inclure_balise_dynamique(
			array("formulaires/inscription2_confirmation", 0,
			array(
				'id' => $id,
				'mode' => $mode,
				'cle' => $cle,
				'pass' => $pass,
			)
		), false);
	
	}elseif($n == 'sup'){
			spip_query("DELETE FROM spip_auteurs WHERE id_auteur = '$id'");
			spip_query("DELETE FROM spip_auteurs_elargis WHERE id_auteur = '$id'");
			echo "<strong>"._T('inscription2:suppression_faite')."</strong>";
	}else
			echo "rien a faire";
	}else{
		include_spip('inc/mail');
		$htpass = generer_htpass($pass);
		$statut = lire_config('inscription2/statut');
		spip_query("UPDATE spip_auteurs SET statut = '$statut', pass='$pass', htpass='$htpass', alea_actuel='' WHERE id_auteur = ".$id);
		echo "<strong>"._T('pass_nouveau_enregistre')."</strong>";
		$var_user = spip_query("SELECT nom, email, login FROM spip_auteurs WHERE id_auteur=".$id);
		$var_user = spip_fetch_array($var_user);
		if($var_user){
			$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
			$adresse_site = $GLOBALS['meta']["adresse_site"];
			$message = _T('inscription2:message_auto')
			. _T('inscription2:email_bonjour', array('nom'=>$var_user['nom']))."\n\n"
			. _T('inscription2:texte_email_confirmation', array('login'=> $var_user['login'], 'nom_site' => $nom_site_spip));
			if (envoyer_mail($var_user['email'],"[$nom_site_spip] "._T('inscription2:compte_active'), $message))
				return;
			else
				return _T('inscription2:probleme_email');
		}else
			return _T('inscription2:probleme_email');
	}
}
?>