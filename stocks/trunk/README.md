# Stocks

Gestion de quantité.

## BOUCLES

```html
<BOUCLE_enStock(STOCKS){objet}{id_objet}>
	#SET{stock, #QUANTITE}
</BOUCLE_enStock>
<select name="quantites[#OBJET][#ID_OBJET]">
<BOUCLE_dispoStock(DATA){enum 1,#GET{stock}}>
		#SET{quantite_choisi, #ENV{quantites}|table_valeur{#OBJET}|table_valeur{#ID_OBJET}|sinon{#QUANTITE}}
		<option value="#VALEUR"[(#GET{quantite_choisi}|=={#VALEUR}|oui) selected]>#VALEUR</option>	
</BOUCLE_dispoStock>
</select>
```

## BALISES

`#QUANTITE` affiche la quantité dans un contexte sans utiliser de boucle


## Todo

!! Revoir le shema
 
- [] un stock doit pouvoir être étendu par des champs extra ou autres fonctionalitées,
on devrait donc créer une table stock et migrer l'actuelle vers stock_liens pour associer un stock
a un objet editorial, plusieures espaces de stockage différents et les stocks disponibles.

- [] Pas de fonction ou action actuelle pour décrémenter le stock au moment d’une commande
- [X] La valeur passé en config de stock par défaut n’est pas utilisée ou fonctionnelle, ni sur des produits déjà créés, ni pour les nouveaux produits..
- [] Necessite saisie juste pour 1 champ ajouté dans editer_objet
- [] Pas de selection des objets sur lesquels on veut gérer des quantitées
- [] ECRIRE - Une page récapitulative pour afficher et gérer les objets en stocks
- [] ECRIRE - Export des stocks vers csv, ou autres …
- [] une noisette/inclure gerer_quantite utilisable dans le panier par exemple