<?php


function svp_actualiser_paquets_locaux() {

	spip_timer('paquets_locaux');
	$paquets = svp_descriptions_paquets_locaux();

	// un mode pour tout recalculer sans désinstaller le plugin... !
	if (_request('var_mode') == 'vider_paquets_locaux') { 
		svp_base_supprimer_paquets_locaux();
		svp_base_inserer_paquets_locaux($paquets);
	} else {
		svp_base_modifier_paquets_locaux($paquets);
	}
	svp_base_actualiser_paquets_actifs();

	$temps = spip_timer('paquets_locaux');
#spip_log('svp_actualiser_paquets_locaux', 'SVP');
#spip_log($temps, 'SVP');
	return "Éxécuté en : " . $temps;
	
}


function svp_descriptions_paquets_locaux() {
	include_spip('inc/plugin');
	liste_plugin_files(_DIR_PLUGINS);
	liste_plugin_files(_DIR_EXTENSIONS);
	$get_infos = charger_fonction('get_infos', 'plugins');
	$paquets_locaux = array(
		'_DIR_PLUGINS'    => $get_infos(array(), false, _DIR_PLUGINS),
		'_DIR_EXTENSIONS' => $get_infos(array(), false, _DIR_EXTENSIONS),
	);
	if (defined('_DIR_PLUGINS_SUPP') and _DIR_PLUGINS_SUPP) {
		liste_plugin_files(_DIR_PLUGINS_SUPP);
		$paquets_locaux['_DIR_PLUGINS_SUPP'] = $get_infos(array(), false, _DIR_PLUGINS_SUPP);
	}
	
	// creer la liste des signatures
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			$paquets_locaux[$const_dir][$chemin]['signature'] = md5($const_dir . $chemin . serialize($paquet));
		}
	}
	
	return $paquets_locaux;
}


// supprime les paquets et plugins locaux.
function svp_base_supprimer_paquets_locaux() {
	sql_delete('spip_paquets', 'id_depot = ' . 0); //_paquets locaux en 0
	sql_delete('spip_plugins', sql_in('id_plugin', sql_get_select('DISTINCT(id_plugin)', 'spip_paquets'), 'NOT'));
}


/**
 * Actualise les informations en base
 * sur les paquets locaux
 * en ne modifiant que ce qui a changé.
 *
 * @param array $plugins liste d'identifiant de plugins
**/
function svp_base_modifier_paquets_locaux($paquets_locaux) {
	include_spip('inc/svp_depoter_distant');

	// On ne va modifier QUE les paquets locaux qui ont change
	// Et cela en comparant les md5 des informations fouries.
	$signatures = array();

	// recuperer toutes les signatures 
	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			$signatures[$paquet['signature']] = array(
				'constante' => $const_dir,
				'chemin'    => $chemin,
				'paquet'    => $paquet,
			);
		}
	}

	// tous les paquets du depot qui ne font pas parti des signatures
	$anciens_paquets = sql_allfetsel('id_paquet', 'spip_paquets', array('id_depot=' . sql_quote(0), sql_in('signature', array_keys($signatures), 'NOT')));
	$anciens_paquets = array_map('array_shift', $anciens_paquets);

	// tous les plugins correspondants aux anciens paquets
	$anciens_plugins = sql_allfetsel('p.id_plugin',	array('spip_plugins AS p', 'spip_paquets AS pa'), array('p.id_plugin=pa.id_plugin', sql_in('pa.id_paquet', $anciens_paquets)));
	$anciens_plugins = array_map('array_shift', $anciens_plugins);

	// suppression des anciens paquets
	sql_delete('spip_paquets', sql_in('id_paquet', $anciens_paquets));
	
	// corriger les vmax (et supprimer les plugins orphelins)
	svp_corriger_vmax_plugins($anciens_plugins);

	// on ne garde que les paquets qui ne sont pas presents dans la base
	$signatures_base = sql_allfetsel('signature', 'spip_paquets', 'id_depot='.sql_quote(0));
	$signatures_base = array_map('array_shift', $signatures_base);
	$signatures = array_diff_key($signatures, array_flip($signatures_base));

	// on recree la liste des paquets locaux a inserer
	$paquets_locaux = array();
	foreach ($signatures as $s => $infos) {
		if (!isset($paquets_locaux[$infos['constante']])) {
			$paquets_locaux[$infos['constante']] = array();
		}
		$paquets_locaux[$infos['constante']][$infos['chemin']] = $infos['paquet'];
	}

	svp_base_inserer_paquets_locaux($paquets_locaux);
}


