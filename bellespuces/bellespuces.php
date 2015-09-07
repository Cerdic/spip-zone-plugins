<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function bellespuces_pre_typo($texte) {
        $texte = preg_replace('/^-\s?+/m','-* ',$texte);
        return $texte;
}


?>