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
	'auteur_forum:description' => 'Zet alle auteurs van openbare berichten ertoe aan om te vullen (van minstens van een brief!) het veld &laquo;@_CS_FORUM_NOM@&raquo; teneinde de volkomen onbekende bijdragen te vermijden.',
	'auteur_forum:nom' => 'Geen onbekende forums',
	'auteurs:description' => 'Dit werktuig configureert de schijn van [de bladzijde van de auteurs ->./?exec=auteurs], gedeeltelijk particulier.

@puce@ Bepaalt hier het maximumaantal auteurs die op het centrale kader van de bladzijde van de auteurs moeten aangegeven worden. Verder is een paginering opgesteld.[[%max_auteurs_page%]]

@puce@ Welke statuten van auteurs kunnen op deze bladzijde op een lijst gezet worden ?
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'Bladzijde van de auteurs',

	// B
	'basique' => 'Fundamenteel',
	'blocs:aide' => 'Openvouwen blokken : <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) et <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Laat u toe om blokken te cre&euml;ren waarvan de titel cliquable ze zichtbaar of onzichtbaar kan maken.

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
',
	'blocs:nom' => 'Openvouwen Blokken',
	'boites_privees:description' => 'Alle beschreven dozen hieronder komen in het particuliere deel voor.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{De revisies van het Zwitserland Mes}} : een kader op deze bladzijde van configuratie, dat op de laatste wijzigingen wijst die aan de code van plugin worden aangebracht ([Source->@_CS_RSS_SOURCE@]).
- {{De artikelen aan het SPIP formaat}} : een aanvullend opvouwbaar kader voor uw artikelen teneinde de code bron te kennen die door hun auteurs wordt gebruikt.
- {{De auteurs in stat}} : een kader aanvullend op [de bladzijde van de auteurs->./?exec=auteurs] die de 10 aangesloten laatsten en de niet bevestigde inschrijvingen aangeven. Enkel de beheerders zien deze informatie.',
	'boites_privees:nom' => 'Particuliere dozen',

	// C
	'categ:admin' => '1. Administratie',
	'categ:divers' => '60. Diversen',
	'categ:interface' => '10. Interface priv&eacute;e',
	'categ:public' => '40. Openbare display',
	'categ:spip' => '50. Bakens, filters, criteria',
	'categ:typo-corr' => '20. Teksten verbeteringen',
	'categ:typo-racc' => '30. Typografische kortere wegen',
	'certaines_couleurs' => 'Enkel de hieronder bepaalde bakens@_CS_ASTER@ :',
	'chatons:aide' => 'Katjes : @liste@',
	'chatons:description' => 'Neemt beelden (of katjes voor {tchats}) op in alle teksten waar een keten van het soort blijkt <code>:nom</code>.
_ Dit werktuig vervangt deze link door de beelden van dezelfde naam die hij in de lijst vindtplugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Katjes',
	'class_spip:description1' => 'U kunt hier bepaalde kortere wegen van SPIP bepalen. Een lege waarde staat gelijk om de waarde per gebrek te gebruiken.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{De kortere wegen van SPIP}}.

U kunt hier bepaalde kortere wegen van SPIP bepalen. Een lege waarde staat gelijk om de waarde per gebrek te gebruiken.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

SPIP gebruikt gewoonlijk het baken &lt;h3&gt; voor intertitres. Kiest hier een andere vervanging :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP heeft verkozen om het baken &lt;i> te gebruiken om italiques over te schrijven. Maar &lt;em> had eveneens kunnen passen. Aan u te zien :[[%racc_i1%]][[->%racc_i2%]]
Merkt op: door de vervanging van de kortere wegen van italiques te wijzigen, de stijl {{2.}} hierboven zal gespecificeerd niet toegepast zijn.

@puce@ {{De stijlen van SPIP}}. Tot de versie 1.92 van SPIP, produceerden de typografische kortere wegen bakens systematisch van de stijl "spip". Par voorbeeld : <code><p class="spip"></code>. U kunt hier de stijl van deze bakens bepalen in functie van uw bladen van stijl. Een leeg hokje betekent dat geen enkele bijzondere stijl zal toegepast zijn.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Bakens &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; en de lijsten (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Bakens &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[%style_h%]]

