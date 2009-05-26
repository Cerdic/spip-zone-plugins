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
	'SPIP_liens:description1' => '@puce@ SPIP tien previstu un estilu CSS pa los enllaces &laquo;~mailto:&raquo;: un sobre peque&ntilde;u tendr&iacute;a que apaecer delantre de cada enllaz lligau a un corr&eacute;u; pero como hai &ntilde;avegadores que nun puen amosalo (notablemente IE6, IE7 y SAF3), t&uacute; decides si quies mantener esta carauter&iacute;stica.
_ Enllaz de preba: [->test@test.com] (recarga la p&aacute;xina pa prebar).[[%enveloppe_mails%]]',
	'SPIP_liens:nom' => 'SPIP y los enllaces… esternos',

	// A
	'acces_admin' => 'Accesu alministraores:',
	'action_rapide' => 'Aici&oacute;n r&aacute;pida, &iexcl;&uacute;nicamente si sabes lo que tas faciendo!',
	'action_rapide_non' => 'Aici&oacute;n r&aacute;pida, disponible de magar que actives esta ferramienta:',
	'admins_seuls' => 'Nam&aacute;i los alministradores',
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
 
 El testu a anubrir/amosar, tres dos saltos de llinia...
 </bloc>
</code></quote>

@puce@ {{Nes cadarmes}}: dispones de les nueves balices #BLOC_TITRE, #BLOC_DEBUT y #BLOC_FIN pa utilizar as&iacute;: 
<quote><code> #BLOC_TITRE o #BLOC_TITRE{mio_URL}
 Mio t&iacute;tulu
 #BLOC_RESUME    (opcional)
 una versi&oacute;n en resume del bloque siguiente
 #BLOC_DEBUT
 El mio bloque estenderexable (que contendr&aacute; la URL a la que apunta si fai falta)
 #BLOC_FIN</code></quote>

@puce@ Si marques &laquo;si&raquo; embaxo, l\'apertura d\'un bloque provocar&aacute; que se zarren tolos dem&aacute;s bloques de la p&aacute;xina, col env&iacute;s de nun tener m&aacute;s qu\'un solu abiertu a la vez.[[%bloc_unique%]]

@puce@ si marques &laquo;s&iacute;&raquo; embaxo, l\'est&aacute;u de los bloques numberaos atroxarase nuna cookie demientres la sesion, pa conservar l\'aspeutu de la p&aacute;xina en casu de volver.[[%blocs_cookie%]]

@puce@ La Navaya Suiza utiliza por omisi&oacute;n la etiqueta HTML &lt;h4&gt; pal t&iacute;tulu de los bloques estenderexables. Equ&iacute; pue escoyese otra etiqueta &lt;hN&gt;:&nbsp;[[%bloc_h4%]]',
	'blocs:nom' => 'Bloques Desplegables',
	'boites_privees:description' => 'Toes les caxes descrites embaxo apaecen per dayuri na parte privada.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]][[->%bp_urls_propres%]][[->%bp_tri_auteurs%]]
