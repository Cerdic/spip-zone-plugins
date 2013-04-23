<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/plugin'); // pour spip_version_compare(), plugin_version_compatible()

class Decideur {

	// plugins actifs en cours avant toute modification
	var $start = array(
		'i' => array(),
		'p' => array(),
	);

	// plugins actifs a la fin des modifications effectuees
	var $end = array(
		'i' => array(),
		'p' => array(),
	);

	var $ask = array();     // toutes les actions a faire demandees
	var $todo = array();    // toutes les actions a faire
	var $changes = array(); // juste les actions a faire en plus de celles demandees
	var $off = array();     // juste les plugins a arreter
	var $invalides = array(); // juste les plugins invalides (suite a des dependances introuvables)
	var $mefiance = false;	// lorsqu'une action entraine des desactivations, mettre ce flag a true !

	var $err = array(); // erreurs rencontrees
	var $ok = true;     // le resultat permet d'effectuer toutes les actions
	var $log = false;   // loguer les differents elements


	function Decideur () {}


	/* Liste des plugins deja actifs */
	function liste_plugins_actifs() {
		return $this->infos_courtes('actif='.sql_quote('oui'));
	}

	function log($quoi) {
		if ($this->log) {
			spip_log($quoi,'decideur');
		}
	}

	function infos_courtes_id($id) {
		// on cache ceux la
		static $plug = array();
		if (!isset($plug[$id])) {
			$plug[$id] = $this->infos_courtes('id_plugin=' . sql_quote($id));
		}
		return $plug[$id];
	}

	/**
	 * recuperer les infos utiles des plugins
	 * on passe un where et on cree deux tableaux
	 * id (infos)
	 * prefixe (infos)
	 * OU prefixe[] (infos) si multiple=true, classes par etats decroissants.
	 *
	 */
	function infos_courtes($where, $multiple=false) {
		$plugs = array(
			'i'=>array(),
			'p'=>array()
		);

		$orderby = $multiple ? 'etatnum DESC' : '';

		$res = sql_select(array(
			'id_plugin AS i',
			'nom AS n',
			'prefixe AS p',
			'version AS v',
			'etatnum AS e',
			'dependances',
			'maj_version AS maj',
			'actif AS a'), 'spip_plugins', $where, '', $orderby);
		while ($r = sql_fetch($res)) {
			$d = unserialize($r['dependances']);
			// voir pour enregistrer en bdd simplement 'n' et 'u' (pas la peine d'encombrer)...
			if (!$d) $d = array('necessite'=>array(), 'utilise'=>array());
			unset($r['dependances']);

			/*
			 * On extrait les multi sur le nom du plugin
			 */
			$r['n'] = extraire_multi($r['n']);

			$plugs['i'][$r['i']] = $r;
			$plugs['i'][$r['i']]['dn'] = $d['necessite'];
			$plugs['i'][$r['i']]['du'] = $d['utilise'];

			if ($multiple) {
				$plugs['p'][$r['p']][] = &$plugs['i'][$r['i']]; // alias
			} else {
				$plugs['p'][$r['p']] = &$plugs['i'][$r['i']]; // alias
			}
		}
		return $plugs;
	}


	/* liste des erreurs */
	function erreur($id, $texte = '') {
		$this->log("erreur: $id -> $texte");
		if (!is_array($this->err[$id])) $this->err[$id] = array();
		$this->err[$id][] = $texte;
		$this->ok = false;
	}

	function en_erreur($id) {
		return isset($this->err[$id]) ? $this->err[$id] : false;
	}


	/* verifier qu'on plugin plus recent existe pour un prefixe et une version donnee */
	function chercher_plugin_recent($prefixe, $version) {
		$news = $this->infos_courtes(array('prefixe=' . sql_quote($prefixe), 'obsolete=' . sql_quote('non'), 'id_zone>'.sql_quote(0)), true);
		$res = false;
		if ($news and count($news['p'][$prefixe]) > 0) {
			foreach ($news['p'][$prefixe] as $new) {
				if (spip_version_compare($new['v'],$version,'>')) {
					if (!$res or version_compare($new['v'],$res['v'],'>')) {
						$res = $new;
					}
				}
			}
		}
		return $res;
	}

	/* verifier qu'un plugin exsite avec prefixe (cfg) pour une version [1.0;] donnee */
	function chercher_plugin_compatible($prefixe, $version) {
		$news = $this->infos_courtes(array('prefixe=' . sql_quote($prefixe), 'obsolete=' . sql_quote('non')), true);
		if ($news and count($news['p'][$prefixe]) > 0) {
			foreach ($news['p'][$prefixe] as $new) {
				if (plugin_version_compatible($version, $new['v'])) {
					return $new;
				}
			}
		}
		return false;
	}