Merkt op: door deze tweede parameter te wijzigen, verliest u dan de standaardstijlen die met deze bakens worden verenigd.</blockquote>',
	'class_spip:nom' => 'SPIP en zijn kortere wegen…',
	'code_css' => 'CSS',
	'code_fonctions' => 'Functies',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Opties',
	'contrib' => 'Meer infos : @url@',
	'couleurs:aide' => 'Inzet in kleuren: <b>[coul]tekst[/coul] </b>@fond@ met <b>coul</b> = @liste@',
	'couleurs:description' => 'Maakt het mogelijk om kleuren gemakkelijk toe te passen op alle teksten van de plaats (artikelen, kort, titels, forum,…) door bakens in kortere wegen te gebruiken.

Twee identieke voorbeelden om de kleur van de tekst te veranderen:@_CS_EXEMPLE_COULEURS2@

Idem om de bodem te veranderen, als de keuze hieronder het toelaat:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@Het formaat van deze verpersoonlijkte bakens moet bestaande kleuren op een lijst zetten of paren &laquo;balise=couleur&raquo;, bepalen, alles die door komma\'s wordt gescheiden. Voorbeelden. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; of nog &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Voor de eerste en het laatste voorbeeld, zijn de toegelaten bakens : <code>[gris]</code> en <code>[rouge]</code> (<code>[fond gris]</code> en <code>[fond rouge]</code> als de middelen toegestaan zijn).',
	'couleurs:nom' => 'Erg in kleuren',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',

	// D
	'decoration:aide' => 'Versiering&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b>, met <b>balise</b> = @liste@',
	'decoration:description' => '<NEW>De nouveaux styles param&eacute;trables dans vos textes et accessibles gr&acirc;ce &agrave; des balises &agrave; chevrons. Exemple : 
&lt;mabalise&gt;texte&lt;/mabalise&gt; ou : &lt;mabalise/&gt;.<br />D&eacute;finissez ci-dessous les styles CSS dont vous avez besoin, une balise par ligne, selon les syntaxes suivantes :
- {type.mabalise = mon style CSS}
- {type.mabalise.class = ma classe CSS}
- {type.mabalise.lang = ma langue (ex : fr)}
- {unalias = mabalise}

Le param&egrave;tre {type} ci-dessus peut prendre trois valeurs :
- {span} : balise &agrave; l\'int&eacute;rieur d\'un paragraphe (type Inline)
- {div} : balise cr&eacute;ant un nouveau paragraphe (type Block)
- {auto} : balise d&eacute;termin&eacute;e automatiquement par le plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Versiering',
	'decoupe:aide' => 'Blok tabben : <b>&lt;onglets>&lt;/onglets></b><br/>S&eacute;parateur van bladzijdes of tabben&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias&nbsp;:&nbsp;@sep@',
	'decoupe:description' => '<NEW>D&eacute;coupe l\'affichage public d\'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l\'endroit qui doit recevoir la coupure.
