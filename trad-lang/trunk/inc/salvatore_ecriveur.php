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

    Copyright 2003-2020
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
        kent1 <kent1@arscenic.info>
        Cerdic <cedric@yterium.com>
*/

include_spip('base/abstract_sql');
include_spip('inc/charsets');
include_spip('inc/config');
include_spip('inc/filtres');
include_spip('inc/texte');
include_spip('inc/xml');
include_spip('inc/lang_liste');
include_spip('inc/session');


/**
 * @param array $liste_sources
 * @param string $message_commit
 * @param string $dir_modules
 * @param string $dir_depots
 * @throws Exception
 */
function salvatore_ecrire($liste_sources, $message_commit='', $dir_modules = null, $dir_depots=null){
	include_spip('inc/salvatore');
	salvatore_init();

	// on va lire dans la base, il faut qu'elle soit a jour
	salvatore_verifier_base_upgradee();

	if (is_null($dir_modules)){
		$dir_modules = _DIR_SALVATORE_MODULES;
	}
	salvatore_check_dir($dir_modules);

	if (is_null($dir_depots)) {
		$dir_depots = _DIR_SALVATORE_DEPOTS;
	}
	salvatore_check_dir($dir_depots);

	$url_gestionnaire = salvatore_get_self_url();

	foreach ($liste_sources as $source){
		salvatore_log("\n<info>--- Module " . $source['module'] . " | " . $source['dir_module'] . " | " . $source['url'] . "</info>");

		$module = $source['module'];
		$dir_module = $dir_modules . $source['dir_module'];

		if ($autre_gestionnaire = salvatore_verifier_gestionnaire_traduction($dir_module, $module)){
			salvatore_fail("[Ecriveur] Erreur sur $module", "Erreur : export impossible, le fichier est traduit autre part : $autre_gestionnaire\n");
		}

		$id_tradlang_module = sql_getfetsel('id_tradlang_module', 'spip_tradlang_modules', 'dir_module = ' . sql_quote($source['dir_module']));
		if (!$id_tradlang_module) {
			salvatore_fail("[Ecriveur] Erreur sur $module", "Erreur : export impossible, le module n'est pas en base\n");
		}
		else {
			// url de l'interface de traduction d'un module
			$url_trad_module = url_absolue(generer_url_entite($id_tradlang_module, 'tradlang_module'), $url_gestionnaire);
			salvatore_exporter_module($id_tradlang_module, $source, $url_gestionnaire, $url_trad_module, $dir_modules, $dir_depots, $message_commit);
		}
	}
}

/**
 * Genere les fichiers de traduction d'un module
 *
 * @param int $id_tradlang_module
 * @param array $source
 * @param string $url_site
 * @param string $url_trad_module
 * @param string $dir_modules
 * @param string $dir_depots
 * @param string $message_commit
 * @return false|int
 */
