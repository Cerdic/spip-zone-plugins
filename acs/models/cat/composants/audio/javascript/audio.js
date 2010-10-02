/**
 * Javascript pour le composant ACS Audio
 * See http://acs.geomaticien.org
 *
 * Fonctionne avec jQuery.
 *
 * Copyright Daniel FAIVRE 2007-2010
 */

soundManager.onerror = function() {
  jQuery(".mp3player").each(function(p) {
    jQuery(this).find(".track_id3").html('<a href="http://www.macromedia.com/">Download player ?</a>');
  });
}

function initSoundPlayers() {
	jQuery(".mp3player:not(._SPok)").each(function(p) {
		var sp = new SoundPlayer(this);
		jQuery(this).addClass("_SPok");
	});	
}

/* Attach SoundPlayer objects to mp3player class objects */
function SoundPlayer(player) {
	var pl = jQuery(player); 
	if (!pl.attr("id"))
			pl.attr("id", "p" + calcMD5(pl.html()));
	soundManager._writeDebug('* init SoundPlayer ' + pl.attr("id"));
	
	var self = this;
	var codehtml = new RegExp('[^<]*'); // à améliorer ... ;-)

	this.sounds = {};
	this.tracks = {};
	this.nbtracks = 0;
	this.track = 1;
	this.paused = false;

	this.createSound = function(href, html) {
		self.sounds[this.track] = {};
		this.sounds[this.track]["url"] = href;
		this.sounds[this.track]["id3"] = html;
//soundManager._writeDebug(" * read " + player.id + "_" + this.track + ' : ' + this.sounds[this.track]["url"]);
		this.track++;
	}

	this.soundManagerCreateSound = function(track, url) {
		var sndmd5 = "s" + calcMD5(url);
soundManager._writeDebug(" * create " + sndmd5 + " (" + url + ")");
		self.sounds[track]["snd"] = soundManager.createSound({
			"id": sndmd5,
			"url": url,
			"stream": true,
			"autoLoad": false,
			"autoPlay": false,
			"whileloading": self.whileLoading,
			"onload": self.whileLoading,
			"whileplaying": self.whilePlaying,
			"onfinish": self.onFinish,
			"onid3": self.onID3,
			"multiShot": false
		});
		self.tracks[sndmd5] = track;
		return self.sounds[track]["snd"];
	}

	this.whileLoading = function() {
		self.GUI.whileLoading(this);
	}

	this.whilePlaying = function() {
		self.GUI.whilePlaying(this);
	}

	this.onFinish = function() {
		if ((self.nbtracks > 1) && (self.track >= self.nbtracks))
			self.play(1); // Loop if in playlist
		else self.next();
	}

	this.onID3 = function() {
		// get id3 data and populate according to formatting string (%artist - %title [%album] etc.)
		var friendlyAttrs = {
		 // ID3V1 inherits from ID3V2 if populated
		 'title': 'songname', // songname/TIT2
		 'artist': 'artist', // artist/TPE1
		 'album': 'album', // album/TALB
		 'track': 'track', // track/TRCK
		 'year': 'year', // year/TYER
		 'genre': 'genre', // genre/TCON
		 'comment': 'comment', // comment/COMM
		 'url': 'WXXX'
		}
		// get normalised data, build string, replace
		var sData = "%{title} (%{artist})";
		var data = null;
		var useID3 = (!self.isEmpty(this.id3.songname) && !self.isEmpty(this.id3.artist)); // artist & title must be present to consider using ID3

		for (var attr in friendlyAttrs) {
			data = this.id3[friendlyAttrs[attr]];
			if (self.isEmpty(data)) data = '!null!';
			sData = sData.replace('\%\{'+attr+'\}',data);
		}
		// remove any empty/null fields
		var aData = sData.split(' ');
		for (var i=aData.length; i--;) {
			if (aData[i].indexOf('!null!')+1) aData[i] = null;
		}
		if (useID3) {
			var track = this.sID.substr(this.sID.indexOf('_') + 1);
			track = track.substr(track.indexOf('_') + 1);
			self.sounds[track]["id3"] = aData.join(' ');
		}
	}

	this.duree = function(snd) {
		if (self.isEmpty(snd)) return 0;
		if ((snd.readyState==3) && snd.loaded)
			return snd.duration;
		else
			return Math.max(snd.duration, snd.durationEstimate);
	}

	this.setPosition = function(pos) {
		var snd = self.sounds[self.track]["snd"];
		if (self.isEmpty(snd)) return false;
		soundManager.setPosition(snd.sID, pos * self.duree(snd));
	}

	this.play = function(i) {
		var snd = self.sounds[i]["snd"];
		if (!snd)
			snd = self.soundManagerCreateSound(i, self.sounds[i]["url"]);
		if (!snd)
			return false;
		if (snd.paused) {
			soundManager._writeDebug(' * resume ' + snd.sID);
			soundManager.resume(snd.sID);
			self.GUI.togglePause();
		}
		else if ((i==this.track) && (snd.playState==1) && ((snd.readyState==1) || (snd.readyState==3))) {
			soundManager._writeDebug(' * pause ' + snd.sID);
			soundManager.pause(snd.sID);
			self.GUI.togglePause();
		}
		else {
			if (i!=this.track) this.stop();
			soundManager.play(snd.sID);
			this.track = i;
			self.GUI.play(i);
		}
	}

	this.stop = function() {
		if (self.sounds[this.track]["snd"]) {
			soundManager._writeDebug(' * stop ' + self.sounds[this.track]["snd"].sID);
			soundManager.stop(self.sounds[this.track]["snd"].sID);
		}
		else
			soundManager.stopAll();
		self.GUI.stop(this.track);
	}

	this.next = function() {
		if (this.track > this.nbtracks) {
			return false;
		}
		this.play(this.track + 1);
		return true;
	}

	this.prev = function() {
		if (this.track <= 1) return false;
		this.play(this.track - 1);
		return true;
	}

	this.isEmpty = function(o) {
		return (typeof o == 'undefined' || o == null || o == 'null' || (typeof o == 'string' && o.toLowerCase() == 'n/a' || o.toLowerCase == 'undefined'));
	}

	pl.parent().find("a[rel='enclosure'][href$=mp3]").each(
			function(i) {
				var html = pl.html().match(codehtml);
				self.createSound(this.href, html + " " + this.title);
				jQuery(this).click(
					function(e) {
						e.preventDefault();
						self.play(i+1);
					}
				);
			}
		);
	pl.parent().find(".playliste li").each(function(i) {
		jQuery(this).hover(function(){
			jQuery(this).addClass("over");
		},function(){
			jQuery(this).removeClass("over");
		});
	});
	this.nbtracks = this.track - 1;
	this.track = 1;
	this.GUI = new spGUI(pl, self);
	if (pl.find(".autostart").html() == "")
		this.play(1);	
}

