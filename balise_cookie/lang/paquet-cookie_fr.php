<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cookie_description' => 'Fourni les balises #COOKIE et #COOKIE_SET
Utilisation:
<code>#COOKIE{truc}</code> retourne la valeur du cookie "spip_truc"  (ou du cookie "truc" si celui "spip_truc" n\'existe pas).
/!\ Pour éviter les problèmes de partage des valeurs de cookie via le cache, cette balise ne fonctionne que pour les visiteurs authentifiés

<code>#COOKIE_SET{truc,ma_valeur,ma_duree}</code> permet de créer le cookie "spip_truc" avec la valeur "ma_valeur" et la durée "ma_duree" (en secondes) avant expiration. 
Seul le premier paramètre (le nom du cookie)  est obligatoire:
<code>#COOKIE_SET{truc,ma_valeur}</code> crée le cookie "spip_truc" pour la durée de la session uniquement
<code>#COOKIE_SET{truc}</code> supprime le cookie "spip_truc".

NB 1: cette balise gère le préfixe des cookie SPIP sans qu\'il soit nécessaire de le passer dans le nom du cookie (premier paramètre)

NB 2: toujours afin de la rendre plus conviviale, contrairement à la fonction spip_setcookie(), cette balise prend une durée en seconde comme second paramètre et non une date d\'expiration sous forme de timestamp.

NB 3: #COOKIE_SET étant "branchée" sur la fonction spip_setcookie(), comme elle, cette balise gère les paramètres supplémentaires utilisés par la fonction setcookie de PHP (chemin, domaine, secure). L\'écriture complète de la balise est donc:
<code>#COOKIE_SET{nom_cookie, ma_valeur, ma_duree, chemin, domaine, secure}</code>',
	'cookie_nom' => 'balise_cookie',
	'cookie_slogan' => 'fournir les balises #COOKIE et #COOKIE_SET',
);

?>
