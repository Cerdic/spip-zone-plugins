<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function verif_secu($w, $secu) {
	return (
		$secu == md5($GLOBALS['meta']['alea_ephemere'].'='.$w)
	or
		$secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].'='.$w)
	);
}

function post_crayons() {
	$results = array();

	if (isset($_POST['crayons']) and is_array($_POST['crayons'])) {
		foreach ($_POST['crayons'] as $crayon) {
			$name = $_POST['name_'.$crayon];
			$content = array();
			if ($_POST['fields_'.$crayon]) {
				foreach (explode(',', $_POST['fields_'.$crayon]) as $field) {
					// cas particulier d'un envoi de fichier
					if (isset($_FILES['content_'.$crayon.'_'.$field])) {
						if ($_FILES['content_'.$crayon.'_'.$field]['size'] > 0) {
							$content[$field] = $_FILES['content_'.$crayon.'_'.$field];
						} else {
							$content[$field] = false;
						}
						// cf. valeur passee dans crayon->md5() : false ou filemtime() du logo
					} else {
						/**
						 * le changement de charset n'est plus necessaire
						 * depuis jquery 1.5 (feature non documentee de jquery!)
						 */
						if (isset($_POST['content_'.$crayon.'_'.$field])) {
							$content[$field] = is_array($_POST['content_'.$crayon.'_'.$field])
								?implode(',', $_POST['content_'.$crayon.'_'.$field])
								:$_POST['content_'.$crayon.'_'.$field];
						} else {
							$content[$field] = null;
						}
					}
				}
			}

			// Si les donnees POSTees ne correspondent pas a leur md5,
			// il faut les traiter
			if (isset($name)
				and md5(serialize($content)) != $_POST['md5_'.$crayon]) {
				if (!isset($_POST['secu_'.$crayon])
					or verif_secu($name, $_POST['secu_' . $crayon])) {
					$results[] = array($name, $content, $_POST['md5_'.$crayon], $crayon);
				} else {
					return false; // erreur secu
				}
			} else {
				// cas inchange
				$results[] = array($name, $content, false, $crayon);
			}
		}
	}
	return $results;
}


