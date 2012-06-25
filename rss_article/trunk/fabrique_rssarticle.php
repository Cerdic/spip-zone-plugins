<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-06-22 09:30:14
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'Flux RSS en articles',
    'slogan' => 'Recopie les flux RSS en articles',
    'description' => 'Ce plugin recopie les flux RSS (articles syndiqués) en articles

-* reprise du contenu du flux;
-* créé l\'auteur s\'il est mentionné dans le flux;
-* ajoute les documents distants présents dans le flux;
-* dans le champs URL de l\'article, on indique l\'adresse de l\'article d\'origine.

Pour éviter les doublons et les imports successifs, une fois l\'article créé, l\'article syndiqué est rejeté (ce qui permet de suivre où en sont les recopiés).',
    'prefixe' => 'rssarticle',
    'version' => '1.0.0',
    'auteur' => 'erational',
    'auteur_lien' => 'http://www.erationnal.org',
    'licence' => 'GNU/GPL v3',
    'categorie' => 'edition',
    'etat' => 'dev',
    'compatibilite' => '[3.0.2;3.0.*]',
    'documentation' => 'http://www.spip-contrib.net/Plugin-Flux-RSS-en-articles',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Copie RSS en articles',
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMAUExURbu7u4aGhqenp+RzNujo6OOme+x9LPnj083NzfmTLczMzPT09PnSteJsLfSRNvHl3cfHx+OdbN6ccvvSq/udOt1hKu7c0vKNNnR0dOOqhNR4PONrI/717/3ix+jFrvCEK9vb29lZJeF+NduJU/KNPPn5+e3t7eOAPOi1lO+BKPzMnJycnHp6eveUNvCJNfqdQuOti/qhRvuZM8jIyO+FMul1JfnOqvvCh+p+OfPt6tyTZPzbvOt+Me6NQeh8O+2CMvHx8eWANeVyLe+IO93d3fbi1vm7g+x7JttdKPmXNemFPv/69uSykurNuvu7e+d2L/CRQd2RXs5iHO+GNPeVON+FRvmYOPmaOfrXu/mWMOnJtvTw7uCec/XNuN1cH+l6Mf3p1fWNK+yCOtDQ0Obm5vrMpf3q2PeRLeFnJPLp5PKlbPmZP9+ie/KMM+e9ofr6+u3Xyfb29vKSQOGKTfWWQf7u3t+abvKIKvaiVZSUlPXDpPG7osTExP7t2+a5nL6+vu6OPeV2OPTx8M1eF/eSMv2gP//+/faVPvSRPel+NvCIM/eWOONvNvqoU+h3Lf/9/P/9++iCPvmmUv3w6PKUQu2KP/CGM+NwLut/Mv///2NjY+Pj49nZ2eDg4NTU1OTk5Pz8/OXl5eHh4f/+//77+dZ9RM/Q0PX29efBqtDQz8vLy9DPz+ZwJc5lIP///v/+/vWNLvmaOv7//vqaOv7//3FxceHg4O3Hrfzt499iJPfAld6fd/nZxfmbOfSLKt9lLvWPMeBnLGZmZurq6vCkcsDAwOm6nOa3mOymiOKmf/7+//CQPe/g1vWSN/SPM+ubcvW/mPbIqbi4uNBoJs/Pz9BqKPv7++uANvObVPqePuOgcfydPvWhWfeoX/imWPCzkfS4juqAPOmFOO6FOu6OR/X19dDP0PLXxfTdzf3YtfjXw/jdzfKVSfb29f3r3OvRwO7azemQYfCfaf7+/vmZOe6FPPKJLO/Nt/Wyffu5d+7KsvqtYdbW1dbW1uuHP/X19Q9/xCoAAAEAdFJOU////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wBT9wclAAAETElEQVR42mL4TyFgoIoB/JLFaCDUKooEA3zVHyUlJT12dlZR6ehob8/OztbX138UE02sAWcck5yc+uJt7O2ndeWK853V7Q77slektegkkQZYKnc6+XdeZ3NnM9ETh+sXEbntWECcASsc4/1NL05m5/nxRq/8HFz/Zs+Sk8QZEPPPP96UZyYQeKe5nU3sDgsH6Q/v5isRJNaAeBvTiJkQkOAX6QnSv7H7rHgH0QbYzGDL2sXzbjHYCOFwI4h+PRViDZg6wz5YU1NevMItDWRCmqHRZpB+E+INsDeZdrootzwxUVfLG2hCrWELUH+wvTPRBpgEywvLJKTeS+fk7C0FBcSBXD0Te5uvxBpgO01PHhyIL39HRhomABlm8ib2M+KTiDZAT7zcGhIJ5kZG+iA33GCbEe9vQbQB4uLn7ppbg7w/U4szvaJt5kx2tnh/JzviDeA725KePullP9AEtw3lqUDqyBqn6UQbEABO/56RnH5Au9ffkA+ePHPmK+npvI1EG6Cr2+3p2XvTKBJkQqqm+4WZMyc38h4k2oCikG5PQ9kJtb2e6bIzZy55YvoJ6IePGQeZiTVgimcYpzAoAXVvuDNh5swHax7KzJx5P06UeAO+fOEERaN3Sa48+8yZe9aseT1zZmycaA+RBpRN2bs30hyUfL7puZvNnLnDKSN25kxXDw9iDTBoAGZfkZ9abuJdwWxZVVX3p2ecr64+4aHYeAukwDeTkAFiSq3A8iN9X3lXsP2MvjXSGby8O+PiFD0UO/OA8ruVr+YRMOBp3enWL+D8C0z//k7TeXkP7hcVBTog1PK/pVDZkhdBUf8t8ZbKTYEN+iEg/TZQ/UDtHvsblcT+iwlJAsPme1DU2ky89UKTj7qtre3Uqf/+eXl5TQSCh3YT1QPF/vs2HwNnse3Kkgb4a6amKANWVCCZd+b/iqBf0JJyptUKMurG45efw/TPlPpAugHRQa5w/UuCmshwgQ8r3ABXKSCfpYYBFbgQMKBJyAdmwPbA/9zaszAA42z87QN+q+1QAwwk/1fOkvibshAZpDDNkjiMv4Hxfmk/xIBAufpZjAtmogMOnUq8BliuPa4RU7Z0aVCz0H/tWWozMcGlWdx4XXAmJ6cur6AAFAM6sxZgMYBj1lxi20gSWA24Mkth1ACSDHARMEYBAi4kGnBNFQ1co7cX1BbNRgGL1OhtAMVeWD9zy8yZ6zYBG4OnPm9avGXxusWkGZA/l+MKCuCYu4CDFAMKC5+igULVVaQYwL0cqu8tmATzjEkxwIFl9nwUMJvFQYAUA9QU0MNAQa2GFANYsICjpBgwf9G2uahg0ex6UgzYliyABpJdGEgxwGUOJrhEvAGMs678mYcBmGaxEGsA0yxtjuSVK69cWb1s2bIrV5KfJV9ZnVyjw0h011dNYhaTwCouZGDMsHVWPfF9Z4WtmJXrLG1SOt8sTIyHUHTraHNRpfcOEGAAOTyBYRHXS+YAAAAASUVORK5CYII=',
        ),
      ),
    ),
    'objets' => 
    array (
    ),
  ),
);

?>