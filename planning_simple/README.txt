Ce plugin permet de créer et imprimer un emploi du temps scolaire ou autre

Le plugin planning simple permet d'afficher une table (table/tr/td) en html de deux manières

MODE EVENEMENTS AGENDA
- Soit les évènements d'un article sur une durée n'excédant pas une semaine.
Pour tester, vous trouverez une base d'evenements en MySQL à entrer via par exemple PHPmyadmin, 
Attention de changer auparavant l'id_article 13 dans le fichier Mysql par le numéro d'article de votre choix.
Rendez-vous ensuite sur la page example.com/?page=planning_simple&id_article=67 <- 67 est le numéro d'article auquel sont attachés les évènements
Vous pouvez ensuite ajouter/modifier les évènements de l'article. 
Vous pouvez également changer le titre et le lieu avec le crayon.
Les noms des jours sont multilingues, vous pouvez ajouter dans l'url de la page la valeur &lang=en pour les afficher en anglais

MODE EVENEMENTS CSV
- Soit une liste d'évènements entrée dans le texte de l'article 
EXEMPLE:
lundi,08:00,10:00,Philo,253,#E7F2A9
lundi,10:00,12:00,Hist-Géo,451,#A1E0D3
lundi,13:00,14:00,AP, ,#888
lundi,14:00,15:00,SES,1x par mois,#FFA3A3
Mardi,08:00,10:00,EPS, ,#E0E0E0
Mardi,10:00,12:00,SES,452,
Mardi,13:30,15:00,Sciences Politiques,453,#F7996A
Mardi,15:00,16:00,Anglais, ,#FCA4F3
Mardi,16:00,18:00,Allemand, ,#F7AFC1
Mercredi,08:00,10:00,Maths,412,#9B9FC1
Mercredi,10:00,12:00,Philo,352,
Jeudi,08:00,10:00,SES,253,
Jeudi,10:00,11:00,Anglais,253,
Jeudi,11:00,12:00,AP,A 451,
Jeudi,11:00,12:00,AP,B 244,
Jeudi,13:00,14:00,AP,B 312,
Jeudi,13:00,14:00,EMC,A 252,
Vendredi,08:00,10:00,Hist-Géo,453,
Vendredi,10:00,12:00,Maths,413,
Rendez-vous ensuite sur la page example.com/?page=planning_simple&id_article=67 <- 67 est le numéro d'article dont le texte comporte une liste d'évènements de type CSV.
SI vous êtes connecté, vous pouvez modifier la liste sous le tableau sur la page publique avec le plugin crayon 
Les noms des jours sont multilingues, vous pouvez ajouter dans l'url de la page la valeur &lang=en pour les afficher en anglais
La 6em colonne permet de déterminer une couleur de fond pour les cellules de même nom, l'indiquer une seule fois suffit, hexadecimale ou string


AUTRES USAGES 
On peut aussi imaginer l'appliquer à une série d'articles, en ajoutant des flèches suivant/précédent ou pour un festival de 4 jours.
Une plage horaire de ce planning ne peut être divisé que en deux demie/heure et sur 2 lieux en même temps.
On peut déterminer un fond en background avec la photo d'un chaton.

CSS
Chaque évènement dispose d'une class avec son nom sans accents, ainsi les cases français portent la class .francais, à vous de styler la css perso.css en repérant les class.

------ TODO -----
- DONE passer le tout en date simple à entrer, genre lundi 9h30 + durée et non pas 12-01-2014 30:00:00
- DONE au besoin rechercher le 1er lundi de l'année en cours pour avoir une date mysql valable. (vérifier si c'est une nécessité d'écriture)
- créer un système de crayons / vues comprenant le titre, la date simple et la durée. Dans les cases vides, il faudrait que le crayon prenne les minutes de la case en référence pour le traitement d'une nouvelle entrée.
- permettre la coloration CSS des matières avec palettes
- DONE ajouter au besoin un formulaire d'écriture facile et rapide avec 1 ligne/jour
- faire une sauvegarde json en session, pour créer un service en ligne personnalisé

- pouvoir imprimer en gris N&B (désaturer avec un bouton)
- pouvoir imprimer en N&B (Virer les fonds)

- DONE --------- réécrire les dates simplement ----
Les passer en 1er lundi ou 1er dimanche de 1970 + horaires

lundi,08:00,09:00,Français,252,#999999
lundi,11:00,12:00,SES,244,#333333
lundi,13:00,15:00,Allemand,244
lundi,15:00,16:00,Anglais,,1x par mois

