# WebFonts 2

La documentation est cours de rédaction sur le wiki de spip-contrib :
https://contrib.spip.net/WebFonts-2


## ToDo


- [ ] Quand on valide le formulaire de config, recharger la liste des fonts présentes dans la pipeline.
- [ ] pas de désinstallation des metas
- [ ] La API key n'est pas prise en compte si elle est définie depuis la config
- [ ] vérifier que l'on inssère bien une seul fois la font:variant demandée
entre les polices ajoutées via la config et celles inssérées depuis un plugin
- [ ] GogleAPIKey : pour le listage et l'accès a la typthèque : conditionner l'affichage de l'onglet de recherche,
du bouton de génération de l'index
- [ ] Intégration du webfont loader
https://github.com/typekit/webfontloader
	- [] TypeKit,… autres fournisseurs de typo
- [ ] Gestion des polices locales
- [ ] Associations Typos / Font pairing : proposer des associations de typos qui fonctionne bien ensemble
genre COmic Sans + Times


## Changelogs

0.3.2

- utiliser affichage_final pour être sur de passer avant les `<link`
- pour les fonts ajoutées par le formulaire, l'index dans le table est le nom de famille (open_sans) slug

0.3.1

- Ajout de font-display:swap à la requète ggoglefont
Comme expliqué https://www.zachleat.com/web/google-fonts-display/
google ajoute le support de font-display sur la requète google-font
https://drafts.csswg.org/css-fonts-4/#font-display-desc

- suppression d'un warning si aucune police n'est insérée via la pipeline
- ajout d'une class lazyload sur les iframes de preview au cas ou script de lazyloading est actif.

v0.3.0

Le plugin fourni un fichier googlefont_list.json, il n'est plus nécessaire d'avoir une googlefont_api_key, pour pouvoir utiliser :

- la recherche dans le catalogue googlefont.
- les selecteurgenerique webfonts

l'extension php curl n'est plus necessaire que pour la mise a jour de l'index.


v0.2.4 :

- [X] améliorer l'interface de selection : formulaire de fltrages des typos, affichage des résultats
- [X] selection des typos et des subsets utilisables
- [] si la Google api key n'est pas définie, proposer dans la config