function crayons_store($options = array()) {
	// permettre de surcharger les fonctions de recuperation des valeurs
	// et de sauvegardes de celles-ci
	$options = array_merge(array(
			'f_get_valeur' => 'crayons_store_get_valeur',
			'f_set_modifs' => 'crayons_store_set_modifs',
		), $options);

	include_spip('inc/crayons');
	$wdgcfg = wdgcfg();

	$return = array('$erreur'=>'');

	$postees = post_crayons();

	$invalides = $modifs = $updates = array();

	if (!is_array($postees)) {
		$return['$erreur'] = _U('crayons:donnees_mal_formatees');
	} else {
		foreach ($postees as $postee) {
			if ($postee[2] !== false) {
				$name = $postee[0];
				$content = $postee[1];

				if ($content && preg_match(_PREG_CRAYON, 'crayon '.$name, $regs)) {
					list(,$crayon,$type,$modele,$id) = $regs;
					$wid = $postee[3];

					spip_log("autoriser('crayonner', $type, $id, null, array('modele' => $modele)", 'crayons_distant');
					if (!autoriser('crayonner', $type, $id, null, array('modele' => $modele))) {
						$return['$erreur'] =
							"$type $id: " . _U('crayons:non_autorise');
					} else {
						// recuperer l'existant pour calculer son md5 et verifier
						// qu'il n'a pas ete modifie entre-temps
						$get_valeur = $options['f_get_valeur'];
						$data = $get_valeur($content, $regs);

						$md5 = md5(serialize($data));

						// est-ce que le champ a ete modifie dans la base entre-temps ?
						if ($md5 != $postee[2]) {
							// si oui, la modif demandee correspond peut-etre
							// a la nouvelle valeur ? dans ce cas on procede
							// comme si "pas de modification", sinon erreur
							if ($md5 != md5(serialize($content))) {
								$return['$erreur'] = "$type $id $modele: " .
									_U('crayons:modifie_par_ailleurs');
							}
						}

						// Vérifications, en retournant presque comme le pipeline formulaire_verifier
						// On permet aussi de modifier la valeur soumise, si une valeur est retournée dans normaliser.
						$data = pipeline(
							'crayons_verifier',
							array(
								'args' => array(
									'type' => $type,
									'modele' => $modele,
									'id' => $id,
									'content' => $content,
									'wid' => $wid,
								),
								'data' => array(
									'erreurs' => array(), // couples : champ => texte d'erreur
									'normaliser' => array(), // couples : champ => valeur à utiliser
								),
							)
						);

						if (count($data['normaliser'])) {
							$content = $data['normaliser'] + $content;
						}

						if ($data['erreurs']) {
							foreach ($data['erreurs'] as $c => $e) {
								$invalides[$wid . '_' . $c]['msg'] = $e;
							}
						}

						$modifs[] = array($type, $modele, $id, $content, $wid);

						// Anciennes méthodes de vérifications.
						// Aiguillage pour verification de la saisie
						// Pour traitement ulterieur les fonctions de verifications doivent renvoyer $invalides :
						// $invalides[wid_champ]['msg'] -> message de saisie invalide
						// $invalides[wid_champ]['retour'] -> caracteres invalides
						$f = 'verifier_'.$type.'_'.$modele;
						if (function_exists($f)) {
							$_invalides = $f($modifs);
							if ($_invalides and is_array($invalides)) {
								$invalides = array_merge($invalides, $_invalides);
							}
						}

					}
				}
			}
		}
	}

	if (!$modifs and !$return['$erreur']) {
		$return['$erreur'] = $wdgcfg['msgNoChange'] ? _U('crayons:pas_de_modification') : ' ';
		$return['$annuler'] = true;
	}

	// un champ invalide ... ou rien ==> on ne fait rien !
	if (count($invalides)) {
		$return['$invalides'] = $invalides;
		return $return;
	}

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if (isset($return['$erreur']) and $return['$erreur']) {
		return $return;
	}

	// on traite toutes les modifications
	// en appelant la fonction adequate de traitement
	$set_modifs = $options['f_set_modifs'];
	$return = $set_modifs($modifs, $return);

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if ($return['$erreur']) {
		return $return;
	}

	// et maintenant refaire l'affichage des crayons modifies
	include_spip('inc/texte');
	foreach ($modifs as $m) {
		list($type, $modele, $id, $content, $wid) = $m;
			$f = charger_fonction($type.'_'.$modele, 'vues', true)
				or $f = charger_fonction($modele, 'vues', true)
				or $f = charger_fonction($type, 'vues', true)
				or $f = 'vues_dist';
			$return[$wid] = $f($type, $modele, $id, $content, $wid);
	}
	return $return;
}

// recuperer une valeur en fonction des parametres recuperes
// cette fonction cherche une valeur d'une colonne d'une table SQL
function crayons_store_get_valeur($content, $regs) {
	list(,$crayon,$type,$modele,$id) = $regs;
	return valeur_colonne_table($type, array_keys($content), $id);
}

