<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// l'actionneur calcule l'ordre des actions
// et permet de les stocker et de les effectuer.

class Actionneur {

	var $decideur;

	// loggue t'on ?
	var $log = false;

	// actions au debut (avant analyse)
	var $start = array();

	// actions en cours d'analyse
	var $middle = array(
		'off' => array(),
		'lib' => array(),
		'on' => array(),
		'neutre' => array(),
	);

	// actions a la fin (apres analyse, et dans l'ordre)
	var $end = array();  // a faire...
	var $done = array(); // faites
	var $work = array(); // en cours

	function Actionneur(){
		include_spip('inc/step_decideur');
		$this->decideur = new Decideur();
		$this->decideur->start();
	}


	function log($quoi) {
		if ($this->log) {
			spip_log($quoi,'actionneur');
		}
	}

	function clear() {
		$this->middle = array(
			'off' => array(),
			'lib' => array(),
			'on' => array(),
			'neutre' => array(),
		);
		$this->end = array();
		$this->done = array();
		$this->work = array();
	}

	function ajouter_actions($todo) {
		foreach ($todo as $id => $action) {
			$this->start[$id] = $action;
		}
		$this->ordonner_actions();
	}


	// ajouter une librairie a installer.
	function add_lib($nom, $source) {
		if (!$this->decideur->est_presente_lib($nom)) {
			if (is_writable(_DIR_LIB)) {
				$this->middle['lib'][$nom] = array(
					'todo'=>'lib',
					'n'=>$nom,
					'p'=>$nom,
					'v'=>$source,
					's'=>$source,
				);
			} else {
				// erreur : impossible d'ecrire dans _DIR_LIB !
				// TODO : message et retour d'erreur a gerer...
				return false;
			}
		}
		return true;
	}


	function ordonner_actions() {
		// il faut deja definir quels sont des
		// actions graduellement realisables.
		// Pour tout ce qui est a installer : ordre des dependances
		// Pour tout ce qui est a desinstaller : ordre inverse des dependances.

		// on commence par separer
		// - ce qui est a desinstaller.
		// - ce qui est a installe
		// - les actions neutres (get, up sur non actif, kill)

		// on commencera par faire ce qui est a desinstaller
		// (il est possible que certains plugins necessitent la desinstallation
		//  d'autres present - tel que : 1 seul service d'envoi de mail)
		// puis ce qui est a installer
		// puis les actions neutres
		$this->clear();

		foreach ($this->start as $id=>$action) {
			$i = $this->decideur->infos_courtes_id($id);
			$i = $i['i'][$id];
			switch ($action) {
				case 'geton':
				case 'on':
					$this->on($i, $action);
					break;
				case 'up':
					if ($i['a'] == 'oui') {
						$this->on($i, $action);
					} else {
						$this->neutre($i, $action);
					}
					break;
				case 'upon':
					$this->on($i, $action);
					break;
				case 'off':
				case 'stop':
					$this->off($i, $action);
					break;
				case 'get':
				case 'kill':
					$this->neutre($i, $action);
					break;
			}
		}

		// c'est termine, on passe tout dans la fin...
		foreach ($this->middle as $acts) {
			$this->end = array_merge($this->end, $acts);
		}
		$this->log("------------");
		#$$this->log("Fin du tri :");
		#$this->log($this->end);
	}


	// a chaque fois qu'une action arrive,
	// on compare avec celles deja presentes
	// pour savoir si on doit la traiter avant ou apres

