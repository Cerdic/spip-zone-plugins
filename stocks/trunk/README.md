# Stocks

Documentation :
https://contrib.spip.net/Stocks

Dépot:
https://zone.spip.org/trac/spip-zone/browser/_plugins_/stocks/trunk

## Todo

- [?] mettre en place une config pour gérer ou non le changement de statut du produit
- [] une noisette/inclure gerer_quantite utilisable dans le panier par exemple



## Changelogs

v0.2.4
- correction sur le changement de statut du produit : autorisation

v0.2.1
- Ajoute une fonction de décrémentation du stock des produits au moment du passage a payé d'une commande
depuis le statut attente|encours (les paiements cartes/paypal ne passe pas par attente)
- Ajoute un Statut épuise sur les produits quand le stock arrive a 0, permet de filtrer donc plus facilement les produits en ligne et dans la sitemap, sans boucler sur les stocks.
- On peut gérer le statut d'un produit depuis la page de gestion des stocks, et le repasser manuellement en publié quand on remet du stock.


v0.1.5
- [X] ECRIRE - Une page récapitulative pour afficher et gérer les objets en stocks
- [X] La valeur passé en config de stock par défaut n’est pas utilisée ou fonctionnelle, ni sur des produits déjà créés, ni pour les nouveaux produits..