function salvatore_exporter_module($id_tradlang_module, $source, $url_site, $url_trad_module, $dir_modules, $dir_depots, $message_commit = ''){

	$url_repo = $source['url'];

	$row_module = sql_fetsel('*', 'spip_tradlang_modules', 'id_tradlang_module=' . intval($id_tradlang_module));
	if (!$row_module) {
		$module = $source['module'];
		salvatore_log("<error>Le module #$id_tradlang_module $module n'existe pas</error>");
		return false;
	}
	$lang_ref = $row_module['lang_mere'];
	$dir_module = $dir_modules . $row_module['dir_module'];
	$module = $row_module['module'];

	if (is_numeric($row_module['limite_trad']) and $row_module['limite_trad']>0){
		$seuil_export = $row_module['limite_trad'];
	}
	else {
		$seuil_export = lire_config('tradlang/seuil_export_tradlang', _SALVATORE_SEUIL_EXPORT);
	}


	$xml_infos = $commit_infos = array();
	$liste_lang = $liste_lang_non_exportees = $liste_lang_a_supprimer = array();

	$count_trad_reference = sql_countsel('spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($row_module['lang_mere']) . " AND statut='OK'", 'id');
	$minimal = ceil((($count_trad_reference*$seuil_export)/100));
	salvatore_log("Minimal = $minimal ($seuil_export %)");

	$langues = sql_allfetsel('lang,COUNT(*) as count', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . " AND statut != 'NEW' AND statut != 'attic'", 'lang', 'lang');
	foreach ($langues as $langue){
		/**
		 * Le fichier est il suffisamment traduit
		 */
		if ($langue['count']>=$minimal){
			$liste_lang[] = $langue['lang'];
			$commit_infos[$langue['lang']] = array();
		} 
		else {
			/**
			 * Le fichier n'est pas suffisamment traduit et n'existe pas, on ne fera donc rien
			 */
			if (!file_exists($dir_module . '/' . $module . '_' . $langue['lang'] . '.php')){
				$liste_lang_non_exportees[] = $langue['lang'];
			} else {
				/**
				 * Il n'est pas suffisamment traduit, cependant, il existe déjà
				 * On ne va donc pas le supprimer à la barbare, mais on le met à jour quand même
				 */
				$liste_lang[] = $langue['lang'];
				$commit_infos[$langue['lang']] = array();
				$liste_lang_a_supprimer[] = $langue['lang'];
				$percent = (($langue['count']/$count_trad_reference)*100);
				if ($percent<($seuil_export-15)){
					$commit_infos[$langue['lang']]['message'] = "La langue '" . $langue['lang'] . "' devrait être supprimée car trop peu traduite (" . number_format($percent, 2) . " %)\n";
				}
			}
		}
	}

	// traiter chaque langue
	foreach ($liste_lang as $lang){
		salvatore_log("Generation de la langue $lang");
		$indent = "\t";

		$php_lines = $chaines = $id_tradlangs = array();
		$initiale = '';

		// On ne prend que les MODIF, les RELIRE et les OK pour ne pas rendre les sites multilingues en français
		$chaines = sql_allfetsel('id_tradlang,id,str,comm,statut,md5', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang) . " AND statut!='NEW' AND statut!='attic'", 'id');
		$id_tradlangs = array_column($chaines, 'id_tradlang');
		$chaines = array_combine(array_column($chaines, 'id'), $chaines);
		ksort($chaines);

		$total_chaines = ['OK' => 0, 'MODIF' => 0, 'RELIRE' => 0];
		foreach ($chaines as $chaine){
			$total_chaines[$chaine['statut']]++;

			$comment = salvatore_clean_comment($chaine['comm']);

			if ($initiale !== strtoupper($chaine['id'][0])){
				$initiale = strtoupper($chaine['id'][0]);
				$php_lines[] = "\n$indent// $initiale";
			}

			if (strlen($chaine['statut']) and ($chaine['statut']!=='OK')){
				$comment .= ' ' . $chaine['statut'];
			}
			if ($comment){
				$comment = ' # ' . trim($comment); // on rajoute les commentaires ?
			}

			// nettoyger la chaine de langue et calcul du md5
			$str = salvatore_nettoyer_chaine_langue($chaine['str'], $lang);
			$newmd5 = md5($str);

			/**
			 * Si le md5 ou la chaine à changé, on la met à jour dans la base
			 */
			if (($chaine['md5']!==$newmd5) || ($str!=$chaine['str'])){
				$r = sql_updateq('spip_tradlangs', array('md5' => $newmd5, 'str' => $str), 'id_tradlang=' . intval($chaine['id_tradlang']));
			}

			$php_lines[] = $indent . var_export($chaine['id'], 1) . ' => ' . var_export($str, 1) . ',' . $comment;
		}

		salvatore_log(" - traduction (".$total_chaines['OK']."/$count_trad_reference OK | ".$total_chaines['RELIRE']."/$count_trad_reference RELIRE | ".$total_chaines['MODIF']."/$count_trad_reference MODIFS), export");
		$file_name = salvatore_exporter_fichier_php($dir_module, $module, $lang, $php_lines, $url_trad_module, ($lang==$lang_ref) ? $url_repo : false);

		// noter la langue et les traducteurs pour lang/module.xml
		$people_unique = array();
		$xml_infos[$lang] = array(
			'traducteurs' => array(),
			'traduits' => $total_chaines['OK'],
			'modifs' => $total_chaines['MODIF'],
			'relire' => $total_chaines['RELIRE'],
		);
		if (defined('_ID_AUTEUR_SALVATORE') and intval(_ID_AUTEUR_SALVATORE)>0){
			$people_unique[] = _ID_AUTEUR_SALVATORE;
		}

		// ici on prend tous les statut de chaine (?)
		$traducteurs = sql_allfetsel('DISTINCT(traducteur)', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang));
		foreach ($traducteurs as $t){
			$traducteurs_lang = explode(',', $t['traducteur']);
			foreach ($traducteurs_lang as $traducteur){
				if (!in_array($traducteur, $people_unique)){
					$traducteur_supp = array();
					if (is_numeric($traducteur) and $id_auteur = intval($traducteur)){
						$traducteur_supp['nom'] = extraire_multi(sql_getfetsel('nom', 'spip_auteurs', 'id_auteur = ' . $id_auteur));
						$traducteur_supp['lien'] = url_absolue(generer_url_entite($id_auteur, 'auteur'), $url_site);
					} elseif (trim(strlen($traducteur))>0) {
						$traducteur_supp['nom'] = trim($traducteur);
						$traducteur_supp['lien'] = '';
					}
					if (isset($traducteur_supp['nom'])){
						$xml_infos[$lang]['traducteurs'][strtolower($traducteur_supp['nom'])] = $traducteur_supp;
					}
					$people_unique[] = $traducteur;
				}
			}
		}
		unset($people_unique);

		$commit_infos[$lang]['file_name'] = basename($file_name);
		$commit_infos[$lang]['lastmodified'] = salvatore_read_lastmodified_file(basename($file_name), $source, $dir_depots);
		$commit_infos[$lang]['must_add'] = false;

		if ($row_module['limite_trad']==0){
			$commit_infos[$lang]['must_add'] = true;
		} elseif (!in_array($module, array('ecrire', 'spip', 'public'))) {
			if ((intval(($xml_infos[$lang]['traduits']/$count_trad_reference)*100)>$seuil_export)){
				$commit_infos[$lang]['must_add'] = true;
			}
		}

		// trouver le commiteur si c'est un fichier deja versionne ou a ajouter
		if ($commit_infos[$lang]['lastmodified'] or $commit_infos[$lang]['must_add']) {
			$where = [
				"objet='tradlang'",
				sql_in('id_objet', $id_tradlangs),
				"id_auteur != '-1'",
				'id_auteur !=' . intval(_ID_AUTEUR_SALVATORE),
			];
			if ($commit_infos[$lang]['lastmodified']) {
				$where[] = "date>".sql_quote(date('Y-m-d H:i:s', $commit_infos[$lang]['lastmodified']));
			}
			$auteur_versions = sql_allfetsel('DISTINCT id_auteur', 'spip_versions',  $where);
			if (count($auteur_versions)==1){
				$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = ' . intval($auteur_versions[0]['id_auteur']));
				if ($email){
					$commit_infos[$lang]['author'] = $email;
					salvatore_log("Le commiteur pour la langue $lang : $email");
				}
			}
		}
	}

	// le fichier XML recapitulatif
	$indent = "\t";
	$xml = "<traduction
{$indent}module=\"$module\" 
{$indent}dir_module=\"".$row_module['dir_module']."\"
{$indent}gestionnaire=\"salvatore\"
{$indent}url=\"$url_site\"
{$indent}source=\"$url_repo\"
{$indent}reference=\"$lang_ref\">\n";
	foreach ($xml_infos as $lang => $info){
		if (count($info['traducteurs']>0)){
			$xml .= "$indent<langue code=\"$lang\" url=\"" . parametre_url($url_trad_module, 'lang_cible', $lang) . "\" total=\"$count_trad_reference\" traduits=\"" . $info['traduits'] . '" relire="' . $info['relire'] . '" modifs="' . $info['modifs'] . '" nouveaux="' . ($count_trad_reference-($info['modifs']+$info['traduits']+$info['relire'])) . '" pourcent="' . number_format((($info['traduits']/$count_trad_reference)*100), 2) . "\">\n";
			ksort($info['traducteurs']);
			foreach ($info['traducteurs'] as $nom => $people){
				$xml .= $indent . $indent . '<traducteur nom="' . entites_html($people['nom']) . '" lien="' . entites_html($people['lien']) . "\" />\n";
			}
			$xml .= "$indent</langue>\n";
		} else {
			$xml .= "$indent<langue code=\"$lang\" url=\"" . parametre_url($url_trad_module, 'lang_cible', $lang) . "\" />\n";
		}
	}
	$xml .= "</traduction>\n";
	file_put_contents($dir_module . '/' . $module . '.xml', $xml);


	if (isset($liste_lang_non_exportees) and (count($liste_lang_non_exportees)>0)){
		salvatore_log("Les langues suivantes ne sont pas exportées car trop peu traduites : " . implode(', ', $liste_lang_non_exportees));
	}
	if (isset($liste_lang_a_supprimer) and (count($liste_lang_a_supprimer)>0)){
		salvatore_log("<error>Les langues suivantes devraient être supprimées car trop peu traduites : " . implode(', ', $liste_lang_a_supprimer)."</error>");
	}

	$nb_to_commit = 0;
	// et on ecrit un json pour que le pousseur sache quoi commit
	if (count($commit_infos)) {
		$nb_to_commit = count($commit_infos);
		if ($message_commit) {
			$commit_infos['message'] = $message_commit;
		}
		file_put_contents($dir_module . '/' . $module . '.commit.json', json_encode($commit_infos));
	}

	$log = salvatore_read_status_modif($module, $source, $dir_depots);
	salvatore_log($log);
	return $nb_to_commit;
}

