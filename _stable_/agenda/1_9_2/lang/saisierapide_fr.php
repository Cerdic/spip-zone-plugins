<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
 'icone_saisie_rapide' => "Saisie rapide d'une liste",
 'titre_cadre_ajouter_liste_evenement' => "Ajouter une liste d'&eacute;v&egrave;nements",
 'saisie_rapide_votre_liste_infos' => "Indiquer un seul &eacute;v&egrave;nement (&eacute;ventuellement ses r&eacute;p&eacute;titions) par ligne :",
 'saisie_rapide_reset' => "Reset",
 'saisie_rapide_explications' => "
<strong>Syntaxe</strong> : &quot;jj/mm[/aaaa][-jj/mm[/aaaa]] [hh:mm[-hh:mm]]
    &quot;Le titre&quot;
[&quot;Le lieu&quot; [&quot;La description&quot;]] [REP=jj/mm/aaaa[,jj/mm/aaaa,etc.]] [MOTS=[groupe1:]mot1[,[groupe2:]mot2,etc.]]<br />
<br />
<strong>Notes</strong> : Les crochets indiquent les &eacute;l&eacute;ments facultatifs. <br />
Les r&eacute;p&eacute;titions de l'&eacute;v&egrave;nement sont indiqu&eacute;es par  'REP=' suivi d'une liste de dates s&eacute;par&eacute;es par des virgules.<br />
Les mots-cl&eacute;s de l'&eacute;v&egrave;nement sont indiqu&eacute;s par  'MOTS=' suivi d'une liste de mots (&eacute;ventuellement pr&eacute;c&eacute;d&eacute;s de leur groupe) s&eacute;par&eacute;s par des virgules. Attention aux majuscules/minuscules. <br />
Respectez bien les espaces (ou tabulations) entre les &eacute;l&eacute;ments et ne mettez pas de guillemets &agrave; l'int&eacute;rieur des textes. <br />
    <br />

    <em>Exemple 1</em> : 
20/09/2006 19:30-22:00 &quot;R&eacute;p&eacute;tition de rentr&eacute;e&quot; &quot;Temple des Gobelins&quot; &quot;Reprise de contact, Durufl&eacute;, et mise au point des calendriers&quot;<br />
<em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement pr&eacute;cis &agrave; une date pr&eacute;cise, et d'une dur&eacute;e  pr&eacute;cise)</em><br />
    <em>Exemple 2</em> : 
  17/08-23/08 &quot;Stage d'&eacute;t&eacute; 
  <?=date('Y', time())?>
&quot; &quot;Les Salines&quot; MOTS=photos, Agenda:priv&eacute;<br />
  <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement cette ann&eacute;e, sans description et sur plusieurs jours en ajoutant deux mots-cl&eacute;s)<br />
  Exemple 3</em> : 
  01/01/2007 &quot;Bonne ann&eacute;e &agrave; tous !&quot; REP=01/01/2008,01/01/2009,01/01/2010<br />
  <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement sans horaire, sans lieu, &agrave; une date pr&eacute;cise et r&eacute;p&eacute;t&eacute; sur 3 autres dates)</em><br />
  <em></em> </p>"
);


?>