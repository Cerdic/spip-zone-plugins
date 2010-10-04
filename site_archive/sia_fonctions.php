<?php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


/*
 * @author: cpaulus at quesaco.org
 * @license: GPL3
 */

if(!defined("_ECRIRE_INC_VERSION")) { return; }

// _DIR_PLUGIN_SIA est défini par ecrire_plugin_actifs()
if(!defined('_DIR_PLUGIN_SIA')) { return(false); }

// chemin du script de sauvegarde
@define('SIA_BIN_FOLDER', _DIR_PLUGIN_SIA.'bin/');
@define('SIA_SCRIPT_FILE', SIA_BIN_FOLDER.'site_archive.sh');

// Délai (time) pour la commande batch
// @see: man batch
define('SIA_SCRIPT_OPTIONS', ' now + 1 minute');

// URI du squelette pour archive en page unique au format html
define('SIA_URI_SKEL_HTML_UNIQUE', _SPIP_SCRIPT.'?page=site_archive-html');

// URI multi-pages. Simple wget sur l'objet
define('SIA_URI_SKEL_HTML_MULTI', _SPIP_SCRIPT.'?');

// URI du squelette pour archive au format texte
define('SIA_URI_SKEL_TEXTE', _SPIP_SCRIPT.'?page=site_archive-texte');

define('SIA_LOG_TAG', '[SIA]');

define('SIA_TYPE_TEXTE', 'texte');
define('SIA_TYPE_UNIQUE', 'unique');
define('SIA_TYPE_MULTIPLE', 'multi');

// pour les  journaux divers (wget)
// (à placer probablement en ~/tmp/)
define('SIA_LOGS_DIR', _DIR_RACINE._NOM_TEMPORAIRES_INACCESSIBLES.'sia/');

/**
 * Envoyer u message sur la console système
 * */
function sia_syslog($priority, $message)
{
	$message = SIA_LOG_TAG.' '.trim($message);
	syslog($priority, $message);
	
	return(true);
}

/**
 * Envoyer un message sur le journal plugin SPIP
 * */
function sia_log($message)
{
	spip_log(SIA_LOG_TAG.' '.$message, 'sia');
	sia_syslog(LOG_NOTICE, $message);
	
	return(true);
}

/**
 * Envoyer un message d'erreur
 * - sur le journal plugin SPIP
 * - sur la console système
 * */
function sia_error_log($message)
{
	$message = trim($message);
	
	if(!empty($message))
	{
		sia_log($message);
		sia_syslog(LOG_ERR, $message);
		// Décommenter la ligne ci-dessous pour journal PHP (php_error)
		// error_log($message);
	}
	return(true);
}

/**
 * Lancer une commande et récupérer
 * le résultat qui est envoyé en STDOUT
 * */
function sia_passthru($exec)
{
	$string = null;
	
	if($exec)
	{
		ob_start();
		passthru($exec, $result);
		$string = ob_get_contents();
		ob_end_clean();
	}
	return($string);
}

/**
 * Rechercle le chemin d'un exécutable système
 * en fouillant dans la variable d'environnement PATH
 * @return: complete filename path string ou false
 **/
function sia_cherche_chemin_exec($exec)
{
	static $paths;
	
	$exec = trim($exec);
	
	if(strlen($exec))
	{
		if(!$paths)
		{
			$paths = explode(':',$_ENV['PATH']
				// compléter par les chemins optionnels
				.':/usr/bin:/opt/local/bin:/usr/local/bin');
		}
		if($paths)
		{
			foreach($paths as $path)
			{
				$f = $path . DIRECTORY_SEPARATOR . $exec;
				if(file_exists($f) && is_executable($f))
				{
					return($f);
				}
			}
		}
	}
	return(false);
}

/**
 * Recherche un exécutable système
 * @param: $exec string, le nom de l'exécutable
 * @return: string chemin du fichier string ou false
 **/
