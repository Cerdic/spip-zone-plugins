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
	'admin' => '1. Administraci&oacute;',
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
	'blocs:description' => 'Us permet crear blocs que, amb el t&iacute;tol clicable, els pot tornar visibles o invisibles.

@puce@ {{En els textos SPIP}}: els redactors tenen a la seva disposici&oacute; les noves etiquetes &lt;bloc&gt; (o &lt;invisible&gt;) i &lt;visible&gt; per utilitzar en el seus textos com aqu&iacute;: 

<quote><code>
<bloc>
 Un t&iacute;tol que esdevindr&agrave; clicable
 
 El text a amagar/mostrar, despr&eacute;s dos salts de l&iacute;nia...
 </bloc>
</code></quote>

@puce@ {{En els esquelets}} : teniu a la vostra disposici&oacute; les noves etiquetes #BLOC_TITRE, #BLOC_DEBUT i #BLOC_FIN per utilitzar d\'aquesta manera: 
<quote><code> #BLOC_TITRE
 El meu t&iacute;tol
 #BLOC_DEBUT
 El meu bloc desplegable
 #BLOC_FIN</code></quote>
',
	'blocs:nom' => 'Blocs Desplegables',
	'boites_privees:description' => 'Tots els quadres descrits m&eacute;s avall apareixen a la part privada.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{Les revisions del Couteau Suisse}} : un quadre a la p&agrave;gina actual de configuraci&oacute;, indicant les &uacute;ltimes modificacions aportades al codi del plugin ([Font->@_CS_RSS_SOURCE@]).
