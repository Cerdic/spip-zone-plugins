// Retient pendant la session la difference d'horaire serveur/client dans un cookie
// pour SPIP < 2.0, il faut le plugin jquery.cookie.js

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
// et tagger avec cs_done pour eviter de traiter plrs fois la meme horloge
function jclock_init() {
	if(jQuery('.jclock', this).length) {
		if(typeof $.cookie!='function')
			jQuery.getScript(cs_CookiePlugin, function(){ 
				var diff = $.cookie('cs_jclock_diff');
				if(diff===null) set_jclock_ajax(); else each_jclock(diff);
			});
		else
			set_jclock_ajax();
   }
}

function set_jclock_ajax() {
	$.get(cs_DateServeur, function(resultat) {
		var local = new Date();
		var diff = local.dateFormat("U") - $("U", resultat).text();
//		var serveurgmt = $("O", resultat).text();
		each_jclock(diff);
		if(typeof $.cookie=='function')
			$.cookie('cs_jclock_diff', diff);
	});
}

function each_jclock(diff) {
	jQuery('.jclock')
	.cs_todo()
	.each(function(){
		var options = { serveur_offset: diff };
		var opt = this.title.split('|');
		for (i=0; i<opt.length; i++) {
			j = opt[i].indexOf('=');
			if(j>0) options[opt[i].substr(0,j).trim()] = opt[i].substring(j+1).trim();
		}
		if(typeof options.id != "undefined") $(this).addClass('jclock'+options.id)
		this.title = "";
		$(this).jclock(options);
	});
}

String.prototype.trim = function() { return this.replace(/(?:^\s+|\s+$)/g, ''); };

Date.prototype.utcTime = function() {
	var localTime = this.getTime();
	var localOffset = this.getTimezoneOffset() * 60000;
	return localTime + localOffset;
};

function js_date(time) {
	var d = new Date();
	d.setTime(time);
	return d;
}

