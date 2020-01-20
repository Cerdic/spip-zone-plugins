<?php

/*
    This file is part of Salvatore, the translation robot of Trad-lang (SPIP)

    Salvatore is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003-2013
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
 		kent1 <kent1@arscenic.info>
*/


/**
 * initialiser salvatore si besoin
 * peut etre appelle plusieurs fois
 * @param string|array $log_function
 * @throws Exception
 */
function salvatore_init($log_function = null){
	static $initialized;

	// set log function if any
	if ($log_function){
		salvatore_log('', $log_function);
	}

	if (is_null($initialized)){
		@ini_set('memory_limit', '50M');
		if (!defined('_DEBUG_TRAD_LANG')){
			define('_DEBUG_TRAD_LANG', 1); // undef si on ne veut pas de messages
		}

		if (!defined('_DIR_SALVATORE')){
			define('_DIR_SALVATORE', _DIR_RACINE . 'salvatore/');
		}

		if (!defined('_DIR_SALVATORE_TRADUCTIONS')){
			define('_DIR_SALVATORE_TRADUCTIONS', _DIR_SALVATORE . 'traductions/');
		}

		if (!defined('_DIR_SALVATORE_TMP')){
			define('_DIR_SALVATORE_TMP', _DIR_SALVATORE . 'tmp/');
		}

		if (!defined('_DIR_SALVATORE_MODULES')){
			define('_DIR_SALVATORE_MODULES', _DIR_SALVATORE . 'modules/');
		}

		if (!defined('_DIR_SALVATORE_DEPOTS')){
			define('_DIR_SALVATORE_DEPOTS', _DIR_SALVATORE . 'depots/');
		}

		if (defined('_ID_AUTEUR_SALVATORE') and is_numeric(_ID_AUTEUR_SALVATORE)){
			$GLOBALS['visiteur_session'] = array();
			$GLOBALS['visiteur_session']['id_auteur'] = _ID_AUTEUR_SALVATORE;
			// TODO : charger une session complete ?
		}

		// par defaut on relit les fichiers si modifies depuis moins de 1J
		if (!defined('_SALVATORE_LECTEUR_REFRESH_DELAY')){
			define('_SALVATORE_LECTEUR_REFRESH_DELAY', 24 * 3600);
		}

		// pourcentage de traduction a partir duquel on exporte la langue
		if (!defined('_SALVATORE_SEUIL_EXPORT')) {
			define('_SALVATORE_SEUIL_EXPORT', 50);
		}

		if (!defined('_SALVATORE_AUTHOR_COMMITS')) {
			define('_SALVATORE_AUTHOR_COMMITS', 'Salvatore <salvatore@rezo.net>');
		}

		if (!defined('_SALVATORE_SVN_PROPSET')) {
			define('_SALVATORE_SVN_PROPSET', true);
		}


		if (!isset($GLOBALS['idx_lang'])){
			$GLOBALS['idx_lang'] = 0;
		}

		// verifications des repertoires
		foreach ([_DIR_SALVATORE, _DIR_SALVATORE_TRADUCTIONS, _DIR_SALVATORE_MODULES, _DIR_SALVATORE_DEPOTS, _DIR_SALVATORE_TMP] as $dir){
			salvatore_check_dir($dir);
		}
		$initialized = true;
	}
}


/**
 * chargement du fichier traductions.txt
 * Construit une liste de modules avec pour chacun un tableau associatif
 *
 * @param string $fichier_traductions
 * @return array
 * @throws Exception
 */
