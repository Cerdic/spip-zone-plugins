<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => '&nbsp;:&nbsp;non',
	'2pts_oui' => '&nbsp;:&nbsp;si',

	// S
	'SPIP_liens:description' => '@puce@ Todas as ligaz&oacute;ns do web se abren predeterminadamente na mesma vent&aacute; de navegaci&oacute;n en curso. Mais pode ser &uacute;til abril ligaz&oacute;ns externas ao web nunha nova vent&aacute; exterior -- iso implica engadir {target="_blank"} a todas as balizas &lt;a&gt; dotadas por  SPIP de clases {spip_out}, {spip_url} ou {spip_glossaire}. Se cadra &eacute; necesario engadir unha destas clases nas ligaz&oacute;ns do esqueleto do web (ficheiros html) co fin de estender ao m&aacute;ximo esta funcionalidade.[[%radio_target_blank3%]]

@puce@ SPIP permite ligar palabras &aacute; s&uacute;a definici&oacute;n merc&eacute; ao atallo tipogr&aacute;fico <code>[?mot]</code>. Predeterminadamente (ou se vostede  deixa baleira a caixa seguinte), o glosario externo reenv&iacute;a sobre a enciclopedia libre wikipedia.org. Pode escoller o enderezo que se vaia utilizar. <br />Ligaz&oacute;n de test : [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP e as ligaz&oacute;ns externas',

	// A
	'acces_admin' => 'Acceso de administraci&oacute;n :',
	'auteur_forum:description' => 'Invite a todos os autores a cubri (cando menos cunha letra!) o campo &laquo;@_CS_FORUM_NOM@&raquo; co fin de evitar as contribuci&oacute;ns totalmente an&oacute;nimas.',
	'auteur_forum:nom' => 'Non haber&aacute; foros an&oacute;nimos',
	'auteurs:description' => 'Esta utilidade configura a apariencia da [p&aacute;xina de autores->./?exec=auteurs], na s&uacute;a parte privada.

@puce@ Defina aqu&iacute; o n&uacute;mero m&aacute;ximo de autores que se mostrar&aacute;n no cadro central da p&aacute;xina de autores. Por demais, os autores ser&aacute;n mostrados mediante unha paxinaci&oacute;n.[[%max_auteurs_page%]]

@puce@ Que estados de autores poden ser listados nesta p&aacute;xina ?
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'P&aacute;xina de autores',

	// B
	'basique' => 'B&aacute;sica',
	'blocs:aide' => 'Bloques despregables : <b>&lt;bloque&gt;&lt;/bloque&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) e <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Permite crear bloques nos que o t&iacute;tulo  activo pode facelos visibles ou invisibles.

@puce@ {{Nos textos SPIP}} : os redactores te&ntilde;en a disposici&oacute;n as novas balizas &lt;bloque&gt; (ou &lt;invisible&gt;) e &lt;visible&gt; para utilizar nos seus textos, coma no caso : 

<quote><code>
<bloc>
 Un t&iacute;tulo que se far&aacute; activo,  cliquable
 
 O texto para ocultar/mostrar, despois de dous saltos de li&ntilde;a...
 </bloc>
</code></quote>

@puce@ {{Nos esqueletos}} : ten &aacute; s&uacute;a disposici&oacute;n as novas balises #BLOC_TITRE, #BLOC_DEBUT e #BLOC_FIN para utilizar coma no caso : 
<quote><code> #BLOC_TITRE ou #BLOC_TITRE{mon_URL}
 O meu t&iacute;tulo
 #BLOC_RESUME    (facultatif)
 unha versi&oacute;n resumida do bloque seguinte
 #BLOC_DEBUT
 O meu bloque despregable (que conter&aacute; o enderezo URL punteado se for necesario)
 #BLOC_FIN</code></quote>
',
	'blocs:nom' => 'Bloques despregables',
	'boites_privees:description' => 'Todas as funcionalidades abaixo descritas aparecen na parte privada.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{As revisi&oacute;ns da Navalla Su&iacute;za}} : un cadre sobre a presente p&aacute;xina de configuraci&oacute;n, indicando as &uacute;ltimas modificaci&oacute;ns achegadas ao c&oacute;digo do m&oacute;dulo ([Source->@_CS_RSS_SOURCE@]).
- {{Os artigos en formato SPIP}} : un cadro repregable suplementario para os seus artigos co fin co fin de co&ntilde;ecer o  c&oacute;digo fonte usado polos seus autores.
- {{Estado de autores}} : un cadro suplementario sobre [a p&aacute;xina de autores->./?exec=auteurs] que indica os &uacute;ltimos 10 conectados e as inscrici&oacute;ns non confirmadas. S&oacute; os administradores ven esta informaci&oacute;n.',
	'boites_privees:nom' => 'Funcionalidades privadas',

	// C
	'categ:admin' => '1. Administraci&oacute;n',
	'categ:divers' => '6. Varios',
	'categ:public' => '4. Exposici&oacute;n p&uacute;blica',
	'categ:spip' => '5. Balizas, filtros, criterios',
	'categ:typo-corr' => '2. Melloramento de textos',
	'categ:typo-racc' => '3. Atallos tipogr&aacute;ficos',
	'certaines_couleurs' => 'S&oacute; as balizas definidas aqu&iacute; abaixo ci-dessous@_CS_ASTER@ :',
	'chatons:aide' => 'Chatons : @liste@',
	'chatons:description' => 'Introduce imaxes(ou chatons para que moito andan cos {tchats}) en todos os textos ou aparece unha cadea do tipo <code>:nom</code>.
_ Esta utilidade troca os atallos polas imaxes que co mesmo nome encontre no cartafol plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Chat&oacute;ns',
	'class_spip:description1' => 'Pode definir aqu&iacute; certos recursos de SPIP. Un valor baleiro equivale a usar o valor predeterminado.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{Os atallos de SPIP}}.

Pode definir aqu&iacute; certos atallos de SPIP. Un valor baleiro equivale a usar o valor predeterminado.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

SPIP adoita usar a baliza &lt;h3&gt; para os intert&iacute;tulos. Escolla aqu&iacute; se quixer, outra cadea de substituci&oacute;n :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP elixiu usar a baliza &lt;i> para transcribir as it&aacute;licas. Mais &lt;em> poder&iacute;a ser igualmente adecuado. &Eacute; dicir :[[%racc_i1%]][[->%racc_i2%]]
Ollo : modificando a substituci&oacute;n dos atallos de it&aacute;licas, o estilo {{2.}} especificado anteriormente xa non ser&aacute; aplicado.

@puce@ {{Os estilos de SPIP}}. Ata a versi&oacute;n 1.92 de SPIP, os atallos tipogr&aacute;ficos produc&iacute;an balizas sistematicamente affubl&eacute;s de tipo "spip". Por exemplo : <code><p class="spip"></code>. Agora pode definir o estilo destas balizas en funci&oacute;n das s&uacute;as follas de estilo. En caso de deixalo baleiro implica que ning&uacute;n estilo particular ser&aacute; aplicado.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Balizas &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; e as listas (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Balizas &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[%style_h%]]

Ollo : modificando este segundo par&aacute;metro, p&eacute;rdense os estilos est&aacute;ndar asociados a estas balizas.</blockquote>',
	'class_spip:nom' => 'SPIP e os seus atallos',
	'code_css' => 'CSS',
	'code_fonctions' => 'Funci&oacute;ns',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Opci&oacute;ns',
	'contrib' => 'M&aacute;is informaci&oacute;n: @url@',
	'couleurs:aide' => 'Colorear : <b>[coul]texte[/coul]</b>@fond@ con <b>coul</b> = @liste@',
	'couleurs:description' => 'Permite aplicar doadamente cores a todos os textos do web (artigos, breves, t&iacute;tulos, foro, …) usando balizas en atallos.

Dou exemplos id&eacute;nticos para trocar a cor do texto :@_CS_EXEMPLE_COULEURS2@

Idem para trocar o fondo, se a opci&oacute;n seguinte o permite :@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@ O formato destas balizas personalizadas debe listar as cores existentes ou definir parellas &laquo;balise=couleur&raquo;, sempe separadas por v&iacute;rgulas. Exemplos : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; ou mesmo &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Para o primeiro e o derradeiro exemplo, as balizas autorizadas son : <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> e <code>[fond rouge]</code> se os fondos son permitidos).',
	'couleurs:nom' => 'Todo en cores',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]texte[/coul]</b>, <b>[bg&nbsp;coul]texte[/coul]</b>',

	// D
	'decoration:aide' => 'Decoraci&oacute;n&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b>, con <b>balise</b> = @liste@',
	'decoration:description' => 'Novos estilos parametrables nos seus textos e acces&iacute;beis merc&eacute; &aacute;s balizas con comas angulares. Exemplo : 
