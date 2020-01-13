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


include_spip('base/abstract_sql');
include_spip('inc/charsets');
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
function salvatore_lire($liste_sources, $dir_modules = null){
	include_spip('inc/salvatore');
	salvatore_init();

	// on va modifier a la base, il faut qu'elle soit a jour
	salvatore_verifier_base_upgradee();

	if (is_null($dir_modules)){
		$dir_modules = _DIR_SALVATORE_MODULES;
	}
	salvatore_check_dir($dir_modules);
	$refresh_time = time()-_SALVATORE_LECTEUR_REFRESH_DELAY;

	$tradlang_verifier_langue_base = charger_fonction('tradlang_verifier_langue_base', 'inc', true);
	$tradlang_verifier_bilans = charger_fonction('tradlang_verifier_bilans', 'inc', true);
	$invalider = false;

	foreach ($liste_sources as $source){
		salvatore_log("\n<info>--- Module " . $source['module'] . " | " . $source['dir_module'] . " | " . $source['url'] . "</info>");

		$module = $source['module'];
		$dir_module = $dir_modules . $source['dir_module'];

		if ($autre_gestionnaire = salvatore_verifier_gestionnaire_traduction($dir_module, $module)){
			salvatore_fail("[Lecteur] Erreur sur $module", "Erreur : import impossible, le fichier est traduit autre part : $autre_gestionnaire\n");
		}

		/**
		 * on doit absolument charger la langue principale en premier (a cause des MD5)
		 */
		$fichier_lang_principal = $dir_module . '/' . $module . '_' . $source['lang'] . '.php';
		$liste_fichiers_lang = glob($dir_module . '/' . $module . '_*.php');
		if (!in_array($fichier_lang_principal, $liste_fichiers_lang)){
			salvatore_fail("[Lecteur] Erreur sur $module", "|-- Pas de fichier lang principal $fichier_lang_principal : import impossible pour ce module");
		}

		// pour la suite, on enleve la langue principale de la liste des fichiers
		$liste_fichiers_lang = array_diff($liste_fichiers_lang, [$fichier_lang_principal]);

		/**
		 * On regarde quelle est la date de dernière modification du fichier de langue principale
		 */
		$last_update = filemtime($fichier_lang_principal);

		if ($row_module = salvatore_retrouver_tradlang_module($dir_module, $module)) {
			$id_tradlang_module = intval($row_module['id_tradlang_module']);
			salvatore_log("Module en base #$id_tradlang_module");
			/**
			 * Si la langue mere a changée, on la modifie
			 */
			if ($row_module['lang_mere']!==$source['lang']){
				sql_updateq('spip_tradlang_modules', array('lang_mere' => $source['lang']), 'id_tradlang_module=' . intval($id_tradlang_module));
				salvatore_log("lang_mere mise a jour : " . $row_module['lang_mere'] . " => " . $source['lang']);
				$last_update = time();
			}
			/**
			 * Si le dir_module a change, on le met a jour
			 */
			if ($row_module['dir_module']!==$source['dir_module']){
				sql_updateq('spip_tradlang_modules', array('dir_module' => $source['dir_module']), 'id_tradlang_module=' . intval($id_tradlang_module));
				salvatore_log("dir_module mis a jour : " . $row_module['dir_module'] . " => " . $source['dir_module']);
				$last_update = time();
			}
		}

		$langues_a_jour = array();

		if (!$row_module or $last_update>$refresh_time){
			$priorite = '';
			$modifs = 0;
			if (defined('_TRAD_PRIORITE_DEFAUT')){
				$priorite = _TRAD_PRIORITE_DEFAUT;
			}

			/**
			 * Si le module n'existe pas... on le crée
			 */
			if (!$row_module or !$id_tradlang_module = intval($row_module['id_tradlang_module'])){
				$insert = [
					'module' => $source['module'],
					'dir_module' => $source['dir_module'],
					'nom_mod' => $source['module'],
					'lang_prefix' => $source['module'],
					'lang_mere' => $source['lang'],
					'priorite' => $priorite,
				];
				$id_tradlang_module = sql_insertq('spip_tradlang_modules', $insert);
				/**
				 * Si insertion echoue on fail
				 */
				if (!intval($id_tradlang_module)){
					salvatore_fail("[Lecteur] Erreur sur $module", "Echec insertion dans spip_tradlang_modules " . json_encode($insert));
				}
				else {
					salvatore_log("Insertion en base #$id_tradlang_module");
				}
			}

		}
		// Pas de mise a jour recente du fichier maitre deja en base
		else {
			salvatore_log("On ne modifie rien : fichier original $fichier_lang_principal inchangé depuis " . date("Y-m-d H:i:s", $last_update));
			$id_tradlang_module=intval($row_module['id_tradlang_module']);

			/**
			 * Le fichier d'origine n'a pas été modifié
			 * Mais on a peut être de nouvelles langues
			 */
			$langues_en_base = sql_allfetsel('DISTINCT lang', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module));
			$langues_en_base = array_column($langues_en_base, 'lang');

			$langues_a_ajouter = array();
			foreach ($liste_fichiers_lang as $fichier_lang){
				$lang = salvatore_get_lang_from($module, $fichier_lang);
				if (!in_array($lang, $langues_en_base)){
					$langues_a_ajouter[] = array('lang' => $lang, 'fichier' => $fichier_lang);
				}
				else {
					// inutile de regarder ce fichier
					$langues_a_jour[] = $lang;
				}
			}

			$liste_fichiers_lang = array();
			if ($langues_a_ajouter){
				salvatore_log('On a ' . count($langues_a_ajouter) . " nouvelle(s) langue(s) à insérer (".count($langues_en_base). " langue(s) an base)");
				$liste_fichiers_lang = array_column($langues_a_ajouter, 'fichier');
			}
		}

		// traiter les fichiers lang
		if (count($liste_fichiers_lang)) {

			// on commence par la langue mere
			$liste_md5_master = array();
			$modifs_master = salvatore_importer_module_langue($id_tradlang_module, $source, $fichier_lang_principal, true, $liste_md5_master);

			// et on fait les autres langues
			foreach ($liste_fichiers_lang as $fichier_lang){
				salvatore_importer_module_langue($id_tradlang_module, $source, $fichier_lang, false, $liste_md5_master);

				$lang = salvatore_get_lang_from($module, $fichier_lang);
				if ($modifs_master>0) {
					if ($tradlang_verifier_langue_base) {
						$tradlang_verifier_langue_base($id_tradlang_module, $lang);
						salvatore_log('|-- Synchro de la langue ' . $lang . ' pour le module ' . $source['module']);
					}
					else {
						salvatore_log("<error>|-- Pas de Fonction de synchro inexistante pour synchroniser lang $lang</error>");
					}
				}
				$langues_a_jour[] = $lang;
			}

			/**
			 * On s'occupe des langues en base sans fichier
			 * s'il y a eu au moins une modif et que l'on peut faire la synchro
			 */
			if ($modifs_master>0 and $tradlang_verifier_langue_base){
				$langues_en_base = sql_allfetsel('DISTINCT lang', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module));
				$langues_en_base = array_column($langues_en_base, 'lang');

				if ($langues_pas_a_jour = array_diff($langues_en_base, $langues_a_jour)) {
					foreach ($langues_pas_a_jour as $langue_todo){
						$tradlang_verifier_langue_base($id_tradlang_module, $langue_todo);
						salvatore_log("|-- Synchro de la langue non exportée en fichier $langue_todo pour le module $module");
					}
				}
			}

			$invalider = true;
			salvatore_log("|");
			unset($langues_a_jour, $langues_pas_a_jour);
		}

		// Mise à jour des bilans
		if ($tradlang_verifier_bilans){
			salvatore_log("Création ou MAJ des bilans du module #$id_tradlang_module $module");
			$tradlang_verifier_bilans($id_tradlang_module, $source['lang'], false);
			salvatore_log("-");
		}
	}

	if ($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
}

