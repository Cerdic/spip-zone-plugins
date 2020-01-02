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

require_once(dirname(__FILE__).'/inc_tradlang.php');
$tmp= _SALVATORE_TMP;

trad_log("\n=======================================\nECRIVEUR\nExporte les fichiers de traduction dans sa copie locale a partir de la base de donnees\n=======================================\n");

$liste_sources=charger_fichier_traductions(); // chargement du fichier traductions.txt

if (!is_dir($tmp)) {
	die('Manque le repertoire '.$tmp);
}

include_spip('base/abstract_sql');
include_spip('inc/filtres');
include_spip('inc/texte');
include_spip('inc/config');
include_spip('inc/xml');

/**
 * On récupère l'URL du site de traduction
 * Elle servira à :
 * -* empêcher l'export de fichiers traduits sur une autre plateforme
 * -* générer l'url de l'interface de traduction d'un module
 */
$url_site = $GLOBALS['meta']['adresse_site'];

if (isset($argv[1]) and strlen($argv[1]) > 1) {
	$message_commit = $argv[1]."\n\n";
}

foreach ($liste_sources as $source) {
	trad_log('==== Module ' . $source[1] . " =======================================\n");
	$export = true;
	/**
	 * On test ici si le fichier est géré par un autre salvatore
	 * Si oui on empeche son export en le signifiant
	 */
	if (file_exists($xml = $tmp.$source[1].'/'.$source[1].'.xml')) {
		$xml_content = spip_xml_load($xml);
		if (is_array($xml_content)) {
			spip_xml_match_nodes('/^traduction/', $xml_content, $matches);
			$test = '<'.key($matches).'>';
			$url = extraire_attribut($test, 'url');
			if ($url && (str_replace(array('http://', 'https://'), '', $url) != str_replace(array('http://', 'https://'), '', $url_site))) {
				$export = false;
				$sujet = 'Ecriveur : Erreur sur '.$source[1];
				$corps = "\nErreur : export impossible, le fichier est traduit autre part : $url != $url_site\n\n";
				trad_sendmail($sujet, $corps);
				trad_log("\nErreur : export impossible, le fichier est traduit autre part : $url != $url_site\n\n");
			}
		}
	}
	/**
	 * Si on l'exporte
	 */
	if ($export) {
		$id_tradlang_module = sql_getfetsel('id_tradlang_module', 'spip_tradlang_modules', 'module = ' . sql_quote($source[1]));
		$url_trad = url_absolue(generer_url_entite($id_tradlang_module, 'tradlang_module'), $url_site);
		export_trad_module($source, $url_site, $url_trad, $message_commit);
	}
}

return 0;

