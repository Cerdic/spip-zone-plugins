<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/faq-manuelsite?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Een forum is standaard geactiveerd voor je @complement@ artikelen; individueel kun je het forum uitschakelen... Bezoekers kunnen dus op je artikelen reageren... Iedere keer wanneer dit gebeurt, ontvang je een mail. Maar de medaille heeft een keerzijde: SPAM kan niet altijd worden voorkomen en moet met de hand worden verwijderd. Om een forumbericht te bewerken (verwijderen of als SPAM aanmerken):
-* Op de publieke site vind je, wanneer je bent aangemeld, twee knoppen "Verwijder dit bericht" of "SPAM"
-* In het privé-gedeelte ga je naar menu Activiteit / Forums opvolgen',
	'forum_q' => 'Hoe beheer ik de forums?',

	// I
	'img' => 'Er is geen « ideale » afmeting voor een afbeelding in een artikel. Het is in ieder geval zinloos een afbeelding van 3000 pixels breed te gebruiken, want geen enkel kan dat in zijn geheel weergeven! Behalve wanneer het moet worden afgedrukt.
-* Wanneer een afbeelding in de tekst van een artikel moet worden opgenomen, hangt alles af van de inhoud: in portretformaat is een hoogte van 200px voldoende; is het een mooi landschap, dan kun je tot maximaal {{@largeur_max@}} pixels breed gaan.
-* Wanneer de afbeelding bedoeld is voor de portfolio van een artikel, ga dan niet verder dan 1000 pixels breed of 600 pixels hoog.

{Let op, de grootte van het document mag niet meer zijn dan {{@poids_max@}}Mb, anders wordt het geweigerd}.',
	'img_nombre' => 'Is het mogelijk om in één klik meerdere afbeeldingen in een artikel te laden:
-* Kopieer de afbeeldingen in een map op je harde schijf
-* Zet ze in de juiste grootte en breedte
-* Pak ze samen in een zip-bestand
-* Voeg het zip-bestand aan het artikel toe. Aan het einde wordt je gevraagd wat je met het bestand wilt doen, bijvoorbeeld alle afbeeldingen in de portfolio plaatsen.',
	'img_nombre_q' => 'Hoe kun je eenvoudig een portfolio vullen?',
	'img_ou_doc' => 'In het algemeen wordt de tag <code><imgXX|center></code> gebruikt om een afbeelding in een tekst op te nemen. Maar wanneer je onder de afbeelding ook de titel of de omschrijving wilt tonen, gebruik je <code><docXX|center></code>.',
	'img_ou_doc_q' => '<code><imgXX> of <docXX></code>?',
	'img_q' => 'Welke afbeelding moet mijn foto hebben?',

	// S
	'son' => 'Bereid een geluidsfragment in mp3-formaat voor, in mono met een frequentie van 11 of 22 kHz en een bitrate van 64kbps (of hoger voor een betere kwaliteit).
	
Koppel het mp3-bestand aan je artikel, zoals met een afbeelding en geef het een titel (en een omschrijving en credit). Plaats op de gewenste plaats in het artikel <code><docXX|center|player></code>. In de publieke site zal een flash-reader het geluidsfragment afspelen. 
_ {Let op: de maximale grootte is 150Mb, wat ongeveer overeen komt met 225 minuten}',
	'son_audacity' => 'Om met geluidsbestanden te werken, kun je de tool Audacity (Mac, Windows, Linux) downloaden: [->http://audacity.sourceforge.net/]. Enkele tips:
-* Na installatie van de tool, heb je een lame librairy nodig voor het encoderen van mp3: [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* Om het bestand in mono te zetten: Menu {Tracks/Stereo naar mono}
-* Om het mp3-bestand te maken: Menu {Bestand/Exporteren}
-* Om de bitrate in te stellen: Menu {Bestand/Exporteren/Opties/Kwaliteit}',
	'son_audacity_q' => 'Hoe bereid ik eluid voor?',
	'son_q' => 'Hoe kun je geluid aan een artikel toevoegen?',

	// T
	'thumbsites' => 'Klik op « Een site koppelen » in rubriek {{@rubrique@}}. Vermeld de URL, waarna het systeem gaat proberen de titel, omschrijving en een logo op te halen.  Pas deze eventueel aan. Wanneer het logo niet automatisch werd aangemaakt, kun je een screenshot gebruiken die je als logo invoegt met een formaat 120x90 pixels.',
	'thumbsites_q' => 'Hoe kun je naar een site verwijzen?',
	'trier' => 'Een nummer voor de titel van een artikel, rubriek of document laat je toe de volgorde te bepalen. De syntax is een getal gevold door een punt en een spatie',
	'trier_q' => 'Hoe kun je de volgorde van artikelen, rubrieken en documenten forceren?',

	// V
	'video_320x240' => 'Bereid een video voor in flv-formaat (streaming flash) van 320x240 pixels met bitrate 400kbps en mono-geluid van 64kbps. Om een videobestand te converteren kun je bijvoorbeeld avidemux (Mac, Windows, Linux) downloaden: [->http://www.avidemux.org/]. 

Voeg het bestand aan het artikel toe, geef het een titel, eventueel een omschrijving met credit en een formaat 320x240. Zet op de gewenste plek <code><docXX|center|video></code>. Op de publieke site zal een flash-lezer de vide weergeven.
_ {Let op: de maximale grootte is 150Mb, ofwel 37.5 minuten}',
	'video_320x240_q' => 'Hoe voeg ik een video aan een artikel toe?',
	'video_dist' => 'Staat de video op DailyMotion, YouTube of Viméo, ga dan in een nieuw venster naar de bladzijde van de video en kopieer de URL. Klik in de redactie van het artikel op "Een video toevoegen" en plak de URL. Plaats vervolgens in je tekst <code><videoXX|center></code>',
	'video_dist_q' => 'Hoe voeg ik een dailymotion (youtube,...) video aan een artikel toe?'
);

?>
