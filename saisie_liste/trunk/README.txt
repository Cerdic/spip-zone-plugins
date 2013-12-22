*========================*
| La saisie liste-objets |
*========================*

C'est quoi ?
------------

Cette saisie permet de gérer des listes d'objets ordonnés. On passe à la
saisie une liste de saisies qui définissent alors un objet, et la saisie
générée permet à l'utilisateur de créer un ou plusieurs de ses objets
et/ou de modifier leur ordre. Elle peut fonctionner sans javascript, mais
pour les utilisateurs qui l'activent, on peux réordonner les objets par
glisser-déposer via le plugin jqueryui.sortable.
On peut voir la saisie en plein action sur la page
/ecrire/?exec=exemples_saisie_liste_objets .

Appel de la saisie
------------------

La saisie s'appelle dans les squelettes comme n'importe quelle saisie :

  [(#REM)
    parametres :
          - nom     => Le nom de la saisie.
          - label   => Le label.
          - saisies => La liste de saisies définissant un objet.
          - inclure => Le chemin vers un squelette qui sera inclu au
                       début de chaque objet. Vide par défaut.
          - defaut  => Le tableau des valeurs par défaut de la saisie.
  ]

  [(#SAISIE{liste_objets, ma-liste,
            label=Objets,
            saisies=#ARRAY{0, #ARRAY{saisie, input,
                                     label, Titre de l'objet,
                                     nom, titre_objet},
                           1, #ARRAY{saisie, textarea,
                                     nom, description,
                                     label, Description}}
  })]

On peut aussi utiliser le format de la balise #GENERER_SAISIES :

  $ma-saisie = array(
      'saisie'  => 'liste-objets',
      'options' => array(
            'nom'     => 'ma-liste',
            'label'   => 'Objets',
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
        ),
    );

Traitement des valeurs postées
------------------------------

Pour que la saisie puisse fonctionner correctement, notamment pour les
utilisateurs qui n'ont pas activé le javascript, il faut executer des
traitement au début des fonctions vérifier et traiter. Il est impératif de
toujours commencer vos fonctions verifier par :

  if (liste_objets_verifier('ma-liste')) return;

et vos fonctions traiter par :

  if (liste_objets_traiter('ma-liste')) return;

où 'ma-liste' est le nom de la saisie liste_objets que vous avez créé.
Si le formulaire contient plusieurs saisies liste_objets, il faut
executer ces traitements pour chacune d'entre elles.
Ce code permet de prendre la main sur les fonctions vérifier et traiter
définies pour le formulaire quand l'utilisateur clique sur "monter",
"supprimer" ou un autre submit spécifique à la saisie liste-objets.

Ceci fait, on peut récupérer les valeurs saisies en appelant

     _request('ma-liste');

qui aura la forme suivante (si on reprend l'exemple ci-dessus) :

    array(
        0 => array(
            'titre_objet' => "Le premier titre saisi par l'utilisateur',
            'description' => "Une longue description de l'objet…",
        ),
        1 => array(
            'titre_objet' => "Le deuxième titre saisi par l'utilisateur',
            'description' => "Une description du deuxième objet…",
        ),
    )

On peut évidement utiliser un tableau de ce genre pour pré-remplir la
saisie dans la fonction charger, ou pour passer des valeurs par défaut à
la saisie.

Personnalisation du glisser-déposer
-----------------------------------

Pour personaliser l'appel au plugin jquerui.sortable, on peut surcharger
le squelette inclure/init-saisie-liste-objets.js.html (voir le code de ce
squelette pour plus d'infos).