function salvatore_charger_fichier_traductions($fichier_traductions = null){

	salvatore_init();
	if (is_null($fichier_traductions)){
		$fichier_traductions = _DIR_SALVATORE_TRADUCTIONS . 'traductions.txt';
	}
	salvatore_check_file($fichier_traductions);

	$lignes = file($fichier_traductions);
	$lignes = array_map('trim', $lignes);
	$lignes = array_filter($lignes);

	$liste_trad = array();
	foreach ($lignes as $ligne){
		if ($ligne[0]!=='#'){
			$liste = explode(';', trim($ligne));
			$methode = $url = $branche = $dir = $module = $lang = '';

			// deprecated ancien format, forcement en svn
			// liste courte de type
			// url;module;lang
			if (count($liste)<=3){
				$methode = 'svn';
				$branche = '';
				$url = $liste[0];
				if (empty($liste[1])){
					$module = preg_replace('#.*/(.*)$#', '$1', $url);
				} else {
					$module = $liste[1];
				}
				if (empty($liste[2])){
					$lang = 'fr';
				} else {
					$lang = $liste[2];
				}
			}
			// format complet et explicite de 6 valeurs
			// seule les valeurs pour branche et dir peuvent etre vide (branche master par defaut en git)
			// svn;url;;;module;lang
			// git;url;master;subdir/tolang;module;lang
			else {
				list($methode, $url, $branche, $dir, $module, $lang) = $liste;
			}
			$methode = trim($methode);
			$url = trim($url);
			$url = rtrim($url, '/'); // homogeneiser
			$dir = trim($dir);
			$dir = trim($dir, '/'); // homogeneiser
			$branche = trim($branche);
			$module = trim($module);
			$lang = trim($lang);

			if ($methode
				and $url
				and $module
				and $lang){
				// que fait la $GLOBALS['modules'] ?
				if (empty($GLOBALS['modules']) or in_array($module, $GLOBALS['modules'])){
					// definir un dir checkout unique meme si plusieurs modules de meme nom dans differents repos
					$d = explode('/', $url);
					while (count($d) and in_array(end($d), ['', 'lang', 'trunk', 'ecrire'])){
						array_pop($d);
					}
					$source = '';
					if (end($d)){
						$source = basename(end($d), '.git');
						$source = '--' . preg_replace(',[^\w-],', '_', $source);
					}
					$dir_module = "{$module}{$source}-" . substr(md5("$methode:$url:$branche:$dir"), 0, 5);
					$dir_checkout = preg_replace(",\W+,", "-", "$methode-$url") . ($branche ? "--$branche-" : "-") . substr(md5("$methode:$url:$branche"), 0, 5);

					$liste_trad[] = [
						'methode' => $methode,
						'url' => $url,
						'branche' => $branche,
						'dir' => $dir,
						'module' => $module,
						'lang' => $lang,
						'dir_module' => $dir_module,
						'dir_checkout' => $dir_checkout,
					];
				}
			} else {
				salvatore_log("Fichier $fichier_traductions, IGNORE ligne incomplete : $ligne");
			}
		}
	}
	return $liste_trad;
}

/**
 * Filtrer la liste complete pour ne garder que un ou plusieurs modules specifiques
 * @param array $liste_trad
 * @param string|array $modules
 * @return array
 */
function salvatore_filtrer_liste_traductions($liste_trad, $modules) {
	if (is_string($modules)) {
		$modules = explode(',', $modules);
	}
	$modules = array_map('trim', $modules);
	$liste_filtree = array();
	foreach ($liste_trad as $trad) {
		if (in_array($trad['module'], $modules)) {
			$liste_filtree[] = $trad;
		}
	}
	return $liste_filtree;
}

/**
 * Extraire la lang d'un fichier de langue d'un module donne
 * @param string $module
 * @param string $fichier_lang
 * @return array|mixed|string|string[]
 */
function salvatore_get_lang_from($module, $fichier_lang) {
	$lang = str_replace($module, '__', basename($fichier_lang, '.php'));
	$lang = explode('___', $lang, 2);
	$lang = end($lang);

	return $lang;
}

/**
 * URL du gestionnaire trad-lang exportee dans les xml
 * @return mixed
 */
function salvatore_get_self_url() {
	$url_gestionnaire = $GLOBALS['meta']['adresse_site'];
	if (defined('_SALVATORE_TEST_URL_GESTIONNAIRE')) {
		$url_gestionnaire = _SALVATORE_TEST_URL_GESTIONNAIRE;
	}
	return $url_gestionnaire;
}

/**
 * Verifier si un module de langue est gere par ce salvatore
 * @param $dir_module
 * @param $module
 * @return string
 *   l'autre gestionnaire de trad si c'est pas nous
 *   chaine vide si c'est bien nous qui gerons
 */
function salvatore_verifier_gestionnaire_traduction($dir_module, $module) {

	/**
	 * On teste ici si le fichier est géré par un autre salvatore
	 * Si oui on empeche son import en le signifiant
	 */
	if ($t = salvatore_lire_gestionnaire_traduction($dir_module, $module)){
		$url = extraire_attribut($t, 'url');
		$gestionnaire = extraire_attribut($t, 'gestionnaire');
		$url_gestionnaire = salvatore_get_self_url();
		if ($gestionnaire !== 'salvatore'
		  or protocole_implicite($url) !== protocole_implicite($url_gestionnaire)) {
			return "$gestionnaire@$url";
		}
	}

	return '';
}

/**
 * Lire la balise <traduction> du fichier .xml
 * @param string $dir_module
 * @param string $module
 * @return string
 */
function salvatore_lire_gestionnaire_traduction($dir_module, $module) {
	$xml_file = $dir_module . '/' . $module . '.xml';
	/**
	 * On teste ici si le fichier est géré par un autre salvatore
	 * Si oui on empeche son import en le signifiant
	 */
	if (file_exists($xml_file)){
		$xml_content = spip_xml_load($xml_file);
		if (is_array($xml_content)){
			// normalement on a qu'une balise <traduction...> englobante, donc on prend la premiere qu'on trouve
			if (spip_xml_match_nodes('/^traduction/', $xml_content, $matches)
			  and $nodes = array_keys($matches)
			  and $node = reset($nodes)) {
				return "<$node>";
			}
		}
	}
	return '';
}

