<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function verif_secu($w, $secu) {
    return (
        $secu == md5($GLOBALS['meta']['alea_ephemere'].'='.$w)
    OR
        $secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].'='.$w)
    );
}

function post_crayons() {
    $results = array();
    if (isset($_POST['crayons']) AND is_array($_POST['crayons']))
    foreach ($_POST['crayons'] as $crayon) {

        $name = $_POST['name_'.$crayon];
        $content = array();
        if ($_POST['fields_'.$crayon]) {
          foreach (explode(',', $_POST['fields_'.$crayon]) as $field) {
            // cas particulier d'un envoi de fichier
            if (isset($_FILES['content_'.$crayon.'_'.$field])) {
            	if ($_FILES['content_'.$crayon.'_'.$field]['size']>0)
            		$content[$field] = $_FILES['content_'.$crayon.'_'.$field];
            	else
            		unset($name);
            } else {
            	$content[$field] = $_POST['content_'.$crayon.'_'.$field];
            	// Compatibilite charset autre que utf8 ; en effet on recoit
            	// obligatoirement les donnees en utf-8, par la magie d'ajax
            	// ... sauf dans le cas d'un envoi de fichier !
            	if ($GLOBALS['meta']['charset']!='utf-8' AND !count($_FILES)) {
            	    include_spip('inc/charsets');
            	    $content[$field] = importer_charset($content[$field], 'utf-8');
            	}
            }
          }
        }

        // Si les donnees POSTees ne correspondent pas a leur md5,
        // il faut les traiter
        if (isset($name)
        AND md5(serialize($content)) != $_POST['md5_'.$crayon]) {
            if (!isset($_POST['secu_'.$crayon])
            OR verif_secu($name, $_POST['secu_'.$crayon])) {
                $results[] = array($name, $content, $_POST['md5_'.$crayon], $crayon);
            }
            else {
                return false; // erreur secu
            }
        }
        // cas inchange
        else
            $results[] = array($name, $content, false, $crayon);
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

	$modifs = $updates = array();
	if (!is_array($postees)) {
		$return['$erreur'] = _U('crayons:donnees_mal_formatees');
	} else {
		foreach ($postees as $postee)
		if ($postee[2] !== false) {
			$name = $postee[0];
			$content = $postee[1];

			if ($content && preg_match(_PREG_CRAYON, 'crayon '.$name, $regs)) {
				list(,$crayon,$type,$modele,$id) = $regs;
				$wid = $postee[3];

				if (!autoriser('crayonner', $type, $id, NULL, array('modele'=>$modele))) {
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

					$modifs[] = array($type, $modele, $id, $content, $wid);
				}
			}
		}
	}

	if (!$modifs AND !$return['$erreur']) {
		$return['$erreur'] = $wdgcfg['msgNoChange'] ?
		   _U('crayons:pas_de_modification') : ' ';
		$return['$annuler'] = true;
	}

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if ($return['$erreur'])
		return $return;

	// on traite toutes les modifications
	// en appelant la fonction adequate de traitement
	$set_modifs = $options['f_set_modifs'];
	$return = $set_modifs($modifs, $return);

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if ($return['$erreur'])
		return $return;

	// et maintenant refaire l'affichage des crayons modifies
	include_spip('inc/texte');
	foreach ($modifs as $m) {
		list($type, $modele, $id, $content, $wid) = $m;
			$f = charger_fonction($type.'_'.$modele, 'vues', true)
			  OR $f = charger_fonction($modele, 'vues', true)
			  OR $f = charger_fonction($type, 'vues', true)
			  OR $f = 'vues_dist';
			$return[$wid] = $f($type, $modele, $id, $content, $wid);
	}
	return $return;
}

// recuperer une valeur en fonction des parametres recuperes
// cette fonction cherche une valeur d'un colonne d'une table SQL
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

		// MODELE
		$fun = '';
		if (function_exists($f = $type.'_'. $modele . "_revision")
		OR function_exists($f = $modele . "_revision")
		OR function_exists($f = $type . "_revision"))
			$fun = $f;
		else switch($type) {
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
			// cas geres de la maniere la plus standard
			case 'auteur':
			case 'document':
			case 'mot':
			case 'signature':
			case 'petition':
			default:
				include_spip('inc/modifier');
				$fun = 'revision_'.$type;
				break;
		}
		if (!$fun or !function_exists($fun)) {
				$fun = 'crayons_update';
//			    $return['$erreur'] = "$type: " . _U('crayons:non_implemente');
//			    break;
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

	// il manque une fonction de mise à jour ==> on ne fait rien !
	if ($return['$erreur'])
	    return $return;

	// hop ! mises à jour table par table et id par id
	foreach ($updates as $type => $idschamps)
	foreach ($idschamps as $fun => $ids) {
		foreach ($ids as $id => $champsvaleurs) {
	        // Enregistrer dans la base
	        // $updok = ... quand on aura un retour
	        // -- revisions_articles($id_article, $c) --
	        spip_log("$fun($id ...)", 'crayons');
	        $fun($id, $champsvaleurs['chval'], $type, $champsvaleurs['wdg']);
	    }
	}

	return $return;
}

//
// VUE
//
function vues_dist($type, $modele, $id, $content, $wid){
	// pour ce qui a une {lang_select} par defaut dans la boucle,
	// la regler histoire d'avoir la bonne typo dans le propre()
	// NB: ceci n'a d'impact que sur le "par defaut" en bas
	if (colonne_table($type, 'lang')) {
		$b = valeur_colonne_table($type, 'lang', $id);
		lang_select($a = array_pop($b));
	} else {
		lang_select($a = $GLOBALS['meta']['langue_site']);
	}

  // chercher vues/article_toto.html
  // sinon vues/toto.html
  if (find_in_path( ($fond = 'vues/' . $type . '_' . $modele) . '.html')
  OR find_in_path( ($fond = 'vues/' . $modele) .'.html')
  OR find_in_path( ($fond = 'vues/' . $type) .'.html')) {
		$contexte = array(
		    'id_' . $type => $id,
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
  }
	// vue par defaut
	else {
		// Par precaution on va rechercher la valeur
		// dans la base de donnees (meme si a priori la valeur est
		// ce qu'on vient d'envoyer, il y a nettoyage des caracteres et
		// eventuellement d'autres filtres de saisie...)
		$valeur = array_pop(valeur_colonne_table($type, $modele, $id));

		// seul spip core sait rendre les donnees
		if (in_array($modele,
		  array('chapo', 'texte', 'descriptif', 'ps', 'bio'))) {
			return propre($valeur);
		} else {
			return typo($valeur);
		}
	}
}

//
// Fonctions de mise a jour generique
//
function crayons_update($id, $colval = array(), $type = '')
{
	if (!$colval) {
		return false;
	}
	list($nom_table, $where) = table_where($type, $id);
	if (!$nom_table) {
		return false;
	}

	$update = $sep = '';
	foreach ($colval as $col => $val) {
		$update .= $sep . '`' . $col . '`=' . _q($val);
		$sep = ', ';
	}

	$a = spip_query($q =
        'UPDATE `' . $nom_table . '` SET ' . $update . ' WHERE ' . $where);

	#spip_log($q);
	include_spip('inc/invalideur');
	suivre_invalideur($cond, $modif=true);

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
		unset ($c['id_rubrique']);
	}
	instituer_article($id_article, $c);
}

// TODO:
// Ce modele est cense enregistrer les tags sous forme de ??
// une ligne dans un champ spip_articles.tags, et/ou des mots-clés...
function modeles_tags($id, $c) {
	var_dump($id); #id_article
	var_dump($c); # perturbant : ici on a array('id_article'=>'valeur envoyee')
}

function action_crayons_store_dist() {
	return action_crayons_store_args();
}

// permettre de passer une autre fonction de stockage des informations
function action_crayons_store_args($store = 'crayons_store') {
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
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
		OR $r['$annuler']) {
			include_spip('inc/headers');
			redirige_par_entete(_request('redirect'));
		} else {
			echo "<h4 class='status'>".$r['$erreur']."</h4>\n";

			foreach ($r as $wid => $v) {
				if ($wid !== '$erreur')
					echo "<div id='$wid'>$v</div><hr />\n";
			}
			echo "<a href='".quote_amp(_request('redirect'))."'>"
				.quote_amp(_request('redirect'))
				."</a>\n";
		}
	}

	// Cas normal : JSON
	else {
		echo json_export($r);
	}

	exit;
}

?>
