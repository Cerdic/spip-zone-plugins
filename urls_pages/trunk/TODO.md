# Plugin URLs Pages Personnalisées : choses à faire


## URLs pages non migrées

Lors de la migration de la branche 0.x vers la branche 1.x, les URLs enregistrées dans le meta `urls_pages` sont migrées dans la table `spip_urls`.
Dans le cas improbable où certaines sont déjà utilisées par des objets éditoriaux, elles sont conservées dans le meta (mais elles ne sont plus fonctionnelles donc).

Pour l'instant on les montre aux utilisateurs dans un onglet à part, il faut ajouter un moyen de régler le problème (un bouton pour supprimer la meta après que les admins aient réglé le problème, ou autre).

## Refactorisation

Tous les traitements se font actuellement dans le formulaire editer_url_page.
Il faut les déplacer dans l'API (à créer) :

- action/editer_url_page.php
    - url_page_insert()
    - url_page_edit()
    - url_page_delete()
- supprimer_url_page.php

## Langues

Réfléchir à la prise en compte des langues.