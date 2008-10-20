<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => '&nbsp;:&nbsp;hay&#305;r',
	'2pts_oui' => '&nbsp;:&nbsp;evet',

	// S
	'SPIP_liens:description' => '@puce@ Sitedeki t&uuml;m ba&#287;lar akt&uuml;el sayfada a&ccedil;&#305;l&#305;r. Ama d&#305;&#351; ba&#287;lar&#305; yeni pencerede a&ccedil;mak kullan&#305;&#351;l&#305; olabilir -- cela revient &agrave; ajouter {target="_blank"} &agrave; toutes les balises &lt;a&gt; dot&eacute;es par SPIP des classes {spip_out}, {spip_url} ou {spip_glossaire}. Il est parfois n&eacute;cessaire d\'ajouter l\'une de ces classes aux liens du squelette du site (fichiers html) afin d\'&eacute;tendre au maximum cette fonctionnalit&eacute;.[[%radio_target_blank3%]]

@puce@ SPIP permet de relier des mots &agrave; leur d&eacute;finition gr&acirc;ce au raccourci typographique <code>[?mot]</code>. Par d&eacute;faut (ou si vous laissez vide la case ci-dessous), le glossaire externe renvoie vers l’encyclop&eacute;die libre wikipedia.org. &Agrave; vous de choisir l\'adresse &agrave; utiliser. <br />Lien de test : [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP ve d&#305;&#351; ba&#287;lant&#305;lar',

	// A
	'acces_admin' => 'Y&ouml;netici eri&#351;imi :',
	'auteur_forum:description' => 'En az&#305;ndan bir mektup yazm&#305;&#351; olan kamusal ileti yazarlar&#305;n&#305;, t&uuml;m kat&#305;l&#305;mlar&#305;n isimsiz olmamas&#305; i&ccedil;in &laquo;@_CS_FORUM_NOM@&raquo; alan&#305;n&#305; doldurmaya te&#351;vik eder.',
	'auteur_forum:nom' => 'Anonim (isimsiz) forum yok',
	'auteurs:description' => 'Bu alet [yazarlar sayfas&#305;->./?exec=auteurs]\'n&#305;n &ouml;zel alandaki g&ouml;r&uuml;n&uuml;&#351;&uuml;n&uuml; konfig&uuml;re eder.

@puce@ Yazarlar sayfas&#305;n&#305;n ortas&#305;ndaki ana &ccedil;er&ccedil;evede g&ouml;sterilecek maksimum yazar say&#305;s&#305;n&#305; belirtiniz. Bu say&#305;dan fazlas&#305; sayfalama (pagination) ile g&ouml;sterilir.[[%max_auteurs_page%]]

@puce@ Quels statuts d\'auteurs peuvent &ecirc;tre list&eacute;s sur cette page ?
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'Yazarlar sayfas&#305;',

	// B
	'basique' => 'Temel',
	'blocs:aide' => 'Katlanabilir bloklar : <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) et <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'T&#305;klanabilir ba&#351;l&#305;kalar sayesinde g&ouml;r&uuml;n&uuml;r veya g&ouml;r&uuml;nmez olabilen bloklar olu&#351;turman&#305;za olanak tan&#305;r.
@puce@ {{SPIP metinlerinde}} : yazarlar i&ccedil;in yeni komutlar sunulmu&#351;tur &lt;bloc&gt; (ou &lt;invisible&gt;) et &lt;visible&gt; bu komutlar&#305; &#351;u bi&ccedil;imde metinlerinde kullanabilirler : 

<quote><code>
<bloc>
 T&#305;klanabilir ba&#351;l&#305;k
 
 G&ouml;sterilecek/gizlenecek metin, 2 sat&#305;r bo&#351;luk...
 </bloc>
</code></quote>

