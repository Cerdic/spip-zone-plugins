<?php
/**
 * @version From SPIP-Listes-V :: import_export.php,v 1.19 paladin@quesaco.org  http://www.quesaco.org/
 * @package spiplistes
 *
 *
 *
 * @todo CP-20110815: lors de la création d'un compte, le format de réception
 * n'est appliqué qu'à la (les) liste sélectionnée. Appliquer
 * ce format au profil par défaut (spip_auteurs_elargis.spip_listes_format)
 * Vérifier ce format lors d'une modification
 * de compte et appliquer ce profil par défaut si manquant.
 * 
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/spiplistes_api_globales');

/**
 * Import d'un fichier texte, liste d'abonnes
 *
 * Le fichier doit etre de type CVS.
 * Les enregistrements sont separes par une tabulation
 * ou un point-virgule ';'
 * Exemple:
 * me@example.com;mylogin;My Name;0minirezo
 * 
 * @param string $filename
 * @param string $realname
 * @param array $abos_liste
 * @param string $format_abo
 * @param string $separateur
 * @param bool $flag_admin pas utilise'!
 * @param bool $listes_autorisees pas utilise'!
 * @param bool $forcer_abo
 * @return string
 * @todo code a nettoyer
 */
function spiplistes_import (
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

	if (is_readable($filename)
	   && ($abos_liste = (spiplistes_ids_valides ($abos_liste))))
	{
		// pour les stats
		$compteur = array(
			'creer' => 0
			, 'abonner' => 0
			, 'existe' => 0
			, 'format' => 0
		);
		$bad_dupli = $bad_email = 0;
		$statuts_auteurs = array('6forum', '1comite', '0minirezo');
		
		$stack_new_auteurs = array(); // tableau des comptes à créer
		
		$stack_new_abonnes = array(); // Tableau des comptes à abonner
		
		$nb_fiches_import = 0; // nb de lignes valides dans le fichier
		
		$nb_auteurs_exists = 0; // comptes déjà dans la base
		
		$nb_abos_records = 0; // nb abonnements enregistrés
		
		$start_time = microtime(1);
		
		/**
		 * En cas d'import de comptes existants,
		 * préserver le statut de chaque compte.
		 * Le compte par défaut reçoit un statut temporaire
		 * pour mise à jour par paquet.
		 * Voir plus bas pour info.
		 */
		$tmp_statut = date('YmdGis'); // statut temporaire
		$s = '6forum';
		$current_statuts = array($s => $s.$tmp_statut);
		
		/**
		 * Recupere les logins et mails existants dans la base
		 * pour eviter les doublons.
		 * Les comptes sans mail sont ignorés.
		 * Le résulat est un tableau :
		 * (
		 * 	email => array (id_auteur, format, statut),
		 * 	...
		 * )
		 */
		$current_auteurs = spiplistes_auteurs_par_mail ();
		
		/**
		 * Charger la table des abonnements en ram
		 * afin d'eviter les petites requettes.
		 */
		$current_abonnements = spiplistes_abonnements_lister ();
		
		/**
		 * Import du fichier transmis
		 */
		spiplistes_log('IMPORT FILE: '.$realname);

		$contenu_fichier = file($filename);
		
		//spiplistes_debug_log ('memory_get_usage[3]: ' . memory_get_usage());
		
		$nb_lignes_fichier = count($contenu_fichier);
		
		//spiplistes_debug_log ('memory_get_usage[2]: ' . memory_get_usage());
		//spiplistes_debug_log ('memory_get_peak_usage[2]: ' . memory_get_peak_usage());
		
		for($jj = 0; $jj < $nb_lignes_fichier; $jj++)
		{
			$nouvelle_entree = trim($contenu_fichier[$jj]);
			
			if(!empty($nouvelle_entree)
			   // ni une ligne de commentaire
			   && (
				   ($char = substr($nouvelle_entree, 0, 1))
				   && ($char != '#')
				   && ($char != '/')
				)
			) {
				$nb_fiches_import++;
				
				list($email, $login, $nom) = explode($separateur, $nouvelle_entree);
				
				$email = strtolower(trim($email));

				$mail_exist = FALSE;
				
				$email = email_valide($email);
				
				if ($email
				   &&	(
						!($mail_exist = array_key_exists($email, $current_auteurs))
						|| $forcer_abo
						)
				)
				{
					
					/**
					 * Si le compte n'existe pas, le creer
					 */
					if(!$mail_exist)
					{
						/**
						 * Commencer par calculer le login
						 */
						$login = trim($login);
						if(empty($login))
						{
							$login = spiplistes_login_from_email($email);
						}
						else
						{
							$login = strtolower($login);
						}
						
						/**
						 * puis le nom
						 */
						$nom = trim($nom);
						if(empty($nom))
						{
							$nom = ucfirst($login);
						}
						
						/**
						 * Ajoute l'invité dans la table des auteurs
						 */
						$pass = creer_pass_aleatoire(8, $email);
					
						/**
						 * Nouvel abo dans la pile des "a creer"
						 */
						$stack_new_auteurs[] = array(
							'nom' => $nom
							, 'email' => $email
							, 'login' => $login
							, 'pass' => md5($pass)
							, 'statut' => $current_statuts['6forum']
							, 'htpass' => generer_htpass($pass)
						);
					} // end if(!$mail_exist)
					
					// adresse mail existe dans la base
					// si on passe par ici, c'est sous-entendu $forcer_abo
					// (doit abonner un compte existant)
					else
					{
						$nb_auteurs_exists++;
						$stack_new_abonnes[] = intval($current_auteurs[$email]['id_auteur']);
					}
				}
				
				/**
				 * Signale en log si la ligne lue est erronée.
				 */
				if (!$email) {
					spiplistes_log ('BAD ENTRY @ LINE #'.$jj.': '.$nouvelle_entree);
				}
			}
		} // end for
				
		$creer_comptes = count($stack_new_auteurs);
		spiplistes_debug_log ('CREATE '.$creer_comptes.' new accounts');
		
		$nb_inscrire_abos = count($stack_new_abonnes);
		spiplistes_debug_log ('SUBSCRIBE '
							  . ($creer_comptes + $nb_inscrire_abos)
							  . ' accounts');
		
		/**
		 * Préparer les statuts temporaires :
		 * On reprend les statuts existants dans la table des
		 * auteurs
		 */
		foreach ($current_auteurs as $key => $val)
		{
			$s = $val['statut'];
			if (!isset ($current_statuts[$s])) {
				$current_statuts[$s] = NULL;
			}
			//spiplistes_debug_log ($s);
		}
		/**
		 * Les statuts temporaires reprennent la clé.
		 * Permettra de changer (update) le vrai statut plus tard
		 * par paquets de statuts au lieu de par enregistrement.
		 */
		foreach ($current_statuts as $key => $val)
		{
			$current_statuts[$key] = $key.$tmp_statut;
		}
		
		/**
		 * Appliquer le statut temporaire aux comptes
		 * existants, pour chaque statut existant.
		 */
		$modifier_par_statuts = array();
		foreach ($current_auteurs as $mail => $auteur)
		{
			if ($format_abo != $auteur['format'])
			{
				$s = $auteur['statut'];
				if (!isset ($modifier_par_statuts[$s])) {
					$modifier_par_statuts[$s] = array();
				}
				$modifier_par_statuts[$s][] = $auteur['id_auteur'];
			}
		}
		foreach ($modifier_par_statuts as $s => $ids_auteurs)
		{
			if (count($ids_auteurs))
			{
				$ids_auteurs = implode(',', $ids_auteurs);
				
				if (sql_update(array('spip_auteurs')
				   , array('statut' => sql_quote($current_statuts[$s]))
				   , array('id_auteur IN ('.$ids_auteurs.')')
				   ) === FALSE
				)
				{
					spiplistes_sqlerror_log('stack_format_abo');
				}
			}
		}
		
		if ($creer_comptes || $nb_inscrire_abos)
		{
			if ($creer_comptes)
			{
				$sql_col_names = '('.implode(',', array_keys($stack_new_auteurs[0])).')';
				$sql_col_values = '';
				
				//spiplistes_debug_log ('memory_get_usage[5]: ' . memory_get_usage());
				
				/**
				 * Préparer le paquet des comptes à créer
				 */
				foreach($stack_new_auteurs as $auteur)
				{
					$values = array_map('sql_quote', $auteur);
					$sql_col_values .= '('.implode(',', $values).'),';
				}
				$sql_col_values = rtrim($sql_col_values,',');
				
				// Envoyer le paquet
				if (sql_insert('spip_auteurs', $sql_col_names, $sql_col_values))
				{
					/**
					 * Récupérer les id_auteur des créés
					 * pour ajouter à la table des abonnements
					 */
					$sql_select = array('id_auteur');
					$sql_from = array('spip_auteurs');
					$sql_where[] = 'statut='.sql_quote($current_statuts['6forum']);
					
					if ($sql_result = sql_select (
						$sql_select
						, $sql_from
						, $sql_where
						)) {
						while ($row = sql_fetch($sql_result)) {
							$stack_new_abonnes[] = $row['id_auteur'];
						}
					}
				}
			}
			$nb_inscrire_abos = count($stack_new_abonnes);
			spiplistes_debug_log ('SUBCRIBE '.$nb_inscrire_abos.' accounts');
			
			//spiplistes_debug_log ('memory_get_usage[6]: ' . memory_get_usage());
			
			/**
			 * Inscrire les abonnements
			 */
			if ($nb_inscrire_abos) {
				
				spiplistes_debug_log ('inscription des abos');
		
				$sql_table = 'spip_auteurs_listes';
				$sql_update_valeurs = array();
				$sql_insert_valeurs = '';
				$q_format = sql_quote($format_abo);
				
				/**
				 * Traiter les listes souhaitées une par une
				 */
				foreach($abos_liste as $id_liste)
				{
					$id_liste = intval ($id_liste);
					
					if ($id_liste <= 0) { continue; }
					
					/**
					 * Pour les membres déjà inscrits,
					 * abonner à la liste si pas déjà abonné
					 */
					foreach ($stack_new_abonnes as $id_auteur)
					{
						$deja_abonne = isset($current_abonnements[$id_liste])
							&& in_array($id_auteur, $current_abonnements[$id_liste]);
						
						if (!$deja_abonne)
						{
							$sql_insert_valeurs .= '('.$id_auteur.','.$id_liste.','.$q_format.',NOW()),';
							$nb_abos_records++;
						}
						else if ($forcer_abo)
						{
							if (!isset($sql_update_valeurs[$id_liste]))
							{
								$sql_update_valeurs[$id_liste] = array();
							}
							$sql_update_valeurs[$id_liste][] = $id_auteur;
							$nb_abos_records++;
						}
					} // foreach
				} // foreach
				/**
				 * Insertion dans la table des abonnements
				 */
				if (!empty($sql_insert_valeurs))
				{
					$sql_noms = '(id_auteur,id_liste,format,date_inscription)';
					$sql_insert_valeurs = rtrim($sql_insert_valeurs, ',');
					
					if (sql_insert($sql_table, $sql_noms, $sql_insert_valeurs) === FALSE)
					{
						spiplistes_sqlerror_log ('INSERT abonnements');
					}
				}
				/**
				 * Correction de la table des abonnements
				 * si forcé.
				 */
				if (count($sql_update_valeurs))
				{
					$desabonner = ($format_abo == 'non');
					$sql_table = 'spip_auteurs_listes';
					$sql_update_champ = array('format' => sql_quote($format_abo));
					
					/**
					 * Traiter les listes une par une
					 */
					foreach ($sql_update_valeurs as $id_liste => $id_auteurs)
					{
						$sql_where = '
							id_liste = '.$id_liste . ' AND 
							id_auteur IN (' . implode(',', $id_auteurs) . ')';
						
						/**
						 * Désabonner ?
						 * Supprimer de la liste des abonnements.
						 */
						if ($desabonner)
						{
							if (sql_delete ($sql_table, $sql_where) === FALSE)
							{
								spiplistes_sqlerror_log ('DELETE abonnements');
							}
						}
						/**
						 * sinon, appliquer le format souhaité
						 */
						else
						{
							if (sql_update ($sql_table,
											$sql_update_champ,
											$sql_where) === FALSE)
							{
								spiplistes_sqlerror_log ('UPDATE format abonnements');
							}
						}
					}
				}
			} // if
		
			/**
			 * Appliquer le format de réception
			 * @todo A remplacer par la fonction en API
			 */
			if(sql_query(
				'INSERT INTO spip_auteurs_elargis
						(id_auteur,`spip_listes_format`) SELECT a.id_auteur,'.sql_quote($format_abo)
								.' FROM spip_auteurs AS a WHERE a.statut='.sql_quote($tmp_statut))
			   === FALSE
			)
			{
				spiplistes_sqlerror_log('import: nouveaux formats dans spip_auteurs_elargis');
			}
		}
		
		
		/**
		 * Redonner le bon statut pour chaque statut rencontré
		 */
		foreach ($current_statuts as $key => $val)
		{
			sql_update(array('spip_auteurs')
				   , array('statut' => sql_quote($key))
				   , array('statut='.sql_quote($val))
				   );
		}
		// fin des req

		$result_affiche .=
			($tt = ($ii = count($stack_new_auteurs)) + ($jj = count($stack_new_abonnes)))
			?	'<ul>'.PHP_EOL
				. '<li class="verdana2">'._T('spiplistes:nb_comptes_importees_en_ms_dont_'
										 , array('nb' => $nb_fiches_import
												 , 'ms' => (microtime(1) - $start_time))
										 )
				. '<ul>'.PHP_EOL
					. '<li>'._T('spiplistes:nb_fiches_crees', array('nb' => $ii)).'</li>'.PHP_EOL
					//. '<li>'._T('spiplistes:nb_comptes_modifies', array('nb' => $jj)).'</li>'.PHP_EOL
					. '<li>'._T('spiplistes:nb_auteurs_exists'
								, array('nb' => $nb_auteurs_exists)) .'</li>'.PHP_EOL
					. '<li>'._T('spiplistes:nb_abos_enregistres_pour_nb_listes'
								, array('nb' => $nb_abos_records, 'nl' => count($abos_liste))
								).'</li>'.PHP_EOL
					
					//. '<li>'._T('spiplistes:nb_comptes_ignores', array('nb' => $jj)).'</li>'.PHP_EOL
				. '</ul>'.PHP_EOL
				. '</li>'.PHP_EOL
				. '</ul>'.PHP_EOL
			: '<br />'._T('spiplistes:pas_dimport').PHP_EOL
			;

		if ($bad_dupli)
		{
			$result_affiche .= '<br />'._T('pass_erreur')
				.' email: '._T('spiplistes:n_duplicata_mail', array('n' => $bad_dupli))
				.PHP_EOL;
		}
		if ($bad_email)
		{
			$result_affiche .= '<br />'._T('pass_erreur')
				.' email: '._T('spiplistes:n_incorrect_mail', array('n' => $bad_email))
				.PHP_EOL;
		}
		
		$result_affiche = _T('spiplistes:fichier_') . ' : <strong>'.$realname.'</strong><br />'.PHP_EOL
			. _T('spiplistes:' . ((count($abos_liste) > 1) ? 'listes_de_destination_s' : 'liste_de_destination_s')
				 , array('s' => '#' . implode(',#', $abos_liste))) .'<br />'.PHP_EOL
			. $result_affiche
			;
	}
	return($result_affiche);
} // spiplistes_import()

