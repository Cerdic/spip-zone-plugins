# Identifiants v2

![](images/plugin-identifiants.svg)

> Un plugin pour attribuer des identifiants textes uniques aux contenus.

> **VERSION DE DEV ! Faîtes une sauvegarde de votre base avant de tester cette version**.

Certains contenus ont une fonction unique. Ce plugin ajoute un moyen de les identifier facilement, sans avoir recours à des astuces ou artifices.

Ainsi, au lieu d’utiliser un numéro d'objet : ```<BOUCLE_rubrique(RUBRIQUES) {id_rubrique=N}>``` ou de détourner un champ de son utilisation normale : ```<BOUCLE_rubrique(RUBRIQUES) {titre = ecureuil}>```, il devient possible de sélectionner proprement un contenu :  ```<BOUCLE_rubrique(RUBRIQUES) {identifiant = ecureuil}>```.

En résumé :

* Les identifiants sont uniques : un identifiant ne peut être utilisé qu’une seule fois par type de contenu et par langue.
* Seuls les webmestres peuvent voir et manipuler les identifiants
* Ce ne sont pas des mots-clés !

## Utilisation

Les types de contenus auxquels on peut ajouter des identifiants sont à sélectionner dans la page de configuration du plugin.

Ensuite, les identifiants se retrouvent dans le formulaire d’édition des contenus, en principe juste après ce qui fait office de titre.

Une page liste tous les identifiants du site dans le menu Édition → Identifiants.

Dans les boucles, il suffit d’utiliser le critère `{identifiant = trucmuche}`.

## Technique

Dans la meta du plugin, 2 clés importantes :

* `tables_repertoriees` est un tableau de toutes les tables déclarées, un booléen indique si elles ont nativement une colonne `identifiant` ou pas.
* `objets` est un tableau des tables sélectionnées.

Le champ `identifiant` n`est ajouté que sur les tables sélectionnées, et retiré lorsqu'on en déselectionne dans la config.

Les tables possédant nativement ce champ ne sont pas traitées par le plugin.