/**
 * Détermine la version max
 * de chaque plugin, c'est a dire
 * la version maxi d'un des paquets qui lui est lié.
 *
 * Supprime les plugins devenus orphelins dans cette liste.
 *
 * @param array $plugins liste d'identifiant de plugins
**/
function svp_corriger_vmax_plugins($plugins) {
	// tous les plugins encore lies a des depots...
	// la vmax est a retablir...
	if ($plugins) {
		$p = sql_allfetsel('DISTINCT(p.id_plugin)', array('spip_plugins AS p', 'spip_paquets AS pa'), array(sql_in('p.id_plugin', $plugins), 'p.id_plugin=pa.id_plugin'));
		$p = array_map('array_shift', $p);
		$diff = array_diff($plugins, $p);
		// pour chaque plugin non encore utilise, on les vire !
		sql_delete('spip_plugins', sql_in('id_plugin', $diff));
	
		// pour les autres, on la fixe correctement
		$vmax = 0;
		
		// On insere, en encapsulant pour sqlite...
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
				
		foreach ($p as $id_plugin) {
			if ($pa = sql_allfetsel('version', 'spip_paquets', 'id_plugin='.$id_plugin)) {
				foreach ($pa as $v) {
					if (spip_version_compare($v['version'], $vmax, '>')) {
						$vmax = $v['version'];
					}
				}
			}
			sql_updateq('spip_plugins', array('vmax'=>$vmax), 'id_plugin=' . intval($id_plugin));
		}
		
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}
}




function svp_base_inserer_paquets_locaux($paquets_locaux) {
	include_spip('inc/svp_depoter_distant');
	
	// On initialise les informations specifiques au paquet :
	// l'id du depot et les infos de l'archive
	$paquet_base = array(
		'id_depot' => 0,
		'nom_archive' => '',
		'nbo_archive' => '',
		'maj_archive' => '',
		'src_archive' => '',
		'date_modif' => '',
		'maj_version' => '',
		'signature' => '',
	);

	$preparer_sql_paquet = charger_fonction('preparer_sql_paquet', 'plugins');

	// pour chaque decouverte, on insere les paquets en base.
	// on evite des requetes individuelles, tres couteuses en sqlite...
	$cle_plugins    = array(); // prefixe => id
	$insert_plugins = array(); // insertion prefixe...
	$insert_paquets = array(); // insertion de paquet...

	include_spip('inc/config');
	$recents = lire_config('plugins_interessants');
	$installes  = lire_config('plugin_installes');
	$actifs  = lire_config('plugin');

	foreach($paquets_locaux as $const_dir => $paquets) {
		foreach ($paquets as $chemin => $paquet) {
			$le_paquet = $paquet_base;

			#$le_paquet['traductions'] = serialize($paquet['traductions']);

			if ($champs = $preparer_sql_paquet($paquet)) {

				// Eclater les champs recuperes en deux sous tableaux, un par table (plugin, paquet)
				$champs = eclater_plugin_paquet($champs);
				$paquet_plugin = true;
				
				// On complete les informations du paquet et du plugin
				$le_paquet = array_merge($le_paquet, $champs['paquet']);
				$le_plugin = $champs['plugin'];

				// On loge l'absence de categorie ou une categorie erronee et on positionne la categorie par defaut "aucune"
				if (!$le_plugin['categorie']) {
					$le_plugin['categorie'] = 'aucune';
				} else {
					if (!in_array($le_plugin['categorie'], $GLOBALS['categories_plugin'])) {
						$le_plugin['categorie'] = 'aucune';
					}
				}

				// creation du plugin...
				$prefixe = strtoupper( $le_plugin['prefixe'] );
				if (!isset($cle_plugins[$prefixe])) {
					if (!$id_plugin = sql_getfetsel('id_plugin', 'spip_plugins', 'prefixe = '.sql_quote($prefixe))) {
						$insert_plugins[$prefixe] = $le_plugin;
					} else {
						$cle_plugins[$prefixe] = $id_plugin;
					}
				}

				// ajout du prefixe dans le paquet, supprime avant insertion...
				$le_paquet['prefixe']     = $prefixe;
				$le_paquet['constante']   = $const_dir;
				$le_paquet['src_archive'] = $chemin;
				$le_paquet['recent']      = isset($recents[$chemin]) ? $recents[$chemin] : 0;
				$le_paquet['installe']    =  in_array($chemin, $installes) ? 'oui': 'non'; // est desinstallable ?
				$le_paquet['obsolete']    =  'non';
				$le_paquet['signature']    =  $paquet['signature'];
				
				$actif = "non";
				if (isset($actifs[$prefixe])
					and ($actifs[$prefixe]['dir_type'] == $const_dir)
					and ($actifs[$prefixe]['dir'] == $chemin)) {
					$actif = "oui";
				}
				$le_paquet['actif'] = $actif;
				// on recherche d'eventuelle mises a jour existantes
				if ($res = sql_allfetsel(
					array('pl.id_plugin', 'pa.version'),
					array('spip_plugins AS pl', 'spip_paquets AS pa'),
					array(
						'pl.id_plugin = pa.id_plugin',
						'pa.id_depot>' . sql_quote(0),
						'pl.prefixe=' . sql_quote($prefixe),
						'pa.etatnum>=' . sql_quote($le_paquet['etatnum']))))
					{

					foreach ($res as $paquet_distant) {
						// si version superieure et etat identique ou meilleur,
						// c'est que c'est une mise a jour possible !
						if (spip_version_compare($paquet_distant['version'],$le_paquet['version'],'>')) {
							if (!strlen($le_paquet['maj_version']) or spip_version_compare($paquet_distant['version'], $le_paquet['maj_version'], '>')) {
								$le_paquet['maj_version'] = $paquet_distant['version'];
							}
							# a voir si on utilisera...
							# "superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL",
							# // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
						}
					}
				}
				$insert_paquets[] = $le_paquet;
			}
		}
	}

	if ($insert_plugins) {
		sql_insertq_multi('spip_plugins', $insert_plugins);
		$pls = sql_allfetsel(array('id_plugin', 'prefixe'), 'spip_plugins', sql_in('prefixe', array_keys($insert_plugins)));
		foreach ($pls as $p) {
			$cle_plugins[$p['prefixe']] = $p['id_plugin'];
		}
	}
	
	if ($insert_paquets) {

		$obsoletes = array();
		foreach ($insert_paquets as $c => $p) {
			$insert_paquets[$c]['id_plugin'] = $cle_plugins[$p['prefixe']];
			unset($insert_paquets[$c]['prefixe']);
			$obsoletes[$p['prefixe']][] = $c;
			// si 2 paquet locaux ont le meme prefixe, mais pas la meme version,
			// l'un est obsolete : la version la plus ancienne
			if (count($obsoletes[$p['prefixe']]) > 1) {
				foreach ($obsoletes[$p['prefixe']] as $cle) {
					if ($cle == $c) continue;
					
					// je suis plus petit qu'un autre
					if (spip_version_compare($insert_paquets[$c]['version'], $insert_paquets[$cle]['version'], '<')) {
						if ($insert_paquets[$c]['etatnum'] <= $insert_paquets[$cle]['etatnum']) {
							$insert_paquets[$c]['obsolete'] = 'oui';
						}
					}
					
					// je suis plus grand qu'un autre...
					// si mon etat est meilleur, rendre obsolete les autres
					elseif ($insert_paquets[$c]['etatnum'] > $insert_paquets[$cle]['etatnum']) {
						$insert_paquets[$cle]['obsolete'] = 'oui';
					}
					
				}
			}

			// remettre les necessite, utilise, librairie dans la cle 0
			// comme SVP
			if ($dep = unserialize($insert_paquets[$c]['dependances']) and is_array($dep)) {
				foreach ($dep as $d => $contenu) {
					if ($contenu) {
						$new = array();
						foreach($contenu as $n) {
							unset($n['id']);
							$new[ strtolower($n['nom']) ] = $n;
						}
						$dep[$d] = array($new);
					}
				}
				$insert_paquets[$c]['dependances'] = serialize($dep);
			}

		}

		sql_insertq_multi('spip_paquets', $insert_paquets);
	}

	if (count($cle_plugins)) {
		svp_corriger_vmax_plugins(array_values($cle_plugins));
	}
}


