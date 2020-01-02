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

    Copyright 2003-2015
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
 		kent1 <kent1@arscenic.info>
*/


require_once(dirname(__FILE__) . '/inc_tradlang.php');
$tmp = _DIR_SALVATORE_TMP;
$invalider = $die_message = false;

/* modules de SPIP requis - il y a surement plus propre... */
include_spip('base/abstract_sql');
include_spip('inc/tradlang_verifier_langue_base');
include_spip('inc/tradlang_verifier_bilans');
include_spip('inc/charsets');
include_spip('inc/filtres');
include_spip('inc/texte');
include_spip('inc/xml');
include_spip('inc/lang_liste');
include_spip('inc/session');

if (defined('_ID_AUTEUR_SALVATORE') and is_numeric(_ID_AUTEUR_SALVATORE)){
	$GLOBALS['visiteur_session']['id_auteur'] = _ID_AUTEUR_SALVATORE;
}

$url_site = $GLOBALS['meta']['adresse_site'];

/* MAIN ***********************************************************************/

salvatore_log("\n=======================================\nLECTEUR\nPrend les fichiers de reference dans sa copie locale et met a jour la base de donnees\n=======================================\n");

$liste_sources = salvatore_charger_fichier_traductions(); // chargement du fichier traductions.txt

foreach ($liste_sources as $source){
	salvatore_log('==== Module ' . $source[1] . " =======================================\n");
	$liste_fic_lang = glob($tmp . $source[1] . '/' . $source[1] . '_*.php');
	$import = true;
	/**
	 * On test ici si le fichier est géré par un autre salvatore
	 * Si oui on empeche son import en le signifiant
	 */
	if (file_exists($xml = $tmp . $source[1] . '/' . $source[1] . '.xml')){
		$xml_content = spip_xml_load($xml);
		if (is_array($xml_content)){
			spip_xml_match_nodes('/^traduction/', $xml_content, $matches);
			$test = '<' . key($matches) . '>';
			$url = extraire_attribut($test, 'url');
			if ($url && (str_replace(array('http://', 'https://'), '', $url)!=str_replace(array('http://', 'https://'), '', $url_site))){
				$import = false;
				$sujet = 'Lecteur : Erreur sur ' . $source[1];
				$corps = "\nErreur : import impossible, le fichier est traduit autre part : $url\n\n";
				salvatore_envoyer_mail($sujet, $corps);
				salvatore_log("\nErreur : import impossible, le fichier est traduit autre part : $url\n\n");
			}
		}
	}
	if ($import){
		/**
		 * on doit absolument charger la langue principale en premier (a cause des MD5)
		 */
		$fic_lang_principal = $tmp . $source[1] . '/' . $source[1] . '_' . $source[2] . '.php';

		/**
		 * On regarde quelle est la date de dernière modification du fichier de langue principale
		 */
		$last_update = filemtime($fic_lang_principal);
		if ($last_update>strtotime('-1 day')){
			$priorite = '';
			$modifs = 0;
			if (defined('_TRAD_PRIORITE_DEFAUT')){
				$priorite = _TRAD_PRIORITE_DEFAUT;
			}
			if (in_array($fic_lang_principal, $liste_fic_lang)){
				$module = sql_fetsel('id_tradlang_module,lang_mere', 'spip_tradlang_modules', 'module = ' . sql_quote($source[1]));
				$id_module = $module['id_tradlang_module'];
				/**
				 * Si le module n'existe pas... on le crée
				 */
				if (!intval($id_module)){
					$id_module = sql_insertq('spip_tradlang_modules', array('module' => $source[1], 'nom_mod' => $source[1], 'lang_prefix' => $source[1], 'lang_mere' => $source[2], 'priorite' => $priorite));
				} elseif ($module['lang_mere']!=$source[2]) {
					/**
					 * Si la langue mere a changée, on la modifie
					 */
					sql_updateq('spip_tradlang_modules', array('lang_mere' => $source[2]), 'id_tradlang_module = ' . intval($id_module));
				}

				/**
				 * Si $id_module n'est pas un entier => on tue le script
				 */
				if (!intval($id_module)){
					$sujet = 'Lecteur : Erreur sur ' . $source[1];
					$corps = "Le module n'est pas un entier";
					salvatore_envoyer_mail($sujet, $corps);
					$die_message = "Le module n'est pas un entier";
					break;
				}
				$liste_id_orig = array();
				$modifs = import_module_spip($source, $fic_lang_principal, $liste_id_orig, 1, $id_module);
				$langues_a_jour = array();
				foreach ($liste_fic_lang as $f){
					if ($f!=$fic_lang_principal){
						import_module_spip($source, $f, $liste_id_orig, 0, $id_module);
						$fich = str_replace($source[1], '', basename($f, '.php'));
						list(, $lang) = explode('_', $fich, 2);
						if (($modifs>0) and function_exists('inc_tradlang_verifier_langue_base_dist')){
							inc_tradlang_verifier_langue_base_dist($source[1], $lang);
							salvatore_log('|-- Synchro de la langue ' . $lang . ' pour le module ' . $source[1] . "\n");
						} elseif (!function_exists('inc_tradlang_verifier_langue_base_dist')) {
							salvatore_log("|-- Fonction de synchro inexistante\n");
						}
						$langues_a_jour[] = $lang;
					}
				}
				/**
				 * On s'occupe des langues en base sans fichier
				 * s'il y a eu au moins une modif et que l'on peut faire la synchro
				 */
				if (($modifs>0) and function_exists('inc_tradlang_verifier_langue_base_dist')){
					$langues_pas_a_jour = sql_allfetsel('lang', 'spip_tradlangs', 'id_tradlang_module = ' . intval($id_module) . ' AND ' . sql_in('lang', $langues_a_jour, 'NOT'), 'lang');
					foreach ($langues_pas_a_jour as $langue_a_jour){
						inc_tradlang_verifier_langue_base_dist($source[1], $langue_a_jour['lang']);
						salvatore_log('|-- Synchro de la langue non exportée en fichier ' . $langue_a_jour['lang'] . ' pour le module ' . $source[1] . "\n");
					}
				}
				$invalider = true;
				salvatore_log("|\n");
				unset($langues_a_jour, $langues_pas_a_jour);
			} else {
				$sujet = 'Lecteur : Erreur sur ' . $source[1];
				$corps = '|-- Pas de fichier lang ' . $source[2] . ' pour le module ' . $source[1] . " : import impossible pour ce module\n";
				salvatore_envoyer_mail($sujet, $corps);
				$die_message = '|-- Pas de fichier lang ' . $source[2] . ' pour le module ' . $source[1] . " : import impossible pour ce module\n";
				break;
			}
		} else {
			salvatore_log("On ne modifie rien car l'original a été modifié il y a longtemps\n");
			/**
			 * Le fichier d'origine n'a pas été modifié
			 * Mais on a peut être de nouvelles langues
			 */
			$langues = $langues_a_ajouter = array();
			$langues_en_base = sql_allfetsel('lang', 'spip_tradlangs', 'module = ' . sql_quote($source[1]), 'lang');
			foreach ($langues_en_base as $langue){
				$langues[] = $langue['lang'];
			}
			foreach ($liste_fic_lang as $f){
				$fich = str_replace($source[1], '', basename($f, '.php'));
				list(, $lang) = explode('_', $fich, 2);

				if (!in_array($lang, $langues)){
					$langues_a_ajouter[] = array('lang' => $lang, 'fichier' => $f);
				}
			}
			if (count($langues_a_ajouter)>0){
				salvatore_log('On a ' . count($langues_a_ajouter) . " nouvelle(s) langue(s) à insérer \n");
				$module = sql_fetsel('*', 'spip_tradlang_modules', 'module = ' . sql_quote($source[1]));
				$id_module = $module['id_tradlang_module'];
				$liste_id_orig = array();
				$modifs = import_module_spip($source, $fic_lang_principal, $liste_id_orig, 1, $id_module);
				foreach ($langues_a_ajouter as $fichier){
					import_module_spip($source, $fichier['fichier'], $liste_id_orig, 0, $id_module);
					if (($modifs>0) && function_exists('inc_tradlang_verifier_langue_base_dist')){
						inc_tradlang_verifier_langue_base_dist($source[1], $lang);
					}
				}
			}
			salvatore_log("\n");
		}
		// Mise à jour des bilans
		if (function_exists('inc_tradlang_verifier_bilans_dist')){
			salvatore_log('Création ou MAJ des bilans de ' . $source[1] . "\n\n");
			inc_tradlang_verifier_bilans_dist($source[1], $source[2], false);
		}
	}
}