&lt;mabalise&gt;texte&lt;/mabalise&gt; ou : &lt;mabalise/&gt;.<br /> Defina seguidamente os estilos CSS dos que te&ntilde;a necesidade, unha baliza por li&ntilde;a, consonte as expresi&oacute;ns seguintes :
- {type.mabalise = meu estilo CSS}
- {type.mabalise.class = mi&ntilde;a clase CSS}
- {type.mabalise.lang = mi&ntilde;a lingua (ex : fr)}
- {unalias = minhabaliza}

O par&aacute;metro {type} seguinte pode ter tres valores:
- {span} : baliza para o interior dun par&aacute;grafo (tipo Inline)
- {div} : baliza asociada a un novo par&aacute;grafo (tipo Block)
- {auto} : baliza determinada automaticamente polo plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Decoraci&oacute;n',
	'decoupe:aide' => 'Bloque de pestanas : <b>&lt;onglets>&lt;/onglets></b><br/>Separador de p&aacute;xinas ou pestanas&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias&nbsp;:&nbsp;@sep@',
	'decoupe:description' => 'Partir a presentaci&oacute;n p&uacute;blica dun artigo en varias p&aacute;xinas merc&eacute; a unha paxinaci&oacute;n autom&aacute;tica. Sit&uacute;e simplemente no seu artigo catro signos de m&aacute;is consecutivos (<code>++++</code>) no lugar que debe recibir o corte.