	function on($info, $action) {
		$info['todo'] = $action;
		$p = $info['p'];
		$this->log("ON: $p $action");

		// si dependance, il faut le mettre avant !
		$in = $out = $deps = $deps_all = array();
		// raz des cles pour avoir les memes que $out (utile reellement ?)
		$this->middle['on'] = array_values($this->middle['on']);
		// ajout des dependance et des librairies si besion
		foreach ($info['dn'] as $dep) {
			if ($dep['id'] != 'SPIP') {
				if (strpos($dep['id'],'lib:')===0) {
					if ($lib = substr($dep['id'], 4)) {
						// il faudrait gerer un retour d'erreur eventuel !
						$this->add_lib($lib, $dep['src']);
					}
				} else {
					$in[]  = $dep['id'];
				}
			}
		}

		// on recupere : tous les prefix de plugin a activer (out)
		// ie. ce plugin peut dependre d'un de ceux la
		//
		// ainsi que les dependences de ces plugins (deps)
		// ie. ces plugins peuvent dependre de ce nouveau a activer.
		foreach ($this->middle['on'] as $inf) {
			$out[] = $inf['p'];
			foreach ($inf['dn'] as $dep) {
				if ($dep['id'] != 'SPIP') {
					if (strpos($dep['id'],'lib:')!==0) {
						$deps[$inf['p']][] = $dep['id'];
						$deps_all[] = $dep['id'];
					}
				}
			}
		}

		if (!$in) {

			// pas de dependance, on le met en premier !
			$this->log("- placer $p tout en haut");
			array_unshift($this->middle['on'], $info);

		} else {

			// intersection = dependance presente aussi
			// on place notre action juste apres la derniere dependance
			if ($diff = array_intersect($in, $out)) {
				$key = array();
				foreach($diff as $d) {$key[] = array_search($d, $out);}
				$key = max($key);
				$this->log("- placer $p apres " . $this->middle['on'][$key]['p']);
				if ($key == count($this->middle['on'])) {
					$this->middle['on'][] = $info;
				} else {
					array_splice($this->middle['on'], $key+1, 0, array($info));
				}

			// intersection = plugin dependant de celui-ci
			// on place notre plugin juste avant la premiere dependance a lui trouvee
			} elseif (in_array($p, $deps_all)) {
				foreach ($deps as $prefix=>$dep) {
					if (in_array($p, $dep)) {
						$key = array_search($prefix, $out);
						$this->log("- placer $p avant $prefix qui en depend ($key)");
						if ($key == 0) {
							array_unshift($this->middle['on'], $info);
						} else {
							array_splice($this->middle['on'], $key, 0, array($info));
						}
						break;
					}
				}

			// rien de particulier, il a des dependances mais les plugins
			// ne sont pas encore la ou les dependances sont deja actives
			// donc on le place tout en bas
			} else {
				$this->log("- placer $p tout en bas");
				$this->middle['on'][] = $info;
			}
		}
		unset($diff, $in, $out);
	}


	function neutre($info, $action) {
		$info['todo'] = $action;
		$this->log("NEUTRE:  $info[p] $action");
		$this->middle['neutre'][] = $info;
	}


	function off($info, $action) {
		$info['todo'] = $action;
		$p = $info['p'];
		$this->log("OFF: $p $action");

		// si dependance, il faut le mettre avant !
		$in = $out = array();
		// raz des cles pour avoir les memes que $out (utile reellement ?)
		$this->middle['off'] = array_values($this->middle['off']);
		foreach ($info['dn'] as $dep) 			{
			if (($dep['id'] != 'SPIP') and (strpos($dep['id'],'lib:')!==0)) {
				$in[]  = $dep['id'];
			}
		}
		foreach ($this->middle['off'] as $inf) 	{$out[] = $inf['p'];}

		if (!$in) {
			// pas de dependance, on le met en dernier !
				$this->log("- placer $p tout en bas");
				$this->middle['off'][] = $info;
		} else {
			// intersection = dependance presente aussi
			// on place notre action juste avant la premiere dependance
			if ($diff = array_intersect($in, $out)) {
				$key = array();
				foreach($diff as $d) {$key[] = array_search($d, $out);}
				$key = min($key);
				$this->log("- placer $p avant " . $this->middle['off'][$key]['p']);
				array_splice($this->middle['off'], $key, 0, array($info));
			} else {
				// pas de dependance, on le met en premier !
				$this->log("- placer $p tout en haut");
				array_unshift($this->middle['on'], $info);
			}
		}
		unset($diff, $in, $out);
	}


	function presenter_actions() {
		$affiche = "";
		if ($this->end or $this->done) {
			$affiche .= "<ul>";
			foreach ($this->done as $i) {
				if(is_string($i['done'])){
					$ajouts_message = "<br />".$i['done'];
				}
				$affiche .= "\t<li>"._T('step:message_action_finale_'.$i['todo'].'_'.($i['done']?'ok':'fail'),array('plugin'=>$i[n],'version'=>$i[v])).$ajouts_message."</li>\n";
			}
			foreach ($this->end as $i) {
				$affiche .= "\t<li>"._T('step:message_action_'.$i['todo'],array('plugin'=>$i[n],'version'=>$i[v]))."</li>\n";
			}
			$affiche .= "</ul>\n";
		}
		return $affiche;
	}