/**
 * Nettoyer le commentaire avant ecriture dans le PHP
 * @param $comment
 * @return mixed|string
 */
function salvatore_clean_comment($comment) {
	if (strlen(trim($comment))>1){
		// On remplace les sauts de lignes des commentaires sinon ça crée des erreurs php
		$comment = str_replace(array("\r\n", "\n", "\r"), ' ', $comment);
		// Conversion des commentaires en utf-8
		$comment = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $comment), ENT_NOQUOTES, 'utf-8'));
		return $comment;
	}
	return '';
}


/**
 * Generer un fichier de langue a partir de ses lignes php
 * @param string $dir_module
 * @param string $module
 * @param string $lang
 * @param array $php_lines
 * @param string $url_trad_module
 * @param $origin
 * @return string
 */
function salvatore_exporter_fichier_php($dir_module, $module, $lang, $php_lines, $url_trad_module, $origin) {
	$file_name = $dir_module . '/' . $module . '_' . $lang . '.php';
	$file_content = '<' . '?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
';
	if ($origin) {
		$file_content .= '// Fichier source, a modifier dans ' . $origin;
	}
	else {
		$url_trad_module = parametre_url($url_trad_module, 'lang_cible', $lang, '&');
		$file_content .= '// extrait automatiquement de ' . $url_trad_module . '
// ** ne pas modifier le fichier **
';
	}

	// historiquement les fichiers de lang de spip_loader ne peuvent pas etre securises
	if ($module !== 'tradloader') {
		$file_content .= "\nif (!defined('_ECRIRE_INC_VERSION')) {
	return;
}\n\n";
	}

	# supprimer la virgule du dernier item
	$php_lines[count($php_lines)-1] = preg_replace('/,([^,]*)$/', '\1', $php_lines[count($php_lines)-1]);

	$file_content .=
		'$GLOBALS[$GLOBALS[\'idx_lang\']] = array(' . "\n"
		. implode("\n", $php_lines)
	  . "\n);\n";
	file_put_contents($file_name, $file_content);
	return $file_name;
}


