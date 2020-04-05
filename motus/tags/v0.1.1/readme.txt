
Notes de l'auteur,
Pour ne pas qu'on l'engueule trop tôt !

# Objet du plugin

Ce plugin a pour idée simple que certains groupes de mots ne sont utiles
que sur certaines rubriques de SPIP. Une rubrique n'a peut-être
pas a proposer pour ses articles les mêmes groupes de mots qu'une autre
rubrique... Pour les rédacteurs, il est moins perturbant de ne proposer d'attacher
que les groupes intéressant leur article.

Cela dit, ce plugin a donc pour simple objectif de cacher la possibilité
de lier certains groupes à certaines rubriques (et leurs enfants et objets).
Il ajoute, dans l'édition d'un groupe de mot un sélecteur pour choisir
ces restrictions.


# Contexte

L'affichage et les autorisations de l'espace privé de SPIP
sur les mots et groupes de mots étant un code assez
ancien, il est difficile de réaliser l'objet du plugin
sans surcharger les fichiers du Core... Or par principe,
je me refuse à cela.

L'idéal eut été de corriger avec parcimonie le code de SPIP
pour pouvoir réaliser ce plugin sans les soucis annotés ci-dessous,
mais je préfère clairement attendre qu'on reprenne entièrement
le code des mots / groupes de mots dans SPIP, qui est un plus gros chantier,
et corriger les problèmes à ce moment là.



# Limitations et problèmes connus :

+ Les restrictions des groupes à certaines rubriques ne sont pas visible
  sur la page ?exec=mots_tous.
  
	  Il n'y a pas le pipeline sur l'affichage
	  de l'objet groupes_mot donc champs extras ne peut pas montrer le champ)


+ Lorsqu'on est sur un article, on peut créer un mot
  (même d'un groupe caché) et l'attacher à cet article.
  
	  Or à cette création, tous les groupes de mots sont
	  présents, même ceux restreint à d'autres lieux.
	  L'inclusion prive/formulaires/selecteur_groupe_mot n'a pas connaissance
	  du contexte d'appel (id_article ou rubrique) ; on ne peut donc pas
	  uniquement le surcharger comme ça.


+ Lorsqu'un mot clé d'un groupe restreint est attribué à un objet
  (par exemple en créant un mot clé avec le point précédent),
  on ne peut plus supprimer la liaison !
  
	  Effectivement, l'autorisation du bouton de suppression, et l'autorisation
	  d'attribuer un groupe sont exactement les mêmes appels ; si bien que
	  lorsqu'on cache un groupe donné, on cache aussi le lien pour délier
	  un mot de ce groupe.
	  => il faut spécialiser les autorisations en leur passant un argument
	  supplémentaire pour désigner le type d'action. Une donnée en plus dans $opt
	  par exemple.

