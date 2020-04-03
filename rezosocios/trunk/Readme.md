# Plugin Rezosocios

> Gestion des liens de réseaux sociaux dans SPIP

## Fonctionnalités

Ce plugin permet de créer autant de liens de réseaux sociaux que l’on souhaite, et permet de les lier aux contenus.

Créez les liens via le menu `Édition → Réseaux sociaux`.

Choisissez les contenus sur lesquels activer les liaisons dans la configuration.

Autres fonctionnalités :

* Un squelette générique à inclure dans ses squelettes pour afficher une barre d’icônes avec les liens configurés : `inclure/rezosocios.html`
* Une noisette qui réutilise ce squelette
* Deux saisies pour choisir des liens ou des types de liens.
* Une entrée de menu qui permet d'avoir un ou plusieurs liens configurés.

## Différences et similarités avec le plugin [Sociaux](https://plugins.spip.net/sociaux.html)

Il y a 2 différences principales :

* On peut créer plusieurs liens pour un même type de réseau social
* On peut lier les réseaux aux contenus

L'apparence de la barre d'icônes est similaire entre les 2 plugins : c'est la même police de de caractère [Socicon](http://www.socicon.com/) qui est utilisée, et il s'agit du même composant CSS `.sociaux`, avec le même markup.

On peut donc passer de l’un à l’autre sans bouleverser l’affichage sur le site public.
La feuille de style surchargeable se trouve dans `css/rezosocios.css`.

Choisissez l’un ou l’autre plugin selon vos besoins.
