Plugin Couleur d'Objet : règles de commit
=========================================

Tout le monde est encouragé à commiter dans la mesure ou ça ne bouleverse pas l'utilisation ou l'apparence du plugin.
Si c'est le cas, rendez-vous sur la liste de discussion spip-zone ou sur IRC pour en discuter.

## Squelettes

En cas d'ajout de squelettes ou de code php, pensez aux autres contributeurs : commentez !
Chaque squelette devrait commencer par 1 à 3 commentaires expliquant certains points :

- la description du squelette.
- les squelettes utilisés, et ceux où il est inclus.
- les paramètres éventuels.

Exemple complet :

    [(#REM)

        Description du squelette

    ][(#REM)

        Utilise :
            inclure/noisette.html
        Inclus dans :
            prive/squelettes/contenu/truc.html

    ][(#REM)

        Paramètres (*obligatoire):
            *param1    explication param1
            param2     explication param2

    ]
