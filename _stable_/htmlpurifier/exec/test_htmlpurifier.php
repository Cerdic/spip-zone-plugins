<?php

function exec_test_htmlpurifier_dist() {

	include_spip('inc/texte');
	
	$texte="{{{Le texte pr�existe � la mise en page}}}

C'est peut-�tre une �vidence, mais pour mettre en page un texte, il faut que le texte existe.

Dans le cas de SPIP, �a veut dire qu'il vaut mieux (au moins dans un premier temps):

-* disposer de tout le texte sans aucun raccourcis typographique,
-* sauter une ligne � chaque changement d'id�e (ce qui donne un changement de paragraphe, voire un titre),
-* faire un retour � la ligne avant chaque �l�ment d'une �num�ration.

Ce n'est qu'ensuite que les raccourcis typographiques de SPIP pourront �tre appliqu�s avec discernement.

{{{Distinction entre paragraphes et caract�res}}}

Certains attributs typographiques ne peuvent s'appliquer qu'� des paragraphes entiers, d'autres doivent �tre appliqu�s � des caract�res dans le {{m�me}} paragraphe.

<doc195|center>

Dans la Barre Typographique de SPIP, les attributs de caract�res forment le premier groupe sur la gauche, les attributs typographiques de paragraphes le deuxi�me.[definition_ancre<-]

{2{Paragraphes}2}

Un paragraphe dans SPIP est pr�c�d� d'une ligne vide et suivi d'une ligne vide[[Sauf les listes � puce et les tableaux]].

Une r�gle g�n�rale est de ne mettre qu'un attribut de paragraphe par paragraphe.

Si deux paragraphes de suite ont le m�me attribut, il faut appliquer {{deux}} fois l'attribut, une fois pour chaque paragraphe.

Les attributs de paragraphe sont :

-* les titres <code>{</code><code>{{</code>Paragraphe du titre<code>}}</code><code>}</code> et sous-titres <code>{n{</code>Texte du titre<code>}n}</code>, n variant de 2 � 5, la barre de raccourcis ne proposant que 2 et 3.
_ [*{{Attention}}*]: il est essentiel de respecter la {{hi�rarchie}} de la titraille et de ne pas commencer par un �l�ment sans qu'il soit pr�c�d� de son niveau sup�rieur (on ne doit pas commencer � 2 !). Voir les exemples de [titraille->#titraille]

-* centrer <code>[|</code>Paragraphe centr�<code>|]</code> : � n'utiliser que de mani�re {{exceptionnelle}}[[J'avais mis �a en place � l'�poque de la version 1.7 de SPIP qui g�rait mal le centrage des images]] !

[|Paragraphe centr�|]

-* aligner � droite <code>[/</code>Paragraphe align� � droite<code>/]</code> : essentiellement pour mettre la signature d'un auteur

[/Paragraphe align� � droite/]

-* encadrer <code>[(</code>Paragraphe � encadrer<code>)]</code>

[(Paragraphe � encadrer)]

Certains attributs sont un peu sp�ciaux :

-* Po�sie <code><poesie></code>Le texte de la po�sie, sur plusieurs lignes, les retour � la ligne simple {{�tant}} pris en compte<code></poesie></code>

<poesie>Le geai g�latineux g�gnait dans le jasmin
Voici mes infins le plus beau vers de la langue fran�aise.</poesie>

-* Cadre <code><cadre></code>Texte qui apparaitra dans une zone de formulaire facilitant le copier/coller[[Essentiellement utilis� sur spip-contrib pour donner des exemples de code]]<code></cadre></code>

<cadre>
Ceci est du texte dans un cadre.
      les espaces en d�but de ligne comptent !
Les retour � la ligne simples aussi !
</cadre>

-* Citation <code><quote></code>Texte d'une citation<code></quote></code>

<quote>C'est en forgeant que l'on devient forgeron.</quote>

{2{Caract�res}2}

Les attributs de caract�res {{doivent}} �tre ouverts et ferm�s � l'int�rieur du m�me paragraphe (pas question de d�buter le gras sur un premier paragraphe et de le terminer sur un deuxi�me).

Mise en forme:

-* gras : <code>{{</code>texte en gras<code>}}</code>; � utiliser pour un �l�ment que l'on souhaite appuyer (sera prononc� plus fort dans un lecteur vocal) : {{texte en gras}}
-* italique : <code>{</code>italique<code>}</code>; � utiliser pour une �l�ment sur lequel on veut insister (sera prononc� avec emphase) : {italique}
-* mise en �vidence <code>[*</code>texte en �vidence<code>*]</code> : �l�ment que l'on souhaite appuyer et attirer le regard par un changement de couleur : [*texte en �vidence*]
-* mise en exposant : <code><sup></code>texte en exposant<code></sup></code> : pour l'abr�viation de saint : S<sup>t</sup>
-* petites capitales : <code><sc></code>texte en petite capitales<code></sc></code> : � utiliser essentiellement pour les nom propres : Charles <sc>de Gaulle</sc>
-* code : <html><tt>&lt;code&gt;</tt></html>du code (raccourcis typographiques, html...)<html><tt>&lt;/code&gt;</tt></html> que l'on ne souhaite pas que SPIP interpr�te
-* biff� : <code><del></code>texte biff�<code></del></code> : pour indiquer qu'on avait pens� � un autre mot et que l'on a chang� d'avis : SPIP, c'est <del>bien</del> fantastique!

Comportement sp�cifique:

-* bulle d'aide : <code>[GPL|Gnu Public Licence]</code> : pour donner la signifation d'un terme ou d'une abr�viation : [GPL|Gnu Public Licence]
-* lien : <code>[texte du lien->http://www.spip.net/]</code> : lien : [texte du lien->http://www.spip.net/]
_ � noter qu'il est possible de faire des liens � l'int�rieur du site SPIP � l'aide des {{num�ros}} des �l�ments et de leur type (se reporter � l'aide en ligne fournie par SPIP).
-* lien avec bulle d'aide : <code>[texte du lien|Le site officiel de SPIP->http://www.spip.net/]</code> : [texte du lien|Le site officiel de SPIP->http://www.spip.net/]
-* lien avec langue des destination (non visible sur Internet Explorer) : <code>[texte du lien|{fr}->http://www.spip.net/]</code> : [texte du lien|{fr}->http://www.spip.net/]
-* lien avec bulle d'aide et angue des destination : <code>[texte du lien|Le site officiel de SPIP{fr}->http://www.spip.net/]</code> : [texte du lien|Le site officiel de SPIP{fr}->http://www.spip.net/]
-* ancre et retour � l'ancre : <code>[definition_ancre<-]</code> et <code>[retour � l'ancre->#definition_ancre]</code> : [retour � l'ancre->#definition_ancre]
-* d�finition dans Wikipedia : <code>[?GPL]</code> : appelle l'encyclop�die libre Wkipedia pour obtenir la d�finition du mot[[Si le mot n'existe pas, vous pouvez le cr�er vous-m�me!]] : [?GPL]
_ Avec bulle d'aide : <code>[?GPL|D�finition sur Wikip�dia]</code> : [?GPL|D�finition sur Wikip�dia]
-* note de bas de page : <code>texte[[note de bas de page]]</code> : cr�e une note de bas de page avec le texte entre les doubles crochets[[Et la note de bas de page est automatiquement num�rot�e, rendue clicable, pour la consulter, et pour revenir au texte l'ayant appel�e]]

{2{Listes}2}

Les listes sont � utiliser pour tout ce qui � le {{sens}} d'une �num�ration.

{{Attention}}: il faut entourer un bloc de listes � puces d'une ligne vide avant et apr�s.

{3{Listes � puces}3}

<cadre>
-* premi�re ligne
-* deuxi�me ligne
-** une sous liste � puce
-* de retour dans le niveau initial
</cadre>

Donnera :

-* premi�re ligne
-* deuxi�me ligne
-** une sous liste � puce
-* de retour dans le niveau initial

{3{Listes num�rot�es}3}

<cadre>
-# premi�re ligne
-# deuxi�me ligne
-## une sous liste � puce
-# de retour dans le niveau initial
</cadre>

Donnera :

-# premi�re ligne
-# deuxi�me ligne
-## une sous liste num�rot�e
-# de retour dans le niveau initial

{2{Tableaux}2}

Pour �tre compl�tement accessible, un tableau dans SPIP doit avoir un titre et une description.

Ainsi :

<cadre>
||Produits bio et prix|Ce tableau sert d'exemple de mise en forme spip||
| {{Produit}} | {{Prix �}} |
| Beurre Bio | 5� |
| Lait Bio | 3� |
| Choux Bio | 4� |
</cadre>

Donnera :

||Produits bio et prix|Ce tableau sert d'exemple de mise en forme spip||
| {{Produit}} | {{Prix �}} |
| Beurre Bio | 5� |
| Lait Bio | 3� |
| Choux Bio | 4� |

Notez les doubles <code>||</code> sur la premi�re ligne du tableau !

[*Attention*]: les pi�ges classiques avec les tableaux sont :

-* ne pas avoir le m�me nombre de | sur une ligne
-* avoir un espace {{apr�s}} le dernier | de la ligne (un moyen simple de v�rifier : la touche fin du clavier am�ne � la fin de la ligne)

{2{Tableaux avec fusion de cellules}2}

<cadre>
||Tableau avec fusion|Ce tableau sert d'exemple de mise en forme spip||
| {{Colonne 1}} | {{Colonne 2}} | {{Colonne 3}} |
| ligne 1 | Cellule fusionn�e avec la suivante |<|
| ligne 2 | Celulle fusionn�e
_ avec celle du sessous | normale |
| ligne 2 |^| normale aussi |
</cadre>

Donnera :

||Tableau avec fusion|Ce tableau sert d'exemple de mise en forme spip||
| {{Colonne 1}} | {{Colonne 2}} | {{Colonne 3}} |
| ligne 1 | Cellule fusionn�e avec la suivante |<|
| ligne 2 | Celulle fusionn�e
_ avec celle du dessous | normale |
| ligne 2 |^| normale aussi |

{{{Images}}}

Pour les images et documents, reportez-vous � l'aide en ligne de SPIP. Seule contrainte pour l'accessibilit� (et donc un meilleur r�f�rencement) : donnez un titre � {{toutes}} vos images d�crivant le {{[sens|signification]}} de chacune d'elles.

{{{Caract�res sp�ciaux}}}

-* <code>~</code> (espace ins�cable ou espace dur -- correspond au <code>&nbsp;</code> du [HTML|Hyper Text Markup Language]) plac� entre deux mots remplace l'espace en ayant l'avantage d'�tre ins�cable, c'est-�-dire, qu'il emp�chera les deux mots d'�tre s�par�s par un retour � la ligne malvenu. S'utilise en particulier entre le pr�nom et le nom propre.
-* <code>--></code> : --> (fl�che vers la droite)
-* <code><--</code> : <-- (fl�che vers la gauche)
-* <code><--></code> : <--> (fl�che vers la gauche et vers droite)
-* <code>--</code> : -- (tiret cadratin) � utiliser pour les incises dans un texte

{{{Ligne horizontale}}}

<code>----</code>: 4 signes moins en seuls sur une ligne (pr�c�d�s d'une ligne vide et suivis d'une ligne vide) donneront un trait de s�paration horizontal.

---- 

{{{�l�ments dangereux}}}

Il y a deux �l�ments {{dangereux}} dans SPIP :

-* le retour � la ligne simple : <code>_ </code> (soulign� espace) en d�but de ligne.
_ Usage tol�r� pour donner adresse et num�ro de t�l�phone/fax.
_ Usage tol�r� : dans une liste � puce pour passer � la ligne sans passer � une nouvelle puce (comme ici).
_ Usage {{[*interdit*]}}: pour mettre plus d'espace vertical entre deux �l�ments de la page.
-* le [?HTML] pur : il est {possible} dans SPIP de mettre du code [HTML|Hyper Text Markup Language]. Le faire est fortement d�conseill� :
-** parce que c'est la porte ouverte � toutes les d�rives, ne serait-ce que celle de sortir de la charte graphique du site, ou celle de produire un code HTML non valide (voire non interpr�table ailleurs que sur [Internet Explorer->115]
-** parce que c'est partir du postulat que votre site ne sera visit� qu'en tant que site web ; il pourrait tr�s bien �tre un jour disponible sous forme de fichier PDF...

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