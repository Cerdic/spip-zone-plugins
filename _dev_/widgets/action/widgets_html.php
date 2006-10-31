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
    return false;
}

function action_widgets_html_dist() {

    header("Content-Type: text/html; charset=".$GLOBALS['meta']['charset']);

    $autoriser_modifs= charger_fonction('autoriser_modifs', 'inc');
    $return = array('$erreur'=>'');

    // Est-ce qu'on a recu des donnees ?
    if (isset($_POST['widgets'])) {
        require_once include_spip('action/widgets_store', false);
    } else {
        // CONTROLEUR
        // sinon on affiche le formulaire demande
        require_once include_spip('inc/widgets', false);
    }
    echo var2js($return);
    exit;
}
?>
