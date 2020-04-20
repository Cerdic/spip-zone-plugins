# Plugin CVT Rechercher

Ce plugin fournit quelques aides permettant de construire des formulaires de recherche orientés « facettes » qui peuvent contenir plusieurs champs.

## Principe

Il se branche automatiquement sur tous les formulaires nommés `#FORMULAIRE_RECHERCHER_XXX`.

Ceux-ci peuvent contenir plusieurs champs avec les valeurs à inclure dans la recherche. Il peut s’agir de champs à valeurs uniques ou multiples.

Le plugin s’occupe de la redirection en y ajoutant les valeurs recherchées : `?champ1=truc&champ2[]=machin&champ2[]=chouette`

Il change également l'intitulé du bouton de validation  si le formulaire est fait au moyen de saisies et que l'option `saisies_texte_submit` n'est pas déjà définie.

## Exemple d’utilisation

Soit un formulaire avec deux saisies « ville » et « pays ». La saisie « ville » permet de choisir plusieurs valeurs.

Il faut ajouter une clé`_rechercher_champs` dans le charger du formulaire, et optionnellement une clé `_rechercher_ancre` :

```php
// Chargement des valeurs
$valeurs = array(
    'ville' => _request('ville'),
    'pays'  => _request('pays'),
);
// Ajouts pour le plugin CVT rechercher
$valeurs['_rechercher_champs'] = array(
	'ville' => array('multiple' => true),
	'pays'  => array(),
);
$valeurs['_rechercher_ancre'] = 'resultats_recherche';
```