/*
	jQuery jclock - Base sur les travaux de :
	http://plugins.jquery.com/project/jclock
	
	Adaptation SPIP + Couteau Suisse : Patrice Vanneufville
	
	Formats disponibles :
	*********************
	ACDT : Australia Central Time (GMT + 10:30)
	ACST : Australia Central Time (GMT + 09:30)
	AEDT : Australia Eastern Time (GMT + 10:00)
	AEST : Australia Eastern Time (GMT + 10:00)
	AHST : Alaska-Hawaii Std Time (GMT - 10:00)
	AKST : Alaska Std Time (GMT - 09:00)
	AWST : Australia Western Time (GMT + 08:00)
	BRADT : Brazilian Acre Daylight Time (GMT - 04:00)
	BRAST : Brazilian Acre Standard Time (GMT - 05:00)
	BRDT : Brazilian Daylight Time (GMT - 02:00)
	BREDT : Brazilian Eastern Daylight Time (GMT - 01:00)
	BREST : Brazilian Eastern Standard Time (GMT - 02:00)
	BRST : Brazilian Standard Time (GMT - 03:00)
	BRWDT : Brazilian Western Daylight Time (GMT - 03:00)
	BRWST : Brazilian Western Standard Time (GMT - 04:00)
	CCT : China Coastal Time (GMT + 08:00)
	CDT : Central Daylight Saving Time (GMT - 05:00)
	CET : Central Europe Time (GMT + 01:00)
	CST : Central Time (GMT - 06:00)
	EAT : East Africa Time (GMT + 02:00)
	EET : Eastern Europe Time (GMT + 02:00)
	EST : Eastern Time (GMT - 05:00)
	GMT : GMT + 00:00
	GMT0100 : GMT + 01:00
	GMT0200 : GMT + 02:00
	GMT0300 : GMT + 03:00
	GMT0330 : GMT + 03:30
	GMT0400 : GMT + 04:00
	GMT0430 : GMT + 04:30
	GMT0500 : GMT + 05:00
	GMT0530 : GMT + 05:30
	GMT0545 : GMT + 05:45
	GMT0600 : GMT + 06:00
	GMT0630 : GMT + 06:30
	GMT0700 : GMT + 07:00
	GMT0710 : GMT + 07:10
	GMT0730 : GMT + 07:30
	GMT0800 : GMT + 08:00
	GMT0830 : GMT + 08:30
	GMT0900 : GMT + 09:00
	GMT0930 : GMT + 09:30
	GMT1000 : GMT + 10:00
	GMT1100 : GMT + 11:00
	GMT1200 : GMT + 12:00
	GMT1300 : GMT + 13:00
	GMT-0100 : GMT - 01:00
	GMT-0200 : GMT - 02:00
	GMT-0230 : GMT - 02:30
	GMT-0300 : GMT - 03:00
	GMT-0330 : GMT - 03:30
	GMT-0400 : GMT - 04:00
	GMT-0430 : GMT - 04:30
	GMT-0500 : GMT - 05:00
	GMT-0600 : GMT - 06:00
	GMT-0700 : GMT - 07:00
	GMT-0800 : GMT - 08:00
	GMT-0900 : GMT - 09:00
	GMT-1000 : GMT - 10:00
	GMT-1100 : GMT - 11:00
	GMT-1200 : GMT - 12:00
	ICT : Bangkok Time (GMT + 07:00)
	IST : Israel Standard Time (GMT + 02:00)
	JOG : Jogjakarta Indonesia Time(GMT + 07:00)
	JST : Japan Time (GMT + 09:00)
	MST : Mountain Time (GMT - 07:00)
	MX-CST : Mexico Central Time (GMT - 06:00)
	MX-MST : Mexico Mountain Time (GMT - 07:00)
	MX-PST : Mexico Pacific Time (GMT - 08:00)
	NZT : New Zealand Time (GMT + 12:00)
	PST : Pacific Time (GMT - 08:00)
	R1T : Russia Time 1 (GMT + 02:00)
	R2T : Russia Time 2 (GMT + 03:00)
	SAST : Australian South Standard Time (GMT + 09:30)
	USZ3 : Russia Time 3 (GMT + 04:00)
	USZ4 : Russia Time 4 (GMT + 05:00)
	USZ5 : Russia Time 5 (GMT + 06:00)
	USZ6 : Russia Time 6 (GMT + 07:00)
	USZ7 : Russia Time 7 (GMT + 08:00)
	USZ8 : Russia Time 8 (GMT + 09:00)
	USZ9 : Russia Time 9 (GMT + 10:00)
	WET : Western Europe Time (GMT + 00:00)
*/

