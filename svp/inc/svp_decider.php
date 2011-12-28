<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('plugins/installer'); // pour spip_version_compare()
include_spip('inc/svp_rechercher'); // svp_verifier_compatibilite_spip()
# include_spip('inc/plugin'); // plugin_version_compatible() [inclu dans svp_rechercher]

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

    // plugins procure par SPIP
    var $procure = array();

	var $ask = array();     // toutes les actions a faire demandees
	var $todo = array();    // toutes les actions a faire
	var $changes = array(); // juste les actions a faire en plus de celles demandees
	var $off = array();     // juste les plugins a arreter
	var $invalides = array(); // juste les plugins invalides (suite a des dependances introuvables)
	var $mefiance = false;	// lorsqu'une action entraine des desactivations, mettre ce flag a true !

	var $err = array(); // erreurs rencontrees
	var $ok = true;     // le resultat permet d'effectuer toutes les actions
	var $log = false;   // loguer les differents elements


	function Decideur () {
		include_spip('inc/config');
		$this->log = (lire_config('svp/mode_log_verbeux') == 'oui');
	}


	/* Liste des plugins deja actifs */
	function liste_plugins_actifs() {
		return $this->infos_courtes('pa.actif='.sql_quote('oui'));
	}

    /* Liste des plugins procure par SPIP */
    function liste_plugins_procure() {
        $procure = array();
        $get_infos = charger_fonction('get_infos','plugins');
       	$infos['_DIR_RESTREINT'][''] = $get_infos('./',false,_DIR_RESTREINT,'plugin.xml');

        foreach($infos['_DIR_RESTREINT']['']['procure'] as $_procure) {
       		$prefixe = strtoupper($_procure['id']);
       		$procure[$prefixe] = $_procure['version'];
       	}

        return $procure;
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
			$plug[$id] = $this->infos_courtes('pa.id_paquet=' . sql_quote($id));
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
	function infos_courtes($condition, $multiple=false) {
		$plugs = array(
			'i'=>array(),
			'p'=>array()
		);

        $from = array('spip_paquets AS pa', 'spip_plugins AS pl');
		$orderby = $multiple ? 'pa.etatnum DESC' : '';
        $where = array('pa.id_plugin = pl.id_plugin');
        if (is_array($condition))
            $where = array_merge($where, $condition);
        else
            $where[] = $condition;

        include_spip('inc/filtres'); // extraire_multi()
		$res = sql_allfetsel(array(
			'pa.id_paquet AS i',
			'pl.nom AS n',
			'pl.prefixe AS p',
			'pa.version AS v',
			'pa.etatnum AS e',
			'pa.compatibilite_spip',
			'pa.dependances',
			'pa.id_depot',
			'pa.maj_version AS maj',
			'pa.actif AS a'), $from, $where, '', $orderby);
		foreach ($res as $r) {
			$r['p'] = strtoupper( $r['p'] ); // on s'assure du prefixe en majuscule.
			
			// savoir si un paquet est en local ou non...
			$r['local'] = ($r['id_depot']) == 0 ? true : false;
			unset($r['id_depot']);
			
			$d = unserialize($r['dependances']);
			// voir pour enregistrer en bdd simplement 'n' et 'u' (pas la peine d'encombrer)...
			$deps = array('necessite'=>array(array()), 'utilise'=>array(array()), 'librairie'=>array(array()));
			if (!$d) $d = $deps;
			
			unset($r['dependances']);

			/*
			 * On extrait les multi sur le nom du plugin
			 */
			$r['n'] = extraire_multi($r['n']);

			$plugs['i'][$r['i']] = $r;

			
			// pour chaque type de dependences... (necessite, utilise, librairie)
			// on cree un tableau unique [$dependence] = array()
			// au lieu de plusieurs tableaux par version de spip
			// en ne mettant dans 0 que ce qui concerne notre spip local
			foreach($deps as $cle => $defaut) {
				if (!isset($d[$cle])) {
					$d[$cle] = $defaut;
				}
				
				// gerer les dependences autres que dans 0 (communs ou local) !!!!
				// il peut exister des cles info[dn]["[version_spip_min;version_spip_max]"] de dependences
				if (!isset($d[$cle][0]) OR count($d[$cle]) > 1) {
					$dep = array();
					$dep[0] = isset($d[$cle][0]) ? $d[$cle][0] : array();
					unset($d[$cle][0]);
					foreach ($d[$cle] as $version => $dependences) {
						if (svp_verifier_compatibilite_spip($version)) {
							$dep = array_merge($dep[0], $dependences);
						}
					}
					$d[$cle] = $dep;
				}
			}
			// passer les prefixes en majuscule
			foreach($d['necessite'][0] as $i=>$n) {
				$d['necessite'][0][$i]['nom'] = strtoupper($n['nom']);
			}
			$plugs['i'][$r['i']]['dn'] = $d['necessite'][0];
			$plugs['i'][$r['i']]['du'] = $d['utilise'][0];
			$plugs['i'][$r['i']]['dl'] = $d['librairie'][0];


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
		$news = $this->infos_courtes(array('pl.prefixe=' . sql_quote($prefixe), 'pa.obsolete=' . sql_quote('non'), 'pa.id_depot > '.sql_quote(0)), true);
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
		
		// on choisit en priorite dans les paquets locaux !
		$locaux = $this->infos_courtes(array(
			'pl.prefixe=' . sql_quote($prefixe),
			'pa.obsolete=' . sql_quote('non'),
			'pa.id_depot='.sql_quote(0)), true);
		if ($locaux and count($locaux['p'][$prefixe]) > 0) {
			foreach ($locaux['p'][$prefixe] as $new) {
				if (plugin_version_compatible($version, $new['v'])
				and svp_verifier_compatibilite_spip($new['compatibilite_spip']) ){
					return $new;
				}
			}
		}
		
		// sinon dans les paquets distants
		$distants = $this->infos_courtes(array(
			'pl.prefixe=' . sql_quote($prefixe),
			'pa.obsolete=' . sql_quote('non'),
			'pa.id_depot>'.sql_quote(0)), true);
		if ($distants and count($distants['p'][$prefixe]) > 0) {
			foreach ($distants['p'][$prefixe] as $new) {
				if (plugin_version_compatible($version, $new['v'])
				and svp_verifier_compatibilite_spip($new['compatibilite_spip']) ){
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
						if ($info['p'] == $n['nom']) {
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
			include_spip('inc/svp_outiller');
			$libs = svp_lister_librairies();
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
					case 'getlib':
						break;
					case 'on':
					case 'geton':
						// ajouter ce plugin dans la liste
						if (!$this->sera_actif_id($id)) {
							$i = $this->infos_courtes_id($id);
							if ($i = $i['i'][$id]) {
								$this->log("--> $t : " . $i['p'] . ' en version : ' . $i['v'] );

								// se mefier : on peut tenter d'activer
								// un plugin de meme prefixe qu'un autre deja actif
								// mais qui n'est pas de meme version ou de meme etat
								// par exemple un plugin obsolete ou un plugin au contraire plus a jour.
								// dans ce cas, on desactive l'ancien (sans desactiver les dependences)
								// et on active le nouveau.
								// Si une dependance ne suit pas, une erreur se produira du coup.
								if (isset($this->end['p'][ $i['p'] ])) {
									$old = $this->end['p'][ $i['p'] ];
									$this->log("-->> off : " . $old['p'] . ' en version : ' . $old['v'] );
									$this->ask($old, 'off');
									$this->todo($old, 'off');
									$this->off($old, false); // a priori, les dependences devraient suivre...

								}
								
								// pas de prefixe equivalent actif...
								$this->add($i);
								$this->ask($i, $i['local'] ? 'on' : 'geton' );
								
							} else {
								// la c'est vraiment pas normal... Erreur plugin inexistant...
								// concurrence entre administrateurs ?
								$this->erreur($id, _T('svp:message_nok_plugin_inexistant',array('plugin' => $id)));
							}
						}
						break;
					case 'up':
					case 'upon':
						// le plugin peut etre actif !
						// ajouter ce plugin dans la liste et retirer l'ancien
						$i = $this->infos_courtes_id($id);
						if ($i = $i['i'][$id]) {
							$this->log("--> $t : " . $i['p'] . ' en version : ' . $i['v'] );

							// new : plugin a installer
							if ($new = $this->chercher_plugin_recent($i['p'], $i['v'])) {
								$this->log("--> maj : " . $new['p'] . ' en version : ' . $new['v'] );
								// ajouter seulement si on l'active !
								// ou si le plugin est actuellement actif
								if ($t == 'upon' or $this->sera_actif_id($id)) {
									$this->remove($i);
									$this->add($new);
								}
								$this->ask($i, $t);
							} else {
								// on n'a pas trouve la nouveaute !!!
								$this->erreur($id, _T('svp:message_nok_maj_introuvable',array('plugin' => $i['n'],'id'=>$id)));
							}
						} else {
							// mauvais identifiant ?
							// on n'a pas trouve le plugin !!!
							$this->erreur($id, _T('svp:message_erreur_maj_inconnu',array('id'=>$id)));
						}
						break;
					case 'off':
					case 'stop':
						// retirer ce plugin
						// (il l'est peut etre deja)
						if ($info = $this->sera_actif_id($id)
						or  $info_off = $this->sera_off_id($id)) {
							// annuler le signalement en "proposition" (due a une mise a 'off' recursive)
							// de cet arret de plugin, vu qu'on le demande reellement
							if (!$info) {
								$info = $info_off;
								$this->annule_change($info);
							}
							$this->log("--> $t : " . $info['p'] . ' en version : ' . $info['v'] );
							$this->ask($info, $t);
							$this->todo($info, $t);
							$this->off($info, true);

						} else {
							// pas normal... plugin deja inactif...
							// concurrence entre administrateurs ?
							$this->erreur($id, _T('svp:message_erreur_plugin_non_actif'));
						}
						break;
					case 'null':
					case 'get':
					case 'kill':
						if ($info = $this->infos_courtes_id($id)) {
							$this->log("--> $t : " . $info['i'][$id]['p'] . ' en version : ' . $info['i'][$id]['v'] );
							$this->ask($info['i'][$id], $t);
						} else {
							// pas normal... plugin inconnu... concurrence entre administrateurs ?
							$this->erreur($id, _T('svp:message_erreur_plugin_introuvable',array('plugin'=>$id,'action'=>$t)));
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
        $this->procure = $this->liste_plugins_procure();
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

		// 1
		// tester la version de SPIP de notre paquet
		// si on ne valide pas, on retourne une erreur !
		// mais normalement, on ne devrait vraiment pas pouvoir tomber sur ce cas
		if (!svp_verifier_compatibilite_spip($info['compatibilite_spip'])) {
			$this->invalider($info);
			$this->erreur($id, _T('svp:message_incompatibilite_spip',array('plugin'=>$info['n'])));
			return false;
		}
					
		// 2
		// ajouter les librairies necessaires a notre paquet
		if (is_array($info['dl']) and count($info['dl'])) {
			$err = false;
			foreach ($info['dl'] as $l) {
				// $l = array('nom' => 'x', 'lien' => 'url')
				$lib = $l['nom'];
				$this->log("## Necessite la librairie : " . $lib );

				// on verifie sa presence OU le fait qu'on pourra la telecharger
				if ($lib and !$this->est_presente_lib($lib)) {
					// peut on ecrire ?
					if (!is_writable(_DIR_LIB)) {
						$this->invalider($info);
						$this->erreur($id, _T('svp:message_erreur_ecriture_lib', array('plugin'=>$info['n'], 'lib_url'=>$l['lien'], 'lib'=>$lib)));
						$err = true;
					}
					// ajout, pour info
					// de la librairie dans la todo list
					else {
						$this->change(array(
							'i' => md5(serialize($l)),
							'p' => $lib,
							'n' => $lib,
							'v' => $l['lien'],
						), 'getlib');
					}
					
				}
			}
			if ($err) {
				return false;
			}
		}

		// 3
		// Trouver les dependences aux necessites
		// et les activer au besoin
		if (is_array($info['dn']) and count($info['dn'])) {
			foreach ($info['dn'] as $n) {

				$p = $n['nom'];
				$v = $n['compatibilite'];

				if ($p == 'SPIP') {
					// c'est pas la que ça se fait !
					// ca ne devrait plus apparaitre comme dependence a un plugin.
				}

                // le core procure le paquet que l'on demande !
                elseif ((array_key_exists($p, $this->procure))
                  and (plugin_version_compatible($v, $this->procure[$p]))) {
                    // rien a faire...
                    $this->log("-- est procure par le core ($p)");

				}

				// pas d'autre alternative qu'un vrai paquet a activer
				else {
					$this->log("-- verifier : $p");
					// nous sommes face a une dependance de plugin
					// on regarde s'il est present et a la bonne version
					// sinon on le cherche et on l'ajoute
					if ($ninfo = $this->sera_actif($p)
					and !$err = $this->en_erreur($ninfo['i'])
					and plugin_version_compatible($v, $ninfo['v'])) {
						// il est deja actif ou a activer, et tout est ok
						$this->log('-- dep OK pour '.$info['p'].' : '.$p);
					}
					// il faut le trouver et demander a l'activer
					else {
					
						// absent ou erreur ou pas compatible
						$etat = $err ? 'erreur' : ($ninfo ? 'conflit' : 'absent');
						// conflit signifie qu'il existe le prefixe actif, mais pas a la version demandee
						$this->log("Dedendance " . $p . " a resoudre ! ($etat)");

						switch ($etat) {
							// commencons par le plus simple :
							// en cas d'absence, on cherche ou est ce plugin !
							case 'absent':
								// on choisit par defaut le meilleur etat de plugin.
								// de preference dans les plugins locaux, sinon en distant.
								if (!$this->sera_off($p)
								and $new = $this->chercher_plugin_compatible($p, $v)
								and $this->verifier_dependances_plugin($new, ++$prof)) {
									// si le plugin existe localement et possede maj_version,
									// c'est que c'est une mise a jour + activation a faire
									$cache[] = $new;
									$i = $this->infos_courtes(array(
											'pl.prefixe=' . sql_quote($new['p']),
											'pa.maj_version=' . sql_quote($new['v'])
										), true);
									if (isset($i['p'][$new['p']]) and count($i['p'][$new['p']])) {
										// c'est une mise a jour
										$vieux = $i['p'][$new['p']][0];
										$this->change($vieux, 'upon');
										$this->log("-- update+active : $p");
									} else {
										// tout nouveau tout beau
										$this->change($new, $new['local'] ? 'on' : 'geton');
										$this->log("-- nouveau : $p");
									}
									$this->add($new);
								} else {
									$this->log("-- !erreur : $p");
									// on ne trouve pas la dependance !
									$this->invalider($info);
									$this->erreur($id, $v ? _T('svp:message_dependance_plugin_version',array('plugin'=>$info['n'],'dependance'=>$p,'version'=>$v)) : _T('svp:message_dependance_plugin',array('plugin'=>$info['n'],'dependance'=>$p)));
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
									$this->erreur($id, $v ? _T('svp:message_dependance_plugin_version',array('plugin'=>$info['n'],'dependance'=>$p,'version'=>$v)) : _T('svp:message_dependance_plugin',array('plugin'=>$info['n'],'dependance'=>$p)));
								}
								break;
						}

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
			$res[] = _T('svp:message_action_'.$info['todo'], array('plugin'=>$info['n'],'version'=>$info['v'],'version_maj'=>$info['maj']));
		}
		return $res;
	}
}


/**
 * Simplification pour la partie verifier
 * des formulaires utilisant le decideur 
 *
 * @param array $a_actionner Tableau des actions par paquet (id_paquet => action)
 * @param array $erreurs Tableau d'erreurs de verifier (CVT)
 * @return bool Operation ok.
**/
function svp_decider_verifier_actions_demandees($a_actionner, &$erreurs) {
	$decideur = new Decideur;
	$decideur->verifier_dependances($a_actionner);

	if (!$decideur->ok) {
		$erreurs['decideur_erreurs'] = array();
		foreach ($decideur->err as $id=>$errs) {
			foreach($errs as $err) {
				$erreurs['decideur_erreurs'][] = $err;
			}
		}
		return false;
	}

	$erreurs['decideur_propositions'] 	= $decideur->presenter_actions('changes');
	$erreurs['decideur_demandes'] 		= $decideur->presenter_actions('ask');
	$erreurs['decideur_actions'] 		= $decideur->presenter_actions('todo');

	// On construit la liste des actions pour la passer au formulaire en hidden
	$todo = array();
	foreach ($decideur->todo as $_todo) {
		$todo[$_todo['i']] = $_todo['todo'];
	}
	set_request('_todo', serialize($todo));
	return true;
}
?>
