# OwlCarousel

Ce plugin intègre a spip la librairie OwlCarousel, permettant de mettre en place toutes sortes de carousels, sliders …

Une configuration basique permet de ne pas insérer les styles css,
si l'on souhaite les gérer indépendamment.

Aucune autre configuration n'est proposée, car chaque utilisation
à travers le site aura forcément des options différentes.

Ce plugin est donc plus un outil pour squelette,
et destiné plus particulièrement aux intégrateurs.

Les noisettes fournies, peuvent servir de point de départ
et ne conviendront certainement pas à tout les cas d'utilisation en l'état.
Il faut plus les considérer comme des extraits de code facilitant l'intégration à votre projet.


## SOURCES :

http://www.owlgraphic.com/owlcarousel/#more-demos

## Utilisation

### Un video player avec oembed

Dans un squelette :

Exemple d'utilisation avec le plugin sélections éditoriales et oEmbed.


```
<div class="selection selection_video">
		#SET{id_document,#ARRAY}
		<BOUCLE_sommaire_selection_4(SELECTIONS){identifiant=sommaire_selection_4}>
			[<h2 class="selection-title[ (#EDIT{titre})]">(#TITRE)</h2>]
			<BOUCLE_listContenus_sommaire_selection_4(SELECTIONS_CONTENUS){id_selection}{par rang, num titre, titre}>
				#SET{id_document,#GET{id_document}|push{#ID_OBJET}}
			</BOUCLE_listContenus_sommaire_selection_4>
		</BOUCLE_sommaire_selection_4>

		<INCLURE{fond=noisettes/owl-video-player}
						{id_document=#GET{id_document}}
						{dots=non}
						{afficher_thumbnails=oui}
						{env} />

</div>
```


### Afficher le portfolio d'un objet

Exemple d'affichage du portfolio d'un article avec passage de breakpoints/mediaqueries
pour les thumbnails/vignettes :

On peut passer toutes les options que l'on souhaite pour chaque breakpoints,
elles viendront surcharger les valeurs définies par défaut.

**Pour récupérer les données json dans votre squelette inclu il faut utiliser `#ENV*{ma_variable}` afin d'échapper les traitements spip**

```html
[(#SET{thumbnails_responsive, #ARRAY{
	0, #ARRAY{items,2},
	480, #ARRAY{items,3},
	768, #ARRAY{items,4},
	960, #ARRAY{items,6}
}|json_encode})]

<INCLURE{fond=noisettes/owlcarousel-objet-portfolio}
		{objet=article}
		{id_objet=28}
		{id-carousel=sommaire_hero}
		{navigation=false}
		{autoplay=true}
		{autoplayHoverPause=true}
		{lazyLoad=true}
		{thumbnails_responsive= #GET{thumbnails_responsive} }
		{env}/>
```

### Afficher les articles d'un mot-clef

Affiche le logo de l'article en fond et le titre ainsi que #INTRODUCTION

```html
<INCLURE{fond=noisettes/owlcarousel-articles_mot_full}
		{titre_mot='sommaire_carousel'}
		{id-carousel=sommaire_hero}
		{navigation=true}
		{autoplay=true}
		{autoplayHoverPause=true}
		{lazyLoad=true}
		{env}
		{ajax} />
```

### Afficher une selection_editoriale

```

<INCLURE{fond=noisettes/owlcarousel-selections_editoriales}
	{identifiant='sommaire'}
	{id-carousel=sommaire_hero}
	{navigation=true}
	{autoplay=true}
	{autoplayHoverPause=true}
	{lazyLoad=true}
	{env}
	{ajax} />

```

### Utiliser un carousel uniquement en mode mobile

Pour optimiser l'espace et la navigation en mode mobile, et profiter du mode touch, on propose pour les petits écran un carousel.

```javascript

var documentWidth = $(window).width();

var owl = $('.js-carousel'), owlOptions = {
								loop: true,
								margin: 10,
								nav: false,
								responsive:{
										0:{
												items:1,
												nav: false,
												lazyLoad:true,
										}
								}
						};

if(documentWidth <= 430){
		var owlActive = owl.toggleClass('owl-carousel owl-theme').owlCarousel(owlOptions);
}else{
		owl.addClass('off');
};

$(window).resize(function() {
		var documentWidth = $(window).width();
		if(documentWidth <= 430){
				if( owl.hasClass('off') ){
						var owlActive = owl.owlCarousel(owlOptions);
						owl.removeClass('off').toggleClass('owl-carousel owl-theme');
				}
		}else{
				if(! owl.hasClass('off')){
						owl.addClass('off')
								.toggleClass('owl-carousel owl-theme')
								.trigger('destroy.owl.carousel');
				}
		}
});

```



## TODO :

- Améliorer l'accessibilité et eviter les alertes axe
	https://codepen.io/AlexRebula/pen/yOjVvY
	https://github.com/rtrvrtg/owlcarousel2-a11ylayer