if ($invalider){
	include_spip('inc/invalideur');
	suivre_invalideur('1');
}

if ($die_message){
	die("$die_message");
}
return 0;

/**
 * Import d'un fichier de langue dans la base
 *
 * @param array $source
 * @param string $module
 * @param array $liste_id_orig
 * @param int $orig 1 signifie que c'est la langue originale
 * @param int $id_module
 * @return string
 */
function import_module_spip($source = array(), $module = '', &$liste_id_orig, $orig = null, $id_module){
	salvatore_log("!\n+ Import de $module\n");
	$memtrad = $GLOBALS['idx_lang'] = 'i18n_' . crc32($module) . '_tmp';
	$GLOBALS[$GLOBALS['idx_lang']] = null;
	$comm_fic_lang = charger_comm_fichier_langue($module);
	include $module;

	$str_lang = $GLOBALS[$memtrad];  // on a vu certains fichiers faire des betises et modifier idx_lang

	if (is_null($str_lang)){
		salvatore_log("Erreur, fichier $module mal forme\n");
		$sujet = 'Lecteur : Erreur sur ' . $module;
		$corps = "Erreur, fichier $module mal forme\n";
		salvatore_envoyer_mail($sujet, $corps);
		return false;
	}

	/**
	 * Nettoyer le contenu de ses <MODIF>,<NEW> et <PLUS_UTILISE>
	 * Ces chaines sont utilisées comme statut
	 */
	$status = array();

	foreach ($str_lang as $id => $v){
		if (1==$orig){
			$status[$id] = 'OK';
		} else {
			if (preg_match(',^<(MODIF|NEW|PLUS_UTILISE)>,US', $v, $r)){
				$str_lang[$id] = preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $v);
				$status[$id] = $r[1];
			} else {
				$status[$id] = 'OK';
			}
		}
	}

	$fich = str_replace($source[1], '', basename($module, '.php'));
	$mod = $source[1];
	list(, $lang) = explode('_', $fich, 2);

	if (!array_key_exists($lang, $GLOBALS['codes_langues'])){
		salvatore_log("!-- Attention : La langue $lang n'existe pas dans les langues possibles - $mod \n");
	} else {
		if (1==$orig){
			$res = spip_query("SELECT id, str, md5 FROM spip_tradlangs WHERE module='" . $source[1] . "' and lang='" . $lang . "' AND statut != 'attic' ");
		} else {
			$res = spip_query("SELECT id, str, md5 FROM spip_tradlangs WHERE module='" . $source[1] . "' and lang='" . $lang . "' and statut!='MODIF' ");
		}
		$nb = sql_count($res);
		if ($nb>0){
			salvatore_log("!-- Fichier de langue $lang du module $mod deja inclus dans la base\n");
		}

		$ajoutees = $inchangees = $supprimees = $modifiees = $ignorees = $recuperees = 0;

		/**
		 * Si la langue est deja dans la base, on ne l'ecrase que s'il s'agit
		 * de la langue source
		 */
		if ($nb==0 or $orig==1){
			$typo = (in_array($lang, array('eo', 'fr', 'cpf')) || strncmp($lang, 'fr_', 3)==0) ? 'fr' : 'en';
			$typographie = charger_fonction($typo, 'typographie');
			// La liste de ce qui existe deja
			$existant = $str_existant = array();
			while ($t = spip_fetch_array($res)){
				$existant[$t['id']] = $t['md5'];
				$str_existant[$t['id']] = $t['str'];
			}

			$bigwhere = 'module = ' . sql_quote($source[1]) . ' AND lang = ' . sql_quote($lang);

			include_spip('action/editer_tradlang');
			// Dans ce qui arrive, il y a 4 cas :
			foreach (array_unique(array_merge(array_keys($existant), array_keys($str_lang))) as $id){
				$comm = (isset($comm_fic_lang[$id])) ? $comm_fic_lang[$id] : '';
				// * chaine neuve
				if (isset($str_lang[$id]) and !isset($existant[$id])){
					if ($orig){
						$md5 = md5($str_lang[$id]);
					} else {
						if (!isset($liste_id_orig[$id])){
							salvatore_log("!-- Chaine $id inconnue dans la langue principale\n");
							$ignorees++;
						} else {
							$md5 = $liste_id_orig[$id];
						}
					}

					if (isset($md5)){
						/**
						 * On enlève les sauts de lignes windows pour des sauts de ligne linux
						 */

						$str_lang[$id] = str_replace("\r\n", "\n", $str_lang[$id]);

						/**
						 * protection dans les balises genre <a href="..." ou <img src="..."
						 * cf inc/filtres
						 */
						if (preg_match_all(_TYPO_BALISE, $str_lang[$id], $regs, PREG_SET_ORDER)){
							foreach ($regs as $reg){
								$insert = $reg[0];
								// hack: on transforme les caracteres a proteger en les remplacant
								// par des caracteres "illegaux". (cf corriger_caracteres())
								$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
								$str_lang[$id] = str_replace($reg[0], $insert, $str_lang[$id]);
							}
						}

						/**
						 * Protéger le contenu des balises <html> <code> <cadre> <frame> <tt> <pre>
						 */
						define('_PROTEGE_BLOCS_HTML', ',<(html|code|cadre|pre|tt)(\s[^>]*)?>(.*)</\1>,UimsS');
						if ((strpos($str_lang[$id], '<')!==false) and preg_match_all(_PROTEGE_BLOCS_HTML, $str_lang[$id], $matches, PREG_SET_ORDER)){
							foreach ($matches as $reg){
								$insert = $reg[0];
								// hack: on transforme les caracteres a proteger en les remplacant
								// par des caracteres "illegaux". (cf corriger_caracteres())
								$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
								$str_lang[$id] = str_replace($reg[0], $insert, $str_lang[$id]);
							}
						}

						/**
						 * On applique la typographie de la langue
						 */
						$str_lang[$id] = $typographie($str_lang[$id]);

						/**
						 * On remet les caractères normaux sur les caractères illégaux
						 */
						$str_lang[$id] = strtr($str_lang[$id], _TYPO_PROTECTEUR, _TYPO_PROTEGER);

						$str_lang[$id] = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $str_lang[$id]), ENT_NOQUOTES, 'utf-8'));

						/**
						 * Calcul du nouveau md5
						 */
						$md5 = md5($str_lang[$id]);

						/**
						 * Si le commentaire est un statut et que l'on ne traite pas le fichier de langue mère
						 * On vire le commentaire et met son contenu comme statut
						 */
						if (in_array($comm, array('NEW', 'OK', 'MODIF', 'MODI')) && $orig!=1){
							if ($comm=='MODI'){
								$comm = 'MODIF';
							}
							$status[$id] = $comm;
							$comm = '';
						} else {
							if ((strlen($comm)>1) && preg_match('/(.*?)(NEW|OK|MODIF)(.*?)/', $comm, $matches)){
								if ($orig!=1){
									$status[$id] = $matches[2];
								}
								$comm = preg_replace('/(NEW|OK|MODIF)/', '', $comm);
							}
						}

						/**
						 * On génère un titre
						 */
						$titre = $id . ' : ' . $source[1] . ' - ' . $lang;

						$data = array('id_tradlang_module' => $id_module, 'titre' => $titre, 'module' => $source[1], 'lang' => $lang, 'id' => $id, 'str' => $str_lang[$id], 'comm' => $comm, 'md5' => $md5, 'statut' => $status[$id]);
						$id_tradlang = sql_insertq('spip_tradlangs', $data);

						/**
						 * L'identifiant de la chaîne de langue a peut être déjà été utilisé puis mis au grenier
						 * On le récupère donc
						 */
						if (!$id_tradlang){
							$tradlang = sql_fetsel('*', 'spip_tradlangs', 'id = ' . sql_quote($id) . ' AND module = ' . sql_quote($source[1]) . 'AND lang = ' . sql_quote($lang) . ' AND statut = ' . sql_quote('attic'));
							if (is_array($tradlang)){
								$id_tradlang = intval($tradlang['id_tradlang']);
								salvatore_log("\n Recuperation d'une chaine de statut ATTIC \n");
								sql_updateq('spip_tradlangs', $data, 'id_tradlang=' . $id_tradlang);
								$trads = sql_allfetsel('id_tradlang', 'spip_tradlangs', 'id = ' . sql_quote($id) . ' AND module = ' . sql_quote($source[1]) . 'AND lang != ' . sql_quote($lang) . ' AND statut = ' . sql_quote('attic'));
								$maj = array('statut' => 'MODIF');
								foreach ($trads as $trad){
									salvatore_log("\n Changement d'une trad dans ATTIC \n");
									sql_updateq('spip_tradlangs', $maj, 'id_tradlang = ' . intval($trad['id_tradlang']));
								}
								$recuperees++;
							}
						}

						/**
						 * Vérifier si une autre chaîne de langue était identique (str == str)
						 *
						 * Si oui, on sélectionne toutes les occurences existantes dans les autres langues et on les duplique
						 */
						$identique_module = sql_getfetsel('id', 'spip_tradlangs', 'module = ' . sql_quote($source[1]) . ' AND lang = ' . sql_quote($lang) . ' AND str = ' . sql_quote($str_lang[$id]));
						if ($identique_module){
							salvatore_log('La nouvelle chaine est une chaine dupliquée : ' . $identique_module . "\n");
							$chaines_a_dupliquer = sql_allfetsel('*', 'spip_tradlangs', 'id = ' . sql_quote($identique_module) . ' AND id_tradlang_module = ' . intval($id_module) . ' AND lang != ' . sql_quote($lang));
							foreach ($chaines_a_dupliquer as $chaine){
								unset($chaine['id_tradlang']);
								unset($chaine['maj']);
								$chaine['id'] = $id;
								$chaine['titre'] = $id . ' : ' . $source[1] . ' - ' . $chaine['lang'];
								$chaine['md5'] = md5($chaine['str']);
								$chaine['date_modif'] = date('Y-m-d H:i:s');
								if ($chaine['statut']=='attic'){
									$chaine['statut'] = 'NEW';
								}
								$nouvelle_chaine = sql_insertq('spip_tradlangs', $chaine);
								salvatore_log('Ajout de la version ' . $chaine['lang'] . ' - ' . $nouvelle_chaine . "\n");
							}
						}
						$ajoutees++;
					}
				} elseif (isset($str_lang[$id]) and isset($existant[$id])) {
					// * chaine existante
					// * identique ? => NOOP
					/**
					 * On enlève les sauts de lignes windows pour des sauts de ligne linux
					 */
					$str_lang[$id] = str_replace("\r\n", "\n", $str_lang[$id]);

					/**
					 * protection dans les balises genre <a href="..." ou <img src="..."
					 * cf inc/filtres
					 */
					if (preg_match_all(_TYPO_BALISE, $str_lang[$id], $regs, PREG_SET_ORDER)){
						foreach ($regs as $reg){
							$insert = $reg[0];
							// hack: on transforme les caracteres a proteger en les remplacant
							// par des caracteres "illegaux". (cf corriger_caracteres())
							$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
							$str_lang[$id] = str_replace($reg[0], $insert, $str_lang[$id]);
						}
					}

					/**
					 * Protéger le contenu des balises <html> <code> <cadre> <frame> <tt> <pre>
					 */
					define('_PROTEGE_BLOCS_HTML', ',<(html|code|cadre|pre|tt)(\s[^>]*)?>(.*)</\1>,UimsS');
					if ((strpos($str_lang[$id], '<')!==false) and preg_match_all(_PROTEGE_BLOCS_HTML, $str_lang[$id], $matches, PREG_SET_ORDER)){
						foreach ($matches as $reg){
							$insert = $reg[0];
							// hack: on transforme les caracteres a proteger en les remplacant
							// par des caracteres "illegaux". (cf corriger_caracteres())
							$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
							$str_lang[$id] = str_replace($reg[0], $insert, $str_lang[$id]);
						}
					}

					/**
					 * On applique la typographie de la langue
					 */
					$str_lang[$id] = $typographie($str_lang[$id]);

					/**
					 * On remet les caractères normaux sur les caractères illégaux
					 */
					$str_lang[$id] = strtr($str_lang[$id], _TYPO_PROTECTEUR, _TYPO_PROTEGER);

					$str_lang[$id] = unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $str_lang[$id]), ENT_NOQUOTES, 'utf-8'));

					/**
					 * Calcul du nouveau md5
					 */
					$md5 = md5($str_lang[$id]);
					if ($md5==$existant[$id]){
						$inchangees++;
					} else {
						// * modifiee ? => UPDATE
						salvatore_log(md5($str_lang[$id]) . ' !- ' . md5($str_existant[$id]) . "\n");
						// modifier la chaine
						$modifs = array(
							'str' => $str_lang[$id],
							'md5' => ($orig ? $md5 : $existant[$id]),
							'statut' => ($orig ? 'OK' : ''),
							'comm' => $comm,
						);
						$id_tradlang = sql_getfetsel('id_tradlang', 'spip_tradlangs', "$bigwhere AND id = " . sql_quote($id));
						$test = tradlang_set($id_tradlang, $modifs);

						/**
						 * signaler le statut MODIF de ses traductions OK
						 * update des str de ses traductions NEW
						 */
						if ($orig and ($orig!=0)){
							spip_query(
								"UPDATE spip_tradlangs SET statut='MODIF' WHERE module='" . $source[1]
								. "' AND id=" . _q($id)
								. ' AND md5 != ' . _q($md5)
								. ' AND lang != ' . _q($lang)
								. " AND statut!='NEW'"
							);
						}
						spip_query(
							'UPDATE spip_tradlangs SET str = ' . sql_quote($str_lang[$id]) . "
							WHERE module='" . $source[1]
							. "' AND id=" . _q($id)
							. ' AND md5 != ' . _q($md5)
							. ' AND lang != ' . _q($lang)
							. " AND statut = 'NEW'"
						);
						$modifiees++;
					}
				} elseif (!isset($str_lang[$id]) and isset($existant[$id])) {
					// * chaine supprimee
					// mettre au grenier
					spip_query("UPDATE spip_tradlangs SET statut='attic' WHERE id=" . _q($id) . ' AND module = ' . _q($source[1]));
					$supprimees++;
				}

				if ($orig and isset($str_lang[$id])){
					$liste_id_orig[$id] = md5($str_lang[$id]);
				}
			}
			salvatore_log('!-- module ' . $source[1] . ", $lang : $modifiees modifiees, $ajoutees ajoutees, $supprimees supprimees, $recuperees recuperees, $ignorees ignorees, $inchangees inchangees\n");
		}
	}
	unset($liste_id_orig, $str_lang, $GLOBALS[$GLOBALS['idx_lang']]);
	return $ajoutees+$supprimees+$modifiees;
}

