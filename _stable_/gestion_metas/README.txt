Ce plugin ajoute un formulaire dans les pages rubriques (exec=naviguer), dans la colonne de gauche, et articles (exec=articles) dans la colonne centrale, qui permet d'�diter les informations de la table.


Le plugin cr�� automatiquement la table n�cessaire � son fonctionnement.



Une fois le plugin install�, il vous suffit d'ins�rer dans l'en-t�te de vos pages le code [(#INCLURE{fond=includes/gestion_metas}{id_article}{lang})] pour les articles et [(#INCLURE{fond=includes/gestion_metas}{id_rubriques}{lang})] pour les rubriques. Ce code ins�rera automatiquement le squelette du plugin contenu dans includes/gestion_metas.html.

Ce squelette suppose que votre table s'apelle ext_spip_gestion_metas, MAIS, si vous avez sp�cifi� un autre pr�fixe � vos tables, la table cr��e s'appellera ext_PREFIXE_gestion_metas (ext_ pour externe � spip), vous devrez modifier le squelette d'affichage en cons�quences.



L'am�lioration de ce plugin peut �tre faite par quiconque, *NEANMOINS*, merci de discuter de ces am�liorations avec son auteur premier, Olivier G. (<o.gendrin@novactive.com> ou <olivier.gendrin@free.fr>) avant tout commit. Vous devez aussi renseigner les fichiers todo.txt et changelog.txt.
