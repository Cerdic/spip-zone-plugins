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

	// listing des erreurs rencontrées
	var $err = array();
	

	function Actionneur(){
		include_spip('inc/svp_decider');
		$this->decideur = new Decideur();
		$this->decideur->start();
	}


	/**
	 * Ajoute un log
	 * lorsqu'on a activé les logs sur notre objet
	 * $actionneur->log = true;
	 *
	 * @param string $quoi : le texte du log
	**/
	function log($quoi) {
		if ($this->log) {
			spip_log($quoi,'actionneur');
		}
	}

	/**
	 * Ajoute une erreur
	 * a la liste des erreurs presentees au moment de traiter les actions.
	 *
	 * @param string $erreur : le texte de l'erreur
	**/
	function err($erreur) {
		if ($erreur) {
			$this->err[] = $erreur;
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
					'todo'=>'getlib',
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
		// il faut deja definir quels sont les
		// actions graduellement realisables.
		// Pour tout ce qui est a installer : ordre des dependances
		// Pour tout ce qui est a desinstaller : ordre inverse des dependances.

		// on commence par separer
		// - ce qui est a desinstaller.
		// - ce qui est a installer
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
				case 'getlib':
					// le plugin en ayant besoin le fera
					// comme un grand...
					break;
				case 'geton':
				case 'on':
					$this->on($i, $action);
					break;
				case 'up':
					// si le plugin est actif
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
		// ajout des dependance
		foreach ($info['dn'] as $dep) {
			$in[]  = $dep['id'];
		}
		// ajout des librairies
		foreach ($info['dl'] as $lib) {
			// il faudrait gerer un retour d'erreur eventuel !
			$this->add_lib($lib['nom'], $lib['lien']);
		}

		// on recupere : tous les prefix de plugin a activer (out)
		// ie. ce plugin peut dependre d'un de ceux la
		//
		// ainsi que les dependences de ces plugins (deps)
		// ie. ces plugins peuvent dependre de ce nouveau a activer.
		foreach ($this->middle['on'] as $inf) {
			$out[] = $inf['p'];
			foreach ($inf['dn'] as $dep) {
				$deps[$inf['p']][] = $dep['id'];
				$deps_all[] = $dep['id'];
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
		foreach ($info['dn'] as $dep) {
			$in[]  = $dep['id'];
		}
		foreach ($this->middle['off'] as $inf) 	{
			$out[] = $inf['p'];
		}

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

		include_spip('inc/filtres_boites');
		
		if (count($this->err)) {
			$erreurs = "<ul>";
			foreach ($this->err as $i) {
				$erreurs .= "\t<li class='erreur'>" . $i . "</li>\n";
			}
			$erreurs .= "</ul>"; 
			$affiche .= boite_ouvrir(_T('svp:actions_en_erreur'), 'error') . $erreurs . boite_fermer();
		}

		if (count($this->done)) {
			$oks = true;
			$done .= "<ul>";
			foreach ($this->done as $i) {
				$ok = ($i['done'] ? true : false);
				$oks = &$ok;
				$ok_texte = $ok ? 'ok' : 'fail';
				$cle_t = 'svp:message_action_finale_' . $i['todo'] . '_' . $ok_texte;
				$texte = _T($cle_t, array('plugin' => $i['n'], 'version' => $i['v']));
				if (is_string($i['done'])) {
					$texte .= " <span class='$ok_texte'>$i[done]</span>";
				}
				$done .= "\t<li class='$ok_texte'>$texte</li>\n";
			}
			$done .= "</ul>";
			$affiche .= boite_ouvrir(_T('svp:actions_realises'), ($oks ? 'success' : 'notice')) . $done . boite_fermer();
		}

		if (count($this->end)) {
			$todo .= "<ul>";
			foreach ($this->end as $i) {
				$todo .= "\t<li>"._T('svp:message_action_'.$i['todo'],array('plugin'=>$i['n'],'version'=>$i['v']))."</li>\n";
			}
			$todo .= "</ul>\n";
			$affiche .= boite_ouvrir(_T('svp:actions_a_faire'), 'notice') . $todo . boite_fermer();
		}

		if ($affiche) {
			include_spip('inc/filtres');
			$affiche = wrap($affiche, "<div id='actionner'>");
		}
		
		return $affiche;
	}


	function sauver_actions() {
		$contenu = serialize(array(
			'todo' => $this->end,
			'done' => $this->done,
			'work' => $this->work,
			'err' => $this->err,
		));
		ecrire_fichier(_DIR_TMP . 'stp_actions.txt', $contenu);
	}


	function get_actions() {
		lire_fichier(_DIR_TMP . 'stp_actions.txt', $contenu);
		$infos = unserialize($contenu);
		$this->end  = $infos['todo'];
		$this->work = $infos['work'];
		$this->done = $infos['done'];
		$this->err  = $infos['err'];
	}


	/**
	 * Effectue une des actions qui reste a faire.  
	**/
	function one_action() {
		// s'il reste des actions, on en prend une, et on la fait.
		if (count($this->end)) {
			$action = $this->work = array_shift($this->end);
			$this->sauver_actions();
			$this->do_action();
			return $action;
		}
		return false;
	}

	/**
	 * Effectue l'action en attente.  
	**/
	function do_action() {
		if ($do = $this->work) {
			$todo = 'do_' . $do['todo'];
			lire_metas(); // avoir les metas a jour
			$this->log("Faire $todo avec $do[n]");
			$do['done'] = $this->$todo($do);
			$this->done[] = $do;
			$this->work = array();
			$this->sauver_actions();
		}
	}


	// attraper et activer un plugin
	function do_geton($info) {
		if (!$this->tester_repertoire_plugins_auto()) {
			return false;
		}
		$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($info['i']));
		if ($dirs = $this->get_paquet_id($i)) {
			$this->activer_plugin_dossier($dirs['dossier'], $i);
			return true;
		}
		
		$this->log("GetOn : Erreur de chargement du paquet " .$info['n']);
		return false;
	}

	// activer un plugin
	// soit il est la... soit il est a telecharger...
	function do_on($info) {
		$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($info['i']));
		if ($i['id_zone'] > 0) {
			return $this->do_geton($info);
		}
		
		// a activer uniquement
		// il faudra prendre en compte les autres _DIR_xx
		if (in_array($i['constante'], array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
			$dossier = rtrim($i['src_archive'], '/');
			$this->activer_plugin_dossier($dossier, $i, $i['constante']);
			return true;
		}
		
		return false;
	}



	// mettre a jour un plugin
	function do_up($info) {
		// ecriture du nouveau
		// suppression de l'ancien (si dans auto, et pas au meme endroit)
		// OU suppression des anciens fichiers
		if (!$this->tester_repertoire_plugins_auto()) {
			return false;
		}

		$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($info['i']));

		// on cherche la mise a jour...
		// c'est a dire le paquet source que l'on met a jour.
		if ($maj = sql_fetsel('*','spip_paquets',array(
			'prefixe='.sql_quote($info['p']),
			'version='.sql_quote($info['maj']),
			'superieur='.sql_quote('oui')))) {
				
			if ($dirs = $this->get_paquet_id($maj)) {
				// Si le plugin a jour n'est pas dans le meme dossier que l'ancien...
				// il faut :
				// - activer le plugin sur son nouvel emplacement (uniquement si l'ancien est actif)...
				// - supprimer l'ancien (si faisable)
				if (($dirs['src_archive'] . '/') != $i['src_archive']) {
					if ($i['actif'] == 'oui') {
						$this->activer_plugin_dossier($dirs['dossier'], $maj);
					}

					if (substr($i['dossier'], 0, 5) == 'auto/') {
						if (supprimer_repertoire($dirs['dir'])) {
							sql_delete('spip_paquets', 'id_paquet=' . sql_quote($info['i']));
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
		$i = sql_fetsel('*', 'spip_paquets', 'id_paquet='.sql_quote($info['i']));
		if ($dirs = $this->do_up($info)) {
			$this->activer_plugin_dossier($dirs['dossier'], $i, $i['constante']);
			return true;
		}
		return false;
	}


	// desactiver un plugin
	function do_off($info) {
		$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($info['i']));
		// il faudra prendre en compte les autres _DIR_xx
		if (in_array($i['constante'], array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
			include_spip('inc/plugin');
			$dossier = ($i['constante'] == '_DIR_PLUGINS') ? $i['dossier'] : '../' . constant($i['constante']) . $i['dossier'];
			ecrire_plugin_actifs(array(rtrim($dossier,'/')), false, 'enleve');
			sql_updateq('spip_paquets', array('actif'=>'non', 'installe'=>'non'), 'id_paquet='.sql_quote($info['i']));
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
		$i = sql_fetsel('*','spip_paquets','id_paquet=' . sql_quote($info['i']));
		// il faudra prendre en compte les autres _DIR_xx
		if (in_array($i['constante'], array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))) {
			include_spip('inc/plugin');
			$dossier = rtrim($i['dossier'],'/');
			$constante = $i['constante'];

			# $constante = $this->donner_chemin_constante_plugins( $i['constante'] );
				
			$installer_plugins = charger_fonction('installer', 'plugins');
			$infos = $installer_plugins($del['src_archive'], 'uninstall');
			if ($infos AND !$infos['install_test'][0]) {
				include_spip('inc/plugin');
				ecrire_plugin_actifs(array($del['src_archive']), false, 'enleve');
				sql_updateq('spip_paquets', array('actif'=>'non', 'installe'=>'non'), 'id_paquet='.sql_quote($info['i']));
			} else {
				// echec
				$this->log("Échec de la désinstallation de " . $del['src_archive']);
			}
		}
		$this->actualiser_plugin_interessants();
		return false;
	}


	// effacer les fichiers d'un plugin
	function do_kill($info) {
		// on reverifie que c'est bien un plugin auto !
		// il faudrait aussi faire tres attention sur un site mutualise
		// cette option est encore plus delicate que les autres...
		$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($info['i']));

		if (in_array($i['constante'], array('_DIR_PLUGINS','_DIR_PLUGINS_SUPPL'))
		and substr($i['dossier'], 0, 5) == 'auto/') {
			$dir = constant($i['constante']) . $i['dossier'];
			if (supprimer_repertoire($dir)) {
				sql_delete('spip_paquets', 'id_paquet=' . sql_quote($info['i']));
				return true;
			}
		}

		return false;
	}

	
	// installer une librairie
	function do_getlib($info) {
		if (!defined('_DIR_LIB') or !_DIR_LIB) {
			$this->err(_T('svp:erreur_dir_dib_indefini'));
			$this->log("/!\ Pas de _DIR_LIB defini !");
			return false;
		}
		if (!is_writable(_DIR_LIB)) {
			$this->err(_T('svp:erreur_dir_dib_ecriture', array('dir' => _DIR_LIB )));
			$this->log("/!\ Ne peut pas écrire dans _DIR_LIB !");
			return false;
		}

		$this->log("Recuperer la librairie : " . $info['n'] );

		// on recupere la mise a jour...
		include_spip('action/teleporter');
		$teleporter_composant = charger_fonction('teleporter_composant', 'action');
		$ok = $teleporter_composant('http', $info['v'], _DIR_LIB . $info['n']);
		if ($ok === true) {
			return true;
		}
		
		$this->err($ok);
		$this->log("Téléporteur en erreur : " . $ok);
		return false;
	}


	// telecharger un plugin
	function do_get($info) {
		if (!$this->tester_repertoire_plugins_auto()) {
			return false;
		}

		$i = sql_fetsel('*', 'spip_paquets', 'id_paquet=' . sql_quote($info['i']));
	
		if ($dirs = $this->get_paquet_id($info['i'])) {
			$this->ajouter_plugin_interessants_meta($dirs['dossier']);
			return true;
		}

		return false;
	}



	// lancer l'installation d'un plugin
	function do_install($info) {
		$message_install = $this->installer_plugin($info);
		return $message_install;
	}


	// adresse du dossier, et row SQL du plugin en question
	function activer_plugin_dossier($dossier, $i, $constante='_DIR_PLUGINS') {
		include_spip('inc/plugin');
		$this->log("Demande d'activation de : " . $dossier);
		
		//il faut absolument que tous les fichiers de cache
		// soient inclus avant modification, sinon un appel ulterieur risquerait
		// de charger des fichiers deja charges par un autre !
		// C'est surtout le ficher de fonction le probleme (options et pipelines
		// sont normalement deja charges).
		if (@is_readable(_CACHE_PLUGINS_OPT)) {include_once(_CACHE_PLUGINS_OPT);}
		if (@is_readable(_CACHE_PLUGINS_FCT)) {include_once(_CACHE_PLUGINS_FCT);}
		if (@is_readable(_CACHE_PIPELINES))   {include_once(_CACHE_PIPELINES);}

		$dossier = ($constante == '_DIR_PLUGINS')? $dossier : '../'.constant($constante).$dossier;
		include_spip('inc/plugin');
		ecrire_plugin_actifs(array($dossier), false, 'ajoute');
		$installe = $i['version_base'] ? 'oui' : 'non';
		if ($installe == 'oui') {
			if(!$i['constante'])
				$i['constante'] = '_DIR_PLUGINS';
			// installer le plugin au prochain tour
			$new_action = array_merge($this->work, array(
				'todo'=>'install',
				'dossier'=>rtrim($dossier,'/'),
				'constante'=>$i['constante']
			));
			array_unshift($this->end, $new_action);
			$this->log("Demande d'installation de $dossier");
			#$this->installer_plugin($dossier);
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
		$dossiers_old = array();
		foreach($plugins_interessants as $p => $score) {
			if (--$score > 0) {
				$plugins_interessants[$p] = $score;
				$dossiers[$p.'/'] = true;
			} else {
				unset($plugins_interessants[$p]);
				$dossiers_old[$p.'/'] = true;
			}
		}

		// enlever les anciens
		if ($dossiers_old) {
			// ATTENTION, il faudra prendre en compte les _DIR_xx
			sql_updateq('spip_paquets', array('recent'=>0), sql_in('src_archive', array_keys($dossiers_old)));
		}

		$plugs = sql_allfetsel('src_archive','spip_paquets', 'actif='.sql_quote('oui'));
		$plugs = array_map('array_shift', $plugs);
		foreach ($plugs as $dossier) {
			$dossiers[$dossier] = true;
			$plugins_interessants[ rtrim($dossier, '/') ] = 30; // score initial
		}

		$plugs = sql_updateq('spip_paquets', array('recent'=>1), sql_in('src_archive', array_keys($dossiers)));
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


	function installer_plugin($info){
		// il faut info['dossier'] et info['constante'] pour installer
		if ($plug = $info['dossier']) {
			$installer_plugins = charger_fonction('installer', 'plugins');
			$infos = $installer_plugins($plug, 'install', $info['constante']);
			if ($infos) {
				// en absence d'erreur, on met a jour la liste des plugins installes...
				if (!is_array($infos) OR $infos['install_test'][0]) {
					$meta_plug_installes = @unserialize($GLOBALS['meta']['plugin_installes']);
					if (!$meta_plug_installes) {
						$meta_plug_installes=array();
					}
					$meta_plug_installes[] = $plug;
					ecrire_meta('plugin_installes',serialize($meta_plug_installes),'non');
				}
				if (is_array($infos)){
					list($ok, $trace) = $infos['install_test'];
					if ($ok) {
						return true;
					}
					$this->err(_T('svp:message_action_finale_install_fail',
						array('plugin' => $info['n'], 'version'=>$info['v'])) . "<br />" . $trace);
				}
			}
		}
		return false;
	}
	


	// telecharge un paquet
	// et supprime les fichiers obsoletes (si presents)
	function get_paquet_id($id_or_row) {
		// on peut passer direct le row sql...
		if (!is_array($id_or_row)) {
			$i = sql_fetsel('*','spip_paquets','id_paquet='.sql_quote($id_or_row));
		} else {
			$i = $id_or_row;
		}
		unset($id_or_row);

		if ($i['nom_archive'] and $i['id_depot']) {
			$this->log("Recuperer l'archive : " . $i['nom_archive'] );
			if ($adresse = sql_getfetsel('url_archives', 'spip_depots', 'id_depot='.sql_quote($i['id_depot']))) {
				$zip = $adresse . '/' . $i['nom_archive'];
				$dest = substr($i['nom_archive'], 0, -4); // enlever .zip ...
				// on recupere la mise a jour...
				include_spip('action/teleporter');
				$teleporter_composant = charger_fonction('teleporter_composant', 'action');
				$ok = $teleporter_composant('http', $zip, _DIR_PLUGINS_AUTO . $dest);				
				if ($ok === true) {
					return array(
						'dir'=> _DIR_PLUGINS_AUTO . $dest,
						'dossier' => 'auto/' . $dest, // c'est depuis _DIR_PLUGINS ... pas bien en dur...
					);
				}
				$this->err($ok);
				$this->log("Téléporteur en erreur : " . $ok);	
			} else {
				$this->log("Aucune adresse pour le dépot " . $i['id_depot'] );
			}
		}
		return false;
	}


	/**
	 * Teste que le répertoire plugins auto existe et
	 * que l'on peut ecrire dedans !
	 *
	 * @return bool : C'est ok, ou pas
	**/
	function tester_repertoire_plugins_auto() {
		if (!defined('_DIR_PLUGINS_AUTO') or !_DIR_PLUGINS_AUTO) {
			$this->err(_T('svp:erreur_dir_plugins_auto_indefini'));
			$this->log("/!\ Pas de _DIR_PLUGINS_AUTO defini !");
			return false;
		}
		if (!is_writable(_DIR_PLUGINS_AUTO)) {
			$this->err(_T('svp:erreur_dir_plugins_auto_ecriture', array('dir'=>_DIR_PLUGINS_AUTO)));
			$this->log("/!\ Ne peut pas écrire dans _DIR_PLUGINS_AUTO !");
			return false;
		}
		return true;
	}

	/**
	 * Retourne le chemin relatif d'un repertoire plugins
	 * depuis _DIR_PLUGINS
	 * 
	 * Étrange chose que ce _DIR_PLUGINS_SUPPL...
	**/
	function donner_chemin_constante_plugins($constante) {
		if ($i['constante'] == '_DIR_PLUGINS_SUPPL') {
			return _DIR_RACINE . constant($constante);
		}
		return constant($constante);
	}
}

?>