/**
 * Chargement des commentaires de fichier de langue
 * Le fichier est chargé en mode texte pour récupérer les commentaires dans lesquels sont situés les statuts
 *
 * @param string $f Le chemin du fichier de langue
 * @return array $liste_trad Un tableau id/chaine
 */
function charger_comm_fichier_langue($f){

	$contenu = file_get_contents($f);

	$tab = preg_split("/\r\n|\n\r|;\n|\n\/\/|\(\n|\n\);\n|\'\,\n|\n[\s\t]*(\')|\/\/[\s\t][0-9A-Z]\n[\s\t](\')/", $contenu, '-1', PREG_SPLIT_NO_EMPTY);

	$liste_trad = array();
	reset($tab);

	while (list(, $ligne) = each($tab)){
		$ligne = str_replace("\'", '', $ligne);
		if (strlen($ligne)>0){
			if (preg_match("/(.*?)\'[\s\t]*=>[\s\t]*\'(.*?)\'[\s\t]*,{0,1}[\s\t]*(#.*)?/ms", $ligne, $matches)){
				if (isset($matches[1]) and isset($matches[3]) and strlen(trim($matches[3]))>0){
					list(, $comm) = explode('#', $matches[3]);
					$liste_trad[$matches[1]] = trim($comm);
				}
			}
		}
	}
	reset($liste_trad);
	return $liste_trad;
}