- {{Les revisiones de La Navaya Suiza}}: un cuadru na presente p&aacute;xina de configuraci&oacute;n, que indica les caberes modificaciones amest&aacute;es al c&oacute;digu del plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Los art&iacute;culos en formatu SPIP}}: un cuadru estenderexable suplementariu pa los art&iacute;culos, col env&iacute;s de saber el c&oacute;digu fonte utilizao polos autores.
- {{Estad&iacute;stiques de los autores}}: un cuadru estenderexable suplementariu na [p&aacute;xina de los autores->./?exec=auteurs] que amuesa los caberos 10 coneutaos y les inscripciones nun confirm&aacute;es. S&oacute;lo los alministradores ven esta informaci&oacute;n.
- {{Los webmasters SPIP}}: un cuadru estenderexable na [p&aacute;xina de los autores->./?exec=auteurs] que amuesa los alministradores elevaos al rangu de webmaster SPIP. S&oacute;lo los alministradores puen ver etas informaci&oacute;n. Si yes webmaster t&uacute; mesmu, mira tambi&eacute;n la ferramienta &laquo;[.->webmasters]&raquo;.
- {{Les URLs propies}}: un cuadru estenderexable pa cada oxetu de conten&iacute;u (art&iacute;culu, estaya, autor, ...) que indica la URL propia asociada igual que los eventuales nomatos. La ferramienta &laquo;[.->type_urls]&raquo; te permite l\'axuste finu de les URLs del to sitiu.
- {{L\'orde d\'autores}}: un cuadru estenderexable pa los art&iacute;culos que tengan m&aacute;s d\'un autor y que permite axustar facilmente l\'orde en que s\'amuesen.',
	'boites_privees:nom' => 'Caxes privaes',
	'bp_tri_auteurs' => 'Ordenaciones d\'autores',
	'bp_urls_propres' => 'Les URLs propies',
	'brouteur:description' => 'Utilizar el selector d\'estaya n\'AJAX a partir de %rubrique_brouteur% estaya(es)',
	'brouteur:nom' => 'Axuste del selector d\'estaya',

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
	'chatons:description' => 'Inxerta imaxes (o emoticonos pa los {chats}) en tolos testos nos que apaeza una cadena de tipu <code>:nome</code>.
_ Esta ferramienta camuda esos atayos poles imaxes del mesmu nome qu\'alcuentre nel direutoriu <code>mon_squelette_toto/img/chatons/</code> o, por omisi&oacute;n, nel direutoriu <code>couteau_suisse/img/chatons/</code>.',
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

 Lo mesmo puedes definir el c&oacute;digu d\'apertura y zarre pa les llam&aacute;es a notes de pie de p&aacute;xina (&iexcl;Atenci&oacute;n! Les modificaciones nun van vese m&aacute;s que nel espaciu p&uacute;blicu.): [[%ouvre_ref%]][[->%ferme_ref%]]
 
 Puedes definir el c&oacute;digu d\'apertura y zarre pa les notes de pie de p&aacute;xina: [[%ouvre_note%]][[->%ferme_note%]]

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
	'cs_reset2' => 'Toles ferramientes actualmente actives van desactivase y van reaniciase sos par&aacute;metros.',
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
	'en_travaux:description' => 'Permite amosar un mensaxe personalizable, demientres una fase de mantenimientu, en tou el sitiu p&uacute;blicu y, eventualmente na parte privada.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]][[%prive_travaux%]]',
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
	'filets_sep:aide' => 'Moldures de Dixebra: <b>__i__</b> onde <b>i</b> ye un n&uacute;mberu.<br />Otres moldures disponibles: @liste@',
	'filets_sep:description' => 'Amesta moldures de dixebra, personalizables con les fueyes d\'estilu, en tolos testos de SPIP.
_ La sintaxis ye: "__code__", onde "code" representa o el n&uacute;mberu d’identificaci&oacute;n (de 0 &agrave; 7) de la moldura a amestar en relaci&oacute;n direuta colos estilos correspondientes, o el nome d\'una imaxe allugada nel direutoriu plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Moldures de Dixebra',
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
	'horloge:description' => 'Ferramienta en cursu de desendolcu. Ufre un rel&oacute; JavaScript . Baliza: <code>#HORLOGE{format,utc,id}</code>. Modelu: <code><horloge></code>',
	'horloge:nom' => 'Rel&oacute;',

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
	'label:bloc_h4' => 'Etiqueta HTML pa los t&iacute;tulos:',
	'label:bloc_unique' => 'Solo un bloque abiertu na p&aacute;xina:',
	'label:blocs_cookie' => 'Utilizaci&oacute;n de cookies:',
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
	'label:enveloppe_mails' => 'Sobre peque&ntilde;u delantre los correos:',
	'label:expo_bofbof' => 'Escribir como exponentes: <html>St(e)(s), Bx, Bd(s) y Fb(s)</html>',
	'label:forum_lgrmaxi' => 'Valor (en carauteres):',
	'label:glossaire_groupes' => 'Grupu(os) utilizao(s):',
	'label:glossaire_js' => 'T&eacute;unica utilizada:',
	'label:glossaire_limite' => 'N&uacute;mberu m&aacute;simu d\'enllaces creaos:',
	'label:insertions' => 'Correiciones autom&aacute;tiques:',
	'label:jcorner_classes' => 'Meyorar los requexos de les seleiciones siguientes:',
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
	'label:ouvre_note' => 'Apertura y zarre de les notes de pie de p&aacute;xina',
	'label:ouvre_ref' => 'Apertura y zarre de les llam&aacute;es a notes de pie de p&aacute;xina',
	'label:paragrapher' => 'Facer p&aacute;rrafos siempre:',
	'label:prive_travaux' => 'Accesibilid&aacute; de l\'espaciu priv&aacute;u por:',
	'label:puce' => 'Marca p&uacute;blica &laquo;<html>-</html>&raquo;:',
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
	'label:urls_avec_id' => 'Una id sistem&aacute;tica, sicas&iacute;...',
	'label:urls_minuscules' => '@_CS_CHOIX@',
	'label:webmestres' => 'Llista de los webmasters del sitiu:',
	'liens_en_clair:description' => 'Ponte a disposici&oacute;n el filtru: \'liens_en_clair\'. El testu probablemente tien enllaces d\'hipertestu que nun son visibles al imprentar. Esti filtru amesta ente corchetes el dest&iacute;n de cada enllaz calcable (enllaces esternos o mails). Atenci&oacute;n: nel mou impresi&oacute;n (par&aacute;metru \'cs=print\' o \'page=print\' na URL de la p&aacute;xina), esti funcionamientu apl&iacute;case autom&aacute;ticamente.',
	'liens_en_clair:nom' => 'Enllaces en claro',
	'liens_orphelins:description' => 'Esta ferramienta tien dos funciones:

@puce@ {{Enllaces correutos}}.

SPIP tien el vezu d\'inxertar un espaciu enantes de los signos d\'interrogaci&oacute;n o d\'esclamaci&oacute;n y camudar el gui&oacute;n doble en cuadr&aacute;u, como manda la tipograf&iacute;a francesa. Pero esto afeuta a les URL de los testos. Esta ferramienta permite protexeles.[[%liens_interrogation%]]

@puce@ {{Enllaces g&uuml;&eacute;rfanos}}.

Camuda sistem&aacute;ticamente toles URLs puestes como testu polos usuarios (especialmente nos foros) y que nun son poro calcables, por enllaces d\'hipertestu en formatu SPIP. Por exemplu: {<html>www.spip.net</html>} reempl&aacute;zase por [->www.spip.net].

Pues escoyer el tipu de reemplazu:
_ • {B&aacute;sicu}: cam&uacute;dense los enllaces del tipu {<html>http://spip.net</html>} (tolos protocolos) o {<html>www.spip.net</html>}.
_ • {Estend&iacute;u} : cam&uacute;dense am&aacute;s los enllaces del tipu {<html>usuariu@spip.net</html>}, {<html>mailto:miomail</html>} o {<html>news:miosnews</html>}.
_ • {Predetermin&aacute;u}: reemplazu autom&aacute;ticu d\'orixe (a partir de la versi&oacute;n 2.0 de SPIP).
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'URLs guapes',

	// M
	'mailcrypt:description' => 'Mazcarita toos los enllaces de corr&eacute;u presentes nos testos y los camuda por un enllaz JavaScript que permite lo mesmo activar la mensaxer&iacute;a del llector. Esta ferramienta escontra\'l corr&eacute;u puxarra tenta torgar que los robots collechen les se&ntilde;es electr&oacute;niques escrites en claro nos foros o nes balices de les tos cadarmes.',
	'mailcrypt:nom' => 'MailCrypt',
	'message_perso' => 'Candiales gracies a los traductores que pasaren per equ&iacute;. Pat ;-)',
	'moderation_admins' => 'alministradores autentificaos',
	'moderation_message' => 'Esti foru ta llend&aacute;u a priori: lo que vienes de mandar nun apaecer&aacute; hasta que tea valid&aacute;u por un alministrador del sitiu, a menos que teas identific&aacute;u y autoriz&aacute;u a escribir direutamente.',
	'moderation_moderee:description' => 'Permite llendar el llend&aacute;u de los foros p&uacute;blicos <b>configuraos a priori</b> polos usuarios inscritos.<br />Exemplu: Si yo soy el webmaster del mio sitiu, y respondo a un mensaxe d\'un usuariu, &iquest;por qu&eacute; tengo que validame el mio propiu mensaxe? &iexcl;El llendamientu llend&aacute;u failo pa m&iacute;! [[%moderation_admin%]][[-->%moderation_redac%]][[-->%moderation_visit%]]',
	'moderation_moderee:nom' => 'Llendamientu llend&aacute;u',
	'moderation_redacs' => 'redactores autentificaos',
	'moderation_visits' => 'visitantes autentificaos',
	'modifier_vars' => 'Camudar estos @nb@ par&aacute;metros',
	'modifier_vars_0' => 'Camudar esto par&aacute;metros',

	// N
	'no_IP:description' => 'Desactiva el mecanismu de grabaci&oacute;n autom&aacute;tica de les se&ntilde;es IP de los visitantes del sitiu pa mantener la confidencialid&aacute;: SPIP ya nun conservar&aacute; deng&uacute;n n&uacute;mberu IP, nin temporalmente durante les visites (pa remanar les estad&iacute;stiques o alimentar spip.log), nin pa los foros (responsabilid&aacute;).',
	'no_IP:nom' => 'Ensin guardar la IP',
	'nouveaux' => 'Nuevos',

	// O
	'orientation:description' => '3 nuevos criterios pa les cadarmes: <code>{portrait}</code> (retratu), <code>{carre}</code> (cuadr&aacute;u) y <code>{paysage}</code> (paisaxe). Ideal pa la clasificaci&oacute;n de les fotos en funci&oacute;n de la so forma.',
	'orientation:nom' => 'Orientaci&oacute;n de les imaxes',
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
	'outils_non_parametrables' => 'Ensin variables:',
	'outils_permuter_gras1' => 'Camudar les ferramientes en negrines',
	'outils_permuter_gras2' => '&iquest;Camudar les @nb@ ferramientes en negrines?',
	'outils_resetselection' => 'Reaniciar la seleici&oacute;n',
	'outils_selectionactifs' => 'Seleicionar toles ferramientes actives',
	'outils_selectiontous' => 'TOES',

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
	'pack_installer' => '&iquest;Tas seguru de que quies reinicializar la Navaya Suiza e instalar el paquete &laquo;&nbsp;@pack@&nbsp;&raquo;?',
	'pack_nb_plrs' => 'Actualmente hai @nb@ &laquo;&nbsp;paquetes de configuraci&oacute;n&nbsp;&raquo; disponibles.',
	'pack_nb_un' => 'Actualmente hai un &laquo;&nbsp;paquete de configuraci&oacute;n&nbsp;&raquo; disponible',
	'pack_nb_zero' => 'Nun hai deng&uacute;n &laquo;&nbsp;paquete de configuraci&oacute;n&nbsp;&raquo; disponible actualmente.',
	'pack_outils_defaut' => 'Instalaci&oacute;n de les ferramientes por omisi&oacute;n',
	'pack_sauver' => 'Guardar la configuraci&oacute;n actual',
	'pack_sauver_descrip' => 'El bot&oacute;n d&eacute;mbaxo te permite enxertar direutamente nel archivu <b>@file@</b> los par&aacute;metros necesarios pa amesta-y un &laquo;&nbsp;paquete de configuraci&oacute;n&nbsp;&raquo; al men&uacute; de la izquierda. Esto va permitite posteriormente tornar nun clic la Navaya Suiza a l\'est&aacute;u nel que ta actualmente.',
	'pack_titre' => 'Configuraci&oacute;n Actual',
	'pack_variables_defaut' => 'Instalaci&oacute;n de les variables por omisi&oacute;n',
	'par_defaut' => 'Por omisi&oacute;n',
	'paragrapher2:description' => 'La funci&oacute;n de SPIP <code>paragrapher()</code> amesta-yos balices &lt;p&gt; y &lt;/p&gt; a tolos testos que nun tengan p&aacute;rrafos. A la fin d\'iguar m&aacute;s finamente los estilos y les paxinaciones, tienes la posibilid&aacute; d\'uniformizar l\'aspeutu de los testos del sitiu Web.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Amestar p&aacute;rrafos',
	'pipelines' => 'Tuber&iacute;es (pipelines) utiliz&aacute;es:',
	'pucesli:description' => 'Reemplaza les marques &laquo;-&raquo; (gui&oacute;n simple) de los art&iacute;culos por llistes anot&aacute;es &laquo;-*&raquo; (traduc&iacute;es en HTML como: &lt;ul>&lt;li>…&lt;/li>&lt;/ul>) nes que l\'estilu pue personalizase con css.',
	'pucesli:nom' => 'Marques guapes',

	// Q
	'qui_webmestres' => 'Los webmasters SPIP',

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
	'set_options:description' => 'Seleiciona d\'oficiu el tipu d’interfaz privada (simplificada o avanzada) pa tolos redactores esistentes o futuros y desanicia el bot&oacute;n correspondiente na barra d\'iconos amenorgaos.[[%radio_set_options4%]]',
	'set_options:nom' => 'Tipu d\'interfaz privada',
	'sf_amont' => 'Enriba',
	'sf_tous' => 'Toos',
	'simpl_interface:description' => 'Desactiva el men&uacute; de camb&eacute;u r&aacute;pidu d\'estatutu d\'un art&iacute;culu al pasar pola so marca de color. Esto ye afayadizo si busques tener una interfaz privada lo m&aacute;s austera posible col env&iacute;s d\'optimizar les prestaciones nel cliente.',
	'simpl_interface:nom' => 'Allixeramientu de la interfaz privada',
	'smileys:aide' => 'Smileys: @liste@',
	'smileys:description' => 'Enxerta smileys en toos los testos nos que apaeza un atayu de tipu <acronym>:-)</acronym>. Ideal pa los  foros.
_ Ta disponible una baliza pa amosar una tabla de smileys nes cadarmes : #SMILEYS.
_ Dise&ntilde;u d\'iconos: [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Smileys',
	'soft_scroller:description' => 'Ufre-y al sitiu p&uacute;blicu un desplazamientu sele de la p&aacute;xina cuando un visitante calca nun enllaz que apunta pa un ancla: mui afayadizo pa evitar perdese nuna p&aacute;xina complexa o nun testu mui llargu...

Atenci&oacute;n, pa que esta ferramienta funcione necesita p&aacute;xines en &laquo;DOCTYPE XHTML&raquo; (&iexcl;non HTML!) dos plugins {jQuery}: {ScrollTo} y {LocalScroll}. La Navaya Suiza pue instalalos direutamente si marques los cuadros siguientes. [[%scrollTo%]][[-->%LocalScroll%]]
@_CS_PLUGIN_JQUERY192@',
	'soft_scroller:nom' => 'Ancles seles',
	'sommaire:description' => 'Construi un sumariu pal testu de los art&iacute;culos y de les estayes a la fin d’acceder r&aacute;pidamente a los t&iacute;tulos destac&aacute;os (etiquetes HTML &lt;h3>Un intert&iacute;tulu&lt;/h3> o atayos SPIP: intert&iacute;tulos na forma: <code>{{{Un titular}}}</code>).

@puce@ Equ&iacute; vas poder conse&ntilde;ar el n&uacute;mberu m&aacute;simu de caraut&egrave;res tom&aacute;os de los intert&iacute;tulos pa construir el sumariu:[[%lgr_sommaire% caraut&egrave;res]]

@puce@ Tami&eacute;n pues axustar el comportamientu del plugin tocante a la creaci&oacute;n del sumariu: 
_ • Sistem&aacute;ticu pa cada art&iacute;culu (una baliza <code>@_CS_SANS_SOMMAIRE@</code> puesta n’ayuri dientro\'l testu de l’art&iacute;culu crear&aacute; una esceici&oacute;n).
_ • &Uacute;nicamente pa los art&iacute;culos que tengan la baliza <code>@_CS_AVEC_SOMMAIRE@</code>.

[[%auto_sommaire%]]

@puce@ Por omisi&oacute;n, la Navaya Suiza enxerta el sumariu en cabeza de l\'art&iacute;culu autom&aacute;ticamente. Pero tienes la posibilida d\'allugar esti sumariu ayuri na cadarma gracies a una baliza #CS_SOMMAIRE que pues activar equ&iacute;:
[[%balise_sommaire%]]

Esti sumariu pue acoplase con: &laquo;&nbsp;[.->decoupe]&nbsp;&raquo;.',
	'sommaire:nom' => 'Un sumariu autom&aacute;ticu',
	'sommaire_avec' => 'Un testu con sumariu: <b>@_CS_AVEC_SOMMAIRE@</b>',
	'sommaire_sans' => 'Un testu ensin sumariu: <b>@_CS_SANS_SOMMAIRE@</b>',
	'spam:description' => 'Tenta lluchar escontra los unv&iacute;os de mensaxes autom&aacute;ticos y gafientos na parte p&uacute;blica. Delles pallabres, igual que les balices en claro &lt;a>&lt;/a>, tan torg&aacute;es: encamienta a los redactores a usar los atayos pa enllaces de SPIP.

Llista equ&iacute; les secuencies torg&aacute;es separtandoles con espacios. [[%spam_mots%]]
• Pa una espresi&oacute;n con espacios, ponla ente comines.
_ • Pa especificar una pallabra entera, m&eacute;tela ente par&eacute;ntesis. Exemplu:~{(premiu)}.
_ • Pa una espresi&oacute;n regular, verifica bien la sintaxis y ponla dientro de barres y comines. Exemplu:~{<html>"/@test\\.(com|org|ast)/"</html>}.',
	'spam:nom' => 'Llucha escontra la puxarra',
	'spam_test_ko' => '&iexcl;Esti mensaxe bloquiarase pol filtru anti-SPAM!',
	'spam_test_ok' => 'Esti mensaxe aceutarase pol filtru anti-SPAM.',
	'spam_tester' => '&iexcl;Entamar la preba!',
	'spam_tester_label' => 'Preba equ&iacute; la llista de secuencies torg&aacute;es:',
	'spip_cache:description' => '@puce@ La cach&eacute; ocupa ciertu espaciu en discu y SPIP puede limitar la cantid&aacute;. Un valor vac&iacute;u o igual a 0 significa que nun s\'aplica cuota denguna.[[%quota_cache% Mb]]

@puce@ Cuando se fai una modificaci&oacute;n del conten&iacute;u del sitiu, SPIP invalida inmediatamente la cach&eacute; ensin esperar al siguiente c&aacute;lculu peri&oacute;dicu. Si el sitiu tien problemes de rendimientu por cuenta d\'una gran carga, puedes marcar &laquo;&nbsp;non&nbsp;&raquo; n\'esta opci&oacute;n.[[%derniere_modif_invalide%]]

@puce@ Si la baliza #CACHE nun s\'alcuentra nes tos cadarmes llocales, SPIP considera por omisi&oacute;n que la cach&eacute; d\'una p&aacute;xina tien una vida m&aacute;sima de 24 hores enantes de volver a calculala. A la fin de xestionar meyor la carga del to sirvidor, puedes cambear equ&iacute; esti valor.[[%duree_cache% hores]]

@puce@ Si tienes dellos sitios en mutualizaci&oacute;n, puedes especificar equ&iacute; el valor por omisi&oacute;n que se toma pa toos los sitios llocales (SPIP 2.0 mini).[[%duree_cache_mutu% hores]]',
	'spip_cache:description1' => '@puce@ Por omisi&oacute;n, SPIP calcula toles p&aacute;xines p&uacute;bliques y ponles na cach&eacute; a la fin d\'acelerar la consulta. Desactivar temporalmente la cach&eacute; pue aidar mientres se desarrolla el sitiu. @_CS_CACHE_EXTENSION@[[%radio_desactive_cache3%]]',
	'spip_cache:description2' => '@puce@ Cuatro opciones pa tresnar el funcionamientu de la cach&eacute; de SPIP: <q1>
_ • {Usu normal}: SPIP calcula toles p&aacute;xines p&uacute;bliques y les pon na cach&eacute; a la fin d\'acelerar la consulta. Tres d\'un ciertu plazu, la cach&eacute; vuelve a calculase y gu&aacute;rdase.
_ • {Cach&eacute; permanente}: los plazos d\'anovaci&oacute;n de la cach&eacute; inorense.
_ • {Ensin cach&eacute;}: desactivar temporalmente la cach&eacute; pue aidar nel desarrollo del sitiu. Equ&iacute;, nada nun se guarda nel discu.
_ • {Control de cach&eacute;}: opci&oacute;n identica a la precedente, con escritura nel discu de tolos resultaos a la fin de podelos controlar si fai falta.</q1>[[%radio_desactive_cache4%]]',
	'spip_cache:nom' => 'SPIP y la cach&eacute;…',
	'stat_auteurs' => 'Autores por estatutu',
	'statuts_spip' => '&Uacute;nicamente los estatutos SPIP siguientes:',
	'statuts_tous' => 'Tolos estatutos',
	'suivi_forums:description' => 'Al autor d\'un art&iacute;culu siempre se-y informa al espublizase un mensaxe nel foru p&uacute;blicu asoci&aacute;u. Pero am&aacute;s ye posible avisar dafechu: a tolos participantes del foru o s&oacute;lo a los autores de mensaxes d\'enriba.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Siguir foros p&uacute;blicos',
	'supprimer_cadre' => 'Desaniciar esti cuadru',
	'supprimer_numero:description' => 'Aplica la funci&oacute;n de SPIP supprimer_numero() al conxuntu de {{t&iacute;tulos}}, de {{nomes}} y de {{tipos}} (de pallabres-clave) del sitiu p&uacute;blicu, ensin que\'l filtru supprimer_numero tea presente nes cadarmes.<br />Esta ye la sintaxis a utilizar nel contestu d\'un sitiu multill&iacute;ng&uuml;e: <code>1. <multi>My Title[fr]Mon Titre[ast]Mio T&iacute;tulu</multi></code>',
	'supprimer_numero:nom' => 'Suprime\'l n&uacute;mberu',

	// T
	'titre' => 'La Navaya Suiza',
	'titre_parent:description' => 'Dientro d\'un bucle, ye corriente que se quiera amosar el t&iacute;tulu del padre de l\'oxetu en cursu. Tradicionalmente, hab&iacute;a que utilizar un segundu bucle, pero esta nueva baliza #TITRE_PARENT va allixerar la escritura de les cadarmes. El result&aacute;u devueltu ye: el t&iacute;tulu del grupu d\'una pallabra-clave o el de la estaya padre (si esiste) de los dem&aacute;s oxetos (art&iacute;culu, estaya, breve, etc.).

Nota: Pa les pallabres-clave, un alias de #TITRE_PARENT ye #TITRE_GROUPE. El tratamientu por SPIP d\'estes nueves balices ye asemey&aacute;u al de #TITRE.

@puce@ Si tas con SPIP 2.0, dispones equ&iacute; de tou un conxuntu de balices #TITRE_XXX que pueden date\'l t&iacute;tulu de l\'oxetu \'xxx\', cola condici&oacute;n que\'l campu \'id_xxx\' tea presente na tabla en cursu (#ID_XXX utilizable nel bucle en cursu).

Por exemplu, nun bucle pa (ARTICLES), #TITRE_SECTEUR dar&aacute; el t&iacute;tulu del sector nel que ta allug&aacute;u l\'art&iacute;culu en cursu, porque l\'identificador #ID_SECTEUR (o el campu \'id_secteur\') ta disponible nesti casu.

La sintaxis <html>#TITRE_XXX{yy}</html> sop&oacute;rtase igualmente. Exemplu: <html>#TITRE_ARTICLE{10}</html> devolver&aacute; el t&iacute;tulu de l\'art&iacute;culu #10.[[%titres_etendus%]]',
	'titre_parent:nom' => 'Balices #TITRE_PARENT/OBJET',
	'titre_tests' => 'La Navaya Suiza - P&aacute;xina de prebes…',
	'tous' => 'Toos',
	'toutes_couleurs' => 'Los 36 colores de los estilos css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Bloques multilling&uuml;es: <b><:trad:></b>',
	'toutmulti:description' => 'Del mesmu mou que ya pod&iacute;es facelo nes tos cadarmes, esta ferramienta te permite utilizar llibremente les cadenes de lling&uuml;es (de SPIP o de les cadarmes) nel conten&iacute;u ensembre del sitiu (art&iacute;culos, t&iacute;tulos, mensaxes, etc.) con l\'aida de l\'atayu <code><:cadena:></code>.

Consulta [equ&iacute; ->http://www.spip.net/fr_article2128.html] la documentaci&oacute;n de SPIP pa esti asuntu.

Esta ferramienta acepta igualmente los argumentos que apaecieron con SPIP 2.0. Por exemplu, l\'atayu <code><:mio_cadena{nome=Charles Martin, eda=37}:></code> permite pasa-y dos par&aacute;metros a la siguiente cadena: <code>\'mio_cadena\'=>"Bones, soi @nome@ y tengo @eda@ a&ntilde;os\\"</code>.

La funci&oacute;n SPIP usada en PHP ye <code>_T(\'cadena\')</code> ensin argumentu, y <code>_T(\'cadena\', array(\'arg1\'=>\'un testu\', \'arg2\'=>\'otru testu\'))</code> con argumentos.

 Nun t\'escaezas de verificar que la clave <code>\'cadena\'</code> tea bien definida nos archivos de les lling&uuml;es.',
	'toutmulti:nom' => 'Bloques multilling&uuml;es',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Esti sitiu volver&aacute; a tar en llinia pronto.
_ Agradec&eacute;moste la comprensi&oacute;n.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => 'Al &ntilde;avegar pola parte privada del sitiu ([->./?exec=auteurs]), equ&iacute; escueyes la ordenaci&oacute;n a utilizar pa amosar los art&iacute;culos dientro de les estayes.

Les propuestes d\'embaxo b&aacute;sense na funci&oacute;n SQL \'ORDER BY\': nun utilices l\'orde personaliz&aacute;u m&aacute;s que si sabes lo que tas faciendo (campos disponibles: {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => 'Orde de los art&iacute;culos',
	'tri_modif' => 'Guetar pola fecha d\'igua (ORDER BY date_modif DESC)',
	'tri_perso' => 'Gueta SQL personalizada, ORDER BY sigu&iacute;o por:',
	'tri_publi' => 'Guetar pola fecha d\'espublizamientu (ORDER BY date DESC)',
	'tri_titre' => 'Guetar pol t&iacute;tulu (ORDER BY 0+titre,titre)',
	'trousse_balises:description' => 'Ferramienta en cursu de desendolcu. Ufre delles balices mui cencielles y enforma pr&aacute;ctiques pa les cadarmes.

@puce@ {{#BOLO}}: xenera un testu falsu d\'unos 3000 carauteres ("bolo" o "[?lorem ipsum]") nes cadarmes enantes de poneles nel so llugar. L\'argumentu opcional d\'esta funci&oacute;n conse&ntilde;a el llargor que se quier pal testu. Exemplu: <code>#BOLO{300}</code>. Esta baliza acepta toles pe&ntilde;eres de SPIP. Exemplu: <code>[(#BOLO|majuscules)]</code>.
_ Tami&eacute;n hai disponible un modelu pa los conten&iacute;os: pon <code><bolo300></code> en cualquier zona de testu (cabecera, descripci&oacute;n, testu, etc.) pa tener 300 carauteres de testu falsu.

@puce@ {{#MAINTENANT}} (o {{#NOW}}): devuelve simplemente la data del momentu, igual que: <code>#EVAL{date(\'Y-m-d H:i:s\')}</code>. L\'argumentu opcional d\'esta funci&oacute;n afita\'l formatu. Exemplu: <code>#MAINTENANT{Y-m-d}</code>. Como con #DATE, personaliza l\'aspeutu gracies a les pe&ntilde;eres de SPIP. Exemplu: <code>[(#MAINTENANT|affdate)]</code>.

- {{#CHR<html>{XX}</html>}}: baliza equivalente a <code>#EVAL{"chr(XX)"}</code> ye afayadiza pa conse&ntilde;ar carauteres especiales (el saltu de llinia por exemplu) o carauteres reservaoss pol compilador de SPIP (los corchetes o les llaves).

@puce@ {{#LESMOTS}}: ',
	'trousse_balises:nom' => 'Cax&oacute;n de balices',
	'type_urls:description' => '@puce@ SPIP ufre una esbilla de xuegos d\'URLs pa fabricar los enllaces d\'accesu a les p&aacute;xines del sitiu Web.

M&aacute;s info: [->http://www.spip.net/fr_article765.html]. La ferramienta &laquo;&nbsp;[.->boites_privees]&nbsp;&raquo; te permite ver na p&aacute;xina de cada oxetu SPIP la URL propia asociada.
[[%radio_type_urls3%]]
<q3>@_CS_ASTER@pa utilizar los formatos {html}, {propies}, {propies2}, {llibres} o {arborescentes}, copia l\'archivu "htaccess.txt" del direutoriu base del sitiu SPIP col nome ".htaccess" (atenci&oacute;n pa nun esborrar otros axustes que pudieras tener conse&ntilde;aos nesti archivu); si el to sitiu ta nun "sub-direutoriu", has d\'iguar tambi&eacute;n la llinia "RewriteBase" nel archivu. Les URLs definies van redirixise agora a los archivos de SPIP.</q3>

<radio_type_urls3 valeur="page">@puce@ {{URLs &laquo;p&aacute;xina&raquo;}}: son los enllaces predetermin&aacute;os, que usa SPIP dende la so versi&oacute;n 1.9x.
_ Exemplu: <code>/spip.php?article123</code>[[%terminaison_urls_page%]][[%separateur_urls_page%]]</radio_type_urls3>

<radio_type_urls3 valeur="html">@puce@ {{URLs &laquo;html&raquo;}}: los enllaces tienen forma de p&aacute;xines html cl&aacute;siques.
_ Exemplu: <code>/article123.html</code></radio_type_urls3>

<radio_type_urls3 valeur="propres">@puce@ {{URLs &laquo;propies&raquo;}}: los enllaces calc&uacute;lense graciee al t&iacute;tulu de los oxetos pid&iacute;os. Les marques (_, -, +, @, etc.) cuadren los t&iacute;tulos en funci&oacute;n del tipu d\'oxetu.
_ Exemplos : <code>/Mio-titulu-d-art&iacute;culu</code> o <code>/-Mio-estaya-</code> o <code>/@Mio-sitiu@</code>[[%terminaison_urls_propres%]][[%debut_urls_propres%]][[%marqueurs_urls_propres%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres2">@puce@ {{URLs &laquo;propies2&raquo;}}: la\'estensi&oacute;n \'.html\' am&eacute;stase a los enllaces {&laquo;propios&raquo;}.
_ Exemplu: <code>/Mio-titulu-d-art&iacute;culu.html</code> o <code>/-Mio-estaya-.html</code>
[[%debut_urls_propres2%]][[%marqueurs_urls_propres2%]]</radio_type_urls3>

<radio_type_urls3 valeur="libres">@puce@ {{URLs &laquo;llibres&raquo;}}: los enllaces son {&laquo;propios&raquo;}, pero ensin marcadores pa dixebrar los oxetos (_, -, +, @, etc.).
_ Exemplu: <code>/Mio-titulu-d-art&iacute;culu</code> o <code>/Mio-estaya</code>
[[%terminaison_urls_libres%]][[%debut_urls_libres%]]</radio_type_urls3>

<radio_type_urls3 valeur="arbo">@puce@ {{URLs &laquo;arborescentes&raquo;}}: los enllaces son {&laquo;propios&raquo;}, pero de tipu arborescente.
_ Exemplu: <code>/sector/estaya1/estaya2/Mio-titulu-d-art&iacute;culu</code>
[[%url_arbo_minuscules%]][[%urls_arbo_sans_type%]][[%url_arbo_sep_id%]][[%terminaison_urls_arbo%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres-qs">@puce@ {{URLs &laquo;propies-qs&raquo;}}: esti sistema funciona en "Query-String", ye dicir, ensin utilizar .htaccess ; los enllaces son {&laquo;propios&raquo;}.
_ Exemplu: <code>/?Mio-t&iacute;tulu-d-art&iacute;culu</code>
[[%terminaison_urls_propres_qs%]]</radio_type_urls3>

<radio_type_urls3 valeur="propres_qs">@puce@ {{URLs &laquo;propies_qs&raquo;}}: esti sist&egrave;ma funciona en "Query-String", esto ye, ensin utilizaci&oacute;n de .htaccess; los enllaces son {&laquo;propios&raquo;}.
_ Exemplu: <code>/?Mio-t&iacute;tulu-d-art&iacute;culu</code>
[[%terminaison_urls_propres_qs%]][[%marqueurs_urls_propres_qs%]]</radio_type_urls3>

<radio_type_urls3 valeur="standard">@puce@ {{URLs &laquo;estandar&raquo;}}: estos enllaces agora obsoletos utiliz&aacute;bense por SPIP hasta la so versi&oacute;n 1.8.
_ Exemplu: <code>article.php3?id_article=123</code>
</radio_type_urls3>

@puce@ Si utilizes el formatu {page} d\'embaxo o si l\'oxetu solicit&aacute;u nun se reconocer&aacute;, pero ye posible escoyer {{el script de llamada}} a SPIP. Por omisi&oacute;n, SPIP escueye {spip.php}, pero {index.php} (exemplu de formatu: <code>/index.php?article123</code>) donde un valor vac&iacute;u (formatu: <code>/?article123</code>) funciona tami&eacute;n. Pa cualquier otru valor, necesites crear dafechu l\'archivu correspondiente na raiz de SPIP, a imaxe del que ya esiste: {index.php}.
[[%spip_script%]]',
	'type_urls:description1' => '@puce@ Si utilices un formatu bas&aacute;u en URLs &laquo;propies&raquo; ({propres}, {propres2}, {libres}, {arborescentes} o {propres_qs}), la Navaya Suiza pue:
<q1>• Asegurase que la URL producida tea totalmente {{en min&uacute;scules}}.</q1>[[%urls_minuscules%]]
<q1>• Provocar l\'amestamientu sistem&aacute;ticu de {{la id de l\'oxetu}} a la URL (como sufixu, prefixu, etc.).
_ (exemplos: <code>/Mio-titulu-d-art&iacute;culu,457</code> o <code>/457-Mio-t&iacute;tulu-d-art&iacute;culu</code>)</q1>',
	'type_urls:nom' => 'Formatu de les URLs',
	'typo_exposants:description' => '{{Testos en franc&eacute;s}}: meyora la presentaci&oacute;n tipogr&aacute;fica de les abreviatures corrientes, escribiendo como esponente los elementos necesarios (as&iacute;, {<acronym>Mme</acronym>} tresf&oacute;rmase en {M<sup>me</sup>}) y corrixendo los fallos comunes ({<acronym>2&egrave;me</acronym>} o  {<acronym>2me</acronym>}, por exemplu, cam&uacute;dense en {2<sup>e</sup>}, &uacute;nica abreviatura correuta).

Les abreviatures obten&iacute;es son conformes coles de l\'Imprimerie nationale como les que s\'indiquen en el {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).

Ig&uuml;ense tambi&eacute;n les siguientes espresiones: <html>Dr, Pr, Mgr, m2, m3, Mn, Md, St&eacute;, &Eacute;ts, Vve, Cie, 1o, 2o, etc.</html>

Escueye equ&iacute; escribir como esponentes dellos atayos suplementarios, magar que l\'Imprimerie nationale lo tenga desaconseyao:[[%expo_bofbof%]]

{{Testos n\'ingl&eacute;s}}: escr&iacute;bense como esponente los n&uacute;mberos ordinales: <html>1st, 2nd</html>, etc.',
	'typo_exposants:nom' => 'Esponentes tipogr&aacute;ficos',

	// U
	'url_arbo' => 'arborescentes@_CS_ASTER@',
	'url_html' => 'html@_CS_ASTER@',
	'url_libres' => 'llibres@_CS_ASTER@',
	'url_page' => 'p&aacute;xina',
	'url_propres' => 'propies@_CS_ASTER@',
	'url_propres-qs' => 'propies-qs',
	'url_propres2' => 'propies2@_CS_ASTER@',
	'url_propres_qs' => 'propies_qs',
	'url_standard' => 'est&aacute;ndar',
	'urls_3_chiffres' => 'Imponer un m&iacute;nimu de 3 cifres',
	'urls_avec_id' => 'Ponela como sufixu',
	'urls_avec_id2' => 'Ponela como prefixu',
	'urls_base_total' => 'Actualmente hai @nb@ URL(s) na base',
	'urls_base_vide' => 'La base de les URLs ta vac&iacute;a',
	'urls_choix_objet' => 'Edici&oacute;n de la base de la URL d\'un oxetu espec&iacute;ficu:',
	'urls_edit_erreur' => 'El formatu actual de les URLs (&laquo;&nbsp;@type@&nbsp;&raquo;) nun permite la edici&oacute;n.',
	'urls_enregistrer' => 'Grabar esta URL na base',
	'urls_id_sauf_rubriques' => 'Encaboxar les estayes',
	'urls_minuscules' => 'Letres min&uacute;scules',
	'urls_nouvelle' => 'Editar la URL &laquo;propia&raquo;:',
	'urls_num_objet' => 'N&uacute;mberu:',
	'urls_purger' => 'Vacialo ensembre',
	'urls_purger_tables' => 'Vaciar les tables seleicion&aacute;es',
	'urls_purger_tout' => 'Reaniciar les URLs guard&aacute;es na base:',
	'urls_rechercher' => 'Restolar esti oxetu na base',
	'urls_titre_objet' => 'T&iacute;tulu grab&aacute;u:',
	'urls_type_objet' => 'Oxetu:',
	'urls_url_calculee' => 'URL p&uacute;blica &laquo;&nbsp;@type@&nbsp;&raquo;:',
	'urls_url_objet' => 'URL &laquo;propia&raquo; grabada:',
	'urls_valeur_vide' => '(Un valor vac&iacute;u produz el rec&aacute;lculu de la URL)',

	// V
	'validez_page' => 'Pa acceder a les modificaciones:',
	'variable_vide' => '(Vac&iacute;o)',
	'vars_modifiees' => 'Los datos modific&aacute;ronse bien',
	'version_a_jour' => 'Esta versi&oacute;n ta actualizada.',
	'version_distante' => 'Versi&oacute;n esterna...',
	'version_distante_off' => 'Verificaci&oacute;n esterna desactivada',
	'version_nouvelle' => 'Versi&oacute;n nueva: @version@',
	'version_revision' => 'Revisi&oacute;n: @revision@',
	'version_update' => 'Actualizaci&oacute;n autom&aacute;tica',
	'version_update_chargeur' => 'Descarga autom&aacute;tica',
	'version_update_chargeur_title' => 'Descarga la cabera versi&oacute;n del plugin gracies al plugin &laquo;Descargador&raquo;',
	'version_update_title' => 'Descarga la cabera versi&oacute;n del plugin y llanza la actualizaci&oacute;n autom&aacute;tica',
	'verstexte:description' => '2 filtros pa les tos cadarmes, que permiten de producir p&aacute;xines m&aacute;s lixeres.
_ version_texte : estr&aacute;i el conten&iacute;u de testu d\'una p&aacute;xina html escluyendo delles etiquetes elementales.
_ version_plein_texte : estr&aacute;i el conten&iacute;u de testu d\'una p&aacute;xina html pa amosar el testu en bruto.',
	'verstexte:nom' => 'Versi&oacute;n testu',
	'visiteurs_connectes:description' => 'Ufre una plizquina de c&oacute;digu pa la cadarma que amuesa el n&uacute;mberu de visites coneut&aacute;es col sitiu p&uacute;blicu.

Amesta-yos simplemente <code><INCLURE{fond=fonds/visiteurs_connectes}></code> a les tos p&aacute;xines.',
	'visiteurs_connectes:nom' => 'Visites coneut&aacute;es',
	'voir' => 'Ver: @voir@',
	'votre_choix' => 'Seleici&oacute;n:',

	// W
	'webmestres:description' => 'Un {{webmaster}} nel sen SPIP ye un {{alministrador}} que tien accesu a l\'espaciu FTP. Por omisi&oacute;n y dende SPIP 2.0, ye l’alministrador <code>id_auteur=1</code> del sitiu. Los webmasters conse&ntilde;&aacute;os equ&iacute;, tienen el privilexu de nun tar oblig&aacute;os a pasar pol FTP pa validar les operaciones sensibles del sitiu, como poner al d&iacute;a la base de datos o la restauraci&oacute;n d’un volc&aacute;u.

Webmaster(s) actual(es): {@_CS_LISTE_WEBMESTRES@}.
_ Alministrador(es) elexible(s): {@_CS_LISTE_ADMINS@}.

Por ser webmaster t&uacute; mesmu, equi tienes permisos pa iguar esta llista de ids -- separt&aacute;es por dos puntos &laquo;&nbsp;:&nbsp;&raquo; si son m&aacute;s d\'un. Exemplu: &laquo;1:5:6&raquo;.[[%webmestres%]]',
	'webmestres:nom' => 'Llista de webmasters',

	// X
	'xml:description' => 'Activa el validador xml pa l\'espaciu p&uacute;blicu como ta esplicao na [documentaci&oacute;n->http://www.spip.net/fr_article3541.html]. Am&eacute;stase un bot&oacute;n titul&aacute;u &laquo;An&aacute;lisis XML&raquo; a los otros botones d\'alministraci&oacute;n.',
	'xml:nom' => 'Validador XML'
);

?>