	// ajouter a la liste des plugins actifs
	function add($info) {
		$this->end['i'][$info['i']] = $info;
		$this->end['p'][$info['p']] = &$this->end['i'][$info['i']];
	}

	function off($info, $recur = false) {
		$this->log('- stopper ' . $info['p']);
		$this->remove($info);
		$this->off[$info['p']] = $info;
		// si recursif, on stoppe aussi les plugins dependants
		if ($recur) {
			foreach ($this->end['i'] as $id => $plug) {
				if (is_array($plug['dn']) and $plug['dn']) {
					foreach ($plug['dn'] as $n) {
						if ($info['p'] == $n['id']) {
							$this->change($plug, 'off');
							$this->off($plug, true);
							$this->mefiance = true;
						}
					}
				}
			}
		}
	}


	function sera_off($prefixe) {
		return isset($this->off[$prefixe]) ? $this->off[$prefixe] : false;
	}

	function sera_off_id($id) {
		foreach ($this->off as $info) {
			if ($info['i'] == $id) {
				return $info;
			}
		}
		return false;
	}

	function sera_actif($prefixe) {
		return isset($this->end['p'][$prefixe]) ? $this->end['p'][$prefixe] : false;
	}

	function sera_actif_id($id) {
		return isset($this->end['i'][$id]) ? $this->end['i'][$id] : false;
	}

	// ajouter a la liste des demandes
	function ask($info, $quoi) {
		$this->ask[$info['i']] = $info;
		$this->ask[$info['i']]['todo'] = $quoi;
		$this->todo($info, $quoi);
	}

	// ajouter a la liste des changements en plus
	function change($info, $quoi) {
		$this->changes[$info['i']] = $info;
		$this->changes[$info['i']]['todo'] = $quoi;
		$this->todo($info, $quoi);
	}

	// pour annuler une action (automatique) qui finalement etait
	// reellement officielement demandee (cas de mise a 'off' de plugins).
	function annule_change($info) {
		unset($this->changes[$info['i']]);
	}

	// ajouter a la liste des actions
	function todo($info, $quoi) {
		$this->todo[$info['i']] = $info;
		$this->todo[$info['i']]['todo'] = $quoi;
	}

	// retirer un plugin des actifs
	function remove($info) {
		$i = $this->end['p'][$info['p']]; // aucazou ce ne soit pas les memes ids
		unset($this->end['i'][$info['i']], $this->end['p'][$info['p']], $this->end['i'][$i['i']]);
	}

	// invalider un plugin...
	function invalider($info) {
		$this->log("-> invalider $info[p]");
		$this->remove($info); // suffisant ?
		$this->invalides[$info['p']] = $info;
		$this->annule_change($info);
		unset($this->todo[$info['i']]);
	}

	function sera_invalide($p) {
		return isset($this->invalides[$p]) ? $this->invalides[$p] : false;
	}


	function est_presente_lib($lib) {
		static $libs = false;
		if ($libs === false) {
			include_spip('inc/step');
			$libs = step_lister_librairies();
		}
		return isset($libs[$lib]) ? $libs[$lib] : false;
	}


