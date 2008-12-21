<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => ':&nbsp;non',
	'2pts_oui' => ':&nbsp;s&iacute;',

	// S
	'SPIP_liens:description' => '@puce@ Tolos enllaces del sitiu abrense por omisi&oacute;n nel ventanu de &ntilde;avegaci&oacute;n actual. Pero pue ser ama&ntilde;oso abrir los enllaces esternos al sitiu nun ventanu esterior nuevu -- lo que lleva a amesta-yos {target="_blank"} a toles balices &lt;a&gt; a les que SPIP conse&ntilde;a les clases {spip_out}, {spip_url} o {spip_glossaire}. Pue ser necesario amesta-yos una d\'estes clases a los enllaces de la cadarma del sitiu (archivos html) pa estender al m&aacute;simu esta carauter&iacute;stica.[[%radio_target_blank3%]]

@puce@ SPIP permite enllazar les pallabres cola so definici&oacute;n gracies a l\'atayu tipogr&aacute;ficu <code>[?pallabra]</code>. Por omisi&oacute;n (o si dexes vac&iacute;a la caxina d\'embaxo), el glosariu esternu empobina pa la enciclopedia llibre wikipedia.org. A to eleici&oacute;n l\'enllaz a utilizar. <br />Enllaz de preba: [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP y los enllaces… esternos',

	// A
	'acces_admin' => 'Accesu alministraores:',
	'action_rapide' => 'Aici&oacute;n r&aacute;pida, &iexcl;&uacute;nicamente si sabes lo que tas faciendo!',
	'action_rapide_non' => 'Aici&oacute;n r&aacute;pida, disponible de magar que actives esta ferramienta:',
	'attente' => 'N\'espera...',
	'auteur_forum:description' => 'Encamienta a tolos autores de mensaxes p&uacute;blicos escribir (&iexcl;polo menos una lletra!) nel campu &laquo;@_CS_FORUM_NOM@&raquo; col fin d\'evitar los mensaxes totalmente an&oacute;nimos.',
	'auteur_forum:nom' => 'Ensin foros an&oacute;nimos',
	'auteurs:description' => 'Esta ferramienta configura l\'aspeutu de [la p&aacute;xina de los autores->./?exec=auteurs], na parte privada.

@puce@ Define equ&iacute; el n&uacute;mberu m&aacute;simu d\'autores a amosar nel cuadru central de la p&aacute;xina d\'autores. Darr&eacute;u, af&iacute;tarase una compaxinaci&oacute;n.[[%max_auteurs_page%]]

@puce@ &iquest;Qu&eacute; estatutos d\'autor puen llistase nesta p&aacute;xina?
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'P&aacute;xina d\'autores',

	// B
	'basique' => 'B&aacute;sica',
	'blocs:aide' => 'Bloques Desplegables: <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias: <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) y <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Te permite crear bloques que puen facese visibles o invisibles al calcar nel so t&iacute;tulu.

@puce@ {{Nos testos SPIP}}: los redactores disponen de les nueves balices &lt;bloc&gt; (o &lt;invisible&gt;) y &lt;visible&gt; pa utilizar nos testos as&iacute;: 

<quote><code>
<bloc>
 Un t&iacute;tulu nel que podr&aacute; calcase
 
 El testu a esconder/amosar, tres dos saltos de llinia...
 </bloc>
</code></quote>

@puce@ {{Nes cadarmes}}: dispones de les noeves balices #BLOC_TITRE, #BLOC_DEBUT y #BLOC_FIN pa utilizar as&iacute;: 
<quote><code> #BLOC_TITRE o #BLOC_TITRE{mio_URL}
 Mio t&iacute;tulu
 #BLOC_RESUME    (opcional)
 una versi&oacute;n en resume del bloque siguiente
 #BLOC_DEBUT
 El mio bloque desplegable (que contendr&aacute; la URL a la que apunta si fai falta)
 #BLOC_FIN</code></quote>

@puce@ Si marques &laquo;si&raquo; embaxo, l\'apertura d\'un bloque provocar&aacute; que se pesllen toos los dem&aacute;s bloques de la p&aacute;xina, col env&iacute;s de nun tener m&aacute;s que uno solu abiertu a la vez.[[%bloc_unique%]]',
	'blocs:nom' => 'Bloques Desplegables',
	'boites_privees:description' => 'Toes les caxes descrites embaxo apaecen per dayuri na parte privada.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]][[->%bp_urls_propres%]][[->%bp_tri_auteurs%]]
