Plugin Albums : todo
====================

Liste de choses à faire, notes et idées diverses, sans version particulière ciblée.

## Liste des albums liés

- Bouton pour transvaser les documents d'un album vers le portfolio.
- Bouton pour transvaser les documents du portfolio dans un nouvel album.
(portfolio = illustrations + portfolio + documents)

## Fichiers surchargés du plugin Médias
Plusieurs fichiers sont surchargés pour régler des problèmes javascripts, ou ajouter des éléments.
A terme il faudrait s'en passer.

- `prive/objets/editer/colonne_documents.html` :
ajout des albums et d'un mini menu pour basculer entre les documents et les albums.
- `formulaires/inc-upload_document.html` :
modifications afin de pouvoir afficher plusieurs fois le formulaire sur une même page.

## Fiche d'un album
Sur la fiche d'un album qu'on a le droit de modifier, les documents sont affichés via le portfolio classique qui les sépare en 3 groupes : «illustrations», «portfolio» et «documents». Mais pour un album, cette distinction n'a pas lieu d'être.
Il faudrait trouver un moyen d'utiliser notre squelette maison à la place du portfolio classique, comme on le fait déjà quand on a pas le droit de modifier l'album (cf. pipeline `afficher_complement_objet`). 
