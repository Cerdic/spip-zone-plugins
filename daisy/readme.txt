= Mode d’emploi
= pour Daisy CSS

1. Activez le plugin
2. Videz les caches pour afficher le site
3. Écrivez vos personnalisations CSS dans un fichier /squelettes/css/custom.css
4. Activez le compactage des feuilles de style (CSS) dans SPIP

C’est tout !

= Usages avancés :
pour utiliser un autre framework que celui distribué avec SPIP

prérequis :

1. Déposez les fichiers CSS dans /squelettes/css
2. Appliquez la nomenclature Daisy pour nommer les fichiers CSS
3. Définissez vos styles perso dans custom.css
4. Activez le compactage des feuilles de style (CSS) dans SPIP

* Activez un framework distribué via plugin SPIP
/!\ À TESTER /!\
compatible avec :
- Tiny Typo
- compatibilité à assurer avec Base CSS ? ou abandonner ?

* Si vous utilisez un framework téléchargé sur Internet :
déposez le code du framework dans base.css

* Si vous construisez votre framework modulaire :
Déposez les fichiers CSS du framework dans /squelettes/css
en appliquant la nomenclature Daisy

* Si vous utilisez un préprocesseur (tel que LESS ou SASS)
Vous faites bien comme vous voulez, mais :
Générez les fichiers CSS dans /squelettes/css
en appliquant la nomenclature Daisy
Typiquement 2 fichiers générés suffisent : base.css et custom.css

/* end */