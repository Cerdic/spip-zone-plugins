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


- [X] Inclure les plugins lazysize depuis un panneau de config ou un define
puis les charger dans insert_head
- [X] config export ie_config()
- [] traduire la doc 
- [] Ajouter via define ou config les options de configuration
- [] surcharger les modèles media, interressants a "lazyloader"
- [] Chaines de langue, et docs/aide des addons