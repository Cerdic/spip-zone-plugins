## Des dates plus ou moins floues

Ce plugin rend disponible en plugin le filtre fuzzydate. Pour plus de doc voir : https://contrib.spip.net/Une-date-plus-ou-moins-floue


## Des dates progressives (affdate_progressive)

On tente d'améliorer les filtres de date de Spip et le filtre FuzzyDate à partir des pratiques en usage dans la presse :

* heure et minute de la publi si c'est dans la journée. Par ex. "Mise à jour à (ou publié à) 14h38". Pas besoin de préciser aujourd'hui, c'est sous-entendu par défaut. ll'espace entre les nombres et le "h" est une espace fine et insécable ;
* heure et "hier" si c'est hier (>2). Par ex. "Mise à jour à 14 h hier". On n'a plus besoin d'indiquer les minutes. L'espace entre le nombre et le h est une espace normale insécable ;
* heure et nom du jour si c'est depuis moins de 7 jours (>2 et <8). Par ex. "Mise à jour à 14 h, lundi". Pas besoin de préciser "dernier" comme dans le filtre FuzzyDate : s'il n'y a pas de date indiquée, c'est que c'est le dernier ;
* nom du jour, numéro du jour et nom du mois si c'est dans les 30 derniers jours. Par ex. "Mise à jour vendredi 9 mars, le nom du jour permettant de capter plus rapidement de quel jour on parle, plus besoin de préciser l'heure) ;
* numéro du jour et nom du mois si c'est dans les 365 derniers jours. Par ex. "Mise à jour le 10 décembre". D'une part pas besoin de préciser l'année, si c'est pas précisé on suppose de suite qu'il s'agit du 10 décembre le plus proche de nous, plus besoin de préciser le nom du jour qui ne représente plus rien vu que c'est trop loin (plus de 30 jours) ;
* numéro du jour, nom du mois et année si c'est plus vieux. Par ex. "Mise à jour le 10 décembre 2016".


## Des dates adaptées aux mises à jour

 Le filtre |affdate_majs affiche

 * l'heure puis le jour si c'est y'a moins de 8 jours
 * le nom du jour, le numéro et le mois si c'est y'a moins de 30 jours

 Il est recommandé pour l'affichage des majs en page d'accueil. 



## Utilisation dans les squelettes

* Si on veut utiliser le filtre fuzzydate, on insère par ex. : ````[(#DATE|affdate_fuzzy)]````
* Si on veut utiliser le filtre affdate_progressive (doc ci-dessus : https://contrib.spip.net/Une-date-plus-ou-moins-floue), on insère par ex. : ````[(#DATE|affdate_progressive)]````
