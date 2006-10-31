<?php


// Recuperation des donnees
#var_dump($_POST);

#$meta['alea_ephemere'] = $meta['alea_ephemere_ancien'] = 'x';


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

function verif_secu($w, $secu) {
    return (
        $secu == md5($GLOBALS['meta']['alea_ephemere'].'='.$w)
    OR
        $secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].'='.$w)
    );
}


// Definition des widgets
class Widget {
    var $name;
    var $text;
    var $key;
    var $md5;
    var $clean;

    function Widget($name, $text='') {
        $this->name = $name;
        $this->text = $text;
        $this->key = strtr(uniqid('wid', true), '.', '_');
    }

    function md5() {
        return md5($this->text);
    }

    function code() {
        return
        '<input type="hidden" class="widget-id" name="widgets[]" value="'.$this->key.'" />'."\n"
        . '<input type="hidden" name="name_'.$this->key.'" value="'.$this->name.'" />'."\n"
        . '<input type="hidden" name="md5_'.$this->key
        .'" value="'.$this->md5().'" />'."\n";
    }

    function input($type = 'ligne', $attrs = array()) {
        switch ($type) {
            case 'texte':
                $return = '<textarea class="widget-active"'
                . ' name="content_'.$this->key.'">'
                . entites_html($this->text)
                . '</textarea>'."\n";
                break;
            case 'ligne':
            default:
                $return = '<input class="widget-active" type="text"'
                . ' name="content_'.$this->key.'"'
                . ' value="'
                . entites_html($this->text)
                . '" />'."\n";
        }
        foreach ($attrs as $attr=>$val) {
            $return = inserer_attribut($return, $attr, $val);
        }
        return $return;
    }
}

//
// Un Widget avec une verification de code de securite
//
class SecureWidget extends Widget {

    function SecureWidget($name, $text='') {
        parent::Widget($name, $text);
    }

    function code() {
        $code = parent::code();
        $secu = md5($GLOBALS['meta']['alea_ephemere']. '=' . $this->name);

        return
            $code
            .'<input type="hidden" name="secu_'.$this->key.'" value="'.$secu.'" />'."\n";
    }
}

?>