/**
 * Retrouver la ligne de spip_tradlang_modules qui correspond a un dir_module/module, meme en cas de chanchement de repo (url/branches)
 * Attention : ca veut dire que si on branche et qu'on veut traduire 2 branches d'un meme module
 * il faut supprimer le fichier xml de la nouvelle branche pour qu'elle soit bien ajoutee a trad-lang
 * et eviter qu'on pense que c'est un renommage
 *
 * @param $dir_module
 * @param $module
 * @return array|bool
 */
function salvatore_retrouver_tradlang_module($dir_module, $module) {
	$base_dir_module = basename($dir_module);
	if ($row_module = sql_fetsel('*', 'spip_tradlang_modules', 'dir_module = ' . sql_quote($base_dir_module))) {
		return $row_module;
	}

	// peut-etre c'est un module qui a change d'url repo, et donc son dir_module a change ?
	// sur la balise <traduction> le dir_module est ecrit dans id
	if ($t = salvatore_lire_gestionnaire_traduction($dir_module, $module)
	  and $old_dir_module = extraire_attribut($t, 'id')
	  and $old_dir_module !== $base_dir_module){

		if ($row_module = sql_fetsel('*', 'spip_tradlang_modules', 'dir_module = ' . sql_quote($old_dir_module))) {
			return $row_module;
		}
	}

	return false;
}

/**
 * Ajouter les credentials user/pass sur les urls de repo
 * @param string $methode
 * @param string $url_repository
 * @param string $module
 * @return string
 */
function salvatore_set_credentials($methode, $url_repository, $module){
	global $domaines_exceptions, $domaines_exceptions_credentials,
	       $SVNUSER, $SVNPASSWD,
	       $GITUSER, $GITPASSWD;

	// on ne sait pas mettre des credentials si c'est du ssh
	if (strpos($url_repository, '://')!==false){
		$user = $pass = false;
		$parts = parse_url($url_repository);
		if (empty($parts['user']) and empty($parts['pass'])){
			$host = $parts['host'];
			require_once(_DIR_ETC . 'salvatore_passwd.inc');

			if (!empty($domaines_exceptions)
				and is_array($domaines_exceptions)
				and in_array($host, $domaines_exceptions)){
				// on est dans une exception

				/**
				 * Est-ce que cette exception dispose de credentials (Github?)
				 */
				if (is_array($domaines_exceptions_credentials)
					and !empty($domaines_exceptions_credentials[$host])){
					$user = $domaines_exceptions_credentials[$host]['user'];
					$pass = $domaines_exceptions_credentials[$host]['pass'];
				}

			} else {
				// un truc perso pour un module en particulier ?
				if (isset(${$module . '_user'})){
					$user = ${$module . '_user'};
					$pass = ${$module . '_passwd'};
				} elseif ($methode==='svn' and isset($SVNUSER)) {
					$user = $SVNUSER;
					$pass = $SVNPASSWD;
				} elseif ($methode==='git' and isset($GITUSER)) {
					$user = $GITUSER;
					$pass = $GITPASSWD;
				}
			}

			if ($user and $pass){
				$url_repository = str_replace("://$host", "://" . urlencode($user) . ":" . urlencode($pass) . "@$host", $url_repository);
			}
		}

	}

	return $url_repository;
}


/**
 * Verifier qu'un repertoire existe
 * @param $dir
 * @throws Exception
 */
function salvatore_check_dir($dir){
	if (!is_dir($dir)){
		throw new Exception("Erreur : le répertoire $dir n'existe pas");
	}
}

/**
 * Verifier qu'un fichier existe
 * @param $file
 * @throws Exception
 */
function salvatore_check_file($file){
	if (!file_exists($file)){
		throw new Exception("Erreur : Le fichier $file est introuvable");
	}
}

/**
 * Loger
 * @param string $msg
 * @param string|array $display_function
 */
function salvatore_log($msg = '', $display_function = null){
	static $function = null;

	if ($display_function and is_callable($display_function)){
		$function = $display_function;
	}

	if (defined('_DEBUG_TRAD_LANG')
		and _DEBUG_TRAD_LANG
		and $msg){
		if ($function){
			call_user_func($function, rtrim($msg));
		} else {
			// fallback : utiliser echo mais enlever les balises de formatage symphony
			$msg = str_replace(["<info>", "</info>", "<error>", "</error>", "<comment>", "</comment>", "<question>", "</question>", "</>"], "", $msg);
			echo rtrim($msg) . "\n";
		}
	}
}

/**
 * Echec sur erreur : on envoie un mail si possible et on echoue en lançant une exception
 * @param $sujet
 * @param $corps
 * @throws Exception
 */
