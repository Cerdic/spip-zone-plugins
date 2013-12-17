<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajoute un message de log dans tmp/migrateur/migrateur.log
 * ainsi que dans tmp/migrateur/etape.log et tmp/log/migrateur.log !
 *
 * @param string $msg Le message
 * @param string $type Type de message
**/
function migrateur_log($msg, $type="") {
	static $done = false;
	$dir = _DIR_TMP . 'migrateur';
	if (!$done) {
		sous_repertoire(_DIR_TMP . 'migrateur');
	}

	if ($type) $message = '[' . $type . '] ' . $message;

	file_put_contents($dir . "/migrateur.log", date("Y:m:d H:i:s") . " > " . $msg . "\n", FILE_APPEND);
	file_put_contents($dir . "/etape.log", $msg . "\n", FILE_APPEND);
	spip_log($msg, 'migrateur');
}


/**
 * Vider les caches, tous les caches !
 *
 * Vider le cache de SPIP (voir action/purger.php)
**/
function migrateur_vider_cache() {
	migrateur_log("Vider le cache");
	include_spip('inc/invalideur');
	supprime_invalideurs();
	@spip_unlink(_CACHE_RUBRIQUES);
	@spip_unlink(_CACHE_PIPELINES);
	@spip_unlink(_CACHE_PLUGINS_PATH);
	@spip_unlink(_CACHE_PLUGINS_OPT);
	@spip_unlink(_CACHE_PLUGINS_FCT);
	@spip_unlink(_CACHE_CHEMIN);
	@spip_unlink(_DIR_TMP."plugin_xml_cache.gz");
	purger_repertoire(_DIR_CACHE,array('subdir'=>true));
	purger_repertoire(_DIR_AIDE);
	purger_repertoire(_DIR_VAR.'cache-css');
	purger_repertoire(_DIR_VAR.'cache-js');
	@spip_unlink(_FILE_META);
}



/**
 * Active un ou plusieurs plugins locaux ayant le préfixe indiqué (nécessite STEP)
 *
 * @param string|array $prefixes
 *     Liste de prefixes de plugins à activer
 * @param string $redirect
 *     URL de redirection, sinon prend dans _request()
**/
function migrateur_activer_plugin_prefixes($prefixes, $redirect=null) {
	if (!$prefixes) return false;

	if (is_null($redirect)) {
		$redirect = _request('redirect');
	}

	if (!is_array($prefixes)) {
		$prefixes = array($prefixes);
	}

	include_spip('inc/step');

	migrateur_log("Actualisation de la base des plugins locaux de STEP");
	step_actualiser_plugins_locaux();

	// simple, mais non affichage d'erreurs en retour.
#	include_spip('inc/step');
#	step_install($prefixes, $redirect);

	// on fait donc une formule compliquée

	$prefixes_minuscule = array_map('strtolower', $prefixes);

	// recuperer les ids des plugins souhaites
	$ids_paquets = sql_allfetsel('id_plugin', 'spip_plugins', array(
		sql_in('prefixe', $prefixes_minuscule),
		'obsolete=' . sql_quote('non'),
		'id_zone=' . sql_quote(0)
	), 'prefixe', 'etatnum DESC');
	if ($ids_paquets) {
		$ids_paquets = array_map('array_shift', $ids_paquets);
	} else {
		$ids_paquets = array();
	}

	migrateur_log('Activer les paquets : ' . implode(',', $prefixes) . ' ( ' . implode(',', $ids_paquets) . ' )');

	include_spip('inc/step_decideur');
	include_spip('inc/step_actionneur');

	$a_actionner = array();
	foreach ($ids_paquets as $i) {
		$a_actionner[$i] = 'on';
	}

	$decideur = new Decideur;
	#$decideur->erreur_sur_maj_introuvable = false;
	$ok = $decideur->verifier_dependances($a_actionner);

	if (!$ok) {
		migrateur_log('[Erreur] Sur le calcul de dépendance');
		foreach ($decideur->err as $id=>$errs) {
			foreach($errs as $err) {
				migrateur_log($err);
			}
		}
		return false;
	}

	$rien = true;
	if ($do = $decideur->presenter_actions('ask')) {
		$rien = false;
		migrateur_log('Plugins demandés :');
		foreach ($do as $desc) { migrateur_log('- ' . $desc); }
	}
	if ($do = $decideur->presenter_actions('changes')) {
		$rien = false;
		migrateur_log('Actions supplémentaires :');
		foreach ($do as $desc) { migrateur_log('- ' . $desc); }
	}

	if ($rien) {
		migrateur_log('[Erreur potentielle !] STEP n\'a rien à faire ?');
		if ($do = $decideur->presenter_actions('todo')) {
			foreach ($do as $desc) { migrateur_log('- ' . $desc); }
		}
	}

	// On construit la liste des actions pour la passer au formulaire en hidden
	$todo = array();
	foreach ($decideur->todo as $_todo) {
		$todo[$_todo['i']] = $_todo['todo'];
	}

	$actionneur = new Actionneur();
	$actionneur->ajouter_actions($todo);
	#$actionneur->verrouiller();
	$actionneur->sauver_actions();

	#$action = generer_url_action('actionner', 'redirect='.urlencode($redirect), '&');
	$action = str_replace('&amp;','&', generer_action_auteur('step_install', '', $redirect));
	include_spip('inc/headers');
	migrateur_log('=> Redirection sur STEP');
	redirige_par_entete($action);
}


