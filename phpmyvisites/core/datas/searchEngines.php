<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: searchEngines.php,v 1.13 2006/01/13 19:14:04 matthieu_ Exp $



// Add your search engines here
// And submit them in phpMyVisites forums on http://www.phpmyvisites.net/forums/
// we'll add them in the next release !
$GLOBALS['searchEngines'] = array(

//" "		=> array(" ", " "),

// 1
"1.cz" 						=> array("1", "q"),
"www.1.cz" 					=> array("1", "q"),

// A9
"www.a9.com"				=> array("A9", ""),
"a9.com"					=> array("A9", ""),

// about
"search.about.com"			=> array("About", "terms"),

// AllTheWeb 
"www.alltheweb.com"         => array("AllTheWeb", "q"),

// all.by
"all.by"					=> array("All.by", "query"),

// Altavista
"listings.altavista.com"    => array("AltaVista", "q"),
"altavista.fr"				=> array("AltaVista", "q"),
"fr.altavista.com"			=> array("AltaVista", "q"),
"www.altavista.fr"			=> array("AltaVista", "q"),
"search.altavista.com"		=> array("AltaVista", "q"),
"search.fr.altavista.com"	=> array("AltaVista", "q"),
"se.altavista.com"			=> array("AltaVista", "q"),
"be-nl.altavista.com" 		=> array("AltaVista", "q"),
"be-fr.altavista.com" 		=> array("AltaVista", "q"),
"it.altavista.com" 			=> array("AltaVista", "q"),
"us.altavista.com" 			=> array("AltaVista", "q"),
"nl.altavista.com" 			=> array("Altavista", "q"),
"www.altavista.com"			=> array("AltaVista", "q"),

// AOL
"www.aolrecherche.aol.fr"	=> array("AOL", "q"),
"www.aolrecherches.aol.fr" 	=> array("AOL", "query"),
"www.aolimages.aol.fr"   	=> array("AOL", "query"),
"www.recherche.aol.fr"		=> array("AOL", "q"),
"aolsearch.aol.com"			=> array("AOL", "query"),
"aolsearcht.aol.com"		=> array("AOL", "query"),
"find.web.aol.com"			=> array("AOL", "query"),
"recherche.aol.ca"			=> array("AOL", "query"),
"aolsearch.aol.co.uk"		=> array("AOL", "query"),
"aolrecherche.aol.fr"		=> array("AOL", "q"),
"search.aol.com"			=> array("AOL", "query"),

// Aport
"sm.aport.ru"				=> array("Aport", "r"),

// Arianna (Libero.it)
"arianna.libero.it" 		=> array("Arianna", "query"),

// Ask
"web.ask.com"				=> array("Ask", "ask"),
"www.ask.co.uk"				=> array("Ask", "q"),
"www.ask.com"				=> array("Ask", "ask"),

// Atlas
"search.atlas.cz" 			=> array("Atlas", "q"),

// Centrum
"search.centrum.cz" 		=> array("Centrum", "q"),

// Club Internet
"recherche.club-internet.fr"=> array("Club Internet", "q"),

// Comet systems
"search.cometsystems.com"	=> array("CometSystems", "q"),

// dir.com
"fr.dir.com" 				=> array("dir.com", "req"),

// dmoz
"editors.dmoz.org"			=> array("dmoz", "search"),
"www.dmoz.org"				=> array("dmoz", "search"),
"dmoz.org"					=> array("dmoz", "search"),

// Dogpile
"search.dogpile.com"		=> array("Dogpile", "q"),
"nbci.dogpile.com"			=> array("Dogpile", "q"),

// earthlink
"search.earthlink.net"		=> array("Earthlink", "q"),

// Eniro
"www.eniro.se" 				=> array("Eniro", "q"),

// Espotting 
"affiliate.espotting.fr"	=> array("Espotting", "keyword"),

// Euroseek
"www.euroseek.com"			=> array("Euroseek", "string"),

// Excite
"www.excite.fr"				=> array("Excite", "search"),
"www.excite.it" 			=> array("Excite", "q"),
"msxml.excite.com"			=> array("Excite", "qkw"),

// Exalead
"www.exalead.com"			=> array("Exalead", "q"),
"www.exalead.fr"			=> array("Exalead", "q"),

// eo
"eo.st"						=> array("eo", "q"),

// Francite
"antisearch.francite.com"	=> array("Francite", "KEYWORDS"),
"recherche.francite.com"	=> array("Francite", "name"),

// Free
"search1-2.free.fr"			=> array("Free", " "),
"search1-1.free.fr"			=> array("Free", " "),

// Google
"gogole.fr"				=> array("Google", "q"),
"ww.google.fr"			=> array("Google", "q"),
"www.google.fr"			=> array("Google", "q"),
"google.fr"				=> array("Google", "q"),
"www2.google.com"		=> array("Google", "q"),
"ww.google.com"			=> array("Google", "q"),
"www.gogole.com"		=> array("Google", "q"),
"go.google.com"			=> array("Google", "q"),
"www.google.ae"			=> array("Google", "q"),
"www.google.as"			=> array("Google", "q"),
"www.google.at"			=> array("Google", "q"),
"www.google.az"			=> array("Google", "q"),
"www.google.be"			=> array("Google", "q"),
"www.google.bg"			=> array("Google", "q"),
"google.bg"				=> array("Google", "q"),
"www.google.bi"			=> array("Google", "q"),
"www.google.ca"			=> array("Google", "q"),
"ww.google.ca"			=> array("Google", "q"),
"www.google.cc"			=> array("Google", "q"),
"www.google.cd"			=> array("Google", "q"),
"www.google.cg"			=> array("Google", "q"),
"www.google.ch"			=> array("Google", "q"),
"www.google.ci"			=> array("Google", "q"),
"www.google.cl"			=> array("Google", "q"),
"www.google.co"			=> array("Google", "q"),
"www.google.de"			=> array("Google", "q"),
"wwwgoogle.de" 			=> array("Google", "q"),
"www.google.dj"			=> array("Google", "q"),
"www.google.dk"			=> array("Google", "q"),
"www.google.es"			=> array("Google", "q"),
"www.google.fi"			=> array("Google", "q"),
"www.google.fm"			=> array("Google", "q"),
"www.google.gg"			=> array("Google", "q"),
"www.google.gl"			=> array("Google", "q"),
"www.google.gm"			=> array("Google", "q"),
"www.google.gr"			=> array("Google", "q"),
"google.gr"				=> array("Google", "q"),
"www.google.hn"			=> array("Google", "q"),
"www.google.hr"			=> array("Google", "q"),
"google.hr"				=> array("Google", "q"),
"www.google.ie"			=> array("Google", "q"),
"www.google.it"			=> array("Google", "q"),
"www.google.kz"			=> array("Google", "q"),
"www.google.li"			=> array("Google", "q"),
"www.google.lt"			=> array("Google", "q"),
"www.google.lu"			=> array("Google", "q"),
"www.google.lv"			=> array("Google", "q"),
"www.google.ms"			=> array("Google", "q"),
"www.google.mu"			=> array("Google", "q"),
"www.google.mw"			=> array("Google", "q"),
"www.google.nl"			=> array("Google", "q"),
"www.google.no"			=> array("Google", "q"),
"www.google.pl"			=> array("Google", "q"),
"www.google.pn"			=> array("Google", "q"),
"www.google.pt"			=> array("Google", "q"),
"www.google.ro"			=> array("Google", "q"),
"www.google.ru"			=> array("Google", "q"),
"www.google.rw"			=> array("Google", "q"),
"www.google.se"			=> array("Google", "q"),
"www.google.sh"			=> array("Google", "q"),
"www.google.sk"			=> array("Google", "q"),
"www.google.sm" 		=> array("Google", "q"),
"www.google.td"			=> array("Google", "q"),
"www.google.tt"			=> array("Google", "q"),
"www.google.uz"			=> array("Google", "q"),
"www.google.vg"			=> array("Google", "q"),
"www.google.com.ar"		=> array("Google", "q"),
"www.google.com.au"		=> array("Google", "q"),
"www.google.com.bo"		=> array("Google", "q"),
"www.google.com.br"		=> array("Google", "q"),
"www.google.com.co"		=> array("Google", "q"),
"www.google.com.cu"		=> array("Google", "q"),
"www.google.com.do"		=> array("Google", "q"),
"www.google.com.fj"		=> array("Google", "q"),
"www.google.com.gr" 	=> array("Google", "q"),
"www.google.com.hk"		=> array("Google", "q"),
"www.google.com.ly"		=> array("Google", "q"),
"www.google.com.mt"		=> array("Google", "q"),
"www.google.com.mx"		=> array("Google", "q"),
"www.google.com.my"		=> array("Google", "q"),
"www.google.com.nf"		=> array("Google", "q"),
"www.google.com.ni"		=> array("Google", "q"),
"www.google.com.np"		=> array("Google", "q"),
"www.google.com.pa"		=> array("Google", "q"),
"www.google.com.pe" 	=> array("Google", "q"),
"www.google.com.ph"		=> array("Google", "q"),
"www.google.com.pk"		=> array("Google", "q"),
"www.google.com.pl"		=> array("Google", "q"),
"www.google.com.pr"		=> array("Google", "q"),
"www.google.com.py"		=> array("Google", "q"),
"www.google.com.ru"		=> array("Google", "q"),
"www.google.com.sg"		=> array("Google", "q"),
"www.google.com.sv"		=> array("Google", "q"),
"www.google.com.tr"		=> array("Google", "q"),
"www.google.com.tw"		=> array("Google", "q"),
"www.google.com.ua"		=> array("Google", "q"),
"www.google.com.uy"		=> array("Google", "q"),
"www.google.com.vc"		=> array("Google", "q"),
"www.google.com.vn"		=> array("Google", "q"),
"www.google.co.cr"		=> array("Google", "q"),
"www.google.co.gg"		=> array("Google", "q"),
"www.google.co.hu"		=> array("Google", "q"),
"www.google.co.id"		=> array("Google", "q"),
"www.google.co.il"		=> array("Google", "q"),
"www.google.co.in" 		=> array("Google", "q"),
"www.google.co.je"		=> array("Google", "q"),
"www.google.co.jp"		=> array("Google", "q"),
"www.google.co.ls"		=> array("Google", "q"),
"www.google.co.ke" 		=> array("Google", "q"),
"www.google.co.kr"		=> array("Google", "q"),
"www.google.co.nz"		=> array("Google", "q"),
"www.google.co.th"		=> array("Google", "q"),
"www.google.co.uk"		=> array("Google", "q"),
"www.google.co.ve"		=> array("Google", "q"),
"www.google.co.za" 		=> array("Google", "q"),
"www.google.com"		=> array("Google", "q"),

// Google translation
"translate.google.com"		=> array("Google Translations", "q"),

// Google Directory
"directory.google.com"		=> array("Google Directory", " "),

// Google Images
"images.google.fr"			=> array("Google Images", "q"),
"images.google.be" 			=> array("Google Images", "q"),
"images.google.ca" 			=> array("Google Images", "q"),
"images.google.co.uk"		=> array("Google Images", "q"),
"images.google.de" 			=> array("Google Images", "q"),
"images.google.be" 			=> array("Google Images", "q"),
"images.google.ca" 			=> array("Google Images", "q"),
"images.google.it"    		=> array("Google Images", "q"),
"images.google.com"			=> array("Google Images", "q"),

// Google News
"news.google.se" 			=> array("Google News", "q"),
"news.google.com" 			=> array("Google News", "q"),
"news.google.es" 			=> array("Google News", "q"),
"news.google.ch" 			=> array("Google News", "q"),
"news.google.lt" 			=> array("Google News", "q"),
"news.google.ie" 			=> array("Google News", "q"),
"news.google.de" 			=> array("Google News", "q"),
"news.google.cl" 			=> array("Google News", "q"),
"news.google.com.ar" 		=> array("Google News", "q"),
"news.google.fr" 			=> array("Google News", "q"),
"news.google.ca" 			=> array("Google News", "q"),
"news.google.co.uk" 		=> array("Google News", "q"),
"news.google.co.jp" 		=> array("Google News", "q"),
"news.google.com.pe" 		=> array("Google News", "q"),
"news.google.com.au" 		=> array("Google News", "q"),
"news.google.com.mx" 		=> array("Google News", "q"),
"news.google.com.hk" 		=> array("Google News", "q"),
"news.google.co.in" 		=> array("Google News", "q"),
"news.google.at" 			=> array("Google News", "q"),
"news.google.com.tw" 		=> array("Google News", "q"),
"news.google.com.co" 		=> array("Google News", "q"),
"news.google.co.ve" 		=> array("Google News", "q"),
"news.google.lu" 			=> array("Google News", "q"),
"news.google.com.ly" 		=> array("Google News", "q"),
"news.google.it" 			=> array("Google News", "q"),
"news.google.sm" 			=> array("Google News", "q"),
"news.google.com" 			=> array("Google News", "q"),

// Hit-Parade
"recherche.hit-parade.com"	=> array("Hit-Parade", "p7"),
"class.hit-parade.com"		=> array("Hit-Parade", "p7"),

// Hotbot via Lycos
"hotbot.lycos.com"			=> array("Hotbot (Lycos)", "query"),
"search.hotbot.fr"			=> array("Hotbot", "query"),
"www.hotbot.com"			=> array("Hotbot", "query"),

// 1stekeuze
"zoek.1stekeuze.nl" 		=> array("1stekeuze","terms"),

// Ilse
"spsearch.ilse.nl" 			=> array("Startpagina","search_for"),
"be.ilse.nl" 				=> array("Ilse BE","query"),
"search.ilse.nl" 			=> array("Ilse NL","search_for"),

// Iwon
"search.iwon.com"			=> array("Iwon", "searchfor"),

// Ixquick
"ixquick.com"				=> array("Ixquick", "query"),
"eu.ixquick.com" 			=> array("Ixquick","query"),

// Jyxo
"jyxo.cz" 					=> array("Jyxo", "q"),

// Kataweb
"www.kataweb.it" 			=> array("Kataweb", "q"),

// La Toile Du Québec via Google
"google.canoe.com"			=> array("La Toile Du Québec (Google)", "q"),
"web.toile.com"				=> array("La Toile Du Québec (Google)", "q"),

// La Toile Du Québec 
"recherche.toile.qc.ca"		=> array("La Toile Du Québec", "query"),

// Looksmart
"www.looksmart.com"			=> array("Looksmart", "key"),

// Lycos
"search.lycos.com"			=> array("Lycos", "query"),
"vachercher.lycos.fr"		=> array("Lycos", "query"),
"www.lycos.fr"				=> array("Lycos", "query"),
"www.multimania.lycos.fr" 	=> array("Lycos", "query"),

// Mail.ru
"go.mail.ru"				=> array("Mailru", "q"),

// Mamma
"www.mamma.com"				=> array("Mamma", "query"),

// Meceoo
"www.meceoo.fr" 			=> array("Meceoo", "kw"),

// Mediaset
"servizi.mediaset.it" 		=> array("Mediaset", "searchword"),

// Metacrawler
"search.metacrawler.com"	=> array("Metacrawler", "general"),

// Monstercrawler
"www.monstercrawler.com" 	=> array("Monstercrawler", "qry"),

// Mozbot
"www.mozbot.fr"				=> array("mozbot", "q"),
"www.mozbot.co.uk" 			=> array("mozbot", "q"),
"www.mozbot.com"			=> array("mozbot", "q"),

// MSN
"beta.search.msn.fr"		=> array("MSN", "q"),
"search.msn.fr"				=> array("MSN", "q"),
"search.msn.es"				=> array("MSN", "q"),
"search.latam.msn.com"		=> array("MSN", "q"),
"search.msn.nl" 			=> array("MSN", "q"),
"leguide.fr.msn.com"		=> array("MSN", "s"),
"leguide.msn.fr"			=> array("MSN", "s"),
"search.msn.co.jp"			=> array("MSN", "q"),
"search.t1msn.com.mx"		=> array("MSN", "q"),
"fr.ca.search.msn.com"		=> array("MSN", "q"),
"search.msn.be" 			=> array("MSN", "q"),
"search.fr.msn.be" 			=> array("MSN", "q"),
"search.msn.it" 			=> array("MSN", "q"),
"sea.search.msn.it" 		=> array("MSN", "q"),
"sea.search.msn.fr" 		=> array("MSN", "q"),
"sea.search.fr.msn.be" 		=> array("MSN", "q"),
"search.msn.de" 			=> array("MSN", "q"),
"search.msn.co.uk" 			=> array("MSN", "q"),
"search.msn.ch" 			=> array("MSN", "q"),
"search.msn.es" 			=> array("MSN", "q"),
"search.msn.com"			=> array("MSN", "q"),

// MyWebSearch
"kf.mysearch.myway.com" 	=> array("MyWebSearch", "searchfor"),
"mysearch.myway.com"		=> array("MyWebSearch", "searchfor"),
"ki.mysearch.myway.com" 	=> array("MyWebSearch", "searchfor"),
"www.mywebsearch.com"		=> array("MyWebSearch", "searchfor"),

// Najdi
"www.najdi.si" 				=> array("Najdi.si", "q"),

// Netster
"www.netster.com"			=> array("Netster", "keywords"),

// Netscape h
"search-intl.netscape.com"	=> array("Netscape", "search"),
"www.netscape.fr"			=> array("Netscape", "q"),
"search.netscape.com"		=> array("Netscape", "query"),

// Nomade
"ie4.nomade.fr"				=> array("Nomade", "s"),
"rechercher.nomade.fr"		=> array("Nomade", "s"),

// Northern Light
"www.northernlight.com"		=> array("Northern Light", "qr"),

// Numéricable
"www.numericable.fr" 		=> array("Numéricable", "query"),

// Onet
"szukaj.onet.pl" 			=> array("Onet.pl", "qt"),

// Opera
"search.opera.com" 			=> array("Opera", "search"),

// Overture
"www.overture.com"			=> array("Overture", "Keywords"),
"www.fr.overture.com"		=> array("Overture", "Keywords"),

// Quick searches
"data.quicksearches.net"	=> array("QuickSearches", "q"),

// Rambler
"search.rambler.ru" 		=> array("Rambler", "words"),

// Reacteur.com
"www.reacteur.com"			=> array("Reacteur", "kw"),

// Sapo
"pesquisa.sapo.pt" 			=> array("Sapo","q"),

// Search.com
"www.search.com"			=> array("Search.com", "q"),

// Search a lot
"www.searchalot.com"		=> array("Searchalot", "query"),

// Searchscout
"www.searchscout.com"		=> array("Search Scout", "gt_keywords"),

// Searchy
"www.searchy.co.uk"			=> array("Searchy", "search_term"),

// Seznam
"search1.seznam.cz" 		=> array("Seznam", "w"),
"search.seznam.cz" 			=> array("Seznam", "w"),
"search1.seznam.cz" 		=> array("Seznam", "w"),
"search2.seznam.cz" 		=> array("Seznam", "w"),

// Sharelook
"www.sharelook.fr"			=> array("Sharelook", "keyword"),

// Skynet
"search.skynet.be" 			=> array("Skynet", "keywords"),

// Supereva
"search.supereva.com" 		=> array("Supereva", "q"),

// Sympatico
"search.sli.sympatico.ca"   => array("Sympatico", "q"),
"search.fr.sympatico.msn.ca"=> array("Sympatico", "q"),
"sea.search.fr.sympatico.msn.ca"=> array("Sympatico", "q"),
"search.sympatico.msn.ca"	=> array("Sympatico", "q"),

// Teoma
"www.teoma.com"				=> array("Teoma", "t"),

// Tiscali
"rechercher.nomade.tiscali.fr" => array("Tiscali", "s"),
"search-dyn.tiscali.it" 	=> array("Tiscali", "key"),
"hledani.tiscali.cz" 		=> array("Tiscali", "query"),

// Trouvez.com
"www.trouvez.com"			=> array("Trouvez.com", "query"),

// Vinden
"zoek.vinden.nl" 			=> array("Vinden", "query"),

// Vindex
"www.vindex.nl" 			=> array("Vindex","search_for"),

// Virgilio
"search.virgilio.it"		=> array("Virgilio", "qs"),

// Voila
"search.ke.voila.fr"		=> array("Voila", "rdata"),
"moteur.voila.fr"			=> array("Voila", "kw"),
"search.voila.fr"			=> array("Voila", "kw"),
"beta.voila.fr"				=> array("Voila", "kw"),
"search.voila.com"			=> array("Voila", "kw"),
"web.volny.cz" 				=> array("Volny", "search"),

// X-recherche
"www.x-recherche.com" 		=> array("X-Recherche", "mots"),

// Yahoo
"ink.yahoo.com"				=> array("Yahoo !", "p"),
"fr.ink.yahoo.com"			=> array("Yahoo !", "p"),
"search.yahoo.fr"			=> array("Yahoo !", "p"),
"ink.yahoo.fr"				=> array("Yahoo !", "p"),
"fr.search.yahoo.com"		=> array("Yahoo !", "p"),
"cf.search.yahoo.com"		=> array("Yahoo !", "p"),
"espanol.search.yahoo.com"	=> array("Yahoo !", "p"),
"ca.search.yahoo.com"		=> array("Yahoo !", "p"),
"es.search.yahoo.com" 		=> array("Yahoo !", "p"),
"mx.search.yahoo.com" 		=> array("Yahoo !", "p"),
"it.search.yahoo.com" 		=> array("Yahoo !", "p"),
"uk.search.yahoo.com" 		=> array("Yahoo !", "p"),
"search.yahoo.co.jp" 		=> array("Yahoo !", "p"),
"kr.search.yahoo.com" 		=> array("Yahoo !", "p"),
"uk.search.yahoo.com" 		=> array("Yahoo !", "p"),
"br.search.yahoo.com" 		=> array("Yahoo !", "p"),
"nl.search.yahoo.com" 		=> array("Yahoo !","p"),
"ar.search.yahoo.com" 		=> array("Yahoo !", "p"),
"search.yahoo.com"			=> array("Yahoo !", "p"),
"fr.dir.yahoo.com"			=> array("Yahoo ! Répertoires", " "),
"cf.dir.yahoo.com"			=> array("Yahoo ! Répertoires", "q"),

// Yahoo ! (Google)
"cf.google.yahoo.com"		=> array("Yahoo ! (Google)", "p"),
"fr.google.yahoo.com"		=> array("Yahoo ! (Google)", "p"),
"google.yahoo.com"			=> array("Yahoo ! (Google)", "p"),

// Yandex
"www.yandex.ru" 			=> array("Yandex", "text"),
"yandex.ru" 				=> array("Yandex", "text"),
"search.yaca.yandex.ru" 	=> array("Yandex", "text"),
"ya.ru" 					=> array("Yandex", "text"),
"www.ya.ru" 				=> array("Yandex", "text"),
"images.yandex.ru"			=> array("Yandex Images","text"),

// Wanadoo
"search.ke.wanadoo.fr"		=> array("Wanadoo", "kw"),

// Wedoo
"fr.wedoo.com"				=> array("Wedoo", "keyword"),

// Web.nl
"www.web.nl" 				=> array("Web.nl","query"),

// Weborama
"www.weborama.fr"			=> array("weborama", "query"),

// WebSearch
"is1.websearch.com"			=> array("WebSearch", "qkw"),
"www.websearch.com"			=> array("WebSearch", "qkw"),
"websearch.cs.com"			=> array("WebSearch", "query"),

// WXS
"wxsl.nl" 					=> array("Planet Internet","q"),

// Zoek
"www3.zoek.nl" 				=> array("Zoek","q"),

// Zoeken
"www.zoeken.nl" 			=> array("Zoeken","query"),

// Zoohoo
"zoohoo.cz" 				=> array("Zoohoo", "q"),
"www.zoohoo.cz" 			=> array("Zoohoo", "q"),

);
?>