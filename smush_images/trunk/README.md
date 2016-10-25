# Smush - Plugin pour SPIP

Ce plugin permet d'optimiser les images générées par SPIP.

Ce plugin a besoin de plusieurs binaires sur le serveur afin de fonctionner correctement.

## Binaires nécessaires

### [ImageMagick](http://www.imagemagick.org/) (obligatoire)

Permet d'utiliser les fonctions `identify` (permettant de connaître le type de fichier) et `convert` (qui transforme les GIFs en PNGs).

Pour l'installer, sur Debian et dérivés :

`apt-get install imagemagick`

### [Pngnq](http://pngnq.sourceforge.net/) (obligatoire)

Permet d'optimiser les fichiers PNGs.

Pour l'installer, sur Debian et dérivés :

`apt-get install pngnq`

### [OptiPNG](http://optipng.sourceforge.net/) (obligatoire)

Permet d'optimiser les fichiers PNGs.

Pour l'installer, sur Debian et dérivés :

`apt-get install optipng`

### [jpegtran](http://jpegclub.org/jpegtran/) (obligatoire)

Permet d'optimiser les fichiers JPEG (enlève principalement les métas).

Si l'image est supérieure à 10ko, elle est rendue "progressive".

Pour l'installer, sur Debian et dérivés : 

`apt-get install libjpeg-turbo-progs`

### [Gifsicle](https://www.lcdf.org/gifsicle/) (obligatoire)

Permet d'optimiser les Gifs animés.

Pour l'installer, sur Debian et dérivés : 

`apt-get install gifsicle`

### [Jpegoptim](https://github.com/tjko/jpegoptim) (facultatif)

Permet d'optimiser les JPEG en modfiant la qualité finale.

Si ce binaire est installé, un champ dans le formulaire de configuration du plugin permet de stipuler la qualité finale souhaitée.

Pour l'installer, sur Debian et dérivés : 

`apt-get install jpegoptim`

## Fonctionnement

Par défaut, toute image passant par les filtres d'images de SPIP (compression, redimensionnement…) passe par le filtre `inc_smush_image_dist` du fichier `inc/smush_image`.

Le format de l'image est alors identifier via la commande `identify`.

L'image est transformée en PNG si elle est un GIF (simple, pas animé) via la commande `convert`.

Puis elle est optimisée au maximum.

## CHANGELOG

### Versions 0.6.x

#### Version 0.6.3 (2016-10-25)

Bien tester l'existence des fichiers avant de tester leur taille (notices PHP).

Ce plugin est pour SPIP > 3.0 et donc nécessite PHP > 5.2 normalement, la compatibilité pour vieux PHP (absence de `json_decode()` et `json_encode()`) n'est donc plus nécessaire.

#### Version 0.6.2 (2016-10-23)

Si une copie en jpg d'un png peut être mieux optimisée ne pas tester la taille d'un fichier n'existant pas, l'image d'origine devient la copie jpg.

#### Version 0.6.1

Dans certains cas une copie en jpg d'un png pourrait être mieux optimisée (dans les cas où il n'y a pas de canal Alpha bien sûr)

On test et on compare, si le jpg est plus intéressant, on le conserve.

#### Version 0.6.0

* Garder dans les metas si un logiciel est ok ou cassé (valeur `oui` si cassé, `non` si fonctionnel)
* Intégration de Jpegoptim qui permet de compresser avec perte les images de type JPEG (85% est un bon ratio pour équilibrer qualité et poids).

### Versions 0.5.x

#### Version 0.5.3

Version originale du plugin avant ce fichier README.md

Version fonctionnelle.