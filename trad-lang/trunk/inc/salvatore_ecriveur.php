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
 * @param string $dir_modules
 * @throws Exception
 */
function salvatore_ecrire($liste_sources, $dir_modules = null, $message_commit=''){
	include_spip('inc/salvatore');
	salvatore_init();

	// on va lire dans la base, il faut qu'elle soit a jour
	salvatore_verifier_base_upgradee();

	if (is_null($dir_modules)){
		$dir_modules = _DIR_SALVATORE_MODULES;
	}
	salvatore_check_dir($dir_modules);
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
			salvatore_exporter_module($id_tradlang_module, $source, $url_gestionnaire, $url_trad_module, $message_commit);
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
 * @param string $message_commit
 */
function salvatore_exporter_module($id_tradlang_module, $source, $url_site, $url_trad_module, $dir_modules, $message_commit = ''){

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

	// charger la langue originale, pour la copier si necessaire
	// TODO : simplifier ? aucune reference a $trad_reference
	$trad_reference = [];
	$count_trad_reference = 0;
	$rows = sql_allfetsel('id, id_tradlang_module,str,comm,statut', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($row_module['lang_mere']) . " AND statut='OK'", 'id');
	foreach ($rows as $row){
		$row['statut'] = 'NEW';
		$trad_reference[$row['id']] = $row;
		$count_trad_reference++;
	}

	$liste_lang = $liste_lang_non_exportees = $liste_lang_a_supprimer = array();
	$minimal = ceil((($count_trad_reference*$seuil_export)/100));
	salvatore_log("Minimal = $minimal ($seuil_export %)");

	$langues = sql_allfetsel('lang,COUNT(*) as count', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . " AND statut != 'NEW' AND statut != 'attic'", 'lang', 'lang');
	foreach ($langues as $langue){
		/**
		 * Le fichier est il suffisamment traduit
		 */
		if ($langue['count']>=$minimal){
			$liste_lang[] = $langue['lang'];
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
				$liste_lang_a_supprimer[] = $langue['lang'];
				$percent = (($langue['count']/$count_trad_reference)*100);
				if ($percent<($seuil_export-15)){
					$message_commit .= "La langue '" . $langue['lang'] . "' devrait être supprimée car trop peu traduite (" . number_format($percent, 2) . " %)\n";
				}
			}
		}
	}

	// traiter chaque langue
	$infos = $commiteurs = array();
	foreach ($liste_lang as $lang){
		salvatore_log("Generation de la langue $lang ");
		// Proteger les caracteres typographiques a l'interieur des tags html
		$typo = (in_array($lang, array('eo', 'fr', 'cpf')) || strncmp($lang, 'fr_', 3)==0) ? 'fr' : 'en';
		$typographie = charger_fonction($typo, 'typographie');
		$tab = "\t";

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
				$php_lines[] = "\n$tab// $initiale";
			}

			if (strlen($chaine['statut']) and ($chaine['statut']!=='OK')){
				$comment .= ' ' . $chaine['statut'];
			}
			if ($comment){
				$comment = ' # ' . trim($comment); // on rajoute les commentaires ?
			}

			$str = savlatore_nettoyer_chaine_base($chaine['str'], $lang);

			/**
			 * Calcul du nouveau md5
			 */
			$newmd5 = md5($str);

			/**
			 * Si le md5 ou la chaine à changé, on la met à jour dans la base
			 */
			if (($chaine['md5']!==$newmd5) || ($str!=$chaine['str'])){
				$r = sql_updateq('spip_tradlangs', array('md5' => $newmd5, 'str' => $str), 'id_tradlang = ' . intval($chaine['id_tradlang']));
			}

			$php_lines[] = $tab . var_export($chaine['id'], 1) . ' => ' . var_export($str, 1) . ',' . $comment;
		}


		$orig = ($lang==$lang_ref) ? $url_repo : false;

		salvatore_log(" - traduction ($total_chaines['OK']/$count_trad_reference OK | $total_chaines['RELIRE']/$count_trad_reference RELIRE | $total_chaines['MODIF']/$count_trad_reference MODIFS), export\n");
		// historiquement les fichiers de lang de spip_loader ne peuvent pas etre securises
		$secure = ($module=='tradloader')
			? ''
			: "if (!defined('_ECRIRE_INC_VERSION')) {
