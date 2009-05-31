<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
'icone_saisie_rapide' => "Schnelleingabe einer Liste",
'titre_cadre_ajouter_liste_evenement' => "Hinzuf&uuml;gen einer Liste von Ereignissen",
'votre_liste_infos' => "Ein einzelnes Ereignis pro Zeile eingeben:",
'reset' => "Reset",
'explications' => "
	{{Syntax}}:
{Date} {Ereignissen} [{Wiederholungen}] [{Schlagworte}]
-* {Date} =&gt; <code>tt/mm[/jjjj][-tt/mm[/jjjj]] [hh:mm[-hh:mm]]</code>
-* {Ereignissen} =&gt; <code>\"Titel\" [\"Ort\" [\"Beschreibung\"]]</code>
-* {Wiederholungen} =&gt; <code>REP=tt/mm/jjjj[,tt/mm/jjjj,etc.]</code>
-* {Schlagworte} =&gt; <code>MOTS=[gruppe1:]wort1[,[gruppe2:]wort2,etc.]</code>

	{{Bemerkungen}}: Die eckigen Klammern bedeuten freiwillige Angaben. <br />
Die Wiederholungen der Ereignisse werden angegeben via 'REP=' gefolgt von einer kommagetrennten Liste der Daten.<br />
Die Schlagworte der Ereignisse werden angegeben via 'MOTS=' gefolgt von einer kommagetrennten Liste der Schlagworte (gegebenenfalls mit vorangestellter Schlagwortgruppe). Bitte auf Gro&szlig;-/Kleinschreibung achten!<br />
Bitte beachten Sie die Leerzeichen (oder Tabulatorzeichen) zwischen den Ereignissen und benutzen Sie keine Anf&uuml;hrungszeichen in den Texten.",

 'exemples' => "
	{{Beispiel 1}}:
04/05/2007 20:00-22:00 &quot;Was bleibt vom westlichen Marxismus?&quot; &quot;Autonomes Zentrum KTS Freiburg&quot; &quot;Praxis, Subjekt und Hegemonie im 20. und 21. Jahrhundert&quot;
_ {&nbsp;(hinzuf&uuml;gen eines pr&auml;zisen Ereignisses, eines pr&auml;zisen Ortes und einer pr&auml;zisen Dauer)}<br />
	{{Beispiel 2}}:
03/02-07/02 &quot;@Y@ Ausstellung: Pueblo in armas&quot; &quot;Autonomes Zentrum KTS Freiburg&quot; MOTS=Ausstellung, Agenda:Public
_ {&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
(hinzuf&uuml; eines Ereignisses in diesem Jahr, &uuml;ber mehrere Tage, ohne Beschreibung und verkn&uuml;pft mit zwei Schlagw&ouml;rtern)}<br />
	{{Beispiel 3}}:
01/01/2008 &quot;Auf ein revolution&auml;res neues Jahr!&quot; REP=01/01/2009,01/01/2010,01/01/2011
_ {&nbsp;(hinzuf&uuml;gen eines Ereignisses ohne Stundenangabe aber an einem pr&auml;zises Datum, ohne Ort, mit drei Wiederholungen an anderen Daten)}"
);

?>