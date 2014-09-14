<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => '&nbsp;:&nbsp;niet',
	'2pts_oui' => '&nbsp;:&nbsp;ja',

	// S
	'SPIP_liens:description' => '@puce@ begint Alle band van de plaats bij verstek in het lopende venster van scheepvaart. Maar het kan nuttig zijn om de externe band te openen aan de plaats in een nieuw buitenlands venster  dat komt terug om {target toe te voegen ="_blank"} aan alle bakens &lt;a&gt; voorzien door SPIP van klasse {spip_out}, {spip_url} of {spip_glossaire}. Het is soms noodzakelijk om &eacute;&eacute;n van deze klassen toe te voegen aan de band van het skelet van de plaats (bestanden HTML) teneinde deze functionaliteit zoveel mogelijk uit te breiden. [[%radio_target_blank3%]]
@puce@ SPIP maakt het mogelijk om woorden te verbinden met hun definitie dank zij de typografische kortere weg <code> [? woord] </code>. Per gebrek (of als u leegte het hokje hieronder laat), stuurt het externe glossarium naar de vrije encyclopedie wikipedia.org terug. Om het te gebruiken adres te kiezen. <br/>Band van test: [? SPIP] [[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP en de externe band…',

	// A
	'acces_admin' => 'Toegang beheerders :',
	'action_rapide' => 'Snelle actie, alleen als u weet wat u doet !',
	'action_rapide_non' => 'Vlugge actie, ter beschikking eens dat het instrument is geactiveerd',
	'attente' => 'Wachten...',
	'auteur_forum:description' => 'Zet alle auteurs van openbare berichten ertoe aan om te vullen (van minstens van een brief!) het veld &laquo;@_CS_FORUM_NOM@&raquo; teneinde de volkomen onbekende bijdragen te vermijden.',
	'auteur_forum:nom' => 'Geen onbekende forums',
	'auteurs:description' => 'Dit werktuig configureert de schijn van [de bladzijde van de auteurs ->./?exec=auteurs], gedeeltelijk particulier.

@puce@ Bepaal hier het maximum aantal auteurs die op het centrale kader van de bladzijde van de auteurs moeten aangegeven worden. Verder is een paginering opgesteld.[[%max_auteurs_page%]]

@puce@ Welke statuten van auteurs kunnen op deze bladzijde op een lijst gezet worden ?
[[%auteurs_tout_voir%[[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]]]',
	'auteurs:nom' => 'Bladzijde van de auteurs',

	// B
	'basique' => 'Fundamenteel',
	'blocs:aide' => 'Openvouwen blokken : <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) et <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => '<MODIF>Laat u toe om blokken te cre&euml;ren waarvan de titel cliquable ze zichtbaar of onzichtbaar kan maken.

@puce@ {{In de SPIP teksten}} : de redacteuren hebben ter beschikking de nieuwe bakens &lt;bloc&gt; (om &lt;invisible&gt;) en &lt;visible&gt; om bij hun teksten zoals dit te gebruiken : 

<quote><code>
<bloc>
 Een titel die geselecterde zal worden
 
 Tekst de te verbergen/tonen, na twee sprongen lijn ...
 </bloc>
</code></quote>

@puce@ {{In de sjabloon}} : u hebt tot uw beschikking de nieuwe bakens #BLOC_TITRE, #BLOC_DEBUT et #BLOC_FIN om als dit te gebruiken : 
<quote><code> #BLOC_TITRE om #BLOC_TITRE{mon_URL}
 Mijn titel
 #BLOC_RESUME    (facultatief)
 een samengevatte versie van het blok
 #BLOC_DEBUT
 Mijn openvouwen blok (wie URL gemarkeerd indien noodzakelijk zal bevatten)
 #BLOC_FIN</code></quote>

@puce@ Door &#132;jawoord&#147; aan te strepen hieronder, zal de opening van een blok de sluiting van alle andere blokken van de bladzijde, veroorzaken teneinde te hebben erover dan slechts &eacute;&eacute;n tegelijkertijd geopend.[[%bloc_unique%]]',
	'blocs:nom' => 'Openvouwen Blokken',
	'boites_privees:description' => '<MODIF>Alle beschreven dozen hieronder komen in het particuliere deel voor.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{De revisies van het Zwitserse Mes}} : een kader op deze bladzijde van configuratie, laatste wijzigingen aangebracht aan de code van plugin  ([Source->@_CS_RSS_SOURCE@]).
- {{De artikelen aan het SPIP formaat}} : een aanvullend opvouwbaar kader voor uw artikelen ten einde de code bron te kennen die door hun auteurs wordt gebruikt.
- {{De auteurs in stat}} : een kader aanvullend op [de bladzijde van de auteurs->./?exec=auteurs] wijst op de  10 laatst aangeslotenen en de niet bevestigde inschrijvingen. Enkel de beheerders zien deze informatie.
- {{URLs eigen}}: een kader d&eacute;pliable voor elk onderwerp van inhoud (artikel, rubriek, auteur,…) aangevend URL eigen verenigd alsmede van hen alias eventueel. Het werktuig &#132;&nbsp; [. - >type_urls] &nbsp;&#147; laat u een fijne configuratie van URLs van uw plaats toe.',
	'boites_privees:nom' => 'Particuliere dozen',
	'bp_urls_propres' => 'Eigen URLs ',

	// C
	'cache_controle' => 'Controle van het dekblad',
	'cache_nornal' => 'Normaal gebruik',
	'cache_permanent' => 'Permanent dekblad',
	'cache_sans' => 'Geen dekblad',
	'categ:admin' => '1. Administratie',
	'categ:divers' => '60. Diversen',
	'categ:interface' => '10. Interface priv&eacute;e',
	'categ:public' => '40. Openbare display',
	'categ:spip' => '50. Bakens, filters, criteria',
	'categ:typo-corr' => '20. Teksten verbeteringen',
	'categ:typo-racc' => '30. Typografische kortere wegen',
	'certaines_couleurs' => 'Enkel de hieronder bepaalde bakens@_CS_ASTER@ :',
	'chatons:aide' => 'Katjes : @liste@',
	'chatons:description' => '<MODIF>Neemt beelden (of katjes voor {tchats}) op in alle teksten waar een keten van het soort blijkt <code>:nom</code>.
_ Dit werktuig vervangt deze link door de beelden van dezelfde naam die hij in de lijst vindtplugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Katjes',
	'class_spip:description1' => 'U kunt hier bepaalde kortere wegen van SPIP bepalen. Een lege waarde staat gelijk om de waarde per gebrek te gebruiken.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{De kortere wegen van SPIP}}.

U kunt hier bepaalde kortere wegen van SPIP bepalen. Een lege waarde staat gelijk om de waarde per gebrek te gebruiken.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '<MODIF>

SPIP gebruikt gewoonlijk het baken &lt;h3&gt; voor intertitres. Kies hier een andere vervanging :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '<MODIF><MODIF>

SPIP heeft verkozen om het baken &lt;strong> te gebruiken om vette letters te schrijven. Maar &lt;b> had eveneens kunnen passen. Aan u om te beslissen :[[%racc_g1%]][[->%racc_g2%]]

SPIP heeft gekozen om &lt;i> te gebruiken om italiques te schrijven. Maar &lt;em> zou ook gekunt hebben, met of zonder stijl.Aan u om te beslissen[[%racc_i1%]][[->%racc_i2%]]

@puce@ {{De stijlen van SPIP}}. Tot de versie 1.92 van SPIP, produceerden de typografische kortere wegen bakens systematisch van de stijl "spip". Bijvoorbeeld : <code><p class="spip"></code>. U kunt hier de stijl van deze bakens bepalen in functie van uw bladen van stijl. Een leeg hokje betekent dat geen enkele bijzondere stijl zal toegepast zijn.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Bakens &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; en de lijsten (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Bakens &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[%style_h%]]

Opgelet: door deze tweede parameter te wijzigen, verliest u dan de standaardstijlen die met deze bakens worden verenigd.</blockquote>',
	'class_spip:nom' => 'SPIP en zijn kortere wegen…',
	'code_css' => 'CSS',
	'code_fonctions' => 'Functies',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Opties',
	'code_spip_options' => 'Opties SPIP',
	'contrib' => 'Meer info : @url@',
	'corbeille:description' => '<MODIF>SPIP verwijdert automatisch de objecten mis au rebuts na 24 uren, en dit meestal rond 4u \'s morgens, dit dankzij &laquo;CRON&raquo; (een periodieke en/of een automatische lancering van het voorgeprogrammeerde proces). Hier kunt u het proces verhinderen zodanig dat u beter vat hebt op het beheer van prullenmand.[[%arret_optimisation%]]',
	'corbeille:nom' => 'Het mandje',
	'corbeille_objets' => '@nb@ onderwerp(en) in het mandje.',
	'corbeille_objets_lies' => '@nb_lies@ ontdekte(n) verbinding.',
	'corbeille_objets_vide' => 'Geen enkel onderwerp in het mandje',
	'corbeille_objets_vider' => 'De geselecteerde onderwerpen afschaffen',
	'corbeille_vider' => 'Het mandje legen&nbsp;:',
	'couleurs:aide' => 'Inzet in kleuren: <b>[coul]tekst[/coul] </b>@fond@ met <b>coul</b> = @liste@',
	'couleurs:description' => 'Maakt het mogelijk om kleuren gemakkelijk toe te passen op alle teksten van de plaats (artikelen, kort, titels, forum,…) door bakens in kortere wegen te gebruiken.

Twee identieke voorbeelden om de kleur van de tekst te veranderen:@_CS_EXEMPLE_COULEURS2@

Idem om de bodem te veranderen, als de keuze hieronder het toelaat:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@Het formaat van deze verpersoonlijkte bakens moet bestaande kleuren op een lijst zetten of paren &laquo;balise=couleur&raquo;, bepalen, alles die door komma\'s wordt gescheiden. Voorbeelden. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; of nog &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Voor de eerste en het laatste voorbeeld, zijn de toegelaten bakens : <code>[gris]</code> en <code>[rouge]</code> (<code>[fond gris]</code> en <code>[fond rouge]</code> als de middelen toegestaan zijn).',
	'couleurs:nom' => 'Erg in kleuren',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',
	'cs_comportement:description' => '<MODIF>@puce@ {{Logs.}}Vele inlichtingen zijn te verkrijgen over de plugin \'Couteau Suisse (Zwitsers mesje)\' in de folders {spip.log} deze kunt U vinden in het repertoire: {@_CS_DIR_TMP@}[[%log_couteau_suisse%]]

@puce@{{Options SPIP.}} SPIP zet de  plugins in een specifieke orde. Om zeker te zijn dat \'le Couteau Suisse\' in het begin staat en zo enkele SPIP opties automatisch be&iuml;nvloedt, moet u de volgende optie aanvinken. Indien de rechten van u server het toestaan, zal de folder{@_CS_FILE_OPTIONS@} automatisch gemodifieerd worden en de volgende folder insluiten {@_CS_DIR_TMP@couteau-suisse/mes_spip_options.php}.
[[%spip_options_on%]]

@puce@ {{Requ&ecirc;tes externes.}} \'Le Couteau Suisse\' verifieert regelmatig het bestaan van een recentere versie en geeft de informatie door waar deze  ter beschikking is. Indien dit een probleem vertoont bij u server probeer dan de volgende link.[[%distant_off%]]',
	'cs_comportement:nom' => 'Gedrag van het Zwitserland Mes',
	'cs_distant_off' => 'De verificaties van verwijderde versies',
	'cs_log_couteau_suisse' => 'Uitvoerige logs van het Zwitserland Mes',
	'cs_reset' => 'Bent u zeker r&eacute;initialiser volkomen het Zwitserland Mes te willen?',
	'cs_spip_options_on' => 'De SPIP opties in &laquo;@_CS_FILE_OPTIONS@&raquo;',

	// D
	'decoration:aide' => 'Versiering&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b>, met <b>balise</b> = @liste@',
	'decoration:description' => '<MODIF>Nieuwe lettertype zijn te parametreren in u teksten en toegankelijk dankzij des balises &agrave; chevrons. Voorbeeld : 
&lt;mabalise&gt;texte&lt;/mabalise&gt; ou : &lt;mabalise/&gt;.<br />Definier hieronder de CSS die u nodig heeft, une balise per lijn, zoals de hierna volgende syntaxes  :
- {type.mabalise = mon style CSS}
- {type.mabalise.class = ma classe CSS}
- {type.mabalise.lang = ma langue (ex : fr)}
- {unalias = mabalise}

De parameter {type} hieronder kan drie verschillende waarden:
- {span} : balise binnenin een paragraaf(type Inline)
- {div} : balise die een nieuwe paragraaf cre&euml;rt (type Block)
- {auto} : balise automatisch gedetermineerd door de plugin 

[[%decoration_styles%]]',
	'decoration:nom' => 'Versiering',
	'decoupe:aide' => 'Blok tabben : <b>&lt;onglets>&lt;/onglets></b><br/>S&eacute;parateur van bladzijdes of tabben&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias&nbsp;:&nbsp;@sep@',
	'decoupe:description' => '<MODIF>@puce@ D&eacute;coupe l\'affichage public d\'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. Placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l\'endroit qui doit recevoir la coupure.

Par d&eacute;faut, le Couteau Suisse ins&egrave;re la pagination en t&ecirc;te et en pied d\'article automatiquement. Mais vous avez la possibilit&eacute; de placer cette pagination ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_DECOUPE que vous pouvez activer ici :
[[%balise_decoupe%]]

@puce@ Si vous utilisez ce s&eacute;parateur &agrave; l\'int&eacute;rieur des balises &lt;onglets&gt; et &lt;/onglets&gt; alors vous obtiendrez un jeu d\'onglets.

Dans les squelettes : vous avez &agrave; votre disposition les nouvelles balises #ONGLETS_DEBUT, #ONGLETS_TITRE et #ONGLETS_FIN.

Cet outil peut &ecirc;tre coupl&eacute; avec &laquo;&nbsp;[.->sommaire]&nbsp;&raquo;.',
	'decoupe:nom' => 'In bladzijdes en tabben snijden',
	'desactiver_flash:description' => 'Schaft de onderwerpen flash van de bladzijdes van uw plaats af en vervangt ze door de verenigde alternatieve inhoud.',
	'desactiver_flash:nom' => 'De activering terugtrekken van de onderwerpen flash',
	'detail_balise_etoilee' => '{{Aandacht}}: Controleert goed het gebruik dat door uw skeletten van de met sterren bezaaide bakens wordt gemaakt. De behandelingen van dit werktuig zullen niet op van toepassing zijn : @bal@.',
	'detail_fichiers' => 'Bestanden :',
	'detail_inline' => 'Code inline :',
	'detail_jquery1' => '{{Aandacht}}: dit werktuig maakt plugin {jQuery} het noodzakelijk om met deze versie van SPIP te werken.',
	'detail_jquery2' => 'Dit werktuig vereist de boekhandel {jQuery}.',
	'detail_jquery3' => '<MODIF>{{Attention}} : deze tool heeft een andere plugin nodig[jQuery pour SPIP 1.92->http://files.spip.org/spip-zone/jquery_192.zip]om efficient te functioneren met deze spip versie.',
	'detail_pipelines' => 'Pijpleidingen :',
	'detail_traitements' => 'Behandelingen :',
	'dossier_squelettes:description' => 'Wijzigt het dossier van het gebruikte skelet. Bijvoorbeeld: &#132;skeletten/mijnskelet&#147;. U kunt verschillende dossiers inschrijven door ze te scheiden door beide punten <html> &laquo;&nbsp;:&nbsp;&raquo;</html>. Door leegte te laten het hokje dat (of door "dist" te typen) volgt, is het originele skelet dat "dist" door SPIP wordt geleverd, dat zal gebruikt worden. [[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Dossier van het skelet',

	// E
	'effaces' => 'Uitgewist',
	'en_travaux:description' => '<MODIF>Maakt het mogelijk om een aan de persoonlijke behoeften aanpasbaar bericht te kennen te geven gedurende een fase van onderhoud op de hele openbare site.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site in werkzaamheden',
	'erreur:bt' => '<MODIF><span style=\\"color:red;\\">Attention :</span> la barre typographique (version @version@) schijnt oud.<br />Het Zwitsers mes  (Couteau Suisse)stemt overeen met een hogere versie of gelijk aan @mini@.',
	'erreur:description' => 'id gebrek hebbend aan in de definitie van het werktuig !',
	'erreur:distant' => 'de verwijderde server',
	'erreur:jquery' => '<MODIF>{{Note}} : de bibliotheek {jQuery} schijnt inactief op deze pagina. Consulteer:[ici->http://contrib.spip.net?article2166] de paragraaf op de \'d&eacute;pendances\' van de plugin of  herlaad deze pagina.',
	'erreur:js' => 'Een fout JavaScript schijnt op deze bladzijde voorgekomen zijn en verhindert zijn goede werking. Gelieve JavaScript op uw navigator activeren om af-activeren sommige plugins SPIP van uw site.',
	'erreur:nojs' => 'JavaScript wordt op deze bladzijde af-activeerd.',
	'erreur:nom' => 'Fout !',
	'erreur:probleme' => 'Zurig probleem : @pb@',
	'erreur:traitements' => 'Het Mes Zwitserland - De compilatie fout van de behandelingen: verboden \'typo\' en \'eigen\' mengeling !',
	'erreur:version' => 'Dit werktuig is niet beschikbaar in deze versie van SPIP.',
	'etendu' => 'Uitgestrekt',

	// F
	'f_jQuery:description' => '<MODIF>Verhindert de installatie van {jQuery} in het openbare deel teneinde economischer te werk te gaan en tijd te besparen. Deze bibliotheek ([- > http://jquery.com/]) brengt talrijke mogelijkheden in de programmering van Javascript en kan door bepaalde plugins gebruikt worden. SPIP gebruikt het in het priv&eacute; gedeelte.

Opgelet: bepaalde werktuigen van het Zwitserse Mes (couteau suisse) vereisen de functies van {jQuery}.',
	'f_jQuery:nom' => 'Inactieve jQuery.',
	'filets_sep:aide' => 'Scheidingsnetten&nbsp;: <b>__i__</b> waar <b>i</b> is een aantal.<br />Andere beschikbare netten : @liste@',
	'filets_sep:description' => 'Neemt scheidingsnetten op, aan de persoonlijke behoeften aanpasbaar door bladen van stijl, in alle teksten van SPIP.
_ De syntaxis is : "__code__", waar &#132;de code&#147; vertegenwoordigt ofwel het identificatienummer (van 0 tot 7) van het net dat in rechtstreeks verband met de overeenkomstige stijlen, ofwel de naam van een beeld moet opgenomen worden dat in het dossier wordt geplaatst plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Scheidingsnetten',
	'filtrer_javascript:description' => '<MODIF>Om javascript in de artikelen te beheren, zijn drie manieren beschikbaar :
- <i>nooit</i>: javascript wordt overal geweigerd
- <i>het gebrek</i>: javascript is in rood in de priv&eacute; ruimte aangeduid
- <i>nog steeds</i>: javascript wordt overal aanvaard.

Opgelet: in de forums, petities, georganiseerde stromen, enz., het beleid van javascript wordt <b>altijd</b> veilig gesteld.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Beleid van JavaScript',
	'flock:description' => 'D&eacute;sactiveren het systeem van grendeling van bestanden door de functie PHP {flock()} te neutraliseren. Bepaalde onderdak geeft immers ernstige problemen ten gevolge van een onaangepast systeem van bestanden of een gebrek aan synchronisatie. Activeert niet dit werktuig als uw plaats normaal werkt.',
	'flock:nom' => 'Geen grendeling van bestanden',
	'fonds' => 'Bodem :',
	'forcer_langue:description' => '<MODIF>Kracht de context van taal voor de spelen van meertalige skeletten die over een formulier of over een menu van talen beschikken die cookie van talen kunnen beheren.',
	'forcer_langue:nom' => '<MODIF>Taal forceren',
	'format_spip' => 'De artikelen aan het SPIP formaat',
	'forum_lgrmaxi:description' => '<MODIF>Per gebrek worden de berichten van forum niet in omvang beperkt. Als dit werktuig wordt geactiveerd, zal een bericht van fout zich aangeven wanneer het iemand een bericht van een hogere omvang zal willen opstellen dan de gespecificeerde waarde, en het bericht zal geweigerd worden. Een lege of gelijke waarde aan 0 betekent niettemin dat geen enkele grens van toepassing is.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Omvang van de forums',

	// G
	'glossaire:aide' => '<MODIF>Een tekst zonder verklarende woordenlijst (glossarium) : <b>@_CS_SANS_GLOSSAIRE@</b>',
	'glossaire:description' => '<MODIF>@puce@ Beleid van een intern glossarium in verband met &eacute;&eacute;n of meer groepen sleutelwoorden. Schrijft hier de naam van de groepen in door ze te scheiden door beide punten &laquo;&nbsp;:&nbsp;&raquo;. Door leegte te laten het hokje dat (of door "Glossarium" te typen) volgt, is het de groep "Glossarium" die zal gebruikt worden.[[%glossaire_groupes%]]@puce@ Voor elk woord, hebt u de mogelijkheid om het maximumaantal band te kiezen die in uw teksten wordt gecre&euml;erd. Elke nul of negatieve waarde impliceert dat alle erkende woorden zullen behandeld worden. [[%glossaire_limite% per sleutelwoord]]@puce@ worden Twee oplossingen u aangeboden om het kleine automatische venster te cre&euml;ren dat bij het overzicht van de muis blijkt.[[%glossaire_js%]]',
	'glossaire:nom' => 'Intern glossarium',
	'glossaire_css' => 'Oplossing CSS',
	'glossaire_js' => '<MODIF>Oplossing Javascript',
	'guillemets:description' => 'Vervangt automatisch de rechte aanhalingstekens (") door de typografische aanhalingstekens van de samenstellingstaal. De vervanging, transparant voor de gebruiker, wijzigt de originele tekst niet maar alleen maar de definitieve display.',
	'guillemets:nom' => 'Typografische aanhalingstekens',

	// H
	'help' => '<MODIF>{{Deze bladzijde is alleen toegankelijk voor de verantwoordelijken voor de site.}}<p>Zij geeft toegang tot de verschillende aanvullende functies die door plugin worden gebracht&laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Plaatselijke versie : @version@@distant@<br/>@pack@</p><p>Band van documentatie :<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://contrib.spip.net?article2166]@contribs@</p><p>R&eacute;initialisations :
_ • [Verborgen werktuigen|Aan de eerste schijn van deze bladzijde terugkomen->@hide@]
_ • [Van hele plugin|Aan de eerste stand van plugin terugkomen->@reset@]@install@
</p>',
	'help2' => '<NEW>Version locale : @version@',
	'help3' => '<NEW><p>Liens de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://contrib.spip.net?article2166]@contribs@</p><p>R&eacute;initialisations :
_ &bull; [Des outils cach&eacute;s|Revenir &agrave; l\'apparence initiale de cette page->@hide@]
_ &bull; [De tout le plugin|Revenir &agrave; l\'&eacute;tat initial du plugin->@reset@]@install@
</p>',

	// I
	'icone_visiter:description' => '<NEW>Remplace l\'image du bouton standard &laquo;&nbsp;Visiter&nbsp;&raquo; (en haut &agrave; droite sur cette page)  par le logo du site, s\'il existe.

Pour d&eacute;finir ce logo, rendez-vous sur la page &laquo;&nbsp;Configuration du site&nbsp;&raquo; en cliquant sur le bouton &laquo;&nbsp;Configuration&nbsp;&raquo;.',
	'icone_visiter:nom' => 'Knoop &laquo;&nbsp;Bezoeken&nbsp;&raquo;',
	'insert_head:description' => '<MODIF>Actief automatisch het baken [#INSERT_HEAD-> http://www.spip.net/fr_article1902.html] op alle skeletten, dat zij of niet dit baken tussen &lt;head&gt; en &lt;/head&gt;. Dank zij deze keuze, zullen plugins van javascript (.js) of de bladen van stijl (.css) kunnen opnemen.',
	'insert_head:nom' => 'Baken #INSERT_HEAD',
	'insertions:description' => 'OPGELET: werktuig in ontwikkeling!! [[%insertions%]]',
	'insertions:nom' => 'Automatische correcties',
	'introduction:description' => '<MODIF>Dit baken dat in de skeletten moet geplaatst worden, dient in het algemeen tot een of in de rubrieken teneinde een samenvatting van de artikelen, van kort te produceren, enz..</p>
<p>{{Opgelet}} : Alvorens deze functionaliteit te activeren, controleert goed dat geen enkele functie {balise_INTRODUCTION ()} bestaat niet reeds in uw plugins skelet , overbelasting zouden dan een fout van compilatie produceren.</p>
@puce@ U kunt (in percent ten opzichte van de waarde die per gebrek wordt gebruikt) de lengte van de tekst aangeven die per baken #INTRODUCTION wordt teruggestuurd. Een nul of gelijke waarde aan 100 wijzigt het aspect van de inleiding niet en gebruikt dus de waarden per gebrek volgend: 500 karakters voor de artikelen, 300 voor kort en 600 voor de forums of de rubrieken.
[[%lgr_introduction%&nbsp;%]]
@puce@ Per gebrek, zijn de punten van vervolg die aan het resultaat van het baken #INTRODUCTION worden toegevoegd, als de tekst te lang is : <html>&laquo;&amp;nbsp;(…)&raquo;</html>. U kunt hier uw eigen keten van carat&egrave;re aangeven die de lezer mededeelt, dat de verminkte tekst goed een vervolg heeft.
[[%suite_introduction%]]
@puce@ Als het baken #INTRODUCTION wordt gebruikt om een artikel kort samen te vatten, dan kan het Mes Zwitserland een band hypertexte op de hierboven bepaalde punten van vervolg vervaardigen teneinde de lezer naar de originele tekst te leiden. Bijvoorbeeld: &laquo;Het vervolg van het artikel lezen…&raquo;
[[%lien_introduction%]]
',
	'introduction:nom' => 'Baken #INTRODUCTION',

	// J
	'jcorner:description' => '<NEW>&laquo;&nbsp;Jolis Coins&nbsp;&raquo; est un outil permettant de modifier facilement l\'aspect des coins de vos {{cadres color&eacute;s}} en partie publique de votre site. Tout est possible, ou presque !
_ Voyez le r&eacute;sultat sur cette page : [->http://www.malsup.com/jquery/corner/].

Listez ci-dessous les objets de votre squelette &agrave; arrondir en utilisant la syntaxe CSS (.class, #id, etc. ). Utilisez le le signe &laquo;&nbsp;=&nbsp;&raquo; pour sp&eacute;cifier la commande jQuery &agrave; utiliser et un double slash (&laquo;&nbsp;//&nbsp;&raquo;) pour les commentaires. En absence du signe &eacute;gal, des coins ronds seront appliqu&eacute;s (&eacute;quivalent &agrave; : <code>.ma_classe = .corner()</code>).[[%jcorner_classes%]]

Attention, cet outil a besoin pour fonctionner du plugin {jQuery} : {Round Corners}. Le Couteau Suisse peut l\'installer directement si vous cochez la case suivante. [[%jcorner_plugin%]]',
	'jcorner:nom' => 'Mooie Hoeken',
	'jcorner_plugin' => '<NEW>&laquo;&nbsp;Round Corners plugin&nbsp;&raquo;',
	'jq_localScroll' => '<NEW>jQuery.LocalScroll ([d&eacute;mo->http://demos.flesler.com/jquery/localScroll/])',
	'jq_scrollTo' => '<NEW>jQuery.ScrollTo ([d&eacute;mo->http://demos.flesler.com/jquery/scrollTo/])',
	'js_defaut' => 'Gebrek',
	'js_jamais' => 'Nooit',
	'js_toujours' => 'Nog steeds',

	// L
	'label:admin_travaux' => 'De openbare site sluiten voor :',
	'label:arret_optimisation' => '<NEW>Emp&ecirc;cher SPIP de vider la corbeille automatiquement&nbsp;:',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Systematische oprichting van het overzicht :',
	'label:balise_decoupe' => '<NEW>Activer la balise #CS_DECOUPE :',
	'label:balise_sommaire' => 'Het baken #CS_SOMMAIRE activeren :',
	'label:bloc_unique' => '<NEW>Un seul bloc ouvert sur la page :',
	'label:couleurs_fonds' => 'De middelen toelaten :',
	'label:cs_rss' => 'Activeren :',
	'label:debut_urls_libres' => '<:label:debut_urls_propres:>',
	'label:debut_urls_propres' => '<NEW>D&eacute;but des URLs :',
	'label:debut_urls_propres2' => '<:label:debut_urls_propres:>',
	'label:decoration_styles' => 'Uw bakens van verpersoonlijkte stijl :',
	'label:derniere_modif_invalide' => '<NEW>Recalculer juste apr&egrave;s une modification :',
	'label:distant_off' => '<NEW>D&eacute;sactiver :',
	'label:dossier_squelettes' => 'Te gebruiken dossier(s) :',
	'label:duree_cache' => 'Duur van het plaatselijke dekblad :',
	'label:duree_cache_mutu' => 'Duur van het dekblad in mutualisatie :',
	'label:expo_bofbof' => '<NEW>Mise en exposants pour : <html>St(e)(s), Bx, Bd(s) et Fb(s)</html>',
	'label:forum_lgrmaxi' => 'Waarde (in karakters) :',
	'label:glossaire_groupes' => 'Gebruikte(n) groep(en) :',
	'label:glossaire_js' => 'Gebruikte techniek :',
	'label:glossaire_limite' => 'Maximumaantal gecre&euml;erde band :',
	'label:insertions' => 'Automatische correcties :',
	'label:jcorner_classes' => '<MODIF>Am&eacute;liorer les coins des s&eacute;lecteurs suivantes :',
	'label:jcorner_plugin' => '<NEW>Installer le plugin {jQuery} suivant :',
	'label:lgr_introduction' => 'Lengte van de samenvatting :',
	'label:lgr_sommaire' => 'Breedte van het overzicht (9 &agrave; 99) :',
	'label:lien_introduction' => 'Punten van vervolg cliquables :',
	'label:liens_interrogation' => 'URLs beschermen :',
	'label:liens_orphelins' => 'Band cliquables :',
	'label:log_couteau_suisse' => '<NEW>Activer :',
	'label:marqueurs_urls_propres' => '<NEW>Ajouter les marqueurs dissociant les objets (SPIP>=2.0) :<br/>(ex. : &laquo;&nbsp;-&nbsp;&raquo; pour -Ma-rubrique-, &laquo;&nbsp;@&nbsp;&raquo; pour @Mon-site@) ',
	'label:marqueurs_urls_propres2' => '<:label:marqueurs_urls_propres:>',
	'label:marqueurs_urls_propres_qs' => '<:label:marqueurs_urls_propres:>',
	'label:max_auteurs_page' => 'Auteurs per bladzijde :',
	'label:message_travaux' => 'Uw bericht van onderhoud :',
	'label:moderation_admin' => '<NEW>Valider automatiquement les messages des : ',
	'label:paragrapher' => 'Nog steeds paragraaf :',
	'label:puce' => 'Openbare chip &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Waarde van de quota :',
	'label:racc_g1' => '<NEW>Entr&eacute;e et sortie de la mise en &laquo;<html>{{gras}}</html>&raquo; :',
	'label:racc_h1' => 'Toegang en output van een &laquo;<html>{{{intertitel}}}</html>&raquo; :',
	'label:racc_hr' => 'Horizontale lijn &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => '<MODIF>Toegang en output van een &laquo;<html>{italique}</html>&raquo; :',
	'label:radio_desactive_cache3' => '<MODIF>het dekblad deactiveren :',
	'label:radio_desactive_cache4' => '<NEW>Utilisation du cache :',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Nieuw venster voor de externe band :',
	'label:radio_type_urls3' => 'Formaat van URLs :',
	'label:scrollTo' => 'Plaatsen volgend {jQuery} plugins :',
	'label:separateur_urls_page' => '<NEW>Caract&egrave;re de s&eacute;paration \'type-id\'<br/>(ex. : ?article-123) :',
	'label:set_couleurs' => 'Te gebruiken set :',
	'label:spam_mots' => 'Verboden sequenties :',
	'label:spip_options_on' => '<NEW>Inclure :',
	'label:spip_script' => 'Verzoek script :',
	'label:style_h' => 'Uw stijl :',
	'label:style_p' => 'Uw stijl :',
	'label:suite_introduction' => 'Punten van vervolg :',
	'label:terminaison_urls_arbo' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_libres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_page' => '<MODIF>De uitgang van URls (ex : .html) :',
	'label:terminaison_urls_propres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_propres_qs' => '<:label:terminaison_urls_page:>',
	'label:titre_travaux' => 'Titel van het bericht :',
	'label:titres_etendus' => '<NEW>Activer l\'utilisation &eacute;tendue des balises #TITRE_XXX&nbsp;:',
	'label:tri_articles' => 'Uw keus :',
	'label:url_arbo_minuscules' => 'Het breken van de titels in URLs behouden :',
	'label:url_arbo_sep_id' => 'Het scheidingskarakter \'titel-idem\' in geval van doublon: <br/>(niet gebruiken \'/\')',
	'label:url_glossaire_externe2' => 'Band naar het externe glossarium :',
	'label:urls_arbo_sans_type' => 'Het soort onderwerp SPIP in URLs te kennen geven :',
	'label:webmestres' => '<NEW>Liste des webmestres du site :',
	'liens_en_clair:description' => 'Ter beschikking uw stelt de filter: \'liens_en_clair\'. Uw tekst bevat waarschijnlijk een band hypertexte die niet zichtbaar bij een indruk is. Deze filter voegt tussen haken de bestemming van elke band cliquable (externe band of mails) toe. Opgelet: in manier indruk (parameter \'cs=print\' of \'page=print\' in url van de bladzijde), is deze functionaliteit automatisch toegepast.',
	'liens_en_clair:nom' => 'Band in klaarheid',
	'liens_orphelins:description' => '<MODIF>Cet outil a deux fonctions :

@puce@ {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[%liens_interrogation%]]

@puce@ {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'Mooi URLs',

	// M
	'mailcrypt:description' => '<MODIF>Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d\'activer la messagerie du lecteur. Cet outil antispam tente d\'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.',
	'mailcrypt:nom' => 'MailCrypt',
	'message_perso' => '<NEW>Un grand merci aux traducteurs qui passeraient par ici. Pat ;-)',
	'moderation_admins' => '<NEW>administrateurs authentifi&eacute;s',
	'moderation_message' => '<NEW>Ce forum est mod&eacute;r&eacute; &agrave; priori&nbsp;: votre contribution n\'appara&icirc;tra qu\'apr&egrave;s avoir &eacute;t&eacute; valid&eacute;e par un administrateur du site, sauf si vous &ecirc;tes identifi&eacute; et autoris&eacute; &agrave; poster directement.',
	'moderation_moderee:description' => '<MODIF>Permet de mod&eacute;rer la mod&eacute;ration des forums pour les utilisateurs inscrits. [[%moderation_admin%]][[-->%moderation_redac%]][[-->%moderation_visit%]]',
	'moderation_moderee:nom' => '<NEW>Mod&eacute;ration mod&eacute;r&eacute;e',
	'moderation_redacs' => '<NEW>r&eacute;dacteurs authentifi&eacute;s',
	'moderation_visits' => '<NEW>visiteurs authentifi&eacute;s',
	'modifier_vars' => 'Dit @nb@ parameters wijzigen',
	'modifier_vars_0' => '<NEW>Modifier ces param&egrave;tres',

	// N
	'no_IP:description' => 'Deactiveer het bezoekers IP adressen automatische registratie van uw site uit zorg voor vertrouwelijkheid: SPIP zal dan geen enkel nummer meer IP, noch tijdelijk bij de bezoeken (behouden om de statistieken te beheren of spip.log te voeden), noch in de forums (verantwoordelijkheid).',
	'no_IP:nom' => 'Geen IP opslag',
	'nouveaux' => 'Nieuw',

	// O
	'orientation:description' => '3 nieuwe criteria voor uw skeletten: <code>{portret}</code>, <code>{vierkant}</code> en <code>{landschap}</code>. Ideaal voor de foto\'s indeling in functie van hun vorm.',
	'orientation:nom' => 'De beelden ori&euml;ntatie',
	'outil_actif' => 'Actief werktuig',
	'outil_activer' => 'Activeren',
	'outil_activer_le' => 'Het werktuig activeren',
	'outil_cacher' => 'Niet meer aangeven',
	'outil_desactiver' => 'Buiten dienst zetten.',
	'outil_desactiver_le' => 'het werktuig buiten dienst zetten.',
	'outil_inactif' => 'Inactief werktuig',
	'outil_intro' => '<MODIF>Deze bladzijde zet de functies van plugin op een lijst die uw ter beschikking worden gesteld.<br /><br />Door op de naam van de werktuigen te klikken hieronder, selecteert u degenen waarvan zult kunnen verwisselen u de stand met behulp van de centrale knoop: de geactiveerde werktuigen d&eacute;sactiv&eacute;s en <i>vice versa</i>. Aan elke klik, blijkt de beschrijving onder de lijsten. De categorie&euml;n zijn opvouwbaar en de werktuigen kunnen verborgen worden. Het dubbele-Voor een eerste gebruik, wordt hij aanbevolen om de werktuigen &eacute;&eacute;n voor &eacute;&eacute;n te activeren, ingeval zeker de onverenigbaarheden met uw skelet, SPIP of anderen plugins zouden blijkenklik maakt het mogelijk om een werktuig snel te verwisselen.<br /><br />.<br /><br />Nota : de eenvoudige lading van deze bladzijde compileert het geheel van de werktuigen van het Zwitserland Mes opnieuw.',
	'outil_intro_old' => 'Deze interface is oud.<br /><br />Als u problemen in het gebruik van <a href=\' ./? exec=admin_couteau_suisse\'> nieuwe interface ondervindt</a>, aarzelt niet aandeel ervan doen over het forum van <a href=\'http://contrib.spip.net?article2166\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ werktuig',
	'outil_nbs' => '@pipe@ : @nb@ werktuigen',
	'outil_permuter' => 'Het werktuig verwisselen : &laquo; @text@ &raquo; ?',
	'outils_actifs' => 'Actieve werktuigen :',
	'outils_caches' => 'Verborgen werktuigen :',
	'outils_cliquez' => 'Klikt op de naam van de werktuigen hierboven om hun beschrijving hier te kennen te geven.',
	'outils_inactifs' => 'Inactief werktuig :',
	'outils_liste' => 'Lijst van de werktuigen van het Mes Zwitserland',
	'outils_permuter_gras1' => 'De werktuigen in vet verwisselen',
	'outils_permuter_gras2' => '@nb@ werktuigen in vet verwisselen ?',
	'outils_resetselection' => 'De selectie eriniti&euml;ren',
	'outils_selectionactifs' => 'Alle actieve werktuigen selecteren',
	'outils_selectiontous' => 'IEDEREEN',

	// P
	'pack_actuel' => '<NEW>Pack @date@',
	'pack_actuel_avert' => '<NEW>Attention, les surcharges sur les define() ou les globales ne sont pas sp&eacute;cifi&eacute;es ici',
	'pack_actuel_titre' => '<NEW>PACK ACTUEL DE CONFIGURATION DU COUTEAU SUISSE',
	'pack_alt' => 'Zie de lopende parameters van configuratie',
	'pack_descrip' => '<MODIF>Uw &#132;huidige configuratie Pakijs&#147; verzamelt het geheel van de lopende configuratie parameters betreffende van het Mes Zwitserland: de activering van de werktuigen en de waarde van hun eventuele variabele.

Deze PHP code kan plaats in het bestand /config/mes_options.php nemen en zal een band van r&eacute;initialisatie op deze bladzijde &#132;van het pakijs {Pakijs Huidige}&#147; toevoegen. Natuurlijk is het u mogelijk om zijn naam hieronder te veranderen.

Als u plugin r&eacute;initialiserd door op een pakijs te klikken, reconfiguratie van het Zwitserland mes  automatisch in functie van het pakijs voor bepaald parameters.',
	'pack_du' => '• van het pakijs @pack@',
	'pack_installe' => 'Het invoeren van een configuratie pakijs',
	'pack_nb_plrs' => '<NEW>Il y a actuellement @nb@ &laquo;&nbsp;packs de configuration&nbsp;&raquo; disponibles.',
	'pack_nb_un' => 'Er is een &laquo;&nbsp;configuration pack&nbsp;&raquo; momenteel beschikbaar',
	'pack_nb_zero' => 'Er is geen &laquo;&nbsp;configuration pack&nbsp;&raquo; momenteel beschikbaar.',
	'pack_outils_defaut' => '<NEW>Installation des outils par d&eacute;faut',
	'pack_sauver' => '<NEW>Sauver la configuration actuelle',
	'pack_sauver_descrip' => '<NEW>Le bouton ci-dessous vous permet d\'ins&eacute;rer directement dans votre fichier <b>@file@</b> les param&egrave;tres n&eacute;cessaires pour ajouter un &laquo;&nbsp;pack de configuration&nbsp;&raquo; dans le menu de gauche. Ceci vous permettra ult&eacute;rieurement de reconfigurer en un clic votre Couteau Suisse dans l\'&eacute;tat o&ugrave; il est actuellement.',
	'pack_titre' => 'Huidige configuratie',
	'pack_variables_defaut' => '<NEW>Installation des variables par d&eacute;faut',
	'par_defaut' => 'Per gebrek',
	'paragrapher2:description' => 'De <code>paragrapher()</code> SPIP functie neemt bakens &lt;p&gt; en &lt;/p&gt; in alle teksten die zonder paragrafen zijn. Teneinde fijner uw stijlen en uw opmaak te beheren, hebt u de mogelijkheid om het aspect van de teksten van uw site uniform te maken.',
	'paragrapher2:nom' => 'Paragraaf',
	'pipelines' => 'Gebruikte pijpleidingen&nbsp;:',
	'pucesli:description' => 'Vervangt de chips &laquo;-&raquo; (eenvoudig koppelteken) van de artikelen door genoteerde lijsten &laquo;-*&raquo; (in HTML door worden vertaald: &lt;ul>&lt;li>…&lt;/li>&lt;/ul>) en waarvan de stijl per css verpersoonlijkt kan worden.',
	'pucesli:nom' => 'Mooie chips',

	// R
	'raccourcis' => 'Actieve typografische kortere wegen van het Mes Zwitserland&nbsp;:',
	'raccourcis_barre' => 'De typografische kortere wegen van het Mes Zwitserland',
	'reserve_admin' => 'Toegang die voor de beheerders is gereserveerd.',
	'rss_actualiser' => '<NEW>Actualiser',
	'rss_attente' => 'Wachten RSS...',
	'rss_desactiver' => '&laquo; de Revisies van het Mes Zwitserland &raquo; deactiveren ',
	'rss_edition' => 'Flux RSS worden bijgewerkt die :',
	'rss_source' => '<NEW>Source RSS',
	'rss_titre' => '&laquo;&nbsp;Het Zwitserland Mes&nbsp;&raquo; in ontwikkeling :',
	'rss_var' => 'De revisies van het Zwitserland Mes',

	// S
	'sauf_admin' => 'Iedereen, behalve de beheerders',
	'set_options:description' => 'Selecteert automatisch het soort particuliere interface (vereenvoudigd of geavanceerd) voor alle redacteuren reeds bestaand of om te komen en schaft de kleine ikonen hoofdband overeenkomstige af.[[%radio_set_options4%]]',
	'set_options:nom' => 'Soort particuliere interface',
	'sf_amont' => 'Voorafgaand',
	'sf_tous' => 'Iedereen',
	'simpl_interface:description' => 'Deactiveer het menu van snelle statuut verandering van een artikel aan het overzicht van zijn kleurrijke chip. Dat is nuttig als u probeert om het meest ontdaan mogelijke van particuliere een interface te verkrijgen ten einde de prestaties klant te optimaliseren.',
	'simpl_interface:nom' => 'Vermindering van de particuliere interface',
	'smileys:aide' => 'Smileys : @liste@',
	'smileys:description' => '<MODIF><MODIF>Ins&egrave;re des smileys dans tous les textes o&ugrave; appara&icirc;t un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Smileys',
	'soft_scroller:description' => '<MODIF>Offre &agrave; votre site public un d&eacute;filement  adouci de la page lorsque le visiteur clique sur un lien pointant vers une ancre : tr&egrave;s utile pour &eacute;viter de se perdre dans une page complexe ou un texte tr&egrave;s long...

Attention, cet outil a besoin pour fonctionner de pages au &laquo;DOCTYPE XHTML&raquo; (non HTML !) et de deux plugins {jQuery} : {ScrollTo} et {LocalScroll}. Le Couteau Suisse peut les installer directement si vous cochez les cases suivantes. [[%scrollTo%]][[-->%LocalScroll%]]
@_CS_PLUGIN_JQUERY192@',
	'soft_scroller:nom' => 'Zachte ankers',
	'sommaire:description' => '<MODIF>Construit un sommaire pour le texte de vos articles et de vos rubriques afin d’acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

@puce@ Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ • Syst&eacute;matique pour chaque article (une balise <code>@_CS_SANS_SOMMAIRE@</code> plac&eacute;e n’importe o&ugrave; &agrave; l’int&eacute;rieur du texte de l’article cr&eacute;era une exception).
_ • Uniquement pour les articles contenant la balise <code>@_CS_AVEC_SOMMAIRE@</code>.

[[%auto_sommaire%]]

@puce@ Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d\'article automatiquement. Mais vous avez la possibilit&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : &laquo;&nbsp;[.->decoupe]&nbsp;&raquo;.',
	'sommaire:nom' => '<MODIF>Een overzicht voor uw artikelen',
	'sommaire_avec' => 'Een artikel met overzicht&nbsp;: <b>@_CS_AVEC_SOMMAIRE@</b>',
	'sommaire_sans' => 'Een artikel zonder overzicht&nbsp;: <b>@_CS_SANS_SOMMAIRE@</b>',
	'spam:description' => '<MODIF>Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.

Listez ici les s&eacute;quences interdites@_CS_ASTER@ en les s&eacute;parant par des espaces. [[%spam_mots%]]
@_CS_ASTER@Pour sp&eacute;cifier un mot entier, mettez-le entre paranth&egrave;ses. Pour une expression avec des espaces, placez-la entre guillemets.',
	'spam:nom' => 'SPAM Bestrijding',
	'spip_cache:description' => '<MODIF>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[%radio_desactive_cache3%]]@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:description1' => '<NEW>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. @_CS_CACHE_EXTENSION@[[%radio_desactive_cache3%]]',
	'spip_cache:description2' => '<NEW>@puce@ Quatre options pour orienter le fonctionnement du cache de SPIP : <q1>
_ &bull; {Usage normal} : SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. Apr&egrave;s un certain d&eacute;lai, le cache est recalcul&eacute; et stock&eacute;.
_ &bull; {Cache permanent} : les d&eacute;lais d\'invalidation du cache sont ignor&eacute;s.
_ &bull; {Pas de cache} : d&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. Ici, rien n\'est stock&eacute; sur le disque.
_ &bull; {Contr&ocirc;le du cache} : option identique &agrave; la pr&eacute;c&eacute;dente, avec une &eacute;criture sur le disque de tous les r&eacute;sultats afin de pouvoir &eacute;ventuellement les contr&ocirc;ler.</q1>[[%radio_desactive_cache4%]]',
	'spip_cache:nom' => 'SPIP en het dekblad…',
	'stat_auteurs' => 'De auteurs in stat',
	'statuts_spip' => 'Alleen de volgende SPIP statuten :',
	'statuts_tous' => 'Alle statuten',
	'suivi_forums:description' => '<NEW>Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Opvolging van de openbare forums',
	'supprimer_cadre' => 'Dit kader afschaffen',
	'supprimer_numero:description' => '<MODIF>Applique la fonction SPIP supprimer_numero() &agrave; l\'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d\'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Schaft het nummer af',

	// T
	'titre' => 'Het Zwitserland Mes',
	'titre_parent:description' => '<MODIF>Au sein d\'une boucle, il est courant de vouloir afficher le titre du parent de l\'objet en cours. Traditionnellement, il suffirait d\'utiliser une seconde boucle, mais cette nouvelle balise #TITRE_PARENT all&eacute;gera l\'&eacute;criture de vos squelettes. Le r&eacute;sultat renvoy&eacute; est : le titre du groupe d\'un mot-cl&eacute; ou celui de la rubrique parente (si elle existe) de tout autre objet (article, rubrique, br&egrave;ve, etc.).

Notez : Pour les mots-cl&eacute;s, un alias de #TITRE_PARENT est #TITRE_GROUPE. Le traitement SPIP de ces nouvelles balises est similaire &agrave; celui de #TITRE.

@puce@ Si vous &ecirc;tes sous SPIP 2.0, alors vous avez ici &agrave; votre disposition tout un ensemble de balises #TITRE_XXX qui pourront vous donner le titre de l\'objet \'xxx\', &agrave; condition que le champ \'id_xxx\' soit pr&eacute;sent dans la table en cours (#ID_XXX utilisable dans la boucle en cours).

Par exemple, dans une boucle sur (ARTICLES), #TITRE_SECTEUR donnera le titre du secteur dans lequel est plac&eacute; l\'article en cours, puisque l\'identifiant #ID_SECTEUR (ou le champ \'id_secteur\') est disponible dans ce cas.[[%titres_etendus%]]',
	'titre_parent:nom' => '<MODIF>Balise #TITRE_PARENT',
	'titre_tests' => 'Het Zwitserland Mes - Tests Bladzijde…',
	'tous' => 'Iedereen',
	'toutes_couleurs' => 'De 36 kleuren van de css stijlen :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Meertalige blokken&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => '<MODIF><MODIF>&Agrave; l\'instar de ce vous pouvez d&eacute;j&agrave; faire dans vos squelettes, cet outil vous permet d\'utiliser librement les cha&icirc;nes de langues (de SPIP ou de vos squelettes) dans tous les contenus de votre site (articles, titres, messages, etc.) &agrave; l\'aide du raccourci <code><:chaine:></code>.
 
Consultez [ici ->http://www.spip.net/fr_article2128.html] la documentation de SPIP &agrave; ce sujet.

Cet outil accepte &eacute;galement les arguments introduits par SPIP 2.0. Par exemple, le raccourci <code><:ma_chaine{nom=Charles Martin, age=37}:></code> permet de passer deux param&egrave;tres &agrave; la cha&icirc;ne suivante : <code>\'ma_chaine\'=>"Bonjour, je suis @nom@ et j\'ai @age@ ans\\"</code>.

La fonction SPIP utilis&eacute;e en PHP est <code>_T(\'chaine\')</code> sans argument, et  <code>_T(\'chaine\', array(\'arg1\'=>\'un texte\', \'arg2\'=>\'un autre texte\'))</code> avec arguments.

 N\'oubliez donc pas de v&eacute;rifier que la clef <code>\'chaine\'</code> est bien d&eacute;finie dans les fichiers de langues.',
	'toutmulti:nom' => 'Meertalige blokken',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Deze site zal zeer binnenkort hersteld worden.
_ Bedankt voor uw begrip.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => '<MODIF>En naviguant sur le site en partie priv&eacute;e ([->./?exec=auteurs]), choisissez ici le tri &agrave; utiliser pour afficher vos articles &agrave; l\'int&eacute;rieur de vos rubriques.

Les propositions ci-dessous sont bas&eacute;es sur la fonctionnalit&eacute; SQL \'ORDER BY\' : n\'utilisez le tri personnalis&eacute; que si vous savez ce que vous faites (champs disponibles : {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => 'Sorteren van de artikelen',
	'tri_modif' => 'Sorteren op de wijzigingsdatum (ORDER BY date_modif DESC)',
	'tri_perso' => 'Verpersoonlijkt sorteren SQL, ORDER BY gevolgd door :',
	'tri_publi' => 'Sorteren op het jaartal (ORDER BY date DESC)',
	'tri_titre' => 'Sorteren op de titel (ORDER BY 0+titre,titre)',
	'type_urls:description' => '<MODIF>@puce@ SPIP offre un choix sur plusieurs jeux d\'URLs pour fabriquer les liens d\'acc&egrave;s aux pages de votre site.

Plus d\'infos : [->http://www.spip.net/fr_article765.html]. L\'outil &laquo;&nbsp;[.->boites_privees]&nbsp;&raquo; vous permet de voir sur la page de chaque objet SPIP l\'URL propre associ&eacute;e.
[[%radio_type_urls3%]]
<q3>@_CS_ASTER@pour utiliser les formats {html}, {propres}, {propres2}, {libres} ou {arborescentes}, recopiez le fichier "htaccess.txt" du r&eacute;pertoire de base du site SPIP sous le sous le nom ".htaccess" (attention &agrave; ne pas &eacute;craser d\'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en "sous-r&eacute;pertoire", vous devrez aussi &eacute;diter la ligne "RewriteBase" ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</q3>

<radio_type_urls3 valeur="page">@puce@ {{URLs &laquo;page&raquo;}} : ce sont les liens par d&eacute;faut, utilis&eacute;s par SPIP depuis sa version 1.9x.
_ Exemple : <code>/spip.php?article123</code>[[%terminaison_urls_page%]][[%separateur_urls_page%]]</radio_type_urls3>

<radio_type_urls3 valeur="html">@puce@ {{URLs &laquo;html&raquo;}} : les liens ont la forme des pages html classiques.
_ Exemple : <code>/article123.html</code></radio_type_urls3>

<radio_type_urls3 valeur="propres">@puce@ {{URLs &laquo;propres&raquo;}} : les liens sont calcul&eacute;s gr&acirc;ce au titre des objets demand&eacute;s. Des marqueurs (_, -, +, @, etc.) encadrent les titres en fonction du type d\'objet.
_ Exemples : <code>/Mon-titre-d-article</code> ou <code>/-Ma-rubrique-</code> ou <code>/@Mon-site@</code>[[%terminaison_urls_propres%]][[%debut_urls_propres%]][[%marqueurs_urls_propres%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres2">@puce@ {{URLs &laquo;propres2&raquo;}} : l\'extension \'.html\' est ajout&eacute;e aux liens {&laquo;propres&raquo;}.
_ Exemple : <code>/Mon-titre-d-article.html</code> ou <code>/-Ma-rubrique-.html</code>
[[%debut_urls_propres2%]][[%marqueurs_urls_propres2%]]</radio_type_urls3>

<radio_type_urls3 valeur="libres">@puce@ {{URLs &laquo;libres&raquo;}} : les liens sont {&laquo;propres&raquo;}, mais sans marqueurs dissociant les objets (_, -, +, @, etc.).
_ Exemple : <code>/Mon-titre-d-article</code> ou <code>/Ma-rubrique</code>
[[%terminaison_urls_libres%]][[%debut_urls_libres%]]</radio_type_urls3>

<radio_type_urls3 valeur="arbo">@puce@ {{URLs &laquo;arborescentes&raquo;}} : les liens sont {&laquo;propres&raquo;}, mais de type arborescent.
_ Exemple : <code>/secteur/rubrique1/rubrique2/Mon-titre-d-article</code>
[[%url_arbo_minuscules%]][[%urls_arbo_sans_type%]][[%url_arbo_sep_id%]][[%terminaison_urls_arbo%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres-qs">@puce@ {{URLs &laquo;propres-qs&raquo;}} : ce syst&egrave;me fonctionne en "Query-String", c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont {&laquo;propres&raquo;}.
_ Exemple : <code>/?Mon-titre-d-article</code>
[[%terminaison_urls_propres_qs%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres_qs">@puce@ {{URLs &laquo;propres_qs&raquo;}} : ce syst&egrave;me fonctionne en "Query-String", c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont {&laquo;propres&raquo;}.
_ Exemple : <code>/?Mon-titre-d-article</code>
[[%terminaison_urls_propres_qs%]][[%marqueurs_urls_propres_qs%]]</radio_type_urls3>

<radio_type_urls3 valeur="standard">@puce@ {{URLs &laquo;standard&raquo;}} : ces liens d&eacute;sormais obsol&egrave;tes &eacute;taient utilis&eacute;s par SPIP jusqu\'&agrave; sa version 1.8.
_ Exemple : <code>article.php3?id_article=123</code>
</radio_type_urls3>

@puce@ Si vous utilisez le format {page} ci-dessus ou si l\'objet demand&eacute; n\'est pas reconnu, alors il vous est possible de choisir {{le script d\'appel}} &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (exemple de format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l\'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => 'Formaat van URLs',
	'typo_exposants:description' => '<MODIF>{{Textes fran&ccedil;ais}} : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).

Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).

Sont aussi trait&eacute;es les expressions suivantes : <html>Dr, Pr, Mgr, m2, m3, Mn, Md, St&eacute;, &Eacute;ts, Vve, Cie, 1o, 2o, etc.</html> 

Choisissez ici de mettre en exposant certains raccourcis suppl&eacute;mentaires, malgr&eacute; un avis d&eacute;favorable de l\'Imprimerie nationale :[[%expo_bofbof%]]

{{Textes anglais}} : mise en exposant des nombres ordinaux : <html>1st, 2nd</html>, etc.',
	'typo_exposants:nom' => 'Typografische inzenders',

	// U
	'url_arbo' => 'boomvormig@_CS_ASTER@',
	'url_html' => 'html@_CS_ASTER@',
	'url_libres' => 'libres@_CS_ASTER@',
	'url_page' => 'bladzijde',
	'url_propres' => 'proper@_CS_ASTER@',
	'url_propres-qs' => 'propers-qs',
	'url_propres2' => 'proper2@_CS_ASTER@',
	'url_propres_qs' => 'propres_qs',
	'url_standard' => 'standaard',
	'urls_base_total' => '<NEW>Il y a actuellement @nb@ URL(s) en base',
	'urls_base_vide' => '<NEW>La base des URLs est vide',
	'urls_choix_objet' => '<MODIF>Edition en base de l\'URL d\'un objet sp&eacute;cifique&nbsp;:',
	'urls_edit_erreur' => '<NEW>Le format actuel des URLs (&laquo;&nbsp;@type@&nbsp;&raquo;) ne permet pas d\'&eacute;dition.',
	'urls_enregistrer' => '<NEW>Enregistrer cette URL en base',
	'urls_nouvelle' => '<MODIF>&Eacute;diter l\'URL &laquo;&nbsp;propres&nbsp;&raquo;&nbsp;:',
	'urls_num_objet' => 'Nummer&nbsp;:',
	'urls_purger' => 'Alles legen',
	'urls_purger_tables' => 'De geselecteerde tafels legen',
	'urls_purger_tout' => '<NEW>R&eacute;initialiser les URLs stock&eacute;es dans la base&nbsp;:',
	'urls_rechercher' => '<NEW>Rechercher cet objet en base',
	'urls_titre_objet' => '<NEW>Titre enregistr&eacute; &nbsp;:',
	'urls_type_objet' => '<NEW>Objet&nbsp;:',
	'urls_url_calculee' => '<NEW>URL publique &laquo;&nbsp;@type@&nbsp;&raquo;&nbsp;:',
	'urls_url_objet' => '<MODIF>URL &laquo;&nbsp;propres&nbsp;&raquo; enregistr&eacute;e&nbsp;:',
	'urls_valeur_vide' => '<MODIF>(Une valeur vide entraine la suppression de l\'URL)',

	// V
	'validez_page' => 'Om de wijzigingen te bereiken :',
	'variable_vide' => '(Leegte)',
	'vars_modifiees' => 'De gegevens werden wel degelijk gewijzigd',
	'version_a_jour' => 'Uw versie is aan dag.',
	'version_distante' => 'Verwijderde versie...',
	'version_distante_off' => 'D&eacute;sactiv&eacute;e verwijderde verificatie',
	'version_nouvelle' => 'Nieuwe versie : @version@',
	'version_revision' => 'Revisie : @revision@',
	'version_update' => 'Automatische update',
	'version_update_chargeur' => 'Automatische download',
	'version_update_chargeur_title' => 'Downloadt de laatste versie van plugin dank zij plugin &laquo;Downloade&raquo;',
	'version_update_title' => 'Downloadt de laatste versie van plugin en lanceert zijn automatische update',
	'verstexte:description' => '<MODIF>2 filters voor uw skeletten, die het mogelijk maken om lichtere bladzijdes te produceren.
_ de tekst_versie : uitgetrokken de inhoud tekst van een HTML bladzijde met uitsluiting van enkele elementaire bakens.
_ volle_tekst_versie : uitgetrokken de inhoud tekst van een HTML bladzijde om van de volle tekst terug te geven.',
	'verstexte:nom' => 'Tekst versie',
	'visiteurs_connectes:description' => 'Aanbod een hazelnoot voor uw skelet dat het aantal bezoekers te kennen geeft die op de openbare plaats worden aangesloten.

Ajoutez simplement <code><INCLURE{fond=fonds/visiteurs_connectes}></code> dans vos pages.',
	'visiteurs_connectes:nom' => 'Aangesloten bezoekers',
	'voir' => 'Zie : @voir@',
	'votre_choix' => 'Uw keus :',

	// W
	'webmestres:description' => '<MODIF>Een SPIP {{webmestre}} is een {{beheerder}} hebben toegang aan ruimte FTP. Bij verstek en vanaf SPIP 2.0, is hij de beheerder <code>id_auteur=1</code> van de site. Hier bepaalde webmestres hebben het voorrecht om niet meer om door FTP verplicht te worden voorbij te gaan om de belangrijke verrichtingen van de plaats te valideren, zoals de update van de database of de restauratie van een dump.

Huidige Webmestre : {@_CS_LISTE_WEBMESTRES@}.
_ In aanmerking komende beheerder : {@_CS_LISTE_ADMINS@}.

Als webmestre zelf, hebt u hier de rechten om deze lijst van ids te wijzigen  gescheiden door beide punten &laquo;&nbsp;:&nbsp;&raquo; als zij verschillende zijn. Exemple : &laquo;1:5:6&raquo;.[[%webmestres%]]',
	'webmestres:nom' => 'Webmestres lijst',

	// X
	'xml:description' => 'Actief validateur xml voor de openbare ruimte zoals hij in  [documentatie->http://www.spip.net/en_article3582.html]  wordt beschreven. Een knoop getiteld &laquo;&nbsp;Analyse XML&nbsp;&raquo; wordt aan de andere knopen van bestuur toegevoegd.',
	'xml:nom' => 'XML Validatie'
);

?>
