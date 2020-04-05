Coupons et bons d'achat
=======================

Bons de réduction
-----------------

Les bons de réduction sont déduits du total TTC de commande.
Ils n'ont pas de TVA, et ils ont dans cette première version un montant fixe.

Le formulaire #FORMULAIRE_UTILISER_COUPON permet de générer une ligne dans le détail de commande qui applique la réduction.
On peut placer ce formulaire par exemple sur la page de paiement.

Cela peut demander une modification de inclure/commande.html, pour afficher le coupon et sa réduction sous le récapitulatif des produits.

Par exemple, sur la base du squelette fourni avec le plugin Commandes :

```
	<BOUCLE_details(DATA){source tableau, #GET{details}}{objet!='coupon'}>
		...
	</BOUCLE_details>
	
	<BOUCLE_coupon(DATA){source tableau, #GET{details}}{objet='coupon'}>
	<tr class="total expedition">
		<td></td>
		<td></td>
		<td class="descriptif">#DESCRIPTIF</td>
		<td class="montant">[(#VALEUR{prix}|prix_formater)]</td>
	</tr>
	[(#SET{total,[(#GET{total}|plus{[(#VALEUR{prix})]})]})]
	[(#VALEUR{prix_ht}|commande_totalise_taxes{#VALEUR{prix}})]
	</BOUCLE_coupon>
```

Les coupons ont une date de fin de validité (configuration du nombre de jours, 365 par défaut).

Les coupons sont utilisables en plusieurs fois.
Une fois le coupon de réduction utilisé dans une commande (quand la commande passée au statut "payé"), le montant de réduction accordé est enregistré dans la table coupons_commandes. 
Quand le montant total a été entièrement utilisé (dans une ou plusieurs commandes), il n'est plus utilisable.

Bons d'achat
------------

On peut créer un produit de type "bon d'achat" : une fois le produit commandé et la commande passée au statut "payé", un bon de réduction du montant TTC du produit est généré, avec un code aléatoire.
La TVA du produit bon d'achat est liée au coupon de réduction, qui n'appliquera donc sa réduction que sur les produits de même TVA.


Todo
----

Pas mal d'options pourraient être ajoutées.

En s'insiprant de ce qui existe dans différents CMS ecommerce :

- gérer un montant de remise en % au lieu de fixe,
- offrir les frais de port au lieu d'une réduction sur la commande,
- restreindre l'utilisation du coupon :
  - à une personne en particulier, 
  - à un nombre total d'utilisation, 
  - à un ou des produits, 
  - à une ou des rubriques,
  - ...