//
// Genere les fichiers de traduction d'un module
//
function export_trad_module($source, $url_site, $url_trad, $message_commit = '') {
	global $tmp;

	// sanity check
	if (!is_dir($tmp.$source[1].'/')) {
		return false;
	}

	$module = sql_fetsel('id_tradlang_module,limite_trad,lang_mere', 'spip_tradlang_modules', 'module = ' . sql_quote($source[1]));

	$seuil_export = 50;
	if (is_numeric($module['limite_trad']) and $module['limite_trad'] > 0) {
		$seuil_export = $module['limite_trad'];
	} elseif (function_exists('lire_config')) {
		$seuil_export = lire_config('tradlang/seuil_export_tradlang', 50);
	}
	if (intval($module['id_tradlang_module']) >= 1) {
		// charger la langue originale, pour la copier si necessaire
		$count_original = 0;
		$res=sql_allfetsel('id, id_tradlang_module,str,comm,statut', 'spip_tradlangs', 'id_tradlang_module = '.intval($module['id_tradlang_module']) . ' AND lang = '.sql_quote($module['lang_mere']) . ' AND statut="OK"', 'id');
		foreach ($res as $row) {
			$row['statut'] = 'NEW';
			$lorigine[$row['id']] = $row;
			$id_tradlang_module = $row['id_tradlang_module'];
			$count_original++;
		}

		$liste_lang = $liste_lang_non_exportees = $liste_lang_supprimer = array();
		$minimal = ceil((($count_original*$seuil_export)/100));
		trad_log("\nMinimal = $minimal ($seuil_export %)\n");

		$res=sql_allfetsel('lang,COUNT(*) as N', 'spip_tradlangs', 'module = ' . sql_quote($source[1]) . ' AND statut != "NEW" AND statut != "attic"', 'lang', 'lang');
		foreach ($res as $row) {
			/**
			 * Le fichier est il suffisamment traduit
			 */
			if ($row['N'] >= $minimal) {
				$liste_lang[]=$row['lang'];
			} else {
				/**
				 * Le fichier n'est pas suffisamment traduit et n'existe pas, on ne fera donc rien
				 */
				if (!file_exists($tmp.$source[1].'/'.$source[1].'_'.$row['lang'].'.php')) {
					$liste_lang_non_exportees[] = $row['lang'];
				} else {
					/**
					 * Il n'est pas suffisamment traduit, cependant, il existe déjà
					 * On ne va donc pas le supprimer à la barbare, mais on le met à jour quand même
					 */
					$liste_lang[]=$row['lang'];
					$liste_lang_supprimer[]=$row['lang'];
					$percent = (($row['N']/$count_original)*100);
					if ($percent < ($seuil_export-15)) {
						$message_commit .= "La langue '".$row['lang']."' devrait être supprimée car trop peu traduite (".number_format($percent, 2)." %)\n";
					}
				}
			}
		}

		// traiter chaque langue
		$infos = $commiteurs = array();
		foreach ($liste_lang as $lang) {
			trad_log("Generation de la langue $lang ");
			// Proteger les caracteres typographiques a l'interieur des tags html
			$typo = (in_array($lang, array('eo','fr','cpf')) || strncmp($lang, 'fr_', 3) == 0) ? 'fr' : 'en';
			$typographie = charger_fonction($typo, 'typographie');
			$tab = "\t";

			$x = $tous = $tradlangs = array();
			$prev = '';
			$traduits = $modifs = $relire = 0;

			// On ne prend que les MODIF, les RELIRE et les OK pour ne pas rendre les sites multilingues en français
			$res=sql_allfetsel('id_tradlang,id,str,comm,statut,md5', 'spip_tradlangs', 'module = "' . $source[1] . '" AND lang = "' . $lang . '" AND statut != "NEW" AND statut != "attic"', 'id');
			foreach ($res as $row) {
				$tradlangs[] = $row['id_tradlang'];
				$tous[$row['id']] = $row;
			}
			ksort($tous);

			foreach ($tous as $row) {
				if ($row['statut'] == 'OK') {
					$traduits ++;
				} elseif ($row['statut'] == 'MODIF') {
					$modifs ++;
				} elseif ($row['statut'] == 'RELIRE') {
					$relire ++;
				}

				if (strlen($row['comm']) > 1) {
					// On remplace les sauts de lignes des commentaires sinon ça crée des erreurs php
					$row['comm'] = str_replace(array("\r\n", "\n", "\r"), ' ', $row['comm']);
					// Conversion des commentaires en utf-8
					$row['comm'] = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $row['comm']), ENT_NOQUOTES, 'utf-8'));
				}

				if ($prev != strtoupper($row['id'][0])) {
					$x[] = "\n$tab// ".strtoupper($row['id'][0]);
				}
				$prev=strtoupper($row['id'][0]);

				if (strlen($row['statut']) and ($row['statut'] != 'OK')) {
					$row['comm'] .= ' '.$row['statut'];
				}
				if (trim($row['comm'])) {
					$row['comm'] = ' # ' . trim($row['comm']); // on rajoute les commentaires ?
				}

				$str = $row['str'];

				/**
				 * On enlève les sauts de lignes windows pour des sauts de ligne linux
				 */
				$str = str_replace("\r\n", "\n", $str);

				/**
				 * protection dans les balises genre <a href="..." ou <img src="..."
				 * cf inc/filtres
				 */
				if (preg_match_all(_TYPO_BALISE, $str, $regs, PREG_SET_ORDER)) {
					foreach ($regs as $reg) {
						$insert = $reg[0];
						// hack: on transforme les caracteres a proteger en les remplacant
						// par des caracteres "illegaux". (cf corriger_caracteres())
						$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
						$str = str_replace($reg[0], $insert, $str);
					}
				}

				/**
				 * Protéger le contenu des balises <html> <code> <cadre> <frame> <tt> <pre>
				 */
				define('_PROTEGE_BLOCS_HTML', ',<(html|code|cadre|pre|tt)(\s[^>]*)?>(.*)</\1>,UimsS');
				if ((strpos($str, '<') !== false) and preg_match_all(_PROTEGE_BLOCS_HTML, $str, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $reg) {
						$insert = $reg[0];
						// hack: on transforme les caracteres a proteger en les remplacant
						// par des caracteres "illegaux". (cf corriger_caracteres())
						$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
						$str = str_replace($reg[0], $insert, $str);
					}
				}

				/**
				 * On applique la typographie de la langue
				 */
				$str = $typographie($str);
				/**
				 * On remet les caractères normaux sur les caractères illégaux
				 */
				$str = strtr($str, _TYPO_PROTECTEUR, _TYPO_PROTEGER);

				$str = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $str), ENT_NOQUOTES, 'utf-8'));

				/**
				 * Calcul du nouveau md5
				 */
				$newmd5 = md5($str);

				/**
				 * Si le md5 ou la chaine à changé, on la met à jour dans la base
				 */
				if (($row['md5'] != $newmd5) || ($str != $row['str'])) {
					$r = sql_updateq('spip_tradlangs', array('md5' => $newmd5, 'str' => $str), 'id_tradlang = '.intval($row['id_tradlang']));
				}

				$x[] = $tab.var_export($row['id'], 1).' => ' .var_export($str, 1).','.$row['comm'];
			}
			$orig = ($lang == $source[2]) ? $source[0] : false;

			trad_log(" - traduction ($traduits/$count_original OK | $relire/$count_original RELIRE | $modifs/$count_original MODIFS), export\n");
			// historiquement les fichiers de lang de spip_loader ne peuvent pas etre securises
			$secure = ($source[1] == 'tradloader')
				? ''
				: "if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}\n\n";

			$fd = fopen($tmp.$source[1] . '/' . $source[1].'_'.$lang.'.php', 'w');

			# supprimer la virgule du dernier item
			$x[count($x)-1] = preg_replace('/,([^,]*)$/', '\1', $x[count($x)-1]);

			$contenu = join("\n", $x);

			// L'URL du site de traduction
			$url_trad = parametre_url($url_trad, 'lang_cible', $lang);
			/**
			 * Ecrire le fichier de langue complet
			 */
			fwrite(
				$fd,
				'<'.'?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
'

				. ($orig
				? '// Fichier source, a modifier dans '.$orig
				: '// extrait automatiquement de '.$url_trad.'
// ** ne pas modifier le fichier **
'
				)
				."\n".$secure.'$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
'
				. $contenu
				.'
);
'
			);
			fclose($fd);

			// noter la langue et les traducteurs pour lang/module.xml
			$infos[$lang] = $people_unique = array();
			$infos[$lang]['traducteurs'] = array();
			$infos[$lang]['traduits'] = $traduits;
			$infos[$lang]['modifs'] = $modifs;
			$infos[$lang]['relire'] = $relire;
			if (defined('_ID_AUTEUR_SALVATORE') and intval(_ID_AUTEUR_SALVATORE) > 0) {
				$people_unique[] = _ID_AUTEUR_SALVATORE;
			}
			$s = sql_allfetsel('DISTINCT(traducteur)', 'spip_tradlangs', 'id_tradlang_module = ' . intval($module['id_tradlang_module']) . ' AND lang = ' . sql_quote($lang));
			foreach ($s as $t) {
				$traducteurs_lang = explode(',', $t['traducteur']);
				foreach ($traducteurs_lang as $traducteur) {
					if (!in_array($traducteur, $people_unique)) {
						if (is_numeric($traducteur) and $id_auteur = intval($traducteur)) {
							$traducteur_supp['nom'] = extraire_multi(sql_getfetsel('nom', 'spip_auteurs', 'id_auteur = ' . $id_auteur));
							$traducteur_supp['lien'] = url_absolue(generer_url_entite($id_auteur, 'auteur'), $url_site);
						} elseif (trim(strlen($traducteur)) > 0) {
							$traducteur_supp['nom'] = trim($traducteur);
							$traducteur_supp['lien'] = '';
						}
						if (isset($traducteur_supp['nom'])) {
							$infos[$lang]['traducteurs'][strtolower($traducteur_supp['nom'])] = $traducteur_supp;
						}
						unset($traducteur_supp);
						$people_unique[] = $traducteur;
					}
				}
			}
			unset($people_unique);

			if (substr(exec('svn status '._SALVATORE_TMP.$source[1] . '/' . $source[1].'_' . $lang. '.php'), 0, 1) == '?') {
				if ($module['limite_trad'] == 0) {
					passthru('svn add '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php 2> /dev/null") ? trad_log("$log\n") : '';
				} elseif (!in_array($source[1], array('ecrire', 'spip', 'public'))) {
					if ((intval(($infos[$lang]['traduits']/$count_original)*100) > $seuil_export)) {
						passthru('svn add '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php* 2> /dev/null") ? trad_log("$log\n") : '';
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
			if (in_array(substr(exec('svn status '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php"), 0, 1), array('A', 'M'))) {
				$last_change = exec('env LC_MESSAGES=en_US.UTF-8 svn info '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php | awk '/^Last Changed Date/ { print $4 \" \" $5 }'");
				$auteur_versions = sql_allfetsel('id_auteur', 'spip_versions', 'objet="tradlang" AND date > ' . sql_quote($last_change).' AND '.sql_in('id_objet', $tradlangs).' AND id_auteur != "-1" AND id_auteur !='.intval(_ID_AUTEUR_SALVATORE), 'id_auteur');
				if (count($auteur_versions) == 1) {
					$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = ' . intval($auteur_versions[0]['id_auteur']));
					if ($email) {
						$commiteurs[$lang] = $email;
					}
					trad_log("\nLe commiteur sera pour la langue $lang : ".$commiteurs[$lang]." \n");
				}
			}
		}

		// ecrire lang/module.xml
		$xml = "<traduction module=\"$source[1]\" gestionnaire=\"salvatore\" url=\"$url_site\" source=\"$source[0]\" reference=\"$source[2]\">\n";
		foreach ($infos as $lang => $info) {
			if (count($info['traducteurs'] > 0)) {
				$xml .= "	<langue code=\"$lang\" url=\"".parametre_url($url_trad, 'lang_cible', $lang)."\" total=\"$count_original\" traduits=\"".$info['traduits'].'" relire="'.$info['relire'].'" modifs="' . $info['modifs'] . '" nouveaux="'.($count_original-($info['modifs']+$info['traduits']+$info['relire'])).'" pourcent="'.number_format((($info['traduits']/$count_original)*100), 2)."\">\n";
				ksort($info['traducteurs']);
				foreach ($info['traducteurs'] as $nom => $people) {
					$xml .= '		<traducteur nom="' . entites_html($people['nom']) . '" lien="' . entites_html($people['lien'])."\" />\n";
				}
				$xml .= "	</langue>\n";
			} else {
				$xml .= "	<langue code=\"$lang\" url=\"".parametre_url($url_trad, 'lang_cible', $lang)."\" />\n";
			}
		}
		unset($traducteurs[$source[2]]);
		$xml .= "</traduction>\n";

		ecrire_fichier($tmp.$source[1].'/'.$source[1].'.xml', $xml);

		if (isset($liste_lang_non_exportees) and (count($liste_lang_non_exportees) > 0)) {
			$liste_lang_non_exportees_string = implode(', ', $liste_lang_non_exportees);
			trad_log("\nLes langues suivantes ne sont pas exportées car trop peu traduites:\n");
			trad_log("$liste_lang_non_exportees_string\n");
		}
		if (isset($liste_lang_supprimer) and (count($liste_lang_supprimer) > 0)) {
			$liste_lang_supprimer_string = implode(', ', $liste_lang_supprimer);
			trad_log("\nLes langues suivantes devraient être supprimées car trop peu traduites:\n");
			trad_log("$liste_lang_supprimer_string\n");
		}
		if ($module['limite_trad'] == 0) {
			foreach ($liste_lang as $lang) {
				passthru('svn add '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php* 2> /dev/null") ? trad_log("$log\n") : '';
			}
		} elseif (!in_array($source[1], array('ecrire', 'spip', 'public'))) {
			trad_log('Limite trad = '.$seuil_export);
			foreach ($liste_lang as $lang) {
				if ((intval(($infos[$lang]['traduits']/$count_original)*100) > $seuil_export)
					and (substr(exec('svn status '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php"), 0, 1) == '?')) {
					passthru('svn add '._SALVATORE_TMP.$source[1].'/'.$source[1]."_$lang.php* 2> /dev/null") ? trad_log("$log\n") : '';
				}
			}
		}
		trad_log("\n".passthru('svn status '._SALVATORE_TMP.$source[1].'/')."\n");
		if (strlen($message_commit) > 1 || count($commiteurs) > 0) {
			$fd = fopen($tmp.$source[1].'/message_commit.inc', 'w');
			# ecrire le fichier
			fwrite(
				$fd,
				'<'.'?php
$message_commit = "'.$message_commit.'";

$commiteurs = '.var_export($commiteurs, 1).';

?'.'>
'
			);
			fclose($fd);
		}
	} else {
		trad_log("\n Ce module n'existe pas\n");
	}
}