- {{Els articles en format SPIP}}: un quadre plegable suplementari pels vostres articles per tal de con&egrave;ixer el codi font utilitzat pels seus autors.
- {{Els autors en estat}}: un quadre suplementari a [la p&agrave;gina d\'autors->./?exec=auteurs] que indica els 10 &uacute;ltims connectats i les inscripcions no confirmades. Aquestes informacions nom&eacute;s les veuen els administradors. ',
	'boites_privees:nom' => 'Requadres privats',

	// C
	'caches' => 'Eines amagades:',
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

@puce@ {{Els estils d\'SPIP}}. Fins a la versi&oacute; 1.92 d\'SPIP, les dreceres tipogr&agrave;fiques produ&iuml;en etiquetes sistem&agrave;ticament de l\'estil "spip". Per exemple: <code><p class="spip"></code>. Aqu&iacute; podeu definir l\'estil d\'aquestes etiquetes en funci&oacute; dels vostres fulls d\'estil. Una casella buida significa que no s\'aplicar&agrave; cap estil en particular.<blockquote style=\'margin:0 2em;\'>
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
	'cs_rss' => 'Les revisions del Couteau Suisse',

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
	'desactiver_rss' => 'Desactivar les &laquo;Revisions del Couteau Suisse&raquo;',
	'descrip_pack' => 'El vostre "Paquet de configuraci&oacute; actual" reuneix el conjunt dels par&agrave;metres de configuraci&oacute; en curs pel que fa al Couteau Suisse: l\'activaci&oacute; d\'eines i el valor de les seves eventuals variables.

Aquest codi PHP es pot posar a dins del fitxer /config/mes_options.php i afegir&agrave; un enlla&ccedil; de tornar a iniciar en aquesta p&agrave;gina "del paquet {Paquet Actual}". Amb tota seguretat, podeu canviar el seu nom m&eacute;s avall.

Si torneu a iniciar el plugin fent un clic al damunt del paquet, el Couteau Suisse es configurar&agrave; de nou autom&agrave;ticament en funci&oacute; dels par&agrave;metres predefinits al paquet. ',
	'detail_fichiers' => 'Fitxers:',
	'detail_inline' => 'Codi inserit:',
	'detail_pipelines' => 'Pipelines :',
	'detail_traitements' => 'Tractaments :',
	'distant' => 'Nova versi&oacute;: [@version@->http://files.spip.org/spip-zone/couteau_suisse.zip]',
	'divers' => '6. Divers',
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
	'erreur:traitements' => 'El Couteau Suisse - Error de compilaci&oacute; dels tractaments: barreja \'typo\' i \'propre\' prohibit!',
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

Atenci&oacute;: certes eines del Couteau Suisse necessiten les funcions de {jQuery}. ',
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
@puce@ Si l\'etiqueta #INTRODUCTION es fa servir per resumir un article, llavors el Couteau Suisse pot fabricar un enlla&ccedil; al damunt dels punts de continuaci&oacute; definits m&eacute;s amunt per tal de conduir al lector cap al text original. Per exemple: &laquo;Llegir la continuaci&oacute; de l\'article…&raquo;
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
	'label:glossaire_js' => '<NEW>Technique utilis&eacute;e :',
	'label:glossaire_limite' => '<NEW>Nombre maximal de liens cr&#233;&#233;s :',
	'label:insertions' => '<NEW>Corrections automatiques :',
	'label:lgr_introduction' => '<NEW>Longueur du r&eacute;sum&eacute; :',
	'label:lgr_sommaire' => '<NEW>Largeur du sommaire (9 &agrave; 99) :',
	'label:liens_interrogation' => '<NEW>Prot&eacute;ger les URLs :',
	'label:liens_orphelins' => '<NEW>Liens cliquables :',
	'label:lien_inctroduction' => '<NEW>Points de suite cliquables :',
	'label:max_auteurs_page' => '<NEW>Auteurs par page :',
	'label:message_travaux' => '<NEW>Votre message de maintenance :',
	'label:paragrapher' => '<NEW>Toujours paragrapher :',
	'label:puce' => '<NEW>Puce publique &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => '<NEW>Valeur du quota :',
	'label:racc_h1' => '<NEW>Entr&eacute;e et sortie d\'un &laquo;<html>{{{intertitre}}}</html>&raquo; :',
	'label:racc_hr' => '<NEW>Ligne horizontale &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => '<NEW>Entr&eacute;e et sortie d\'un &laquo;<html>{italique}</html>&raquo; :',
	'label:radio_desactive_cache3' => '<NEW>D&eacute;sactiver le cache :',
	'label:radio_filtrer_javascript3' => '<NEW>@_CS_CHOIX@',
	'label:radio_set_options4' => '<NEW>@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '<NEW>@_CS_CHOIX@',
	'label:radio_target_blank3' => '<NEW>Nouvelle fen&ecirc;tre pour les liens externes :',
	'label:radio_type_urls3' => '<NEW>Format des URLs :',
	'label:set_couleurs' => '<NEW>Set &agrave; utiliser :',
	'label:spam_mots' => '<NEW>S&eacute;quences interdites :',
	'label:spip_script' => '<NEW>Script d\'appel :',
	'label:style_h' => '<NEW>Votre style :',
	'label:style_p' => '<NEW>Votre style :',
	'label:suite_introduction' => '<NEW>Points de suite :',
	'label:titre_travaux' => '<NEW>Titre du message :',
	'label:url_glossaire_externe2' => '<NEW>Lien vers le glossaire externe :',
	'liens_en_clair:description' => '<NEW>Met &agrave; votre disposition le filtre : \'liens_en_clair\'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d\'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre \'cs=print\' ou \'page=print\' dans l\'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.',
	'liens_en_clair:nom' => '<NEW>Liens en clair',
	'liens_orphelins:description' => '<NEW>Cet outil a deux fonctions :

@puce@ {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[%liens_interrogation%]]

@puce@ {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => '<NEW>Belles URLs',
	'liste_outils' => '<NEW>Liste des outils du Couteau Suisse',
	'log_couteau_suisse:description' => '<NEW>Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin \'Le Couteau Suisse\' dans les fichiers spip.log que l\'on peut trouver dans le r&eacute;pertoire : @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => '<NEW>Log d&eacute;taill&eacute; du Couteau Suisse',

	// M
	'mailcrypt:description' => '<NEW>Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d\'activer la messagerie du lecteur. Cet outil antispam tente d\'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.',
	'mailcrypt:nom' => '<NEW>MailCrypt',
	'maj_tous' => '<NEW>TOUS',
	'modifier_vars' => '<NEW>Modifier ces @nb@ param&egrave;tres',

	// N
	'nb_outil' => '<NEW>@pipe@ : @nb@ outil',
	'nb_outils' => '<NEW>@pipe@ : @nb@ outils',
	'neplusafficher' => '<NEW>Ne plus afficher',
	'nouveaux' => '<NEW>Nouveaux',
	'no_IP:description' => '<NEW>D&eacute;sactive le m&eacute;canisme d\'enregistrement automatique des adresses IP des visiteurs de votre site par soucis de confidentialit&eacute; : SPIP ne conservera alors plus aucun num&eacute;ro IP, ni temporairement lors des visites (pour g&eacute;rer les statistiques ou alimenter spip.log), ni dans les forums (responsabilit&eacute;).',
	'no_IP:nom' => '<NEW>Pas de stockage IP',

	// O
	'orientation:description' => '<NEW>3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.',
	'orientation:nom' => '<NEW>Orientation des images',

	// P
	'pack' => '<NEW>Configuration Actuelle',
	'page' => '<NEW>page',
	'paragrapher2:description' => '<NEW>La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => '<NEW>Paragrapher',
	'par_defaut' => '<NEW>Par d&eacute;faut',
	'permuter' => '<NEW>Permuter les outils en gras',
	'permuter_outil' => '<NEW>Permuter l\'outil : \\u00ab @text@ \\u00bb ?',
	'permuter_outils' => '<NEW>Permuter les @nb@ outils en gras ?',
	'pipelines' => '<NEW>Pipelines utilis&eacute;s&nbsp;:',
	'presente_outils' => '<NEW>Cette interface est ancienne.<br /><br />Si rencontrez des probl&egrave;mes dans l\'utilisation de la <a href=\'./?exec=admin_couteau_suisse\'>nouvelle interface</a>, n\'h&eacute;sitez pas &agrave; nous en faire part sur le forum de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'presente_outils2' => '<NEW>Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition.<br /><br />En cliquant sur le nom des outils ci-dessous, vous s&eacute;lectionnez ceux dont vous pourrez permuter l\'&eacute;tat &agrave; l\'aide du bouton central : les outils activ&eacute;s seront d&eacute;sactiv&eacute;s et <i>vice versa</i>. &Agrave; chaque clic, la description apparait au-dessous des listes. Les cat&eacute;gories sont repliables et les outils peuvent &ecirc;tre cach&eacute;s. Le double-clic permet de permuter rapidement un outil.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d\'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d\'autres plugins.<br /><br />Note : le simple chargement de cette page recompile l\'ensemble des outils du Couteau Suisse.',
	'prochainement' => '<NEW>Ce site sera r&eacute;tabli tr&egrave;s prochainement.
_ Merci de votre compr&eacute;hension.',
	'propres' => '<NEW>propres@_CS_ASTER@',
	'propres-qs' => '<NEW>propres-qs',
	'propres2' => '<NEW>propres2@_CS_ASTER@',
	'public' => '<NEW>4. Affichage public',
	'pucesli:description' => '<NEW>Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',
	'pucesli:nom' => '<NEW>Belles puces',

	// R
	'raccourcis' => '<NEW>Raccourcis typographiques actifs du Couteau Suisse&nbsp;:',
	'raccourcis_barre' => '<NEW>Les raccourcis typographiques du Couteau Suisse',
	'reserve_admin' => '<NEW>Acc&egrave;s r&eacute;serv&eacute; aux administrateurs.',
	'resetselection' => '<NEW>R&eacute;initialiser la s&eacute;lection',
	'rss_titre' => '<NEW>&laquo;&nbsp;Le Couteau Suisse&nbsp;&raquo; en d&eacute;veloppement :',

	// S
	'sauf_admin' => '<NEW>Tous, sauf les administrateurs',
	'selectiontous' => '<NEW>S&eacute;lectionner tous les outils actifs',
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
	'spip' => '<NEW>5. Balises, filtres, crit&egrave;res',
	'spip_cache:description' => '<NEW>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[%radio_desactive_cache3%]]@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => '<NEW>SPIP et le cache&hellip;',
	'SPIP_liens:description' => '@puce@ Tout els enlla&ccedil;os del lloc s\'obren per defecte a la mateixa finestra de navegaci&oacute; que esteu. Per&ograve; pot ser &uacute;til obrir els enlla&ccedil;os externs al lloc en una nova finestra --es pot aconseguir afegint {target="_blank"} a totes les etiquetes &lt;a&gt; dotades per SPIP de les classes {spip_out}, {spip_url} o {spip_glossaire}. A vegades pot ser &uacute;til afegir una d\'aquestes classes als enlla&ccedil;os de l\'esquelet del lloc (fitxers html) per tal d\'ampliar al m&agrave;xim aquesta funcionalitat.[[%radio_target_blank3%]]

@puce@ SPIP permet lligar paraules amb la seva definici&oacute; gr&agrave;cies a la drecera tipogr&agrave;fica <code>[?mot]</code>. Per defecte (o si deixeu buida la casella de m&eacute;s avall), el glossari extern us envia cap a l\'enciclop&egrave;dia lliure wikipedia.org. Us toca a vosaltres decidir quina adre&ccedil;a voleu utilitzar. <br />Enlla&ccedil; de prova: [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP i els enlla&ccedil;os externs',
	'standard' => '<NEW>standard',
	'statuts_spip' => '<NEW>Uniquement les statuts SPIP suivants :',
	'statuts_tous' => '<NEW>Tous les statuts',
	'stat_auteurs' => '<NEW>Les auteurs en stat',
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
	'travaux_titre' => '<NEW>@_CS_TRAVAUX_TITRE@',
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
	'typo-corr' => '<NEW>2. Am&eacute;liorations des textes',
	'typo-racc' => '<NEW>3. Raccourcis typographiques',
	'typo_exposants:description' => '<NEW>Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).',
	'typo_exposants:nom' => '<NEW>Exposants typographiques',

	// V
	'validez_page' => '<NEW>Pour acc&eacute;der aux modifications :',
	'variable_vide' => '<NEW>(Vide)',
	'vars_modifiees' => '<NEW>Les donn&eacute;es ont bien &eacute;t&eacute; modifi&eacute;es',
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