function sia_chemin_exec($exec)
{
	static $paths;
	
	if($paths === null)
	{
		$paths = array();
	}
	
	$exec = trim($exec);
	
	// recherche l'exec
	// signale en log spip si manquante (~/tmp/spip.log)
	if(!isset($paths[$exec]))
	{
		$paths[$exec] = sia_cherche_chemin_exec($exec);
	}
	return($paths[$exec]);
}

/** Titre de l'objet (rubrique ou article)
 * @return string
 * */
function sia_titre_objet($objet, $id_objet, $prefix='', $type=SIA_TYPE_UNIQUE)
{
	$contexte = array(
		'lang' => $GLOBALS['spip_lang']
		, $objet => $id_objet
		, 'prefixe' => $prefix
		, 'suffixe' => substr($type,0,1)
	);
	
	$titre = recuperer_fond('titre_objet', $contexte);
	$titre = strlen($s = trim($titre)) ? $s : 'erreur-objet';
	
	return($titre);
}

/**
 * Date de modification de l'objet
 *
 * Pas défaut, considère que c'est une rubrique
 * qui est demandé.
 * 
 * $objet peut être id_article pour un article.
 * 
 * @todo: à compléter pour les mots, etc.
 * 
 * @todo: revoir les skel date_modif_*
 * 	Voire créer un skel d'aiguillage (date_modif_objet)
 * 	qui fait un include de date_modif_quivabien
 * 	car pas possible de placer une boucle dans une
 * 	condition (sioui ou autre)...
 *
 * @todo: compléter le skel rubrique
 *  qui ne fait pas de récursif. Ne traite que le premier
 *  niveau. Voir date_modif_rubrique.html
 * 
 * 	* */
function sia_time_modif_objet($objet, $id_objet)
{
	$time = null;
	
	$contexte = array(
		'lang' => $GLOBALS['spip_lang']
		, $objet => $id_objet
	);
	
	$date_modif_objet = 'date_modif_rubrique';
	
	if($objet != 'id_rubrique')
	{
		switch($objet)
		{
			case 'id_article':
				$date_modif_objet = 'date_modif_article';
				break;
		}
	}
	
	$date_modif = recuperer_fond($date_modif_objet, $contexte);
	
	if($date = recup_date($date_modif))
	{
		list($annee, $mois, $jour) = $date;
		list($heures, $minutes, $secondes) = recup_heure($date_modif);

		$time = mktime($heures, $minutes, $secondes, $mois, $jour, $annee);
	}
	
	//spip_log($date_modif.' '.date("Ymd\THis", $time));
	return($time);
}

/**
 * Renvoie valeur bool cfg pour une option de config
 * */
function sia_cfg_option_on($option)
{
	return(
		   (($ii = lire_config('sia/'.$option)) && ($ii == 'on'))
		   ? $ii
		   : null
	);
}

/**
 * Fonction de calcul pour la balise URL_ARCHIVE_RUBRIQUE
 * @param $id int, résulat de SPIP (fouiller dans les sources
 * pour comprendre. Je ne sais pas pourquoi ça fonctionne)
 * Ici, les arguments transmis correspondent aux paramètres
 * dans le modèle lien_archive_rubrique.html,
 * eux-mêmes transmis en paramètres dans le corps de l'article
 * 
 * @return $url-path string de l'archive archive
 * 	ex.: '/img/zip/Titre-rubrique-u.zip'
 **/
