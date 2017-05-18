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


### Afficher le portfolio d'un article

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
			
<INCLURE{fond=noisettes/owlcarousel-article-portfolio}
		{id_article=28}
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

## TRAVAUX :


## TODO :






