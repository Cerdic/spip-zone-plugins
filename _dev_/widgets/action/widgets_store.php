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
        $content = $_POST['content_'.$widget];

        // Compatibilite charset autre que utf8 ; en effet on recoit
        // obligatoirement les donnees en utf-8, par la magie d'ajax
        if ($GLOBALS['meta']['charset']!='utf-8') {
            include_spip('inc/charsets');
            $content = importer_charset($content, 'utf-8');
        }

        // Si les donnees POSTees ne correspondent pas a leur md5,
        // il faut les traiter
        if (md5($content) <> $_POST['md5_'.$widget]) {

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

// les brutes viennent du POST
	$brutes = post_widgets();
// modifs et updates c'est ce qu'on en tirera
// modifs vérifie chaque changement
// updates est rangé par table/id
	$modifs = $updates = array();
	if (!is_array($brutes)) {
	    $return['$erreur'] = _U('widgets:donnees_mal_formatees');
	} else {
	    include_spip('inc/autoriser');

	    foreach($brutes as $brute) {
	        if ($brute[2] && preg_match(_PREG_WIDGET, 'widget '.$brute[0], $regs)) {
	            list(,$widget,$type,$champ,$id) = $regs;
	            $wid = $brute[3];
	            if (!autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))) {
	                $return['$erreur'] =
	                    "$type $id: " . _U('widgets:non_autorise');
	                break;
	            }

	            // champ est vue:champ éventuellement
	            if (count($champ = explode(':', $champ)) > 1) {
					$champvue = $champ[0];
	            	$champtable = $champ[1];
	            } else {
					$champvue = '';
					$champtable = $champ[0];
	            }

	            $md5 = md5(valeur_colonne_table($type, $champtable, $id));

	            // est-ce que le champ a ete modifie dans la base ?
	            if ($md5 != $brute[2]) {
	                // si oui, la modif demandee correspond peut-etre
	                // a la nouvelle valeur ? dans ce cas on procede
	                // comme si "pas de modification", sinon erreur
	                if ($md5 != md5($brute[1])) {
	                    $return['$erreur'] = "$type $id $champtable: " .
	                        _U('widgets:modifie_par_ailleurs');
	                    }
	                break;
	            }
	            $modifs[] = array($wid,$type,array($champtable, $champvue),$id,$brute[1]);
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
	                    default :
	                $return['$erreur'] =
	                    "$type: " . _U('widgets:non_implemente');
	                break 2;
	                }
	                $updates[$type] = array('fun'=>$fun, 'ids'=>array());
	            }
	            if (!isset($updates[$type]['ids'][$id])) {
	                $updates[$type]['ids'][$id] = array('wdg'=>array(), 'chval'=>array());
	            }
	            // pour reaffecter le retour d'erreur sql au cas ou
	            $updates[$type]['ids'][$id]['wdg'][] = $wid;
	            $updates[$type]['ids'][$id]['chval'][$champtable] = $brute[1];
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

	// sinon on bosse
	foreach($updates as $type => $idschamps) {
	    foreach($idschamps['ids'] as $id => $champsvaleurs) {

	        // Enregistrer dans la base
	        // $updok = ... quand on aura un retour
	        // -- revisions_articles($id_article, $c) --
	        $idschamps['fun']($id, $champsvaleurs['chval']);
	    }
	}
	foreach($modifs as $modif) {
	    list($wid,$type,$champ,$id,$valeur) = $modif;

	    // VUE
	    // chercher vues/article_toto.html
	    // sinon vues/toto.html
	    if ((count($champ) > 1 &&
		        find_in_path($fond = 'vues/' . $champ[1] .'.html')) ||
		    (($champ = $champ[0]) &&
	        (find_in_path( ($fond = 'vues/' . $type . '_' . $champ) . '.html')
	        || find_in_path( ($fond = 'vues/' . $champ) .'.html') ))) {
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
	        $valeur = valeur_colonne_table($type, $champ, $id);

	        // seul spip core sait rendre les donnees
	        include_spip('inc/texte');
	        if (in_array($champ,
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