_ Si vous utilisez ce s&eacute;parateur &agrave; l\'int&eacute;rieur des balises &lt;onglets&gt; et &lt;/onglets&gt; alors vous obtiendrez un jeu d\'onglets.
_ Dans les squelettes : vous avez &agrave; votre disposition les nouvelles balises #ONGLETS_DEBUT, #ONGLETS_TITRE et #ONGLETS_FIN.
_ Cet outil peut &ecirc;tre coupl&eacute; avec {Un sommaire pour vos articles}.',
	'decoupe:nom' => 'In bladzijdes en tabben snijden',
	'desactiver_flash:description' => 'Schaft de onderwerpen flash van de bladzijdes van uw plaats af en vervangt ze door de verenigde alternatieve inhoud.',
	'desactiver_flash:nom' => 'De activering terugtrekken van de onderwerpen flash',
	'detail_balise_etoilee' => '<NEW>{{Attention}} : V&eacute;rifiez bien l\'utilisation faite par vos squelettes des balises &eacute;toil&eacute;es. Les traitements de cet outil ne s\'appliqueront pas sur : @bal@.',
	'detail_fichiers' => 'Bestanden :',
	'detail_inline' => 'Code inline :',
	'detail_jquery1' => '{{Aandacht}}: dit werktuig maakt plugin {jQuery} het noodzakelijk om met deze versie van SPIP te werken.',
	'detail_jquery2' => 'Dit werktuig vereist de boekhandel {jQuery}.',
	'detail_pipelines' => 'Pijpleidingen :',
	'detail_traitements' => 'Behandelingen :',
	'dossier_squelettes:description' => '<NEW>Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. En laissant vide la case qui suit (ou en tapant &quot;dist&quot;), c\'est le squelette original &quot;dist&quot; fourni par SPIP qui sera utilis&eacute;.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => '<NEW>Dossier du squelette',

	// E
	'effaces' => 'Uitgewist',
	'en_travaux:description' => 'Maakt het mogelijk om een aan de persoonlijke behoeften aanpasbaar bericht te kennen te geven gedurende een fase van onderhoud op de hele openbare site.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site in werkzaamheden',
	'erreur:description' => 'id gebrek hebbend aan in de definitie van het werktuig !',
	'erreur:distant' => 'de verwijderde server',
	'erreur:js' => 'Een fout JavaScript schijnt op deze bladzijde voorgekomen zijn en verhindert zijn goede werking. Gelieve JavaScript op uw navigator activeren om af-activeren sommige plugins SPIP van uw site.',
	'erreur:nojs' => 'JavaScript wordt op deze bladzijde af-activeerd.',
	'erreur:nom' => 'Fout !',
	'erreur:probleme' => 'Zurig probleem : @pb@',
	'erreur:traitements' => '<NEW>Le Couteau Suisse - Erreur de compilation des traitements : m&eacute;lange \'typo\' et \'propre\' interdit !',
	'erreur:version' => 'Dit werktuig is niet beschikbaar in deze versie van SPIP.',
	'etendu' => 'Uitgestrekt',

	// F
	'f_jQuery:description' => 'Verhindert de installatie van {jQuery} in het openbare deel teneinde e&eacute;conmiser een beetje van &#132;tijd bekokstooft&#147;. Deze boekhandel ([- > http://jquery.com/]) brengt talrijke gerief in de programmering van Javascript en kan door bepaalde plugins gebruikt worden. SPIP gebruikt het bij zijn deel priv&eacute;e.

Opgelet: bepaalde werktuigen van het Zwitserland Mes vereisen de functies van {jQuery}.',
	'f_jQuery:nom' => 'Inactieve jQuery.',
	'filets_sep:aide' => 'Scheidingsnetten&nbsp;: <b>__i__</b> waar <b>i</b> is een aantal.<br />Andere beschikbare netten : @liste@',
	'filets_sep:description' => 'Neemt scheidingsnetten op, aan de persoonlijke behoeften aanpasbaar door bladen van stijl, in alle teksten van SPIP.
_ De syntaxis is : "__code__", waar &#132;de code&#147; vertegenwoordigt ofwel het identificatienummer (van 0 tot 7) van het net dat in rechtstreeks verband met de overeenkomstige stijlen, ofwel de naam van een beeld moet opgenomen worden dat in het dossier wordt geplaatst plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Scheidingsnetten',
	'filtrer_javascript:description' => '<MODIF>Om javascript in de artikelen te beheren, zijn drie manieren beschikbaar :
- <i>nooit</i>: javascript wordt overal geweigerd
- <i>het gebrek</i>: javascript is in rood in de particuliere ruimte aangeduid
- <i>nog steeds</i>: javascript wordt overal aanvaard.

