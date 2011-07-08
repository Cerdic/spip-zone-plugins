<?php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
 * @author: cpaulus at quesaco.org
 */


if(!defined('_ECRIRE_INC_VERSION')) { return; }

// A l'installation, crée un répertoire 'sia' pour accueillir
// les logs de wget.

// A la désinstallation, supprime ce répertoire.

include_spip('sia_fonctions');

function sia_install($action)
{
	switch($action)
	{
		case 'install':
			$sh_ok = is_file(SIA_SCRIPT_FILE);
			$c = sia_command_available();
			/**
			 * Si le script sh est là
			 * et si les commandes dispo,
			 * installer
			 */
			if($sh_ok && !in_array(FALSE, $c))
			{
				chmod(SIA_SCRIPT_FILE, 0700);
				// Créer protection (attention, vérifiez votre
				// configuration apache !)
				if(!file_exists($f = SIA_BIN_FOLDER.'.htaccess'))
				{
					if(!file_put_contents($f, 'deny from all'))
					{
						spip_log(SIA_LOG_TAG.' error: '.$f);
					}
				}
				
				// Créer le répertoire des journaux
				$result = mkdir(SIA_LOGS_DIR, 0755);
				
				if($result)
				{
					if(function_exists('ecrire_config'))
					{
						if(is_null(lire_config('sia'))
						   || is_null(lire_config('sia/level'))
						)
						{
							// level est la seule var définie
							// par défaut
							ecrire_config('sia/level','1');
						}
					}
				}
			}
			/**
			 * Sinon, afficher message d'erreur
			 * dans la boite témoin.
			 */
			else
			{
				$msg = '';
				if (!$sh_ok) {
					$msg .= '<li>' . _T('sia:err_manque_script') . '</li>' . PHP_EOL;
				}
				if (in_array(FALSE, $c))
				{
					foreach ($c as $key => $value)
					{
						if ($value === FALSE)
						{
							$msg .= '<li>' . _T('sia:err_manque_command',
											 array('cde' => $key))
								. '</li>' . PHP_EOL
								;
						}
					}
				}
				if (!empty($msg))
				{
					$msg = '<ul>' . PHP_EOL
						. $msg
						. '</ul>' . PHP_EOL
						;
					echo($msg);
				}
			}
			sia_log('install '.($result ? 'ok' : 'err'));
			break;
		
		case 'test':
			$result = is_dir(SIA_LOGS_DIR);
			//sia_log('test '.($result ? 'ok' : 'err'));
			break;
		
		case 'uninstall':
			foreach(scandir(SIA_LOGS_DIR) as $item)
			{ 
				if($item == '.' || $item == '..')
				{
					continue;
				}
				$item = SIA_LOGS_DIR.$item;
				sia_log('unlink ' . $item . ' : ' . (unlink($item) ? 'Ok' : 'error'));
			}
			
			$result = rmdir(SIA_LOGS_DIR);
			
			if($result)
			{
				if(function_exists('effacer_config'))
				{
					effacer_config('sia');
				}
			}
			sia_log('uninstall '.($result ? 'ok' : 'err'));
			break;
	}
	return($result);
}
