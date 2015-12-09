<?php
/**
 * Fonction du plugin Bolo
 *
 * @plugin     Bolo
 * @copyright  2010
 * @author     Cyril MARION - Ateliers CYM
 * @licence    GPL
 * @package    SPIP\Bolo\Fonctions
 */

/**
 * Compile la balise `#BOLO` chargée d'afficher du faux texte.
 *
 * - Le 1er argument est un chiffre qui indique la taille de coupe du texte au moyen du filtre du même nom.
 *   Si on met autre chose qu'un nombre, le paramètre est ignoré (pas de coupe).
 * - Le 2ème argument permet de définir les points de suite, par défaut `&nbsp;(...)`.
 *   Quand on veut l'ignorer, on peut utiliser la chaîne `null` (cf. 3ème exemple plus bas).
 * - Le 3ème argument permet de définir le type de texte utilisé (cf. bolo/{type}.php).
 *
 * @balise
 * @uses couper()
 * @example
 *    ```
 *    [(#BOLO{300})]              // coupe à 300 avec les points de suite par défaut
 *    [(#BOLO{300,''})]           // coupe à 300 sans point de suite.
 *    [(#BOLO{500,null,gangsta})] // coupe à 500 avec les points de suite par défaut, et utilise le texte «gangsta».
 *    ```
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @param string $_couper
 *     Taille de la coupe du texte
 * @param string $_suite
 *     Points de suite ajoutés quand le texte est coupé
 *     par défaut `&nbsp;(...)`
 *     On peut utiliser la chaîne 'null' pour ignorer et utiliser la valeur par défaut.
 * @param string $_type
 *     Type de texte à utiliser.
 *     par défaut `latin`
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_BOLO($p) {

	// longueur du texte
	$_couper = 'null';
	if (($v = interprete_argument_balise(1,$p)) !== null) {
		$_couper = '(intval('.$v.') > 0) ? intval('.$v.') : null';
	}
	// points de suite
	$_suite = "'&nbsp;(...)'";
	if (($v2 = interprete_argument_balise(2,$p)) !== null) {
		$_suite = '('.$v2.' !== \'null\') ? '.$v2.' : '.$_suite;
	}
	// type de texte utilisé, cf. bolo/{type}.php
	$_type = "'latin'";
	if (($v3 = interprete_argument_balise(3,$p)) !== null) {
		$_type = $v3;
	}

	$p->code = "((\$bolo = charger_fonction($_type, 'bolo', true)) ? ($_couper ? couper(\$bolo(), $_couper, $_suite) : \$bolo()) : '')";
	$p->interdire_scripts = false;

	return $p;
}


/**
 * Décale le contenu d'un texte.
 *
 * On veut éviter que les faux textes commencent toujours par la même chose.
 * Ce filtre permet de varier le contenu du texte en "décalant" les mots.
 * Par exemple, si on décale tous les mots de 5 positions vers la gauche,
 * le 5ème mot va se retrouver au début, et tous les mots précédents seront collés à la fin.
 *
 * @example
 * Le texte suivant :
 * « Longtemps, je me suis levé de bonne heure »
 * donnerait ceci avec un décalage de 3 :
 * « suis levé de bonne heure. Longtemps, je me »
 *
 * @param string $texte
 *     Texte à traiter
 * @param int $decalage
 *     Nombre de mots à décaler, aléatoire par défaut.
 * @return string
 *     Texte avec les mots décalé
 */
function filtre_decaler_texte_dist($texte, $decalage='') {

	// on place tous les mots dans un tableau associatif.
	// (position numérique du mot => mot)
	$mots = str_word_count($texte, 2);

	// on récupère la position du mot de départ.
	// a) soit le numéro du mot est donné...
	if (intval($decalage)) {
		// on pondère l'decalage
		$decalage = ($decalage > count($mots)) ? $decalage % count($mots) : $decalage;
		// on récupère la position du mot correspondant
		$v = array_reverse($mots)[$decalage];
		$position = array_search($v,$mots);
	}
	// b) ...soit on choisit une position au hasard.
	else {
		$position = array_rand($mots);
	}

	// on coupe le texte en 2.
	$debut = substr($texte, 0, $position);
	$fin = substr($texte, $position);

	// on recolle tout ça.
	$texte = $fin.' '.$debut;

	return $texte;
}
