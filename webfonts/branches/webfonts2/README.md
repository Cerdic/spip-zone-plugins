# WebFonts 2

## Bénéfices

L'utilisation du webfontloader,
permet de mieux gérer le fallback utilisé lors du chargement
ou de l'échec du chargement. wia des class `.wf-active`

## Configuration:

Pour pouvoir afficher les fonts via l'api GoogleFont, il est nécessaire d'avoir
préalablement récupéré une clef d'accès aux API Google et sélectionné GoogleFont dans
le APIs intérrogeables.

Cette clef, peut être définie depuis un squelette ou un plugin

```
define('_GOOGLE_API_KEY', 'votre_clef_google_api');
```

ou via la configuration du plugin.




- GogleAPIKey : pour le listage et l'accès a la typthèque
- TypeKit,… pour les autres fournisseurs de typo
- Jeux de typo a charger et formats (woff, woff2), on génère la requète  
depuis le html directement dans l'entête du site, pour éviter de maintenir un fichier css
- Ajout du webfont loader développé par Google/TypeKit  
https://github.com/typekit/webfontloader


## Sources & Docs

Article sur l'implémentation du webfont loader
Présentation par cssTricks du webfontLoader :
https://css-tricks.com/loading-web-fonts-with-the-web-font-loader/


## ToDo

- [] améliorer l'interface de selection : formulaire de fltrages des typos, affichage des résultats
- [] selection des typos et des subsets utilisables
- [] si la Google api key n'est pas définie, proposer dans la config