/**
 * Active un ou plusieurs plugins locaux ayant le chemin indiqué
 *
 * @param string|array $chemins
 *     Liste de chemins de plugins à activer
 * @param string $redirect
 *     URL de redirection, sinon prend dans _request()
**/
function migrateur_activer_plugin_chemins($chemins, $redirect=null) {
	if (!$chemins) return false;

	if (is_null($redirect)) {
		$redirect = _request('redirect');
	}

	if (!is_array($chemins)) {
		$chemins = array($chemins);
	}

	include_spip('inc/plugin');
	migrateur_log('Ajout des plugins actifs : ' . implode(', ', $chemins));
	ecrire_plugin_actifs($chemins,false,'ajoute');

	$action = generer_url_ecrire('admin_plugin');
	include_spip('inc/headers');
	migrateur_log('=> Redirection sur la page Plugins pour terminer leur installation');
	redirige_par_entete($action);
}





/**
 * Déplace le contenu d'une table dans une autre en s'appuyant sur un tableau de correspondance
 * des champs, et en supposant que la table destination est vide au départ
 *
 * @param string $table_source
 *     Nom de la table SQL source, tel que 'spip_trucs_old'
 * @param string $table_destination
 *     Nom de la table SQL source, tel que 'spip_trucs'
 * @param array $correspondances
 *     Couples de correspondances : nom du champ ancien => nom du champ nouveau.
 *     Si le nouveau champ est vide, la colonne ancienne n'est pas importée.
 * @param array $options
 *     Tableau d'options
 *     - string 'callback_ligne' : fonction de callback modifiant une ligne insérée
**/
function migrateur_deplacer_table_complete($table_source, $table_destination, $correspondances = array(), $options = array()) {

	$options = $options + array(
		// fonction de callback modifiant une ligne insérée
		// 'callback_ligne' => 'toto',
		// function toto($données, $anciennes_données) { ... return $donnees; }
		'callback_ligne' => '',
	);

	// transposer les donnees dans la nouvelle structure
	$inserts = array();
	$valeurs = sql_allfetsel('*', $table_source);
	if (!is_array($valeurs) OR !$valeurs) {
		migrateur_log("% Insertion dans $table_destination : source $table_source absente ou vide)");
		return true;
	}

	// on remet les noms des cles dans le tableau de valeur
	// en s'assurant de leur correspondance au passage
	$callback = $options['callback_ligne'];
	foreach ($valeurs as $v) {
		$i = array();
		foreach ($v as $cle => $valeur) {
			if (isset($correspondances[$cle]) and $correspondances[$cle]) {
				$i[ $correspondances[$cle] ] = $valeur;
			}
		}
		$inserts[] = $callback ? $callback($i, $v) : $i;
	}
	unset($valeurs);

	// inserer les donnees en base.
	$nb_inseres = 0;
	// ne pas reimporter ceux deja la (en cas de timeout)
	$nb_deja_la = sql_countsel($table_destination);
	$nb_total   = count($inserts);


	// on ecrit un gentil message pour suivre l'avancement.
	migrateur_log("Insertion dans $table_destination (depuis $table_source)");
	migrateur_log("  - $nb_deja_la sont déjà là (sur $nb_total)");

	// tout est déjà là !
	if ($nb_total == $nb_deja_la) {
		return true;
	}

	$inserts = array_slice($inserts, $nb_deja_la);
	$nb_a_inserer = count($inserts);

	migrateur_log("  - $nb_a_inserer sont à insérer");

	// on decoupe en petit bout (pour reprise sur timeout)
	$inserts = array_chunk($inserts, 100);
	foreach ($inserts as $i) {
		sql_insertq_multi($table_destination, $i);
		$nb_inseres += count($i);

		// serie_alter() relancera la fonction jusqu'a ce que l'on sorte sans timeout.
		if (time() >= _TIME_OUT) {
			// on ecrit un gentil message pour suivre l'avancement.
			migrateur_log("  [relance] Insertion dans $table_destination relancée");
			migrateur_log("  - $nb_inseres ont été insérés");
			$a_faire = $nb_a_inserer - $nb_inseres;
			migrateur_log("  - $a_faire sont à insérer");

			#$redirect = generer_url_action('migrateur', _request('arg'), true);
			$redirect = url_de_base() . _DIR_RESTREINT_ABS . '?' . $_SERVER['QUERY_STRING'];
			$redirect = parametre_url($redirect, 'redirect', _request('redirect'), '&');
			$redirect = parametre_url($redirect, 'recharger', 1, '&');
			migrateur_log("  --> Recharger \n\n");

			include_spip('inc/headers');
			#var_dump($redirect); die();
			redirige_par_entete($redirect);
			return false; // aucazou
		}
	}

	migrateur_log("  - $nb_inseres ont été insérés");


	return true;
}