// stocke les valeurs envoyees dans des colonnes de table SQL
function crayons_store_set_modifs($modifs, $return) {
	// sinon on bosse : toutes les modifs ont ete acceptees
	// verifier qu'on a tout ce qu'il faut pour mettre a jour la base
	// et regrouper les mises a jour par type/id
	foreach ($modifs as $modif) {
		list($type, $modele, $id, $content, $wid) = $modif;

		$fun = '';
		// si le crayon est un MODELE avec une fonction xxx_revision associee
		// cas ou une fonction xxx_revision existe
		if (function_exists($f = $type.'_'. $modele . '_revision')
			or function_exists($f = $modele . '_revision')
			or function_exists($f = $type . '_revision')) {
			$fun = $f;
		} elseif (function_exists('lister_tables_objets_sql')
			and $tables_objet = lister_tables_objets_sql()
			and isset($tables_objet[table_objet_sql($type)])) {
			// si on est en SPIP 3+ et qu'on edite un objet editorial bien declare
			// passer par l'API objet_modifier
			$fun = 'crayons_objet_modifier';
		} else {
			// sinon spip < 3 (ou pas un objet edito)
			// on teste les objets connus et on route sur les fonctions correspondantes
			switch ($type) {
				case 'article':
					$fun = 'crayons_update_article';
					break;
				case 'breve':
					include_spip('action/editer_breve');
					$fun = 'revisions_breves';
					break;
				case 'forum':
					include_spip('inc/forum');
					$fun = 'enregistre_et_modifie_forum';
					break;
				case 'rubrique':
					include_spip('action/editer_rubrique');
					$fun = 'revisions_rubriques';
					break;
				case 'syndic':
				case 'site':
					include_spip('action/editer_site');
					$fun = 'revisions_sites';
					break;
				case 'document':
					include_spip('plugins/installer');
					include_spip('inc/plugin');
					if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0alpha', '>=')) {
						include_spip('action/editer_document');
						$fun = 'document_modifier';
					} else {
						include_spip('inc/modifier');
						$fun = 'revision_document';
					}
					break;
				// cas geres de la maniere la plus standard
				case 'auteur':
				case 'mot':
				case 'signature':
				case 'petition':
				default:
					include_spip('inc/modifier');
					$fun = 'revision_'.$type;
					break;
			}
		}
		// si on a pas reussi on passe par crayons_update() qui fera un update sql brutal
		if (!$fun or !function_exists($fun)) {
			$fun = 'crayons_update';
			// $return['$erreur'] = "$type: " . _U('crayons:non_implemente');
			// break;
		}

		if (!isset($updates[$type][$fun])) {
			$updates[$type][$fun] = array();
		}
		if (!isset($updates[$type][$fun][$id])) {
			$updates[$type][$fun][$id] = array('wdg'=>array(), 'chval'=>array());
		}
		// pour reaffecter le retour d'erreur sql au cas ou
		$updates[$type][$fun][$id]['wdg'][] = $wid;
		foreach ($content as $champtable => $val) {
			$updates[$type][$fun][$id]['chval'][$champtable] = $val;
		}
	}

	// il manque une fonction de mise a jour ==> on ne fait rien !
	if ($return['$erreur']) {
		return $return;
	}

	// hop ! mises a jour table par table et id par id
	foreach ($updates as $type => $idschamps) {
		foreach ($idschamps as $fun => $ids) {
			foreach ($ids as $id => $champsvaleurs) {
				/* cas particulier du logo dans un crayon complexe :
				   ce n'est pas un champ de la table */
				if (isset($champsvaleurs['chval']['logo'])) {
					spip_log('revision logo', 'crayons');
					logo_revision($id, $champsvaleurs['chval'], $type, $champsvaleurs['wdg']);
					unset($champsvaleurs['chval']['logo']);
				}
				if (count($champsvaleurs['chval'])) {
					// -- revisions_articles($id_article, $c) --
					spip_log("$fun($id ...)", 'crayons');
					$updok = $fun($id, $champsvaleurs['chval'], $type, $champsvaleurs['wdg']);
					// Renvoyer erreur si update base distante echoue,
					// on ne regarde pas les updates base local car ils ne renvoient rien
					list($distant,$table) = distant_table($type);
					if ($distant and !$updok) {
						$return['$erreur'] = "$type: " . _U('crayons:update_impossible');
					}
				}
			}
		}
	}
	return $return;
}

