Ce plugin ajoute un formulaire dans les pages rubriques (exec=naviguer), dans la colonne de gauche, et articles (exec=articles) dans la colonne centrale, qui permet d'éditer les informations de la table.


Le plugin créé automatiquement la table nécessaire à son fonctionnement.



Une fois le plugin installé, il vous suffit d'insérer dans l'en-tête de vos pages le code [(#INCLURE{fond=includes/gestion_metas}{id_article}{lang})] pour les articles et [(#INCLURE{fond=includes/gestion_metas}{id_rubriques}{lang})] pour les rubriques. Ce code insérera automatiquement le squelette du plugin contenu dans includes/gestion_metas.html.

Ce squelette suppose que votre table s'apelle ext_spip_gestion_metas, MAIS, si vous avez spécifié un autre préfixe à vos tables, la table créée s'appellera ext_PREFIXE_gestion_metas (ext_ pour externe à spip), vous devrez modifier le squelette d'affichage en conséquences.



L'amélioration de ce plugin peut être faite par quiconque, *NEANMOINS*, merci de discuter de ces améliorations avec son auteur premier, Olivier G. (<o.gendrin@novactive.com> ou <olivier.gendrin@free.fr>) avant tout commit. Vous devez aussi renseigner les fichiers todo.txt et changelog.txt.
