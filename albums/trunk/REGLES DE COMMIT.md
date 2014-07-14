Plugin Albums : règles de commit
================================

Tout le monde est encouragé à commiter dans la mesure ou ça ne bouleverse pas l'utilisation ou l'apparence du plugin.
Si c'est le cas, rendez-vous sur la liste de discussion spip-zone ou sur IRC pour en discuter.

## Branches et trunk

Les branches v1, v2 et plus sont pour les versions stables, d'où sont issus les zip.
Le trunk est une version de développement où on peut faire joujou.

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

## Modèles

Le plugin ne fournit que 2 variantes pour le modèle `<album>` : une vue «vignettes» par défaut et une variante «liste».
Ces 2 modèles sont minimalistes par choix : ils ont vocation à être surchargés.
On s'en tient donc à ces 2 modèles de base, en cas d'idée géniale pour un nouveau modèle, discutons en avant.

### Ajouter des modèles

Les plugins qui se servent des albums peuvent ajouter des variantes du modèle `<album>`, pour des diaporamas, des playlists etc.
Le principe du plugin «Insérer modèles» est repris.
A chaque variante du modèle doit correspondre un fichier yaml contenant les saisies des options du modèle.
Ce fichier yaml sert au formulaire qui permet de générer une balise `<album>` à insérer dans le texte, en mode édition.

Par exemple, pour ajouter une variante «diaporama», il faut :

- un fichier `album_diaporama.html`
- un fichier `album_diaporama.yaml`. Il y a 4 saisies obligatoires : `modele`, `id_modele`, `id_album` et `variante`. Voir pour exemple `album_liste.yaml`