//
// VUE
//
function vues_dist($type, $modele, $id, $content, $wid) {
	// pour ce qui a une {lang_select} par defaut dans la boucle,
	// la regler histoire d'avoir la bonne typo dans le propre()
	// NB: ceci n'a d'impact que sur le "par defaut" en bas
	list($distant,$table) = distant_table($type);
	if (colonne_table($type, 'lang')) {
		$b = valeur_colonne_table($type, 'lang', $id);
		lang_select($a = array_pop($b));
	} else {
		lang_select($a = $GLOBALS['meta']['langue_site']);
	}

	// chercher vues/article_toto.html
	// sinon vues/toto.html
	if (find_in_path(($fond = 'vues/' . $type . '_' . $modele) . '.html')
		or find_in_path(($fond = 'vues/' . $modele) .'.html')
		or find_in_path(($fond = 'vues/' . $type) .'.html')) {
		$primary = (function_exists('id_table_objet')?id_table_objet($table):'id_' . $table);
		$contexte = array(
			$primary => $id,
			'crayon_type' => $type,
			'crayon_modele' => $modele,
			'champ' => $modele,
			'class' => _request('class_'.$wid),
			'self' => _request('self'),
			'lang' => $GLOBALS['spip_lang']
		);
		$contexte = array_merge($contexte, $content);
		include_spip('public/assembler');
		return recuperer_fond($fond, $contexte);
	} else {
		// vue par defaut
		// Par precaution on va rechercher la valeur
		// dans la base de donnees (meme si a priori la valeur est
		// ce qu'on vient d'envoyer, il y a nettoyage des caracteres et
		// eventuellement d'autres filtres de saisie...)
		$bdd = valeur_colonne_table($type, $modele, $id);
		if (count($bdd)) {
			$valeur = array_pop($bdd);
		} else {
			// les champs n'ont pas ete retrouves dans la base
			// ce qui signifie a priori que nous sommes en face d'une cle primaire compose
			// et qu'un crayon a modifie un element de cette cle (c'est pas malin !)
			// dans ce cas, on reaffiche a minima ce qu'on vient de publier
			// mais il sera impossible de le reediter dans la foulee avec le meme crayon
			// (car l'identifiant du crayon se base sur l'id).
			// Il faudra donc recharger la page pour pouvoir reediter.
			if (is_scalar($id)) {
				$valeur = $content[$modele];
			}
		}

		if ($valeur) {
			// seul spip core sait rendre les donnees
			if (function_exists('appliquer_traitement_champ')) {
				$valeur = appliquer_traitement_champ($valeur, $modele, table_objet($table));
			} else {
				if (in_array($modele, array('chapo', 'texte', 'descriptif', 'ps', 'bio'))) {
					$valeur = propre($valeur);
				} else {
					$valeur = typo($valeur);
				}
			}
		}
		return $valeur;
	}
}


/**
 * Fonction de mise a jour par API editer_objet
 * @param $id
 * @param $data
 * @param $type
 * @param $ref
 * @return bool|mixed|string
 */
function crayons_objet_modifier($id, $data, $type, $ref) {
	if (include_spip('action/editer_objet')
		and function_exists('objet_modifier')) {
		$type = objet_type($type);
		// objet_modifier attend id_parent pour le parent et pas id_rubrique
		if (isset($data['id_rubrique']) and !isset($data['id_parent']) and $type!=='rubrique') {
			$data['id_parent'] = $data['id_rubrique'];
		}
		return objet_modifier($type, $id, $data);
	}
	// fallback
	return crayons_update($id, $data, $type);
}

//
// Fonctions de mise a jour generique
//
function crayons_update($id, $colval = array(), $type = '') {
	if (!$colval or !count($colval)) {
		return false;
	}
	list($distant,$table) = distant_table($type);

	if ($distant) {
		list($nom_table, $where) = table_where($type, $id);
		if (!$nom_table) {
			return false;
		}

		$update = $sep = '';
		foreach ($colval as $col => $val) {
			$update .= $sep . '`' . $col . '`=' . _q($val);
			$sep = ', ';
		}

		$a = spip_query($q = 'UPDATE `' . $nom_table . '` SET ' . $update . ' WHERE ' . $where, $distant);

		#spip_log($q);
		include_spip('inc/invalideur');
		suivre_invalideur($cond, $modif = true);
	} else {
		// cle primaire composee : 3-4-rubrique
		// calculer un where approprie
		// et modifier sans passer par la fonction destinee aux tables principales
		// on limite a SPIP 2 mini car sql_updateq n'est pas mappe dans les crayons_compat
		if (is_scalar($id) and ($GLOBALS['spip_version_code'] >= '1.93')) {
			list($nom_table, $where) = table_where($type, $id, true); // where sous forme de tableau
			$a = sql_updateq($nom_table, $colval, $where);
		} else {
			// modification d'une table principale
			include_spip('inc/modifier');
			$a = modifier_contenu($type, $id, array(), $colval);
		}
	}
	return $a;
}