/**
 * Lire la date de derniere modif d'un fichier de langue
 * @param string $file_name
 * @param array $source
 * @param string $dir_depots
 * @return false|int
 */
function salvatore_read_lastmodified_file($file_name, $source, $dir_depots) {

	$file_path_relative = $file_name;
	if ($source['dir']) {
		$file_path_relative = $source['dir'] . DIRECTORY_SEPARATOR . $file_path_relative;
	}
	$file_path = $dir_depots . $source['dir_checkout'] . DIRECTORY_SEPARATOR . $file_path_relative;

	$lastmodified = 0;
	switch ($source['methode']) {
		case 'git':
			$d = getcwd();
			chdir($dir_depots . $source['dir_checkout']);
			$lastmodified = exec("git log -1 -c --pretty=tformat:'%ct' $file_path_relative | head -1");
			$lastmodified = intval(trim($lastmodified));
			chdir($d);
			break;
		case 'svn':
			$lastmodified = exec('env LC_MESSAGES=en_US.UTF-8 svn info ' . $file_path . "| awk '/^Last Changed Date/ { print $4 \" \" $5 }'");
			$lastmodified = strtotime($lastmodified);
			break;
	}

	return $lastmodified;
}


/**
 * Afficher le status des fichiers modifies pour un module
 * @param string $module
 * @param array $source
 * @param $dir_depots
 * @return string
 */
function salvatore_read_status_modif($module, $source, $dir_depots) {
	$pre = "";
	if ($source['dir']) {
		$pre = $source['dir'] . DIRECTORY_SEPARATOR;
	}
	$files_list = [$pre . $module . '_*', $pre . $module . '.xml'];
	$files_list = implode(' ', $files_list);

	$d = getcwd();
	chdir($dir_depots . $source['dir_checkout']);
	$output = array();
	switch ($source['methode']) {
		case 'git':
			exec("git status --short $files_list 2>&1", $output);
			break;
		case 'svn':
			exec("svn status $files_list 2>&1", $output);
			break;
	}
	chdir($d);
	return implode("\n", $output);
}

/*
if ($row_module['limite_trad']==0){
	foreach ($liste_lang as $lang){
		passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
	}
} elseif (!in_array($module, array('ecrire', 'spip', 'public'))) {
	salvatore_log('Limite trad = ' . $seuil_export);
	foreach ($liste_lang as $lang){
		if ((intval(($xml_infos[$lang]['traduits']/$count_trad_reference)*100)>$seuil_export)
			and (substr(exec('svn status ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php"), 0, 1)=='?')){
			passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
		}
	}
}
*/

/*

		if (substr(exec('svn status ' . _DIR_SALVATORE_TMP . $module . '/' . $module . '_' . $lang . '.php'), 0, 1)=='?'){
			if ($row_module['limite_trad']==0){
				passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php 2> /dev/null") ? salvatore_log("$log\n") : '';
			} elseif (!in_array($module, array('ecrire', 'spip', 'public'))) {
				if ((intval(($xml_infos[$lang]['traduits']/$count_trad_reference)*100)>$seuil_export)){
					passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
				}
			}
		}


 */