<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function action_installer_vocabulaire_dist($arg = null) {
    if (is_null($arg)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }

    include_spip('inc/minipres');
    echo ( install_debut_html('Mise à jour'));

    list($fichier, $pointer) = explode('|', $arg);

    $fichier_open = find_in_path($fichier);
    // On va ce servir des fonctions des gestions des fichiers
    // Elle consomme beaucoup moins de mémoire que les autres.
    $open = fopen($fichier_open, 'r');
    for ($i = 0; $i < _VOCABULAIRE_CADENCE; $i++) {
        // On ce positionne sur le pointer courant
        fseek($open, $pointer);
        // On lit la ligne
        $mot = utf8_encode(trim(fgets($open)));
		if (!empty($mot)) {
			// On récupère le pointer
			$pointer = ftell($open);
			echo 'Insertion de : '.$mot.'<br />';
			// On enregistre le mots dans la base de données
			sql_insertq('spip_vocabulaires', array('mot' => $mot));
		}
	}

    // On est à la fin du fichier ?
    $eof = feof($open);

    // Fermer le fichier
    fclose($open);

    // Maintenant on va rediriger sur l'action avec le pointeur en cour.
    // Cela évitera a PHP ce timeout.
    if (!$eof) {
        $redirect = generer_action_auteur('installer_vocabulaire', $fichier.'|'.$pointer, _request('redirect'), true);
        echo http_script('location.href="'.$redirect.'";');
    } else {
        include_spip('inc/headers');
        echo "Fin de l'installation";
		$redirect = _request('redirect');
		echo http_script('location.href="'.$redirect.'";');
    }

    echo install_fin_html();
    // forcer l'envoi du buffer par tous les moyens !
    echo(str_repeat("<br />\r\n", 256));
    while (@ob_get_level()) {
        @ob_flush();
        @flush();
        @ob_end_flush();
    }
}
