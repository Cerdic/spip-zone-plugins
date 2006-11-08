<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function verif_secu($w, $secu) {
    return (
        $secu == md5($GLOBALS['meta']['alea_ephemere'].'='.$w)
    OR
        $secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].'='.$w)
    );
}

function post_widgets() {
    $results = array();

    if (isset($_POST['widgets']) AND is_array($_POST['widgets']))
    foreach ($_POST['widgets'] as $widget) {

        $name = $_POST['name_'.$widget];
        $content = array();
        foreach (explode(',', $_POST['fields_'.$widget]) as $field) {
            $content[$field] = $_POST['content_'.$widget.'_'.$field];
            // Compatibilite charset autre que utf8 ; en effet on recoit
            // obligatoirement les donnees en utf-8, par la magie d'ajax
            if ($GLOBALS['meta']['charset']!='utf-8') {
                include_spip('inc/charsets');
                $content[$field] = importer_charset($content[$field], 'utf-8');
            }
        }

        // Si les donnees POSTees ne correspondent pas a leur md5,
        // il faut les traiter
        if (md5(serialize($content)) <> $_POST['md5_'.$widget]) {
            if (!isset($_POST['secu_'.$widget]))
                $results[] = array($name, $content, $_POST['md5_'.$widget], $widget);

            elseif (verif_secu($name, $_POST['secu_'.$widget]))
                $results[] = array($name, $content, $_POST['md5_'.$widget], $widget);
            else
                return false; // erreur secu
        }
        // cas inchange
        else
            $results[] = array($name, $content, false, $widget);
    }

    return $results;
}


function action_widgets_store_dist() {

    include_spip('inc/widgets');
    header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

    $return = array('$erreur'=>'');

	$postees = post_widgets();
	$modifs = $updates = array();
	if (!is_array($postees)) {
	    $return['$erreur'] = _U('widgets:donnees_mal_formatees');
	} else {
	    include_spip('inc/autoriser');

	    foreach ($postees as $postee) {
	    	$name = $postee[0];
	    	$content = $postee[1];
	        if ($content && preg_match(_PREG_WIDGET, 'widget '.$name, $regs)) {
	            list(,$widget,$type,$modele,$id) = $regs;
	            $wid = $postee[3];
	            if (!autoriser('modifier', $type, $id, NULL, array('modele'=>$modele))) {
	                $return['$erreur'] =
	                    "$type $id: " . _U('widgets:non_autorise');
	                break;
	            }

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
	                        _U('widgets:modifie_par_ailleurs');
	                    }
	                break;
	            }
	            $modifs[] = array($type, $modele, $id, $content, $wid);
	        }
	    }
	}

	if (!$modifs AND !$return['$erreur']) {
	    $return['$erreur'] = _U('widgets:pas_de_modification');
	    $return['$annuler'] = true;
	}

	// une quelconque erreur ... ou rien ==> on ne fait rien !
	if ($return['$erreur']) {
	    echo var2js($return);
	    exit;
	}

	// sinon on bosse : toutes les modifs ont ete acceptees
	// vérifier qu'on a tout ce qu'il faut pour mettre a jour la base
	// et regrouper les mises à jour par type/id
	foreach ($modifs as $modif) {
		list($type, $modele, $id, $content, $wid) = $modif;
		if (!isset($updates[$type])) {
			// MODELE
			switch($type) {
				case 'article':
				    include_spip('action/editer_article');
				    $fun = 'revisions_articles';
				    break;
				case 'rubrique':
				    include_spip('action/editer_rubrique');
				    $fun = 'revisions_rubriques';
				    break;
				case 'breve':
				    include_spip('action/editer_breve');
				    $fun = 'revisions_breves';
				    break;
				default:
				    $return['$erreur'] = "$type: " . _U('widgets:non_implemente');
				    break 2;
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
	        $idschamps['fun']($id, $champsvaleurs['chval']);
	    }
	}

	// et maintenant refaire l'affichage des widgets modifies
	foreach ($modifs as $m) {
		list($type, $modele, $id, $content, $wid) = $modif;

	    // VUE
	    // chercher vues/article_toto.html
	    // sinon vues/toto.html
	    if (find_in_path( ($fond = 'vues/' . $type . '_' . $modele) . '.html')
	    OR find_in_path( ($fond = 'vues/' . $modele) .'.html')) {
	        $contexte = array(
	            'id_' . $type => $id,
	            'lang' => $GLOBALS['spip_lang']
	        );
	        include_spip('public/assembler');
	        $return[$wid] = recuperer_fond($fond, $contexte);
	    }
	    // vues par defaut
	    else {
	        // Par precaution on va rechercher la valeur
	        // dans la base de donnees (meme si a priori la valeur est
	        // ce qu'on vient d'envoyer, il y a nettoyage des caracteres et
	        // eventuellement d'autres filtres de saisie...)
	        $valeur = valeur_colonne_table($type, $modele, $id);

	        // seul spip core sait rendre les donnees
	        include_spip('inc/texte');
	        if (in_array($modele,
	        array('chapo', 'texte', 'descriptif', 'ps'))) {
	            $return[$wid] = propre($valeur);
	        } else {
	            $return[$wid] = typo($valeur);
	        }
	    }
	}

    echo var2js($return);
    exit;
}
?>