	/* Ajouter les actions demandees */
	function actionner($todo = null) {
		if (is_array($todo)) {
			foreach ($todo as $id => $t) {
				// plusieurs choses nous interessent... Sauf... le simple telechargement
				// et la suppression des fichiers (qui ne peuvent etre fait
				// que si le plugin n'est pas actif)
				$this->log("-- todo: $id/$t");

				switch ($t) {
					case 'on':
						// ajouter ce plugin dans la liste
						if (!$this->sera_actif_id($id)) {
							$i = $this->infos_courtes_id($id);
							if ($i['i'][$id]) {
								$this->add($i['i'][$id]);
								$this->ask($i['i'][$id], 'on');
							} else {
								// la c'est vraiment pas normal... Erreur plugin inexistant...
								// concurrence entre administrateurs ?
								$this->erreur($id, _T('step:message_plugin_inexistant',array('plugin'=>$id)));
							}
						}
						break;
					case 'up':
					case 'upon':
						// le plugin peut etre actif !
						// ajouter ce plugin dans la liste et retirer l'ancien
						$i = $this->infos_courtes_id($id);
						if ($i = $i['i'][$id]) {
							// new : plugin a installer
							if ($new = $this->chercher_plugin_recent($i['p'], $i['v'])) {
								// ajouter seulement si on l'active !
								// ou si le plugin est actuellement actif
								if ($t == 'upon' or $this->sera_actif_id($id)) {
									$this->remove($i);
									$this->add($new);
								}
								$this->ask($i, $t);
							} else {
								// on n'a pas trouve la nouveaute !!!
								$this->erreur($id, _T('step:message_maj_introuvable',array('plugin' => $i[p],'id'=>$id)));
							}
						} else {
							// mauvais identifiant ?
							// on n'a pas trouve le plugin !!!
							$this->erreur($id, _T('step:message_erreur_maj_inconnu',array('id'=>$id)));
						}
						break;
					case 'off':
					case 'stop':
						// retirer ce plugin
						// (il l'est peut etre deja)
						if ($info = $this->sera_actif_id($id)
						or  $info_off = $this->sera_off_id($id)) {
							// annuler le signalement en "proposition" (due a une mise a 'off' recursive)
							// de cet arret de plugin, vu qu'on de demande reellement
							if (!$info) {
								$info = $info_off;
								$this->annule_change($info);
							}
							$this->ask($info, $t);
							$this->todo($info, $t);
							$this->off($info, true);

						} else {
							// pas normal... plugin deja inactif...
							// concurrence entre administrateurs ?
							$this->erreur($id, _T('step:message_erreur_plugin_non_actif'));
						}
						break;
					case 'null':
					case 'get':
					case 'kill':
						if ($info = $this->infos_courtes_id($id)) {
							$this->ask($info['i'][$id], $t);
						} else {
							// pas normal... plugin inconnu... concurrence entre administrateurs ?
							$this->erreur($id, _T('step:message_erreur_plugin_introuvable',array('plugin'=>$id,'action'=>$t)));
						}
						break;
				}
			}
		}
		return $this->ok;
	}


	// ecrire les plugins actifs
	function start() {
		$this->start = $this->end = $this->liste_plugins_actifs();
	}

	/* Calcul de dependances */
	function verifier_dependances($todo = null) {

		$this->start();

		// ajouter les actions
		if (!$this->actionner($todo)) {
			$this->log("! Todo en echec !");
			$this->log($decideur->err);
			return false;
		}

		// doit on reverifier les dependances ?
		// oui des qu'on modifie quelque chose...
		// attention a ne pas boucler infiniment !

		$supersticieux = 0;
		do {
			$try_again = 0;
			$supersticieux++;

			// verifier chaque dependance de chaque plugin a activer
			foreach ($this->end['i'] as $info) {
				if (!$this->verifier_dependances_plugin($info)) {
					$try_again = true;
				}
			}
			unset($id, $info);
			$this->log("--------> try_again: $try_again, supersticieux: $supersticieux");
		} while ($try_again > 0 and $supersticieux < 100); # and !count($this->err)

		$this->log("Fin !");
		$this->log("Ok: " . $this->ok);
		# $this->log($this->todo);

		return $this->ok;
	}