Opgelet: in de forums, petities, georganiseerde stromen, enz, het beleid van javascript <b>wordt altijd</b> veiliggesteld.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Beleid van javascript',
	'flock:description' => 'D&eacute;sactiveren het systeem van grendeling van bestanden door de functie PHP {flock()} te neutraliseren. Bepaalde onderdak geeft immers ernstige problemen ten gevolge van een onaangepast systeem van bestanden of een gebrek aan synchronisatie. Activeert niet dit werktuig als uw plaats normaal werkt.',
	'flock:nom' => 'Geen grendeling van bestanden',
	'fonds' => 'Bodem :',
	'forcer_langue:description' => 'Kracht de context van taal voor de spelen van meertalige skeletten die over een formulier of over een menu van talen beschikken die cookie van talen kunnen beheren.',
	'forcer_langue:nom' => 'Taal forceren',
	'format_spip' => 'De artikelen aan het SPIP formaat',
	'forum_lgrmaxi:description' => '<NEW>Par d&eacute;faut les messages de forum ne sont pas limit&eacute;s en taille. Si cet outil est activ&eacute;, un message d\'erreur s\'affichera lorsque quelqu\'un voudra poster un message  d\'une taille sup&eacute;rieure &agrave; la valeur sp&eacute;cifi&eacute;e, et le message sera refus&eacute;. Une valeur vide ou &eacute;gale &agrave; 0 signifie n&eacute;amoins qu\'aucune limite ne s\'applique.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Omvang van de forums',

	// G
	'glossaire:description' => '<NEW>@puce@ Gestion d&rsquo;un glossaire interne li&eacute; &agrave; un ou plusieurs groupes de mots-cl&eacute;s. Inscrivez ici le nom des groupes en  les s&eacute;parant par les deux points &laquo;&nbsp;:&nbsp;&raquo;. En laissant vide la case qui  suit (ou en tapant &quot;Glossaire&quot;), c&rsquo;est le groupe &quot;Glossaire&quot; qui sera utilis&eacute;.[[%glossaire_groupes%]]@puce@ Pour chaque mot, vous avez la possibilit&#233; de choisir le nombre maximal de liens cr&#233;&#233;s dans vos textes. Toute valeur nulle ou n&#233;gative implique que tous les mots reconnus seront trait&#233;s. [[%glossaire_limite% par mot-cl&#233;]]@puce@ Deux solutions vous sont offertes pour g&#233;n&#233;rer la petite fen&ecirc;tre automatique qui apparait lors du survol de la souris. [[%glossaire_js%]]',
	'glossaire:nom' => 'Intern glossarium',
	'glossaire_css' => 'Oplossing CSS',
	'glossaire_js' => 'Oplossing Javascript',
	'guillemets:description' => '<NEW>Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',
	'guillemets:nom' => 'Typografische aanhalingstekens',

	// H
	'help' => '{{Deze bladzijde is alleen toegankelijk voor de verantwoordelijken voor de site.}}<p>Zij geeft toegang tot de verschillende aanvullende functies die door plugin worden gebracht&laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Plaatselijke versie : @version@@distant@<br/>@pack@</p><p>Band van documentatie :<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>R&eacute;initialisations :
_ • [Verborgen werktuigen|Aan de eerste schijn van deze bladzijde terugkomen->@hide@]
_ • [Van hele plugin|Aan de eerste stand van plugin terugkomen->@reset@]@install@
</p>',
	'help0' => '<NEW>{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Lien de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>R&eacute;initialisation :
_ &bull; [De tout le plugin->@reset@]
</p>',

	// I
	'insert_head:description' => '<NEW>Active automatiquement la balise [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] sur tous les squelettes, qu\'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).',
	'insert_head:nom' => '<NEW>Balise #INSERT_HEAD',
	'insertions:description' => '<NEW>ATTENTION : outil en cours de d&eacute;veloppement !! [[%insertions%]]',
	'insertions:nom' => 'Automatische correcties',
	'introduction:description' => '<MODIF>Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Attention}} : Avant d\'activer cette fonctionnalit&eacute;, v&eacute;rifiez bien qu\'aucune fonction {balise_INTRODUCTION()} n\'existe d&eacute;j&agrave; dans votre squelette ou vos plugins, la surcharge produirait alors une erreur de compilation.</p>
