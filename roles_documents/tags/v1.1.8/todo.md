# Rôles de documents : choses à faire

## Boucles

Les boucles DOCUMENTS devraient peut-être ne pas retourner les documents avec le rôle de logo par défaut, sauf si critères {tout} ou {role=logo}.

## Interface

Surcharger le formulaire d'édition d'un logo : utiliser le formulaire d'ajout de document à la place, en forçant le rôle "logo".

## Bugs/limitations

- Chaque rôle donné à un document crée une nouvelle ligne dans la table `spip_documents_liens`, donc si on boucle sur cette table pour afficher les documents liés à un objet, le même document ressortira autant de fois qu'il a de rôles (cf. `documents_colonne.html` du plugins Médias).
- Le critère `{vu}` est inopérant puisqu'un même document peut se retrouver à la fois vu et non vu après plusieurs manoeuvres. Pistes pour résoudre ça : une seul ligne par document lié, avec plusieurs rôles séparés par des virgules ? Ou alors par défaut faire en sorte que la boucle documents ne retourne qu'une seule fois un doc lié ayant plusieurs rôles ?
- l'API des rôles ne permet pas de limiter les rôles attribuables à un objet. En conséquence, le rôle de logo peut être attribué plusieurs fois.
