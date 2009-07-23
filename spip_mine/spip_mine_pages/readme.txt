Outil de prototypage de sites sous SPIP

Pour la gestion de :

- pages 
	- id_page
	- nom_page
	- objectif
	- particularités
	- DA (image)
	- url
	- commentaires

- blocs
	- id_bloc
	- id_parent
	- id_page (calculé ou id_bloc... pour le cas de bloc inclus dans un bloc)
	- nom_bloc


- liens
	- id_lien
	- id_parent (peut etre un bloc)
	- url

Balises :
---------

- liens entrants : comment on arrive sur la page ? calculé à partir des liens pointant vers cette page
- liens sortants : vers où l'on peut aller ? calculé à partir des liens des blocs de la branche

Faut-il distinguer pages et blocs ?
- les blocs peuvent s'imbriquer les uns dans les autres
- les liens sont contenus dans des blocs, ex bloc nav, bloc menu (et pas dans des pages)