(function($) {
	var gmtDataBase = { 'ACDT':3780,'ACST':3420,'AEDT':3600,'AEST':3600,'AHST':-3600,'AKST':-3240,'AWST':2880,'BRADT':-1440,'BRAST':-1800,'BRDT':-720,'BREDT':-360,'BREST':-720,'BRST':-1080,'BRWDT':-1080,'BRWST':-1440,'CCT':2880,'CDT':-1800,'CET':360,'CST':-2160,'EAT':720,'EET':720,'EST':-1800,'GMT':0,'GMT0100':360,'GMT0200':720,'GMT0300':1080,'GMT0330':1260,'GMT0400':1440,'GMT0430':1620,'GMT0500':1800,'GMT0530':1980,'GMT0545':2070,'GMT0600':2160,'GMT0630':2340,'GMT0700':2520,'GMT0710':2580,'GMT0730':2700,'GMT0800':2880,'GMT0830':3060,'GMT0900':3240,'GMT0930':3420,'GMT1000':3600,'GMT1100':3960,'GMT1200':4320,'GMT1300':4680,'GMT-0100':-360,'GMT-0200':-720,'GMT-0230':-540,'GMT-0300':-1080,'GMT-0330':-900,'GMT-0400':-1440,'GMT-0430':-1260,'GMT-0500':-1800,'GMT-0600':-2160,'GMT-0700':-2520,'GMT-0800':-2880,'GMT-0900':-3240,'GMT-1000':-3600,'GMT-1100':-3960,'GMT-1200':-4320,'ICT':2520,'IST':720,'JOG':2520,'JST':3240,'MST':-2520,'MX-CST':-2160,'MX-MST':-2520,'MX-PST':-2880,'NZT':4320,'PST':-2880,'R1T':720,'R2T':1080,'SAST':3420,'USZ3':1440,'USZ4':1800,'USZ5':2160,'USZ6':2520,'USZ7':2880,'USZ8':3240,'USZ9':3600,'WET':0 };

/*
	Time zone changes and daylight saving time start dates between year 2010 and 2019
	Update : 2010-04-23
	http://www.timeanddate.com/worldclock/custom.html
	syntax : {UTC_time:UTC_ofset}
*/
	var dstDataBase = {
	'addis ababa':3,
	'adelaide':{0:10.5,1270305000:9.5,1286029800:10.5,1301754600:9.5,1317479400:10.5,1333204200:9.5,1349533800:10.5,1365258600:9.5,1380983400:10.5,1396708200:9.5,1412433000:10.5,1428157800:9.5,1443882600:10.5,1459607400:9.5,1475332200:10.5,1491057000:9.5,1506781800:10.5,1522506600:9.5,1538836200:10.5,1554561000:9.5,1570285800:10.5},
	'aden':3,
	'algiers':1,
	'almaty':6,
	'amman':{0:2,1269550800:3,1288296000:2,1301601600:3,1319745600:2,1333051200:3,1351195200:2,1364504400:3,1382644800:2,1395954000:3,1414702800:2,1427403600:3,1446152400:2,1459454400:3,1477598400:2,1490904000:3,1509048000:2,1522353600:3,1540497600:2,1553806800:3,1571947200:2},
	'amsterdam':'madrid',
	'anadyr':'kamchatka',
	'anchorage':{0:-9,1268560800:-8,1289120400:-9,1300010400:-8,1320570000:-9,1331460000:-8,1352019600:-9,1362909600:-8,1383469200:-9,1394359200:-8,1414918800:-9,1425808800:-8,1446368400:-9,1457863200:-8,1478422800:-9,1489312800:-8,1509872400:-9,1520762400:-8,1541322000:-9,1552212000:-8,1572771600:-9},
	'ankara':'helsinki',
	'antananarivo':3,
	'asuncion':{0:-3,1270947600:-4,1286071200:-3,1302397200:-4,1317520800:-3,1333846800:-4,1349575200:-3,1365901200:-4,1381024800:-3,1397350800:-4,1412474400:-3,1428800400:-4,1443924000:-3,1460250000:-4,1475373600:-3,1491699600:-4,1506823200:-3,1523149200:-4,1538877600:-3,1555203600:-4,1570327200:-3},
	'athens':'helsinki',
	'atlanta':{0:-5,1268546400:-4,1289106000:-5,1299996000:-4,1320555600:-5,1331445600:-4,1352005200:-5,1362895200:-4,1383454800:-5,1394344800:-4,1414904400:-5,1425794400:-4,1446354000:-5,1457848800:-4,1478408400:-5,1489298400:-4,1509858000:-5,1520748000:-4,1541307600:-5,1552197600:-4,1572757200:-5},
	'auckland':{0:13,1270296000:12,1285416000:13,1301745600:12,1316865600:13,1333195200:12,1348920000:13,1365249600:12,1380369600:13,1396699200:12,1411819200:13,1428148800:12,1443268800:13,1459598400:12,1474718400:13,1491048000:12,1506168000:13,1522497600:12,1538222400:13,1554552000:12,1569672000:13},
	'baghdad':3,
	'bangkok':7,
	'barcelona':'madrid',
	'beijing':8,
	'beirut':{0:2,1269723600:3,1288465200:2,1301173200:3,1319914800:2,1332622800:3,1351364400:2,1364677200:3,1382814000:2,1396126800:3,1414263600:2,1427576400:3,1445713200:2,1459026000:3,1477767600:2,1490475600:3,1509217200:2,1521925200:3,1540666800:2,1553979600:3,1572116400:2},
	'belgrade':'madrid',
	'berlin':'madrid',
	'bogota':-5,
	'boston':'atlanta',
	'brasilia':'rio de janeiro',
	'brisbane':10,
	'brussels':'madrid',
	'bucharest':'helsinki',
	'budapest':'madrid',
	'buenos aires':-3,
	'cairo':{0:2,1272571200:3,1304020800:3,1317322800:2,1335470400:3,1348772400:2,1366920000:3,1380222000:2,1398369600:3,1411671600:2,1429819200:3,1443121200:2,1461873600:3,1475175600:2,1493323200:3,1506625200:2,1524772800:3,1538074800:2,1556222400:3,1569524400:2},
	'canberra':'melbourne',
	'cape town':2,
	'caracas':-4.5,
	'casablanca':0,
	'chatham islands':{1270295999:13.75,1270296000:12.75,1285415999:12.75,1285416000:13.75,1301745599:13.75,1301745600:12.75,1316865599:12.75,1316865600:13.75,1333195199:13.75,1333195200:12.75,1348919999:12.75,1348920000:13.75,1365249599:13.75,1365249600:12.75,1380369599:12.75,1380369600:13.75,1396699199:13.75,1396699200:12.75,1411819199:12.75,1411819200:13.75,1428148799:13.75,1428148800:12.75,1443268799:12.75,1443268800:13.75,1459598399:13.75,1459598400:12.75,1474718399:12.75,1474718400:13.75,1491047999:13.75,1491048000:12.75,1506167999:12.75,1506168000:13.75,1522497599:13.75,1522497600:12.75,1538222399:12.75,1538222400:13.75,1554551999:13.75,1554552000:12.75,1569671999:12.75,1569672000:13.75},
	'chicago':'houston',
	'copenhagen':'madrid',
	'darwin':9.5,
	'denver':{0:-7,1268553600:-6,1289113200:-7,1300003200:-6,1320562800:-7,1331452800:-6,1352012400:-7,1362902400:-6,1383462000:-7,1394352000:-6,1414911600:-7,1425801600:-6,1446361200:-7,1457856000:-6,1478415600:-7,1489305600:-6,1509865200:-7,1520755200:-6,1541314800:-7,1552204800:-6,1572764400:-7},
	'detroit':'atlanta',
	'dhaka':6,
	'dubai':4,
	'dublin':'lisbon',
	'edmonton':'denver',
	'frankfurt':'madrid',
	'geneva':'madrid',
	'guatemala':-6,
	'halifax':{0:-4,1268542800:-3,1289102400:-4,1299992400:-3,1320552000:-4,1331442000:-3,1352001600:-4,1362891600:-3,1383451200:-4,1394341200:-3,1414900800:-4,1425790800:-3,1446350400:-4,1457845200:-3,1478404800:-4,1489294800:-3,1509854400:-4,1520744400:-3,1541304000:-4,1552194000:-3,1572753600:-4},
	'hanoi':7,
	'harare':2,
	'havana':{0:-5,1268539200:-4,1288494000:-5,1299988800:-4,1319943600:-5,1331438400:-4,1351393200:-5,1362888000:-4,1382842800:-5,1394337600:-4,1414292400:-5,1425787200:-4,1445742000:-5,1457841600:-4,1477796400:-5,1489291200:-4,1509246000:-5,1520740800:-4,1540695600:-5,1552190400:-4,1572145200:-5},
	'helsinki':{0:2,1269730800:3,1288483200:2,1301180400:3,1319932800:2,1332630000:3,1351382400:2,1364684400:3,1382832000:2,1396134000:3,1414281600:2,1427583600:3,1445731200:2,1459033200:3,1477785600:2,1490482800:3,1509235200:2,1521932400:3,1540684800:2,1553986800:3,1572134400:2},
	'hong kong':8,
	'honolulu':-10,
	'houston':{0:-6,1268550000:-5,1289109600:-6,1299999600:-5,1320559200:-6,1331449200:-5,1352008800:-6,1362898800:-5,1383458400:-6,1394348400:-5,1414908000:-6,1425798000:-5,1446357600:-6,1457852400:-5,1478412000:-6,1489302000:-5,1509861600:-6,1520751600:-5,1541311200:-6,1552201200:-5,1572760800:-6},
	'indianapolis':'atlanta',
	'islamabad':5,
	'istanbul':'helsinki',
	'jakarta':7,
	'jerusalem':{0:2,1269558000:3,1284238800:2,1301608800:3,1317502800:2,1333058400:3,1348347600:2,1364511600:3,1378587600:2,1395961200:3,1411851600:2,1427410800:3,1442696400:2,1459461600:3,1475960400:2,1490911200:3,1506200400:2,1522360800:3,1537045200:2,1553814000:3,1570309200:2},
	'johannesburg':2,
	'kabul':4.5,
	'kamchatka':{0:12,1269694800:12,1288447200:11,1301144400:12,1319896800:11,1332594000:12,1351346400:11,1364648400:12,1382796000:11,1396098000:12,1414245600:11,1427547600:12,1445695200:11,1458997200:12,1477749600:11,1490446800:12,1509199200:11,1521896400:12,1540648800:11,1553950800:12,1572098400:11},
	'karachi':5,
	'kathmandu':5.75,
	'khartoum':3,
	'kingston':-5,
	'kiritimati':14,
	'kolkata':5.5,
	'kuala lumpur':8,
	'kuwait city':3,
	'kyiv':'helsinki',
	'la paz':-4,
	'lagos':1,
	'lahore':5,
	'lima':-5,
	'lisbon':{0:1,1288476000:1,1319925600:1,1351375200:1,1382824800:1,1414274400:1,1445724000:1,1477778400:1,1509228000:1,1540677600:1},
	'london':'lisbon',
	'los angeles':{0:-8,1268557200:-7,1289116800:-8,1300006800:-7,1320566400:-8,1331456400:-7,1352016000:-8,1362906000:-7,1383465600:-8,1394355600:-7,1414915200:-8,1425805200:-7,1446364800:-8,1457859600:-7,1478419200:-8,1489309200:-7,1509868800:-8,1520758800:-7,1541318400:-8,1552208400:-7,1572768000:-8},
	'madrid':{0:1,1269730800:2,1288483200:1,1301180400:2,1319932800:1,1332630000:2,1351382400:1,1364684400:2,1382832000:1,1396134000:2,1414281600:1,1427583600:2,1445731200:1,1459033200:2,1477785600:1,1490482800:2,1509235200:1,1521932400:2,1540684800:1,1553986800:2,1572134400:1},
	'managua':-6,
	'manila':8,
	'melbourne':{0:11,1270303200:10,1286028000:11,1301752800:10,1317477600:11,1333202400:10,1349532000:11,1365256800:10,1380981600:11,1396706400:10,1412431200:11,1428156000:10,1443880800:11,1459605600:10,1475330400:11,1491055200:10,1506780000:11,1522504800:10,1538834400:11,1554559200:10,1570284000:11},
	'mexico city':{0:-6,1270360800:-5,1288501200:-6,1301810400:-5,1319950800:-6,1333260000:-5,1351400400:-6,1365314400:-5,1382850000:-6,1396764000:-5,1414299600:-6,1428213600:-5,1445749200:-6,1459663200:-5,1477803600:-6,1491112800:-5,1509253200:-6,1522562400:-5,1540702800:-6,1554616800:-5,1572152400:-6},
	'miami':'atlanta',
	'minneapolis':'houston',
	'minsk':{0:2,1269727200:3,1288479600:2,1301176800:3,1319929200:2,1332626400:3,1351378800:2,1364680800:3,1382828400:2,1396130400:3,1414278000:2,1427580000:3,1445727600:2,1459029600:3,1477782000:2,1490479200:3,1509231600:2,1521928800:3,1540681200:2,1553983200:3,1572130800:2},
	'montevideo':{0:-2,1268535600:-3,1286074800:-2,1299985200:-3,1317524400:-2,1331434800:-3,1349578800:-2,1362884400:-3,1381028400:-2,1394334000:-3,1412478000:-2,1425783600:-3,1443927600:-2,1457838000:-3,1475377200:-2,1489287600:-3,1506826800:-2,1520737200:-3,1538881200:-2,1552186800:-3,1570330800:-2},
	'montgomery':'houston',
	'montreal':'atlanta',
	'moscow':{0:3,1269723600:4,1288476000:3,1301173200:4,1319925600:3,1332622800:4,1351375200:3,1364677200:4,1382824800:3,1396126800:4,1414274400:3,1427576400:4,1445724000:3,1459026000:4,1477778400:3,1490475600:4,1509228000:3,1521925200:4,1540677600:3,1553979600:4,1572127200:3},
	'mumbai':5.5,
	'nairobi':3,
	'nassau':'atlanta',
	'new delhi':5.5,
	'new orleans':'houston',
	'new york':'atlanta',
	'oslo':'madrid',
	'ottawa':'atlanta',
	'paris':'madrid',
	'perth':8,
	'philadelphia':'atlanta',
	'phoenix':-7,
	'prague':'madrid',
	'reykjavik':0,
	'rio de janeiro':{1287277200:-2,1318726800:-2,1350781200:-2,1382230800:-2,1413680400:-2,1445130000:-2,1476579600:-2,1508029200:-2,1540083600:-2,1571533200:-2},
	'riyadh':3,
	'rome':'madrid',
	'san francisco':'los angeles',
	'san juan':-4,
	'san salvador':-6,
	'santiago':{0:-3,1270342800:-4,1286676000:-3,1299981600:-4,1318125600:-3,1331431200:-4,1350180000:-3,1362880800:-4,1381629600:-3,1394330400:-4,1413079200:-3,1426384800:-4,1444528800:-3,1457834400:-4,1475978400:-3,1489284000:-4,1508032800:-3,1520733600:-4,1539482400:-3,1552183200:-4,1570932000:-3},
	'santo domingo':-4,
	'sao paulo':'rio de janeiro',
	'seattle':'los angeles',
	'seoul':9,
	'shanghai':8,
	'singapore':8,
	'sofia':'helsinki',
	'st. john\'s':{1268533859:-3.5,1268533860:-2.5,1289093459:-2.5,1289093460:-3.5,1299983459:-3.5,1299983460:-2.5,1320543059:-2.5,1320543060:-3.5,1331433059:-3.5,1331433060:-2.5,1351992659:-2.5,1351992660:-3.5,1362882659:-3.5,1362882660:-2.5,1383442259:-2.5,1383442260:-3.5,1394332259:-3.5,1394332260:-2.5,1414891859:-2.5,1414891860:-3.5,1425781859:-3.5,1425781860:-2.5,1446341459:-2.5,1446341460:-3.5,1457836259:-3.5,1457836260:-2.5,1478395859:-2.5,1478395860:-3.5,1489285859:-3.5,1489285860:-2.5,1509845459:-2.5,1509845460:-3.5,1520735459:-3.5,1520735460:-2.5,1541295059:-2.5,1541295060:-3.5,1552185059:-3.5,1552185060:-2.5,1572744659:-2.5,1572744660:-3.5},
	'st. paul':'houston',
	'stockholm':'madrid',
	'suva':{0:13,1269694800:12,1287835200:13,1301144400:12,1319284800:13,1332594000:12,1350734400:13,1364648400:12,1382792400:13,1396098000:12,1414242000:13,1427547600:12,1445691600:13,1458997200:12,1477137600:13,1490446800:12,1508587200:13,1521896400:12,1540036800:13,1553950800:12,1572094800:13},
	'sydney':'melbourne',
	'taipei':8,
	'tallinn':'helsinki',
	'tashkent':5,
	'tegucigalpa':-6,
	'tehran':{0:3.5,1269199800:4.5,1285090200:3.5,1300735800:4.5,1316626200:3.5,1332271800:4.5,1348162200:3.5,1363894200:4.5,1379784600:3.5,1395430200:4.5,1411320600:3.5,1426966200:4.5,1442856600:3.5,1458502200:4.5,1474392600:3.5,1490124600:4.5,1506015000:3.5,1521660600:4.5,1537551000:3.5,1553196600:4.5,1569087000:3.5},
	'tokyo':9,
	'toronto':'atlanta',
	'vancouver':'los angeles',
	'vienna':'madrid',
	'vladivostok':{0:10,1269698400:11,1288450800:10,1301148000:11,1319900400:10,1332597600:11,1351350000:10,1364652000:11,1382799600:10,1396101600:11,1414249200:10,1427551200:11,1445698800:10,1459000800:11,1477753200:10,1490450400:11,1509202800:10,1521900000:11,1540652400:10,1553954400:11,1572102000:10},
	'warsaw':'madrid',
	'washington dc':'atlanta',
	'winnipeg':'houston',
	'yangon':6.5,
	'zagreb':'madrid',
	'zurich':'madrid'}
	
function dstOffset(utcTime, db) {
	if(typeof db == "string") db = dstDataBase[db];
	if(typeof db == "number") return db;
	var res = false;
	for(i in db) {
		if(utcTime < i) return res;
		res = db[i];
	}
	return res;
}

$.fn.jclock = function(options) {
 
	// options
	var opts = $.extend({}, $.fn.jclock.defaults, options);
		 
	return this.each(function() {
		$this = $(this);
		$this.timerID = null;
		$this.running = false;
		
		var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
		$this.format = o.format;
		$this.serveur_offset = o.serveur_offset;
		$this.utc = o.utc.toUpperCase();
		$this.utc_offset = o.utc_offset;
		if (o.utc) $this.utc_offset = gmtDataBase[$this.utc]*10000;
		$this.town = o.town.toLowerCase();
		$.fn.jclock.startClock($this);
	
	});
  };
       
  $.fn.jclock.startClock = function(el) {
    $.fn.jclock.stopClock(el);
    $.fn.jclock.displayTime(el);
  }
 
  $.fn.jclock.stopClock = function(el) {
    if(el.running) clearTimeout(el.timerID);
    el.running = false;
  }
 
  $.fn.jclock.displayTime = function(el) {
    var time = $.fn.jclock.getTime(el);
    el.html(time);
    el.timerID = setTimeout(function(){$.fn.jclock.displayTime(el)},1000);
  }
 
  $.fn.jclock.getTime = function(el) {
    var now = new Date();
	if(!el.format) {
		// ici on veut l'horloge du serveur
		now = new Date(now.getTime() - el.serveur_offset*1000);
		el.format = 'H:i:s';
	} else if(el.utc.length) {
		now = new Date(now.utcTime() + el.utc_offset - el.serveur_offset*1000);
    } else if(el.town.length) {
		var utc = now.utcTime() - el.serveur_offset*1000
		now = new Date(utc + dstOffset(Math.floor(utc/1000), dstDataBase[el.town])*3600000 );
	}
 	return now.dateFormat(el.format);
  }
       
  $.fn.jclock.defaults = {
    format: 'H:i:s',
	utc: '',
	utc_offset: 0,
	town: '',
	serveur_offset:0
  };
 
})(jQuery);