Foundation-4-spip
=================

Ce plugin active différente joyeuseté pour SPIP:

Le framework [foundation](http://foundation.zurb.com/) et tout ces plugins.

Vous pouvez choisir entre foundation 3 et foundation 4. Ou installer une autre version (Qu'il faudra intégrer au plugin).

# Filtre ajouté par le plugin:

* |iframe_responsive, détecte les iframes dans une chaine de caractère et ajoute le markup HTML pour les rendres responsive.


# ToDo

* Activer les javascripts de foundation séparément plutôt que de charger l'intégralité des scripts.
* Utiliser la classe flex-video de foundation et adapter les paramètres en fonction de la vidéo (classe widescreen et Vimeo).

# Version

## Foundation-4-SPIP 1.4.1

* Finalement les dépendances casse tout, on va retirer les dépendances et laisser le choix au dev de faire avec ce qu'il veux

## Foundation-4-SPIP 1.4

* Mise à jour de foundation 4.3.1 => 4.3.2
* Changement dans les dépendances, on passe à Zcore et Z5.
* Utiliser le responsive de foundation pour les iFrames et détecter vimeo pour ajouter la bonne class.

## Foundation-4-SPIP 1.3.9

* Mise à jour de foundation. (4.3.0 => 4.3.1) 
* Signaler que le fichier htc est expérimental. 
* Version 1.3.9, et passer en stable. 

## Foundation-4-SPIP 1.3.7

* Activer Foundation 4 à l'installation de foundation.

## Foundation-4-SPIP 1.3.6

* Fixe pour les taille équivalente entre input.button et a.buttonOn utilise @-moz-document url-prefix() pour que le hack css des boutons ne cible que firefox.

## Foundation-4-SPIP 1.3.5

* Fixe pour les taille équivalente entre input.button et a.button

## Foundation-4-SPIP 1.3.4

* Mise à jour de foundation en version 4.3.0

## Foundation-4-SPIP 1.3.2

* Ajoute une class langue sur la balise html.


## Foundation-4-SPIP 1.3.1

* Retour de la dépendance à Zpip vu qu'on ce base sur structure.html
* Mise à jour de foundation 4 (4.2.2 => 4.2.3)

## Foundation-4-SPIP 1.3.0

* Ajoute le filtre |iframe_responsive.

## Foundation-4-SPIP 1.2.0

* Ajoute la possibilité de désactiver le fichier.
* Mise à jour du fichier de langue.

## Foundation-4-SPIP 1.1.1

* Supprime les dépendances inutiles.

## Foundation-4-SPIP 1.1.0

* Ajoute la possibilité de désactiver le javascript de foundation.

## Foundation-4-SPIP 1.0.0

* Permière version publique.
