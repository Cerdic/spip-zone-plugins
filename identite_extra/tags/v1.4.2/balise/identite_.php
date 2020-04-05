<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_IDENTITE_($p) {
    $cfg = identite_extra_champs();

    $nom = $p->nom_champ;
    $champ = substr(strtolower($nom),9);

    if ($nom === 'IDENTITE_') {
        $msg = array('zbug_balise_sans_argument', array('balise' => ' IDENTITE_'));
        erreur_squelette($msg, $p);
        $p->interdire_scripts = false;
        
        return $p;
    } elseif (!in_array($champ, $cfg)) {
        $msg = array('zbug_balise_inexistante', array('balise' => $nom, 'from' => 'identite_extra'));
        erreur_squelette($msg, $p);
        $p->interdire_scripts = false;
        
        return $p;
    } else {
        $p->code = 'lire_config("identite_extra/' . $champ .'")';
        $p->interdire_scripts = false;
        
        return $p;
    }
}
