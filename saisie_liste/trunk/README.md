
La saisie liste
===============

Cette saisie permet de gérer des listes. On passe à la saisie une
liste de saisies qui définissent alors un élément de la liste, et la
saisie générée permet à l'utilisateur d'éditer, de créer ou de
supprimer des éléments de la liste et/ou de modifier leur ordre. Elle
peut fonctionner sans javascript, mais pour les utilisateurs qui
l'activent, on peux réordonner les éléments par glisser-déposer via le
plugin jqueryui.sortable.

Un fois le plugin installé, on peut voir la saisie en plein action sur
la page `/ecrire/?exec=exemples_saisie_liste`.

Appel de la saisie
------------------

La saisie s'appelle dans les squelettes comme n'importe quelle saisie :

```
[(#REM)
  parametres :
        - nom     => Le nom de la saisie. Obligatoire, le reste est
                     optionnel.
        - label   => Le label.
        - legende => La légende du fieldset qui contient la saisie.
        - saisies => La liste de saisies définissant un élément.
        - inclure => Le chemin vers un squelette qui sera inclu au
                     début de chaque élément. Vide par défaut.
        - defaut  => Le tableau des valeurs par défaut de la saisie.
]

[(#SAISIE{liste, ma-liste,
          label=Objets,
          saisies=#ARRAY{0, #ARRAY{saisie, input,
                                   label, Titre de l'objet,
                                   nom, titre_objet},
                         1, #ARRAY{saisie, textarea,
                                   nom, description,
                                   label, Description}}
})]
```

On peut aussi utiliser le format de la balise `#GENERER_SAISIES` :

```php
$ma_saisie = array(
    'saisie'  => 'liste',
    'options' => array(
        'nom'     => 'ma-liste',
        'label'   => 'Objets',
    ),
    'saisies' => array(
        array(
            'saisie'  => 'input',
            'options' => array(
                'label' => "Titre de l'objet",
                'nom'   => 'titre_objet',
            ),
        ),
        array(
            'saisie'  => 'textarea',
            'options' => array(
                'label' => "Description",
                'nom'   => 'description',
            ),
        ),
    ),
);
```

Traitement des valeurs postées
------------------------------

Pour que la saisie puisse fonctionner correctement, notamment pour les
utilisateurs qui n'ont pas activé le javascript, il faut executer des
traitement au début des fonctions vérifier et traiter. Le plus simple
est de toujours commencer vos fonctions verifier et traiter par :

```php
if (saisies_liste_verifier('ma-liste'))
    return array();
```

et vos fonctions traiter par :

```php
if (saisies_liste_traiter('ma-liste'))
    return array('editable' => 'oui');
```

où 'ma-liste' est le nom de la saisie liste que vous avez créé. Si le
formulaire contient plusieurs saisies liste, il faut passer à ces
fonctions un tableau des noms des saisies, p.ex :

```php
if (saisies_liste_verifier(array('liste-1', 'liste-2', 'liste-3')))
    return array();
```

Les fonctions `saisies_liste_verifier` et `saisies_liste_traiter`
s'occupent de préparer les valeurs postées de manière à cacher celles
qui ne sont utiles que pour le fonctionnement interne de la
saisie. Utiliser la fonction `_request` avant des les avoir appelées
est à vos riques et périls… Elle retournent le nom de la saisie si le
formulaire à été posté par un submit propre à une saisie liste, comme
le bouton supprimer ou les flèches. Dans ce cas on souhaite alors en
général interrompre les traitements du formulaire.

Dans le cas où le formulaire à été posté par un autre submit,
`saisies_liste_verfier` et `saisies_liste_traiter` retournent
`FALSE`. On récupère alors les valeurs saisies en appelant :

```php
_request('ma-liste');
```

qui aura la forme suivante (si on reprend l'exemple ci-dessus) :

```php
array(
    0 => array(
        'titre_objet' => "Le premier titre saisi par l'utilisateur",
        'description' => "Une longue description de l'objet…",
    ),
    1 => array(
        'titre_objet' => "Le deuxième titre saisi par l'utilisateur",
        'description' => "Une description du deuxième objet…",
    ),
)
```

On peut évidement utiliser un tableau de ce genre pour pré-remplir la
saisie dans la fonction charger, ou pour passer des valeurs par défaut
à la saisie.

Personnalisation du glisser-déposer
-----------------------------------

Pour personaliser l'appel au plugin jquerui.sortable, on peut
surcharger le squelette `javascript/saisie_liste.js.html` (voir le
code de ce squelette pour plus d'infos). On peut aussi créer un
fichier `javascript/saisie_ma-liste.js.html` pour surcharger une
saisie particulière.