@puce@ Vous pouvez pr&eacute;ciser (en pourcentage par rapport &agrave; la valeur utilis&eacute;e par d&eacute;faut) la longueur du texte renvoy&eacute; par balise #INTRODUCTION. Une valeur nulle ou &eacute;gale &agrave; 100 ne modifie pas l\'aspect de l\'introduction et utilise donc les valeurs par d&eacute;faut suivantes : 500 caract&egrave;res pour les articles, 300 pour les br&egrave;ves et 600 pour les forums ou les rubriques.
[[%lgr_introduction%&nbsp;%]]
@puce@ Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION si le texte est trop long sont : <html>&laquo;&amp;nbsp;(&hellip;)&raquo;</html>. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant au lecteur que le texte tronqu&eacute; a bien une suite.
[[%suite_introduction%]]
@puce@ Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l\'article&hellip;&raquo;
[[%lien_introduction%]]
',
	'introduction:nom' => '<NEW>Balise #INTRODUCTION',

	// J
	'js_defaut' => 'Gebrek',
	'js_jamais' => 'Nooit',
	'js_toujours' => 'Nog steeds',

	// L
	'label:admin_travaux' => 'De openbare site sluiten voor :',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Systematische oprichting van het overzicht :',
	'label:balise_sommaire' => 'Het baken #CS_SOMMAIRE activeren :',
	'label:couleurs_fonds' => 'De middelen toelaten :',
	'label:cs_rss' => 'Activeren :',
	'label:decoration_styles' => 'Uw bakens van verpersoonlijkte stijl :',
	'label:dossier_squelettes' => 'Te gebruiken dossier(s) :',
	'label:duree_cache' => 'Duur van het plaatselijke dekblad :',
	'label:duree_cache_mutu' => 'Duur van het dekblad in mutualisatie :',
	'label:forum_lgrmaxi' => 'Waarde (in karakters) :',
	'label:glossaire_groupes' => 'Gebruikte(n) groep(en) :',
	'label:glossaire_js' => 'Gebruikte techniek :',
	'label:glossaire_limite' => 'Maximumaantal gecre&euml;erde band :',
	'label:insertions' => 'Automatische correcties :',
	'label:lgr_introduction' => 'Lengte van de samenvatting :',
	'label:lgr_sommaire' => 'Breedte van het overzicht (9 &agrave; 99) :',
	'label:lien_introduction' => 'Punten van vervolg cliquables :',
	'label:liens_interrogation' => 'URLs beschermen :',
	'label:liens_orphelins' => 'Band cliquables :',
	'label:max_auteurs_page' => 'Auteurs per bladzijde :',
	'label:message_travaux' => '<NEW>Votre message de maintenance :',
	'label:paragrapher' => '<NEW>Toujours paragrapher :',
	'label:puce' => '<NEW>Puce publique &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => '<NEW>Valeur du quota :',
	'label:racc_h1' => 'Toegang en output van een &laquo;<html>{{{intertitel}}}</html>&raquo; :',
	'label:racc_hr' => 'Horizontale lijn &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => 'Toegang en output van een &laquo;<html>{italique}</html>&raquo; :',
	'label:radio_desactive_cache3' => 'het dekblad deactiveren :',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Nieuw venster voor de externe band :',
	'label:radio_type_urls3' => 'Formaat van URLs :',
	'label:set_couleurs' => 'Te gebruiken set :',
	'label:spam_mots' => 'Verboden sequenties :',
	'label:spip_script' => 'Verzoek script :',
	'label:style_h' => 'Uw stijl :',
	'label:style_p' => 'Uw stijl :',
	'label:suite_introduction' => 'Punten van vervolg :',
	'label:titre_travaux' => 'Titel van het bericht :',
	'label:tri_articles' => '<NEW>Votre choix :',
	'label:url_glossaire_externe2' => 'Band naar het externe glossarium :',
	'liens_en_clair:description' => '<NEW>Met &agrave; votre disposition le filtre : \'liens_en_clair\'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d\'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre \'cs=print\' ou \'page=print\' dans l\'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.',
	'liens_en_clair:nom' => 'Band in klaarheid',
	'liens_orphelins:description' => '<NEW>Cet outil a deux fonctions :

@puce@ {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[%liens_interrogation%]]