	function sauver_actions() {
		$contenu = serialize(array(
			'todo' => $this->end,
			'done' => $this->done,
			'work' => $this->work,
		));
		ecrire_fichier(_DIR_TMP . 'step_actions.txt', $contenu);
	}


	function get_actions() {
		lire_fichier(_DIR_TMP . 'step_actions.txt', $contenu);
		$infos = unserialize($contenu);
		$this->end  = $infos['todo'];
		$this->work = $infos['work'];
		$this->done = $infos['done'];
	}


	function one_action() {
		if (count($this->end)) {
			$action = $this->work = array_shift($this->end);
			$this->sauver_actions();
			$this->do_action();
			return $action;
		}
		return false;
	}

	function do_action() {
		if ($do = $this->work) {
			$todo = 'do_' . $do['todo'];
			lire_metas(); // avoir les metas a jour
			$do['done'] = $this->$todo($do);
			$this->done[] = $do;
			$this->work = array();
			$this->sauver_actions();
		}
	}


	// activer un plugin
	// soit il est la... soit il est a telecharger...
	function do_on($info) {
		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));
		if ($i['id_zone'] > 0) {
			// a telecharger et activer
			if ($dirs = $this->get_paquet_id($i)) {
				$this->activer_plugin_dossier($dirs['dossier'], $i);
				return true;
			}
		} else {
			// a activer uniquement
			// il faudra prendre en compte les autres _DIR_xx
			if (in_array($i['constante'],array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
				$dossier = rtrim($i['dossier'],'/');
				$this->activer_plugin_dossier($dossier, $i, $i['constante']);
				return true;
			}
		}
		return false;
	}



	// mettre a jour un plugin
	function do_up($info) {
		// ecriture du nouveau
		// suppression de l'ancien (si dans auto, et pas au meme endroit)
		// OU suppression des anciens fichiers
		if (!defined('_DIR_PLUGINS_AUTO') or !_DIR_PLUGINS_AUTO or !is_writable(_DIR_PLUGINS_AUTO)) {
			$this->log("Pas de _DIR_PLUGINS_AUTO defini !");
			return false;
		}

		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));

		// on cherche la mise a jour...
		if ($maj = sql_fetsel('*','spip_plugins',array(
			'prefixe='.sql_quote($info['p']),
			'version='.sql_quote($info['maj']),
			'superieur='.sql_quote('oui')))) {
				if ($dirs = $this->get_paquet_id($maj)) {
					// Si le plugin a jour n'est pas dans le meme dossier que l'ancien...
					// il faut :
					// - activer le plugin sur son nouvel emplacement (uniquement si l'ancien est actif)...
					// - supprimer l'ancien (si faisable)
					if (($dirs['dossier'] . '/') != $i['dossier']) {
						if ($i['actif'] == 'oui') {
							$this->activer_plugin_dossier($dirs['dossier'], $maj);
						}

						if (substr($i['dossier'],0,5) == 'auto/') {
							if ($this->deleteDirectory($dirs['dir'])) {
								sql_delete('spip_plugins', 'id_plugin=' . sql_quote($info['i']));
							}
						}
					}

					$this->ajouter_plugin_interessants_meta($dirs['dossier']);
					return $dirs;
				}
		}
		return false;
	}


	// mettre a jour et activer un plugin
	function do_upon($info) {
		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));
		if ($dirs = $this->do_up($info)) {
			$this->activer_plugin_dossier($dirs['dossier'], $i, $i['constante']);
			return true;
		}
		return false;
	}


	// desactiver un plugin
	function do_off($info) {
		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));
		// il faudra prendre en compte les autres _DIR_xx
		if (in_array($i['constante'],array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
			include_spip('inc/plugin');
			$dossier = ($i['constante'] == '_DIR_PLUGINS')? $i['dossier'] : '../'.constant($i['constante']).$i['dossier'];
			ecrire_plugin_actifs(array(rtrim($dossier,'/')), false, 'enleve');
			sql_updateq('spip_plugins', array('actif'=>'non', 'installe'=>'non'), 'id_plugin='.sql_quote($info['i']));
			$this->actualiser_plugin_interessants();
			// ce retour est un rien faux...
			// il faudrait que la fonction ecrire_plugin_actifs()
			// retourne au moins d'eventuels message d'erreur !
			return true;
		}
		return false;
	}


	// desinstaller un plugin
	function do_stop($info) {
		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));
		// il faudra prendre en compte les autres _DIR_xx
		if (in_array($i['constante'],array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
			include_spip('inc/plugin');
			include_spip('inc/step');
			$dossier = rtrim($i['dossier'],'/');
			$constante = $i['constante'];
			$plugin_get_infos = charger_fonction('get_infos', 'plugins');
			if($i['constante'] == '_DIR_PLUGINS_SUPPL')
				$constante = _DIR_RACINE.constant($i['constante']);
			else 
				$constante = constant($i['constante']);
			$infos = $plugin_get_infos($dossier,false,$constante);

			if (isset($infos['install'])){
				
				// desinstaller
				$dossier = ($i['constante'] == '_DIR_PLUGINS')? $dossier : '../'.constant($i['constante']).$dossier;
				$etat = desinstalle_un_plugin($dossier, $infos);

				// desactiver si il a bien ete desinstalle
				if (!$etat) {
					ecrire_plugin_actifs(array($dossier), false, 'enleve');
					sql_updateq('spip_plugins', array('actif'=>'non', 'installe'=>'non'), 'id_plugin='.sql_quote($info['i']));
					return true;
				}
				// echec de la desinstallation
			}
			// pas de desinstallation possible !
		}
		$this->actualiser_plugin_interessants();
		return false;
	}


	// effacer les fichiers d'un plugin
	function do_kill($info) {
		// on reverifie que c'est bien un plugin auto !
		// il faudrait aussi faire tres attention sur un site mutualise
		// cette option est encore plus delicate que les autres...
		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));

		if (in_array($i['constante'],array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))
		and substr($i['dossier'],0,5) == 'auto/') {
			$dir = constant($i['constante']) . $i['dossier'];
			if ($this->deleteDirectory($dir)) {
				sql_delete('spip_plugins', 'id_plugin=' . sql_quote($info['i']));
				return true;
			}
		}

		return false;
	}


	// installer une librairie
	function do_lib($info) {
		if (!defined('_DIR_LIB') or !_DIR_LIB or !is_writable(_DIR_LIB)) {
			$this->log("Pas de _DIR_LIB defini !");
			return false;
		}

		$zip = $info['s'];
		if ($files = $this->get_zip($zip, _DIR_LIB)) {
			$dest = $files[0]['stored_filename'];
			$dest = rtrim($dest, '/');
			$dir = _DIR_LIB . $dest;

			$this->log("Suppression des anciens fichiers de $dir");
			$this->remove_older_files($dir, $files);

			return true;
		}

		return false;
	}


	// telecharger un plugin
	function do_get($info) {
		if (!defined('_DIR_PLUGINS_AUTO') or !_DIR_PLUGINS_AUTO or !is_writable(_DIR_PLUGINS_AUTO)) {
			$this->log("Pas de _DIR_PLUGINS_AUTO defini !");
			return false;
		}

		$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($info['i']));
		if ($i['paquet']) {
			if ($adresse = sql_getfetsel('adresse','spip_zones_plugins','id_zone='.sql_quote($i['id_zone']))) {
				$adresse = dirname($adresse);
				$zip = $adresse . '/' . $i['paquet'];
				if ($files = $this->get_zip($zip, _DIR_PLUGINS_AUTO)) {
					$dest = $files[0]['stored_filename'];
					// rendre obsolete ce paquet distant
					// (ca le fera tout seul au moment d'actualiser les paquets locaux)
					// trouver le nouveau paquet et le mettre dans les interessants...
					$dest = 'auto/' . rtrim($dest, '/');
					$this->ajouter_plugin_interessants_meta($dest);
					
					// c'est la ou _DIR_PLUGINS_AUTO
					// ne sert pas a grand chose... a ameliorer
					return true;
				}
			}
		}

		return false;
	}



	// lancer l'installation d'un plugin
	function do_install($info) {
		include_spip('inc/plugin');
		$message_install = $this->installe_plugin($info);
		return $message_install;
	}


	// adresse du dossier, et row SQL du plugin en question
	function activer_plugin_dossier($dossier, $i, $constante='_DIR_PLUGINS') {
		include_spip('inc/plugin');

		//il faut absolument que tous les fichiers de cache
		// soient inclus avant modification, sinon un appel ulterieur risquerait
		// de charger des fichiers deja charges par un autre !
		// C'est surtout le ficher de fonction le probleme (options et pipelines
		// sont normalement deja charges).
		if (@is_readable(_CACHE_PLUGINS_OPT)) {include_once(_CACHE_PLUGINS_OPT);}
		if (@is_readable(_CACHE_PLUGINS_FCT)) {include_once(_CACHE_PLUGINS_FCT);}
		if (@is_readable(_CACHE_PIPELINES))   {include_once(_CACHE_PIPELINES);}

		$dossier = ($constante == '_DIR_PLUGINS')? $dossier : '../'.constant($constante).$dossier;
		ecrire_plugin_actifs(array($dossier), false, 'ajoute');
		$installe = $i['version_base'] ? 'oui' : 'non';
		if ($installe == 'oui') {
			if(!$i['constante'])
				$i['constante'] = '_DIR_PLUGINS';
			// installer le plugin au prochain tour
			$new_action = array_merge($this->work, array(
				'todo'=>'install',
				'dossier'=>rtrim($i['dossier'],'/'),
				'constante'=>$i['constante']
			));
			array_unshift($this->end, $new_action);
			#$this->installe_plugin($dossier);
		}

		$this->ajouter_plugin_interessants_meta($dossier);
		$this->actualiser_plugin_interessants();
	}


	// actualiser les plugins interessants
	function actualiser_plugin_interessants() {
		// Chaque fois que l'on valide des plugins,
		// on memorise la liste de ces plugins comme etant "interessants",
		// avec un score initial, qui sera decremente a chaque tour :
		// ainsi un plugin active pourra reter visible a l'ecran,
		// jusqu'a ce qu'il tombe dans l'oubli.
		$plugins_interessants = @unserialize($GLOBALS['meta']['plugins_interessants']);
		if (!is_array($plugins_interessants)) {
			$plugins_interessants = array();
		}
		$dossiers = array();
		foreach($plugins_interessants as $p => $score) {
			if (--$score > 0) {
				$plugins_interessants[$p] = $score;
				$dossiers[$p.'/'] = true;
			} else {
				unset($plugins_interessants[$p]);
				// ATTENTION, il faudra prendre en compte les _DIR_xx
				sql_updateq('spip_plugins',array('recent'=>0),'dossier='.sql_quote($p));
			}
		}

		$plugs = sql_select('dossier','spip_plugins','actif='.sql_quote('oui'));

		while ($plug = sql_fetch($plugs)) {
			$dossiers[$plug['dossier']] = true;
			$plugins_interessants[ rtrim($plug['dossier'],'/') ] = 30; // score initial
		}

		$plugs = sql_updateq('spip_plugins', array('recent'=>1), sql_in('dossier', array_keys($dossiers)));
		ecrire_meta('plugins_interessants', serialize($plugins_interessants));
	}



	function ajouter_plugin_interessants_meta($dir) {
		$plugins_interessants = @unserialize($GLOBALS['meta']['plugins_interessants']);
		if (!is_array($plugins_interessants)) {
			$plugins_interessants = array();
		}
		$plugins_interessants[$dir] = 30;
		ecrire_meta('plugins_interessants', serialize($plugins_interessants));
	}


	function installe_plugin($info){
		$plugin_get_infos = charger_fonction('get_infos', 'plugins');
		if($info['constante'] == '_DIR_PLUGINS_SUPPL')
			$constante = _DIR_RACINE.constant($info['constante']);
		else
			$constante = constant($info['constante']);
		
		$infos = $plugin_get_infos($info['dossier'],false,$constante);
		if (isset($infos['install'])) {
			ob_start();
			include_spip('inc/step');
			$dossier = ($info['constante'] == '_DIR_PLUGINS')? $info['dossier'] : '../'.constant($info['constante']).$info['dossier'];
			if (installe_un_plugin($dossier, $infos)) {
				$meta_plug_installes = @unserialize($GLOBALS['meta']['plugin_installes']);
				if (!$meta_plug_installes) $meta_plug_installes=array();
				$meta_plug_installes[] = $dossier;
				ecrire_meta('plugin_installes',serialize($meta_plug_installes), 'non');
				//$messages = preg_replace('/<div.*(install-plugins).*<\/div>/','',ob_get_contents());
				ob_end_clean();
				return true;
			}
			ob_end_clean();
		}
		return false;
	}



	// serait mieux dans inc/flock...
	// necessite PHP 5
	// vient de http://www.php.net/manual/en/function.rmdir.php#92050
	function deleteDirectory($dir) {
		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
			if (!$this->deleteDirectory($dir . "/" . $item)) {
				chmod($dir . "/" . $item, 0777);
				if (!$this->deleteDirectory($dir . "/" . $item)) return false;
			};
		}
		return rmdir($dir);
	}


	// telecharge un paquet
	// et supprime les fichiers obsoletes (si presents)
	function get_paquet_id($id_or_row) {
		// on peut passer direct le row sql...
		if (!is_array($id_or_row)) {
			$i = sql_fetsel('*','spip_plugins','id_plugin='.sql_quote($id_or_row));
		} else {
			$i = $id_or_row;
		}
		unset($id_or_row);

		if ($i['paquet']) {
			if ($adresse = sql_getfetsel('adresse','spip_zones_plugins','id_zone='.sql_quote($i['id_zone']))) {
				$adresse = dirname($adresse);
				$zip = $adresse . '/' . $i['paquet'];
				// on recupere la mise a jour...
				if ($files = $this->get_zip($zip, _DIR_PLUGINS_AUTO)) {
					$dest = $files[0]['stored_filename'];
					$dest = 'auto/' . rtrim($dest, '/');
					$dir = _DIR_PLUGINS . $dest;

					// la c'est ennuyant : il faut supprimer les vieux fichiers...
					$this->log("Suppression des anciens fichiers de $dir");
					$this->remove_older_files($dir, $files);

					return array(
						'dir'=>$dir,
						'dossier'=>$dest,
					);
				}
			}
		}
		return false;
	}


	function get_zip($zip, $dir_dest) {

		# si premiere lecture, destination temporaire des fichiers
		$tmp = sous_repertoire(_DIR_CACHE, 'chargeur');

		# $extract = sous_repertoire($tmp, 'extract');
		$extract = $dir_dest;

		$fichier = $tmp . basename($zip);
		include_spip('inc/distant');
		$contenu = recuperer_page($zip, $fichier, false, _COPIE_LOCALE_MAX_SIZE);
		if (!$contenu) {
			$this->log('Impossible de charger : '. $zip);
			return false;
		}

		include_spip('inc/pclzip');
		$uzip = new PclZip($fichier);

		// On extrait, mais dans tmp/
		$ok = $uzip->extract(
			PCLZIP_OPT_PATH,
			$extract,
			PCLZIP_OPT_SET_CHMOD,
			_SPIP_CHMOD,
			PCLZIP_OPT_REPLACE_NEWER
		);

		if ($uzip->error_code < 0) {
			$this->log('Impossible de decompresser : '. $zip);
			$this->log('> erreur '. $uzip->error_code .' : ' . $uzip->errorName(true));
			return false;
		}

		# spip_log($ok, 'ok');
		# spip_log($uzip->listContent(), 'ok');
		// ok contient toute la liste des fichiers installes :)
		// ainsi que leur localisation et quelques infos.
		// [0] est le nom du premier repertoire
		/*
			  0 =>
			  array (
				'filename' => '../plugins/auto/aa/',
				'stored_filename' => 'aa/',
				'size' => 0,
				'compressed_size' => 0,
				'mtime' => 1262386900,
				'comment' => '',
				'folder' => true,
				'index' => 0,
				'status' => 'ok',
			  ),
		*/
		return $ok;
	}


	function remove_older_files($dir, $files = array()) {
		static $ok = false;
		if ($ok === false) {
			$ok = array();
			foreach ($files as $f) {
				$ok[$f['filename']] = true;
			}
		}

		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) {
			if (!isset($ok[$dir])) {
				$this->log('- supp :' . $dir);
				unlink($dir);
			}
			return true;
		}

		$dir = rtrim($dir, '/');
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
			$this->remove_older_files($dir . "/" . $item);
		}

		return true;
	}

}

// scandir pour php4
// http://fr2.php.net/manual/fr/function.scandir.php#73062
if (!function_exists('scandir')) {
function scandir($dir, $listDirectories=false, $skipDots=true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != "..") || $skipDots == true) {
                if($listDirectories == false) { if(is_dir($file)) { continue; } }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    return $dirArray;
}
}

?>
