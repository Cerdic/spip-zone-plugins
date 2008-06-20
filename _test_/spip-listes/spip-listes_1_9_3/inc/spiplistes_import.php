<?php
// From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/spiplistes_api_globales');

function spiplistes_import ($filename, $realname, $abos_liste, $format_abo = "non"
	, $separateur = "\t"
	, $flag_admin
	, $listes_autorisees
) {
	$result = "";
	if(is_readable($filename)) {
		// récupère les logins et mails existants pour éviter les doublons
		$current_logins = array();
		$current_emails = array();
		$sql_result = sql_select(array('login', 'email'), "spip_auteurs");
		while($row = spip_fetch_array($sql_result)) {
			$current_logins[] = strtolower($row['login']);
			$current_emails[] = strtolower($row['email']);
		}
		//
		$new_entries = file($filename);
		$nb_entries = count($new_entries);
		$new_abonne = $bad_login = $bad_email = 0;
		$statuts_auteurs = array('6forum', '1comite', '0minirezo');
		
		for($jj = 0; $jj < $nb_entries; $jj++) {
			$nouvelle_entree = trim($new_entries[$jj]);
			if(!empty($nouvelle_entree) && !ereg("^[/#]", $nouvelle_entree)) {
				list($email, $login, $nom, $statut) = explode($separateur, $nouvelle_entree);
				$email = strtolower(trim($email));
				if(
					!in_array($statut, $statuts_auteurs)
				) {
					$statut = "6forum";
				}
				if(($email = email_valide($email)) && !in_array($email, $current_emails)) {
					$login = strtolower(trim($login));
					if(empty($login)) {
						$login = substr($email, 0, strpos($email, "@"));
					}
					if(!in_array($login, $current_logins)) {
						if(empty($nom)) {
							$nom = ucfirst($login);
						}

						$result .= "<li class='verdana2'><strong>+</strong> <a href='mailto:$email'>$login</a> $email ($nom)</li>\n";

						// ajoute l'invite' dans la table des auteurs
						$pass = creer_pass_aleatoire(8, $email);
						
						$id_auteur = sql_insertq(
							"spip_auteurs"
							, array(
								  'nom' => $nom
								, 'email' => $email
								, 'login' => $login
								, 'pass' => md5($pass)
								, 'statut' => $statut
								, 'htpass' => generer_htpass($pass)
								, 'cookie_oubli' => creer_uniqid()
							)
						);

						// le format de reception
						spiplistes_format_abo_modifier($id_auteur, $format_abo);

						// abonne le compte aux listes
						if(is_array($abos_liste) && count($abos_liste)) {
							$sql_values = "";
							foreach($abos_liste as $id_liste) {
								$sql_values .= " (".sql_quote($id_auteur).",".sql_quote($id_liste).",NOW()),";
							}
							$sql_values = rtrim($sql_values, ",");
							if(!empty($sql_values)) {
								$sql_query = "INSERT INTO spip_auteurs_listes (id_auteur,id_liste,date_inscription) 
									VALUES ".$sql_values;
								sql_query($sql_query);
							}
						}
					} else {
						$bad_login++;
					}
				} else {
					$bad_email++;
				}
			}
		}
		if(!empty($result)) {
			$result = "<ul>\n".$result."</ul>\n";
		}
		else {
			$result = "<br />&lt;none&gt;\n";
		}
		if($bad_login) {
			$result .= "<br />"._T('pass_erreur')." login: $bad_login\n";
		}
		if($bad_email) {
			$result .= "<br />"._T('pass_erreur')." email: $bad_email "._T('spiplistes:incorrect_ou_dupli')."\n";
		}
		$result = "<strong>$realname</strong>\n" . $result;
	}
	return($result);
}
//
?>