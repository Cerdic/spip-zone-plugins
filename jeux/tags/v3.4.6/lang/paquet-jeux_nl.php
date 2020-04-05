<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-jeux?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// J
	'jeux_description' => 'Spelen met SPIP !

Hier krijg je de mogelijkheid in je artikelen een kruiswoordraadsel, sudoku, raadseltje, grap, poesie, meerkeuzevraag, invuloefening, enz. op te nemen.

Zo maak je je site educatief en ludiek!

Twee oplossingen:
-* Gecodeerde spelletjes in een artikel tussen de bakens <code><jeux></code> en <code></jeux></code>.
-* Gecodeerde spelletjes in de privé-ruimte die dankzij het model <code><jeuXX></code> toegankelijk zijn, waarbij XX het spelnummer is.

Alleen bij de tweede oplossing kunnen de scores worden bijgehouden.

De plugin werkt het best in skeletten met het baken [#INSERT_HEAD->https://www.spip.net/fr_article1902.html] en er kan worden getest in de privé-ruimte.

_* Voor de grafische spelletjes moet de {{GD}} bibliotheek op de server zijn geinstalleerd.
_* De plugin is nog in ontwikkeling, dus houdt de updates bij.
  
Om presentatiefouten te voorkomen (zoals antwoorden in het overzicht) moet je:
-* ofwel een {{introductie}} in je artikel opnemen tussen de bakens <code><intro></code> en <code></intro></code>,
-* of de {{omschrijving}} van het artikel gebruiken.

Credits:
-* Afbeeldingen: Jonathan Roche
-* Oorsprokelijk werk:
-** Meerkeuzevragen: Mathieu Giannecchini
-** Kruiswoordraadsel en scores: Maïeul Rouquette
-** Schaakdiagram: François Schreuer',
	'jeux_slogan' => 'Maak allerlei spelletjes en oefeningen.'
);