/**
 * Fait correspondre l'état des métas des plugins actifs & installés
 * avec ceux en base de données dans spip_paquets pour le dépot local 
**/
function svp_base_actualiser_paquets_actifs() {
	$installes  = lire_config('plugin_installes');
	$actifs  = lire_config('plugin');

	$locaux = sql_allfetsel(
		array('pa.id_paquet', 'pl.prefixe', 'pa.actif', 'pa.installe', 'pa.constante', 'pa.src_archive'),
		array('spip_paquets AS pa', 'spip_plugins AS pl'),
		array('pa.id_plugin=pl.id_plugin', 'id_depot='.sql_quote(0)));
	$changements = array();

	foreach ($locaux as $l) {
		$copie = $l;
		$prefixe = strtoupper($l['prefixe']);
		// actif ?
		if (isset($actifs[$prefixe])
			and ($actifs[$prefixe]['dir_type'] == $l['constante'])
			and ($actifs[$prefixe]['dir'] == $l['src_archive'])) {
			$copie['actif'] = "oui";
		} else {
			$copie['actif'] = "non";
		}
			
		// installe ?
		if (in_array($l['src_archive'], $installes)) {
			$copie['installe'] = "oui";
		} else {
			$copie['installe'] = "non";
		}

		if ($copie != $l) {
			$changements[ $l['id_paquet'] ] = array( 'actif'=> $copie['actif'], 'installe'=>$copie['installe'] );
		}
	}

	if (count($changements)) {
		// On insere, en encapsulant pour sqlite...
		if (sql_preferer_transaction()) {
			sql_demarrer_transaction();
		}
				
		foreach ($changements as $id_paquet => $data) {
			sql_updateq('spip_paquets', $data, 'id_paquet=' . intval($id_paquet));
		}
		
		if (sql_preferer_transaction()) {
			sql_terminer_transaction();
		}
	}

}

?>
