# Périodes de prix
Extension pour [Prix objets](https://plugins.spip.net/prix_objets) qui permet de définir un prix par rapport à une période de temps.

## Dépendances
- [Saisies](https://plugins.spip.net/saisies.html)
- [Vérifier](https://plugins.spip.net/verifier.html)
- [Prix objets](https://plugins.spip.net/prix_objets)
- [Périodes](https://plugins.spip.net/periodes.html)
- [Dates outils](https://plugins.spip.net/dates_outils.html)

## Utilisation
### Définir une période
Définissez vos périodes sous Édition/Périodes

Choisissez d'abord le type de période :
- Dates
- Jour de semaine

Puis définissez si le prix doit s'appliquer quand:
- il y a `coïncidence` entre les dates soumises et celle de la période définie.
- les dates soumises sont `exclues` de la période définie.

Ensuite déterminez la période (date début/ fin ou bien jour début/fin)

### Définir les prix
Définissez les prix en choissisant les périodes définies. Vous pouvez choisir plusieurs
périodes à la fois

### Utilisation dans votre code
Le plugin prix_objets fourni un filtre/fonction `prix_par_objet` qui permet de calculer le prix d'un objet en
tenant compte des extensions définis.

Le plugin [Objets locations](https://github.com/abelass/location_objets/blob/master/inc/objets_location.php#L34)
en fournit un example.