	function verifier_dependances_plugin($info, $prof=0) {
		$this->log("- [$prof] verifier dependances " . $info['p']);
		$id = $info['i'];

		$cache = array(); // cache des actions realisees dans ce tour

		if (is_array($info['dn']) and $info['dn']) {
			foreach ($info['dn'] as $n) {
				// de deux choses l'une...
				// soit la dependance est a SPIP, soit a un plugin, soit a une librairie...

				$p = strtolower($n['id']);
				$v = $n['version'];

				// si c'est a SPIP et qu'on ne valide pas, on retourne une erreur !
				// normalement, on ne devrait pas trop pouvoir tomber sur ce cas
				if (strtoupper($p) == 'SPIP') {
					if (!step_verifier_plugin_compatible_version_spip($v)) {
						$this->invalider($info);
						$this->erreur($id, _T('step:message_incompatibilite_spip',array('plugin'=>$info[p])));
						// est-ce qu'on quitte tout de suite, ou teste-t-on tout ?
						// pour l'instant, essayons de tout tester quand meme
						// nous verrons par la suite si c'est judicieux ou pas
					}
				} elseif (strpos($p,'lib:')===0) {
					$lib = substr($p, 4);
					// l'identifiant commence par "lib:", c'est une librairie dont il s'agit.
					// on verifie sa presence OU le fait qu'on pourra la telecharger
					if ($lib and !$this->est_presente_lib($lib)) {
						// peut on ecrire ?
						if (!is_writable(_DIR_LIB)) {
							$this->invalider($info);
							$this->erreur($id, _T('step:message_erreur_ecriture_lib',array('plugin'=>$info[p],'lib_url'=>$n[src],'lib'=>$lib)));
						}
					}

				} else {
					$this->log("-- verifier $p");
					// nous sommes face a une dependance de plugin
					// on regarde s'il est present et a la bonne version
					// sinon on le cherche et on l'ajoute
					if (!($ninfo = $this->sera_actif($p)
					and !$err = $this->en_erreur($ninfo['i'])
					and plugin_version_compatible($v, $ninfo['v']))) {

						// absent ou erreur ou pas compatible
						$etat = $err ? 'erreur' : ($ninfo ? 'conflit' : 'absent');
						$this->log("Dedendance " . $p . " &agrave; resoudre ! ($etat)");

						switch ($etat) {
							// commencons par le plus simple :
							// en cas d'absence, on cherche ou est ce plugin !
							case 'absent':
								// on choisit par defaut le meilleur etat de plugin.
								// (attention: la on ne regarde pas si c'est local ou distant... a corriger ?)
								if (!$this->sera_off($p)
								and $new = $this->chercher_plugin_compatible($p, $v)
								and $this->verifier_dependances_plugin($new, ++$prof)) {
									// si le plugin existe localement, c'est que
									// c'est une mise a jour + activation a faire
									$cache[] = $new;
									$i = $this->infos_courtes(array(
											'prefixe=' . sql_quote($new['p']),
											'maj_version=' . sql_quote($new['v'])
										), true);
									if (isset($i['p'][$new['p']]) and count($i['p'][$new['p']])) {
										// c'est une mise a jour
										$vieux = $i['p'][$new['p']][0];
										$this->change($vieux, 'upon');
										$this->log("-- update+active : $p");
									} else {
										// tout nouveau tout beau
										$this->change($new, 'on');
										$this->log("-- nouveau : $p");
									}
									$this->add($new);
								} else {
									$this->log("-- !erreur : $p");
									// on ne trouve pas la dependance !
									$this->invalider($info);
									$this->erreur($id, $v ? _T('step:message_dependance_plugin_version',array('plugin'=>$info['p'],'dependance'=>$p,'version'=>$v)) : _T('step:message_dependance_plugin',array('plugin'=>$info['p'],'dependance'=>$p)));
								}
								unset($new, $vieux);
								break;

							case 'erreur':
								break;

							// present, mais conflit de version
							// de deux choses l'une :
							// soit on trouve un paquet meilleur...
							// soit pas :)
							case 'conflit':
								$this->log("  conflit -> demande $v, present : " . $ninfo['v']);
								if (!$this->sera_off($p)
								and $new = $this->chercher_plugin_compatible($p, $v)
								and $this->verifier_dependances_plugin($new, ++$prof)) {
									// on connait le nouveau...
									$cache[] = $new;
									$this->remove($ninfo);
									$this->add($new);
									$this->change($ninfo,'up');
									$this->log("-- update : $p");
								} else {
									$this->log("-- !erreur : $p");
									// on ne trouve pas la dependance !
									$this->invalider($info);
									$this->erreur($id, $v ? _T('step:message_dependance_plugin_version',array('plugin'=>$info['p'],'dependance'=>$p,'version'=>$v)) : _T('step:message_dependance_plugin',array('plugin'=>$info['p'],'dependance'=>$p)));
								}
								break;
						}

					} else {
						$this->log('-- dep OK pour '.$info['p'].' : '.$p);
					}
				}

				if ($this->sera_invalide($info['p'])) {
					break;
				}
			}
			unset($n, $v, $p, $ninfo, $present, $conflit, $erreur, $err);

			// si le plugin est devenu invalide...
			// on invalide toutes les actions qu'on vient de faire !
			if ($this->sera_invalide($info['p'])) {
				$this->log("> Purge du cache");
				foreach ($cache as $i) {
					$this->invalider($i);
				}
				return false;
			}
		}
		return true;
	}

	function presenter_actions($quoi) {
		$res = array();
		foreach ($this->$quoi as $id=>$info) {
			$supp = ($info['todo'] == 'up' or $info['todo'] == 'upon') ? 'en version ' . $info['maj'] : '';
			$res[] = _T('step:message_action_'.$info['todo'],array('plugin'=>$info[p],'version'=>$info[v],'supp'=>$supp));
		}
		return $res;
	}
}

?>
