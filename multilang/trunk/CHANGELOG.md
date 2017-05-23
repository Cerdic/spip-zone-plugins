# Multilang

Ce fichier sert de changelog, il liste les modifications lors du changement de version.

## Version 1.2.x

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