/* Set the player GUI and connect it to the soundPlayer object */
function spGUI(player,sp) {
	var self = this;
		
	this.id3pos = 0;
	this.lastX = 0;
	this.tid3max = 100;
	this.playAnimTime = 150; // ms
	this.progressBarBorder = player.find(".progressBarBorder");
	this.loadBar = player.find(".loadBar");
	this.progressBar = player.find(".progressBar");
	this.slider = player.find(".slider");
	this.position = player.find(".position");

	var ctl = player.find(".btn");
	ctl.find(".b_play").get(0).onclick = function() { sp.play(sp.track); };
	ctl.find(".b_stop").get(0).onclick = function() { sp.stop(); };
	ctl.find(".b_prev").get(0).onclick = function() { sp.prev(); };
	ctl.find(".b_next").get(0).onclick = function() { sp.next(); };
	ctl.find("li img").each(function(i) {
		jQuery(this).hover(function(){
			jQuery(this).attr("src",acsAudio_images + jQuery(this).attr("id") + '_over.png');
		},function(){
			jQuery(this).attr("src",acsAudio_images + jQuery(this).attr("id") + '.png');
		});
	});
	this.bw = self.progressBarBorder.width();
	
	soundManager._writeDebug(" * GUI bord=" + this.bw + " " + jQuery("#" + player.attr("id")).find(".progressBarBorder").width());

	this.hsw = player.find(".slider").width() / 2;
	this.mode = 'play';
	if (sp.nbtracks > 0)	player.css('opacity',1);
	if (sp.nbtracks > 1) {
		player.find(".track_control").removeClass('track_control');
	}
	self.progressBarBorder.get(0).onclick = function(e) {
		var ev=e?e:event;
		var pos = (ev.clientX - self.getOffX(self.progressBarBorder.get(0)) ) / self.bw;
		self.lastPos = 0;
		sp.setPosition(pos);
	}
	player.find(".track_index").html("1");
	self.position.html("000:000");

	self.slider.draggable({
		helper: "original",
		axis: "x",
		containment: self.progressBarBorder,
		cursor: "e-resize",
		stop: function(e,ui) {
			var pos = (e.clientX - self.getOffX(self.progressBarBorder.get(0)) ) / self.bw;
			self.lastPos = 0;
			sp.setPosition(pos);
		}
	});

	this.play = function(i) {
		this.lastPos = 0;
		player.find(".track_index").html(i);
		player.parent(".playlist").find(".pl_l" + i).addClass("play_on");
		player.find(".b_play").addClass("play_on");
		if (sp.sounds[i]["snd"].readyState == 3)
			self.loadBar.width(self.bw);
		self.togglePause();
		if (this.mode == 'play') self.togglePause();
		var duree = Math.round(sp.duree(sp.sounds[sp.track]["snd"])/1000);
		var chiffres = Math.ceil((Math.log(duree+1))/Math.log(10));
		self.position.html(self.nb(0,chiffres) + ':' + duree);
		self.whilePlaying(sp.sounds[i]["snd"]);		
	};


	this.stop = function(i) {
		self.progressBar.width(0);
		player.parent(".playlist").find(".pl_l" + i).removeClass("play_on");
		player.find(".b_play").removeClass("play_on");
		player.find(".btn").find("li img").each(function(i) {
				jQuery(this).attr("src",acsAudio_images + jQuery(this).attr("id") + '.png');
		});
		self.slider.css("left","-" + self.hsw + "px");
		var duree = Math.round(sp.duree(sp.sounds[sp.track]["snd"])/1000);
		var chiffres = Math.ceil((Math.log(duree+1))/Math.log(10));
		self.position.html(self.nb(0,chiffres) + ':' + duree);
		player.find(".track_id3").html("");
		this.id3pos = 0;
		this.lastPos = 0;
		if (this.mode == 'pause') self.togglePause();
	};

	this.togglePause = function() {
		if (this.mode == 'pause') {
			this.mode = 'play';
			player.find("img.b_play").attr("id", "b_play");
		}
		else {
			this.mode = 'pause';
			player.find("img.b_play").attr("id", "b_pause");
		}
		var img = player.find("img.b_pause").attr("src");
		player.find("img.b_pause").attr("src", player.find("img.b_play").attr("src"));
		player.find("img.b_play").attr("src", img);
	};

	this.whileLoading = function(snd) {
		var track = sp.tracks[snd.sID];
		var lplayer = jQuery("#" + player.attr("id")); // le player lie au son
		var loaded = snd.loaded ? 1 : snd.bytesLoaded / snd.bytesTotal;
		if (sp.track == track)
			self.loadBar.width( parseInt(lplayer.find(".progressBarBorder").width() * loaded) + "px");
		loaded = Math.round(loaded*100);
		if (loaded) {
			trackstatus = (loaded < 99) ? loaded + "%" : ".";
		}
		else {
			trackstatus = "x";
			lplayer.parent().find(".pl_dl" + track).addClass("alert");
		}
		lplayer.parent().find(".pl_dl" + track).html(trackstatus);
	};
	
	this.whilePlaying = function(snd) {
		self.whileLoading(snd);
		if (Math.abs(self.lastPos - snd.position) < self.playAnimTime)
			return false;
		var track = sp.tracks[snd.sID];
		if (sp.track == track) {
			var lplayer = jQuery("#" + player.attr("id")); // le player lie au son
			self.lastPos = snd.position;
			var duree = sp.duree(snd);
			var bw = lplayer.find(".progressBarBorder").width();
			var x = bw * snd.position / duree;
			x = parseInt(Math.min(x, bw)); /* antibug ;-) */	
			if (x != self.lastX) {
				lplayer.find(".progressBar").width(x);
				lplayer.find(".slider").css("left",parseInt(x - self.hsw));
				self.lastX = x;
			}
			duree = Math.round(duree/1000);
			var chiffres = Math.ceil((Math.log(duree+1))/Math.log(10));
			lplayer.find(".position").html(this.nb(Math.round(snd.position/1000), chiffres) + ':' + duree);
			if (sp.sounds[track]["id3"]) {
				if (self.id3pos >= sp.sounds[track]["id3"].length)
					self.id3pos = 0;
				var txt = sp.sounds[track]["id3"].substr(self.id3pos++, self.tid3max);
				while (txt.length < self.tid3max)
					txt = txt + ' ' + sp.sounds[track]["id3"].substr(0, Math.min(sp.sounds[track]["id3"].length, self.tid3max - txt.length));
				lplayer.find(".track_id3").html(txt.substr(0, self.tid3max));
			}
		}
	};

	this.nb = function(nombre, chiffres) {
		nombre = String(nombre);
		while(nombre.length < chiffres)
			nombre = "0" + nombre;
		return nombre;
	};

	this.getOffX = function(o) {
		// http://www.xs4all.nl/~ppk/js/findpos.html
		var curleft = 0;
		if (o.offsetParent) {
			while (o.offsetParent) {
				curleft += o.offsetLeft;
				o = o.offsetParent;
			}
		}
		else if (o.x) curleft += o.x;
		return curleft;
	};

}
