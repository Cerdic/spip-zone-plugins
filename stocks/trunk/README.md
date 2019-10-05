# Stocks

Documentation :
https://contrib.spip.net/Stocks

Dépot:
https://zone.spip.org/trac/spip-zone/browser/_plugins_/stocks/trunk

## Todo

@todo -  mettre en place une config pour gérer ou non le changement de statut du produit ?

@todo - saisie gerer_quantite utilisable dans le panier par exemple

@todo - Quand il n'y a pas de stock créés a l'instalation la page de gestion des stock est vide, prévoir un message explicatif

@todo - Gestion js/ajaxreload Sur la vue d'un produit
- le formulaire de gestion du stock affiche 'créer le stock' il faut recharger la page pour que l'id_stock affiche le bon message.
- lors de la suppression du stock le ajax reload ne fonctionne pas

## Améliorations à prévoir

Actuellement le stock, est débité mis a jour lors du retour de paiement quand
le statut passe a payé.

il faudrait tester sur la table spip panier lien les produits encours de commande, pour ne pas proposer dans la saisie quantité plus que de stock disponible.



## Changelogs

0.2.8

- utilisation de la pipeline `remplir_panier` pour tester le stock disponible lors de l'ajout au panier.

0.2.7

ajout des autorisations sur les stocks.seul un webmestre ou rédacteur peuvent voir ou éditer un stock

v0.2.5

Supprimer un stock

- Ajout d'une action pour supprimer un stock
- Ajout au formulaire gerer stock (utilisé la page d'édition d'un produit et la page des stocks) d'un bouton action de suppression du stock.
- Rechargement du bloc conteneur quand on agit sur le formulaire pour mettre a jour les infos et passer de créer à éditer/supprimer
- ajout d'un pagination sur la liste des stocks, on trie par date inverse.

En test
- Ajout aux utilise le plugin livraison
- Ajout d'une colonne dans le listing des stocks "Livraison" qui affiche si le produit est immatériel ou livrable
- dans le cas d'un produit livrable, et si le plugin livraison est installé, on affiche le/s modes de livraison "forcés" si il en a d'associés au produit, sinon on signale que les rêgles de livraison classique s'applique.


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