_ De utilizar este separador no interior das balizas &lt;pestanas&gt; e &lt;/pestanas&gt; obter&aacute; un xogo de pestanas.
_ Nos esqueletos : ten &aacute; s&uacute;a disposici&oacute;n as novas balizas #ONGLETS_DEBUT, #ONGLETS_TITRE e #ONGLETS_FIN.
_ Esta utilidade pode ser emparellada cun {Sumario para os seus artigos}.',
	'decoupe:nom' => 'Partici&oacute;n en p&aacute;xinas e pestanas',
	'desactiver_flash:description' => 'Suprime os obxectos flash das p&aacute;xinas do seu web e substit&uacute;eas polo contido alternativo asociado.',
	'desactiver_flash:nom' => 'Desactiva os obxectos flash',
	'detail_balise_etoilee' => '{{Aviso}} : Comprobe a utilizaci&oacute;n feita polos seus esqueletos das balizas estreladas. O tratamento desta ferramenta non se aplicar&aacute;n sobre : @bal@.',
	'detail_fichiers' => 'Ficheiros :',
	'detail_inline' => 'C&oacute;digo inline :',
	'detail_jquery1' => '{{Aviso}} : esta utilidade precisa o m&oacute;dulo {jQuery} para funcionar con esta versi&oacute;n de SPIP.',
	'detail_jquery2' => 'Esta ferramenta necesita a librar&iacute;a {jQuery}.',
	'detail_pipelines' => 'Tubar&iacute;as (pipelines) :',
	'detail_traitements' => 'Tratamentos :',
	'dossier_squelettes:description' => 'Modifica o cartafol do esqueleto usado. Por exemplo : "squelettes/monsquelette". Pode rexistrar varios cartafoles separ&aacute;ndoos polos dous puntos <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. Deixando baleira caixa seguinte (ou escribindo "dist"), vai ser o esqueleto orixinal "dist" fornecido por SPIP o que ser&aacute; usado.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Cartafol do esqueleto',

	// E
	'effaces' => 'Borrados',
	'en_travaux:description' => 'Permite mostrar unha mensaxe personalizable durante unha fase de mantemento sobre todo o web p&uacute;blico.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Web en obras',
	'erreur:description' => 'Falta o id na definici&oacute;n da ferramenta!',
	'erreur:distant' => 'O servidor remoto',
	'erreur:js' => 'Un erro de JavaScript parece terse producido nesta p&aacute;xina e impide o seu funcionamento correcto. Active JavaScript no seu navegador ou desactive alg&uacute;ns m&oacute;dulos do seu web.',
	'erreur:nojs' => 'O JavaScript est&aacute; desactivado nesta p&aacute;xina.',
	'erreur:nom' => 'Erro !',
	'erreur:probleme' => 'Problema en : @pb@',
	'erreur:traitements' => 'A Navalla Su&iacute;za - Erro de compilation dos tratamentos : mestura \'typo\' e \'propre\' prohibida !',
	'erreur:version' => 'Esta ferramenta non est&aacute; dispo&ntilde;&iacute;bel nesta versi&oacute;n de SPIP.',
	'etendu' => 'Estendido',

	// F
	'f_jQuery:description' => 'Impide a instalaci&oacute;n de {jQuery} na parte p&uacute;blica co fin de economizar un pouco de &laquo;tempo m&aacute;quina&raquo;. Esta librar&iacute;a ([->http://jquery.com/]) achega numerosas comodidades na programaci&oacute;n de Javascript e poder ser usada por certos m&oacute;dulos. SPIP usa dela na &aacute;rea privada.

Aviso : alg&uacute;nhas ferramentas de A Navalla Su&iacute;za necesitan as funci&oacute;ns de {jQuery}. ',
	'f_jQuery:nom' => 'Desactiva jQuery',
	'filets_sep:aide' => 'Filetes de separaci&oacute;n&nbsp;: <b>__i__</b> ou <b>i</b> &eacute; un n&uacute;mero.<br />Outros filetes dipo&ntilde;&iacute;beis : @liste@',
	'filets_sep:description' => 'Insire filetes de separaci&oacute;n, personalizables mediante as follas de estilo, en todos os textos de SPIP.
_ A sintaxe &eacute; : "__code__", ou "code" representa ben o n&uacute;mero de identificaci&oacute;n (de 0 &agrave; 7) do filete inserible en relaci&oacute;n directa cos estilos correspondentes, ben o nome dunha imaxe situada no cartafol plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Filetes de separaci&oacute;n',
	'filtrer_javascript:description' => 'Para xerar o javascript nos artigos, tres modos son dispo&ntilde;&iacute;beis :
- <i>nunca</i> : o javascript &eacute; rexeitado en todas partes
- <i>predeterminadot</i> : o javascript m&aacute;rcase en vermellos na zona privad
- <i>sempre</i> : o javascript &eacute; aceptado por todas as partes.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <b>toujours</b> s&eacute;curis&eacute;e.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Xesti&oacute;n do javascript',
	'flock:description' => 'Desactiva o bloqueo de ficheiros neutralizando a funci&oacute;n PHP {flock()}. Alg&uacute;s aloxadores dan de feito problemas graves sexa por un sistema de ficheiros inadaptados ou sexa por unha falta de sincronizaci&oacute;n. Non active esta utilidade  se este funciona normalmente.',
	'flock:nom' => 'Non bloquear os ficheiros',
	'fonds' => 'Fondos :',
	'forcer_langue:description' => 'Forza o contexto de lingua para os xogos de esqueletos multiling&uuml;es que dispo&ntilde;en dun formulario ou dun men&uacute; de linguas que saiban xera a cookie de linguas.',
	'forcer_langue:nom' => 'Forzar lingua',
	'format_spip' => 'Artigos en formato SPIP',
	'forum_lgrmaxi:description' => 'De modo predeterminado as mensaxes de foros non se limitan en tama&ntilde;o. De activar esta ferramenta, unha mensaxe de erro mostrarase cando algu&eacute;n queira introducir unha mensaxe de tama&ntilde;o superior ao especificado, e a mensaxe, e a mensaxe ser&aacute; rexeitada. Un valor baleiro ou igual a  0 significa no entanto que ning&uacute;n l&iacute;mite se aplica.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Tama&ntilde;o dos foros',

	// G
	'glossaire:description' => '@puce@ Xesti&oacute;n dun glosario interno ligado a un ou varios grupos de palabras-clave. Rex&iacute;strese aqu&iacute; o nome dos grupos, separados por dous puntos &laquo;&nbsp;:&nbsp;&raquo;. Deixando a caixa baleira, o que se ler&aacute; (ou escribirr "Glosario") &eacute; o grupo "Glosario" para ser usado. [[%glossaire_groupes%]] @puce@ Para cada palabra, pode escoller o n&uacute;mero m&aacute;ximo establecido de ligaz&oacute;ns nos seus textos. Calquera valor nulo ou negativo implica que todas as palabras reco&ntilde;ecidas ser&aacute;n tratadas. [[%%glossaire_limite por palavra-clave]] @puce@ D&uacute;as soluci&oacute;ns son ofrecidas para xerar a pequena xanela autom&aacute;tica que aparece cando se sobrevoa &aacute; ocorrencia. [[%glossaire_js%]]',
	'glossaire:nom' => 'Glosario interno',
	'glossaire_css' => 'Soluci&oacute;n CSS',
	'glossaire_js' => 'Soluci&oacute;n Javascript',
	'guillemets:description' => 'Substituci&oacute;n autom&aacute;tica das comas dereitas (") polas tipogr&aacute;ficas da lingua de composici&oacute;n. A substituci&oacute;n, transparente para o usuario, non modifica o texto orixinal sen&oacute;n que soamente cambia a presentaci&oacute;n final.',
	'guillemets:nom' => 'V&iacute;rgulas tipogr&aacute;ficas',

	// H
	'help' => '{{Esta p&aacute;xina &eacute; unicamente accesible para o responsable do web.}}<p>D&aacute; acceso &aacute;s diferentes funci&oacute;ns suplementarias achegadas polo m&oacute;dulo &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Version local : @version@@distant@<br/>@pack@</p><p>Ligaz&oacute;ns de documentaci&oacute;n :<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>R&eacute;initialisations :