@puce@ {{&#304;skeletlerde}} : yeni komutlar &#351;unlard&#305;r #BLOC_TITRE, #BLOC_DEBUT ve #BLOC_FIN , &#351;u bi&ccedil;imde kullanabilirsiniz : 
<quote><code> #BLOC_TITRE veya #BLOC_TITRE{bana_ait_URL}
 Bana ait ba&#351;l&#305;k
 #BLOC_RESUME    (se&ccedil;imlik)
 takip eden blo&#287;un bir &ouml;zeti
 #BLOC_DEBUT
 Katlanabilir blo&#287;um (istenirse hedef URL\'yi g&ouml;sterecektir)
 #BLOC_FIN</code></quote>
',
	'blocs:nom' => 'Katlanabilir Bloklar (D&eacute;pliables)',
	'boites_privees:description' => 'A&#351;a&#287;&#305;da tan&#305;mlanan t&uuml;m kutular &ouml;zel alanda g&ouml;r&uuml;n&uuml;rler. [[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305; g&ouml;zden ge&ccedil;irmeleri}} : ([Source->@_CS_RSS_SOURCE@]) eklentisinin kodlar&#305;nda yap&#305;lan son de&#287;i&#351;iklikler akt&uuml;el konfig&uuml;rasyon sayfas&#305;nda bir &ccedil;er&ccedil;eve i&ccedil;inde g&ouml;sterilirler.
- {{SPIP format&#305;ndaki makaleler}} : yazarlar&#305; taraf&#305;ndan kullan&#305;lan kaynak kodlar&#305; g&ouml;rebilmeniz i&ccedil;in katlanabilir ek bir &ccedil;er&ccedil;eve.
- {{stat durumundaki yazarlar}} : [yazarlar sayfas&#305;nda->./?exec=auteurs] ba&#287;lanan son 10 kullan&#305;c&#305;y&#305; ve onaylanmam&#305;&#351; kay&#305;tlar&#305; g&ouml;steren &#287;m bir &ccedil;er&ccedil;eve. Bu bilgileri sadece y&ouml;neticiler g&ouml;rebilir.',
	'boites_privees:nom' => '&Ouml;zel kutular',

	// C
	'categ:admin' => '1. Y&ouml;netim',
	'categ:divers' => '6. Di&#287;er',
	'categ:interface' => '10. &Ouml;zel aray&uuml;z',
	'categ:public' => '4. Kamusal g&ouml;sterim',
	'categ:spip' => '5. Komutlar, filtreler, kriterler',
	'categ:typo-corr' => '2. Metin geli&#351;tirmeleri',
	'categ:typo-racc' => '3. Tipografik K&#305;saltmalar',
	'certaines_couleurs' => 'Sadece a&#351;a&#287;&#305;da tan&#305;mlanan komutlar @_CS_ASTER@ :',
	'chatons:aide' => '<NEW>Chatons : @liste@',
	'chatons:description' => '<NEW>Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <code>:nom</code>.
_ Cet outil remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => '<NEW>Chatons',
	'class_spip:description1' => 'Burada baz&#305; SPIP k&#305;sayollar&#305;n&#305; tan&#305;mlayabilirsiniz. Bo&#351; bir de&#287;er "Varsay&#305;lan&#305; kullan" anlam&#305;ndad&#305;r.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{SPIP k&#305;sayollar&#305;}}.

Burada baz&#305; SPIP k&#305;sayollar&#305;n&#305; tan&#305;mlayabilirsiniz. Bo&#351; de&#287;er varsay&#305;lan de&#287;erin kullan&#305;lmas&#305; demektir.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

SPIP normalde ara ba&#351;l&#305;klar i&ccedil;in &lt;h3&gt; komutunu kullan&#305;r. Burada ba&#351;ka bir komut se&ccedil;iniz :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '<NEW>

SPIP a choisi d\'utiliser la balise &lt;i> pour transcrire les italiques. Mais &lt;em> aurait pu &eacute;galement convenir. &Agrave; vous de voir :[[%racc_i1%]][[->%racc_i2%]]
Notez : en modifiant le remplacement des raccourcis d\'italiques, le style {{2.}} sp&eacute;cifi&eacute; plus haut ne sera pas appliqu&eacute;.

@puce@ {{Les styles de SPIP}}. Jusqu\'&agrave; la version 1.92 de SPIP, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style "spip". Par exemple : <code><p class="spip"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu\'aucun style particulier ne sera appliqu&eacute;.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[%style_h%]]

Notez : en modifiant ce deuxi&egrave;me param&egrave;tre, vous perdez alors les styles standards associ&eacute;s &agrave; ces balises.</blockquote>',
	'class_spip:nom' => 'SPIP ve k&#305;sayollar&#305;…',
	'code_css' => 'CSS',
	'code_fonctions' => '&#304;&#351;levler',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Se&ccedil;enekler',
	'contrib' => 'Daha fazla bilgi : @url@',
	'couleurs:aide' => 'Renklendirme : <b>[coul]metin[/coul]</b>@fond@ ile <b>coul</b> = @liste@',
	'couleurs:description' => 'K&#305;sayollar&#305;n i&ccedil;inde komutlar kullanarak sitedeki t&uuml;m metinlere renk uygulanmas&#305;na olanak tan&#305;r (makaleler, k&#305;sa haberler, ba&#351;l&#305;klar, forum, ...).

Metin rengini de&#287;i&#351;tirmek i&ccedil;in 2 e&#351;de&#287;er &ouml;rnek:@_CS_EXEMPLE_COULEURS2@

Fon rengini de&#287;i&#351;tirmek i&ccedil;in (e&#287;er yukar&#305;daki se&ccedil;enek izin veriyorsa) :@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@Bu ki&#351;iselle&#351;tirilmi&#351; komutlar&#305;n format&#305; mevcut renkleri listelemeli veya &laquo;komut=renk&raquo; ikililerini virg&uuml;lle ayr&#305;lm&#305;&#351; bi&ccedil;imde tan&#305;mlamal&#305;d&#305;r. &Ouml;rnek : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; veya &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. &#304;lk ve son &ouml;rnekler i&ccedil;in izin verilen komutlar &#351;unlard&#305;r : <code>[gris]</code> ve <code>[rouge]</code> (<code>[fond gris]</code> ve <code>[fond rouge]</code> - e&#287;er fona izin verilmi&#351;se -).',
	'couleurs:nom' => 'Hepsi renkli',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]metin[/coul]</b>, <b>[bg&nbsp;coul]metin[/coul]</b>',

	// D
	'decoration:aide' => 'Dekorasyon&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b> ile <b>balise</b> = @liste@',
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
	'decoration:nom' => 'Dekorasyon',
	'decoupe:aide' => 'T&#305;rnak blo&#287;u : <b>&lt;onglets>&lt;/onglets></b><br/>Sayfa veya t&#305;rnak ayrac&#305;&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias&nbsp;:&nbsp;@sep@',
	'decoupe:description' => '<MODIF>Bir makalenin, otomatik bir sayfalama ile kamusal alanda bir ka&ccedil; sayfaya b&ouml;l&uuml;nerek g&ouml;sterilmesini sa&#287;lar. Makalenizde sadece pe&#351;pe&#351;e art&#305; i&#351;aretlerini(<code>++++</code>) kesinti yap&#305;lacak yerde kullan&#305;n.
_ E&#287;er bu ayrac&#305; &lt;onglets&gt; ve &lt;/onglets&gt; komutlar&#305;yla kullan&#305;rsan&#305;z bir &ccedil;ift t&#305;rnak elde edersiniz.
_ &#304;skeletlerde : &#351;u yeni komutlara sahipsiniz #ONGLETS_DEBUT, #ONGLETS_TITRE ve #ONGLETS_FIN.
_ Bu gere&ccedil; {makaleleriniz i&ccedil;in bir &ouml;zet} ile birlikte kullan&#305;labilir.',
	'decoupe:nom' => 'Sayfalara ve ba&#351;l&#305;klara ay&#305;r',
	'desactiver_flash:description' => 'Sitenizin web sayfalar&#305;ndaki flash nesneleri siler ve ilintili alternatif i&ccedil;erikler de&#287;i&#351;tiri.',
	'desactiver_flash:nom' => 'Flash nesnelerini deaktive eder',
	'detail_balise_etoilee' => '{{Dikkat}} : V&eacute;rifiez bien l\'utilisation faite par vos squelettes des balises &eacute;toil&eacute;es. Les traitements de cet outil ne s\'appliqueront pas sur : @bal@.',
	'detail_fichiers' => 'Dosyalar :',
	'detail_inline' => 'Inline kod :',
	'detail_jquery1' => '{{Dikkat}} : bu alet SPIP\'in bu s&uuml;r&uuml;m&uuml;yle &ccedil;al&#305;&#351;abilmek i&ccedil;in {jQuery} eklentisini gerektirir.',
	'detail_jquery2' => 'Bu alet {jQuery} k&uuml;t&uuml;phanesini gerektirir.',
	'detail_pipelines' => 'Boru hatlar&#305; (pipeline) :',
	'detail_traitements' => '&#304;&#351;lemler :',
	'dossier_squelettes:description' => 'Kullan&#305;lan iskelet dizinini de&#287;i&#351;tirir. &Ouml;rne&#287;in : "squelettes/iskeletim". Dizinleri iki nokta ile ay&#305;rarak bir &ccedil;ok dizin belirtebilirsiniz  <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. &#304;zleyen kutuyu bo&#351; b&#305;rakarak (veya "dist" yazarak) SPIP taraf&#305;ndan sunulan orijinal "dist" iskeletini kullanabilirsiniz. [[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => '&#304;skelet dosyas&#305;',

	// E
	'effaces' => 'Silinmi&#351;',
	'en_travaux:description' => 'T&uuml;m kamusal sitede bak&#305;m yap&#305;l&#305;rken ki&#351;iselle&#351;tirilebilir bir mesaj yay&#305;nlanmas&#305;n&#305; sa&#287;lar.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Sitede &ccedil;al&#305;&#351;ma var',
	'erreur:description' => 'Alet tan&#305;m&#305;nda id eksik !',
	'erreur:distant' => 'uzak sunucu',
	'erreur:js' => 'bu sayfada bir JavaScript hatas&#305; olu&#351;tu ve sayfan&#305;n do&#287;ru &ccedil;al&#305;&#351;mas&#305;n&#305; engelliyor. L&uuml;tfen gezgininizde JavaScript\'i aktive edin veyasitenizdeki baz&#305; SPIP eklentilerini deaktive edin.',
	'erreur:nojs' => 'JavaScript bu sayfada deaktive edilmi&#351;.',
	'erreur:nom' => 'Hata !',
	'erreur:probleme' => 'Sorun var : @pb@',
	'erreur:traitements' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305; - Derleme hatas&#305; : \'typo\' ve \'propre\' kar&#305;&#351;&#305;m&#305; yasakt&#305;r !',
	'erreur:version' => 'Bu gere&ccedil; SPIP\'in bu s&uuml;r&uuml;m&uuml;nde mevcut de&#287;il.',
	'etendu' => 'Kapsam',

	// F
	'f_jQuery:description' => '{jQuery}\'nin kamusal alana kurulmas&#305;n&#305; engeller, b&ouml;ylece &laquo;makine zaman&#305;&raquo;ndan biraz ekonomi yapar. Bu ([->http://jquery.com/]) kitapl&#305;&#287;&#305; Javascript programlamada bir &ccedil;ok kolayl&#305;k getirir ve baz&#305; eklentilerde kullan&#305;labilir. SPIP, Jquery\'yi &ouml;zel alanda kullan&#305;r.

Dikkat : baz&#305; &#304;svi&ccedil;re &Ccedil;ak&#305;s&#305; gere&ccedil;leri {jQuery} fonksiyonlar&#305;na ihtiya&ccedil; duyar. ',
	'f_jQuery:nom' => 'jQuery\'yi deaktive eder',
	'filets_sep:aide' => 'Ay&#305;rma Fileleri (Filet)&nbsp;: <b>__i__</b>  <b>i</b> burada bir say&#305;y&#305; temsil eder.<br />Di&#287;er fileler : @liste@',
	'filets_sep:description' => '<NEW>Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de SPIP.
_ La syntaxe est : "__code__", o&ugrave; "code" repr&eacute;sente soit le num&eacute;ro d’identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d\'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Ay&#305;rma Filesi (Filet)',
	'filtrer_javascript:description' => 'Makalelerde javascript kullan&#305;m&#305; i&ccedil;in 3 metod vard&#305;r :
- <i>jamais</i> : javascript her yerde reddedilir
- <i>d&eacute;faut</i> : javascript &ouml;zel alanda k&#305;rm&#305;z&#305; ile belirtilir 
- <i>toujours</i> : javascript her yerde kab&ucirc;l edilir.

Dikkat : forumlarda, dilek&ccedil;elerde, payla&#351;&#305;lan ak&#305;larda ve benzerlerinde javascript\'in y&ouml;netimi <b>daima</b> g&uuml;venlidir.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Javascript y&ouml;netimi',
	'flock:description' => 'PHP fonksiyonunu n&ouml;tralize ederek {flock()} dosya kilitleme sistemini deaktive eder. Baz&#305; bar&#305;nd&#305;rma firmalar&#305; uyumsuz dosya sistemi veya senkronizasyon eksikli&#287;i y&uuml;z&uuml;nden b&uuml;y&uuml;k sorunlara yol a&ccedil;maktad&#305;r. E&#287;er siteniz normal &ccedil;al&#305;&#351;&#305;yorsa bunu aktive etmeyin.',
	'flock:nom' => 'Dosya kilitleme yok',
	'fonds' => 'Arka alanlar :',
	'forcer_langue:description' => 'Dil cookie\'sini y&ouml;netmeyi bilen bir dil men&uuml;s&uuml; veya bir form i&ccedil;eren &ccedil;ok dilli iskelet tak&#305;m&#305;na sahip dile zorla',
	'forcer_langue:nom' => 'Bu dile zorla',
	'format_spip' => 'SPIP format&#305;nda makaleler',
	'forum_lgrmaxi:description' => 'Varsay&#305;lan olarak, forum mesajlar&#305;n&#305;n boyu s&#305;n&#305;rlanmam&#305;&#351;t&#305;r. Bu gere&ccedil; aktive edildi&#287;inde, bir kullan&#305;c&#305; belirtilen boydan daha uzun bir mesaj g&ouml;ndermek istedi&#287;inde bir hata mesaj&#305; g&ouml;r&uuml;lecektir ve mesaj reddedilecektir. Bo&#351; bir de&#287;er veya S&#305;f&#305;r boyda s&#305;n&#305;r olmad&#305;&#287;&#305;n&#305; belirtir. [[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Forumlar&#305;n boyutu',

	// G
	'glossaire:description' => '<NEW>@puce@ Gestion d’un glossaire interne li&eacute; &agrave; un ou plusieurs groupes de mots-cl&eacute;s. Inscrivez ici le nom des groupes en  les s&eacute;parant par les deux points &laquo;&nbsp;:&nbsp;&raquo;. En laissant vide la case qui  suit (ou en tapant "Glossaire"), c’est le groupe "Glossaire" qui sera utilis&eacute;.[[%glossaire_groupes%]]@puce@ Pour chaque mot, vous avez la possibilit&eacute; de choisir le nombre maximal de liens cr&eacute;&eacute;s dans vos textes. Toute valeur nulle ou n&eacute;gative implique que tous les mots reconnus seront trait&eacute;s. [[%glossaire_limite% par mot-cl&eacute;]]@puce@ Deux solutions vous sont offertes pour g&eacute;n&eacute;rer la petite fen&ecirc;tre automatique qui apparait lors du survol de la souris. [[%glossaire_js%]]',
	'glossaire:nom' => '&#304;&ccedil; endeks',
	'glossaire_css' => 'CSS &ccedil;&ouml;z&uuml;m&uuml;',
	'glossaire_js' => 'Javascript &ccedil;&ouml;z&uuml;m&uuml;',
	'guillemets:description' => 'Normal t&#305;rnak i&#351;aretlerini (") tipografik t&#305;rnak i&#351;aretleriyle de&#287;i&#351;tirir. De&#287;i&#351;tirme kullan&#305;c&#305; taraf&#305;ndan g&ouml;r&uuml;lmez ve orijinal metni etkilemez sadece g&ouml;sterilen metni etkiler.',
	'guillemets:nom' => 'Tipografik t&#305;rnaklar',

	// H
	'help' => '{{Bu sayfa yaln&#305;z site sorumlular&#305;n&#305;n eri&#351;imine a&ccedil;&#305;kt&#305;r.}} &laquo;{{&#304;svi&ccedil;re&nbsp;&Ccedil;ak&#305;s&#305;}}&raquo; eklentisinin getirdi&#287;i farkl&#305; bir &ccedil;ok ek i&#351;levin d&uuml;zenlenmesine izin verir .',
	'help0' => '{{Bu sayfa sadece site sorumlusunun eri&#351;imine a&ccedil;&#305;kt&#305;r.}}<p>&laquo;{{&#304;svi&ccedil;re&nbsp;&Ccedil;ak&#305;s&#305;}}&raquo; eklentisinin fonksiyonlar&#305;na eri&#351;im olana&#287;&#305; tan&#305;r.</p><p>Belgeleme ba&#287;lant&#305;s&#305; :<br/>• [&#304;svi&ccedil;re&nbsp;&Ccedil;ak&#305;s&#305;->http://www.spip-contrib.net/?article2166]</p><p>S&#305;f&#305;rlama (Yeniden ba&#351;latma) :
_ • [T&uuml;m eklentiyi ba&#351;tan->@reset@]
</p>',
	'help2' => 'Yerel s&uuml;r&uuml;m : @version@',
	'help3' => 'Belgelendirme ba&#287;lant&#305;lar&#305; :<br/>• [&#304;svi&ccedil;re&nbsp;&Ccedil;ak&#305;s&#305;->http://www.spip-contrib.net/?article2166]@contribs@</p><p>Yeniden ba&#351;lat&#305;lmas&#305; :
_ • [Gizli gere&ccedil;lerin|Bu sayfan&#305;n ilk g&ouml;r&uuml;n&uuml;m&uuml;ne d&ouml;n&uuml;lmesi->@hide@]
_ • [T&uuml;m eklentinin|Eklentini ilk durumuna d&ouml;n&uuml;lmesi->@reset@]@install@
</p>',

	// I
	'icone_visiter:description' => 'Standart &laquo;&nbsp;Ziyaret Et&nbsp;&raquo; d&uuml;&#287;mesini (bu sayfan&#305;n sa&#287; &uuml;st&uuml;ndeki) e&#287;er varsa site logosu ile de&#287;i&#351;tirir.

Bu logoyu tan&#305;mlamak i&ccedil;in &laquo;&nbsp;Konfig&uuml;rasyon&nbsp;&raquo; d&uuml;&#287;mesine t&#305;klayarak &laquo;&nbsp;Site konfig&uuml;rasyonu&nbsp;&raquo; sayfas&#305;na gidiniz.',
	'icone_visiter:nom' => '&laquo;&nbsp;Ziyaret Et&nbsp;&raquo; d&uuml;&#287;mesi',
	'insert_head:description' => '[#INSERT_HEAD->http://www.spip.net/fr_article1902.html] komutunu t&uuml;m iskeletlerde aktif hale getirir (bu komutu &lt;head&gt; ve &lt;/head&gt; aras&#305;nda i&ccedil;erseler de i&ccedil;ermeseler de). Bu se&ccedil;enek sayesinde eklentiler javascript (.js) veya stil sayfas&#305; (.css) ekleyebilirler.',
	'insert_head:nom' => '#INSERT_HEAD komutu',
	'insertions:description' => 'DiKKAT : geli&#351;tirilmekte olan gere&ccedil; !! [[%insertions%]]',
	'insertions:nom' => 'Otomatik d&uuml;zeltmeler',
	'introduction:description' => '<MODIF><NEW>Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Dikkat}} : Bu i&#351;levi aktive etmeden &ouml;nce iskeletinizde veya eklentilerinizde hi&ccedil;bir {balise_INTRODUCTION()} fonksiyonunun  olmad&#305;&#287;&#305;ndan emin olun, aksi halde derleme hatas&#305; olu&#351;acakt&#305;r.</p>
@puce@ #INTRODUCTION komutu taraf&#305;ndan g&ouml;nderilen metnin uzunlu&#287;unu (varsay&#305;lan de&#287;ere g&ouml;re y&uuml;zde olarak) belirtebilirsiniz. Bo&#351; bir de&#287;er veya 100 de&#287;eri metni de&#287;i&#351;tirmeyecektir ve &#351;u varsay&#305;lan de&#287;erleri kullanacakt&#305;r : makaleler i&ccedil;in 500 karakter, k&#305;sa haberler i&ccedil;in 300karakter, forumlar veya ba&#351;l&#305;klar i&ccedil;in 600 karakter.
[[%lgr_introduction%&nbsp;%]]
@puce@ E&#287;er metin &ccedil;ok uzunsa, #INTRODUCTION komutuna eklenen varsay&#305;lan &uuml;&ccedil; nokta &#351;&ouml;yledir : <html>&laquo;&amp;nbsp;(…)&raquo;</html>. Burada, metnin kesildi&#287;ini ve devam&#305; oldu&#287;unu siz kendi &ouml;zel karakter zincirinizi kullanarak okuyucular&#305;n&#305;za belirtebilirsiniz.
[[%suite_introduction%]]
@puce@ Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l\'article…&raquo;
[[%lien_introduction%]]
',
	'introduction:nom' => '#INTRODUCTION komutu',

	// J
	'jcorner:description' => '<NEW>&laquo;&nbsp;Jolis Coins&nbsp;&raquo; est un outil permettant de modifier facilement l\'aspect des coins de vos {{cadres color&eacute;s}} en partie publique de votre site. Tout est possible, ou presque !
_ Voyez le r&eacute;sultat sur cette page : [->http://www.malsup.com/jquery/corner/].

Listez ci-dessous les objets de votre squelette &agrave; arrondir en utilisant la syntaxe CSS (.class, #id, etc. ). Utilisez le le signe &laquo;&nbsp;=&nbsp;&raquo; pour sp&eacute;cifier la commande jQuery &agrave; utiliser et un double slash (&laquo;&nbsp;//&nbsp;&raquo;) pour les commentaires. En absence du signe &eacute;gal, des coins ronds seront appliqu&eacute;s (&eacute;quivalent &agrave; : <code>.ma_classe = .corner()</code>).[[%jcorner_classes%]]

Attention, cet outil a besoin pour fonctionner du plugin {jQuery} : {Round Corners}. Le Couteau Suisse peut l\'installer directement si vous cochez la case suivante. [[%jcorner_plugin%]]',
	'jcorner:nom' => '<NEW>Jolis Coins',
	'jcorner_plugin' => '<NEW>&laquo;&nbsp;Round Corners plugin&nbsp;&raquo;',
	'js_defaut' => 'Varsay&#305;lan',
	'js_jamais' => 'Asla',
	'js_toujours' => 'Daima',

	// L
	'label:admin_travaux' => 'Kamusal alan&#305; &#351;una kapat :',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => '&Ouml;zet\'in sistemli bi&ccedil;imde olu&#351;turulmas&#305; :',
	'label:balise_sommaire' => '#CS_SOMMAIRE komutunu aktive et :',
	'label:couleurs_fonds' => 'Arka alanlara izin ver :',
	'label:cs_rss' => 'Aktive et :',
	'label:decoration_styles' => 'Ki&#351;iselle&#351;tirilmi&#351; stil komutlar&#305;n&#305;z :',
	'label:dossier_squelettes' => 'Kullan&#305;lacak dizinler :',
	'label:duree_cache' => 'Yerel &ouml;nbelle&#287;in s&uuml;resi :',
	'label:duree_cache_mutu' => '&Ouml;n bellek s&uuml;resi :',
	'label:forum_lgrmaxi' => 'De&#287;er (karakter cinsinden) :',
	'label:glossaire_groupes' => 'Kullan&#305;lan gruplar :',
	'label:glossaire_js' => 'Kullan&#305;lan teknik :',
	'label:glossaire_limite' => 'Olu&#351;turulan maksimum ba&#287; :',
	'label:insertions' => 'Otomatik d&uuml;zeltmeler :',
	'label:jcorner_classes' => '<NEW>Am&eacute;liorer les coins des s&eacute;lecteurs suivantes :',
	'label:jcorner_plugin' => '<NEW>Installer le plugin {jQuery} suivant :',
	'label:lgr_introduction' => '&Ouml;zet\'in uzunlu&#287;u :',
	'label:lgr_sommaire' => '&Ouml;zet\'in b&uuml;y&uuml;kl&uuml;&#287;&uuml; (9 &agrave; 99) :',
	'label:lien_introduction' => 'T&#305;klanabilir &uuml;&ccedil; nokta :',
	'label:liens_interrogation' => '&#350;u URL\'leri koru :',
	'label:liens_orphelins' => 'T&#305;klanabilir ba&#287;lar :',
	'label:max_auteurs_page' => 'Bir sayfadaki yazar adedi :',
	'label:message_travaux' => 'Bak&#305;m mesaj&#305;n&#305;z :',
	'label:paragrapher' => 'Daima paragraflanmal&#305; :',
	'label:puce' => 'Kamusal ikon &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Kota de&#287;eri :',
	'label:racc_h1' => ' &laquo;<html>{{{intertitre}}}</html>&raquo; giri&#351; &ccedil;&#305;k&#305;&#351;&#305; :',
	'label:racc_hr' => 'Yatay &ccedil;izgi &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => '&laquo;<html>{italique}</html>&raquo; giri&#351; &ccedil;&#305;k&#305;&#351;&#305;:',
	'label:radio_desactive_cache3' => '&Ouml;nbelle&#287;i deaktive et :',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'D&#305;&#351; ba&#287;lar i&ccedil;in yeni pencere:',
	'label:radio_type_urls3' => 'URL\'lerin format&#305; :',
	'label:scrollTo' => 'Eklentileri {jQuery} kur :',
	'label:set_couleurs' => 'Kullan&#305;lacak set :',
	'label:spam_mots' => 'Yasaklanan diziler :',
	'label:spip_script' => '&Ccedil;a&#287;r&#305; script\'i :',
	'label:style_h' => 'Stiliniz :',
	'label:style_p' => 'Stiliniz :',
	'label:suite_introduction' => '&Uuml;&ccedil; nokta  :',
	'label:terminaison_urls_arbo' => 'URl soyadlar&#305; (&ouml;r : .html) :',
	'label:titre_travaux' => 'Mesaj&#305;n ba&#351;l&#305;&#287;&#305; :',
	'label:tri_articles' => 'Se&ccedil;iminiz :',
	'label:url_arbo_minuscules' => 'URL\'lerde k&uuml;&ccedil;&uuml;k b&uuml;y&uuml;k harfleri koru :',
	'label:url_arbo_sep_id' => 'Tekrarlama durumunda kullan&#305;lacak ayra&ccedil; \'titre-id\' :<br/>(\'/\' kullanmay&#305;n)',
	'label:url_glossaire_externe2' => 'D&#305;&#351; s&ouml;zl&uuml;&#287;e ba&#287; :',
	'label:urls_arbo_sans_type' => 'URL\'lerde SPIP nesnesinin tipini g&ouml;ster :',
	'liens_en_clair:description' => '<NEW>Met &agrave; votre disposition le filtre : \'liens_en_clair\'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d\'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre \'cs=print\' ou \'page=print\' dans l\'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.',
	'liens_en_clair:nom' => 'A&ccedil;&#305;kta b&#305;rak&#305;lm&#305;&#351; ba&#287;lar',
	'liens_orphelins:description' => 'Bu gerecin 2 i&#351;levi vard&#305;r:

@puce@ {{Do&#287;ru ba&#287;lar}}.

SPIP, frans&#305;z gramerine ba&#287;l&#305; olarak soru ve &uuml;nlem i&#351;aretlerinden &ouml;nce bir bo&#351;luk b&#305;rak&#305;r. &#304;&#351;te size metinlerinizde bulunan URL\'lerdeki soru i&#351;aretlerini koruyan bir gere&ccedil;.[[%liens_interrogation%]]

@puce@ {{Yetim ba&#287;lar}}.

Kullan&#305;c&#305;lar taraf&#305;ndan metin olarak b&#305;rak&#305;lm&#305;&#351; \'t&#305;klanamayan\' t&uuml;m URL\'leri sistemli bi&ccedil;imde SPIP format&#305;nda hipermetin ba&#287;lar&#305;yla de&#287;i&#351;tirir (&ouml;zellikle forumlarda). &Ouml;rne&#287;in : {<html>www.spip.net</html>} [->www.spip.net] ile de&#287;i&#351;tirilir.

De&#287;i&#351;tirme tipini siz se&ccedil;ebilirsiniz :
_ • {Temel} : {<html>http://spip.net</html>} (t&uuml;m  protokoller) veya  {<html>www.spip.net</html>} de&#287;i&#351;tirilir.
_ • {Yayg&#305;n} : &#351;u tipteki ba&#287;lar da de&#287;i&#351;tirilir {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} veya {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'G&uuml;zel URL\'ler',
	'log_couteau_suisse:description' => '\'&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\' eklentisinin &ccedil;al&#305;&#351;mas&#305;na dair bir &ccedil;ok bilgiyi spip.log dosyalar&#305;na yazar. Bu dosyalar &#351;u dizindedir : @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\'n&#305;n detayl&#305; taporu',

	// M
	'mailcrypt:description' => 'Metinlerinizde bulunan t&uuml;m ba&#287;lar&#305; maskeler ve bir Javascript ba&#287; yard&#305;m&#305;yla okuyucunun mesajla&#351;mas&#305;n&#305; aktive etme olana&#287;&#305; tan&#305;r. Bu anti-spam gereci robotlar&#305;n, forumlarda veya iskeletlerde kullan&#305;lan komutlarda a&ccedil;&#305;kta b&#305;rak&#305;lan elektronik adresleri toplamas&#305;n&#305; engellemeye &ccedil;al&#305;&#351;&#305;r.',
	'mailcrypt:nom' => 'MailCrypt',
	'modifier_vars' => '@nb@ parametreyi de&#287;i&#351;tir',

	// N
	'no_IP:description' => '&Ouml;zel bilgilerin korunmas&#305; endi&#351;esiyle sitenizi ziyaret edenlerin IP adreslerinin otomatik olarak kaydeilmesi i&#351;lemini durdurur : SPIP art&#305;k istatistikler i&ccedil;in veya spip.log dosyas&#305; i&ccedil;in forumlarda bile ziyaretler esnas&#305;nda ge&ccedil;ici olarak hi&ccedil;bir IP numaras&#305;n&#305; saklamayacakt&#305;r.',
	'no_IP:nom' => 'IP kayd&#305; yapma',
	'nouveaux' => 'Yeni',

	// O
	'orientation:description' => '&#304;skeletleriniz i&ccedil;in 3 yeni kriter : <code>{portrait}</code>, <code>{carre}</code> ve <code>{paysage}</code>. Foto&#287;raflar&#305;n &#351;ekilleri bak&#305;m&#305;ndan s&#305;n&#305;fland&#305;r&#305;lmas&#305; i&ccedil;in ideal.',
	'orientation:nom' => 'Resimlerin y&ouml;n&uuml;',
	'outil_actif' => 'Aktif alet',
	'outil_activer' => 'Aktive et',
	'outil_activer_le' => 'Aleti aktive et',
	'outil_cacher' => 'Art&#305;k g&ouml;sterme',
	'outil_desactiver' => 'Deaktive et',
	'outil_desactiver_le' => 'Aleti deaktive et',
	'outil_inactif' => '&#304;naktif aktif',
	'outil_intro' => '<NEW>Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition.<br /><br />En cliquant sur le nom des outils ci-dessous, vous s&eacute;lectionnez ceux dont vous pourrez permuter l\'&eacute;tat &agrave; l\'aide du bouton central : les outils activ&eacute;s seront d&eacute;sactiv&eacute;s et <i>vice versa</i>. &Agrave; chaque clic, la description apparait au-dessous des listes. Les cat&eacute;gories sont repliables et les outils peuvent &ecirc;tre cach&eacute;s. Le double-clic permet de permuter rapidement un outil.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d\'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d\'autres plugins.<br /><br />Note : le simple chargement de cette page recompile l\'ensemble des outils du Couteau Suisse.',
	'outil_intro_old' => 'Bu aray&uuml;z eski.<br /><br />E&#287;er <a href=\'./?exec=admin_couteau_suisse\'>yeni aray&uuml;z</a>\'&uuml;n kullan&#305;m&#305;nda sorunla kar&#351;&#305;la&#351;&#305;rsan&#305;z, bizle <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a> forumunda payla&#351;maktan &ccedil;ekinmeyin.',
	'outil_nb' => '@pipe@ : @nb@ alet',
	'outil_nbs' => '@pipe@ : @nb@ alet',
	'outil_permuter' => '&laquo; @text@ &raquo; gereci de&#287;i&#351;tirilsin mi ?',
	'outils_actifs' => 'Aktif aletler :',
	'outils_caches' => 'Sakl&#305; aletler :',
	'outils_cliquez' => 'Yukar&#305;daki gere&ccedil;lerin a&ccedil;&#305;klamalar&#305;n&#305; g&ouml;rmek i&ccedil;in isimlerine t&#305;klay&#305;n&#305;z.',
	'outils_inactifs' => '&#304;naktif aletler :',
	'outils_liste' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305; aletleri listesi ',
	'outils_permuter_gras1' => 'Koyu yaz&#305;l&#305; aletleri &ccedil;aprazla (Permuter)',
	'outils_permuter_gras2' => 'Koyu @nb@ gere&ccedil;leri de&#287;i&#351;tirilsin mi?',
	'outils_resetselection' => 'Se&ccedil;imleri ba&#351;tan al',
	'outils_selectionactifs' => 'T&uuml;m aktif aletleri se&ccedil;',
	'outils_selectiontous' => 'HEPS&#304;',

	// P
	'pack_alt' => 'Aktif konfig&uuml;rasyonun parametrelerini g&ouml;ster',
	'pack_descrip' => '<NEW>Votre "Pack de configuration actuelle" rassemble l\'ensemble des param&egrave;tres de configuration en cours concernant le Couteau Suisse : l\'activation des outils et la valeur de leurs &eacute;ventuelles variables.

Ce code PHP peut prendre place dans le fichier /config/mes_options.php et ajoutera un lien de r&eacute;initialisation sur cette page "du pack {Pack Actuel}". Bien s&ucirc;r il vous est possible de changer son nom ci-dessous.

Si vous r&eacute;initialisez le plugin en cliquant sur un pack, le Couteau Suisse se reconfigurera automatiquement en fonction des param&egrave;tres pr&eacute;d&eacute;finis dans le pack.',
	'pack_du' => '• @pack@ paketinin',
	'pack_installe' => 'Bir konfig&uuml;rasyon paketini y&uuml;kle',
	'pack_titre' => 'Akt&uuml;el Konfig&uuml;rasyon',
	'par_defaut' => 'Varsay&#305;lan',
	'paragrapher2:description' => '<NEW>La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Paragrafla',
	'pipelines' => 'Kullan&#305;lan boru hatlar&#305;&nbsp;:',
	'pucesli:description' => 'Makalelerdeki &laquo;-&raquo; (basit tire) i&#351;aretlerini &laquo;-*&raquo; ile de&#287;i&#351;tirir (HTML\'e : &lt;ul>&lt;li>…&lt;/li>&lt;/ul> olarak &ccedil;evrilir). Bunlar&#305;n bi&ccedil;imi css ile ki&#351;iselle&#351;tirilebilir.',
	'pucesli:nom' => 'G&uuml;zel simgeler',

	// R
	'raccourcis' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\'n&#305;n aktif tipografik k&#305;sayollar&#305;&nbsp;:',
	'raccourcis_barre' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\'n&#305;n tipografik k&#305;sayollar&#305;',
	'reserve_admin' => 'Y&ouml;neticilere ayr&#305;lm&#305;&#351; eri&#351;im.',
	'rss_attente' => 'RSS bekleniyor...',
	'rss_desactiver' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\'n&#305;n "G&ouml;zden Ge&ccedil;irmeleri"ni deaktive et',
	'rss_edition' => 'RSS ak&#305;&#351;&#305;n&#305;n g&uuml;ncellenme tarihi :',
	'rss_titre' => '&laquo;&nbsp;&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;&nbsp;&raquo; geli&#351;tirilmekte :',
	'rss_var' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;\'n&#305;n "G&ouml;zden Ge&ccedil;irmeleri"',

	// S
	'sauf_admin' => 'Y&ouml;neticiler d&#305;&#351;&#305;nda herkes',
	'set_options:description' => '<NEW>S&eacute;lectionne d\'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[%radio_set_options4%]]',
	'set_options:nom' => '&Ouml;zel aray&uuml;z tipi',
	'sf_amont' => '<NEW>En amont',
	'sf_tous' => 'Hepsi',
	'simpl_interface:description' => 'Renkli ikonun &uuml;zerinden ge&ccedil;erken bir makalenin stat&uuml;s&uuml;n&uuml; h&#305;zl&#305; y&uuml;kleme men&uuml;s&uuml;n&uuml; dezaktive eder. E&#287;er \'client\' performas&#305;n&#305; artt&#305;rmak i&ccedil;in &ouml;zel bir aray&uuml;z istiyorsan&#305;z idealdir.',
	'simpl_interface:nom' => '&Ouml;zel aray&uuml;z&uuml;n hafifletilmesi',
	'smileys:aide' => 'G&uuml;len y&uuml;zler : @liste@',
	'smileys:description' => '<acronym>:-)</acronym> tipinde k&#305;sayol i&ccedil;eren t&uuml;m metinlere g&uuml;len y&uuml;z ekler. Forumlar i&ccedil;in ideal. 
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'G&uuml;len y&uuml;zler (smileys)',
	'soft_scroller:description' => '<NEW>Offre &agrave; votre site public un d&eacute;filement  adouci de la page lorsque le visiteur clique sur un lien pointant vers une ancre : tr&egrave;s utile pour &eacute;viter de se perdre dans une page complexe ou un texte tr&egrave;s long...

Attention, cet outil a besoin pour fonctionner de deux plugins {jQuery} : {ScrollTo} et {LocalScroll}. Le Couteau Suisse peut les installer directement si vous cochez les cases suivantes. [[%scrollTo%]][[->%LocalScroll%]]',
	'soft_scroller:nom' => 'Yumu&#351;ak &ccedil;apalar',
	'sommaire:description' => '<NEW>Construit un sommaire pour vos articles afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

@puce@ Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ &bull; Syst&eacute;matique pour chaque article (une balise <code>[!sommaire]</code> plac&eacute;e n&rsquo;importe o&ugrave; &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article cr&eacute;era une exception).
_ &bull; Uniquement pour les articles contenant la balise <code>[sommaire]</code>.

[[%auto_sommaire%]]

@puce@ Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d\'article automatiquement. Mais vous avez la possibilt&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe en pages et onglets}.',
	'sommaire:nom' => 'Makaleleriniz i&ccedil;in bir &ouml;zet',
	'sommaire_avec' => '&Ouml;zet i&ccedil;eren bir makale&nbsp;: <b>@racc@</b>',
	'sommaire_sans' => '&Ouml;zetsiz bir makale&nbsp;: <b>@racc@</b>',
	'spam:description' => 'Kamusal b&ouml;l&uuml;mde otomatik veya k&ouml;t&uuml; niyetli mesaj g&ouml;nderilmesine engel olmaya &ccedil;al&#305;&#351;&#305;r. Baz&#305; s&ouml;zc&uuml;kler ve &lt;a>&lt;/a> komutlar&#305; yasakt&#305;r.

Burada yasaklanacak serileri @_CS_ASTER@ aralar&#305;nda bir bo&#351;luk b&#305;rakarak listeleyiniz. [[%spam_mots%]]
@_CS_ASTER@Tek bir s&ouml;zc&uuml;&#287;&uuml; parantez i&ccedil;ine al&#305;n&#305;z. Bo&#351;luklar i&ccedil;eren bir deyimi t&#305;rnak i&ccedil;ine al&#305;n&#305;z.',
	'spam:nom' => 'SPAM\'a kar&#351;&#305; sava&#351;',
	'spip_cache:description' => '<MODIF>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[%radio_desactive_cache3%]]@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => 'SPIP ve &ouml;nbellek…',
	'stat_auteurs' => 'Stat durumundaki yazarlar',
	'statuts_spip' => 'Sadece &#351;u SPIP stat&uuml;s&uuml; :',
	'statuts_tous' => 'T&uuml;m stat&uuml;ler',
	'suivi_forums:description' => 'Bir makale yazar&#305;, ilintili kamusal forumda bir mesaj yay&#305;nland&#305;&#287;&#305;nda her zaman bilgilendirilir. Ama ayr&#305;ca &#351;unlar da bilgilendirilebilir : t&uuml;m forum kat&#305;l&#305;mc&#305;lar&#305; veya mesajlar&#305;n yazarlar&#305;.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Kamusal forumlar&#305;n izlenmesi',
	'supprimer_cadre' => 'Bu &ccedil;er&ccedil;eveyi kald&#305;r',
	'supprimer_numero:description' => '<NEW>Applique la fonction SPIP supprimer_numero() &agrave; l\'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d\'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Numaray&#305; sil',

	// T
	'titre' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305;',
	'titre_tests' => '&#304;svi&ccedil;re &Ccedil;ak&#305;s&#305; - Test sayfalar&#305;',
	'tous' => 'Hepsi',
	'toutes_couleurs' => 'Css stillerinin 36 rengi :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => '&Ccedil;ok dilli bloklar&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => '<MODIF>Bir makaleye kolayca &ccedil;ok dilli bloklar eklemek i&ccedil;in <code><:un_texte:></code> k&#305;sayolunu sunar.
_ Kullan&#305;lan SPIP fonksiyonu &#351;udur : <code>_T(\'un_texte\', 
flux)</code>.
_ Dil dosyalar&#305;nda \'un_texte\' de&#287;i&#351;keninin d&uuml;zg&uuml;n bi&ccedil;imde tan&#305;mland&#305;&#287;&#305;ndan ein olun.',
	'toutmulti:nom' => '&Ccedil;ok dilli bloklar',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Bu site &ccedil;ok yak&#305;nda tekrar yay&#305;na ba&#351;layacak.
_ Anlay&#305;&#351;&#305;n&#305;z i&ccedil;in te&#351;ekk&uuml;rler.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => '<NEW>En naviguant sur le site en partie priv&eacute;e ([->./?exec=auteurs]), choisissez ici le tri &agrave; utiliser pour afficher vos articles &agrave; l\'int&eacute;rieur de vos rubriques.

Les propositions ci-dessous sont bas&eacute;es sur la fonctionnalit&eacute; SQL \'ORDER BY\' : n\'utilisez le tri personnalis&eacute; que si vous savez ce que vous faites (champs disponibles : {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => 'Makalelerin s&#305;ralanmas&#305;',
	'tri_modif' => 'De&#287;i&#351;iklik tarihine g&ouml;re s&#305;ralama (ORDER BY date_modif DESC)',
	'tri_perso' => 'Ki&#351;iselle&#351;tirilmi&#351; SQL s&#305;ralamas&#305; ORDER BY :',
	'tri_publi' => 'Yay&#305;n tarihine g&ouml;re s&#305;ralama (ORDER BY date DESC)',
	'tri_titre' => 'Ba&#351;l&#305;&#287;a g&ouml;re s&#305;ralama (ORDER BY 0+titre,titre)',
	'type_urls:description' => '<NEW>@puce@ SPIP offre un choix sur plusieurs jeux d\'URLs pour fabriquer les liens d\'acc&egrave;s aux pages de votre site :
<div style="font-size:90%; margin:0 2em;">
- {{page}} : la valeur par d&eacute;faut depuis SPIP version 1.9x : <code>/spip.php?article123</code>.
- {{html}} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
- {{propre}} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
- {{propres2}} : l\'extension \'.html\' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
- {{arborescentes}} (&agrave; partir de SPIP 2.0 uniquement) les liens sont de type arborescent : <code>/secteur/rubrique1/rubrique2/article</code>.
- {{standard}} : URLs utilis&eacute;es par SPIP v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>.
- {{propres-qs}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.

</div>

Plus d\'infos : [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@pour utiliser les formats {html}, {propre}, {propre2} ou {arborescentes}, Recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site SPIP sous le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d\'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</p>

@puce@ {{Uniquement si vous utilisez le format {page} ci-dessus}}, alors il vous est possible de choisir le script d\'appel &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l\'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[%spip_script%]]

@puce@ {{Uniquement si vous utilisez les URLS {arborescentes} ci-dessus}}, alors il vous est possible d\'en personnaliser le rendu :</p>
[[%url_arbo_minuscules%]][[%urls_arbo_sans_type%]][[%url_arbo_sep_id%]][[%terminaison_urls_arbo%]]',
	'type_urls:nom' => 'URL\'lerin formatlar&#305;',
	'typo_exposants:description' => '<MODIF>Frans&#305;zca metinler : g&uuml;ncel k&#305;saltmalar&#305;n tipografik g&ouml;r&uuml;n&uuml;m&uuml;n&uuml; gerekli elemanlar&#305; &uuml;s\'e koyarak geli&#351;tirir, (b&ouml;ylece, {<acronym>Mme</acronym>} &#351;u hale gelir {M<sup>me</sup>}) ve s&#305;k&ccedil;a yap&#305;lan hatalar&#305; d&uuml;zeltir ({<acronym>2&egrave;me</acronym>} veya {<acronym>2me</acronym>} &#351;u hale gelir {2<sup>e</sup>}).
_ Burada elde edilen k&#305;saltmalar 2002 y&#305;l&#305;nda yay&#305;nlanan Ulusal Bas&#305;mevi standartlar&#305;na uygundur. [Buradaki bilgiler Frans&#305;zca i&ccedil;in ge&ccedil;erlidir. &Ccedil;N]',
	'typo_exposants:nom' => 'Tipografik &uuml;s\'ler',

	// U
	'url_arbo' => '<NEW>arborescentes@_CS_ASTER@',
	'url_html' => 'html@_CS_ASTER@',
	'url_page' => 'sayfa',
	'url_propres' => 'propres@_CS_ASTER@',
	'url_propres-qs' => 'propres-qs',
	'url_propres2' => 'propres2@_CS_ASTER@',
	'url_standard' => 'standart',

	// V
	'validez_page' => 'De&#287;i&#351;ikliklere eri&#351;mek i&ccedil;in :',
	'variable_vide' => '(Bo&#351;)',
	'vars_modifiees' => 'Veriler sorunsuz de&#287;i&#351;tirildi',
	'version_a_jour' => 'S&uuml;r&uuml;m&uuml;n&uuml;z g&uuml;ncel.',
	'version_distante' => 'En eski s&uuml;r&uuml;m...',
	'version_nouvelle' => 'Yeni s&uuml;r&uuml;m : @version@',
	'version_revision' => 'G&ouml;zden ge&ccedil;irme : @revision@',
	'version_update' => 'Otomatik g&uuml;ncelleme',
	'version_update_title' => 'Eklentinin son s&uuml;r&uuml;m&uuml;n&uuml; indirir ve otomatik g&uuml;ncellemeyi ba&#351;lat&#305;r',
	'verstexte:description' => '&#304;skeletleriniz i&ccedil;in, daha hafif sayfalar olu&#351;turman&#305;z&#305; sa&#287;layacak 2 filtre.
_ version_texte : birka&ccedil; &ouml;nemli komut d&#305;&#351;&#305;nda bir html sayfan&#305;n metin i&ccedil;eri&#287;ini al&#305;r.
_ version_plein_texte : bir html sayfan&#305;n t&uuml;m metin i&ccedil;eri&#287;ini al&#305;r.',
	'verstexte:nom' => 'Metin s&uuml;r&uuml;m&uuml;',
	'visiteurs_connectes:description' => '&#304;skeletiniz i&ccedil;in, kamusal sitedeki ziyaret&ccedil;i say&#305;s&#305;n&#305; g&ouml;steren bir programc&#305;k sunar.

Sayfalar&#305;n&#305;za yaln&#305;zca &#351;unu ekleyin  <code><INCLURE{fond=fonds/visiteurs_connectes}></code>.',
	'visiteurs_connectes:nom' => 'Ba&#287;l&#305; ziyaret&ccedil;iler',
	'voir' => 'Bkz : @voir@',
	'votre_choix' => 'Se&ccedil;iminiz :',

	// X
	'xml:description' => 'Xml onaylay&#305;c&#305;s&#305;n&#305;, kamusal alan i&ccedil;in [&#351;u belgede->http://www.spip.net/fr_article3541.html] belirtildi&#287;i gibi aktive eder. &laquo;&nbsp;Analyse XML&nbsp;&raquo; ba&#351;l&#305;kl&#305; bir d&uuml;&#287;me di&#287;er y&ouml;netim d&uuml;&#287;melerine eklenecektir.',
	'xml:nom' => 'XML onaylay&#305;c&#305;s&#305;'
);

?>
