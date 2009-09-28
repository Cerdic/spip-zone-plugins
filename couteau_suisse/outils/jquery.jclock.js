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
		now = new Date(now.getTime() - el.serveur_offset * 1000);
		el.format = 'H:i:s';
	} else if(el.utc.length) {
		var localTime = now.getTime();
		var localOffset = now.getTimezoneOffset() * 60000;
		var utc = localTime + localOffset;
		var utcTime = utc + el.utc_offset;
		now = new Date(utcTime - el.serveur_offset * 1000);
    }
 	return now.dateFormat(el.format);
  }
       
  $.fn.jclock.defaults = {
    format: 'H:i:s',
	utc: '',
	utc_offset: 0,
	serveur_offset:0
  };
 
})(jQuery);