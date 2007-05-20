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
        foreach (explode(',', $_POST['fields_'.$crayon]) as $field) {
            $content[$field] = $_POST['content_'.$crayon.'_'.$field];
            // Compatibilite charset autre que utf8 ; en effet on recoit
            // obligatoirement les donnees en utf-8, par la magie d'ajax
            if ($GLOBALS['meta']['charset']!='utf-8') {
                include_spip('inc/charsets');
                $content[$field] = importer_charset($content[$field], 'utf-8');
            }
        }

        // Si les donnees POSTees ne correspondent pas a leur md5,
        // il faut les traiter
        if (md5(serialize($content)) <> $_POST['md5_'.$crayon]) {
            if (!isset($_POST['secu_'.$crayon]))
                $results[] = array($name, $content, $_POST['md5_'.$crayon], $crayon);

            elseif (verif_secu($name, $_POST['secu_'.$crayon]))
                $results[] = array($name, $content, $_POST['md5_'.$crayon], $crayon);
            else
                return false; // erreur secu
        }
        // cas inchange
        else
            $results[] = array($name, $content, false, $crayon);
    }

    return $results;
}


function action_crayons_store_dist() {
	include_spip('inc/crayons');
	lang_select($GLOBALS['auteur_session']['lang']);
	$wdgcfg = wdgcfg();
	header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);
	
	$return = array('$erreur'=>'');

	$postees = post_crayons();

	$modifs = $updates = array();
	if (!is_array($postees)) {
		$return['$erreur'] = _U('crayons:donnees_mal_formatees');
	} else {
		include_spip('inc/autoriser');

		foreach ($postees as $postee) {
			$name = $postee[0];
			$content = $postee[1];
			if ($content && preg_match(_PREG_CRAYON, 'crayon '.$name, $regs)) {
				list(,$crayon,$type,$modele,$id) = $regs;
				$wid = $postee[3];
				if (!autoriser('modifier', $type, $id, NULL, array('modele'=>$modele))) {
					$return['$erreur'] = 
					  "$type $id: " . _U('crayons:non_autorise');
				  break;
				}

				// recuperer l'existant pour calculer son md5 et verifier
				// qu'il n'a pas ete modifie entre-temps

				$data = array();
				foreach ($content as $champtable => $val) {
					$data[$champtable] = valeur_colonne_table($type, $champtable, $id);
				}
				$md5 = md5(serialize($data));

				// est-ce que le champ a ete modifie dans la base entre-temps ?
				if ($md5 != $postee[2]) {
					// si oui, la modif demandee correspond peut-etre
					// a la nouvelle valeur ? dans ce cas on procede
					// comme si "pas de modification", sinon erreur
					if ($md5 != md5(serialize($content))) {
						$return['$erreur'] = "$type $id $champtable: " .
							_U('crayons:modifie_par_ailleurs');
					}
					break;
				}

				$modifs[] = array($type, $modele, $id, $content, $wid);
			}
		}
	}

	if (!$modifs AND !$return['$erreur']) {
		$return['$erreur'] = $wdgcfg['msgNoChange'] ?
		   _U('crayons:pas_de_modification') : ' ';
		$return['$annuler'] = true;
	}

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if ($return['$erreur']) {
		echo var2js($return);
		exit;
	}

	// sinon on bosse : toutes les modifs ont ete acceptees
	// verifier qu'on a tout ce qu'il faut pour mettre a jour la base
	// et regrouper les mises a jour par type/id
	foreach ($modifs as $modif) {
		list($type, $modele, $id, $content, $wid) = $modif;
		if (!isset($updates[$type])) {
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
			$updates[$type] = array('fun'=>$fun, 'ids'=>array());
		}
		if (!isset($updates[$type]['ids'][$id])) {
			$updates[$type]['ids'][$id] = array('wdg'=>array(), 'chval'=>array());
		}
		// pour reaffecter le retour d'erreur sql au cas ou
		$updates[$type]['ids'][$id]['wdg'][] = $wid;
		foreach ($content as $champtable => $val) {
			$updates[$type]['ids'][$id]['chval'][$champtable] = $val;
		}
	}

	// il manque une fonction de mise à jour ==> on ne fait rien !
	if ($return['$erreur']) {
	    echo var2js($return);
	    exit;
	}
	// hop ! mises à jour table par table et id par id
	foreach ($updates as $type => $idschamps) {
		foreach ($idschamps['ids'] as $id => $champsvaleurs) {
	        // Enregistrer dans la base
	        // $updok = ... quand on aura un retour
	        // -- revisions_articles($id_article, $c) --
	        if ($idschamps['fun'] == 'crayons_update') {
		        crayons_update($id, $champsvaleurs['chval'], $type);
	        } else {
		        $idschamps['fun']($id, $champsvaleurs['chval']);
	        }
	    }
	}

	// et maintenant refaire l'affichage des crayons modifies
	include_spip('inc/texte');
	foreach ($modifs as $m) {
		list($type, $modele, $id, $content, $wid) = $modif;
			$f = charger_fonction($type.'_'.$modele, 'vues', true)
			  OR $f = charger_fonction($modele, 'vues', true)
			  OR $f = charger_fonction($type, 'vues', true)
			  OR $f = 'vues_dist';
			$return[$wid] = $f($type, $modele, $id, $content);
	}
	echo var2js($return);
	exit;
}

//
// VUE
//
function vues_dist($type, $modele, $id, $content){

	// pour ce qui a une {lang_select} par defaut dans la boucle,
	// la regler histoire d'avoir la bonne typo dans le propre()
	// NB: ceci n'a d'impact que sur le "par defaut" en bas
	if (colonne_table($type, 'lang')) {
		lang_select($a = valeur_colonne_table($type, 'lang', $id));
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
		    'champ' => $modele,
		    'class' => _request('class'),
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
		$valeur = valeur_colonne_table($type, $modele, $id);
		
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
		$update .= $sep . $col . '=' . _q($val);
		$sep = ', ';
	}

    return spip_query(
        'UPDATE ' . $nom_table . ' SET ' . $update . ' WHERE ' . $where);
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

?>
