Ce plugin ajoute, dans la colonne de gauche des pages rubriques (exec=naviguer), articles (exec=articles) et br�ves (exec=breves_voir), un formulaire dans les pages  dans la colonne centrale, qui permet d'�diter les informations de la table.


Le plugin cr�� automatiquement la table n�cessaire � son fonctionnement.



Une fois le plugin install�, il vous suffit d'ins�rer dans l'en-t�te de vos pages le code [(#INCLURE{fond=includes/gestion_metas}{id_breve})] pour les breves, [(#INCLURE{fond=includes/gestion_metas}{id_article})] pour les articles et [(#INCLURE{fond=includes/gestion_metas}{id_rubrique})] pour les rubriques. Ce code ins�rera automatiquement le squelette du plugin contenu dans includes/gestion_metas.html.

Pour, par exemple, la page sommaire, on pourra utiliser [(#INCLURE{fond=includes/gestion_metas}{id_rubrique=0})] (on �ditera la rubrique 0 dans l'admin en changant l'URL).



Ce squelette suppose que votre table s'apelle spip_gestion_metas, MAIS, si vous avez sp�cifi� un autre pr�fixe � vos tables, la table cr��e s'appellera PREFIXE_gestion_metas, vous devrez modifier le squelette d'affichage en cons�quences.



L'am�lioration de ce plugin peut �tre faite par quiconque, *NEANMOINS*, merci de discuter de ces am�liorations avec son auteur premier, Olivier G. (<o.gendrin@novactive.com> ou <olivier.gendrin@free.fr>) avant tout commit. Vous devez aussi renseigner les fichiers todo.txt et changelog.txt.