@puce@ {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'Mooi URLs',
	'log_couteau_suisse:description' => 'Ingeschrevene van talrijke inlichtingen met betrekking tot de werking van plugin \'het Mes Zwitserland\' in de bestanden spip.log die men in de lijst kan vinden : @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => '<NEW>Log d&eacute;taill&eacute; du Couteau Suisse',

	// M
	'mailcrypt:description' => '<NEW>Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d\'activer la messagerie du lecteur. Cet outil antispam tente d\'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.',
	'mailcrypt:nom' => '<NEW>MailCrypt',
	'modifier_vars' => '<NEW>Modifier ces @nb@ param&egrave;tres',

	// N
	'no_IP:description' => '<NEW>D&eacute;sactive le m&eacute;canisme d\'enregistrement automatique des adresses IP des visiteurs de votre site par soucis de confidentialit&eacute; : SPIP ne conservera alors plus aucun num&eacute;ro IP, ni temporairement lors des visites (pour g&eacute;rer les statistiques ou alimenter spip.log), ni dans les forums (responsabilit&eacute;).',
	'no_IP:nom' => '<NEW>Pas de stockage IP',
	'nouveaux' => '<NEW>Nouveaux',

	// O
	'orientation:description' => '<NEW>3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.',
	'orientation:nom' => '<NEW>Orientation des images',
	'outil_actif' => 'Actief werktuig',
	'outil_activer' => 'Activeren',
	'outil_activer_le' => 'Het werktuig activeren',
	'outil_cacher' => 'Niet meer aangeven',
	'outil_desactiver' => '<NEW>D&eacute;sactiver',
	'outil_desactiver_le' => '<NEW>D&eacute;sactiver l\'outil',
	'outil_inactif' => 'Inactief werktuig',
	'outil_intro' => 'Deze bladzijde zet de functies van plugin op een lijst die uw ter beschikking worden gesteld.<br /><br />Door op de naam van de werktuigen te klikken hieronder, selecteert u degenen waarvan zult kunnen verwisselen u de stand met behulp van de centrale knoop: de geactiveerde werktuigen d&eacute;sactiv&eacute;s en <i>vice versa</i>. Aan elke klik, blijkt de beschrijving onder de lijsten. De categorie&euml;n zijn opvouwbaar en de werktuigen kunnen verborgen worden. Het dubbele-Voor een eerste gebruik, wordt hij aanbevolen om de werktuigen &eacute;&eacute;n voor &eacute;&eacute;n te activeren, ingeval zeker de onverenigbaarheden met uw skelet, SPIP of anderen plugins zouden blijkenklik maakt het mogelijk om een werktuig snel te verwisselen.<br /><br />.<br /><br />Nota : de eenvoudige lading van deze bladzijde compileert het geheel van de werktuigen van het Zwitserland Mes opnieuw.',
	'outil_intro_old' => '<NEW>Cette interface est ancienne.<br /><br />Si vous rencontrez des probl&egrave;mes dans l\'utilisation de la <a href=\'./?exec=admin_couteau_suisse\'>nouvelle interface</a>, n\'h&eacute;sitez pas &agrave; nous en faire part sur le forum de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ werktuig',
	'outil_nbs' => '@pipe@ : @nb@ werktuigen',
	'outil_permuter' => 'Het werktuig verwisselen : &laquo; @text@ &raquo; ?',
	'outils_actifs' => '<NEW>Outils actifs :',
	'outils_caches' => '<NEW>Outils cach&eacute;s :',
	'outils_cliquez' => '<NEW>Cliquez sur le nom des outils ci-dessus pour afficher ici leur description.',
	'outils_inactifs' => '<NEW>Outil inactifs :',
	'outils_liste' => '<NEW>Liste des outils du Couteau Suisse',
	'outils_permuter_gras1' => '<NEW>Permuter les outils en gras',
	'outils_permuter_gras2' => '<NEW>Permuter les @nb@ outils en gras ?',
	'outils_resetselection' => '<NEW>R&eacute;initialiser la s&eacute;lection',
	'outils_selectionactifs' => '<NEW>S&eacute;lectionner tous les outils actifs',
	'outils_selectiontous' => '<NEW>TOUS',

	// P
	'pack_alt' => '<NEW>Voir les param&egrave;tres de configuration en cours',
	'pack_descrip' => '<NEW>Votre "Pack de configuration actuelle" rassemble l\'ensemble des param&egrave;tres de configuration en cours concernant le Couteau Suisse : l\'activation des outils et la valeur de leurs &eacute;ventuelles variables.

Ce code PHP peut prendre place dans le fichier /config/mes_options.php et ajoutera un lien de r&eacute;initialisation sur cette page "du pack {Pack Actuel}". Bien s&ucirc;r il vous est possible de changer son nom ci-dessous.

Si vous r&eacute;initialisez le plugin en cliquant sur un pack, le Couteau Suisse se reconfigurera automatiquement en fonction des param&egrave;tres pr&eacute;d&eacute;finis dans le pack.',
	'pack_du' => '<NEW>&bull; du pack @pack@',
	'pack_installe' => '<NEW>Mise en place d\'un pack de configuration',
	'pack_titre' => '<NEW>Configuration Actuelle',
	'par_defaut' => '<NEW>Par d&eacute;faut',
	'paragrapher2:description' => '<NEW>La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => '<NEW>Paragrapher',
	'pipelines' => '<NEW>Pipelines utilis&eacute;s&nbsp;:',
	'pucesli:description' => '<NEW>Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',
	'pucesli:nom' => '<NEW>Belles puces',

	// R
	'raccourcis' => 'Actieve typografische kortere wegen van het Mes Zwitserland&nbsp;:',
	'raccourcis_barre' => 'De typografische kortere wegen van het Mes Zwitserland',
	'reserve_admin' => 'Toegang die voor de beheerders is gereserveerd.',
	'rss_attente' => 'Wachten RSS...',
	'rss_desactiver' => '&laquo; de Revisies van het Mes Zwitserland &raquo; deactiveren ',
	'rss_edition' => 'Flux RSS worden bijgewerkt die :',
	'rss_titre' => '<NEW>&laquo;&nbsp;Le Couteau Suisse&nbsp;&raquo; en d&eacute;veloppement :',
	'rss_var' => '<NEW>Les r&eacute;visions du Couteau Suisse',

	// S
	'sauf_admin' => '<NEW>Tous, sauf les administrateurs',
	'set_options:description' => '<NEW>S&eacute;lectionne d\'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[%radio_set_options4%]]',
	'set_options:nom' => '<NEW>Type d\'interface priv&eacute;e',
	'sf_amont' => '<NEW>En amont',
	'sf_tous' => '<NEW>Tous',
	'simpl_interface:description' => '<NEW>D&eacute;sactive le menu de changement rapide de statut d\'un article au survol de sa puce color&eacute;e. Cela est utile si vous cherchez &agrave; obtenir une interface priv&eacute;e la plus d&eacute;pouill&eacute;e possible afin d\'optimiser les performances client.',
	'simpl_interface:nom' => '<NEW>All&egrave;gement de l\'interface priv&eacute;e',
	'smileys:aide' => '<NEW>Smileys : @liste@',
	'smileys:description' => '<NEW>Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => '<NEW>Smileys',
	'sommaire:description' => '<NEW>Construit un sommaire pour vos articles afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

@puce@ Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ &bull; Syst&eacute;matique pour chaque article (une balise <code>[!sommaire]</code> plac&eacute;e n&rsquo;importe o&ugrave; &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article cr&eacute;era une exception).
_ &bull; Uniquement pour les articles contenant la balise <code>[sommaire]</code>.

[[%auto_sommaire%]]

@puce@ Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d\'article automatiquement. Mais vous avez la possibilt&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe en pages et onglets}.',
	'sommaire:nom' => '<NEW>Un sommaire pour vos articles',
	'sommaire_avec' => '<NEW>Un article avec sommaire&nbsp;: <b>@racc@</b>',
	'sommaire_sans' => '<NEW>Un article sans sommaire&nbsp;: <b>@racc@</b>',
	'spam:description' => '<NEW>Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.

Listez ici les s&eacute;quences interdites@_CS_ASTER@ en les s&eacute;parant par des espaces. [[%spam_mots%]]
@_CS_ASTER@Pour sp&eacute;cifier un mot entier, mettez-le entre paranth&egrave;ses. Pour une expression avec des espaces, placez-la entre guillemets.',
	'spam:nom' => '<NEW>Lutte contre le SPAM',
	'spip_cache:description' => '<NEW>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[%radio_desactive_cache3%]]@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => '<NEW>SPIP et le cache&hellip;',
	'stat_auteurs' => '<NEW>Les auteurs en stat',
	'statuts_spip' => '<NEW>Uniquement les statuts SPIP suivants :',
	'statuts_tous' => '<NEW>Tous les statuts',
	'suivi_forums:description' => '<NEW>Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => '<NEW>Suivi des forums publics',
	'supprimer_cadre' => '<NEW>Supprimer ce cadre',
	'supprimer_numero:description' => '<NEW>Applique la fonction SPIP supprimer_numero() &agrave; l\'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d\'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => '<NEW>Supprime le num&eacute;ro',

	// T
	'titre' => '<NEW>Le Couteau Suisse',
	'titre_tests' => '<NEW>Le Couteau Suisse - Page de tests&hellip;',
	'tous' => '<NEW>Tous',
	'toutes_couleurs' => '<NEW>Les 36 couleurs des styles css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => '<NEW>Blocs multilingues&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => '<NEW>Introduit le raccourci <code><:un_texte:></code> pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : <code>_T(\'un_texte\', $flux)</code>.
_ N\'oubliez pas de v&eacute;rifier que \'un_texte\' est bien d&eacute;fini dans les fichiers de langue.',
	'toutmulti:nom' => '<NEW>Blocs multilingues',
	'travaux_nom_site' => '<NEW>@_CS_NOM_SITE@',
	'travaux_prochainement' => '<NEW>Ce site sera r&eacute;tabli tr&egrave;s prochainement.
_ Merci de votre compr&eacute;hension.',
	'travaux_titre' => '<NEW>@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => '<NEW>En naviguant sur le site en partie priv&eacute;e ([->./?exec=auteurs]), choisissez ici le tri &agrave; utiliser pour afficher vos articles &agrave; l\'int&eacute;rieur de vos rubriques.

Les propositions ci-dessous sont bas&eacute;es sur la fonctionnalit&eacute; SQL \'ORDER BY\' : n\'utilisez le tri personnalis&eacute; que si vous savez ce que vous faites (champs disponibles : {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => '<NEW>Tri des articles',
	'tri_modif' => '<NEW>Tri sur la date de modification (ORDER BY date_modif DESC)',
	'tri_perso' => '<NEW>Tri SQL personnalis&eacute;, ORDER BY suivi de :',
	'tri_publi' => '<NEW>Tri sur la date de publication (ORDER BY date DESC)',
	'tri_titre' => '<NEW>Tri sur le titre (ORDER BY 0+titre,titre)',
	'type_urls:description' => '<NEW>@puce@ SPIP offre un choix sur plusieurs jeux d\'URLs pour fabriquer les liens d\'acc&egrave;s aux pages de votre site :
<div style="font-size:90%; margin:0 2em;">
- {{page}} : la valeur par d&eacute;faut pour SPIP v1.9x : <code>/spip.php?article123</code>.
- {{html}} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
- {{propre}} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
- {{propres2}} : l\'extension \'.html\' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
- {{standard}} : URLs utilis&eacute;es par SPIP v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>
- {{propres-qs}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.</div>

Plus d\'infos : [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@pour utiliser les formats {html}, {propre} ou {propre2}, Recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site SPIP sous le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d\'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</p>

@puce@ {{Uniquement si vous utilisez le format {page} ci-dessus}}, alors il vous est possible de choisir le script d\'appel &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l\'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => '<NEW>Format des URLs',
	'typo_exposants:description' => '<NEW>Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).',
	'typo_exposants:nom' => '<NEW>Exposants typographiques',

	// U
	'url_html' => '<NEW>html@_CS_ASTER@',
	'url_page' => '<NEW>page',
	'url_propres' => '<NEW>propres@_CS_ASTER@',
	'url_propres-qs' => '<NEW>propres-qs',
	'url_propres2' => '<NEW>propres2@_CS_ASTER@',
	'url_standard' => '<NEW>standard',

	// V
	'validez_page' => '<NEW>Pour acc&eacute;der aux modifications :',
	'variable_vide' => '<NEW>(Vide)',
	'vars_modifiees' => '<NEW>Les donn&eacute;es ont bien &eacute;t&eacute; modifi&eacute;es',
	'version_a_jour' => '<NEW>Votre version est &agrave; jour.',
	'version_distante' => '<NEW>Version distante...',
	'version_nouvelle' => '<NEW>Nouvelle version : @version@',
	'verstexte:description' => '<NEW>2 filtres pour vos squelettes, permettant de produire des pages plus l&eacute;g&egrave;res.
_ version_texte : extrait le contenu texte d\'une page html &agrave; l\'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d\'une page html pour rendre du texte plein.',
	'verstexte:nom' => '<NEW>Version texte',
	'votre_choix' => '<NEW>Votre choix :',

	// X
	'xml:description' => '<NEW>Active le validateur xml pour l\'espace public tel qu\'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html]. Un bouton intitul&eacute; &laquo;&nbsp;Analyse XML&nbsp;&raquo; est ajout&eacute; aux autres boutons d\'administration.',
	'xml:nom' => '<NEW>Validateur XML'
);

?>
