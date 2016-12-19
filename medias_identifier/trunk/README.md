# Plugin Image identifier

En tant que développeur, nous ne pouvons nous assurer à 100% de ce que font :

* les utilisateurs téléversant des documents;
* les autres serveurs servant des images distantes;

Il se peut qu'il y ai des erreurs dans les extensions de fichiers qui entraînent potentiellement de gros problèmes (impossibilité d'afficher des images, pages blanches…) car le contenu du fichier ne correspond pas à sont extension ou au type mime envoyé par le serveur.

Ces problèmes arrivent principalement en utilisant GD2 qui plante automatiquement dans ce genre de cas.

Si vous utilisez `php-imagick` ou `convert` comme méthode de fabrication automatique d'images, vous ne verrez certainement pas ces problèmes, le seul inconvénient est de conserver un fichier mal nommé dans un mauvais répertoire, pouvant dans le futur, causer à nouveau le même genre de problème en cas de changement de configuration.

Ce problème a été soulevé par ce [ticket sur GitHub->https://github.com/seenthis/seenthis_squelettes/issues/174] concernant le [projet seenthis](http://www.seenthis.net)

## Changelog

### Versions 0.x.x

#### Version 0.1.0

Version fonctionnelle si [php-imagick](http://php.net/manual/fr/book.imagick.php) est installé sur le serveur.

* Si une image distante dont l'extension ne correspond pas à la réalité du fichier est insérée en base, elle est renommée dans le bon répertoire dans `IMG/distant/ext/…`
* Si une image uploadée par un utilisateur (non distante) dont l'extension ne correspond pas à la réalité du fichier est insérée en base, elle est renommée dans le bon répertoire dans `IMG/ext/…`
* Si une fonction image est appelée soit sur une image distante directement, soit sur une image distante rendue "locale" via `copie_locale()`, vérifier que le format correspond bien à l'extension et utiliser les bonne fonctions de création.

## Todo

* [ ] Faire fonctionner ce plugin si php-imagick n'est pas installé mais convert accessible
* [ ] Faire une alerte dans le privé si ni php-imagick, ni convert sont accessibles
* [ ] Trouver une icône
* [ ] Internationalisation + paquet.xml
* [ ] Intégrer dans le core en 3.2.x ?

