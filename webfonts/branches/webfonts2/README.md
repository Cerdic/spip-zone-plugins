# WebFonts 2

Le chargement de webfonts peut impacter sur le rendu des pages,
sur l'accessibilité au contenus, sur les performances de votre page.
Il conviens donc d'en être conscient et de les utiliser en connaissance de cause.

Outre les considérations d'ordre esthétique et typographique,
il est recommandé de ne pas utiliser plus de deux ou trois variantes de fonts,
afin de ne pas détériorer les performances de chargement de vos pages.

Le plugin inssert dans le head et en tête des autres fichiers css
les balise link ou style nécessaires. La requète vers google font ne necessite pas
d'accès a l'api.



La rêgle css `@font-face` : 
https://developer.mozilla.org/fr/docs/Web/CSS/@font-face


```css
/* latin */
@font-face {
  font-family: 'Open Sans';
  font-style: italic;
  font-weight: 400;
  src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v15/xjAJXh38I15wypJXxuGMBogp9Q8gbYrhqGlRav_IXfk.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2212, U+2215;
}
```


## Techniques de chargement de fonts :

Les stratégies de chargement de font comparées par 
https://www.zachleat.com/web/comprehensive-webfonts/


### Google webfont :

utilisation du service google web font, via une requète


### Depuis les css :



## Fonctionalitées

* insertion auto 

## 

* Ajoute une pipeline pour que les plugins theme puisse activer leur insertions



En cours :
- Proposer un formulaire de recherche de typos et leurs selection pour utilisation
avec gestion des variantes





## Configuration:

### GoogleFonts API Key

Pour pouvoir afficher les fonts via l'api GoogleFont, il est nécessaire d'avoir
préalablement récupéré une clef d'accès aux API Google et sélectionné GoogleFont dans
le APIs intérrogeables.

Cette clef, peut être définie depuis un squelette ou un plugin

```
define('_GOOGLE_API_KEY', 'votre_clef_google_api');
```

ou via la configuration du plugin.


### Méthode d'insertion

Propose plusieurs techniques d'intégration/chargement de webfont

* insertion standard via la balise `<link>`
* insertion @import via une balise `<style>`


## Utilisation

Description d'une font dans la font list :

* family : Nom de la famille de police
* variants : italic,bold
* subsets : les subsets sont nécessaires uniquement pour les navigateurs ne supportant pas la proprité unicode-range,
pour les autres les subsets sont ignorés, et le navigateur choisira ce qui est necessaire dans le DOM.
Explication sur les subsets : https://developers.google.com/fonts/docs/getting_started#Subsets
Navigateurs supportant `unicode-range : https://caniuse.com/#feat=font-unicode-range


### Depuis un squelette

via le fichier `mes_options.php`

```php
// Exemple d'ajout dans le pipeline "fonts_list" :
$GLOBALS['spip_pipeline']['fonts_list'] .= "|skel_webfonts";
 
function skel_webfonts($fonts) {
	$fonts = array(
		'0'=> array(
			'family'=> 'Open Sans',
			'variants'=> array('300','300italic','regular','italic','600')
		),
		'1'=> array(
			'family'=> 'Montserrat',
			'variants'=> array('regular','800')
		)
	);
    return $fonts;
}
```

### Depuis un plugin

Utilisation de la pipeline `fonts_list()`.

Ajouter au `paquet.xml`

```xml
<pipeline nom="fonts_list" inclure="prefix_plugin_pipelines.php" />
```


```php

function prefix_plugin_fonts_list($fonts){
	$fonts = array(
		'0'=> array(
			'family'=> 'Open Sans',
			'variants'=> array('300','300italic','regular','italic','600'),
			'subsets'=>array()
		),
		'1'=> array(
			'family'=> 'Roboto Condensed',
			'variants'=> array('700','800'),
			'subsets'=>array()
		)
	);
	
	return $fonts;
}
```



----


- GogleAPIKey : pour le listage et l'accès a la typthèque
- TypeKit,… pour les autres fournisseurs de typo
- Jeux de typo a charger et formats (woff, woff2), on génère la requète  
depuis le html directement dans l'entête du site, pour éviter de maintenir un fichier css
- Ajout du webfont loader développé par Google/TypeKit  
https://github.com/typekit/webfontloader


## Sources & Docs

https://developers.google.com/fonts/docs/developer_api

Article sur l'implémentation du webfont loader
Présentation par cssTricks du webfontLoader :
https://css-tricks.com/loading-web-fonts-with-the-web-font-loader/

Google font : webfont loader https://github.com/typekit/webfontloader

ZACH LEATHERMAN (Filament Group):
webfont loading strategies : https://www.zachleat.com/web/comprehensive-webfonts/

Etapes du chargement d'une font dans le navigateur :
https://font-display.glitch.me/

### Des plugins spip ou techniques d'intégration d'utilisation de font/polices

Plugin spip permettant d'utiliser les images typographiques via un modèle
https://contrib.spip.net/Choix-Police-Typo

Le filtre image_typo :

https://www.spip.net/fr_article3325.html



### Typothèques

https://fontlibrary.org/
https://www.fontsquirrel.com/tools/webfont-generator



## ToDo

- [] améliorer l'interface de selection : formulaire de fltrages des typos, affichage des résultats
- [] selection des typos et des subsets utilisables
- [] si la Google api key n'est pas définie, proposer dans la config