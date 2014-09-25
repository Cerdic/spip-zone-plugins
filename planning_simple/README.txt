Le plugin planning simple permet d'afficher dans une table en dur les évènements d'un article sur une durée n'excédant pas une semaine.
Ce plugin a été codé à l'origine pour pouvoir créer et imprimer un emploi du temps scolaire.
On peut aussi imaginer l'appliquer à une série d'articles, en ajoutant des flèches suivant/précédent ou pour un festival de 4 jours.
Ce planning ne peut être divisé que par demie/heure et sur 2 lieux en même temps.
Chaque évènement à une class avec son nom sans accents, ainsi les cases français portent la class .francais, à vous de styler la css perso.css en repérant les class.

Pour tester, vous trouverez une base d'evenements en MySQL à entrer via par exemple PHPmyadmin, Attention de changer auparavant l'id_article 13 par le numéro d'article de votre choix.

Vous pouvez ensuite ajouter/modifier les évènements de l'article. 
Vous pouvez également changer le titre et le lieu avec le crayon.


------ TODO -----
- passer le tout en date simple à entrer, genre lundi 9h30 + durée
et non pas 12-01-2014 30:00:00
au besoin rechercher le 1er lundi de l'année en cours pour avoir une date mysql valable. (vérifier si c'est une nécessité d'écriture)
- créer un système de crayons / vues comprenant le titre, la date simple et la durée. Dans les cases vides, il faudrait que le crayon prenne les minutes de la case en référence pour le traitement d'une nouvelle entrée.
- permettre la coloration CSS des matières avec palettes
- ajouter au besoin un formulaire d'écriture facile et rapide avec 1 ligne/jour
- faire une sauvegarde json en session, pour créer un service en ligne personnalisé

- pouvoir imprimer en gris N&B (désaturer avec un bouton)
- pouvoir imprimer en N&B (Virer les fonds)

--------- réécrire les dates simplement ----
Les passer en 1er lundi ou 1er dimanche de 1970 + horaires

lundi,08:00,09:00,Français,252
lundi,11:00,12:00,SES,244
lundi,13:00,15:00,Allemand,244
lundi,15:00,16:00,Anglais,,1x par mois

