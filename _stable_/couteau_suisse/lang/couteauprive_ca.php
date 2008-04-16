<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => '&nbsp;:&nbsp;no',
	'2pts_oui' => '&nbsp;:&nbsp;si',

	// A
	'acces_admin' => 'Acc&eacute;s administradors:',
	'actif' => 'Eina activa',
	'actifs' => 'Eines actives:',
	'activer' => 'Activar',
	'activer_outil' => 'Activar l\'eina',
	'alt_pack' => 'Veure els par&agrave;metres de configuraci&oacute; en curs',
	'auteurs:description' => 'Aquesta eina configura l\'aparen&ccedil;a de [la p&agrave;gina autors->./?exec=auteurs], a la part privada.

@puce@ Defineix aqu&iacute; el nombre m&agrave;xim d\'autors a mostrar en el quadre central de la p&agrave;gina d\'autors. M&eacute;s enll&agrave;, trobem una paginaci&oacute;.[[%max_auteurs_page%]]

@puce@ Quins estats d\'autors es poden llistar en aquesta p&agrave;gina? 
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'P&agrave;gina d\'autors',
	'auteur_forum:description' => 'Incita a tots els autors de missatges p&uacute;blics a omplir (amb una lletra com a m&iacute;nim!) el camp &laquo;@_CS_FORUM_NOM@&raquo; per tal d\'evitar les contribucions totalment an&ograve;nimes.',
	'auteur_forum:nom' => 'No als f&ograve;rums an&ograve;nims',
	'a_jour' => 'La vostra versi&oacute; est&agrave; actualitzada.',

	// B
	'balise_etoilee' => '{{Atenci&oacute;}} : Verifiqueu b&eacute; l\'&uacute;s que fan els vostres esquelets de les etiquetes amb estrelles. Els tractaments d\'aquesta eina no s\'aplicaran a: @bal@.',
	'basique' => 'B&agrave;sic',
	'blocs:aide' => 'Blocs Desplegables: <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (&agrave;lies: <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) i <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => '<MODIF>Us permet crear blocs que, amb el t&iacute;tol clicable, els pot tornar visibles o invisibles.

@puce@ {{En els textos SPIP}}: els redactors tenen disponibles les noves etiquetes &lt;bloc&gt; (o &lt;invisible&gt;) i &lt;visible&gt; a utilitzar en el seus textos d\'aquesta manera: 

<quote><code>
<bloc>
 Un t&iacute;tol que esdevindr&agrave; clicable
 
 El text a amagar/mostrar, despr&eacute;s dos salts de l&iacute;nia...
 </bloc>
</code></quote>

@puce@ {{En els esquelets}}: teniu disponibles les noves etiquetes #BLOC_TITRE, #BLOC_DEBUT i #BLOC_FIN per utilitzar d\'aquesta manera: 
<quote><code> #BLOC_TITRE
 El meu t&iacute;tol
 #BLOC_DEBUT
 El meu bloc desplegable
 #BLOC_FIN</code></quote>
',
	'blocs:nom' => 'Blocs Desplegables',
	'boites_privees:description' => 'Tots els quadres descrits m&eacute;s avall apareixen a la part privada.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{Les revisions del Ganivet Su&iacute;s}} : un quadre a la p&agrave;gina actual de configuraci&oacute;, indicant les &uacute;ltimes modificacions aportades al codi del plugin ([Font->@_CS_RSS_SOURCE@]).
