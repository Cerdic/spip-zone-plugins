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


function traiter_les_widgets() {
$modifs = post_widgets();
$anamod = $anaupd = array();  # TODO: expliciter les noms de variables
if (!is_array($modifs)) {
    $return['$erreur'] = _U('widgets:donnees_mal_formatees');
} else {
    include_spip('inc/autoriser');

    foreach($modifs as $m) {
        if ($m[2] && preg_match(_PREG_WIDGET, 'widget '.$m[0], $regs)) {
            list(,$widget,$type,$champ,$id) = $regs;
            $wid = $m[3];
            if (!autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))) {
                $return['$erreur'] =
                    "$type $id: " . _U('widgets:non_autorise');
                break;
            }

            // alias temporaire pour titreurl, en attendant un modele
            if ($champ == 'titreurl') $champtable = 'titre';
            else $champtable = $champ;

            $md5 = md5(valeur_colonne_table($type, $champtable, $id));

            // est-ce que le champ a ete modifie dans la base ?
            if ($md5 != $m[2]) {
                // si oui, la modif demandee correspond peut-etre
                // a la nouvelle valeur ? dans ce cas on procede
                // comme si "pas de modification", sinon erreur
                if ($md5 != md5($m[1])) {
                    $return['$erreur'] = "$type $id $champtable: " .
                        _U('widgets:modifie_par_ailleurs');
                    }
                break;
            }
            $anamod[] = array($wid,$type,$champ,$id,$m[1]);
            if (!isset($anaupd[$type])) {
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
                $anaupd[$type] = array('fun'=>$fun, 'ids'=>array());
            }
            if (!isset($anaupd[$type]['ids'][$id])) {
                $anaupd[$type]['ids'][$id] = array('wdg'=>array(), 'chval'=>array());
            }
            // pour reaffecter le retour d'erreur sql au cas ou
            $anaupd[$type]['ids'][$id]['wdg'][] = $wid;
            $anaupd[$type]['ids'][$id]['chval'][$champtable] = $m[1];
        }
    }
}
if (!$anamod AND !$return['$erreur']) {
    $return['$erreur'] = _U('widgets:pas_de_modification');
    $return['$annuler'] = true;
}

// une quelconque erreur ... ou rien ==> on ne fait rien !
if ($return['$erreur']) {
    echo var2js($return);
    exit;
}

// sinon on bosse
foreach($anaupd as $type => $idschamps) {
    foreach($idschamps['ids'] as $id => $champsvaleurs) {

        // Enregistrer dans la base
        // $updok = ... quand on aura un retour
        // -- revisions_articles($id_article, $c) --
        $idschamps['fun']($id, $champsvaleurs['chval']);
    }
}
foreach($anamod as $m) {
    list($wid,$type,$champ,$id,$valeur) = $m;

    // VUE
    // chercher vues/article_toto.html
    // sinon vues/toto.html
    if (find_in_path( ($fond = 'vues/' . $type . '_' . $champ) . '.html')
        || find_in_path( ($fond = 'vues/' . $champ) .'.html') ) {
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

return $return;
}
?>
