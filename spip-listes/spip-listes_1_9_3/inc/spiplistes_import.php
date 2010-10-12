<?php
// From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/spiplistes_api_globales');

function spiplistes_import(
	$filename
	, $realname
	, $abos_liste
	, $format_abo = 'non'
	, $separateur = "\t"
	, $flag_admin
	, $listes_autorisees
	, $forcer_abo = false
) {
	$result_affiche = '';
	
	$ajouter_format = $ajouter_abonnements = false;
	
	if(is_readable($filename))
	{
		if(!is_array($abos_liste))
		{
			if(($ii = intval($abos_liste)) <= 0)
			{
				return(false);
			}
			$abos_liste = array($ii);
		}
		else
		{
			$abos_liste = array_map('intval', $abos_liste);
		}
		
		// recupere les logins et mails existants dans la base
		// pour eviter les doublons
		
		$current_entries = array();
		$sql_result = sql_select(array('id_auteur', 'login', 'email', 'nom'), 'spip_auteurs');
		
		while($row = spip_fetch_array($sql_result))
		{
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
		
		// charger la table des abonnements en ram afin d'eviter les petites requettes
		$abonnements = array();
		if(($sql_result = sql_select("id_auteur,id_liste"
									, "spip_auteurs_listes")) !== false) {
			while($row = sql_fetch($sql_result)) {
				if(!isset($abonnements[$row['id_liste']])) {
					$abonnements[$row['id_liste']] = array();
				}
				$abonnements[$row['id_liste']][] = $row['id_auteur'];
			}
		}
		else {
			spiplistes_sqlerror_log("module import (abonnements)");
		}
		
		if($forcer_abo) {
			$formats = array();
			// charger la table des formats afin d'eviter les petites requettes
			if(($sql_result = sql_select("id_auteur,`spip_listes_format` AS format"
										, "spip_auteurs_elargis")) !== false) {
				while($row = sql_fetch($sql_result)) {
					$formats[$row['id_auteur']] = $row['format'];
				}
			}
			else {
				spiplistes_sqlerror_log("module import (format)");
			}
		}
		
		// tableau des VALUES pour LA requete de fin d'import
		// les abonnements
		$ajouter_abonnements = array();
		// les formats
		$ajouter_format = array();		
		
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
						// si le compte n'existe pas, le creer
						
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
					
						// on ne peut pas empiler les req car il nous manque id_auteur
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
							//spiplistes_format_abo_modifier($id_auteur, $format_abo);
							// empiler le tout pour une seule req
							$ajouter_format[] = "(" . sql_quote($id_auteur) . "," . sql_quote($format_abo) . ")";
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
					// adresse mail existe dans la base
					// si on passe par ici, c'est sous-entendu $forcer_abo (abonne' un compte existant)
					else {
						$id_auteur = intval($current_entries[$email]['id_auteur']);
						$login = $current_entries[$email]['login'];
						$nom = $current_entries[$email]['nom'];
						
						// forcer le format dans la foulee
						if(!isset($formats[$id_auteur])) {
							$ajouter_format[] = "(" . sql_quote($id_auteur) . "," . sql_quote($format_abo) . ")";
						}
					}
					
					$acte = ($mail_exist ? $flag_ajout : $flag_creation);
					
					// abonner le compte a(ux) liste(s)
					$id_auteur_q = sql_quote($id_auteur);
					$aux_listes = array();
					foreach($abos_liste as $id_liste) {
						//spiplistes_log("chercher si id_auteur #$id_auteur deja abonne a id_liste #$id_liste");
						if(
						   (!isset($abonnements[$id_liste]))
						   || !in_array($id_auteur, $abonnements[$id_liste])
						) {
							$ajouter_abonnements[] = "($id_auteur_q,".sql_quote($id_liste).",NOW())";
							$aux_listes[] = $id_liste;
						}
					}
					$ii = count($aux_listes) ? "#" . implode(",#", $aux_listes) : "";
					if(!empty($ii)) {
						$result_affiche .= ""
								. "<li class='verdana2'><a href='mailto:$email'>$login</a> $email ($nom)"
								. " <small>[$acte #$id_auteur -> $ii ]</small></li>\n"
								;
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
		} // end for
		
		if(count($ajouter_abonnements)) {
			$sql_values = implode(",", $ajouter_abonnements);
			if(!empty($sql_values))
			{
				spiplistes_log("ajout abonnements: " . $sql_values);
				if(sql_insert('spip_auteurs_listes'
							  , "(id_auteur,id_liste,date_inscription)", $sql_values) === false) {
					spiplistes_sqlerror_log("module import ajout abonnements");
				}
			}			
		}
		
		// inserer les formats des abos manquants
		if(count($ajouter_format)) {
			$sql_values = implode(",", $ajouter_format);
			if(!empty($sql_values))
			{
				spiplistes_log("ajout format: " . $sql_values);
				if(sql_insert('spip_auteurs_elargis'
							  , "(id_auteur,`spip_listes_format`)", $sql_values) === false) {
					spiplistes_sqlerror_log("module import ajout formats");
				}
			}
		}
		
		if(!empty($result_affiche)) {
			$result_affiche = "<ul>\n".$result_affiche."</ul>\n";
		}
		else {
			$result_affiche = "<br />" . _T('spiplistes:pas_dimport') . "\n";
		}
		if($bad_dupli) {
			$result_affiche .= "<br />"._T('pass_erreur')." email: "._T('spiplistes:n_duplicata_mail', array('n' => $bad_dupli))."\n";
		}
		if($bad_email) {
			$result_affiche .= "<br />"._T('pass_erreur')." email: "._T('spiplistes:n_incorrect_mail', array('n' => $bad_email))."\n";
		}
		$result_affiche = _T('spiplistes:fichier_') . " : <strong>$realname</strong><br />\n"
			. _T('spiplistes:' . ((count($abos_liste) > 1) ? 'Listes_de_destination_s' : 'Liste_de_destination_s')
				 , array('s' => "#" . implode(",#", $abos_liste))) ."<br />\n"
			. $result_affiche
			;
	}
	return($result_affiche);
}
//
?>