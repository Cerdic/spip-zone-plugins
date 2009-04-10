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
	, $forcer_abo = false
) {
	$result = "";
	
	if(is_readable($filename)) {
		
		// recupere les logins et mails existants pour eviter les doublons
		
		$current_entries = array();
		$sql_result = sql_select(array('id_auteur', 'login', 'email', 'nom'), "spip_auteurs");
		while($row = spip_fetch_array($sql_result)) {
			// ne prendre que les comptes qui ont un email
			if($m = $row['email']) {
				$m = strtolower($m);
				$current_entries[$m] = array('login' => strtolower($row['login'])
										   , 'id_auteur' => $row['id_auteur']
										   , 'nom' => $row['nom']
										   );
			}
		}
		//
		$new_entries = file($filename);
		$nb_entries = count($new_entries);
		$bad_dupli = $bad_email = 0;
		$statuts_auteurs = array('6forum', '1comite', '0minirezo');
		
		$flag_ajout = _T('spiplistes:ajout');
		$flag_creation = _T('spiplistes:creation');
		
		for($jj = 0; $jj < $nb_entries; $jj++) {
			
			$nouvelle_entree = trim($new_entries[$jj]);
			
			if(!empty($nouvelle_entree) && !ereg("^[/#]", $nouvelle_entree))
			{
				list($email, $login, $nom, $statut) = explode($separateur, $nouvelle_entree);
				$email = strtolower(trim($email));
				if(
					!in_array($statut, $statuts_auteurs)
				) {
					$statut = "6forum";
				}
				
				$mail_exist = false;
				
				if(($email = email_valide($email))
				   &&	(
						!($mail_exist = array_key_exists($email, $current_entries))
						|| $forcer_abo
						)
				) {
					if(!$mail_exist) {
						
						// commencer par calculer le login
						$login = strtolower(trim($login));
						if(empty($login)) {
							$login = spiplistes_login_from_email($email);
						}
						// puis le nom
						if(empty($nom)) {
							$nom = ucfirst($login);
						}
						
						// ajoute l'invite' dans la table des auteurs
						$pass = creer_pass_aleatoire(8, $email);
					
						if($id_auteur = sql_insertq(
								"spip_auteurs"
								, array(
									  'nom' => $nom
									, 'email' => $email
									, 'login' => $login
									, 'pass' => md5($pass)
									, 'statut' => $statut
									, 'htpass' => generer_htpass($pass)
									//, 'cookie_oubli' => creer_uniqid()
								)
							)
						) {
							// le format de reception
							spiplistes_format_abo_modifier($id_auteur, $format_abo);
						}
						else {
							static $err;
							$nb_err = 1;
							if(!$err) { $err = _T('spiplistes:erreur_import_base'); }
							if($message_erreur != $err) {
								$message_erreur = $err;
							}
							else {
								$nb_err++;
							}
						}
						if($nb_err > 1) {
							$message_erreur .= " " . _T('spiplistes:erreur_n_fois', array('n', $nb_err)); 
						}
						if(!empty($message_erreur)) {
							spiplistes_log($message_erreur);
							$message_erreur = "";
						}
					}
					// sous-entendu $forcer_abo (abonne' un compte existant)
					else {
						$id_auteur = $current_entries[$email]['id_auteur'];
						$login = $current_entries[$email]['login'];
						$nom = $current_entries[$email]['nom'];
					}
					
					$acte = ($mail_exist ? $flag_ajout : $flag_creation);
					
					$result .= ""
						. "<li class='verdana2'><a href='mailto:$email'>$login</a> $email ($nom)"
						. " <small>[$acte #$id_auteur]</small></li>\n";
					
					// abonner le compte a(ux) liste(s)
					if(is_array($abos_liste) && count($abos_liste)) {
						$sql_values = "";
						foreach($abos_liste as $id_liste) {
							$sql_values .= " (".sql_quote($id_auteur).",".sql_quote($id_liste).",NOW()),";
						}
						$sql_values = rtrim($sql_values, ",");
						if(!empty($sql_values)) {
							$sql_query = "INSERT IGNORE INTO spip_auteurs_listes (id_auteur,id_liste,date_inscription) 
								VALUES ".$sql_values;
							spiplistes_log($sql_query);
							if(!sql_query($sql_query)) {
								spiplistes_sqlerror_log("module import");
							}
						}
					}
				} else {
					if($mail_exist) {
						$bad_dupli++;
						spiplistes_log("import dupli: $mail");
					}
					else {
						$bad_email++;
						spiplistes_log("import bad: $mail");
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
		if($bad_dupli) {
			$result .= "<br />"._T('pass_erreur')." email: "._T('spiplistes:n_duplicata_mail', array('n' => $bad_dupli))."\n";
		}
		if($bad_email) {
			$result .= "<br />"._T('pass_erreur')." email: "._T('spiplistes:n_incorrect_mail', array('n' => $bad_email))."\n";
		}
		$result = "<strong>$realname</strong>\n" . $result;
	}
	return($result);
}
//
?>