<?php
// From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/affichage');

function spiplistes_import ($filename, $realname, $abos_liste, $format_abo = "non", $return = false) {
	$result = "";
	if(is_readable($filename)) {
		// récupère les logins et mails existants pour éviter les doublons
		$current_logins = array();
		$sql_query = "SELECT login FROM spip_auteurs";
		$sql_result = spip_query($sql_query);
		while($row = spip_fetch_array($sql_result)) {
			$current_logins[] = strtolower($row['login']);
		}
		//
		$new_entries = file($filename);
		$ii = count($new_entries);
		$new_abonne = 0;
		$statut = "6forum";
		for($jj=0; $jj<$ii; $jj++) {
			$nouvelle_entree = trim($new_entries[$jj]);
			if(!empty($nouvelle_entree) && !ereg("^[/#]", $nouvelle_entree)) {
				list($email, $login, $nom) = explode("\t", $nouvelle_entree);
				$email = strtolower(trim($email));
				if(email_valide($email)) {
					$login = strtolower(trim($login));
					if(empty($login)) {
						$login = substr($email, 0, strpos($email, "@"));
					}
					if(!in_array($login, $current_logins)) {
						if(empty($nom)) {
							$nom = ucfirst($login);
						}
						$pass = creer_pass_aleatoire(8, $email);
						$mdpass = md5($pass);
						$htpass = generer_htpass($pass);
						$cookie_oubli = creer_uniqid();
						$result .= "<li class='verdana2'><strong>+</strong> <a href='mailto:$email'>$login</a> $email ($nom)</li>\n";
						$sql_query = "INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, cookie_oubli) 
							VALUES ("._q($nom).","._q($email).","._q($login).","._q($mdpass).","._q($statut).","._q($htpass).","._q($cookie_oubli).")";
						spip_query($sql_query);
						$id_auteur = spip_insert_id();
						// ajoute le format de réception pour ce nouveau compte
						$sql_query = "INSERT INTO spip_auteurs_elargis (id_auteur,`spip_listes_format`) VALUES  ("._q($id_auteur).","._q($format_abo).")";
						spip_query($sql_query);
						// abonne le comptes aux listes
						if(is_array($abos_liste) && count($abos_liste)) {
							$sql_query = "";
							foreach($abos_liste as $id_liste) {
								$sql_query .= " ("._q($id_auteur).","._q($id_liste).",NOW()),";
							}
							$sql_query = rtrim($sql_query, ",");
							if(!empty($sql_query)) {
								$sql_query = "INSERT INTO spip_auteurs_listes (id_auteur,id_liste,date_inscription) VALUES ".$sql_query;
								spip_query($sql_query);
							}
						}
					}
				}
			}
		}
		if(!empty($result)) {
			$result = "<ul>\n".$result."</ul>\n";
		}
		else {
			$result = "<br />&lt;none&gt;\n";
		}
		$result = "<strong>$realname</strong>\n" . $result;
	}
	if($return) return($result);
	else echo($result);
}
?>