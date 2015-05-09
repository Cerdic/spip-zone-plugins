## Balises

Prendre en charge tous les `LOGO_XXX` (pour l'instant seuls `#LOGO_ARTICLE` et `#LOGO_RUBRIQUE` sont pris en charge).

## Critères

Ajouter la possibilité de filtrer les rôles avec des critères, `{role=truc}` est insuffisant car les rôles peuvent être multiples.

## Déclarations

Dans les déclarations, il faudrait pouvoir dire qu'un rôle DOIT être unique pour un même contenu, ou inversement qu'un autre rôle peut être multiple et quand on l'assigne à un autre document : ça l'enlève alors du précédent (pour le cas où le rôle doit être unique).
En conséquence, dans l'interface, il faudrait prendre en compte ces limitations. Par ex. rendre impossible l'attribution d'un même rôle à plusieurs documents (cas des logos).
À noter : en ajoutant des rôles à un type d'objet, il faut prendre en compte les rôles portant sur tous les objets et qui ont la clé '*', et faire un merge.

## Interface

Rendre les rôles attribués plus visibles. Le rôle doit prendre la primeur sur le titre. Quand c'est le logo, ça devrait alors placer l'image dans dans un sous-titre "Logo", comme il y a "illustration" et "portfolio" (qui disparaitront ). Ça donnerait 3 listes de documents : Logo / Documents insérés / Documents joints . Après sélection d'un rôle, recharger tout le bloc des documents.

## Bugs/limitations

- Chaque rôle donné à un document crée une nouvelle ligne dans la table `spip_documents_liens`, donc si on boucle sur cette table pour afficher les documents liés à un objet, le même document ressortira autant de fois qu'il a de rôles (cf. `documents_colonne.html` du plugins Médias).
- Le critère `{vu}` est inopérant puisqu'un même document peut se retrouver à la fois vu et non vu après plusieurs manoeuvres. Pistes pour résoudre ça : une seul ligne par document lié, avec plusieurs rôles séparés par des virgules ? Ou alors par défaut faire en sorte que la boucle documents ne retourne qu'une seule fois un doc lié ayant plusieurs rôles ?
- On ne peux pas détacher un document qui possède un rôle déclaré par un plugin désactivé.
