# Objets disponibles
Permet de définir des disponibilités pour les objets

## Utilisation
Dans la configuration du plugin déclarez les objets pour lesquels vous voulez gérer
des diponibilités.

Vous popuvez alors définir pour ces objets des periodes de disponibilité ou de non
disponibilités

Dans l'état actuel, la manière principal d'affichage des dates disponibles est via
la saisies `dates_disponibles`, vous y trouverez des examples ainsi que toutes les
variables utilisées.

Il existe également une fonction `dates_disponibles($options, $contexte)` qui utilise
les mêmes variables que la saisies et retourne un tableau avec les dates disponibles.

## Fonctionnement
Basiquement on calcules les dates disponibles pour un objet on y déduit les non disponibles
puis les dates utilisées (par example dans le cadre d'une location avec le plugin
[Objets Location](https://github.com/abelass/location_objets)

toutes les calcules se font dans des squelettes, donc facilement modifiable. Les dates
disponibles et indisponibles se trouvent dans le dossier disponibilites puis si nécessaire
on peut déclarer un squelette utilisé pour le calcul des dates utilisées en employant la
variable `utilisation_squelette` comme dans l'example avec `utilisation_squelette=disponibilites/utilisees_objet_location.html`,

## to do
A l'instar de api prix. Faire une balise disponibilité qui calcule la disponiblite d'un objet .
