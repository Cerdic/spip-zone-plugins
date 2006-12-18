<?php

	function bellespuces_pre_typo($texte) {
        $texte = preg_replace('/^-\s+/m','-* ',$texte);
        return $texte;
    }


?>