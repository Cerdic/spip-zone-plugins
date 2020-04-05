# Gérer des options sur des produits


## v1.3.2 ##

Les options peuvent être associées à tous les objets éditoriaux, pas uniquement aux produits.
L'association se fait dans la configuration du plugin (liste des objets à cocher).
Les plugins produits et prix deviennent donc optionnels.
Pour gérer des options sur un objet patate, on pourra par exemple ajouter un champ prix (et taxe) dans la définition de l'objet patate.

Un formulaire générique permet d'ajouter un objet et ses options au panier.
Exemple : #FORMULAIRE_PANIER_OPTIONS{patate, #ID_PATATE} à utiliser dans une <boucle_(PATATES)>

 
## v1.0.0 ##

Le plugin gère des groupes d'options, dans lesquels on crée des options.

Chaque option a un prix HT par défaut (positif ou négatif), qui s'ajoute au prix de base du produit.

Sur chaque produit, on choisit quelles options on lui affecte.  
On peut aussi modifier le prix de l'option sur le produit, pour avoir un prix différent du prix par défaut de l'option.

Côté public, les options sont proposées sous forme de boutons radio, classées par groupes.  
Le prix HT des options est ajouté au prix HT du produit, et la TVA s'applique donc sur le total.  
Un script JS mets à jour visuellement le prix TTC du produit en fonction des options choisies.

Les options sont transmises aux paniers puis aux commandes.  
La surcharge de formulaires/panier permet d'afficher le nom du produit avec toutes les options choisies.

## Notes techniques

A l'installation, ajout d'un champ 'options varchar(100)' dans les tables spip_paniers_liens et spip_commandes_details

La clé primaire composée de spip_paniers_liens est supprimée et recréée avec ces champs : (id_panier, id_objet, objet, options) 

Surcharges du plugin panier pour tenir compte des options :

- action/commandes_paniers.php
- action/remplir_panier.php
- formulaires/panier.html
- formulaires/panier.php

## TODO 

**1 - Choix des options**

Actuellement, côté public, on ne peut choisir qu'une option par groupe.

Proposer une configuration pour chaque groupe, qui permettrait d'en choisir soit une seule soit plusieurs (radio/checkbox).

**2 - Problème à l'installation**

Si la table spip_paniers_liens contient déjà des données, la création de la nouvelle primary key composée génère une erreur sur un serveur Mysql (Duplicate entry ...).

Deux solutions : 

1/ vider la table spip_paniers_liens

2/ passer par une clé autoincrement temporaire :  

ALTER TABLE `spip_paniers_liens` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;  
ALTER TABLE `spip_paniers_liens` ADD UNIQUE KEY  (`id_panier`, `id_objet`, `objet`, `options`);  
ALTER TABLE `spip_paniers_liens` DROP `id`;
  
mais ça ne marche pas sur tous les serveurs Mysql