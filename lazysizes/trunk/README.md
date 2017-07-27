# LazySizes pour spip


Intégration pour spip du script lazysize.

Outil / lib de lazyloading extensible via un système de plugin/addons.

Permet notamment de déférer le téléchargement de ressources (images, iframes, vidéos, ..) de manière a ce que l'utilisateur
ne charge que ce qui est visible dans la fenetre de navigation, ou quand on souhaite sur un autre évenement via un sytème de hooks.

Lazysizes est un script de lazyloading rapide et sans dépendance a un framework d'images
(incluant les images responsives picture/srcset), les iframes, les scripts/widgets, …
Il agit sur la priorité des pré-chargement en différenciant les éléments
suivant la distance plus ou moins importante du champ visible, afin d'augmenter l'impression de rapidité.

LazySizes peut aussi devenir votre outil numéro 1 pour intégrer à votre site les images responsive.

LazySizes peut calculer automatiquement la taille et le ratio de vos images.

Il peut vous permettre de partager vos media queries entre vos css et les attributs de vos medias,
aidant ainsi à séparer votre layout (CSS) de votre contenu/structure (HTML),
rendant ainsi l'intégration des images responsive dans n'importe quel environement ou contexte beaucoup plus simple.

Il inclu aussi un système de plugin, permettant d'étendre ses fonctionnalitées.


## Pourquoi LazySize


Lazysizes est conçut différement des autres scripts de lazyload d'image.

Il detecte automatiquement les changement de visibilité affectant l'élément courrant a précharger
ainsi que les suivant, et ce dans n'importe quel environement :

Le script fonctionne de manière autonome, auto initialisé, auto configuré,
de manière universelle, désactivant ou adaptant les composants future a précharger images ou iframes
en fonction de leur visibilité, que ce soit via un défilement utilisateur,
une animation css déclenchée par un changement d'état (:hover)
ou par un événement/action js (carousel, slider, masonry, isotope, filtrage, Ajax).

Il fonctionne aussi automatiquement en conjonction avec n'importe quel JS/CSS/front end framework
(jQuery mobile, Bootstrap, Backbone, Angular, React, Ember (voir si besoin l'extension attrchange/re-initialization )).


**À l'épreuve du futur** :

Inclue nativement le chargement différé sur les images responsives picture/srcset.

**Separation of concerns** :

For responsive image support it adds an automatic sizes calculation as also alias names for media queries feature.
There is also no JS change needed if you add a scrollable container with CSS (overflow: auto)
or create a mega menu containing images.

**Performance** :

It's based on highly efficient, best practice code (runtime and network)
to work jank-free at 60fps and can be used with hundreds of images/iframes on CSS and JS-heavy pages or webapps.
	
**Extendable** :

It provides JS and CSS hooks to extend lazysizes with any kind of lazy loading, lazy instantiation,
in view callbacks or effects (see also the available plugins/snippets).

**Intelligent prefetch/Intelligent resource prioritization** :

lazysizes prefetches/preloads near the view assets to improve user experience,
but only while the browser network is idling (see also expand, expFactor and loadMode options).
This way in view elements are loaded faster and near of view images are preloaded lazily before they come into view.

**Lightweight, but mature solution**:

lazysizes has the right balance between a lightweight and a fast, reliable solution.

**Amélioration SEO**:

Lazysize ne masque pas les images/ressources à google ou aux autre robots des moteurs de recherche.
Ces robots ne défilent pas dans la page et n'interragissent pas avec votre site.
Lazysize detecte, si l'agent utilisateur (user agent) est capable de défiler
dans la page et révèle dans ce cas les contenus/ressources immédiatement.




## Addons :

### respimg polyfill plugin

Polyfill alternatif léger pour les images responsives (picture et image src-set).


### OPTIMUMX plugin

The srcset attribute with the w descriptor and sizes attribute
automatically also includes high DPI images.
But each image has a different optimal pixel density,
which might be lower (for example 1.5x) than the pixel density
of your device (2x or 3x).
This information is unknown to the browser and
therefore can't be optimized for. The lazySizes optimumx extension gives you more control to trade between perceived quality vs. perceived performance.

### object-fit extension

The object fit plugin polyfills the object-fit and the object-position property in non supporting browsers.

### unveilhooks plugin

The unveilhooks plugin plugin enables lazySizes to lazyload background images, widgets/components/scripts, styles and video/audio elements.

### include plugin

The include plugin plugin enables lazySizes to lazyload content, styles or AMD modules either simply postponed or conditionally (for example matching certain media queries). This extension also heavily simplifies the architecture of conditional, dynamically changing responsive behavior and has great scalability.

### bgset plugin - lazy responsive background-image

The bgset plugin allows lazyloading of multiple background images with different resolutions/sizes and/or media queries (responsive background images). In case you only need one image use the unveilhooks extension.

### lazysizes custommedia extension

lazySizes custommedia extension allows you to automatically sync and manage your breakpoints between your CSS and the media attributes of your "picture > source" elements using the customMedia option of lazySizes.

### attrchange / re-initialization extension

In case you are changing the data-src/data-srcset attributes
of already transformed lazyload elements, you must normally also re-add the lazyload class to the element.

This attrchange / re-initialization extension automatically
detects changes to your data-* attributes and adds the class for you.

### parent-fit extension

The parent fit plugin extends the data-sizes="auto" feature to also calculate the right sizes for object-fit: contain|cover image elements and other height ( and width) constrained image elements in general.

### unload extension

The unload extends lazysizes to unload not in view images to improve memory
consumption and orientationchange/resize performance.

### noscript extension

The noscript extension is the ultimate progressive enhancement
extension for lazySizes. It allows you to transform any HTML
inside a noscript element as soon as it becomes visible.

### aspectratio extension

The aspectratio extension allows you to control the aspectratio
of your images using markup instead of CSS. It is an alternative
for the CSS intrinsic ratio technique.

### print plugin

The print plugin plugin enables lazySizes to unveil all elements
as soon as the user starts to print.
(Or set lazySizesConfig.preloadAfterLoad to true).

### progressive plugin

The progressive plugin adds better support for rendering progressive jpgs/pngs.

### RIaS plugin - (Responsive Images as a Service / Responsive image on demand)

The RIaS plugin is a neat full responsive images solution
without the need of any additional plugins/polyfills.

It enables lazySizes to generate the best suitable
image source based on an URL pattern.
It works with pre-build images (i.e. grunt-responsive-images)
as also with any third party (ReSrc, Pixtulate, mobify, WURFL's Image Tailor ...)
or self hosted restful responsive image service (responsive image on demand).
It makes responsive images even more easier without any need for another third party script.


## Sources et docs

https://github.com/aFarkas/lazysizes


## @todo


- [-] Inclure les plugins lazysize depuis un panneau de config ou un define
puis les charger dans insert_head
	- [] Inclure les plugins lazysize depuis un define
	- [] ajouter les scripts dans le privé vu que les modèle sont dipponibles
- [] 	preview des modeles dans l'espace privé
		par exemple : `<picture5|id_document=5>`
		
- [X] config export ie_config()
- [] traduire la doc 
- [] Ajouter via define ou config les options de configuration
- [] surcharger les modèles media, interressants a "lazyloader"
- [] Chaines de langue, et docs/aide des addons