function calculer_URL_ARCHIVE()
{
	static $myjobs = array();
	
	// Désactiver si en espace privé
	if(test_espace_prive()) { return('#'); }

	// Commence par vérifier si le script shell existe
	if(!file_exists(SIA_SCRIPT_FILE))
	{
		sia_error_log('Error: '.SIA_SCRIPT_FILE.' missing');
		return(false);
	}
	// et s'il est exécutable
	else if(!is_executable(SIA_SCRIPT_FILE))
	{
		sia_error_log('Error: '.SIA_SCRIPT_FILE.' is not an executable file');
		return(false);
	}
	
	// le lien de l'archive zip transmise en retour
	$url_zip = false;
	
	// $objet peut être rubrique ou article
	// (ou autre, si vous écrivez le skel qui va)
	$objet = false;
	
	// le site cible
	$url_site = lire_meta('adresse_site') || ('http://'.$_SERVER['HTTP_HOST']);
	$url_site = trim($url_site,'/').'/';
	
	// par défaut, archive en une seule page
	$type = SIA_TYPE_UNIQUE;
	
	// les options complémentaires (via cfg)
	if(function_exists('lire_config'))
	{
		$random_wait = sia_cfg_option_on('random_wait');
		$strict_mode = sia_cfg_option_on('strict_mode');
		$simulation_mode = sia_cfg_option_on('simulation_mode');
			
		$user_agent =
			(
				($ii = lire_config('sia/user_agent'))
				&& ($ii = trim($ii))
				&& (!empty($ii))
			)
			? substr($ii, 0, 31)
			: null
			;
			
		$level =
			(
				($ii = lire_config('sia/level'))
				&& ($ii >= 1)
				&& ($ii <= 5)
			)
			? $ii
			: null
			;
	}
	
	// pour le moment, 3 args acceptés
	for($ii = 0; $ii<3; $ii++)
	{
		// dépiler l'argument
		if(!($arg = func_get_arg($ii)))
		{
			break;
		}
		
		// si correct, traiter
		if(strpos($arg, '='))
		{
			list($key, $val) = explode('=', $arg);
			$val = trim($val);
		
			switch($key)
			{
				case 'id_article':
				case 'id_rubrique':
					$objet = $key;
					$id_objet = $val;
					break;
				case 'type':
					// type d'archive
					// Les 4 premiers caractères suffisent.
					$val = substr($val,0,4);
					if($val == 'mult')
					{
						$type = SIA_TYPE_MULTIPLE;
					}
					else if($val == 'uniq')
					{
						$type = SIA_TYPE_UNIQUE;
					}
					else if($val == 'text')
					{
						$type = SIA_TYPE_TEXTE;
					}
					break;
				case 'url_site':
					$url_site = $val;
					break;
			}
		}
	}

	// les commandes systèmes nécessaires
	$c = array_flip(array('batch', 'wget', 'zip'));
	$commandes_ok = true;
	foreach(array_keys($c) as $key)
	{
		// $batch, $wget et $zip

		if(!($$key = sia_chemin_exec($key)))
		{
			sia_error_log('Error: command not found: '.$key);
			if(!isset($simulation_mode) || ($simulation_mode != 'on'))
			{
				$commandes_ok = false;
			}
		}
		else
		{
			$$key .= ' ';
		}
	}

	if($commandes_ok)
	{
		if($id_objet > 0)
		{
			// rep des images
			$rep = _NOM_PERMANENTS_ACCESSIBLES;
			
			if(is_writable($rep))
			{	
				// le rep contenant les archives zip
				$rep .= 'zip/';
				
				// si répertoire manquant, le créer
				if(file_exists($rep) || mkdir($rep))
				{
					// Définir le diminutif du site
					if($s = lire_meta('nom_site'))
					{
						$t = strtolower($s);
						
						// caractères fr supl. de mots
						// @see: http://www.quesaco.org/Compter-les-mots-dans-un-chaine-PHP
						$chars = "àâæçéèêëïîôœùüûÿ0123456789-'";
						
						if(str_word_count($t, 0, $chars) > 1)
						{
							// Plusieurs mots ?
							// Ne prendre que les capitales
							$u = preg_replace('/[^A-Z]/', '', $s);
							if(strlen($u)<2)
							{
								// Trop petit !
								// Construire l'acronyme à partir
								// du nom du site
								$s = '';
								foreach(explode(' ', $t) as $val)
								{
									$s .= trim(substr($val, 0, 1));
								}
							}
							else
							{
								$s = strtolower($u);
							}
						}
						// ne prendre que les 8 premiers car. (max)
						$prefix = substr($s, 0, 8);
					}
					else
					{
						// nom par défaut si champ vide
						$prefix = 'site';
					}
					
					// nom de l'archive
					$name = sia_titre_objet($objet, $id_objet, $prefix, $type);
					
					// sa version zippée, pour vérifier péremption
					$ziped = $name.'.zip';
					
					// chemin complet pour URL affichée en public
					$url_zip = $rep.$ziped;
					
					// le lock, pour éviter process en //
					$lock = _NOM_TEMPORAIRES_INACCESSIBLES . $name.'.lock';
					
					// le todo: les params de l'archive
					$todo_file = _NOM_TEMPORAIRES_INACCESSIBLES . $name.'.todo';
					
					// le lock est géré par le script shell.
					//
					// Il est créé ici pour éviter de re-écrire
					// le todo à chaque hit de page, inutilement.
					//
					// Le script shell scrute les *lock disponibles
					// et le todo qui lui est attaché.
					//
					// Puis il lit le todo,
					// vérifie si c'est un sia (première ligne)
					//
					// Enfin, il supprime le lock et le todo
					// lorsque la tâche est terminée.
					
					// Si lock existe, tâche en cours. Abandon.
					// sinon, faire le job.
					if(!file_exists($lock))
					{
						// SPIP passe 3 fois ici.
						// Pourquoi ?
						
						if(file_exists($f = $rep.$ziped))
						{
							// si fichier zip existe, noter son age
							$last_archive_time = filemtime($f);
						
							// global, dernière modif du site.
							// Incorrect pour une date de modif rubrique seule.
							//$derniere_modif = lire_meta('derniere_modif');
							
							// dernière date de modification texte
							// pour la rubrique ou l'article concerné.
							$last_edition_time = sia_time_modif_objet($objet, $id_objet);
							
							if($forcer_calcul = ($c = _request('var_mode'))
									&& (strpos($c, 'calcul') !== false))
							{
								sia_log('Force new archive ['.$name.'] via request_uri (var_mode='.$c.')');
							}
							
							$do_update =
								// si l'archive est obsolète
								($last_archive_time < $last_edition_time)
								// ou si recalcul demandé en URI
								|| $forcer_calcul;
						}
						else
						{
							sia_log('Target archive missing. Create it. ('.$f.')');
							$do_update = true;
						}
						
						if($do_update)
						{
							// Mettre le verrou (fichier vide, qui contiendra
							// plus tard le pid du shell)
							if(touch($lock))
							{
								// Placer les paramètres de l'archivage en todo
								if(touch($todo_file))
								{
									// l'url pour le wget
									$targeturl = $url_site;
									
									$arg_sep = ini_get('arg_separator.output'); 
									
									switch($type)
									{
										case SIA_TYPE_TEXTE:
											$targeturl .= SIA_URI_SKEL_TEXTE
												. $arg_sep
												. $objet.'='.$id_objet;
											break;
										case SIA_TYPE_UNIQUE:
											$targeturl .= SIA_URI_SKEL_HTML_UNIQUE
												. $arg_sep
												. $objet.'='.$id_objet;
											break;
										default: //case SIA_TYPE_MULTIPLE:
											$targeturl .= SIA_URI_SKEL_HTML_MULTI;
											// écriture classique Spip
											switch($objet)
											{
												case 'id_rubrique':
													$targeturl .= 'rubrique'.$id_objet;
													break;
												case 'id_article':
													$targeturl .= 'article'.$id_objet;
													break;
											}
											break;
									}
									
									$revision = str_replace('$', '', '$LastChangedRevision$');
									
									//sia_log('Selected type: '.$type);
									//sia_log('Target site: '.$url_site);
									
									$myjobs[] = $name;
									sia_log($name.' : This archive must be updated.');
									
									file_put_contents(
											  $todo_file
											,	  'sia: '.$revision.PHP_EOL
												. 'siajobname: '.$name.PHP_EOL
												. 'destdir: '.$rep.PHP_EOL
												. 'tmpdir: '._NOM_TEMPORAIRES_INACCESSIBLES.PHP_EOL
												. 'lockfile: '.$lock.PHP_EOL
												. 'logdir: '._DIR_LOG.PHP_EOL
												. 'logsuf: '._FILE_LOG_SUFFIX.PHP_EOL
												. 'sialogsdir: '.SIA_LOGS_DIR.PHP_EOL
												. 'type: '.$type.PHP_EOL
												. 'level: '.$level.PHP_EOL
												. 'targeturl: '.$targeturl.PHP_EOL
												. 'iphost: '.$_SERVER['SERVER_ADDR'].PHP_EOL
												. 'wget: '.$wget.PHP_EOL
												. ($random_wait ? 'randomwait: '.$random_wait.PHP_EOL : '')
												. ($strict_mode ? 'strict: '.$strict_mode.PHP_EOL : '')
												. ($simulation_mode ? 'simulation: '.$simulation_mode.PHP_EOL : '')
												. ($user_agent ? 'useragent: '.$user_agent.PHP_EOL : '')
											, LOCK_EX
											);
									
									if($simulation_mode && ($simulation_mode=='on'))
									{
										sia_log('Simulation mode! batch command not activated for this job. You probably have to activate it manually.');
									}
									else
									{
										// Appel du batch
										$exec_line = $batch.' -f '.SIA_SCRIPT_FILE.SIA_SCRIPT_OPTIONS;
										
										sia_log($exec_line);
										$string = system($exec_line, $r);
										
										if($r != 0)
										// si erreur, signaler en console
										{
											sia_error_log(
												$exec.' : '
												. (empty($string) ? 'Unknown error '.$r : 'Error: '.$string)
											);
										}
										else
										// sinon, confirmer en donnat ID du job
										{
											// ID du prochain job
											$string = sia_passthru('atq -v | tail -n 1');
											sia_log('Next job: '.trim($string));
										}
									} // fin ($simulation_mode && ($simulation_mode=='on'))
								} // fin if(touch($todo_file))
								else
								{
									sia_error_log($todo_file.' not writable');
								}
							} // fin if(touch($lock))
							else
							{
								sia_error_log($lock.' not writable');
							}
						} // if($do_update)
						else
						{
							if(!in_array($name, $myjobs))
							{
								$myjobs[] = $name;
								
								//sia_log('Last edition/archive: '
								//		. date(DATE_ATOM, $last_edition_time)
								//		. '/'
								//		. date(DATE_ATOM, $last_archive_time));
								
								sia_log($name.' : No update needed.');
							}
						} // fin if($do_update)
					} // fin file_exists($lock)
					else
					{
						if(!in_array($name, $myjobs))
						{
							$myjobs[] = $name;
								
							sia_log($name.' already locked.');
						}
					}
				} 
				else
				{
					sia_error_log('Error: '.$rep.' not found');
				} // fin if(file_exists($rep) || mkdir($rep))
			} 
			else
			{
				sia_error_log('Error: '.$rep.' not found');
			} // fin if(is_writable($rep))
		}
		else
		{
			sia_error_log('Error: '.$objet.' : '.$id_objet);
		} // fin if($id_objet)
	}
	
	// complète l'url (sera placé option + tard)
	if($url_zip)
	{
		$s = lire_meta('adresse_site');
		$s = ($s) 
			? rtrim($s, '/')
			: 'http://localhost'
			;
		$url_zip = $s.'/'.$url_zip;
	}
	
	return($url_zip);
}

/** La balise active l'archivage.
 * Placer la balise #URL_ARCHIVE dans la page des liens d'archives
 * @return string l'url de l'archive zip
 * */
function balise_URL_ARCHIVE($p)
{
	($arg1 = interprete_argument_balise(1,$p))
	&& ($arg2 = interprete_argument_balise(2,$p))
	&& ($arg3 = interprete_argument_balise(3,$p));
	
	$p->code = "calculer_URL_ARCHIVE($arg1,$arg2,$arg3)";
	$p->interdire_scripts = false;
	return($p);
}
