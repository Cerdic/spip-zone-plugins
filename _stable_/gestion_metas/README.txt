Ce plugin ajoute, dans la colonne de gauche des pages rubriques (exec=naviguer), articles (exec=articles) et brèves (exec=breves_voir), un formulaire dans les pages  dans la colonne centrale, qui permet d'éditer les informations de la table.


Le plugin créé automatiquement la table nécessaire à son fonctionnement.



Une fois le plugin installé, il vous suffit d'insérer dans l'en-tête de vos pages le code [(#INCLURE{fond=includes/gestion_metas}{id_breve})] pour les breves, [(#INCLURE{fond=includes/gestion_metas}{id_article})] pour les articles et [(#INCLURE{fond=includes/gestion_metas}{id_rubrique})] pour les rubriques. Ce code insérera automatiquement le squelette du plugin contenu dans includes/gestion_metas.html.

Pour, par exemple, la page sommaire, on pourra utiliser [(#INCLURE{fond=includes/gestion_metas}{id_rubrique=0})] (on éditera la rubrique 0 dans l'admin en changant l'URL).



Ce squelette suppose que votre table s'apelle spip_gestion_metas, MAIS, si vous avez spécifié un autre préfixe à vos tables, la table créée s'appellera PREFIXE_gestion_metas, vous devrez modifier le squelette d'affichage en conséquences.



L'amélioration de ce plugin peut être faite par quiconque, *NEANMOINS*, merci de discuter de ces améliorations avec son auteur premier, Olivier G. (<o.gendrin@novactive.com> ou <olivier.gendrin@free.fr>) avant tout commit. Vous devez aussi renseigner les fichiers todo.txt et changelog.txt.