return;
}\n\n";

		$fd = fopen($dir_module . '/' . $module . '_' . $lang . '.php', 'w');

		# supprimer la virgule du dernier item
		$php_lines[count($php_lines)-1] = preg_replace('/,([^,]*)$/', '\1', $php_lines[count($php_lines)-1]);

		$contenu = join("\n", $php_lines);

		// L'URL du site de traduction
		$url_trad_module = parametre_url($url_trad_module, 'lang_cible', $lang);
		/**
		 * Ecrire le fichier de langue complet
		 */
		fwrite(
			$fd,
			'<' . '?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
'

			. ($orig
				? '// Fichier source, a modifier dans ' . $orig
				: '// extrait automatiquement de ' . $url_trad_module . '
// ** ne pas modifier le fichier **
'
			)
			. "\n" . $secure . '$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
'
			. $contenu
			. '
);
'
		);
		fclose($fd);

		// noter la langue et les traducteurs pour lang/module.xml
		$infos[$lang] = $people_unique = array();
		$infos[$lang]['traducteurs'] = array();
		$infos[$lang]['traduits'] = $total_chaines['OK'];
		$infos[$lang]['modifs'] = $total_chaines['MODIF'];
		$infos[$lang]['relire'] = $total_chaines['RELIRE'];
		if (defined('_ID_AUTEUR_SALVATORE') and intval(_ID_AUTEUR_SALVATORE)>0){
			$people_unique[] = _ID_AUTEUR_SALVATORE;
		}
		$s = sql_allfetsel('DISTINCT(traducteur)', 'spip_tradlangs', 'id_tradlang_module = ' . intval($row_module['id_tradlang_module']) . ' AND lang = ' . sql_quote($lang));
		foreach ($s as $t){
			$traducteurs_lang = explode(',', $t['traducteur']);
			foreach ($traducteurs_lang as $traducteur){
				if (!in_array($traducteur, $people_unique)){
					if (is_numeric($traducteur) and $id_auteur = intval($traducteur)){
						$traducteur_supp['nom'] = extraire_multi(sql_getfetsel('nom', 'spip_auteurs', 'id_auteur = ' . $id_auteur));
						$traducteur_supp['lien'] = url_absolue(generer_url_entite($id_auteur, 'auteur'), $url_site);
					} elseif (trim(strlen($traducteur))>0) {
						$traducteur_supp['nom'] = trim($traducteur);
						$traducteur_supp['lien'] = '';
					}
					if (isset($traducteur_supp['nom'])){
						$infos[$lang]['traducteurs'][strtolower($traducteur_supp['nom'])] = $traducteur_supp;
					}
					unset($traducteur_supp);
					$people_unique[] = $traducteur;
				}
			}
		}
		unset($people_unique);

		if (substr(exec('svn status ' . _DIR_SALVATORE_TMP . $module . '/' . $module . '_' . $lang . '.php'), 0, 1)=='?'){
			if ($row_module['limite_trad']==0){
				passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php 2> /dev/null") ? salvatore_log("$log\n") : '';
			} elseif (!in_array($module, array('ecrire', 'spip', 'public'))) {
				if ((intval(($infos[$lang]['traduits']/$count_trad_reference)*100)>$seuil_export)){
					passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
				}
			}
		}
		/**
		 * Le fichier a été modifié ou ajouté (svn status A ou M)
		 *
		 * On récupère la date de dernier changement avec svn info
		 * On cherche toutes les dernières modifications dans la base de donnée
		 * Si un seul auteur de révisions (Hors salvatore et -1) on l'ajoute comme commiteur
		 * Si plusieurs auteurs le commiteur sera Salvatore
		 */
		if (in_array(substr(exec('svn status ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php"), 0, 1), array('A', 'M'))){
			$last_change = exec('env LC_MESSAGES=en_US.UTF-8 svn info ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php | awk '/^Last Changed Date/ { print $4 \" \" $5 }'");
			$auteur_versions = sql_allfetsel('id_auteur', 'spip_versions', 'objet="tradlang" AND date > ' . sql_quote($last_change) . ' AND ' . sql_in('id_objet', $id_tradlangs) . ' AND id_auteur != "-1" AND id_auteur !=' . intval(_ID_AUTEUR_SALVATORE), 'id_auteur');
			if (count($auteur_versions)==1){
				$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = ' . intval($auteur_versions[0]['id_auteur']));
				if ($email){
					$commiteurs[$lang] = $email;
				}
				salvatore_log("\nLe commiteur sera pour la langue $lang : " . $commiteurs[$lang] . " \n");
			}
		}
	}

	// ecrire lang/module.xml
	$xml = "<traduction module=\"$module\" gestionnaire=\"salvatore\" url=\"$url_site\" source=\"$url_repo\" reference=\"$lang_ref\">\n";
	foreach ($infos as $lang => $info){
		if (count($info['traducteurs']>0)){
			$xml .= "	<langue code=\"$lang\" url=\"" . parametre_url($url_trad_module, 'lang_cible', $lang) . "\" total=\"$count_trad_reference\" traduits=\"" . $info['traduits'] . '" relire="' . $info['relire'] . '" modifs="' . $info['modifs'] . '" nouveaux="' . ($count_trad_reference-($info['modifs']+$info['traduits']+$info['relire'])) . '" pourcent="' . number_format((($info['traduits']/$count_trad_reference)*100), 2) . "\">\n";
			ksort($info['traducteurs']);
			foreach ($info['traducteurs'] as $nom => $people){
				$xml .= '		<traducteur nom="' . entites_html($people['nom']) . '" lien="' . entites_html($people['lien']) . "\" />\n";
			}
			$xml .= "	</langue>\n";
		} else {
			$xml .= "	<langue code=\"$lang\" url=\"" . parametre_url($url_trad_module, 'lang_cible', $lang) . "\" />\n";
		}
	}
	unset($traducteurs[$lang_ref]);
	$xml .= "</traduction>\n";

	ecrire_fichier($dir_module . '/' . $module . '.xml', $xml);

	if (isset($liste_lang_non_exportees) and (count($liste_lang_non_exportees)>0)){
		$liste_lang_non_exportees_string = implode(', ', $liste_lang_non_exportees);
		salvatore_log("\nLes langues suivantes ne sont pas exportées car trop peu traduites:\n");
		salvatore_log("$liste_lang_non_exportees_string\n");
	}
	if (isset($liste_lang_a_supprimer) and (count($liste_lang_a_supprimer)>0)){
		$liste_lang_a_supprimer_string = implode(', ', $liste_lang_a_supprimer);
		salvatore_log("\nLes langues suivantes devraient être supprimées car trop peu traduites:\n");
		salvatore_log("$liste_lang_a_supprimer_string\n");
	}
	if ($row_module['limite_trad']==0){
		foreach ($liste_lang as $lang){
			passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
		}
	} elseif (!in_array($module, array('ecrire', 'spip', 'public'))) {
		salvatore_log('Limite trad = ' . $seuil_export);
		foreach ($liste_lang as $lang){
			if ((intval(($infos[$lang]['traduits']/$count_trad_reference)*100)>$seuil_export)
				and (substr(exec('svn status ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php"), 0, 1)=='?')){
				passthru('svn add ' . _DIR_SALVATORE_TMP . $module . '/' . $module . "_$lang.php* 2> /dev/null") ? salvatore_log("$log\n") : '';
			}
		}
	}
	salvatore_log("\n" . passthru('svn status ' . _DIR_SALVATORE_TMP . $module . '/') . "\n");
	if (strlen($message_commit)>1 || count($commiteurs)>0){
		$fd = fopen($dir_module . '/message_commit.inc', 'w');
		# ecrire le fichier
		fwrite(
			$fd,
			'<' . '?php
$message_commit = "' . $message_commit . '";

$commiteurs = ' . var_export($commiteurs, 1) . ';

?' . '>
'
		);
		fclose($fd);
	}
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
 * Nettoyer la chaine traduite qui est en base avant export dans le PHP
 * @param string $chaine
 * @param string $lang
 * @return string
 */
function savlatore_nettoyer_chaine_base($chaine, $lang) {
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