# Multilang

Ce fichier sert de changelog, il liste les modifications lors du changement de version.

## Version 1.4.0 (2019-11-13)

- Ajout d'un pipeline `multilang_parametres` pour permettre aux plugins de changer les paramètres passés au script d'init. Concrètement cela permet d'ajouter des formulaires à prendre en compte par le script (entre autres).
- Ménage dans le script d'init afin qu'il soit plus lisible et maintenable : javascript et php sont séparés.
- Configuration : les formulaires sur lesquels activer le menu sont tous regroupés dans une même clé `formulaires`.
- Configuration : ajout d'une option qui permet d'ajouter des sélecteurs pour `root` (c.a.d d’autres formulaires).
- Le script n'est actif que s'il y a des éléments dans `root` (auparavant, le script s’activait sur *tous* les formulaires quand on déselectionnait tout dans la config par exemple : formulaire de recherche, de login, etc.).
- Menu de langues : boutons plus visibles, centrés, ajout d'un label, langues en toutes lettres, langues vides signalées par le symbole "∅" au lieu de changer la couleur de fond (ce qui pouvait faire croire à une entrée de menu active).

## Version 1.3.0 (2017-10-11)

- Compatible 3.2
- Correction pour avoir multilang sur les groupes de mots
- Syntaxe HTML du formulaire de configuration + passage en checkbox

### Version 1.2.2 (2017-05-23)

- Lorsque deux (ou plus) formulaires utilisant mutilang sont dans la même page, l'un des deux perdait la fonctionnalité de numéro de titre, exemple sur la page de rubrique ayant un document, le document perdait cette fonctionnalité.
- remplacer les `.find('li.editer_titre_numero,div.editer_titre_numero')` par `.find('.editer_titre_numero')` qui suffisent.
- Passer pour la forme la version de `schema` en 3 chiffres.

### Version 1.2.1

* ```z-index``` en mode flottant suffisant pour passer au dessus de leaflet sur GIS

### Version 1.2.0

* nettoyage de code (PSR)
* lors du switch de langue, appliquer la bonne direction sur les textarea et inputs impactés
* correction du sélecteur par défaut car on n'a plus ```#champ_geocoder``` dans les dernières versions de GIS

## Versions 1.1.x

### Version 1.1.5

* injecter un div si le formulaire est en div
* ajout d'un changelog