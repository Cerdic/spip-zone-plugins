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
	
	if(is_readable($filename))
	{
		// abonner les adresses importees
		// aux listes...
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
		spiplistes_log('import fichier '.$filename);
		$new_entries = file($filename);
		
		$nb_new_entries = count($new_entries);
		
		$bad_dupli = $bad_email = 0;
		$statuts_auteurs = array('6forum', '1comite', '0minirezo');
		
		// charger la table des abonnements en ram afin d'eviter les petites requettes
		$abonnements = array();
		if(($sql_result = sql_select('id_auteur,id_liste'
									, 'spip_auteurs_listes')
			) !== false)
		{
			while($row = sql_fetch($sql_result)) {
				if(!isset($abonnements[$row['id_liste']])) {
					$abonnements[$row['id_liste']] = array();
				}
				$abonnements[$row['id_liste']][] = $row['id_auteur'];
			}
		}
		else {
			spiplistes_sqlerror_log('module import (abonnements)');
		}
		
		if($forcer_abo)
		{
			$auteurs_format = array();
			// charger la table des formats afin d'eviter les petites requettes
			if(($sql_result = sql_select("id_auteur,`spip_listes_format` AS format"
										, 'spip_auteurs_elargis')) !== false)
			{
				while($row = sql_fetch($sql_result)) {
					$auteurs_format[$row['id_auteur']] = $row['format'];
				}
			}
			else
			{
				spiplistes_sqlerror_log("module import (format)");
			}
		}
		
		// les formats
		$modifier_format = array();
		
		$err_import = _T('spiplistes:erreur_import_base');
		
		//syslog(LOG_NOTICE, 'memory_get_usage[2]: ' . memory_get_usage());
		//syslog(LOG_NOTICE, 'memory_get_peak_usage[2]: ' . memory_get_peak_usage());
		
		$start_time = microtime(1);
		
		$stack_new_auteurs = array();
		
		// statut temporaire
		$tmp_statut = '6abo'.date('YmdGis');
		
		for($jj = 0; $jj < $nb_new_entries; $jj++)
		{
			$nouvelle_entree = trim($new_entries[$jj]);
			
			if(!empty($nouvelle_entree)
			   // ni une ligne de commentaire
			   && !ereg("^[/#]", $nouvelle_entree))
			{
				list($email, $login, $nom) = explode($separateur, $nouvelle_entree);
				
				$email = strtolower(trim($email));
				
				$mail_exist = false;
				
				if(($email = email_valide($email))
				   &&	(
						!($mail_exist = array_key_exists($email, $current_entries))
						|| $forcer_abo
						)
				)
				{
					if(!$mail_exist)
					{
						// si le compte n'existe pas, le creer
						
						// commencer par calculer le login
						$login = trim($login);
						if(empty($login))
						{
							$login = spiplistes_login_from_email($email);
						}
						else
						{
							$login = strtolower($login);
						}
						// puis le nom
						$nom = trim($nom);
						if(empty($nom))
						{
							$nom = ucfirst($login);
						}
						
						// ajoute l'invite' dans la table des auteurs
						$pass = creer_pass_aleatoire(8, $email);
					
						// nouvel abo dans la pile des "a creer"
						$stack_new_auteurs[] = array(
							'nom' => $nom
							, 'email' => $email
							, 'login' => $login
							, 'pass' => md5($pass)
							, 'statut' => $tmp_statut
							, 'htpass' => generer_htpass($pass)
						);
					} // end if(!$mail_exist)
					
					// adresse mail existe dans la base
					// si on passe par ici, c'est sous-entendu $forcer_abo (abonne' un compte existant)
					else
					{
						$id_auteur = intval($current_entries[$email]['id_auteur']);
						
						// forcer le format dans la foulee
						if(!isset($auteurs_format[$id_auteur]))
						{
							$modifier_format[] = '(' . sql_quote($id_auteur) . ',' . sql_quote($format_abo) . ')';
						}
					}
					// est-ce vraiment utile (voir plus bas)
				}
				else
				{
					if($mail_exist) {
						$bad_dupli++;
						spiplistes_log('import dupli: '.$mail);
					}
					else {
						$bad_email++;
						spiplistes_log('import bad: '.$mail);
					}
				}
			}
		} // end for($jj = 0; $jj < $nb_new_entries; $jj++)
				
		// importer les nouveaux abonnés
		if(count($stack_new_auteurs))
		{
			$sql_col_names = '('.implode(',', array_keys($stack_new_auteurs[0])).')';
			$sql_col_values = '';
			
			foreach($stack_new_auteurs as $auteur)
			{
				$values = array_map('sql_quote', $auteur);
				$sql_col_values .= '('.implode(',', $values).'),';
			}
			$sql_col_values = rtrim($sql_col_values,',');
			//syslog(LOG_NOTICE, $sql_col_values);
			
			$r = sql_insert('spip_auteurs', $sql_col_names, $sql_col_values);
			//syslog(LOG_NOTICE, 'rr:'.(is_bool($r) ? ($r?'ok':'ko') : $r));
			
			// nouveaux abonnements
			foreach($abos_liste as $id_liste)
			{
				// un INSERT sans VALUES
				// @todo: vérifier compatibilite sqlite et pg
				if(sql_query(
					'INSERT INTO spip_auteurs_listes
								(id_auteur,id_liste) SELECT a.id_auteur,'.$id_liste
									.' FROM spip_auteurs AS a WHERE a.statut='.sql_quote($tmp_statut))
				   === false
				)
				{
					spiplistes_sqlerror_log('import nouveaux abos dans spip_auteurs_listes');
				}
			}
			
			// format pour les nouveaux auteurs
				// un INSERT sans VALUES
				// @todo: vérifier compatibilite sqlite et pg
			if(sql_query(
				'INSERT INTO spip_auteurs_elargis
						(id_auteur,`spip_listes_format`) SELECT a.id_auteur,'.sql_quote($format_abo)
								.' FROM spip_auteurs AS a WHERE a.statut='.sql_quote($tmp_statut))
			   === false
			)
			{
				spiplistes_sqlerror_log('import nouveauxformats dans spip_auteurs_elargis');
			}
		
		}
		
		// Comptes deja existants, inclus dans le fichier import
		// - changer son format de réception ?
		// - l'ajouter aux listes sélectionnées ?
		// - ou ignorer ?
		if(count($modifier_format))
		{
			// pour l'instant: ignorer !
			// 
		}
		
		// redonner le bon statut visiteur aux nouveaux
		sql_update(array('spip_auteurs'), array('statut' => sql_quote('6forum')), array('statut='.sql_quote($tmp_statut)));

		// fin des req

		$result_affiche .=
			($tt = ($ii = count($stack_new_auteurs)) + ($jj = count($modifier_format)))
			?	'<ul>'.PHP_EOL
				. '<li class="verdana2">'._T('spiplistes:nb_comptes_importees_en_ms_dont_'
										 , array('nb' => $tt, 'ms' => (microtime(1) - $start_time)))
				. '<ul>'.PHP_EOL
					. '<li>'._T('spiplistes:nb_fiches_crees', array('nb' => $ii)).'</li>'.PHP_EOL
					//. '<li>'._T('spiplistes:nb_comptes_modifies', array('nb' => $jj)).'</li>'.PHP_EOL
					. '<li>'._T('spiplistes:nb_comptes_ignores', array('nb' => $jj)).'</li>'.PHP_EOL
				. '</ul>'.PHP_EOL
				. '</li>'.PHP_EOL
				. '</ul>'.PHP_EOL
			: '<br />'._T('spiplistes:pas_dimport').PHP_EOL
			;

		
		if($bad_dupli) {
			$result_affiche .= '<br />'._T('pass_erreur').' email: '._T('spiplistes:n_duplicata_mail', array('n' => $bad_dupli)).PHP_EOL;
		}
		if($bad_email) {
			$result_affiche .= '<br />'._T('pass_erreur').' email: '._T('spiplistes:n_incorrect_mail', array('n' => $bad_email)).PHP_EOL;
		}
		$result_affiche = _T('spiplistes:fichier_') . ' : <strong>$realname</strong><br />'.PHP_EOL
			. _T('spiplistes:' . ((count($abos_liste) > 1) ? 'Listes_de_destination_s' : 'Liste_de_destination_s')
				 , array('s' => '#' . implode(',#', $abos_liste))) .'<br />'.PHP_EOL
			. $result_affiche
			;
	}
	return($result_affiche);
}