//
// Fonctions de mise a jour
//
function crayons_update_article($id_article, $c = false) {
	include_spip('action/editer_article');

	// Enregistrer les nouveaux contenus
	revisions_articles($id_article, $c);

	// En cas de statut ou de id_rubrique
	// NB: instituer_article veut id_parent, et pas id_rubrique !
	if (isset($c['id_rubrique'])) {
		$c['id_parent'] = $c['id_rubrique'];
		unset($c['id_rubrique']);
	}
	instituer_article($id_article, $c);
}

/**
 * Enregistre les modifications sur une configuration
 * suite à un crayon sur une meta
 *
 * La colonne est toujours 'valeur' pour ces données.
 * La donnée à enregistrer peut-être une sous partie de configuration.
 * Si c'est le cas, on gère l'enregistrement via ecrire_config.
 *
 * @param string $a
 *   Nom ou clé de la meta (descriptif_site ou demo__truc pour demo/truc)
 * @param bool|array $c
 *   Liste des champs modifiés
 *   Ici, 'valeur' normalement.
 * @return void
**/
function revision_meta($a, $c = false) {
	if (isset($c['valeur'])) {
		// Certaines cles de configuration sont echapées ici (cf #EDIT_CONFIG{demo/truc})
		$a = str_replace('__', '/', $a);
		spip_log("meta '$a' = '$c[valeur]'", 'crayons');
		// eviter de planter les vieux SPIP
		if (false === strpos($a, '/')) {
			ecrire_meta($a, $c['valeur']);
		// SPIP 3 ou Bonux 2 ou CFG
		} else {
			include_spip('inc/config');
			ecrire_config($a, $c['valeur']);
		}
		include_spip('inc/invalideur');
		suivre_invalideur('meta');
	}
}


// TODO:
// Ce modele est cense enregistrer les tags sous forme de ??
// une ligne dans un champ spip_articles.tags, et/ou des mots-cles...
function modeles_tags($id, $c) {
	var_dump($id); #id_article
	var_dump($c); # perturbant : ici on a array('id_article'=>'valeur envoyee')
}

function action_crayons_store_dist() {
	return action_crayons_store_args();
}

// permettre de passer une autre fonction de stockage des informations
function action_crayons_store_args($store = 'crayons_store') {
	header('Content-Type: text/plain; charset='.$GLOBALS['meta']['charset']);
	lang_select($GLOBALS['auteur_session']['lang']);

	$r = $store();

	// Si on a ete appeles par jQuery, on renvoie tout, c'est le client
	// crayons.js qui va traiter l'affichage du resultat et status
	# Attention le test $_SERVER["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest"
	# n'est pas bon car le cas d'un fichier uploade via iframe n'est pas detecte

	// S'il y a une adresse de redirection, on renvoie vers elle
	// En cas d'erreur il faudrait ajouter &err=... dans l'url ?
	if (_request('redirect')) {
		if (!$r['$erreur']
			or $r['$annuler']) {
			include_spip('inc/headers');
			redirige_par_entete(_request('redirect'));
		} else {
			echo "<h4 class='status'>".$r['$erreur']."</h4>\n";

			foreach ($r as $wid => $v) {
				if ($wid !== '$erreur') {
					echo "<div id='$wid'>$v</div><hr />\n";
				}
			}
			echo "<a href='".quote_amp(_request('redirect'))."'>"
				.quote_amp(_request('redirect'))
				."</a>\n";
		}
	} else {
		// Cas normal : JSON
		echo crayons_json_export($r);
	}

	exit;
}