/**
 * Import d'un fichier de langue dans la base
 *
 * @param int $id_tradlang_module
 * @param array $source
 *   tableau decrivant le module extrait du fichier traductions
 * @param string $fichier_lang
 *   chemin vers le fichier de langue
 * @param bool $is_master
 *   true signifie que c'est la langue originale
 * @param array $liste_md5_master
 * @return string
 */
function salvatore_importer_module_langue($id_tradlang_module, $source, $fichier_lang, $is_master, &$liste_md5_master){
	salvatore_log("!\n+ Import de $fichier_lang\n");
	$idx = $GLOBALS['idx_lang'] = 'i18n_' . crc32($fichier_lang) . '_tmp';

	$lang = salvatore_get_lang_from($source['module'], $fichier_lang);
	$module = $source['module'];

	// charger le fichier et ses commentaires
	$GLOBALS[$idx] = null;
	$commentaires = salvatore_charger_commentaires_fichier_langue($fichier_lang);

	include $fichier_lang;
	$chaines = $GLOBALS[$idx];  // on a vu certains fichiers faire des betises et modifier idx_lang

	if (is_null($chaines)){
		$erreur = "Erreur, fichier $fichier_lang mal forme";
		salvatore_log("<error>$erreur</error>");
		salvatore_envoyer_mail("[Lecteur] Erreur sur $module", $erreur);
		return false;
	}

	/**
	 * Nettoyer le contenu de ses <MODIF>,<NEW> et <PLUS_UTILISE>
	 * Ces chaines sont utilisées comme statut
	 */
	$status = array();

	foreach ($chaines as $id => $chaine){
		if ($is_master){
			$status[$id] = 'OK';
		} else {
			if (preg_match(',^<(MODIF|NEW|PLUS_UTILISE)>,US', $chaine, $r)){
				$chaines[$id] = preg_replace(',^(<(MODIF|NEW|PLUS_UTILISE)>)+,US', '', $chaine);
				$status[$id] = $r[1];
			} else {
				$status[$id] = 'OK';
			}
		}
	}

	$ajoutees = $inchangees = $supprimees = $modifiees = $ignorees = $recuperees = 0;

	if (array_key_exists($lang, $GLOBALS['codes_langues'])) {
		$statut_exclus = ($is_master ? 'attic' : 'MODIF');
		$res = sql_select("id, str, md5", "spip_tradlangs", "id_tradlang_module=" . intval($id_tradlang_module) . " AND lang=" . sql_quote($lang) . " AND statut!=" . sql_quote($statut_exclus));
		$nb = sql_count($res);
		if ($nb>0){
			salvatore_log("!-- Fichier de langue $lang du module $module deja inclus dans la base\n");
		}

		/**
		 * Si la langue est deja dans la base, on ne l'ecrase que s'il s'agit
		 * de la langue source
		 */
		if (!$nb or $is_master){
			// La liste de ce qui existe deja
			$existant = $str_existant = array();
			while ($row = sql_fetch($res)){
				$existant[$row['id']] = $row['md5'];
				$str_existant[$row['id']] = $row['str'];
			}

			$bigwhere = "id_tradlang_module=" . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang);

			include_spip('action/editer_tradlang');
			// Dans ce qui arrive, il y a 4 cas :
			$ids = array_unique(array_merge(array_keys($existant), array_keys($chaines)));
			foreach ($ids as $id){
				$comm = (isset($commentaires[$id])) ? $commentaires[$id] : '';

				/**
				 * 1. chaine neuve
				 */
				if (isset($chaines[$id]) and !isset($existant[$id])){
					$md5 = null;
					if ($is_master){
						$md5 = md5($chaines[$id]);
					} else {
						if (!isset($liste_md5_master[$id])){
							salvatore_log("<info>!-- Chaine $id inconnue dans la langue principale</info>");
							$ignorees++;
						} else {
							$md5 = $liste_md5_master[$id];
						}
					}

					if ($md5){
						$chaines[$id] = salvatore_nettoyer_chaine_php($chaines[$id], $lang);

						/**
						 * Calcul du nouveau md5
						 */
						$md5 = md5($chaines[$id]);

						/**
						 * Si le commentaire est un statut et que l'on ne traite pas le fichier de langue mère
						 * On vire le commentaire et met son contenu comme statut
						 */
						if (in_array($comm, array('NEW', 'OK', 'MODIF', 'MODI')) and !$is_master){
							if ($comm=='MODI'){
								$comm = 'MODIF';
							}
							$status[$id] = $comm;
							$comm = '';
						} else {
							if ((strlen($comm)>1) && preg_match('/(.*?)(NEW|OK|MODIF)(.*?)/', $comm, $matches)){
								if (!$is_master){
									$status[$id] = $matches[2];
								}
								$comm = preg_replace('/(NEW|OK|MODIF)/', '', $comm);
							}
						}

						/**
						 * On génère un titre
						 */
						$titre = $id . ' : ' . $source['module'] . ' - ' . $lang;

						$set = array(
							'id_tradlang_module' => $id_tradlang_module,
							'titre' => $titre,
							'module' => $source['module'],
							'lang' => $lang,
							'id' => $id,
							'str' => $chaines[$id],
							'comm' => $comm,
							'md5' => $md5,
							'statut' => $status[$id]
						);
						$id_tradlang = sql_insertq('spip_tradlangs', $set);

						/**
						 * L'identifiant de la chaîne de langue a peut être déjà été utilisé puis mis au grenier
						 * On le récupère donc
						 */
						if (!$id_tradlang){
							// TODO : la cle unique id doit etre sur id	- id_tradlang_module - lang et pas sur id	- module - lang
							// mais il serait bien de pouvoir piquer une chaine attic du meme module meme si pas id_tradlang_module identique
							$tradlang = sql_fetsel('*', 'spip_tradlangs', 'id=' . sql_quote($id) . ' AND id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang) . ' AND statut=' . sql_quote('attic'));
							if ($tradlang and $id_tradlang = intval($tradlang['id_tradlang'])){
								salvatore_log("<info>Recuperation chaine ".$source['module'].":{$id}[{$lang}] de statut ATTIC</info>");
								sql_updateq('spip_tradlangs', $set, 'id_tradlang=' . intval($id_tradlang));

								$trads = sql_allfetsel('id_tradlang', 'spip_tradlangs', 'id=' . sql_quote($id) . ' AND id_tradlang_module=' . intval($id_tradlang_module) . 'AND lang!=' . sql_quote($lang) . ' AND statut=' . sql_quote('attic'));
								$maj = array('statut' => 'MODIF');
								foreach ($trads as $trad){
									salvatore_log("Changement de la trad #" . $trad['id_tradlang'] . " ATTIC => MODIF");
									sql_updateq('spip_tradlangs', $maj, 'id_tradlang=' . intval($trad['id_tradlang']));
								}
								$recuperees++;
							}
							else {
								salvatore_fail("[Lecteur] Echec insertion", "Echec insertion en base : " . json_encode($set));
							}
						}

						/**
						 * Vérifier si une autre chaîne de langue était identique (str == str)
						 *
						 * Si oui, on sélectionne toutes les occurences existantes dans les autres langues et on les duplique
						 */
						$identique_module = sql_getfetsel('id', 'spip_tradlangs', 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang=' . sql_quote($lang) . ' AND str=' . sql_quote($chaines[$id]));
						if ($identique_module){
							salvatore_log('La nouvelle chaine est une chaine dupliquée : ' . $identique_module);

							$chaines_a_dupliquer = sql_allfetsel('*', 'spip_tradlangs', 'id=' . sql_quote($identique_module) . ' AND id_tradlang_module=' . intval($id_tradlang_module) . ' AND lang!=' . sql_quote($lang));
							foreach ($chaines_a_dupliquer as $chaine){
								unset($chaine['id_tradlang']);
								unset($chaine['maj']);
								$chaine['id'] = $id;
								$chaine['titre'] = $id . ' : ' . $source['module'] . ' - ' . $chaine['lang'];
								$chaine['md5'] = md5($chaine['str']);
								$chaine['date_modif'] = date('Y-m-d H:i:s');
								if ($chaine['statut']=='attic'){
									$chaine['statut'] = 'NEW';
								}
								$nouvelle_chaine = sql_insertq('spip_tradlangs', $chaine);
								salvatore_log('Ajout de la version ' . $chaine['lang'] . ' - #' . $nouvelle_chaine);
							}
						}
						$ajoutees++;
					}
				}


				/**
				 * 2. chaine existante
				 *
				 */
				elseif (isset($chaines[$id]) and isset($existant[$id])) {
					// * chaine existante
					// * identique ? => NOOP
					$chaines[$id] = salvatore_nettoyer_chaine_php($chaines[$id], $lang);

					/**
					 * Calcul du nouveau md5
					 */
					$md5 = md5($chaines[$id]);
					if ($md5 === $existant[$id]){
						$inchangees++;
					}
					else {
						// * modifiee ? => UPDATE
						salvatore_log("Chaine $id modifiee $md5 != ".$existant[$id]);

						// modifier la chaine
						$modifs = array(
							'str' => $chaines[$id],
							'md5' => ($is_master ? $md5 : $existant[$id]),
							'statut' => ($is_master ? 'OK' : ''),
							'comm' => $comm,
						);
						$id_tradlang = sql_getfetsel('id_tradlang', 'spip_tradlangs', "$bigwhere AND id = " . sql_quote($id));
						$test = tradlang_set($id_tradlang, $modifs);

						/**
						 * signaler le statut MODIF de ses traductions OK
						 * update des str de ses traductions NEW
						 */
						if ($is_master){
							sql_updateq('spip_tradlangs', array('statut'=> 'MODIF'),
								'id_tradlang_module=' . intval($id_tradlang_module)
								. ' AND id=' . sql_quote($id)
								. ' AND md5!=' . sql_quote($md5)
								. ' AND lang!=' . sql_quote($lang)
								. ' AND statut!=' . sql_quote('NEW'));
						}
						sql_updateq('spip_tradlangs', array('str'=> $chaines[$id]),
							'id_tradlang_module=' . intval($id_tradlang_module)
							. ' AND id=' . sql_quote($id)
							. ' AND md5!=' . sql_quote($md5)
							. ' AND lang!=' . sql_quote($lang)
							. ' AND statut=' . sql_quote('NEW'));
						$modifiees++;
					}
				}

				/**
				 * 3. chaine supprimee
				 *
				 */
				elseif (!isset($chaines[$id]) and isset($existant[$id])) {
					// * chaine supprimee
					// mettre au grenier
					sql_updateq('spip_tradlangs', array('statut' => 'attic'), 'id_tradlang_module=' . intval($id_tradlang_module) . ' AND id=' . sql_quote($id));
					$supprimees++;
				}

				if ($is_master and isset($chaines[$id])){
					$liste_md5_master[$id] = md5($chaines[$id]);
				}
			}
			salvatore_log('!-- module ' . $source['module'] . ", $lang : $modifiees modifiees, $ajoutees ajoutees, $supprimees supprimees, $recuperees recuperees, $ignorees ignorees, $inchangees inchangees");
		}
	}
	else {
		salvatore_log("<error>!-- Attention : La langue $lang n'existe pas dans les langues possibles - $module</error>");
	}

	// TODO : BUG ?
	// unset $liste_md5_master alors qu'elle est repassee d'un appel a l'autre ?
	unset($liste_md5_master, $chaines, $GLOBALS[$GLOBALS['idx_lang']]);

	return $ajoutees + $supprimees + $modifiees;
}


/**
 * Chargement des commentaires de fichier de langue
 * Le fichier est chargé en mode texte pour récupérer les commentaires dans lesquels sont situés les statuts
 *
 * @param string $fichier_lang Le chemin du fichier de langue
 * @return array $liste_trad Un tableau id/chaine
 */
function salvatore_charger_commentaires_fichier_langue($fichier_lang){

	$contenu = file_get_contents($fichier_lang);

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

/**
 * Nettoyer la chaine de langue venant du fichier PHP
 * @param string $chaine
 * @param string $lang
 * @return string
 */
function salvatore_nettoyer_chaine_php($chaine, $lang){
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