_ • [Ferramentas de cach&eacute;|Volver &aacute; apariencia inicial desta p&aacute;xina->@hide@]
_ • [De todo o m&oacute;dulo|Volver ao estado inicial do m&oacute;dulo->@reset@]@install@
</p>',
	'help0' => '{{Esta p&aacute;xina s&oacute; &eacute; acces&iacute;bel para os respons&aacute;beis do web.}}<p>D&aacute; acceso a diferentes funci&oacute;ns suplementarias achegadas polo m&oacute;dulo &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Ligaz&oacute;n de documentaci&oacute;n :<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>Reinicializaci&oacute;n:
_ • [De todo o m&oacute;dulo->@reset@]
</p>',

	// I
	'insert_head:description' => 'Activa automaticamente a baliza [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] en todos os esqueletos, que te&ntilde;an ou non esta baliza entre &lt;head&gt; e &lt;/head&gt;. Merc&eacute; a esta opci&oacute;n, os plugins poder&aacute;n inserir javascript (.js) ou follas de estilo (.css).',
	'insert_head:nom' => 'Baliza #INSERT_HEAD',
	'insertions:description' => 'AVISO : ferramenta en proceso de desenvolvemento !! [[%insertions%]]',
	'insertions:nom' => 'Correcci&oacute;ns autom&aacute;ticas',
	'introduction:description' => 'Esta baliza para situar nos esqueletos serve en xeral para placer dans les squelettes sert en g&eacute;n&eacute;ral para actualizaci&oacute;n (&agrave; la une) ou nas secci&oacute;ns co fin de producir un resumo de artigos, de breves, etc..</p>
<p>{{Aviso}} : Antes de activar esta funcionalidade, comprobe ben que ningunha funci&oacute;n {balise_INTRODUCTION()} exista xano seu esqueleto ou nos seus m&oacute;dulos, a sobrecarga producir&iacute;a un erro de compilaci&oacute;n.</p>
@puce@ Pode precisar (en porcentaxe en relaci&oacute;n co valor usado de modo predeterminado) a largura do texto reeenviado pola baliza #INTRODUCTION. Une valeur nulo ou igual a 100 non modifica o aspecto da introduci&oacute;n e usa daquela os valores predeterminados seguintes : 500 caracteres para os artigos, 300 para as breves e 600 para os foros ou as secci&oacute;ns.
[[%lgr_introduction%&nbsp;%]]
@puce@ De modo predeterminado, os puntos suspensivos engadidos ao resultado da baliza #INTRODUCTION se o texto &eacute; demasiado longo son : <html>&laquo;&amp;nbsp;(…)&raquo;</html>. Pode precisar aqu&iacute; a s&uacute;a propia cadea de caracteres que indiquen ao lector que o texto truncado ten unha continuidade.
[[%suite_introduction%]]
@puce@ Se a baliza #INTRODUCTION se emprega para resumir un artigo, ent&oacute;n A Navalla Su&iacute;za pode fabricar unha ligaz&oacute;n sobre eses puntos suspensivos definidos a seguir co fin de levar o lector ao texto orixinal. Por exemplo : &laquo;Ler a continuidade deste artigo…&raquo;
[[%lien_inctroduction%]]
',
	'introduction:nom' => 'Baliza #INTRODUCTION',

	// J
	'js_defaut' => 'Predeterminado',
	'js_jamais' => 'Nunca',
	'js_toujours' => 'Sempre',

	// L
	'label:admin_travaux' => 'Pechar o web para :',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Creaci&oacute;n sistem&aacute;tica de sumario :',
	'label:balise_sommaire' => 'Activar a baliza #CS_SOMMAIRE :',
	'label:couleurs_fonds' => 'Permitir os fondos :',
	'label:cs_rss' => 'Activar :',
	'label:decoration_styles' => 'As s&uacute;as balizas de estilo pesonalizado :',
	'label:dossier_squelettes' => 'Cartafol para utilizar :',
	'label:duree_cache' => 'Duraci&oacute;n da cach&eacute; local :',
	'label:duree_cache_mutu' => 'Duraci&oacute;n da cach&eacute; en mutualizaci&oacute;n :',
	'label:forum_lgrmaxi' => 'Valor (en caracteres) :',
	'label:glossaire_groupes' => 'Grupo(s) usado(s) :',
	'label:glossaire_js' => 'T&eacute;cnica usada :',
	'label:glossaire_limite' => 'N&uacute;mero m&aacute;ximo de ligaz&oacute;ns creadas :',
	'label:insertions' => 'Correcci&oacute;ns autom&aacute;ticas :',
	'label:lgr_introduction' => 'Lonxitude do resumo :',
	'label:lgr_sommaire' => 'Lonxitude do sumario (9 a 99) :',
	'label:lien_inctroduction' => 'Puntos suspensivos de continuidade activos :',
	'label:liens_interrogation' => 'Protexer os URL :',
	'label:liens_orphelins' => 'Ligaz&oacute;ns activas :',
	'label:max_auteurs_page' => 'Autors por p&aacute;xina :',
	'label:message_travaux' => 'A s&uacute;a mensaxe de mantemento :',
	'label:paragrapher' => 'Paragrafar sempre :',
	'label:puce' => 'Vi&ntilde;eta p&uacute;blica &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Valor de quota :',
	'label:racc_h1' => 'Entrada e sa&iacute;da dun &laquo;<html>{{{intert&iacute;tulo}}}</html>&raquo; :',
	'label:racc_hr' => 'Li&ntilde;a horizontal &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => 'Entrada e sa&iacute;da dunha &laquo;<html>{it&aacute;lica}</html>&raquo; :',
	'label:radio_desactive_cache3' => 'Desactivar a cach&eacute; :',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Nova xanela para as ligaz&oacute;ns externas :',
	'label:radio_type_urls3' => 'Formato dos URL :',
	'label:set_couleurs' => 'Conxunto para usar :',
	'label:spam_mots' => 'Secuencias prohibidas :',
	'label:spip_script' => 'Script de chamada :',
	'label:style_h' => 'O seu estilo :',
	'label:style_p' => 'O seu estilo :',
	'label:suite_introduction' => 'Puntos de continuidade :',
	'label:titre_travaux' => 'T&iacute;tulo da mensaxe :',
	'label:url_glossaire_externe2' => 'Ligaz&oacute;n sobre o glosario externo :',
	'liens_en_clair:description' => 'Pon &aacute; s&uacute;a disposici&oacute;n o filtro : \'liens_en_clair\'. O seu texto cont&eacute;n probablemente ligaz&oacute;ns de hipertexto que non son visibles tras unha impresi&oacute;n. Este filtro engade entre corchetes o destino de cada ligaz&oacute;n activa (ligaz&oacute;ns externas ou correos). Atenci&oacute;n : en modo de impresi&oacute;n (par&aacute;metro \'cs=print\' ou \'page=print\' no url da p&aacute;xina), esta funcionalidade apl&iacute;case automaticamente.',
	'liens_en_clair:nom' => 'Ligaz&oacute;ns en claro',
	'liens_orphelins:description' => 'Esta ferramenta ten d&uacute;as funci&oacute;ns :