- {{Les revisiones de La Navaya Suiza}}: un cuadru na presente p&aacute;xina de configuraci&oacute;n, que indica les caberes modificaciones amest&aacute;es al c&oacute;digu del plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Los art&iacute;culos en formatu SPIP}}: un cuadru plegable suplementariu pa los art&iacute;culos, col env&iacute;s de saber el c&oacute;digu fonte utilizao polos autores.
- {{Estad&iacute;stiques de los autores}}: un cuadru suplementariu na [p&aacute;xina de los autores->./?exec=auteurs] que amuesa los caberos 10 coneutaos y les inscripciones nun confirm&aacute;es. S&oacute;lo los alministradores ven esta informaci&oacute;n.
- {{Les URLs propies}}: un cuadru desplegable pa cada oxetu de conten&iacute;u (art&iacute;culu, estaya, autor, ...) que indica la URL propia asociada igual que los eventuales nomatos. La ferramienta &laquo;[.->type_urls]&raquo; te permite l\'axuste finu de les URLs del to sitiu.
- {{L\'orde d\'autores}}: un cuadru desplegable pa los art&iacute;culos que tengan m&aacute;s d\'un autor y que permite axustar facilmente l\'orde en que s\'amuesen.',
	'boites_privees:nom' => 'Caxes privaes',
	'bp_tri_auteurs' => 'Ordenaciones d\'autores',
	'bp_urls_propres' => 'Les URLs propies',

	// C
	'cache_controle' => 'Control de la cach&eacute;',
	'cache_nornal' => 'Usu normal',
	'cache_permanent' => 'Cach&eacute; permanente',
	'cache_sans' => 'Ensin cach&eacute;',
	'categ:admin' => '1. Alministraci&oacute;n',
	'categ:divers' => '60. Diversos',
	'categ:interface' => '10. Interfaz privada',
	'categ:public' => '40. Asoleyamientu p&uacute;blicu',
	'categ:spip' => '50. Balices, filtros, criterios',
	'categ:typo-corr' => '20. Meyores de los testos',
	'categ:typo-racc' => '30. Atayos tipogr&aacute;ficos',
	'certaines_couleurs' => 'S&oacute;lo les balices definies embaxo@_CS_ASTER@ :',
	'chatons:aide' => 'Emoticonos: @liste@',
	'chatons:description' => 'Enxerta imaxes (o emoticonos pa los {chats}) en tolos testos nos que apaeza una cadena de tipu <code>:nome</code>.
_ Esta ferramienta camuda esos atayos poles imaxes del mesmu nome que alcuentre nel direutoriu plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Emoticonos',
	'class_spip:description1' => 'Equ&iacute; vas poder definir dellos atayos de SPIP. Un valor vac&iacute;u ye lo mesmo que utilizar el valor por omisi&oacute;n.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{Los atayos de SPIP}}.

Equ&iacute; vas poder definir dellos atayos de SPIP. Un valor vac&iacute;u ye igual que utilizar el valor por omisi&oacute;n.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

{Atenci&oacute;n: si la ferramienta &laquo;[.->pucesli]&raquo; ta activada, el remplazu del gui&oacute;n &laquo;&nbsp;-&nbsp;&raquo; nun s\'efeutua; nel so llugar se utilizar&aacute; una llista &lt;ul>&lt;li>.}

SPIP utiliza habitualmente la etiqueta &lt;h3&gt; pa los intert&iacute;tulos. Escueye equ&iacute; otra si qui&eacute;s cambeala:[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP escueye utilizar la marca&lt;strong> pa trescribir les negrines. Pero &lt;b> podr&iacute;a convenir lo mesmo, con o ensin estilu. Como t&uacute; lo veas: [[%racc_g1%]][[->%racc_g2%]]

SPIP escueye utilizar la marca &lt;i> pa trescribir les it&aacute;liques. Pero &lt;em> podr&iacute;a convenir lo mesmo, con o ensin estilu. Como t&uacute; lo veas: [[%racc_i1%]][[->%racc_i2%]]

@puce@ {{Los estilos por omisi&oacute;n de SPIP}}. Hasta la versi&oacute;n 1.92 de SPIP, los atayos tipogr&aacute;ficos produc&iacute;en balices col estilu "spip" conse&ntilde;&aacute;u por sistema. Por exemplu: <code><p class="spip"></code>. Equ&iacute; pues definir l\'estilu d\'estes balices en funci&oacute;n de les tos fueyes d\'estilu. Una caxa vac&iacute;a significa que nun va aplicase deng&uacute;n estilu en particular.

{Atenci&oacute;n: si se cambearon m&aacute;s enriba dellos atayos (llinia horizontal, intert&iacute;tulu, it&aacute;lica, negrina), los estilos d\'embaxo nun s\'aplicar&aacute;n.}

<q1>
_ {{1.}} Balices &lt;p&gt;, &lt;i&gt;, &lt;strong&gt;:[[%style_p%]]
_ {{2.}} Balices &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt;, &lt;blockquote&gt; y les llistes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_h%]]

Dec&aacute;tate: al modificar esti segundu estilu, pierdes los estilos est&aacute;ndar de SPIP asociaos con eses balices.</q1>',
	'class_spip:nom' => 'SPIP y los sos atayos…',
	'code_css' => 'CSS',
	'code_fonctions' => 'Funciones',
	'code_jq' => 'jQuery',
	'code_js' => 'JavaScript',
	'code_options' => 'Opciones',
	'code_spip_options' => 'Opciones de SPIP',
	'contrib' => 'M&aacute;s info: @url@',
	'corbeille:description' => 'SPIP desanicia autom&aacute;ticamente los oxetos tiraos a la basoria en pasando 24 hores, en xeneral hacia les 4 de la ma&ntilde;ana, gracies a un trabayu &laquo;CRON&raquo; (llanzamientu peri&oacute;dicu y/o autom&aacute;ticu de procesos preprogramaos). Equ&iacute; pues encaboxar esi procesu col fin de xestionar meyor la papelera.[[%arret_optimisation%]]',
	'corbeille:nom' => 'La papelera',
	'corbeille_objets' => '@nb@ oxeto(s) na papelera.',
	'corbeille_objets_lies' => '@nb_lies@ enllaz(es) detectao(s).',
	'corbeille_objets_vide' => 'Nun hai oxetos na papelera',
	'corbeille_objets_vider' => 'Desaniciar los oxetos seleicionaos',
	'corbeille_vider' => 'Vaciar la papelera:',
	'couleurs:aide' => 'Poner de colores: <b>[coul]testu[/coul]</b>@fond@ siendo <b>coul</b> = @liste@',
	'couleurs:description' => 'Permite aplica-yos facilmente colores a tolos testos del sitiu (art&iacute;culos, breves, t&iacute;tulos, foru, …) utilizando balices en atayos.

Dos exemplos id&eacute;nticos pa camudar la color del testu:@_CS_EXEMPLE_COULEURS2@

Idem pa camudar el fondu, si la opci&oacute;n d\'embaxo lo permite:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@El formatu d\'estes balices personalizaes tien que llistar les colores esistentes o definir pareyes &laquo;baliza=color&raquo;, too separtao por comes. Exemplos : &laquo;gris, bermeyo&raquo;, &laquo;suave=mariello, fuerte=bermeyo&raquo;, &laquo;baxu=#99CC11, altu=brown&raquo; o tambi&eacute;n &laquo;gris=#DDDDCC, bermeyo=#EE3300&raquo;. Pal primer y l\'&uacute;ltimu exemplu, les balices autorizaes son: <code>[gris]</code> y <code>[bermeyo]</code> (<code>[fond gris]</code> y <code>[fond bermeyo]</code> si los fondos tan permit&iacute;os).',
	'couleurs:nom' => 'Too en collores',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]testu[/coul]</b>, <b>[bg&nbsp;coul]testu[/coul]</b>',
	'cs_comportement:description' => '@puce@ {{Logs.}} Atopa bayurosa informaci&oacute;n tocante al funcionamentu de la Navaya Suiza nos archivos {spip.log} que puen alcontrase nel direutoriu: {@_CS_DIR_TMP@}[[%log_couteau_suisse%]]

@puce@ {{Opciones SPIP.}} SPIP ordena los plugins siguiendo un orde espec&iacute;ficu. A la fin de tar seguru que la Navaya Suiza sea el primeru pa remanar dende eh&iacute; delles opciones de SPIP, marca la opci&oacute;n siguiente. Si los permisos del sirvidor lo permiten, l\'archivu {@_CS_FILE_OPTIONS@} camudarase autom&aacute;ticamente pa amesta-y l\'archivu {@_CS_DIR_TMP@couteau-suisse/mes_spip_options.php}.
[[%spip_options_on%]]

@puce@ {{Peticiones esternes.}} La Navaya Suiza compreba davezu la esistencia d\'una versi&oacute;n m&aacute;s reciente del so c&oacute;digu e informa na p&aacute;xina de configuraci&oacute;n si hubiera una actualizaci&oacute;n disponible. Si les peticiones esternes del to sirvidor dan problemes, marca la caxa siguiente.[[%distant_off%]]',
	'cs_comportement:nom' => 'Comportamientu de la Navaya Suiza',
	'cs_distant_off' => 'Les comprebaciones de versiones distintes',
	'cs_log_couteau_suisse' => 'Los rexistros detallaos de la Navaya Suiza',
	'cs_reset' => '&iquest;Confirmes que qui&eacute;s reaniciar dafechu la Navaya Suiza?',
	'cs_spip_options_on' => 'Les opciones de SPIP en &laquo;@_CS_FILE_OPTIONS@&raquo;',

	// D
	'decoration:aide' => 'Decoraci&oacute;n: <b>&lt;baliza&gt;test&lt;/baliza&gt;</b>, con <b>baliza</b> = @liste@',
	'decoration:description' => 'Nuevos estilos param&eacute;tricos nos testos que son accesibles gracies a balices ente signos angulares. Exemplu: 
&lt;miobaliza&gt;testu&lt;/miobaliza&gt; o: &lt;miobaliza/&gt;.<br />Define embaxo los estilos CSS que necesites, una baliza per llinia, seg&uacute;n les sintaxis siguientes :
- {type.miobaliza = mio estilu CSS}
- {type.miobaliza.class = mio clase CSS}
- {type.miobaliza.lang = mio llingua (p.ex: ast)}
- {unalias = miobaliza}

El par&aacute;metru {type} d\'enriba pue tener tres valores:
- {span}: baliza nel interior d\'un p&aacute;rrafu (type Inline)
- {div} : baliza que crea un p&aacute;rrafu nuevu (type Block)
- {auto} : baliza determinada autom&aacute;ticamente pol plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Decoraci&oacute;n',
	'decoupe:aide' => 'Bloque de lling&uuml;etes : <b>&lt;onglets>&lt;/onglets></b><br/>Separtador de p&aacute;xines o de lling&uuml;etes: @sep@',
	'decoupe:aide2' => 'Alias:&nbsp;@sep@',
	'decoupe:description' => '@puce@ Divide la presentaci&oacute;n p&uacute;blica d\'un art&iacute;culu en delles p&aacute;xines gracies a una paxinaci&oacute;n autom&aacute;tica. Nam&aacute;i pon nel art&iacute;culu cuatro signos m&aacute;s consecutivos (<code>++++</code>) nel llugar u vaya tar el corte.

Por omisi&oacute;n, la Navaya Suiza enxerta los n&uacute;mberos de p&aacute;xina na cabecera y el pie de l\'art&iacute;culu autom&aacute;ticamente. Pero tienes la posibilid&aacute; de poner esti n&uacute;mberu n\'otru llugar de la to cadarma gracies a una baliza #CS_DECOUPE que puedes activar equ&iacute;:
[[%balise_decoupe%]]

@puce@ Si utilices esti separtaor dientro de les balices &lt;onglets&gt; y &lt;/onglets&gt; vas tener un xueu de lling&uuml;etes.

Nes cadarmes: tienes a to disposici&oacute;n les nueves balices #ONGLETS_DEBUT, #ONGLETS_TITRE y #ONGLETS_FIN.

Esta ferramienta puede acoplase con &laquo;[.->sommaire]&raquo;.',
	'decoupe:nom' => 'Cortar en p&aacute;xines y lling&uuml;etes',
	'desactiver_flash:description' => 'Desanicia los oxetos flash de les p&aacute;xines del sitiu web y les camuda pol conten&iacute;u alternativu asociau.',
	'desactiver_flash:nom' => 'Desactiva los oxetos flash',
	'detail_balise_etoilee' => '{{Attention}}: Compreba bien l\'usu que faen les cadarmes de les balices con asteriscu. Los procesos d\'esta ferramienta nun s\'aplicar&aacute;n a: @bal@.',
	'detail_fichiers' => 'Archivos:',
	'detail_inline' => 'C&oacute;digu en llinia:',
	'detail_jquery1' => '{{Atenci&oacute;n}}: esta ferramienta necesita el plugin {jQuery} pa funcionar con esta versi&oacute;n de SPIP.',
	'detail_jquery2' => 'Esta ferramienta necesita la llibrer&iacute;a {jQuery}.',
	'detail_jquery3' => '{{Atenci&oacute;n}}: esta ferramienta necesita el plugin [jQuery pa SPIP 1.92->http://files.spip.org/spip-zone/jquery_192.zip] pa funcionar correutamente con esta versi&oacute;n de SPIP.',
	'detail_pipelines' => 'Tuber&iacute;es:',
	'detail_traitements' => 'Tratamientos:',
	'dossier_squelettes:description' => 'Modifica la carpeta de cadarma utilizada. Por exemplu: "squelettes/miocadarma". Pues escribir dellos direutorios separtaos por dos puntos <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. Si dexes vac&iacute;u el cuadru siguiente (o escribiendo "dist"), sedr&aacute; la cadarma orixinal "dist" que ufre SPIP la que se use.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Direutoriu de la cadarma',

	// E
	'effaces' => 'Desaniciaos',
	'en_travaux:description' => '<MODIF>Permite amosar un mensaxe personalizable demientres una fase de mantenimientu en tou el sitiu p&uacute;blicu.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Sitiu n\'obres',
	'erreur:bt' => '<span style=\\"color:red;\\">Atenci&oacute;n :</span> la barra tipogr&aacute;fica (versi&oacute;n @version@) paez antigua.<br />La Navaya Suiza ye compatible con una versi&oacute;n mayor o igual a @mini@.',
	'erreur:description' => '&iexcl;falta la id na definici&oacute;n de la ferramienta!',
	'erreur:distant' => 'el sirvidor remotu',
	'erreur:jquery' => '{{Nota}}: la biblioteca {jQuery} paez inactiva nesta p&aacute;xina. Has de consultar [equ&iacute;->http://www.spip-contrib.net/?article2166] el p&aacute;rrafu so les dependencies del plugin o recargar esta p&aacute;xina.',
	'erreur:js' => 'Paez que hubo un error de JavaScript nesta p&aacute;xina que torga el so bon funcionamientu. Has de activar JavaScript nel to &ntilde;avegador o desactivar dellos plugins SPIP del to sitiu.',
	'erreur:nojs' => 'El JavaScript ta desactiv&aacute;u nesta p&aacute;xina.',
	'erreur:nom' => '&iexcl;Fallu!',
	'erreur:probleme' => 'Problema en: @pb@',
	'erreur:traitements' => 'La Navaya Suiza - Error de compilaci&oacute;n de los tratamientos: &iexcl;la mestura de \'typo\' y \'propre\' ta torgada!',
	'erreur:version' => 'Esta ferramienta nun ta disponible pa esta versi&oacute;n de SPIP.',
	'etendu' => 'Estend&iacute;u',

	// F
	'f_jQuery:description' => 'Torga l\'instalaci&oacute;n de {jQuery} na parte p&uacute;blica col env&iacute;s d\'aforrar un poco de &laquo;tiempu de m&aacute;quina&raquo;. Esta biblioteca ([->http://jquery.com/]) aporta enforma de comodid&aacute; na programaci&oacute;n de JavaScript y pue utilizase por ciertos plugins. SPIP lo utiliza na so parte privada.

Atenci&oacute;n: delles ferramientes de la Navaya Suiza necesiten les funciones de {jQuery}. ',
	'f_jQuery:nom' => 'Desactiva jQuery',
	'filets_sep:aide' => '<NEW>Filets de S&eacute;paration&nbsp;: <b>__i__</b> o&ugrave; <b>i</b> est un nombre.<br />Autres filets disponibles : @liste@',
	'filets_sep:description' => '<NEW>Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de SPIP.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d\'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => '<NEW>Filets de S&eacute;paration',
	'filtrer_javascript:description' => 'Pa xestionar l\'enxertu de JavaScript nos art&iacute;culos, hai tres modos disponibles:
- <i>enxam&aacute;s</i>: el JavaScript refugase siempre
- <i>omisi&oacute;n</i>: el JavaScript m&aacute;rcase en roxu nel espaciu priv&aacute;u
- <i>siempre</i>: el JavaScript aceptase siempre.

Atenci&oacute;n: nos foros, solicitudes, fluxos sindicaos, etc., la xesti&oacute;n del JavaScript ye <b>siempre</b> en mou seguru.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Xesti&oacute;n del JavaScript',
	'flock:description' => 'Desactiva el sistema de bloqu&eacute;u d\'archivos neutralizando la funci&oacute;n PHP {flock()}. Dellos agospiamientos causen problemes graves por cuenta d\'un sistema d\'archivos inadaut&aacute;u que carez de perda de sincronizaci&oacute;n. Nun actives esta ferramienta si el sitiu funciona normalmente.',
	'flock:nom' => 'Ensin bloqu&eacute;u d\'archivos',
	'fonds' => 'Fondos:',
	'forcer_langue:description' => 'Fuerza el contestu de llingua pa los xuegos de cadarmes multilling&uuml;es que tengan un formulariu o un menu de lling&uuml;es que sepa xestionar la cookie de lling&uuml;es.

T&eacute;unicamente, l\'efeutu d\'esta ferramienta ye:
- desactivar la gueta d\'una cadarma en funci&oacute;n de la llingua de l\'oxetu,
- desactivar el criteriu <code>{lang_select}</code> autom&aacute;ticu pa los oxetos cl&aacute;sicos (art&iacute;culos, breves, estayes, etc... ).

Los bloques multi s\'amuesen siempre na llingua pid&iacute;a pol visitante.',
	'forcer_langue:nom' => 'Forzar llingua',
	'format_spip' => 'Los art&iacute;culos en formatu SPIP',
	'forum_lgrmaxi:description' => 'Por omisi&oacute;n, los mensaxes del foru nun tienen llendes de tama&ntilde;u. Si se activa esta ferramienta, va amosase un mensaxe d\'error cuando daqui&eacute;n quiera mandar un mensaxe de tama&ntilde;u superior al valor conse&ntilde;&aacute;u, y el mensaxe refugarase. Un valor vac&iacute;u o igual a 0 significa que nun s\'aplica llende dala.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Tama&ntilde;u de los foros',

	// G
	'glossaire:aide' => 'Testu ensin glosariu: <b>@_CS_SANS_GLOSSAIRE@</b>',
	'glossaire:description' => '@puce@ Xesti&oacute;n d’un glosariu internu enllaz&aacute;u con un o m&aacute;s groupes de pallabres-clave. Escribe equ&iacute; el nome de los grupos separt&aacute;ndolos con dos puntos &laquo;:&raquo;. Si se dexa vac&iacute;a la caxa siguiente (o escribiendo "Glossaire"),sedr&aacute; el grupu "Glossaire" el que va utilizase.[[%glossaire_groupes%]]

@puce@ Pa cada pallabra, ties la posibilid&aacute; d\'escoyer el n&uacute;mberu m&aacute;simu d\'enllaces creaos nos testos. Tou valor nulu o negativu implica que toes les pallabres reconoc&iacute;es van tratase. [[%glossaire_limite% par mot-cl&eacute;]]

@puce@ Ufrense dos soluciones pa xenerar el ventanucu autom&aacute;ticu que apaez cuando pases el mur enriba la pallabra. [[%glossaire_js%]]',
	'glossaire:nom' => 'Glosariu internu',
	'glossaire_css' => 'Soluci&oacute;n CSS',
	'glossaire_js' => 'Soluci&oacute;n JavaScript',
	'guillemets:description' => 'Camuda autom&aacute;ticamente les comines dereches (") por les comines tipogr&aacute;fiques de la llingua de composici&oacute;n. El camb&eacute;u, tresparente pa l\'usuariu, nun camuda el testu orixinal sinon s&oacute;lo l\'aspeutu final.',
	'guillemets:nom' => 'Comines tipogr&aacute;fiques',

	// H
	'help' => '{{Esta p&aacute;xina &uacute;nicamente ye accesible pa los responsables del sitiu.}}<p>Da accesu a les diferentes funciones suplementaries aport&aacute;es pol plugin &laquo;{{La&nbsp;Navaya&nbsp;Suiza}}&raquo;.',
	'help2' => 'Versi&oacute;n local: @version@',
	'help3' => '<p>Enllaces de documentaci&oacute;n :<br/>• [La&nbsp;Navaya&nbsp;Suiza->http://www.spip-contrib.net/?article2166]@contribs@</p><p>Reentamos:
_ • [De les ferramientes tapec&iacute;es|Tornar a l\'apariencia inicial d\'esta p&aacute;xina->@hide@]
_ • [De tol plugin|Tornar a l\'est&aacute;u inicial del plugin->@reset@]@install@
</p>',

	// I
	'icone_visiter:description' => 'Camb&eacute;a la imaxe del bot&oacute;n est&aacute;ndar &laquo;Visitar&raquo; (enriba a la derecha d\'esta p&aacute;xina) pol logo del sitiu, si esiste.

Pa definir esti logo, vete a la p&aacute;xina de &laquo;Configuraci&oacute;n del sitiu&raquo; calcando nel bot&oacute;n &laquo;Configuraci&oacute;n&raquo;.',
	'icone_visiter:nom' => 'Bot&oacute;n &laquo;Visitar&raquo;',
	'insert_head:description' => 'Activa autom&aacute;ticamente la baliza [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] en toes les cadarmes, da igual que tengan o non esta baliza ente &lt;head&gt; y &lt;/head&gt;. Gracies a esta opci&oacute;n, los plugins podr&aacute;n enxertar JavaScript (.js) o fueyes d\'estilu (.css).',
	'insert_head:nom' => 'Baliza #INSERT_HEAD',
	'insertions:description' => 'ATENCI&Oacute;N: &iexcl;&iexcl;ferramienta en cursu de desendolcu!! [[%insertions%]]',
	'insertions:nom' => 'Correiciones autom&aacute;tiques',
	'introduction:description' => 'Esta baliza pa amestar nes cadarmes sirve en xeneral pa la portada o pa les estayes col env&iacute;s de producir un resume de art&iacute;culos, de breves, etc...</p>
<p>{{Atenci&oacute;n}}: Enantes d\'activar esta funci&oacute;n, compreba bien que denguna funci&oacute;n {balise_INTRODUCTION()} nun esista ya na cadarma o nos plugins, la sobrecarga producir&iacute;a un error de compilaci&oacute;n.</p>
@puce@ Puedes precisar (en porcentaxe relativu del valor utiliz&aacute;u por omisi&oacute;n) el llargu del testu devueltu pela baliza #INTRODUCTION. Un valor nulu o igual a 100 nun modifica l\'aspeutu de la introducci&oacute;n utilizando ent&oacute;s los valores por omisi&oacute;n siguientes: 500 carauteres pa los art&iacute;culos, 300 pa les breves y 600 pa los foros o les estayes.
[[%lgr_introduction%&nbsp;%]]
@puce@ Por omisi&oacute;n, los puntos de siguir amestaos al resultau de la baliza #INTRODUCTION si el testu ye enforma llargu son: <html>&laquo;&amp;nbsp;(…)&raquo;</html>. Equ&iacute; pues conse&ntilde;ar una cadena de carauteres propia que indique al llector que el testu cort&aacute;u tien una continuaci&oacute;n.
[[%suite_introduction%]]
@puce@ Si la baliza #INTRODUCTION util&iacute;zase pa resumir un art&iacute;culu, la Navaya Suiza pue fabricar un enllaz d\'hipertestu pa amestar a los puntos de siguir definios enriba, col fin de llevar al llector al testu orixinal. Por exemplu: &laquo;Lleer el restu de l\'art&iacute;culu…&raquo;
[[%lien_introduction%]]
',
	'introduction:nom' => 'Baliza #INTRODUCTION',

	// J
	'jcorner:description' => '&laquo;Esquines guapes&raquo; ye una ferramienta que permite cambear facilmente l\'aspeutu de les esquines de los {{cuadros coloreaos}} na parte p&uacute;blica del to sitiu. &iexcl;Too ye posible, o cuasique!
_ Mira el result&aacute;u nesta p&aacute;xina: [->http://www.malsup.com/jquery/corner/].

Llista embaxo los oxetos de la cadarma a redondiar utilizando la sintaxis CSS (.class, #id, etc. ). Utiliza el signu &laquo;=&raquo; pa especificar la orde jQuery a utilizar y una barra doble (&laquo;//&raquo;) pa los comentarios. Si nun hai signu igual, aplicaranse esquines redondes (equivalente a: <code>.mio_clase = .corner()</code>).[[%jcorner_classes%]]

Atenci&oacute;n, esta ferramienta necesita pa funcionar el plugin {jQuery} : {Round Corners}. La Navaya Suiza pue instalalu direutamente si marques el cuadru siguiente. [[%jcorner_plugin%]]',
	'jcorner:nom' => 'Esquines Guapes',
	'jcorner_plugin' => '&laquo;plugin Round Corners&raquo;',
	'jq_localScroll' => 'jQuery.LocalScroll ([demo->http://demos.flesler.com/jquery/localScroll/])',
	'jq_scrollTo' => 'jQuery.ScrollTo ([demo->http://demos.flesler.com/jquery/scrollTo/])',
	'js_defaut' => 'Por omisi&oacute;n',
	'js_jamais' => 'Enxam&aacute;s',
	'js_toujours' => 'Siempre',

	// L
	'label:admin_travaux' => 'Zarrar el sitiu p&uacute;blicu por:',
	'label:arret_optimisation' => 'Torgar que SPIP vac&iacute;e la papelera autom&aacute;ticamente:',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Creaci&oacute;n sistem&aacute;tica del sumariu:',
	'label:balise_decoupe' => 'Activar la baliza #CS_DECOUPE:',
	'label:balise_sommaire' => 'Activar la baliza #CS_SOMMAIRE:',
	'label:bloc_unique' => 'Solo un bloque abiertu na p&aacute;xina:',
	'label:couleurs_fonds' => 'Permitir los fondos:',
	'label:cs_rss' => 'Activar:',
	'label:debut_urls_libres' => '<:label:debut_urls_propres:>',
	'label:debut_urls_propres' => 'Entamu de les URLs:',
	'label:debut_urls_propres2' => '<:label:debut_urls_propres:>',
	'label:decoration_styles' => 'Les tos balices d\'estilu personaliz&aacute;u:',
	'label:derniere_modif_invalide' => 'Recalcular xusto dempu&eacute;s d\'un camb&eacute;u:',
	'label:distant_off' => 'Desactivar:',
	'label:dossier_squelettes' => 'Direutoriu(os) a utilizar:',
	'label:duree_cache' => 'Duraci&oacute;n de la cach&eacute; local:',
	'label:duree_cache_mutu' => 'Duraci&oacute;n de la cach&eacute; en mutualizaci&oacute;n:',
	'label:expo_bofbof' => 'Escribir como exponentes: <html>St(e)(s), Bx, Bd(s) y Fb(s)</html>',
	'label:forum_lgrmaxi' => 'Valor (en carauteres):',
	'label:glossaire_groupes' => 'Grupu(os) utilizao(s):',
	'label:glossaire_js' => 'T&eacute;unica utilizada:',
	'label:glossaire_limite' => 'N&uacute;mberu m&aacute;simu d\'enllaces creaos:',
	'label:insertions' => 'Correiciones autom&aacute;tiques:',
	'label:jcorner_classes' => 'Meyorar les esquines de les seleiciones siguientes:',
	'label:jcorner_plugin' => 'Instalar el plugin {jQuery} siguiente:',
	'label:lgr_introduction' => 'Estensi&oacute;n del resume:',
	'label:lgr_sommaire' => 'Estensi&oacute;n del sumariu (9 a 99):',
	'label:lien_introduction' => 'Puntos suspensivos calcables:',
	'label:liens_interrogation' => 'Protexer les URLs:',
	'label:liens_orphelins' => 'Enllaces calcables:',
	'label:log_couteau_suisse' => 'Activar:',
	'label:marqueurs_urls_propres' => 'Amestar los marcadores que dixebren los oxetos (SPIP>=2.0) :<br/>(ex. : &laquo;&nbsp;-&nbsp;&raquo; pa -Mio-estaya-, &laquo;&nbsp;@&nbsp;&raquo; pa @Mio-sitiu@) ',
	'label:marqueurs_urls_propres2' => '<:label:marqueurs_urls_propres:>',
	'label:marqueurs_urls_propres_qs' => '<:label:marqueurs_urls_propres:>',
	'label:max_auteurs_page' => 'Autores por p&aacute;xina:',
	'label:message_travaux' => 'El mensaxe de mantenimientu:',
	'label:moderation_admin' => 'Validar autom&aacute;ticamente los mensaxes de los: ',
	'label:paragrapher' => 'Facer p&aacute;rrafos siempre:',
	'label:puce' => '<NEW>Puce publique &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Valor de la cuota:',
	'label:racc_g1' => 'Entrada y salida pa poner en &laquo;<html>{{negrina}}</html>&raquo;:',
	'label:racc_h1' => 'Entrada y salida pa un &laquo;<html>{{{intert&iacute;tulu}}}</html>&raquo;:',
	'label:racc_hr' => 'Llinia horizontal &laquo;<html>----</html>&raquo;:',
	'label:racc_i1' => 'Entrada y salida pa conse&ntilde;ar escritura en &laquo;<html>{it&aacute;liques}</html>&raquo;:',
	'label:radio_desactive_cache3' => 'Usu de la cach&eacute;:',
	'label:radio_desactive_cache4' => 'Usu de la cach&eacute;:',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Ventanu nuevu pa los enllaces esternos:',
	'label:radio_type_urls3' => 'Formatu de les URLs:',
	'label:scrollTo' => 'Instalar los plugins {jQuery} siguientes:',
	'label:separateur_urls_page' => 'Carauter de separaci&oacute;n \'type-id\'<br/>(p.ex.: ?article-123):',
	'label:set_couleurs' => 'Xuegu a utilizar:',
	'label:spam_mots' => 'Secuencies torg&aacute;es:',
	'label:spip_options_on' => 'Incluir:',
	'label:spip_script' => 'Script de llamada:',
	'label:style_h' => 'El to estilu:',
	'label:style_p' => 'El to estilu:',
	'label:suite_introduction' => 'Puntos de siguir:',
	'label:terminaison_urls_arbo' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_libres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_page' => 'Terminaci&oacute;n de les URLs (p.ex.: &laquo;.html&raquo;):',
	'label:terminaison_urls_propres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_propres_qs' => '<:label:terminaison_urls_page:>',
	'label:titre_travaux' => 'T&iacute;tulu del mensaxe:',
	'label:titres_etendus' => 'Activar l\'usu estend&iacute;u de les balices #TITRE_XXX:',
	'label:tri_articles' => 'To seleici&oacute;n:',
	'label:url_arbo_minuscules' => 'Conservar les may&uacute;scules de los t&iacute;tulos nes URLs:',
	'label:url_arbo_sep_id' => 'Carauter de separaci&oacute;n \'titre-id\' en casu de duplicaos :<br/>(nun uses \'/\')',
	'label:url_glossaire_externe2' => 'Enllaz al glosariu esternu:',
	'label:urls_arbo_sans_type' => 'Amosar el tipu d\'oxetu SPIP nes URLs:',
	'label:webmestres' => 'Llista de los webmasters del sitiu:',
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

	// M
	'mailcrypt:description' => 'Mazcarita toos los enllaces de corr&eacute;u presentes nos testos y los camuda por un enllaz JavaScript que permite lo mesmo activar la mensaxer&iacute;a del llector. Esta ferramienta escontra\'l corr&eacute;u puxarra tenta torgar que los robots collechen les se&ntilde;es electr&oacute;niques escrites en claro nos foros o nes balices de les tos cadarmes.',
	'mailcrypt:nom' => 'MailCrypt',
	'message_perso' => 'Candiales gracies a los traductores que pasaren per equ&iacute;. Pat ;-)',
	'moderation_admins' => '<NEW>administrateurs authentifi&eacute;s',
	'moderation_message' => '<NEW>Ce forum est mod&eacute;r&eacute; &agrave; priori&nbsp;: votre contribution n\'appara&icirc;tra qu\'apr&egrave;s avoir &eacute;t&eacute; valid&eacute;e par un administrateur du site, sauf si vous &ecirc;tes identifi&eacute; et autoris&eacute; &agrave; poster directement.',
	'moderation_moderee:description' => 'Permite llendar el llend&aacute;u de los foros p&uacute;blicos <b>configuraos a priori</b> polos usuarios inscritos.<br />Exemplu: Si yo soy el webmaster del mio sitiu, y respondo a un mensaxe d\'un usuariu, &iquest;por qu&eacute; tengo que validame el mio propiu mensaxe? &iexcl;El llendamientu llend&aacute;u failo pa m&iacute;! [[%moderation_admin%]][[-->%moderation_redac%]][[-->%moderation_visit%]]',
	'moderation_moderee:nom' => '<NEW>Mod&eacute;ration mod&eacute;r&eacute;e',
	'moderation_redacs' => '<NEW>r&eacute;dacteurs authentifi&eacute;s',
	'moderation_visits' => '<NEW>visiteurs authentifi&eacute;s',
	'modifier_vars' => 'Camudar estos @nb@ par&aacute;metros',
	'modifier_vars_0' => 'Camudar esto par&aacute;metros',

	// N
	'no_IP:description' => 'Desactiva el mecanismu de grabaci&oacute;n autom&aacute;tica de les se&ntilde;es IP de los visitantes del sitiu pa mantener la confidencialid&aacute;: SPIP ya nun conservar&aacute; deng&uacute;n n&uacute;mberu IP, nin temporalmente durante les visites (pa remanar les estad&iacute;stiques o alimentar spip.log), nin pa los foros (responsabilid&aacute;).',
	'no_IP:nom' => 'Ensin guardar la IP',
	'nouveaux' => 'Nuevos',

	// O
	'orientation:description' => '<NEW>3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.',
	'orientation:nom' => '<NEW>Orientation des images',
	'outil_actif' => 'Ferramienta activa',
	'outil_activer' => 'Activar',
	'outil_activer_le' => 'Activar la ferramienta',
	'outil_cacher' => 'Nun amosar m&aacute;s',
	'outil_desactiver' => 'Desactivar',
	'outil_desactiver_le' => 'Desactivar la ferramienta',
	'outil_inactif' => 'Ferramienta inactiva',
	'outil_intro' => 'Esta p&aacute;xina llista les carauter&iacute;stiques que ufre\'l plugin.<br /><br />Calcando nel nome de les ferramientes d\'embaxo, seleiciones los que vas poder camuda-yos l\'estau con l\'aida del bot&ograve;n central: les ferramientes actives desact&iacute;vense y <i>viceversa</i>. A cada clic, apaez la descripci&oacute;n embaxo de les llistes. Les categor&iacute;es son desplegables y les ferramientes puen tapecese. El doble-clic permite cambear r&aacute;pidamente de ferramienta.<br /><br />Pal primer usu, encami&eacute;ntase activar les ferramientes una a una, por si acasu apaecen incompatibilidaes cola to cadarma, con SPIP o con otros plugins.<br /><br />Nota: la simple carga d\'esta p&aacute;xina recompila dafechu toes les ferramientes de La Navaya Suiza.',
	'outil_intro_old' => 'Esta interfaz ye antigua.<br /><br />Si alcuentres problemes cola utilizaci&oacute;n de la <a href=\'./?exec=admin_couteau_suisse\'>interfaz nueva</a>, afal&aacute;moste a coment&aacute;noslo nel foru de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ ferramienta',
	'outil_nbs' => '@pipe@ : @nb@ ferramientes',
	'outil_permuter' => '&iquest;Camudar la ferramienta: &laquo;@text@&raquo;?',
	'outils_actifs' => 'Ferramientes actives:',
	'outils_caches' => 'Ferramientes tapec&iacute;es:',
	'outils_cliquez' => 'Calca nel nome de les ferramientes d\'embaxo pa amosar equ&iacute; la descripci&oacute;n.',
	'outils_inactifs' => 'Ferramientes inactives:',
	'outils_liste' => 'Llista de ferramientes de la Navaya Suiza',
	'outils_permuter_gras1' => 'Camudar les ferramientes en negrines',
	'outils_permuter_gras2' => '&iquest;Camudar les @nb@ ferramientes en negrines?',
	'outils_resetselection' => '<NEW>R&eacute;initialiser la s&eacute;lection',
	'outils_selectionactifs' => '<NEW>S&eacute;lectionner tous les outils actifs',
	'outils_selectiontous' => '<NEW>TOUS',

	// P
	'pack_actuel' => 'Paquete @date@',
	'pack_actuel_avert' => 'Atenci&oacute;n, les sobrecargues nos define() o les globales nun se conse&ntilde;en equ&iacute;',
	'pack_actuel_titre' => 'PAQUETE DE CONFIGURACI&Oacute;N ACTUAL DE LA NAVAYA SUIZA',
	'pack_alt' => 'Ver los par&aacute;metros de configuraci&oacute;n en cursu',
	'pack_descrip' => 'El &laquo;Paquete de configuraci&oacute;n actual&raquo; axunta el conxuntu de par&aacute;metros de configuraci&oacute;n en cursu de La Navaya Suiza: l\'activaci&oacute;n de les ferramientes y el valor de les variables, si ye\'l casu.

Si los permisos d\'escritura lo autoricen, el c&oacute;digu PHP d\'embaxo podr&aacute; amestase nel archivu {{/config/mes_options.php}} apaecer&aacute; nesta p&aacute;xina un enllaz pal reaniciu del paquete &laquo;{@pack@}&raquo;. Y ye dafechu posible camuda-y el nome.

Si reanicies el plugin calcando nun paquete, la Navaya Suiza reconfigurarase autom&aacute;ticamente en funci&oacute;n de los par&aacute;metros predefinios nesti paquete.',
	'pack_du' => '• del paquete @pack@',
	'pack_installe' => 'Afitamientu d\'un paquete de configuraci&oacute;n',
	'pack_installer' => '<NEW>&Ecirc;tes-vous s&ucirc;r de vouloir r&eacute;initialiser le Couteau Suisse et installer le pack &laquo;&nbsp;@pack@&nbsp;&raquo; ?',
	'pack_nb_plrs' => '<NEW>Il y a actuellement @nb@ &laquo;&nbsp;packs de configuration&nbsp;&raquo; disponibles.',
	'pack_nb_un' => '<NEW>Il y a actuellement un &laquo;&nbsp;pack de configuration&nbsp;&raquo; disponible',
	'pack_nb_zero' => '<NEW>Il n\'y a pas de &laquo;&nbsp;pack de configuration&nbsp;&raquo; disponible actuellement.',
	'pack_outils_defaut' => '<NEW>Installation des outils par d&eacute;faut',
	'pack_sauver' => '<NEW>Sauver la configuration actuelle',
	'pack_sauver_descrip' => '<NEW>Le bouton ci-dessous vous permet d\'ins&eacute;rer directement dans votre fichier <b>@file@</b> les param&egrave;tres n&eacute;cessaires pour ajouter un &laquo;&nbsp;pack de configuration&nbsp;&raquo; dans le menu de gauche. Ceci vous permettra ult&eacute;rieurement de reconfigurer en un clic votre Couteau Suisse dans l\'&eacute;tat o&ugrave; il est actuellement.',
	'pack_titre' => '<NEW>Configuration Actuelle',
	'pack_variables_defaut' => '<NEW>Installation des variables par d&eacute;faut',
	'par_defaut' => '<NEW>Par d&eacute;faut',
	'paragrapher2:description' => '<NEW>La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => '<NEW>Paragrapher',
	'pipelines' => '<NEW>Pipelines utilis&eacute;s&nbsp;:',
	'pucesli:description' => '<NEW>Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',
	'pucesli:nom' => '<NEW>Belles puces',

	// R
	'raccourcis' => 'Atayos tipogr&aacute;ficos activos de la Navaya Suiza:',
	'raccourcis_barre' => 'Los atayos tipogr&aacute;ficos de la Navaya Suiza',
	'reserve_admin' => 'Accesu acutao pa los alministradores.',
	'rss_actualiser' => 'Actualizar',
	'rss_attente' => 'Esperando RSS...',
	'rss_desactiver' => 'Desactivar les &laquo;Revisiones de la Navaya Suiza&raquo;',
	'rss_edition' => 'Fluxu RSS puestu al d&iacute;a el:',
	'rss_source' => 'Fonte RSS',
	'rss_titre' => '&laquo;La Navaya Suiza&raquo; en desarrollu:',
	'rss_var' => 'Les revisiones de la Navaya Suiza',

	// S
	'sauf_admin' => 'Toos, sacante los alministradores',
	'set_options:description' => '<NEW>S&eacute;lectionne d\'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[%radio_set_options4%]]',
	'set_options:nom' => '<NEW>Type d\'interface priv&eacute;e',
	'sf_amont' => '<NEW>En amont',
	'sf_tous' => '<NEW>Tous',
	'simpl_interface:description' => '<NEW>D&eacute;sactive le menu de changement rapide de statut d\'un article au survol de sa puce color&eacute;e. Cela est utile si vous cherchez &agrave; obtenir une interface priv&eacute;e la plus d&eacute;pouill&eacute;e possible afin d\'optimiser les performances client.',
	'simpl_interface:nom' => '<NEW>All&egrave;gement de l\'interface priv&eacute;e',
	'smileys:aide' => '<NEW>Smileys : @liste@',
	'smileys:description' => 'Enxerta smileys en toos los testos nos que apaeza un atayu de tipu <acronym>:-)</acronym>. Ideal pa los  foros.
_ Ta disponible una baliza pa amosar una tabla de smileys nes cadarmes : #SMILEYS.
_ Dise&ntilde;u d\'iconos: [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => '<NEW>Smileys',
	'soft_scroller:description' => '',
	'soft_scroller:nom' => '<NEW>Ancres douces',
	'sommaire:description' => '',
	'sommaire:nom' => 'Un sumariu autom&aacute;ticu',
	'sommaire_avec' => 'Un testu con sumariu: <b>@_CS_AVEC_SOMMAIRE@</b>',
	'sommaire_sans' => 'Un testu ensin sumariu: <b>@_CS_SANS_SOMMAIRE@</b>',
	'spam:description' => '<NEW>Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.

Listez ici les s&eacute;quences interdites@_CS_ASTER@ en les s&eacute;parant par des espaces. [[%spam_mots%]]
@_CS_ASTER@Pour sp&eacute;cifier un mot entier, mettez-le entre paranth&egrave;ses. Pour une expression avec des espaces, placez-la entre guillemets.',
	'spam:nom' => '<NEW>Lutte contre le SPAM',
	'spip_cache:description' => '@puce@ La cach&eacute; ocupa ciertu espaciu en discu y SPIP puede limitar la cantid&aacute;. Un valor vac&iacute;u o igual a 0 significa que nun s\'aplica cuota denguna.[[%quota_cache% Mb]]

@puce@ Cuando se fai una modificaci&oacute;n del conten&iacute;u del sitiu, SPIP invalida inmediatamente la cach&eacute; ensin esperar al siguiente c&aacute;lculu peri&oacute;dicu. Si el sitiu tien problemes de rendimientu por cuenta d\'una gran carga, puedes marcar &laquo;&nbsp;non&nbsp;&raquo; n\'esta opci&oacute;n.[[%derniere_modif_invalide%]]

@puce@ Si la baliza #CACHE nun s\'alcuentra nes tos cadarmes llocales, SPIP considera por omisi&oacute;n que la cach&eacute; d\'una p&aacute;xina tien una vida m&aacute;sima de 24 hores enantes de volver a calculala. A la fin de xestionar meyor la carga del to sirvidor, puedes cambear equ&iacute; esti valor.[[%duree_cache% hores]]

@puce@ Si tienes dellos sitios en mutualizaci&oacute;n, puedes especificar equ&iacute; el valor por omisi&oacute;n que se toma pa toos los sitios llocales (SPIP 2.0 mini).[[%duree_cache_mutu% hores]]',
	'spip_cache:description1' => '<NEW>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. @_CS_CACHE_EXTENSION@[[%radio_desactive_cache3%]]',
	'spip_cache:description2' => '<NEW>@puce@ Quatre options pour orienter le fonctionnement du cache de SPIP : <q1>
_ &bull; {Usage normal} : SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. Apr&egrave;s un certain d&eacute;lai, le cache est recalcul&eacute; et stock&eacute;.
_ &bull; {Cache permanent} : les d&eacute;lais d\'invalidation du cache sont ignor&eacute;s.
_ &bull; {Pas de cache} : d&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. Ici, rien n\'est stock&eacute; sur le disque.
_ &bull; {Contr&ocirc;le du cache} : option identique &agrave; la pr&eacute;c&eacute;dente, avec une &eacute;criture sur le disque de tous les r&eacute;sultats afin de pouvoir &eacute;ventuellement les contr&ocirc;ler.</q1>[[%radio_desactive_cache4%]]',
	'spip_cache:nom' => '<NEW>SPIP et le cache&hellip;',
	'stat_auteurs' => '<NEW>Les auteurs en stat',
	'statuts_spip' => '<NEW>Uniquement les statuts SPIP suivants :',
	'statuts_tous' => '<NEW>Tous les statuts',
	'suivi_forums:description' => '<NEW>Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => '<NEW>Suivi des forums publics',
	'supprimer_cadre' => '<NEW>Supprimer ce cadre',
	'supprimer_numero:description' => 'Aplica la funci&oacute;n de SPIP supprimer_numero() al conxuntu de {{t&iacute;tulos}}, de {{nomes}} y de {{tipos}} (de pallabres-clave) del sitiu p&uacute;blicu, ensin que\'l filtru supprimer_numero tea presente nes cadarmes.<br />Esta ye la sintaxis a utilizar nel contestu d\'un sitiu multill&iacute;ng&uuml;e: <code>1. <multi>My Title[fr]Mon Titre[ast]Mio T&iacute;tulu</multi></code>',
	'supprimer_numero:nom' => '<NEW>Supprime le num&eacute;ro',

	// T
	'titre' => 'La Navaya Suiza',
	'titre_parent:description' => '<NEW>Au sein d\'une boucle, il est courant de vouloir afficher le titre du parent de l\'objet en cours. Traditionnellement, il suffirait d\'utiliser une seconde boucle, mais cette nouvelle balise #TITRE_PARENT all&eacute;gera l\'&eacute;criture de vos squelettes. Le r&eacute;sultat renvoy&eacute; est : le titre du groupe d\'un mot-cl&eacute; ou celui de la rubrique parente (si elle existe) de tout autre objet (article, rubrique, br&egrave;ve, etc.).

Notez : Pour les mots-cl&eacute;s, un alias de #TITRE_PARENT est #TITRE_GROUPE. Le traitement SPIP de ces nouvelles balises est similaire &agrave; celui de #TITRE.

@puce@ Si vous &ecirc;tes sous SPIP 2.0, alors vous avez ici &agrave; votre disposition tout un ensemble de balises #TITRE_XXX qui pourront vous donner le titre de l\'objet \'xxx\', &agrave; condition que le champ \'id_xxx\' soit pr&eacute;sent dans la table en cours (#ID_XXX utilisable dans la boucle en cours).

Par exemple, dans une boucle sur (ARTICLES), #TITRE_SECTEUR donnera le titre du secteur dans lequel est plac&eacute; l\'article en cours, puisque l\'identifiant #ID_SECTEUR (ou le champ \'id_secteur\') est disponible dans ce cas.[[%titres_etendus%]]',
	'titre_parent:nom' => '<NEW>Balise #TITRE_PARENT',
	'titre_tests' => '<NEW>Le Couteau Suisse - Page de tests&hellip;',
	'tous' => 'Toos',
	'toutes_couleurs' => '<NEW>Les 36 couleurs des styles css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => '<NEW>Blocs multilingues&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => 'Del mesmu mou que ya pod&iacute;es facelo nes tos cadarmes, esta ferramienta te permite utilizar llibremente les cadenes de lling&uuml;es (de SPIP o de les cadarmes) con tolos conten&iacute;os del sitiu (art&iacute;culos, t&iacute;tulos, mensaxes, etc.) con l\'aida de l\'atayu <code><:cadena:></code>.

Consulta [equ&iacute; ->http://www.spip.net/fr_article2128.html] la documentaci&oacute;n de SPIP pa esti asuntu.

Esta ferramienta acepta igualmente los argumentos amestaos con SPIP 2.0. Por exemplu, l\'atayu <code><:mio_cadena{nome=Charles Martin, eda=37}:></code> permite pasa-y dos par&aacute;metros a la siguiente cadena: <code>\'mio_cadena\'=>"Bones, soi @nome@ y tengo @eda@ a&ntilde;os\\"</code>.

La funci&oacute;n SPIP usada en PHP ye <code>_T(\'cadena\')</code> ensin argumentu, y <code>_T(\'cadena\', array(\'arg1\'=>\'un testu\', \'arg2\'=>\'otru testu\'))</code> con argumentos.

 Nun t\'escaezas de verificar que la clave <code>\'cadena\'</code> tea bien definida nos archivos de les lling&uuml;es.',
	'toutmulti:nom' => '<NEW>Blocs multilingues',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Esti sitiu volver&aacute; a tar en llinia pronto.
_ Agradec&eacute;moste la comprensi&oacute;n.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => '<NEW>En naviguant sur le site en partie priv&eacute;e ([->./?exec=auteurs]), choisissez ici le tri &agrave; utiliser pour afficher vos articles &agrave; l\'int&eacute;rieur de vos rubriques.

Les propositions ci-dessous sont bas&eacute;es sur la fonctionnalit&eacute; SQL \'ORDER BY\' : n\'utilisez le tri personnalis&eacute; que si vous savez ce que vous faites (champs disponibles : {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => '<NEW>Tri des articles',
	'tri_modif' => '<NEW>Tri sur la date de modification (ORDER BY date_modif DESC)',
	'tri_perso' => '<NEW>Tri SQL personnalis&eacute;, ORDER BY suivi de :',
	'tri_publi' => '<NEW>Tri sur la date de publication (ORDER BY date DESC)',
	'tri_titre' => '<NEW>Tri sur le titre (ORDER BY 0+titre,titre)',
	'type_urls:description' => '',
	'type_urls:nom' => '<NEW>Format des URLs',
	'typo_exposants:description' => '{{Testos en franc&eacute;s}}: meyora la presentaci&oacute;n tipogr&aacute;fica de les abreviatures corrientes, escribiendo como esponente los elementos necesarios (as&iacute;, {<acronym>Mme</acronym>} tresf&oacute;rmase en {M<sup>me</sup>}) y corrixendo los fallos comunes ({<acronym>2&egrave;me</acronym>} o  {<acronym>2me</acronym>}, por exemplu, cam&uacute;dense en {2<sup>e</sup>}, &uacute;nica abreviatura correuta).

Les abreviatures obten&iacute;es son conformes coles de l\'Imprimerie nationale como les que s\'indiquen en el {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).

Ig&uuml;ense tambi&eacute;n les siguientes espresiones: <html>Dr, Pr, Mgr, m2, m3, Mn, Md, St&eacute;, &Eacute;ts, Vve, Cie, 1o, 2o, etc.</html>

Escueye equ&iacute; escribir como esponentes dellos atayos suplementarios, magar que l\'Imprimerie nationale lo tenga desaconseyao:[[%expo_bofbof%]]

{{Testos n\'ingl&eacute;s}}: escr&iacute;bense como esponente los n&uacute;mberos ordinales: <html>1st, 2nd</html>, etc.',
	'typo_exposants:nom' => '<NEW>Exposants typographiques',

	// U
	'url_arbo' => '<NEW>arborescentes@_CS_ASTER@',
	'url_html' => '<NEW>html@_CS_ASTER@',
	'url_libres' => '<NEW>libres@_CS_ASTER@',
	'url_page' => '<NEW>page',
	'url_propres' => '<NEW>propres@_CS_ASTER@',
	'url_propres-qs' => '<NEW>propres-qs',
	'url_propres2' => '<NEW>propres2@_CS_ASTER@',
	'url_propres_qs' => '<NEW>propres_qs',
	'url_standard' => '<NEW>standard',
	'urls_base_total' => '<NEW>Il y a actuellement @nb@ URL(s) en base',
	'urls_base_vide' => '<NEW>La base des URLs est vide',
	'urls_choix_objet' => 'Edici&oacute;n de la base de la URL d\'un oxetu espec&iacute;ficu:',
	'urls_edit_erreur' => '<NEW>Le format actuel des URLs (&laquo;&nbsp;@type@&nbsp;&raquo;) ne permet pas d\'&eacute;dition.',
	'urls_enregistrer' => '<NEW>Enregistrer cette URL en base',
	'urls_nouvelle' => 'Editar la URL &laquo;propia&raquo;:',
	'urls_num_objet' => '<NEW>Num&eacute;ro&nbsp;:',
	'urls_purger' => '<NEW>Tout vider',
	'urls_purger_tables' => '<NEW>Vider les tables s&eacute;lectionn&eacute;es',
	'urls_purger_tout' => '<NEW>R&eacute;initialiser les URLs stock&eacute;es dans la base&nbsp;:',
	'urls_rechercher' => '<NEW>Rechercher cet objet en base',
	'urls_titre_objet' => '<NEW>Titre enregistr&eacute; &nbsp;:',
	'urls_type_objet' => '<NEW>Objet&nbsp;:',
	'urls_url_calculee' => '<NEW>URL publique &laquo;&nbsp;@type@&nbsp;&raquo;&nbsp;:',
	'urls_url_objet' => 'URL &laquo;propia&raquo; grabada:',
	'urls_valeur_vide' => '<NEW>(Une valeur vide entraine la suppression de l\'URL)',

	// V
	'validez_page' => '<NEW>Pour acc&eacute;der aux modifications :',
	'variable_vide' => '<NEW>(Vide)',
	'vars_modifiees' => '<NEW>Les donn&eacute;es ont bien &eacute;t&eacute; modifi&eacute;es',
	'version_a_jour' => '<NEW>Votre version est &agrave; jour.',
	'version_distante' => '<NEW>Version distante...',
	'version_distante_off' => '<NEW>V&eacute;rification distante d&eacute;sactiv&eacute;e',
	'version_nouvelle' => '<NEW>Nouvelle version : @version@',
	'version_revision' => '<NEW>R&eacute;vision : @revision@',
	'version_update' => '<NEW>Mise &agrave; jour automatique',
	'version_update_chargeur' => '<NEW>T&eacute;l&eacute;chargement automatique',
	'version_update_chargeur_title' => '<NEW>T&eacute;l&eacute;charge la derni&egrave;re version du plugin gr&acirc;ce au plugin &laquo;T&eacute;l&eacute;chargeur&raquo;',
	'version_update_title' => '<NEW>T&eacute;l&eacute;charge la derni&egrave;re version du plugin et lance sa mise &agrave; jour automatique',
	'verstexte:description' => '2 filtros pa les tos cadarmes, que permiten de producir p&aacute;xines m&aacute;s lixeres.
_ version_texte : estr&aacute;i el conten&iacute;u de testu d\'una p&aacute;xina html escluyendo delles etiquetes elementales.
_ version_plein_texte : estr&aacute;i el conten&iacute;u de testu d\'una p&aacute;xina html pa amosar el testu en bruto.',
	'verstexte:nom' => '<NEW>Version texte',
	'visiteurs_connectes:description' => '<NEW>Offre une noisette pour votre squelette qui affiche le nombre de visiteurs connect&eacute;s sur le site public.

Ajoutez simplement <code><INCLURE{fond=fonds/visiteurs_connectes}></code> dans vos pages.',
	'visiteurs_connectes:nom' => '<NEW>Visiteurs connect&eacute;s',
	'voir' => 'Ver: @voir@',
	'votre_choix' => '<NEW>Votre choix :',

	// W
	'webmestres:description' => '<NEW>Un {{webmestre}} au sens SPIP est un {{administrateur}} ayant acc&egrave;s &agrave; l\'espace FTP. Par d&eacute;faut et &agrave; partir de SPIP 2.0, il est l’administrateur <code>id_auteur=1</code> du site. Les webmestres ici d&eacute;finis ont le privil&egrave;ge de ne plus &ecirc;tre oblig&eacute;s de passer par FTP pour valider les op&eacute;rations sensibles du site, comme la mise &agrave; jour de la base de donn&eacute;es ou la restauration d&rsquo;un dump.

Webmestre(s) actuel(s) : {@_CS_LISTE_WEBMESTRES@}.
_ Administrateur(s) &eacute;ligible(s) : {@_CS_LISTE_ADMINS@}.

En tant que webmestre vous-m&ecirc;me, vous avez ici les droits de modifier cette liste d\'ids -- s&eacute;par&eacute;s par les deux points &laquo;&nbsp;:&nbsp;&raquo; s\'ils sont plusieurs. Exemple : &laquo;1:5:6&raquo;.[[%webmestres%]]',
	'webmestres:nom' => '<NEW>Liste des webmestres',

	// X
	'xml:description' => '<NEW>Active le validateur xml pour l\'espace public tel qu\'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html]. Un bouton intitul&eacute; &laquo;&nbsp;Analyse XML&nbsp;&raquo; est ajout&eacute; aux autres boutons d\'administration.',
	'xml:nom' => '<NEW>Validateur XML'
);

?>
