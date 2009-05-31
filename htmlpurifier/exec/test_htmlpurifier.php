<?php

function exec_test_htmlpurifier_dist() {

	include_spip('inc/texte');
	
	$texte="{{{Le texte préexiste à la mise en page}}}

C'est peut-être une évidence, mais pour mettre en page un texte, il faut que le texte existe.

Dans le cas de SPIP, ça veut dire qu'il vaut mieux (au moins dans un premier temps):

-* disposer de tout le texte sans aucun raccourcis typographique,
-* sauter une ligne à chaque changement d'idée (ce qui donne un changement de paragraphe, voire un titre),
-* faire un retour à la ligne avant chaque élément d'une énumération.

Ce n'est qu'ensuite que les raccourcis typographiques de SPIP pourront être appliqués avec discernement.

{{{Distinction entre paragraphes et caractères}}}

Certains attributs typographiques ne peuvent s'appliquer qu'à des paragraphes entiers, d'autres doivent être appliqués à des caractères dans le {{même}} paragraphe.

<doc195|center>

Dans la Barre Typographique de SPIP, les attributs de caractères forment le premier groupe sur la gauche, les attributs typographiques de paragraphes le deuxième.[definition_ancre<-]

{2{Paragraphes}2}

Un paragraphe dans SPIP est précédé d'une ligne vide et suivi d'une ligne vide[[Sauf les listes à puce et les tableaux]].

Une règle générale est de ne mettre qu'un attribut de paragraphe par paragraphe.

Si deux paragraphes de suite ont le même attribut, il faut appliquer {{deux}} fois l'attribut, une fois pour chaque paragraphe.

Les attributs de paragraphe sont :

-* les titres <code>{</code><code>{{</code>Paragraphe du titre<code>}}</code><code>}</code> et sous-titres <code>{n{</code>Texte du titre<code>}n}</code>, n variant de 2 à 5, la barre de raccourcis ne proposant que 2 et 3.
_ [*{{Attention}}*]: il est essentiel de respecter la {{hiérarchie}} de la titraille et de ne pas commencer par un élément sans qu'il soit précédé de son niveau supérieur (on ne doit pas commencer à 2 !). Voir les exemples de [titraille->#titraille]

-* centrer <code>[|</code>Paragraphe centré<code>|]</code> : à n'utiliser que de manière {{exceptionnelle}}[[J'avais mis ça en place à l'époque de la version 1.7 de SPIP qui gérait mal le centrage des images]] !

[|Paragraphe centré|]

-* aligner à droite <code>[/</code>Paragraphe aligné à droite<code>/]</code> : essentiellement pour mettre la signature d'un auteur

[/Paragraphe aligné à droite/]

-* encadrer <code>[(</code>Paragraphe à encadrer<code>)]</code>

[(Paragraphe à encadrer)]

Certains attributs sont un peu spéciaux :

-* Poésie <code><poesie></code>Le texte de la poésie, sur plusieurs lignes, les retour à la ligne simple {{étant}} pris en compte<code></poesie></code>

<poesie>Le geai gélatineux gégnait dans le jasmin
Voici mes infins le plus beau vers de la langue française.</poesie>

-* Cadre <code><cadre></code>Texte qui apparaitra dans une zone de formulaire facilitant le copier/coller[[Essentiellement utilisé sur spip-contrib pour donner des exemples de code]]<code></cadre></code>

<cadre>
Ceci est du texte dans un cadre.
      les espaces en début de ligne comptent !
Les retour à la ligne simples aussi !
</cadre>

-* Citation <code><quote></code>Texte d'une citation<code></quote></code>

<quote>C'est en forgeant que l'on devient forgeron.</quote>

{2{Caractères}2}

Les attributs de caractères {{doivent}} être ouverts et fermés à l'intérieur du même paragraphe (pas question de débuter le gras sur un premier paragraphe et de le terminer sur un deuxième).

Mise en forme:

-* gras : <code>{{</code>texte en gras<code>}}</code>; à utiliser pour un élément que l'on souhaite appuyer (sera prononcé plus fort dans un lecteur vocal) : {{texte en gras}}
-* italique : <code>{</code>italique<code>}</code>; à utiliser pour une élément sur lequel on veut insister (sera prononcé avec emphase) : {italique}
-* mise en évidence <code>[*</code>texte en évidence<code>*]</code> : élément que l'on souhaite appuyer et attirer le regard par un changement de couleur : [*texte en évidence*]
-* mise en exposant : <code><sup></code>texte en exposant<code></sup></code> : pour l'abréviation de saint : S<sup>t</sup>
-* petites capitales : <code><sc></code>texte en petite capitales<code></sc></code> : à utiliser essentiellement pour les nom propres : Charles <sc>de Gaulle</sc>
-* code : <html><tt>&lt;code&gt;</tt></html>du code (raccourcis typographiques, html...)<html><tt>&lt;/code&gt;</tt></html> que l'on ne souhaite pas que SPIP interprète
-* biffé : <code><del></code>texte biffé<code></del></code> : pour indiquer qu'on avait pensé à un autre mot et que l'on a changé d'avis : SPIP, c'est <del>bien</del> fantastique!

Comportement spécifique:

-* bulle d'aide : <code>[GPL|Gnu Public Licence]</code> : pour donner la signifation d'un terme ou d'une abréviation : [GPL|Gnu Public Licence]
-* lien : <code>[texte du lien->http://www.spip.net/]</code> : lien : [texte du lien->http://www.spip.net/]
_ À noter qu'il est possible de faire des liens à l'intérieur du site SPIP à l'aide des {{numéros}} des éléments et de leur type (se reporter à l'aide en ligne fournie par SPIP).
-* lien avec bulle d'aide : <code>[texte du lien|Le site officiel de SPIP->http://www.spip.net/]</code> : [texte du lien|Le site officiel de SPIP->http://www.spip.net/]
-* lien avec langue des destination (non visible sur Internet Explorer) : <code>[texte du lien|{fr}->http://www.spip.net/]</code> : [texte du lien|{fr}->http://www.spip.net/]
-* lien avec bulle d'aide et angue des destination : <code>[texte du lien|Le site officiel de SPIP{fr}->http://www.spip.net/]</code> : [texte du lien|Le site officiel de SPIP{fr}->http://www.spip.net/]
-* ancre et retour à l'ancre : <code>[definition_ancre<-]</code> et <code>[retour à l'ancre->#definition_ancre]</code> : [retour à l'ancre->#definition_ancre]
-* définition dans Wikipedia : <code>[?GPL]</code> : appelle l'encyclopédie libre Wkipedia pour obtenir la définition du mot[[Si le mot n'existe pas, vous pouvez le créer vous-même!]] : [?GPL]
_ Avec bulle d'aide : <code>[?GPL|Définition sur Wikipédia]</code> : [?GPL|Définition sur Wikipédia]
-* note de bas de page : <code>texte[[note de bas de page]]</code> : crée une note de bas de page avec le texte entre les doubles crochets[[Et la note de bas de page est automatiquement numérotée, rendue clicable, pour la consulter, et pour revenir au texte l'ayant appelée]]

{2{Listes}2}

Les listes sont à utiliser pour tout ce qui à le {{sens}} d'une énumération.

{{Attention}}: il faut entourer un bloc de listes à puces d'une ligne vide avant et après.

{3{Listes à puces}3}

<cadre>
-* première ligne
-* deuxième ligne
-** une sous liste à puce
-* de retour dans le niveau initial
</cadre>

Donnera :

-* première ligne
-* deuxième ligne
-** une sous liste à puce
-* de retour dans le niveau initial

{3{Listes numérotées}3}

<cadre>
-# première ligne
-# deuxième ligne
-## une sous liste à puce
-# de retour dans le niveau initial
</cadre>

Donnera :

-# première ligne
-# deuxième ligne
-## une sous liste numérotée
-# de retour dans le niveau initial

{2{Tableaux}2}

Pour être complètement accessible, un tableau dans SPIP doit avoir un titre et une description.

Ainsi :

<cadre>
||Produits bio et prix|Ce tableau sert d'exemple de mise en forme spip||
| {{Produit}} | {{Prix €}} |
| Beurre Bio | 5€ |
| Lait Bio | 3€ |
| Choux Bio | 4€ |
</cadre>

Donnera :

||Produits bio et prix|Ce tableau sert d'exemple de mise en forme spip||
| {{Produit}} | {{Prix €}} |
| Beurre Bio | 5€ |
| Lait Bio | 3€ |
| Choux Bio | 4€ |

Notez les doubles <code>||</code> sur la première ligne du tableau !

[*Attention*]: les pièges classiques avec les tableaux sont :

-* ne pas avoir le même nombre de | sur une ligne
-* avoir un espace {{après}} le dernier | de la ligne (un moyen simple de vérifier : la touche fin du clavier amène à la fin de la ligne)

{2{Tableaux avec fusion de cellules}2}

<cadre>
||Tableau avec fusion|Ce tableau sert d'exemple de mise en forme spip||
| {{Colonne 1}} | {{Colonne 2}} | {{Colonne 3}} |
| ligne 1 | Cellule fusionnée avec la suivante |<|
| ligne 2 | Celulle fusionnée
_ avec celle du sessous | normale |
| ligne 2 |^| normale aussi |
</cadre>

Donnera :

||Tableau avec fusion|Ce tableau sert d'exemple de mise en forme spip||
| {{Colonne 1}} | {{Colonne 2}} | {{Colonne 3}} |
| ligne 1 | Cellule fusionnée avec la suivante |<|
| ligne 2 | Celulle fusionnée
_ avec celle du dessous | normale |
| ligne 2 |^| normale aussi |

{{{Images}}}

Pour les images et documents, reportez-vous à l'aide en ligne de SPIP. Seule contrainte pour l'accessibilité (et donc un meilleur référencement) : donnez un titre à {{toutes}} vos images décrivant le {{[sens|signification]}} de chacune d'elles.

{{{Caractères spéciaux}}}

-* <code>~</code> (espace insécable ou espace dur -- correspond au <code>&nbsp;</code> du [HTML|Hyper Text Markup Language]) placé entre deux mots remplace l'espace en ayant l'avantage d'être insécable, c'est-à-dire, qu'il empêchera les deux mots d'être séparés par un retour à la ligne malvenu. S'utilise en particulier entre le prénom et le nom propre.
-* <code>--></code> : --> (flèche vers la droite)
-* <code><--</code> : <-- (flèche vers la gauche)
-* <code><--></code> : <--> (flèche vers la gauche et vers droite)
-* <code>--</code> : -- (tiret cadratin) à utiliser pour les incises dans un texte

{{{Ligne horizontale}}}

<code>----</code>: 4 signes moins en seuls sur une ligne (précédés d'une ligne vide et suivis d'une ligne vide) donneront un trait de séparation horizontal.

---- 

{{{Éléments dangereux}}}

Il y a deux éléments {{dangereux}} dans SPIP :

-* le retour à la ligne simple : <code>_ </code> (souligné espace) en début de ligne.
_ Usage toléré pour donner adresse et numéro de téléphone/fax.
_ Usage toléré : dans une liste à puce pour passer à la ligne sans passer à une nouvelle puce (comme ici).
_ Usage {{[*interdit*]}}: pour mettre plus d'espace vertical entre deux éléments de la page.
-* le [?HTML] pur : il est {possible} dans SPIP de mettre du code [HTML|Hyper Text Markup Language]. Le faire est fortement déconseillé :
-** parce que c'est la porte ouverte à toutes les dérives, ne serait-ce que celle de sortir de la charte graphique du site, ou celle de produire un code HTML non valide (voire non interprétable ailleurs que sur [Internet Explorer->115]
-** parce que c'est partir du postulat que votre site ne sera visité qu'en tant que site web ; il pourrait très bien être un jour disponible sous forme de fichier PDF...

[titraille<-]

{{{Exemples de titraille : Titre principal}}}

<code>{</code><code>{{</code>Exemples de titraille : Titre principal<code>}}</code><code>}</code>

{2{Titre niveau deux}2}

<code>{</code><code>2{</code>Titre niveau deux<code>}2</code><code>}</code>

{3{Titre niveau trois}3}

<code>{</code><code>3{</code>Titre niveau trois<code>}3</code><code>}</code>

{4{Titre niveau quatre}4}

<code>{</code><code>4{</code>Titre niveau quatre<code>}4</code><code>}</code>

{5{Titre niveau cinq}5}

<code>{</code><code>5{</code>Titre niveau cinq<code>}5</code><code>}</code>";
	$time_start = microtime(true);
	for ($i = 1; $i <= 1; $i++) {
		$resultat = safehtml(propre($texte));
	}
	$time_end = microtime(true);
	$time = $time_end - $time_start;

	echo "Temps d'ex&eacute;cution $time seconds\n";
	
	echo $resultat;
}
?>