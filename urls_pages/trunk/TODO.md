# Plugin URLs Pages Personnalisées : choses à faire


## Liste des fonds des pages

Une même page peut avoir plusieurs fonds, par exemple :
- `squelette/contenu/lapage.html`
- `squelettes/lapage.html`
- `squelettes-dist/lapage.html`

Dans la liste, il faudrait trouver un moyen (optionnel) de ne faire apparaître le fond d'une page qu'une seule fois, en prenant celui qui à la priorité (dans l'exemple, avec zCore activé, ce serait  `squelettes/contenu/lapage.html`).


## URLs pages non migrées

Lors de la migration de la branche 0.x vers la branche 1.x, les URLs enregistrées dans le meta `urls_pages` sont migrées dans la table `spip_urls`.
Seules les URLs non converties car déjà présentes dans la table sont conservées dans le meta.

En principe, ce cas de figure ne devrait pas se présenter car on vérifiait les doublons au moment d'enregistrer les URLs, mais réfléchir à ce qu'on pourrait faire dans ce cas improbable :
- message de mise en garde sur la page des URLs ?
- migration sauvage (c'est à dire qu'on enregistre quand même, et l'URL de la page prendra le pas sur l'URL de l'objet) ?
