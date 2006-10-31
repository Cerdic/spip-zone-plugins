<?php

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
