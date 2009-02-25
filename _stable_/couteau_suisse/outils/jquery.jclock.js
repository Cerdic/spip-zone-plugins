// Retient pendant la session la difference d'horaire serveur/client dans un cookie
// pour SPIP < 2.0, il faut le plugin jquery.cookie.js

$(function($) {
	if($('.jclock').length) {
		if(typeof $.cookie!='function')
			$.getScript(cs_CookiePlugin, function(){ 
				var diff = $.cookie('cs_jclock_diff');
				if(diff===null) set_jclock_ajax(); else each_jclock(diff);
			});
		else
			set_jclock_ajax();
   }
});

function set_jclock_ajax() {
	$.get(cs_DateServeur, function(resultat) {
		var local = new Date();
		var diff = local.dateFormat("U") - $("U", resultat).text();
		var serveurgmt = $("O", resultat).text();
		each_jclock();
		if(typeof $.cookie=='function')
			$.cookie('cs_jclock_diff', diff);
	});
}

function each_jclock(diff) {
	$('.jclock').each(function(){
		var reg = /([^|]*)\|(.*$)/g;
		reg = reg.exec(this.title);
		if(!reg) 
			var options = { offset: diff };
		else 
			var options = {	offset: diff, utc: reg[1], format: reg[2] }; 
		$(this).jclock(options);
	});
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
	var gmtDataBase = '/ACDT:37800/ACST:34200/AEDT:36000/AEST:36000/AHST:-36000/AKST:-32400/AWST:28800/BRADT:-14400/BRAST:-18000/BRDT:-7200/BREDT:-3600/BREST:-7200/BRST:-10800/BRWDT:-10800/BRWST:-14400/CCT:28800/CDT:-18000/CET:3600/CST:-21600/EAT:7200/EET:7200/EST:-18000/GMT:0/GMT0100:3600/GMT0200:7200/GMT0300:10800/GMT0330:12600/GMT0400:14400/GMT0430:16200/GMT0500:18000/GMT0530:19800/GMT0545:20700/GMT0600:21600/GMT0630:23400/GMT0700:25200/GMT0710:25800/GMT0730:27000/GMT0800:28800/GMT0830:30600/GMT0900:32400/GMT0930:34200/GMT1000:36000/GMT1100:39600/GMT1200:43200/GMT1300:46800/GMT-0100:-3600/GMT-0200:-7200/GMT-0230:-5400/GMT-0300:-10800/GMT-0330:-9000/GMT-0400:-14400/GMT-0430:-12600/GMT-0500:-18000/GMT-0600:-21600/GMT-0700:-25200/GMT-0800:-28800/GMT-0900:-32400/GMT-1000:-36000/GMT-1100:-39600/GMT-1200:-43200/ICT:25200/IST:7200/JOG:25200/JST:32400/MST:-25200/MX-CST:-21600/MX-MST:-25200/MX-PST:-28800/NZT:43200/PST:-28800/R1T:7200/R2T:10800/SAST:34200/USZ3:14400/USZ4:18000/USZ5:21600/USZ6:25200/USZ7:28800/USZ8:32400/USZ9:36000/WET:0';
	var jclockFormatDefaut = 'H:i:s';

  $.fn.jclock = function(options) {
 
    // options
    var opts = $.extend({}, $.fn.jclock.defaults, options);
         
    return this.each(function() {
		$this = $(this);
		$this.timerID = null;
		$this.running = false;
		
		var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
		
		$this.format = o.format?o.format:jclockFormatDefaut;
		$this.offset = o.offset;
		$this.utc = o.utc;
		$this.utc_offset = o.utc_offset;
 		if (o.utc.length && ((index = gmtDataBase.indexOf("/"+o.utc+":")) != -1)) {
		  var utc_offset = gmtDataBase.substring(index);
		  var reg = new RegExp(":(-?[0-9]+)","g");
		  utc_offset = reg.exec(utc_offset);
		  $this.utc_offset = utc_offset[1]*1000;
		}

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
    if(el.utc.length) {
		var localTime = now.getTime();
		var localOffset = now.getTimezoneOffset() * 60000;
		var utc = localTime + localOffset;
		var utcTime = utc + el.utc_offset;
		now = new Date(utcTime);
    }
 	return now.dateFormat(el.format);
  }
       
  $.fn.jclock.defaults = {
    format: jclockFormatDefaut,
	utc: '',
	utc_offset: 0,
	offset:0,
  };
 
})(jQuery);