@puce@ {{Ligaz&oacute;ns correctas}}.

SPIP ten por h&aacute;bito inserir un espazo diante dos puntos de interrogaci&oacute;n ou de exclamaci&oacute;n, tipograf&iacute;a francesa obriga. Velaqu&iacute; unha ferramenta que protexe o punto de interrogaci&oacute;n nos url dos seus textos.[[%liens_interrogation%]]

@puce@ {{Ligaz&oacute;ns orfas}}.

Substit&uacute;e sistematicamente todos os url deixados en texto polos usuarios (nomeadamente nos foros) e que non son clicables, polas ligaz&oacute;ns de hipertexto en formato  SPIP. Por exemplo : {<html>www.spip.net</html>} substit&uacute;ese por [->www.spip.net].

Podedes escoller o tipo de substituci&oacute;n :
_ • {B&aacute;sica} : son substitu&iacute;das as ligaz&oacute;ns do tipo {<html>http://spip.net</html>} (inclu&iacute;do o protocolo) ou {<html>www.spip.net</html>}.
_ • {Estendido} : son substitu&iacute;das ademais as ligaz&oacute;ns do tipo {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'URL fermosas',
	'log_couteau_suisse:description' => 'Hai numerosas rese&ntilde;as a prop&oacute;sito do funcionamento do m&oacute;dulo \'A Navalla Su&iacute;za\' nos ficheiros spip.log que se poden atopar no cartafol : @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => 'Rexistro (log) detallado de \'A Navalla Su&iacute;za\'',

	// M
	'mailcrypt:description' => 'Oculta todas as ligaz&oacute;ns de correo presentes nos seus textos e substit&uacute;eos por unha ligaz&oacute;n Javascript que permite activara a aplicaci&oacute;n de correo do lector. Esta ferramenta antispam tenta impedir os robots de colleita de enderezos electr&oacute;nicos deixados en claro nos foros ou nas balizas dos seus esqueletos.',
	'mailcrypt:nom' => 'MailCrypt',
	'modifier_vars' => 'Modificar os par&aacute;metros @nb@',

	// N
	'no_IP:description' => 'Desactiva o mecanismo de rexistro autom&aacute;tico de enderezos IP dos visitantes do seu web por raz&oacute;ns de confidencialidade : SPIP non conservar&aacute; daquela ning&uacute;n n&uacute;mero IP, nin temporalmente logo das visitas (para xerar as estat&iacute;sticas ou alimentar o spip.log), nin nos foros (responsabilidade).',
	'no_IP:nom' => 'Non conservar IP',
	'nouveaux' => 'Novos',

	// O
	'orientation:description' => '3 criterios novos para os seus esqueletos : <code>{portrait}</code>, <code>{carre}</code> e <code>{paysage}</code>. Ideal para a ordenaci&oacute;n de fotos en funci&oacute;n da s&uacute;a forma.',
	'orientation:nom' => 'Orientaci&oacute;n das imaxes',
	'outil_actif' => 'Utilidade activa',
	'outil_activer' => 'Activar',
	'outil_activer_le' => 'Activar a ferramenta',
	'outil_cacher' => 'Non volver a mostrar',
	'outil_desactiver' => 'Desactivar',
	'outil_desactiver_le' => 'Desactivar a ferramenta',
	'outil_inactif' => 'Utilidade inactiva',
	'outil_intro' => 'Esta p&aacute;xina lista as caracter&iacute;sticas do m&oacute;dulo postos &aacute; s&uacute;a disposici&oacute;n. <br /> <br /> Ao premer sobre o nome das ferramentas que aparecen a seguir, seleccione, as que pode cambiar o estado usando o bot&oacute;n central: as ferramentas activadas ser&aacute;n desactivadas e <i> viceversa </ i>. Con cada click, a descrici&oacute;n aparece a seguir das listas. As categor&iacute;as son pregables e as ferramentas p&oacute;dense ocultar. O dobre clic permite trocar rapidamente unha ferramenta. <br /> <br /> Nun primeiro uso, recom&eacute;ndase activar as ferramentas unha por unha, no caso de apareceren certas incompatibilidades co seu esqueleto, con SPIP ou con outros m&oacute;dulos. <br /> <br /> Nota: a simple carga desta p&aacute;xina compila o conxunto das ferramentas da Navalla Su&iacute;za .',
	'outil_intro_old' => 'Esta interface &eacute; antiga.<br /><br />Se vostede encontra problema coa utilizaci&oacute;n da <a href=\'./?exec=admin_couteau_suisse\'>nova     interface</a>, non dubide en fac&eacute;rnolo saber no foro <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ ferramenta',
	'outil_nbs' => '@pipe@ : @nb@ ferramentas',
	'outil_permuter' => 'Cambiar a ferramenta : &laquo; @text@ &raquo; ?',
	'outils_actifs' => 'Ferramentas activas :',
	'outils_caches' => 'Ferramentas ocultas :',
	'outils_cliquez' => 'Prema sobre o nome das ferramentas seguintes para mostrar aqu&iacute; a s&uacute;a descrici&oacute;n.',
	'outils_inactifs' => 'Ferramentas inactivas :',
	'outils_liste' => 'Lista de ferramentas da Navalla Su&iacute;za',
	'outils_permuter_gras1' => 'Trocar as ferramentas en negra',
	'outils_permuter_gras2' => 'Trocar as @nb@ ferramentas en negra ?',
	'outils_resetselection' => 'Reinicializar a selecci&oacute;n',
	'outils_selectionactifs' => 'Seleccionar todas as ferramentas activas',
	'outils_selectiontous' => 'TODOS',

	// P
	'pack_alt' => 'Ver os par&aacute;metros de configuraci&oacute;n en curso',
	'pack_descrip' => 'O seu "Pack de configuraci&oacute;n actual" recolle o conxunto dos par&aacute;metros de configuraci&oacute;n relativos &aacute; Navalla Su&iacute;za: a activaci&oacute;n de ferramentas e o valor das s&uacute;as eventuais variables.

Este c&oacute;digo PHP pode po&ntilde;erase no ficheiror /config/mes_options.php e engadir&aacute; unha ligaz&oacute;n de reinicializaci&oacute;n sobre esta p&aacute;xina "do paquete {Pack Actuel}". Desde logo p&oacute;delle cambiar o nome a seguir.

De reinicializar o m&oacute;dulo premendo sobre un paquete, a Navalla Su&iacute;za reconfigurarase automaticamente en funci&oacute;n dos par&aacute;metros predeterminado no paquete.',
	'pack_du' => '• do paquete @pack@',
	'pack_installe' => 'Actualizaci&oacute;n dun paquete de configuraci&oacute;n',
	'pack_titre' => 'Configuraci&oacute;n actual',
	'par_defaut' => 'Predeterminado',
	'paragrapher2:description' => 'A funci&oacute;n SPIP <code>paragrapher()</code> insere balizas &lt;p&gt; e &lt;/p&gt; en todos os textos que son que est&aacute;n desprovistos de par&aacute;grafos. Co fin de xerar m&aacute;is finamente os seus estilos e os dese&ntilde;os, ten a posibilidade de uniformizar o aspecto dos textos do seu  web.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Paragrafar',
	'pipelines' => 'Tubar&iacute;as (pipelines usadas)&nbsp;:',
	'pucesli:description' => 'Substit&uacute;a as vi&ntilde;etas &laquo;-&raquo; (gui&oacute;n simple) dos artigos por listas les par des listes nominadas &laquo;-*&raquo; (traducidas en  HTML por : &lt;ul>&lt;li>…&lt;/li>&lt;/ul>) e nas que o estilo pode ser personalizado por css.',
	'pucesli:nom' => 'Vi&ntilde;etas fermosas',

	// R
	'raccourcis' => 'Atallos tipogr&aacute;ficos activos da Navalla Su&iacute;za&nbsp;:',
	'raccourcis_barre' => 'Os atallo tipogr&aacute;ficos da Navalla Su&iacute;za',
	'reserve_admin' => 'Acceso reservado aos administradores.',
	'rss_attente' => 'Espera RSS...',
	'rss_desactiver' => 'Desactivar as &laquo; Revisi&oacute;ns da Navalla Su&iacute;za &raquo;',
	'rss_edition' => 'Flux RSS actualizado o :',
	'rss_titre' => '&laquo;&nbsp;A Navalla Su&iacute;za&nbsp;&raquo; en desenvolvemento :',
	'rss_var' => 'As revisi&oacute;n da Navalla Su&iacute;za',

	// S
	'sauf_admin' => 'Todos, ag&aacute;s os administradores',
	'set_options:description' => 'Seleccione o tipo de interface privada predeterminada (simplificada ou avanzada) para todos os redactores xa existentes ou futuros e suprima o bot&oacute;n correspondente da barra de iconas.[[%radio_set_options4%]]',
	'set_options:nom' => 'Tipo de interface privada',
	'sf_amont' => 'Fluxo ascendente',
	'sf_tous' => 'Todos',
	'simpl_interface:description' => 'Desactive o cambio r&aacute;pido de estado dun artigo sobrevoando a s&uacute;a vi&ntilde;eta de cor. Iso &eacute; &uacute;til se vostede procura obter unha  interface privada o m&aacute;is limpa co fin de optimizar o rendemento do lado do cliente.',
	'simpl_interface:nom' => 'Alixeiramento da interfacer privada',
	'smileys:aide' => 'Riso&ntilde;os : @liste@',
	'smileys:description' => 'Inserir riso&ntilde;os en todos os textos onde aparece un atallo de estilo <acronym>:-)</acronym>. Ideal para os foros.
_ Est&aacute; dispo&ntilde;ible unha baliza para mostrar unha t&aacute;boa de riso&ntilde;os nos seus esqueletos : #SMILEYS.
_ Dese&ntilde;os : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Riso&ntilde;os',
	'sommaire:description' => 'Constr&uacute;e un sumario para os seus artigos co fin de acceder rapidamente  a t&iacute;tulos de alto tama&ntilde;o (balizas HTML &lt;h3>Un intert&iacute;tulo&lt;/h3> ou a atallos de SPIP : intert&iacute;tulos do estilo :<code>{{{Un t&iacute;tulo grande}}}</code>).

@puce@ Pode definir aqu&iacute; o n&uacute;mero m&aacute;ximo de caracteres retidos dos intert&iacute;tulos para constru&iacute;r o sumario :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Pode fixar tam&eacute;n o comportamento do m&oacute;dulo concernente &aacute; creaci&oacute;n do sumario: 
_ • Sistematicamente para cada artigo (unha baliza <code>[!sommaire]</code> situada en calquera lugar ou no interior do texto do artigo crear&aacute; unha excepci&oacute;n).
_ • Unicamente para os artigos que conte&ntilde;an a baliza <code>[sommaire]</code>.

[[%auto_sommaire%]]

@puce@ De modo predeterminado, a Navalla Su&iacute;za insire o sumario na cabeceira do artigo automaticamente. Vostede ten a posibilidade de situar este sumario por outra banda no seu esqueleto grazas a unha baliza #CS_SOMMAIRE que vostede pode activar aqu&iacute; :
[[%balise_sommaire%]]

Este sumario pode ser aparellado con : {Partici&oacute;n en p&aacute;xinas e pestanas}.',
	'sommaire:nom' => 'Un sumario para os seus artigos',
	'sommaire_avec' => 'Un artigo con sumario&nbsp;: <b>@racc@</b>',
	'sommaire_sans' => 'Un artigo sen sumario&nbsp;: <b>@racc@</b>',
	'spam:description' => 'Tenta loitar contra os env&iacute;os de mensaxes autom&aacute;ticas e impertinentes na parte p&uacute;blica. Algunhas palabras e as balizas &lt;a>&lt;/a> est&aacute;n prohibidas.

Liste aqu&iacute; as secuencias prohibidas @_CS_ASTER@ separ&aacute;ndoas por espazos. [[%spam_mots%]]
@_CS_ASTER@Para especificar unha palabra enteira, p&oacute;&ntilde;aa entre par&eacute;nteses. Para unha expresi&oacute;n con espazos, sit&uacute;ea entre comas.',
	'spam:nom' => 'Loita contra o SPAM',
	'spip_cache:description' => '@puce@ De modo predeterminado, SPIP calcula todas as p&aacute;xinas p&uacute;blicas e as sit&uacute;a na cach&eacute; co fin de acelerar a consulta. Desactivar temporalmente a cach&eacute; pode axudar ao desenvolvemento do web.[[%radio_desactive_cache3%]]@puce@ A cach&eacute; ocupa un certo espazo de disco e SPIP pode limitar o tama&ntilde;o. Un valor baleiro ou igual a 0 significa que non se aplica ningunha cota.[[%quota_cache% Mo]]@puce@ Se a baliza #CACHE non se encontra nos seus esqueletos, SPIP considera de modo predeterminado que a cach&eacute; dunha p&aacute;xina ten unha duraci&oacute;n de vida de 24 horas antes de a recalcular. Co fin de xestionar mellor a carga do seu servidor, pode modificar aqu&iacute; este valor.[[%duree_cache% heures]]@puce@ Se vostede ten varios webs en mutualizaci&oacute;n, pode especificar aqu&iacute; o valor predeterminado tomado en conta por todos os web locais (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => 'SPIP e a memoria cach&eacute;…',
	'stat_auteurs' => 'Os estado dos autores',
	'statuts_spip' => 'Unicamente os estados SPIP seguintes :',
	'statuts_tous' => 'Todos os estados',
	'suivi_forums:description' => 'Un autor de artigo ser&aacute; sempre informado cando apareza unha mensaxe no foro p&uacute;blico asociado. Tam&eacute;n &eacute; posible adverter ademais : todoso os participantes no foro ou soamente os autores de mensaxes en fluxo ascendente.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Seguimento dos foros p&uacute;blicos',
	'supprimer_cadre' => 'Suprimir este cadro',
	'supprimer_numero:description' => 'Aplique a funci&oacute;n SPIP supprimer_numero() ao conxunto dos {{t&iacute;tulos}} e dos {{nomes}} do web p&uacute;blico, sen que o filtro supprimer_numero estea presente nos esqueletos.<br />Velaqu&iacute; a sintaxe que se vai usar no cadro dun web multiling&uuml;e : <code>1. <multi>O Meu T&iacute;tulo[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Suprime o n&uacute;mero',

	// T
	'titre' => 'A Navalla Su&iacute;za',
	'titre_tests' => 'A Navalla Su&iacute;za - P&aacute;xina de tests…',
	'tous' => 'Todos',
	'toutes_couleurs' => 'As 36 cores dos estilos css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Bloques multiling&uuml;es&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => 'Introduce o atallo <code><:un_texte:></code> para introducir libremente bloques multiling&uuml;es nun artigo.
_ A funci&oacute;n SPIP usada &eacute; : <code>_T(\'un_texte\', 
flux)</code>.
_ Non esqueza verificar \'un_texte\' est&aacute; ben definido nos ficheiros de lingua.',
	'toutmulti:nom' => 'Bloques multiling&uuml;es',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Este web ser&aacute; restablecido axi&ntilde;a.
_ Grazas pola s&uacute;a comprensi&oacute;n.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'type_urls:description' => '@puce@ SPIP ofrece unha elecci&oacute;n entre varios xogos de URL para facer as ligaz&oacute;ns de acceso &aacute;s p&aacute;xinas do seu web :
<div style="font-size:90%; margin:0 2em;">
- {{paxina}} : o valor predeterminado de SPIP v1.9x : <code>/spip.php?article123</code>.
- {{html}} : as ligaz&oacute;ns te&ntilde;en a forma de p&aacute;xinas html cl&aacute;sicas : <code>/article123.html</code>.
- {{propias}} : as ligaz&oacute;ns calc&uacute;lanse grazas ao t&iacute;tulo: <code>/Mon-titre-d-article</code>.
- {{propias2}} : a extensi&oacute;n \'.html\' eng&aacute;dese aos enderezos xerados : <code>/Mon-titre-d-article.html</code>.
- {{estandar}} : URL usadas por v1.8 e precedentes : <code>article.php3?id_article=123</code>
- {{propias-qs}} : este sistema funciona en "Query-String", &eacute; dicir necesidade de .htaccess ; as ligaz&oacute;ns ser&aacute;n desta forma : <code>/?Mon-titre-d-article</code>.</div>

M&aacute;is info : [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@para usar os formatos {html}, {propria} ou {propria2}, copie o ficheiro "htaccess.txt" do cartafol de base do web SPIP co nome ".htaccess" (atenci&oacute;n a non borrar outras regraxes que vostede te&ntilde;a posto nese ficheiro) ; se o seu web est&aacute; nun subcartafol, deber&aacute; tam&eacute;n editar a li&ntilde;a "RewriteBase" neste ficheiro. Os URL definidos ser&aacute;n logo redirixidos cara aos ficheiros de SPIP.</p>

@puce@ {{Unicamente de usar o formato p&aacute;xina {paxina} que segue}}, ent&oacute;n &eacute; pos&iacute;bel escoller o  script de chamada a SPIP. De modo predeterminado, SPIP escolle {spip.php}, sen&oacute;n {index.php} (formato : <code>/index.php?article123</code>) ou un valor baleiro (formato : <code>/?article123</code>) funcionan tam&eacute;n. Para calquera outro valor, &eacute; completamente necesario crear o ficheiro correspondente na ra&iacute;za de SPIP, &aacute; imaxe daquel que xa existe : {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => 'Formato das URL',
	'typo_exposants:description' => 'Textos franceses : mellora o rendementos tipogr&aacute;fico das abreviaturas correntes, metendo en super&iacute;ndice os elementos necesarios (as&iacute;, {<acronym>Mme</acronym>} produce {M<sup>me</sup>}) e corrixindo os erros correntes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, por exemplo, produce {2<sup>e</sup>}, s&oacute; abreviatura correcta).
_ As abreviaturas obtidas est&aacute;n conformes con aquelas da Imprenta nacional como constan en {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (artigo &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, imprentas da Imprimerie nationale, Paris, 2002).',
	'typo_exposants:nom' => 'Super&iacute;ndices tipogr&aacute;ficos',

	// U
	'url_html' => 'html@_CS_ASTER@',
	'url_page' => 'p&aacute;xina',
	'url_propres' => 'propias@_CS_ASTER@',
	'url_propres-qs' => 'propias-qs',
	'url_propres2' => 'propias2@_CS_ASTER@',
	'url_standard' => 'est&aacute;ndar',

	// V
	'validez_page' => 'Para acceder &aacute;s modificaci&oacute;ns :',
	'variable_vide' => '(Baleiro)',
	'vars_modifiees' => 'Os datos foron correctamente modificados',
	'version_a_jour' => 'A s&uacute;a versi&oacute;n est&aacute; actualizada.',
	'version_distante' => 'Versi&oacute;n remota...',
	'version_nouvelle' => 'Nova versi&oacute;n : @version@',
	'verstexte:description' => '2 filtros para os seus esqueletos, que permiten producir p&aacute;xinas m&aacute;is lixeiras.
_ version_texte : extrae o contido de texto dunha p&aacute;xina html coa exclusi&oacute;n dalgunhas balizas elementares.
_ version_plein_texte : extrae o contido de texto dunha p&aacute;xina html para manter o texto pleno.',
	'verstexte:nom' => 'Versi&oacute;n de texto',
	'votre_choix' => 'A s&uacute;a elecci&oacute;n :',

	// X
	'xml:description' => 'Activa o validador xml para o espazo p&uacute;blico tal como se describe na [documentaci&oacute;n->http://www.spip.net/fr_article3541.html]. Un bot&oacute;n titulado &laquo;&nbsp;Analise XML&nbsp;&raquo; foi engadido aos outros bot&oacute;ns de administraci&oacute;n.',
	'xml:nom' => 'Validador XML'
);

?>
