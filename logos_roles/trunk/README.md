
Logos par rôles
===============

Ce plugin augmente le plugin « Rôles de documents » en ré-implémentant l'API des logos en se servant des rôles.
Cela permet d'assurer une bonne rétro-compatibilité tout en permettant d'étendre le mécanisme des logos beaucoup plus facilement.

L'idée est de se baser sur les rôles de documents dont le nom commence par `logo` pour définir les types de logos disponibles.
Les rôles définis par le plugin « Rôles de documents » nous donnent les rôles habituels de spip : « logo » et « logo_survol », mais on peut aussi ajouter d'autres rôles de logos via la méthode décrite dans [La documentation du plugin Rôles](http://contrib.spip.net/Des-roles-sur-des-liens).

Pour simplifier les choses, une globale permet de configurer les rôles qui vont bien facilement :

    /* Rôles de documents pour les logos */
    $GLOBALS['roles_logos'] = array(
    	'logo_bandeau'  => array(
    		'label' => 'jp:role_logo_bandeau',
    		'objets' => array('articles', 'rubriques'),
    	),
    	'logo_extrait' => array(
    		'label' => 'jp:role_logo_extrait',
    		'objets' => array('articles', 'rubriques')
    	),
    	'logo_slideshow' => array(
    		'label' => 'jp:role_logo_slideshow',
    		'objets' => array('articles', 'rubriques'),
    	),
    );

Le plugin se charge de créer automagiquement les balises correspondant aux rôles défini pour les logos.

### Modification des boucles `DOCUMENTS` ###

Pour des raisons de rétro-compatibilité, les boucles `DOCUMENTS` ne montrent pas les logos.
Les logos n'apparaissent que si l'on utilise le critère `{role}` dans la boucle.


Surcharges du core
------------------

On surcharge plusieurs fichiers du core :

### Modification du formulaire `EDITER_LOGO` ###

Ce formulaire se comporte plus ou moins comme l'ancien, avec quelques améliorations :

- Il utilise les nouvelles APIs.
- On ajoute automatiquement des champs d'upload fonctionnels pour tous les rôles de logos définis.
- Permet d'éditer le document correspondant.
- Ajoute un pipeline qui permet d'ajouter des liens d'actions en-dessous des aperçus de logo : `logo_desc_actions`.
- TODO utiliser le plugin saisies pour construire le formulaire, avec à terme possibilité de compatibilité avec la saisie upload_html5

### Modification de `inc/chercher_logo.php` ###

La façon habituelle d'appeler cette fonction donne les résultats habituels, on garde une totale rétro-compatibilité.
Mais l'on peut aussi passer un rôle au paramètre `$mode`, et dans ce cas la fonction trouve un éventuel document associé à l'objet avec ce rôle.

S'il existe un logo enregistré avec l'ancienne API, on le retourne en priorité.

### Modification de `action/editer_logo.php` ###

Ici aussi, on essaie de garder une totale rétro-compatibilité, mais en permettant d'utiliser un rôle dans le troisième paramètre.

Les logos enregistrés avec l'ancienne API sont convertis à la nouvelle automatiquement.


Reste à faire
-------------

### Migration des logos existants ###

Comme les logos enregistrés avec l'ancienne API fonctionnent toujours avec la nouvelle, il n'y pas d'urgence à migrer, la cohabitation se fait bien.
En l'état actuel, on peut passer un logo enregistré avec l'ancienne API à la nouvelle API en le ré-uploadant dans le formulaire d'édition des logos.
Mais à terme il serait bien de migrer les logos historiques vers le système de rôles.

Comme ça représente potentiellement beaucoup de logos, il faut être prudent.
On pourrait se servir d'un cron qui le ferait petit à petit, et/ou une commande spip-cli ?