/**
 * Obtenir dans la base en cours la liste des plugins actifs
 * ayant un certain terme dans leur préfixe.
 *
 * @param string $terme
 *     Terme cherché, par exemple 'migrateur'
 * @return array
 *     Chemins vers les plugins actifs ayant ce terme
**/
function migrateur_obtenir_plugins_actifs($terme) {
	migrateur_log("Extraire les plugins actifs ayant '$terme'");
	$plugins = array();

	$plugins_actifs = sql_getfetsel('valeur', 'spip_meta', 'nom=' . sql_quote('plugin'));

	if ($plugins_actifs and ($plugins_actifs = unserialize($plugins_actifs))) {
		foreach ($plugins_actifs as $prefixe => $infos ) {
			if (stripos($prefixe, $terme) !== false) {
				$plugins[$prefixe] = $infos;
			}
		}
	}

	migrateur_log("-> Plugins trouvés : " . implode(',', array_keys($plugins)));

	return $plugins;
}



/**
 * Ajouter des plugins actifs à la base en cours
 *
 * @note
 *   Il vaut mieux passer par SVP ou la fonction
 *   migrateur_activer_plugin_prefixes()
 *
 * @param array $plugins
 *     Couples (prefixe => infos)
**/
function migrateur_ajouter_plugins_actifs($plugins) {
	migrateur_log("Ajouter les plugins actifs : " . implode(',', array_keys($plugins)));

	$plugins_migrateurs = array();

	$plugins_actifs = sql_getfetsel('valeur', 'spip_meta', 'nom=' . sql_quote('plugin'));
	if ($plugins_actifs and $plugins_actifs = unserialize($plugins_actifs)) {
		$plugins_actifs = array_merge($plugins_actifs, $plugins);
	} else {
		$plugins_actifs = $plugins;
	}

	if (is_array($plugins_actifs)) {
		ecrire_meta('plugin', serialize($plugins_actifs));
	}
}
