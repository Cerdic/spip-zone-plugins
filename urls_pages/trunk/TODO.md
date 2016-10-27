# Plugin URLs Pages Personnalisées : choses à faire


## URLs pages non migrées

Lors de la migration de la branche 0.x vers la branche 1.x, les URLs enregistrées dans le meta `urls_pages` sont migrées dans la table `spip_urls`.
Seules les URLs non converties car déjà présentes dans la table sont conservées dans le meta.

En principe, ce cas de figure ne devrait pas se présenter car on vérifiait les doublons au moment d'enregistrer les URLs, mais réfléchir à ce qu'on pourrait faire dans ce cas improbable :
- message de mise en garde sur la page des URLs ?
- migration sauvage (c'est à dire qu'on enregistre quand même, et l'URL de la page prendra le pas sur l'URL de l'objet) ?
