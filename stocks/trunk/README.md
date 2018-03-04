# Stocks

Documentation :
https://contrib.spip.net/Stocks

Dépot:
https://zone.spip.org/trac/spip-zone/browser/_plugins_/stocks/trunk

## Todo

!! Revoir le schema

- [] un stock doit pouvoir être étendu par des champs extra ou autres fonctionalitées,
on devrait donc créer une table stock et migrer l'actuelle vers stock_liens pour associer un stock
a un objet editorial, plusieures espaces de stockage différents et les stocks disponibles.
- [] Pas de fonction ou action actuelle pour décrémenter le stock au moment d’une commande
- [] Necessite saisie juste pour 1 champ ajouté dans editer_objet
- [] Pas de selection des objets sur lesquels on veut gérer des quantitées
- [] ECRIRE - Export des stocks vers csv, ou autres …
- [] une noisette/inclure gerer_quantite utilisable dans le panier par exemple

## Changelogs



v0.1.5
- [X] ECRIRE - Une page récapitulative pour afficher et gérer les objets en stocks
- [X] La valeur passé en config de stock par défaut n’est pas utilisée ou fonctionnelle, ni sur des produits déjà créés, ni pour les nouveaux produits..
