<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'arp_description' => 'Ce plugin vient en supplément du plugin Accès Restreint. Ce dernier permet d\'empêcher les accès aux rubriques/articles appartenant à une zone définie comme étant à accès restreint. Les rubriques/articles sont alors totalement invisibles aux visiteurs n\'ayant pas les droits d\'accès.

ARP autorise l\'accès ; les rubriques/articles sortent dans les résultats des boucles. Mais le contenu des #TEXTE est filtré de telle sorte de ne laisser voir qu\'une partie.

- Le rédacteur peut choisir l\endroit précis de la coupe du texte, en ajoutant un tag <couper_ici> dans le texte à l\'emplacement désiré.
- On peut choisir la règle de filtrage à appliquer en attribuant un mot-clé à l\'article : arp_regle_1, arp_regle_(N)
Les règles étant définies dans la configuration de l\'article.
- Enfin, si aucune des possibilités ci-dessus n\'est utilisée, la règle de filtrage par défaut est appliquée. Elle est définie dans la conf. du plugin.

Il est possible d\'ajouter du texte avant et après le texte filtré, par exemple pour renvoyer le visiteur vers une page d\'abonnement.

Le plugin met à disposition quelques modèles, à insérer par exemple dans le texte avant ou après.
- <liste_doc> pour lister les documents présents dans l\'article,
- <liste_intertitre> pour listes les intertitres.
',
	'arp_nom' => 'Accès Restreint Partiel',
	'arp_slogan' => 'Restreindre un peu mais pas tout',
);

?>