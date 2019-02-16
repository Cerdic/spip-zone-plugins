# Gridle

Ce plugin fournit la grille CSS [Gridle](http://gridle.org/).
En plus, déclare la grille au plugin [Noizetier Layout](https://zone.spip.net/trac/spip-zone/browser/_plugins_/noizetier_layout/trunk) et donc permet son utilisation dans les noisettes.

Les sources SCSS sont fournies, mais elles ne sont pas compatibles avec SCSSPHP (enfin plutôt l'inverse, c'est ce dernier qui est à la traine).
Elles ne sont là que pour le développement, à compiler au moyen de Gulp.

La 1ère fois : `npm install`
Puis : `gulp watch` pour surveiller les changements et lancer la compilation automatiquement, ou `gulp css` pour une compilation ponctuelle.
