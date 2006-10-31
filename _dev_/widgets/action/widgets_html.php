<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function valeur_colonne_table($table, $col, $id) {
    $s = spip_query(
        'SELECT ' . $col .
          ' AS val FROM spip_' . $table .'s    WHERE id_' . $table . '=' . $id);
    if ($t = spip_fetch_array($s)) {
        return $t['val'];
    }
    return false;
}


/**
    * Transform a variable into its javascript equivalent (recursive)
    * @access private
    * @param mixed the variable
    * @return string js script | boolean false if error
    */
function var2js($var) {
    $asso = false;
    switch (true) {
        case is_null($var) :
            return 'null';
        case is_string($var) :
            return '"' . addcslashes($var, "\"\\\n\r") . '"';
        case is_bool($var) :
            return $var ? 'true' : 'false';
        case is_scalar($var) :
            return $var;
        case is_object( $var) :
            $var = get_object_vars($var);
            $asso = true;
        case is_array($var) :
            $keys = array_keys($var);
            $ikey = count($keys);
            while (!$asso && $ikey--) {
                $asso = $ikey !== $keys[$ikey];
            }
            $sep = '';
            if ($asso) {
                $ret = '{';
                foreach ($var as $key => $elt) {
                    $ret .= $sep . '"' . $key . '":' . var2js($elt);
                    $sep = ',';
                }
                return $ret ."}\n";
            } else {
                $ret = '[';
                foreach ($var as $elt) {
                    $ret .= $sep . var2js($elt);
                    $sep = ',';
                }
                return $ret ."]\n";
            }
    }
    return 'null';
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

function action_widgets_html_dist() {
    include_spip('inc/widgets');
    include_spip('inc/texte');
    include_spip('inc/rubriques');

    header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

    $autoriser_modifs= charger_fonction('autoriser_modifs', 'inc');
    $return = array('$erreur'=>'');

    // Est-ce qu'on a recu des donnees ?
    if (isset($_POST['widgets'])) {
        $modifs = post_widgets();
        $anamod = $anaupd = array();  # TODO: expliciter les noms de variables
        if (!is_array($modifs)) {
            $return['$erreur'] = _T('widgets:donnees_mal_formatees');
        } else {
            foreach($modifs as $m) {
                if ($m[2] && preg_match(_PREG_WIDGET, 'widget '.$m[0], $regs)) {
                    list(,$widget,$type,$champ,$id) = $regs;
                    $wid = $m[3];
                    if (!$autoriser_modifs($type, $champ, $id)) {
                        $return['$erreur'] =
                            "$type $id: " . _T('widgets:non_autorise');
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
                                _T('widgets:modifie_par_ailleurs');
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
                            "$type: " . _T('widgets:non_implemente');
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
            $return['$erreur'] = _T('widgets:pas_de_modification');
            $return['$annuler'] = true;
        }

        // une quelconque erreur ... ou rien ==> on ne fait rien !
        if ($return['$erreur']) {
            echo var2js($return);
            return;
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
                if (in_array($champ,
                array('chapo', 'texte', 'descriptif', 'ps'))) {
                    $return[$wid] = propre($valeur);
                } else {
                    $return[$wid] = typo($valeur);
                }
            }
        }
    }

    // CONTROLEUR
    // sinon on affiche le formulaire demande
    else if (preg_match(_PREG_WIDGET, $_GET['class'], $regs)) {
        list(,$widget,$type,$champ,$id) = $regs;
        if (!$autoriser_modifs($type, $champ, $id)) {
            $return['$erreur'] = "$type $id: " . _T('widgets:non_autorise');
        } else {
            $f = charger_fonction($type.'_'.$champ, 'controleurs', true)
            OR $f = charger_fonction($champ, 'controleurs', true)
            OR $f = 'controleur_dist';
            list($html,$status) = $f($regs);
            if ($status) {
                $return['$erreur'] = $html;
            } else {
                $return['$html'] = $html;
            }
        }
    } else {
        $return['$erreur'] = _T('widgets:donnees_mal_formatees');
    }

    echo var2js($return);
    exit;
}


function controleur_dist($regs) {
    list(,$widget,$type,$champ,$id) = $regs;

    // type du widget
    if (in_array($champ,
    array('chapo', 'texte', 'descriptif', 'ps')))
        $mode = 'texte';
    else
        $mode = 'ligne';

    // taille du widget
    $w = intval($_GET['w']);
    $h = intval($_GET['h']);
    $wh = intval($_GET['wh']); // window height
    if ($w<100) $w=100;
    if ($w>700) $w=700;
    if ($mode == 'texte') {
        if ($h<36) $h=36; #ici on pourrait mettre minimum 3*$_GET['em']
    }
    else // ligne, hauteur naturelle
        $h='';#$hx = htmlspecialchars($_GET['em']);

    // hauteur maxi d'un textarea -- pas assez ? trop ?
    $maxheight = min(max($wh-50,400), 700);
    if ($h>$maxheight) $h=$maxheight;

    $inputAttrs = array(
        'style' => "width:${w}px;" . ($h ? " height:${h}px;" : ''));

    $valeur = valeur_colonne_table($type, $champ, $id);
    if ($valeur !== false) {
        $n = new Widget($widget, $valeur);
        $widgetsAction = self();
        $widgetsCode = $n->code();
        $widgetsInput = $n->input($mode, $inputAttrs);
        $widgetsImgPath = dirname(find_in_path('images/cancel.png'));

        // title des boutons
        $OK = texte_backend(_T('bouton_enregistrer'));
        $Cancel = texte_backend(_L('Annuler'));
        $Editer = texte_backend(_L("&Eacute;diter $type $id"));
        $url_edit = "ecrire/?exec={$type}s_edit&amp;id_{$type}=$id";

        $html =
        <<<FIN_FORM

<form method="post" action="{$widgetsAction}">
  {$widgetsCode}
  {$widgetsInput}
  <div class="widget-boutons">
  <div style="position:absolute;">
    <a class="widget-submit" title="{$OK}">
      <img src="{$widgetsImgPath}/ok.png" width="20" height="20" />
    </a>
    <a class="widget-cancel" title="{$Cancel}">
      <img src="{$widgetsImgPath}/cancel.png" width="20" height="20" />
    </a>
    <a href="{$url_edit}" title="{$Editer}" class="widget-full">
      <img src="{$widgetsImgPath}/edit.png" width="20" height="20" />
    </a>
  </div>
</div>
</form>

FIN_FORM;
        $status = NULL;

    }
    else {
        $html = "$type $id $champ: " . _T('widgets:pas_de_valeur');
        $status = 6;
    }

    return array($html,$status);
}

?>
