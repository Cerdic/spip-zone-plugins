
Logos par rôles
===============

Ce plugin modifie le système de logos de SPIP pour le rendre plus flexible et permettre de donner plus de contrôle aux rédacteurs.

Un problème récurent avec les logos de SPIP, c'est qu'on veut les afficher à des endroits différents du site, en général dans plusieurs format différents.
On peut par exemple afficher le logo d'un article sous la forme d'un petit carré dans les listes d'articles, mais aussi en grand format sur la page d'accueil.
Les rédacteurs doivent alors trouver des images qui fonctionnent dans les deux formats, ce qui s'avère souvent impossible.

On peut alors détourner les logos de survol, mais c'est vite limité.
Parce que ça n'offre qu'un seul logo alternatif par objet éditorial, mais aussi parce que les rédacteurs ne voient pas le logo dans le bon format dans l'espace privé, ce qui oblige à des allers-retours.

En utilisant ce plugin, on peut définir autant de types de logos qu'on le souhaite, qui peuvent alors être gérés indépendamment par les rédacteurs.
On pourra alors utiliser des images différentes pour la page d'accueil et pour les listes.

Le plugin [Massicot](https://contrib.spip.net/Massicot) complète très bien ce plugin, et permet alors de définir des recadrages différents pour les différents types de logos.
On peut aussi utiliser des formats prédéfinis pour le recadrage.


Fonctionnement
--------------

Ce plugin s'appuie sur le plugin « Rôles de documents », et ré-implémente l'API des logos en se servant des rôles.
Cela permet d'assurer une bonne rétro-compatibilité tout en permettant d'étendre le mécanisme des logos beaucoup plus facilement.

L'idée est de se baser sur les rôles de documents dont le nom commence par `logo` pour définir les types de logos disponibles.
Les rôles définis par le plugin « Rôles de documents » nous donnent les rôles habituels de spip : `logo` et `logo_survol`, mais on peut aussi ajouter d'autres rôles de logos via la méthode décrite dans [la documentation du plugin Rôles](https://contrib.spip.net/Des-roles-sur-des-liens).

Pour simplifier les choses, on propose d'ajouter les nouveaux types de logos avec le pipeline `roles_logos` :


	function prefix_plugin_roles_logos($logos) {

		$logos['logo_bandeau'] = array(
			'label' => 'Bandeau du site',
			'objets' => array('site'),
			'dimensions' => array(
				'largeur' => 1200,
				'hauteur' => 300,
			),
		);

		$logos['logo_extrait'] = array(
			'label' => 'Extraits pour les listes',
			'objets' => array('articles', 'rubriques'),
			'defaut' => 'img/logo-extrait.png',
			'dimensions' => array(
				'largeur' => 800,
				'hauteur' => 500,
			),
		);

		$logos['logo_slideshow'] = array(
			'label' => 'Slideshow page d\'accueil',
			'objets' => array('articles', 'rubriques'),
		);

		return $logos;
	}


En se basant sur cette liste de rôles, le plugin se charge automatiquement de :

- Déclarer les rôles de documents qui correspondent, en les liant aux bons objets.
- Surcharger le formulaire d'édition des logos, pour permettre de gérer les différents types de logos.
- Créer les balises pour afficher ces logos dans les squelettes. Avec l'exemple ci-dessus, on pourra alors appeler la balise `#LOGO_ARTICLE_EXTRAIT`, qui reverra le bon logo.

### Paramètres des rôles de logo ###

Les logos que l'on définit dans le pipeline `logos_roles` nécessitent au moins deux paramètres :

- __label :__ Le nom du type de logo tel qu'il doit s'afficher dans l'espace privé. Peut être une chaîne de langue.
- __objets :__ Une liste des types d'objets pour lesquels ce type de logo doit être actif.

D'autres paramètres sont optionnels :
- __dimensions :__ Ce paramètre permet de forcer les dimensions d'un logo, la balise `#LOGO_` correspondante recadre alors automatiquement le logo. Doit être un tableau avec les clés `largeur` et `hauteur`. Cette fonction est particulièrement utile quand on utilise le plugin massicot, qui propose alors directement le bon format pour chaque type de logo.
- __defaut :__ Permet de spécifier un logo qui sera affiché par défaut, qu'on ira alors chercher dans le chemin de SPIP.

### Modification des boucles `DOCUMENTS` ###

Pour des raisons de rétro-compatibilité, les boucles `DOCUMENTS` ne montrent pas les logos.
Les logos n'apparaissent que si l'on utilise le critère `{role}` dans la boucle.

### Migration des logos existants ###

Comme les logos enregistrés avec l'ancienne API fonctionnent toujours avec la nouvelle, il n'y pas d'urgence à migrer, la cohabitation se fait bien.

On peut passer un logo enregistré à la racine d'IMG à la nouvelle API en le ré-uploadant dans le formulaire d'édition des logos.

Le formulaire de configuration du plugin propose également de migrer les logos en masse.


Surcharges du core
------------------

On surcharge plusieurs fichiers du core :

### Modification du formulaire `EDITER_LOGO` ###

Ce formulaire se comporte plus ou moins comme l'ancien, avec quelques améliorations :

- Il utilise les nouvelles APIs.
- On ajoute automatiquement des champs d'upload fonctionnels pour tous les rôles de logos définis.
- Permet d'éditer le document correspondant.
- Ajoute un pipeline qui permet d'ajouter des liens d'actions en-dessous des aperçus de logo : `logo_desc_actions`.

### Modification de `inc/chercher_logo.php` ###

La façon habituelle d'appeler cette fonction donne les résultats habituels, on garde une totale rétro-compatibilité.
Mais l'on peut aussi passer un rôle au paramètre `$mode`, et dans ce cas la fonction trouve un éventuel document associé à l'objet avec ce rôle.

S'il existe un logo enregistré avec l'ancienne API, on le retourne en priorité.

### Modification de `action/editer_logo.php` ###

Ici aussi, on essaie de garder une totale rétro-compatibilité, mais en permettant d'utiliser un rôle dans le troisième paramètre.

Les logos enregistrés avec l'ancienne API sont convertis à la nouvelle automatiquement.

### Surcharge de la balise `{logo}`

On surcharge cette balise pour qu'elle se serve de l'API chercher_logo pour trouver quels objets ont des logos, au lieu de chercher dans le dossier IMG/.