- {{Els articles en format SPIP}}: un quadre plegable suplementari pels vostres articles per tal de con&egrave;ixer el codi font utilitzat pels seus autors.
- {{Els autors en estat}}: un quadre suplementari a [la p&agrave;gina d\'autors->./?exec=auteurs] que indica els 10 &uacute;ltims connectats i les inscripcions no confirmades. Aquestes informacions nom&eacute;s les veuen els administradors. ',
	'boites_privees:nom' => 'Requadres privats',

	// C
	'caches' => 'Eines amagades:',
	'categ:admin' => '1. Administraci&oacute;',
	'categ:divers' => '6. Divers',
	'categ:public' => '4. Visualitzaci&oacute; p&uacute;blica',
	'categ:spip' => '5. Etiquetes, filtres, criteris',
	'categ:typo-corr' => '2. Millora dels textos',
	'categ:typo-racc' => '3. Dreceres tipogr&agrave;fiques',
	'certaines_couleurs' => 'Nom&eacute;s les etiquetes definides m&eacute;s avall@_CS_ASTER@ :',
	'chatons:aide' => 'Emoticones: @liste@',
	'chatons:description' => 'Insereix imatges (o emoticones pels {xats}) en tots els textos on apareix una cadena del tipus <code>:nom</code>.
_ Aquesta eina substitueix aquestes dreceres per les imatges del mateix nom que troba a dins del directori  plugins/couteau_suisse/img/emoticones.',
	'chatons:nom' => 'Emoticones',
	'class_spip:description1' => 'Aqu&iacute; podeu definir algunes dreceres d\'SPIP. Un valor buit equival a utilitzar el valor per defecte.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{Les dreceres d\'SPIP}}.

Aqu&iacute; podeu definir algunes dreceres d\'SPIP. Un valor buit equival a fer servir el valor per defecte.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

SPIP utilitza habitualment l\'etiqueta &lt;h3&gt; pels subt&iacute;tols. Escolliu aqu&iacute; un altre empla&ccedil;ament:[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP ha escollit utilitzar l\'etiqueta &lt;i> per transcriure les it&agrave;liques. Per&ograve; &lt;em> tamb&eacute; hauria pogut anar b&eacute;. Vosaltres decidiu:[[%racc_i1%]][[->%racc_i2%]]
Tingueu present: modificant la substituci&oacute; de les dreceres it&agrave;liques, l\'estil {{2.}} especificat m&eacute;s amunt no s\'aplicar&agrave;.

@puce@ {{Els estils d\'SPIP}}. Fins a la versi&oacute; 1.92 d\'SPIP, les dreceres tipogr&agrave;fiques produ&iuml;en etiquetes sistem&agrave;ticament ornamentades de l\'estil "spip". Per exemple: <code><p class="spip"></code>. Aqu&iacute; podeu definir l\'estil d\'aquestes etiquetes en funci&oacute; dels vostres fulls d\'estil. Una casella buida significa que no s\'aplicar&agrave; cap estil en particular.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Etiquetes &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; i les llistes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Etiquetes &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; i &lt;blockquote&gt; :[[%style_h%]]

Anoteu: modificant aquest segon par&agrave;metre, tamb&eacute; perdeu els estils est&agrave;ndards associats a aquestes etiquetes.</blockquote>',
	'class_spip:nom' => 'SPIP i les seves dreceres…',
	'cliquezlesoutils' => 'Feu un clic al damunt del nom de les eines que teniu m&eacute;s avall per mostrar aqu&iacute; la seva descripci&oacute;.',
	'code_css' => 'CSS',
	'code_fonctions' => 'Funcions',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Opcions',
	'contrib' => 'M&eacute;s informacions: [->http://www.spip-contrib.net/?article@id@]',
	'couleurs:aide' => 'Acolorir el text: <b>[coul]text[/coul]</b>@fond@ amb <b>coul</b> = @liste@',
	'couleurs:description' => 'Permet aplicar f&agrave;cilment colors a tots els textos del lloc (articles, breus, t&iacute;tols, f&ograve;rum, …) utilitzant etiquetes en dreceres.

Dos exemples id&egrave;ntics per canviar el color del text:@_CS_EXEMPLE_COULEURS2@

&Iacute;dem per canviar el fons, si la opci&oacute; de m&eacute;s avall ho permet:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@El format d\'aquestes etiquetes personalitzades ha de llistar colors existents o definir parelles &laquo;balise=couleur&raquo;, separats tots per comes. Exemples: &laquo;gris, vermell&raquo;, &laquo;flux=groc, fort=vermell&raquo;, &laquo;baix=#99CC11, alt=marr&oacute;&raquo; o fins i tot    &laquo;gris=#DDDDCC, vermell=#EE3300&raquo;. Pel primer i l\'&uacute;ltim exemple, les etiquetes autoritzades s&oacute;n: <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> et <code>[fond rouge]</code> si els fons s&oacute;n permesos).',
	'couleurs:nom' => 'Tot en colors',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',
	'cs_rss' => 'Les revisions del Ganivet Su&iacute;s',

	// D
	'decoration:aide' => 'Decoraci&oacute;: <b>&lt;etiqueta&gt;test&lt;/etiqueta&gt;</b>, amb <b>etiqueta</b> = @liste@',
	'decoration:description' => 'De nou estils parametrables a dins dels vostres textos i accessibles gr&agrave;cies a les etiquetes &agrave; chevrons. Exemple: 
&lt;lamevaetiqueta&gt;text&lt;/lamevaetiqueta&gt; o : &lt;lamevaetiqueta/&gt;.<br />Definiu m&eacute;s avall els estils CSS que necessiteu, una etiqueta per l&igrave;nia, segons les seg&uuml;ents sintaxis:
- {tipus.lamevaetiqueta = el meu estil CSS}
- {tipus.lamevaetiqueta.class = la meva  classe CSS}
- {tipus.lamevaetiqueta.lang = la meva llengua (ex: ca)}
- {unalies = lamevaetiqueta}

El par&agrave;metre {tipus} de m&eacute;s amunt pot agafar tres valors:
- {span}: etiqueta a l\'interior d\'un par&agrave;graf (tipus Inline)
- {div}: etiqueta que crea un par&agrave;graf nou (tipus Block)
- {auto}: etiqueta determinada autom&agrave;ticament pel plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Decoraci&oacute;',
	'decoupe:aide' => 'Bloc de pestanyes: <b>&lt;pestanyes>&lt;/pestanyes></b><br/>Separador  de p&agrave;gines o de pestanyes: @sep@',
	'decoupe:aide2' => '&Agrave;lies:&nbsp;@sep@',
	'decoupe:description' => 'Talla la visualitzaci&oacute; p&uacute;blica d\'un article en diverses p&agrave;gines gr&agrave;cies a una paginaci&oacute; autom&agrave;tica. Col&middot;loqueu simplement a dins del vostre article quatre signes mes consecutius (<code>++++</code>) on s\'hagi de realitzar el tall.
_ Si feu servir aquest separador a l\'interior d\'etiquetes &lt;pestanyes&gt; i &lt;/pestanyes&gt; obtindreu un joc de pestanyes.
_ A dins dels esquelets: teniu a la vostra disposici&oacute; les noves etiquetes #ONGLETS_DEBUT, #ONGLETS_TITRE i #ONGLETS_FIN.
_ Aquesta eina es pot completar amb {Un sumari pels vostres articles}.',
	'decoupe:nom' => 'Talla en p&agrave;gines i pestanyes',
	'desactiver' => 'Desactivar',
	'desactiver_flash:description' => 'Suprimeix els objectes flash de les p&agrave;gines del vostre lloc i les substitueix pel contingut alternatiu associat.',
	'desactiver_flash:nom' => 'Desactiva els objectes flash',
	'desactiver_outil' => 'Desactivar l\'eina',
	'desactiver_rss' => 'Desactivar les &laquo;Revisions del Ganivet Su&iacute;s&raquo;',
	'descrip_pack' => 'El vostre "Paquet de configuraci&oacute; actual" reuneix el conjunt dels par&agrave;metres de configuraci&oacute; en curs pel que fa al Ganivet Su&iacute;s: l\'activaci&oacute; d\'eines i el valor de les seves eventuals variables.

Aquest codi PHP es pot posar a dins del fitxer /config/mes_options.php i afegir&agrave; un enlla&ccedil; de tornar a iniciar en aquesta p&agrave;gina "del paquet {Paquet Actual}". Amb tota seguretat, podeu canviar el seu nom m&eacute;s avall.

Si torneu a iniciar el plugin fent un clic al damunt del paquet, el Ganivet Su&iacute;s es configurar&agrave; de nou autom&agrave;ticament en funci&oacute; dels par&agrave;metres predefinits al paquet. ',
	'detail_fichiers' => 'Fitxers:',
	'detail_inline' => 'Codi inserit:',
	'detail_pipelines' => 'Pipelines :',
	'detail_traitements' => 'Tractaments :',
	'distant' => 'Nova versi&oacute;: [@version@->http://files.spip.org/spip-zone/couteau_suisse.zip]',
	'dossier_squelettes:description' => 'Modifica la carpeta de l\'esquelet utilitzat. Per exemple: "esquelets/elmeuesquelet". Podeu inscriure diverses carpetes separant-les pels dos punts <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. Deixar buida la caixa que segueix (o teclejant "dist"), &eacute;s l\'esquelet original "dist" subministrat per SPIP el que es far&agrave; servir.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Carpeta de l\'esquelet',
	'du_pack' => '• del paquet @pack@',

	// E
	'edition' => 'Flux RSS actualitzat el:',
	'effaces' => 'Esborrats',
	'en_travaux:description' => 'Permet mostrar un missatge personalitzat durant una fase de manteniment a tot el lloc p&uacute;blic.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Lloc en manteniment',
	'erreur:description' => 'id absent en la definici&oacute; de l\'eina!',
	'erreur:distant' => 'servidor distant',
	'erreur:js' => 'Sembla que s\'ha produ&iuml;t un error JavaScript en aquesta p&agrave;gina i impedeix el seu bon funcionament. Vulgueu activar JavaScript al vostre navegador o desactivar alguns plugins SPIP del vostre lloc.',
	'erreur:nojs' => 'Aquesta p&agrave;gina t&eacute; el JavaScript desactivat.',
	'erreur:nom' => 'Error!',
	'erreur:probleme' => 'Problema a: @pb@',
	'erreur:traitements' => 'El Ganivet Su&iacute;s - Error de compilaci&oacute; dels tractaments: barreja \'typo\' i \'propre\' prohibit!',
	'erreur:version' => 'Aquesta eina &eacute;s indispensable en aquesta versi&oacute; d\'SPIP.',
	'etendu' => 'Est&egrave;s',

	// F
	'filets_sep:aide' => 'L&iacute;nies de Separaci&oacute;: <b>__i__</b> o <b>i</b> &eacute;s un nombre.<br />Altres l&iacute;nies disponibles: @liste@',
	'filets_sep:description' => 'Insereix l&iacute;nies de separaci&oacute;, que es poden personalitzar per fulls d\'estil, a tots els textos d\'SPIP.
_ La sintaxi &eacute;s: "__code__", o "code" representa o b&eacute; el n&uacute;mero d\'identificaci&oacute; (de 0 a 7) de la l&iacute;nia a inserir en relaci&oacute; directa amb els estils corresponents, o b&eacute; el nom d\'una imatge situada a dins de la carpeta plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'L&iacute;nies de Separaci&oacute;',
	'filtrer_javascript:description' => 'Per gestionar el javascript a dins dels articles, podem fer-ho de tres maneres:
- <i>jamais</i> : el javascript &eacute;s rebutjat a tot arreu
- <i>d&eacute;faut</i> : el javascript s\'assenyala en vermell a l\'espai privat
- <i>toujours</i> : el javascript s\'accepta arreu.

Atenci&oacute;: a dins dels f&ograve;rums, peticions, flux sindicats, etc., la gesti&oacute; dels javascript &eacute;s <b>sempre</b> segura.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Gesti&oacute; del javascript',
	'flock:description' => 'Desactiva el sistema bloqueig de fitxers neutralitzant la funci&oacute; PHP {flock()}. Alguns hostatjadors posen problemes greus fruit d\'un sistema de fitxers inadaptat o a una manca de sincronitzaci&oacute;. No activeu aquesta eina si el vostre lloc funciona normalment. ',
	'flock:nom' => 'Cap bloqueig de fitxers',
	'fonds' => 'Fons:',
	'forcer_langue:description' => 'Imposa el context de llengua pels jocs d\'esquelets multiling&uuml;es que disposen d\'un formulari o d\'un men&uacute; de lleng&uuml;es que sap gestionar la galeta de llengua. ',
	'forcer_langue:nom' => 'Imposar llengua',
	'format_spip' => 'Els articles en format SPIP',
	'forum_lgrmaxi:description' => 'Per defecte, els missatges de f&ograve;rum no tenen l&iacute;mits de mida. Si aquesta eina est&agrave; activada, es mostrar&agrave; un missatge d\'error quan alg&uacute; vulgui enviar un missatge d\'una mida superior al valor especificat, i el missatge es rebutjar&agrave;. Un valor buit o igual a 0 significa, no obstant, que no s\'aplica cap l&iacute;mit.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Mida dels f&ograve;rums',
	'f_jQuery:description' => 'Impedeix la instal&middot;laci&oacute; de {jQuery} a la part p&uacute;blica per tal d\'economitzar una mica de &laquo;temps m&agrave;quina&raquo;. Aquesta llibreria ([->http://jquery.com/]) aporta nombroses comoditats a la programaci&oacute; de Javascript i pot ser utilitzat per certs plugins. SPIP l\'utilitza a la seva part privada.

Atenci&oacute;: certes eines del Ganivet Su&iacute;s necessiten les funcions de {jQuery}. ',
	'f_jQuery:nom' => 'Desactiva jQuery',

	// G
	'glossaire:description' => '@puce@ Gesti&oacute; d\'un glossari intern lligat a un o diversos grups de paraules clau. Inscriviu aqu&iacute; el nom dels grups separant-los per dos punts &laquo;&nbsp;:&nbsp;&raquo;. Deixant buida la casella que segueix (o teclejant "Glossari"), &eacute;s el grup "Glossari" el que es far&agrave; servir.[[%glossaire_groupes%]]@puce@ Per cada paraula, teniu la possibilitat d\'escollir el n&uacute;mero m&agrave;xim d\'enlla&ccedil;os creats als vostres textos. Tot valor nul o negatiu implica que es tractaran totes les paraules reconegudes. [[%glossaire_limite% per paraula clau]]@puce@ S\'ofereixen dues solucions per gestionar la petita finestra autom&agrave;tica que apareix quan hi passes per sobre el ratol&iacute;.  [[%glossaire_js%]]',
	'glossaire:nom' => 'Glossari intern',
	'glossaire_css' => 'Soluci&oacute; CSS',
	'glossaire_js' => 'Soluci&oacute; Javascript',
	'guillemets:description' => 'Substitueix autom&agrave;ticament les cometes (") per les cometes tipogr&agrave;fiques de la llengua de composici&oacute;. La substituci&oacute;, transparent per l\'usuari, no modifica el text original sin&oacute; nom&eacute;s la seva publicaci&oacute; final. ',
	'guillemets:nom' => 'Cometes tipogr&agrave;fiques',

	// H
	'help' => '{{Aquesta p&agrave;gina nom&eacute;s &eacute;s accessible pels responsables del lloc.}}<p>D&oacute;na acc&eacute;s a les diferents funcions suplement&agrave;ries aportades pel plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Versi&oacute; local: @version@@distant@<br/>@pack@</p><p>Enlla&ccedil;os de documentaci&oacute;:<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>Reiniciacions:
_ • [Eines amagades|Tornar a l\'aparen&ccedil;a inicial d\'aquesta p&agrave;gina->@hide@]
_ • [De tot el plugin|Tornar a l\'estat inicial del plugin->@reset@]@install@
</p>',
	'help0' => '{{Aquesta p&agrave;gina &eacute;s nom&eacute;s accessible als responsables del lloc.}}<p>Permet accedir a les diferents funcions suplement&agrave;ries aportades pel plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Enlla&ccedil; de documentaci&oacute;:<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>Reiniciaci&oacute;:
_ • [De tot el plugin->@reset@]
</p>',
	'html' => 'html@_CS_ASTER@',

	// I
	'inactif' => 'Eina inactiva',
	'inactifs' => 'Eina inactius:',
	'insertions:description' => 'ATENCI&Oacute;: eina en curs de desenvolupament!! [[%insertions%]]',
	'insertions:nom' => 'Correccions autom&agrave;tiques',
	'insert_head:description' => 'Activa autom&agrave;ticament l\'etiqueta [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] a tots els esquelets, tinguin o no aquesta etiqueta entre &lt;head&gt; i &lt;/head&gt;. Gr&agrave;cies a aquesta opci&oacute;, els plugins podran inserir javascript (.js) o fulls d\'estil (.css).',
	'insert_head:nom' => 'Etiqueta #INSERT_HEAD',
	'installe_pack' => 'Instal&middot;lar un pack de configuraci&oacute;',
	'introduction:description' => 'Aquesta etiqueta situada als esquelets serveix en general a la p&agrave;gina principal o a les seccions per fer un resum dels articles, breus, etc..</p>
<p>{{Atenci&oacute;}}: Abans d\'activar aquesta funcionalitat, verifiqueu b&eacute; que no existeix ja cap funci&oacute; {balise_INTRODUCTION()} al vostre esquelet o als vostres plugins. La sobrec&agrave;rrega produ&iuml;ra un error de compilaci&oacute;.</p>
@puce@ Podeu precisar (en percentatge en relaci&oacute; al valor utilitzat per defecte) la llargada del text retornat per l\'etiqueta #INTRODUCTION. Un valor  nul o igual a 100 no modifica l\'aspecte de la introducci&oacute; i utilitza, per tant, els valors per defecte seg&uuml;ents: 500 car&agrave;cters pels articles, 300 per les breus i 600 per les seccions.
[[%lgr_introduction%&nbsp;%]]
@puce@ Per defecte, els punts de continuaci&oacute; afegits al resultat de l\'etiqueta #INTRODUCTION si el text &eacute;s massa llarg s&oacute;n: <html>&laquo;&amp;nbsp;(…)&raquo;</html>. Aqu&iacute; podeu precisar la vostra pr&ograve;pia cadena de car&agrave;cters que indiqui al lector que el text truncat t&eacute; una continuaci&oacute;.
[[%suite_introduction%]]
@puce@ Si l\'etiqueta #INTRODUCTION es fa servir per resumir un article, llavors el Ganivet Su&iacute;s pot fabricar un enlla&ccedil; al damunt dels punts de continuaci&oacute; definits m&eacute;s amunt per tal de conduir al lector cap al text original. Per exemple: &laquo;Llegir la continuaci&oacute; de l\'article…&raquo;
[[%lien_inctroduction%]]
',
	'introduction:nom' => 'Etiqueta #INTRODUCTION',

	// J
	'jquery1' => '{{Atenci&oacute;}}: aquesta eina necessita el plugin {jQuery} per funcionar amb aquesta versi&oacute; d\'SPIP.',
	'jquery2' => 'Aquesta eina utilitza la llibreria {jQuery}.',
	'js_defaut' => 'Per defecte',
	'js_jamais' => 'Mai',
	'js_toujours' => 'Sempre',

	// L
	'label:admin_travaux' => 'Tancar el lloc p&uacute;blic per:',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Creaci&oacute; sistem&agrave;tica del sumari:',
	'label:balise_sommaire' => 'Activar l\'etiqueta #CS_SOMMAIRE :',
	'label:couleurs_fonds' => 'Permetre els fons:',
	'label:cs_rss' => 'Activar:',
	'label:decoration_styles' => 'Les vostres etiquetes d\'estil personalitzat:',
	'label:dossier_squelettes' => 'Carpeta(es) a utilitzar:',
	'label:duree_cache' => 'Durada de la mem&ograve;ria cau local:',
	'label:duree_cache_mutu' => 'Durada de la mem&ograve;ria cau en mutualitzaci&oacute;:',
	'label:forum_lgrmaxi' => 'Valor (en car&agrave;cters):',
	'label:glossaire_groupes' => 'Grup(s) utilitzat(s):',
	'label:glossaire_js' => 'T&egrave;cnica utilitzada:',
	'label:glossaire_limite' => 'N&uacute;mero m&agrave;xim d\'enlla&ccedil;os creats:',
	'label:insertions' => 'Correccions autom&agrave;tiques:',
	'label:lgr_introduction' => 'Llargada del resum:',
	'label:lgr_sommaire' => 'Llargada del sumari (9 a 99):',
	'label:liens_interrogation' => 'Protegir els URLs: ',
	'label:liens_orphelins' => 'Enlla&ccedil;os clicables:',
	'label:lien_inctroduction' => 'Punts de continuaci&oacute; clicables:',
	'label:max_auteurs_page' => 'Autors per p&agrave;gina:',
	'label:message_travaux' => 'El vostre missatge de manteniment:',
	'label:paragrapher' => 'Sempre par&agrave;grafs:',
	'label:puce' => 'Car&agrave;cter p&uacute;blic &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Valor de la quota :',
	'label:racc_h1' => 'Entrada i sortida d\'un &laquo;<html>{{{subt&iacute;tol}}}</html>&raquo; :',
	'label:racc_hr' => 'L&iacute;nia horitzontal &laquo;<html>----</html>&raquo;:',
	'label:racc_i1' => 'Entrada i sortida d\'una &laquo;<html>{cursiva}</html>&raquo; :',
	'label:radio_desactive_cache3' => 'Desactivar la mem&ograve;ria cau:',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Nova finestra pels enlla&ccedil;os externs:',
	'label:radio_type_urls3' => 'Format dels URLs:',
	'label:set_couleurs' => 'Set per utilitzar:',
	'label:spam_mots' => 'Seq&uuml;&egrave;ncies prohibides:',
	'label:spip_script' => 'Script de crida:',
	'label:style_h' => 'El vostre estil:',
	'label:style_p' => 'El vostre estil:',
	'label:suite_introduction' => 'Punts de continuaci&oacute;:',
	'label:titre_travaux' => 'T&iacute;tol del missatge:',
	'label:url_glossaire_externe2' => 'Enlla&ccedil; al glossari extern:',
	'liens_en_clair:description' => 'Posa a la vostra disposici&oacute; el filtre: \'liens_en_clair\'. El vostre text cont&eacute; probablement enlla&ccedil;os que no s&oacute;n visibles durant la impressi&oacute;. Aquest filtre afegeix entre claud&agrave;tors el dest&iacute; de cada enlla&ccedil; clicable (enlla&ccedil;os externs o correus electr&ograve;nics). Atenci&oacute;: en mode impressi&oacute; (par&agrave;metre \'cs=print\' o \'page=print\' al url de la p&agrave;gina), aquesta funcionalitat s\'aplica autom&agrave;ticament.',
	'liens_en_clair:nom' => 'Enlla&ccedil;os visibles',
	'liens_orphelins:description' => 'Aquesta eina t&eacute; dues funcions:

@puce@ {{Enlla&ccedil;os correctes}}.

SPIP t&eacute; per costum inserir un espai abans dels interrogants o dels signes d\'exclamaci&oacute;, la tipografia francesa obliga. Aqu&iacute; teniu una eina que protegeix l\'interrogant als URLs dels vostres textos.[[%liens_interrogation%]]

@puce@ {{Enlla&ccedil;os orfes}}.

Substitueix sistem&agrave;ticament tots els URLs deixats en text pels usuaris (sobretot als f&ograve;rums), i que no s&oacute;n clicables, `per enlla&ccedil;os en format SPIP. Per exemple: {<html>www.spip.net</html>} queda substitu&iuml;t per [->www.spip.net].

Podeu escollir el tipus de substituci&oacute;:
_ • {B&agrave;sic}: s&oacute;n substitu&iuml;ts els enlla&ccedil;os del tipus {<html>http://spip.net</html>} (tot protocol) o {<html>www.spip.net</html>}.
_ • {Extens}: s&oacute;n substitu&iuml;ts a m&eacute;s els enlla&ccedil;os del tipus {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} o {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'URLs bonics',
	'liste_outils' => 'Llista d\'eines del Ganivet Su&iacute;s',
	'log_couteau_suisse:description' => 'Inscrit de nombroses informacions pel que fa al funcionament del plugin \'El Ganivet Su&iacute;s\' en els fitxers spip.log que es poden trobar en el directori: @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => 'Registre detallat del Ganivet Su&iacute;s',

	// M
	'mailcrypt:description' => 'Amaga tots els enlla&ccedil;os de correus presents als vostres textos substituint-los per un enlla&ccedil; Javascript que permet malgrat tot activar la missatgeria del lector. Aquesta eina antispam impedeix que els robots recullin les adreces electr&ograve;niques deixades visibles als f&ograve;rums o a les etiquetes dels vostres esquelets.',
	'mailcrypt:nom' => 'MailCrypt',
	'maj_tous' => 'TOTS',
	'modifier_vars' => 'Modificar aquests @nb@ par&agrave;metres',

	// N
	'nb_outil' => '@pipe@: @nb@ eina',
	'nb_outils' => '@pipe@: @nb@ eines',
	'neplusafficher' => 'No mostrar-ho m&eacute;s',
	'nouveaux' => 'Nou',
	'no_IP:description' => 'Desactiva el mecanisme d\'enregistrament autom&agrave;tic de les adreces IP dels visitants del vostre lloc per motius de confidencialitat: SPIP no conservar&agrave; m&eacute;s cap n&uacute;mero IP, ni temporalment, de les persones que us puguin visitar (per gestionar les estad&iacute;stiques o alimentar spip.log), ni en els f&ograve;rums (responsabilitat).',
	'no_IP:nom' => 'No emmagatzemar la IP',

	// O
	'orientation:description' => '3 nous criteris pels vostres esquelets: <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Ideal per la classificaci&oacute; de les fotografies en funci&oacute; de la seva forma. ',
	'orientation:nom' => 'Orientaci&oacute; de les imatges',

	// P
	'pack' => 'Configuraci&oacute; Actual',
	'page' => 'p&agrave;gina',
	'paragrapher2:description' => 'La funci&oacute; SPIP <code>paragrapher()</code> insereix etiquetes &lt;p&gt; i &lt;/p&gt; a tots els textos que estan desprove&iuml;ts de par&agrave;grafs. Per tal de gestionar m&eacute;s finament els vostres estils i les vostres compaginacions, teniu la possibilitat d\'uniformitzar l\'aspecte dels textos del vostre lloc.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Par&agrave;graf',
	'par_defaut' => 'Per defecte',
	'permuter' => 'Canviar les eines en negreta ',
	'permuter_outil' => 'Canviar l\'eina: \\u00ab @text@ \\u00bb ?',
	'permuter_outils' => 'Canviar les @nb@ eines en negreta?',
	'pipelines' => 'Pipelines utilitzades:',
	'presente_outils' => 'Aquesta interf&iacute;cie &eacute;s antiga.<br /><br />Si trobeu problemes en l\'&uacute;s de la <a href=\'./?exec=admin_couteau_suisse\'>nouvelle interface</a>, no dubte en explicar-ho al f&ograve;rum de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'presente_outils2' => 'Aquesta p&agrave;gina llista les funcionalitats del plugin posades a la vostra disposici&oacute;.<br /><br />Fent un clic al damunt del nom de les eines que hi ha m&eacute;s avall, seleccioneu aquells que podreu canviar l\'estat amb l\'ajuda del bot&oacute; central: les eines activades seran desactivades i <i>viceversa</i>. A cada clic, la descripci&oacute; apareix a sota de les llistes. Les categories s&oacute;n  plegables i les eines poden estar amagades. El doble clic permet canviar r&agrave;pidament una eina.<br /><br />Per un primer &uacute;s, es recomana activar les eines una a una, per evitar que apareguin algunes incompatibilitats amb el vostre esquelet, amb SPIP o amb altres plugins.<br /><br />Nota: la simple c&agrave;rrega d\'aquesta p&agrave;gina torna a compilar el conjunt d\'eines del Ganivet Su&iacute;s.',
	'prochainement' => 'Aquest lloc es restablir&agrave; ben aviat.
_ Gr&agrave;cies per la vostra comprensi&oacute;.',
	'propres' => 'propres@_CS_ASTER@',
	'propres-qs' => 'propres-qs',
	'propres2' => 'propres2@_CS_ASTER@',
	'pucesli:description' => 'Substitueix els car&agrave;cters &laquo;-&raquo; (guionet simple) dels articles per llistes numerades &laquo;-*&raquo; (tradu&iuml;des en HTML per: &lt;ul>&lt;li>…&lt;/li>&lt;/ul>) dels que l\'estil es pot personalitzar amb CSS.',
	'pucesli:nom' => 'Car&agrave;cters bonics',

	// R
	'raccourcis' => 'Dreceres tipogr&agrave;fiques actives del Ganivet Su&iacute;s:',
	'raccourcis_barre' => 'Les dreceres tipogr&agrave;fiques del Ganivet Su&iacute;s',
	'reserve_admin' => 'Acc&eacute;s reservat als administradors.',
	'resetselection' => 'Reiniciar la selecci&oacute;',
	'rss_titre' => '&laquo;El Ganivet Su&iacute;s&raquo; en desenvolupament:',

	// S
	'sauf_admin' => 'Tots, excepte els administradors',
	'selectiontous' => 'Seleccionar totes les eines actives',
	'set_options:description' => 'Selecciona d\'entrada el tipus d\'interf&iacute;cie privada (simple o avan&ccedil;ada) per tots els redactors ja existents o per aquells que poden venir i suprimeix el bot&oacute; corresponent de la banda on hi ha les icones petites.[[%radio_set_options4%]]',
	'set_options:nom' => 'Tipus d\'interf&iacute;cie privada',
	'sf_amont' => 'M&eacute;s amunt',
	'sf_tous' => 'Tots',
	'simpl_interface:description' => 'Desactiva el men&uacute; de canvi r&agrave;pid de l\'estat d\'un article passant pel damunt del seu car&agrave;cter acolorit. Aix&ograve; &eacute;s &uacute;til si busqueu obtenir una interf&iacute;cie privada el m&eacute;s simple possible per tal d\'optimitzar les prestacions del client. ',
	'simpl_interface:nom' => 'Alleugeriment de la interf&iacute;cie privada',
	'smileys:aide' => 'Emoticones: @liste@',
	'smileys:description' => 'Insereix emoticones en tots els textos on apareix una drecera del tipus <acronym>:-)</acronym>. Ideal pels f&ograve;rums.
_ Hi ha una etiqueta per mostrar una taula d\'emoticones als vostres esquelets: #SMILEYS.
_ Dibuixos: [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Emoticones',
	'sommaire:description' => 'Construeix un sumari pels vostres articles per tal d\'accedir r&agrave;pidament als titulars (balises HTML &lt;h3>Un subt&iacute;tol&lt;/h3> o dreceres SPIP: subt&iacute;tols del tipus:<code>{{{Un gran t&iacute;tol}}}</code>).

@puce@ Podeu definir aqu&iacute; el nombre m&agrave;xim de car&agrave;cters que es retindran dels subt&iacute;tols a l\'hora de construir el sumari:[[%lgr_sommaire% car&agrave;cters]]

@puce@ Tamb&eacute; podeu fixar el comportament del plugin en refer&egrave;ncia a la creaci&oacute; del sumari: 
_ • Sistem&agrave;ticament per cada article (una etiqueta <code>[!sommaire]</code> situada a qualsevol lloc o a l\'interior del text de l\'article crear&agrave; una excepci&oacute;).
_ • Nom&eacute;s pels articles que continguin l\'etiqueta <code>[sommaire]</code>.

[[%auto_sommaire%]]

@puce@ Per defecte, el Ganivet Su&iacute;s insereix el sumari a la cap&ccedil;alera de l\'article de forma autom&agrave;tica. Per&ograve; vosaltres teniu la possibilitat de situar-lo a qualsevol indret a dins del vostre esquelet gr&agrave;cies a una etiqueta #CS_SOMMAIRE que podeu activar aqu&iacute;:
[[%balise_sommaire%]]

Aquest sumari pot ser acoblat a: {Tallat en p&agrave;gines i pestanyes}.',
	'sommaire:nom' => 'Un sumari pels vostres articles',
	'sommaire_avec' => 'Un article amb sumari: <b>@racc@</b>',
	'sommaire_sans' => 'Un article sense sumari: <b>@racc@</b>',
	'spam:description' => 'Intenta lluitar contra els enviaments de missatges autom&agrave;tics i malevolents a la part p&uacute;blica. Algunes paraules i les etiquetes &lt;a>&lt;/a> estan prohibides.

Llisteu aqu&iacute; les seq&uuml;&egrave;ncies prohibides @_CS_ASTER@ separant-les per espais. [[%spam_mots%]]
@_CS_ASTER@Per especificar una paraula sencera, poseu-la entre par&egrave;ntesi. En cas d\'una expressi&oacute; amb espais, poseu-la entre cometes. ',
	'spam:nom' => 'Lluita contra l\'SPAM',
	'spip_cache:description' => '@puce@ Per defecte, SPIP calcula totes les p&agrave;gines p&uacute;bliques i les situa a la mem&ograve;ria cau per tal d\'accelerar la consulta. Desactivar temporalment la mem&ograve;ria cau pot ajudar al desenvolupament del lloc.[[%radio_desactive_cache3%]]@puce@ La mem&ograve;ria cau ocupa un cert espai del disc i SPIP pot limitar-ne la import&agrave;ncia. Un valor buit o igual a 0 significa que no s\'aplica cap quota.[[%quota_cache% Mo]]@puce@ Si l\'etiqueta #CACHE no es troba als vostres esquelets locals, SPIP considera per defecte que la mem&ograve;ria cau d\'una p&agrave;gina t&eacute; una durada de vida de 24 hores abans de tornar-la a calcular. Per tal de gestionar millor l&iexcl;enc&agrave;rrec del vostre servidor, podeu modificar aqu&iacute; aquest valor.[[%duree_cache% heures]]@puce@ Si teniu diversos llocs mutualitzats, podeu especificar aqu&iacute; el valor per defecte que tindran en compte tots els llocs locals (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => 'SPIP i la mem&ograve;ria cau…',
	'SPIP_liens:description' => '@puce@ Tout els enlla&ccedil;os del lloc s\'obren per defecte a la mateixa finestra de navegaci&oacute; que esteu. Per&ograve; pot ser &uacute;til obrir els enlla&ccedil;os externs al lloc en una nova finestra --es pot aconseguir afegint {target="_blank"} a totes les etiquetes &lt;a&gt; dotades per SPIP de les classes {spip_out}, {spip_url} o {spip_glossaire}. A vegades pot ser &uacute;til afegir una d\'aquestes classes als enlla&ccedil;os de l\'esquelet del lloc (fitxers html) per tal d\'ampliar al m&agrave;xim aquesta funcionalitat.[[%radio_target_blank3%]]

@puce@ SPIP permet lligar paraules amb la seva definici&oacute; gr&agrave;cies a la drecera tipogr&agrave;fica <code>[?mot]</code>. Per defecte (o si deixeu buida la casella de m&eacute;s avall), el glossari extern us envia cap a l\'enciclop&egrave;dia lliure wikipedia.org. Us toca a vosaltres decidir quina adre&ccedil;a voleu utilitzar. <br />Enlla&ccedil; de prova: [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP i els enlla&ccedil;os externs',
	'standard' => 'est&agrave;ndard',
	'statuts_spip' => 'Nom&eacute;s els statuts SPIP seg&uuml;ents:',
	'statuts_tous' => 'Tots els statuts',
	'stat_auteurs' => 'Els autors en stat',
	'suivi_forums:description' => 'Un autor d\'un article est&agrave; sempre informat quan es publica un missatge al f&ograve;rum que aquest t&eacute; associat. Per&ograve;, a m&eacute;s, tamb&eacute; es possible advertir a: tots els participants al f&ograve;rum o nom&eacute;s als autors dels missatges en endavant.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Seguiment dels f&ograve;rums p&uacute;blics',
	'supprimer_cadre' => 'Suprimir aquest quadre',
	'supprimer_numero:description' => 'Aplica la funci&oacute; SPIP supprimer_numero() al conjunt dels {{t&iacute;tols}} i dels {{noms}} del lloc p&uacute;blic, sense que el filtre supprimer_numero estigui present als esquelets.<br />Heus aqu&iacute; la sintaxis que cal utilitzar en el marc d\'un lloc multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Suprimeix el n&uacute;mero',

	// T
	'titre' => 'El Ganivet Su&iacute;s',
	'titre_tests' => 'El Ganivet Su&iacute;s - P&agrave;gina de proves…',
	'tous' => 'Tots',
	'toutes_couleurs' => 'Els 36 colors dels estils CSS :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Blocs multiling&uuml;es&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => 'Introdueix la drecera <code><:un_texte:></code> per introduir lliurement blocs multiling&uuml;es en un article.
_ La funci&oacute; SPIP utilitzada &eacute;s: <code>_T(\'un_texte\', 
flux)</code>.
_ No oblideu verificar que \'un_texte\' estigui ben definit als fitxers de llengua. ',
	'toutmulti:nom' => 'Blocs multiling&uuml;es',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'type_urls:description' => '@puce@ SPIP ofereix la possibilitat d\'escollir entre diversos jocs d\'URLs per fabricar els enlla&ccedil;os d\'acc&eacute;s a les p&agrave;gines del vostre lloc:
<div style="font-size:90%; margin:0 2em;">
- {{page}}: el valor per defecte per SPIP v1.9x: <code>/spip.php?article123</code>.
- {{html}}: els enlla&ccedil;os tenen la forma de p&agrave;gines html cl&agrave;ssiques: <code>/article123.html</code>.
- {{propre}}: els enlla&ccedil;os es calculen per mitj&agrave; del t&iacute;tol: <code>/El-meu-titol-d-article</code>.
- {{propres2}}: l\'extensi&oacute; \'.html\' s\'afegeix a les adreces generades: <code>/El-meu-titol-d-article.html</code>.
- {{standard}} : URLs utilitzades per SPIP v1.8 i precedents: <code>article.php3?id_article=123</code>
- {{propres-qs}}: aquest sistema funciona en "Query-String", &eacute;s a dir sense usar .htaccess; els enlla&ccedil;os s&oacute;n del tipus: <code>/?El-meu-titol-d-article</code>.</div>

M&eacute;s informacions: [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@per utilitzar els formats {html}, {propre} o {propre2}, Torneu a copiar el fitxer "htaccess.txt" del directori de base del lloc SPIP amb el nom ".htaccess" (atenci&oacute; a no esborrar altres regulacions que pogu&eacute;ssiu tenir posades a dins d\'aquest fitxer) ; si el vostre lloc est&agrave; en "sub-directori", haureu d\'editar tamb&eacute; la l&iacute;nia "RewriteBase" d\'aquest fitxer. Els URLs definits es redirigiran llavors cap als fitxer d\'SPIP.</p>

@puce@ {{Nom&eacute;s si utilitzeu el format {page} m&eacute;s amunt}}, llavors us &eacute;s possible escollir l\'script de crida a SPIP. Per defecte, SPIP escull {spip.php}, per&ograve; {index.php} (format: <code>/index.php?article123</code>) o un valor buit (format: <code>/?article123</code>) tamb&eacute; funcionen. Per qualsevol altre valor, us fa falta absolutament crear el fitxer corresponent a l\'arrel d\'SPIP, a imatge d\'aquell que ja existeix: {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => 'Format dels URLs',
	'typo_exposants:description' => 'Textos francesos: millora el retorn tipogr&agrave;fic de les abreviacions corrents, exposant els elements necessaris (aix&iacute;, {<acronym>Mme</acronym>} esdev&eacute; {M<sup>me</sup>}) i corregint-ne els errors normals ({<acronym>2&egrave;me</acronym>} o  {<acronym>2me</acronym>}, per exemple, esdevenen {2<sup>e</sup>}, &uacute;nica abreviaci&oacute; correcta).
_ Les abreviacions obtingudes s&oacute;n conformes a les de la Impremta nacional tal com s\'indiquen al {L&egrave;xic de les regles tipogr&agrave;fiques en &uacute;s a la Impremta nacional} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).',
	'typo_exposants:nom' => 'Super&iacute;ndexs',

	// V
	'validez_page' => 'Per accedir a les modificacions:',
	'variable_vide' => '(Buit)',
	'vars_modifiees' => 'Les dades s\'han modificat correctament',
	'verstexte:description' => '2 filtres pels vostres esquelets, permetent produir p&agrave;gines m&eacute;s lleugeres.
_ version_texte: extreu el contingut text d\'una p&agrave;gina html excepte algunes etiquetes elementals.
_ version_plein_texte : extreu el contingut text d\'una p&agrave;gina html per retornar text complet. ',
	'verstexte:nom' => 'Versi&oacute; text',
	'votre_choix' => 'La vostre elecci&oacute;:',

	// X
	'xml:description' => 'Activa el validador xml per l\'espai p&uacute;blic tal i com est&agrave; descrit a la [documentaci&oacute;->http://www.spip.net/ca_article3577.html]. Un bot&oacute; anomenat &laquo;&nbsp;An&agrave;lisi XML&nbsp;&raquo; s\'afegeix als altres botons d\'administraci&oacute;.',
	'xml:nom' => 'Validador XML'
);

?>