/**
 * Controler les id donnes
 *
 * L'argument est soit un int positif, soit
 * un tableau d'int positifs.
 * @param int|array liste à controler
 * @return array|bool
 */
function spiplistes_ids_valides ($ids) {
	
	if (!is_array($ids)) {
		$ids = array ($ids);
	}
	if (is_array($ids)) {
		$array = array();
		foreach ($ids as $id) {
			$ii = intval ($id);
			if ($id > 0) {
				$array[] = $id;
			}
		}
		return ($array);
	}
	return (FALSE);
}

/**
 * Retourne liste des auteurs sous forme
 * d'un tableau dont l'index est l'email :
 * 	[$email] => array(
 * 		'id_auteur' => $id_auteur,
 * 		'format' => $format,
 * 		'statut' => $statut
 * 		)
 * @return array
 */
function spiplistes_auteurs_par_mail ()
{
	$auteurs = array();
	
	$auteurs_format = spiplistes_formats_defaut_lister ();
	
	if ($sql_result = sql_select(
							  array('id_auteur', 'email', 'statut')
							 , array('spip_auteurs')
							 , array('email<>'.sql_quote(''))
							 )
	) {
		while($row = spip_fetch_array($sql_result))
		{
			$id_auteur = intval($row['id_auteur']);
			$email = strtolower($row['email']);

			$format = (isset($auteurs_format[$id_auteur]))
				? $auteurs_format[$id_auteur]
				: 'non'
				;
			
			$auteurs[$email] = array(
				'id_auteur' => $id_auteur,
				'format' => $format,
				'statut' => $row['statut']
				);
		}
	}
	return ($auteurs);
}