function salvatore_fail($sujet, $corps){
	$corps = rtrim($corps) . "\n\n";
	salvatore_envoyer_mail($sujet, $corps);
	throw new Exception($corps);
}

/**
 * @param string $sujet
 * @param string $corps
 */
function salvatore_envoyer_mail($sujet = 'Erreur', $corps = ''){
	if (defined('_EMAIL_ERREURS') and _EMAIL_ERREURS
		and defined('_EMAIL_SALVATORE') and _EMAIL_SALVATORE){
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		$destinataire = _EMAIL_ERREURS;
		$from = _EMAIL_SALVATORE;
		$envoyer_mail($destinataire, $sujet, $corps, $from);
		salvatore_log("Un email a été envoyé à l'adresse : " . _EMAIL_ERREURS . "\n");
	}
}


/**
 * Verifier que la base de salvatore a bien ete mise a jour
 * pour ajouter le dir_module qui est la cle unique a la place de module
 * lancer
 * spip salvatore:upgrade --traductions=...
 * avec le bon fichier de traduction pour mettre à jour la base de salvatore avant de pouvoir lancer a nouveau le lecteur ou l'ecriveur
 */
function salvatore_verifier_base_upgradee() {

	$schema_declare = filtre_info_plugin_dist('tradlang', 'schema');
	$schema_base = $GLOBALS['meta']['tradlang_base_version'];
	if ($schema_base !== $schema_declare) {
		throw new Exception("Schema de base pas a jour ($schema_base vs $schema_declare). Lancez la commande \nspip salvatore:upgrade --help");
	}

	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_tradlang_modules');

	// est-ce que le champ a ete cree ?
	if (!isset($desc['field']['dir_module'])) {
		throw new Exception("Pas de champ dir_module dans la base spip_tradlang_modules. Lancez la commande \nspip salvatore:upgrade --help");
	}

	// est-ce que tous les modules en base on bien eu un dir_module affecte (et ni vide ni =module qui est la valeur par defaut lors de l'upgrade de base)
	$nb = sql_countsel('spip_tradlang_modules', "dir_module='' OR dir_module=module");
	if ($nb>0) {
		throw new Exception("Le champ dir_module de spip_tradlang_modules n'est pas renseigne pour tous les modules. Lancez la commande \nspip salvatore:upgrade --help");
	}

}

/**
 * Nettoyer la chaine de langue (venant du fichier PHP en lecture ou de la base en ecriture)
 * @param string $chaine
 * @param string $lang
 * @return string
 */
function salvatore_nettoyer_chaine_langue($chaine, $lang){
	static $typographie_functions = array();

	if (!isset($typographie_functions[$lang])){
		$typo = (in_array($lang, array('eo', 'fr', 'cpf')) || strncmp($lang, 'fr_', 3)==0) ? 'fr' : 'en';
		$typographie_functions[$lang] = charger_fonction($typo, 'typographie');
	}

	/**
	 * On enlève les sauts de lignes windows pour des sauts de ligne linux
	 */

	$chaine = str_replace("\r\n", "\n", $chaine);

	/**
	 * protection dans les balises genre <a href="..." ou <img src="..."
	 * cf inc/filtres
	 */
	if (preg_match_all(_TYPO_BALISE, $chaine, $regs, PREG_SET_ORDER)){
		foreach ($regs as $reg){
			$insert = $reg[0];
			// hack: on transforme les caracteres a proteger en les remplacant
			// par des caracteres "illegaux". (cf corriger_caracteres())
			$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
			$chaine = str_replace($reg[0], $insert, $chaine);
		}
	}

	/**
	 * Protéger le contenu des balises <html> <code> <cadre> <frame> <tt> <pre>
	 */
	define('_PROTEGE_BLOCS_HTML', ',<(html|code|cadre|pre|tt)(\s[^>]*)?>(.*)</\1>,UimsS');
	if ((strpos($chaine, '<')!==false) and preg_match_all(_PROTEGE_BLOCS_HTML, $chaine, $matches, PREG_SET_ORDER)){
		foreach ($matches as $reg){
			$insert = $reg[0];
			// hack: on transforme les caracteres a proteger en les remplacant
			// par des caracteres "illegaux". (cf corriger_caracteres())
			$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
			$chaine = str_replace($reg[0], $insert, $chaine);
		}
	}

	/**
	 * On applique la typographie de la langue
	 */
	$chaine = $typographie_functions[$lang]($chaine);

	/**
	 * On remet les caractères normaux sur les caractères illégaux
	 */
	$chaine = strtr($chaine, _TYPO_PROTECTEUR, _TYPO_PROTEGER);

	$chaine = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $chaine), ENT_NOQUOTES, 'utf-8'));

	return $chaine;
}