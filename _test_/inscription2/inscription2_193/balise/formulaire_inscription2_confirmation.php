<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2_CONFIRMATION', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION_stat($args, $filtres) {
	//initialiser mode d'inscription et adresse de retour
	$mode = $args[0];
	$retour = $args[1];
	
	if(!$mode || ($mode != 'redac' && $mode != 'forum')){
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
		$retour = $args[0];
	}
	
	if(!$retour)
		$retour = $GLOBALS['meta']["adresse_site"];
	
	return array($mode, $retour);
}

function balise_FORMULAIRE_INSCRIPTION2_CONFIRMATION_dyn($mode, $retour) {
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
				),
				false
			);
			
		}elseif($n == 'sup'){
			sql_delete("spip_auteurs","id_auteur = '$id'");
			sql_delete("spip_auteurs_elargis","id_auteur = '$id'");
			echo "<strong>"._T('inscription2:suppression_faite')."</strong>";
		}else
			echo _T('inscription2:rien_a_faire');
	}else{
		include_spip('inc/acces');
		include_spip('inc/envoyer_mail');
		
		$htpass = generer_htpass($pass);
		$statut = lire_config('inscription2/statut_nouveau');
		/* Confirme l'inscription par la mise à jour du statut par défaut et le mot de passe */
		sql_updateq(
		    "spip_auteurs",
		    array(
		        "statut" => $statut, 
		        "pass" => $pass, 
		        "htpass" => $htpass, 
		        "alea_actuel" => ""
	        ),
	         "id_auteur = ".$id)
         ;
		echo "<strong>"._T('pass_nouveau_enregistre')."</strong><p><a href='".$retour."'>"._T('retour')."</a></p>";
		$var_user = sql_fetsel("nom, email, login","spip_auteurs","id_auteur=".$id);

		if($var_user){
			$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
			$adresse_site = $GLOBALS['meta']["adresse_site"];
			
			$message = _T('inscription2:message_auto')."\n\n" 
			. _T('inscription2:email_bonjour', array('nom'=>$var_user['nom']))."\n\n"
			. _T('inscription2:texte_email_confirmation', array('login'=> $var_user['login'], 'nom_site' => $nom_site_spip));
			
			if (envoyer_mail($var_user['email'],"[$nom_site_spip] "._T('inscription2:compte_active', array('nom_site' => $nom_site_spip)), $message))
				return;
			else
				return _T('inscription2:probleme_email');
		}else
			return _T('inscription2:probleme_email');
	}
}
?>
