Outil de prototypage de sites sous SPIP

Pour la gestion de :

- pages 
	- id_page
	- nom_page
	- objectif
	- particularit�s
	- DA (image)
	- url
	- commentaires

- blocs
	- id_bloc
	- id_parent
	- id_page (calcul� ou id_bloc... pour le cas de bloc inclus dans un bloc)
	- nom_bloc


- liens
	- id_lien
	- id_parent (peut etre un bloc)
	- url

Balises :
---------

- liens entrants : comment on arrive sur la page ? calcul� � partir des liens pointant vers cette page
- liens sortants : vers o� l'on peut aller ? calcul� � partir des liens des blocs de la branche

Faut-il distinguer pages et blocs ?
- les blocs peuvent s'imbriquer les uns dans les autres
- les liens sont contenus dans des blocs, ex bloc nav, bloc menu (et pas dans des pages)