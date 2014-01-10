<?php


/**
 * Compile la balise `#IMAGE_PAR_DEFAUT` qui crée une image de la taille
 * indiquée, en écrivant, dessus, sa taille.
 *
 * Paramètres :
 * - taille x (pixels)
 * - taille y (pixels)
 * - texte (défaut vide)
 * - couleur fond (défaut gris clair)
 * - couleur texte (défaut gris foncé)
 *
 * @example
 *     ```
 *     #IMAGE_PAR_DEFAUT{960,120}
 *     ```
 *
 * @param Balise $p
 * @return Balise
 **/
function balise_IMAGE_PAR_DEFAUT_dist($p) {

    $_taille_x = interprete_argument_balise(1, $p);
    $_taille_y = interprete_argument_balise(2, $p);

    if ($_taille_x AND $_taille_y) {
        $_texte         = interprete_argument_balise(3, $p);
        $_couleur_fond  = interprete_argument_balise(4, $p);
        $_couleur_texte = interprete_argument_balise(5, $p);
        $_options =
            ($_texte ? ", $_texte" : '')
                . ($_couleur_fond ? ", $_couleur_fond" : '')
                . ($_couleur_texte ? ", $_couleur_texte" : '');

        $p->code = "image_par_defaut($_taille_x, $_taille_y$_options)";
    }

    return $p;
}

/**
 * Crée une image de la taille indiquée, en écrivant, dessus, sa taille
 * et retourne son code HTML
 *
 * @param int $x Taille x en pixels
 * @param int $y Taille y en pixels
 * @param string $description Texte d'accompagnement
 * @param string $couleur_fond Couleur de fond
 * @param string $couleur_texte Couleur du texte
 * @return string Code HTML
 **/
function image_par_defaut($x, $y, $description='', $couleur_fond='#cccccc', $couleur_texte='#444444') {
    include_spip('inc/filtres');

    if ($description) {
        $texte =  $description . ' // ';
    }
    $texte .= "$x x $y";

    $image = filtrer('image_typo', $texte, "couleur=" . ltrim($couleur_texte, '#'));
    $image = filtrer('image_aplatir', $image, 'png', ltrim($couleur_fond, '#'));
    $image = filtrer('image_recadre', $image, $x, $y, 'center', $couleur_fond);
    $image = filtrer('image_graver', $image);
    return $image;
}
