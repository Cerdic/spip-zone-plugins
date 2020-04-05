Plugin d'Évaluations
====================

Auteurs : Cyril Marion & Matthieu Marcillaud
Licence : GNU/GPL
Début des travaux : mai 2013

Description
-----------

Ce plugin pour SPIP permet de créer des évaluations basées sur des critères spécifiques.
Chaque critère peut recevoir optionnellement une note, un commentaire, des forces et faiblesses
de la part des auteurs identifiés évaluant quelque chose.

Une évaluation s'applique sur un objet éditorial quelconque, actuellement non défini à l'avance
dans le plugin.

Installation
------------

Commencer par créer une évaluation en donnant un identifiant texte, et un titre au minimum.
Dans cette évaluation, créer au moins 1 critère d'évaluation, par exemple «Cadrage de la photo»
ou «Qualité de rédaction» en définissant pour ce critère les champs à faire remplir par les
évaluateurs.

Dans un squelette, utiliser le formulaire évaluation : `#FORMULAIRE_EVALUATION{identifiant}`
où identifiant est l'identifiant donnée à votre évaluation. Un second paramètre peut indiquer
une url de redirection après l'enregistrement de l'évaluation.

Ce formulaire peut se mettre

- soit dans une boucle de l'objet qui vous intéresse, tel que :
  `<BOUCLE_(ARTICLES){id_article}> #FORMULAIRE_EVALUATION{identifiant}`
- soit en indiquant l'objet et l'identifiant en 3e et 4e paramètre.
  `#FORMULAIRE_NOTATION{identifiant,#SELF,article,8}`


Historique
----------

v1.7.1 : correction de l'affichage des évaluations sur un objet évalué
v1.7.0 : vue de crayon pour le texte de synthèse d'évaluations
v1.6.0 : permettre de supprimer des critères d'évaluation
