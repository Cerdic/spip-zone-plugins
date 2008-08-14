/**
 * Javascript pour le composant ACS Audio
 * See http://acs.geomaticien.org
 *
 * Fonctionne avec jQuery.
 *
 * Copyright Daniel FAIVRE 2008
 */

soundManager.onerror = function() {
  jQuery(".mp3player").each(function(p) {
    jQuery(this).find(".track_id3").html('<a href="http://www.macromedia.com/">Download player ?</a>');
  });
}

function initSoundPlayers() {
  var codehtml = new RegExp('[^<]*'); // à améliorer ... ;-)
  soundManager._writeDebug('audio.js : initSoundPlayers()');

  jQuery(".mp3player").each(function(p) {
    var sp = new SoundPlayer(p+1,this);
    jQuery(this).parent().find("a[@rel='enclosure'][@href$=mp3]").each(
      function(i) {
        var html = jQuery(this).html().match(codehtml);
        sp.createSound(this.href, html + " " + this.title);
        jQuery(this).click(
          function(e) {
            e.preventDefault();
            sp.play(i+1);
          }
        );
      }
    );
    sp.init();
  });
}

/* Attach SoundPlayer objects to mp3player class objects */
function SoundPlayer(p, player) {
  var self = this;
  this.sounds = {};
  this.tracks = 0;
  this.track = 1;
  this.position = 0;
  this.paused = false;

  this.init = function() {
    this.tracks = this.track - 1;
    this.track = 1;
    this.GUI = new spGUI(player,self);
    if (jQuery(player).find(".autostart").html() == "")
      this.play(1);
  }

  this.createSound = function(href, html) {
    self.sounds[this.track] = {};
    this.sounds[this.track]["url"] = href;
    this.sounds[this.track]["id3"] = html;
    soundManager._writeDebug(' * ' + "mp3_" + p + "_" + this.track + ' : ' + this.sounds[this.track]["url"]);
    this.track++;
  }

  this.soundManagerCreateSound = function(track, url) {
    soundManager._writeDebug(' * create ' + "mp3_" + p + "_" + track);
    soundManager.createSound({
      "id": "mp3_" + p + "_" + track,
      "url": url,
      "stream": true,
      "autoLoad": false,
      "autoPlay": false,
      "whileloading": self.whileLoading,
      "whileplaying": self.whilePlaying,
      "onfinish": self.onFinish,
      "onid3": self.onID3,
      "multiShot": false
    });
    self.sounds[track]["snd"] = soundManager.getSoundById("mp3_" + p + "_" + track);
    self.sounds[track]["loaded"] = 0;
    return self.sounds[track]["snd"];
  }

  this.whileLoading = function() {
    var track = this.sID.substr(this.sID.indexOf('_') + 1);
    track = track.substr(track.indexOf('_') + 1);
    self.sounds[track]["loaded"] = this.bytesLoaded / this.bytesTotal;
    self.GUI.whileLoading(track);
  }

  this.whilePlaying = function() {
    self.position = this.position;
    var track = this.sID.substr(this.sID.indexOf('_') + 1);
    track = track.substr(track.indexOf('_') + 1);
    self.GUI.whilePlaying(track);
  }

  this.onFinish = function() {
    if (self.track >= self.tracks) self.play(1); // Loop
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

  this.duree = function(track) {
    var snd = self.sounds[track]["snd"];
    if ((snd.readyState==3) && snd.loaded)
      return snd.duration;
    else
      return Math.max(snd.duration, snd.durationEstimate);
  }

  this.isLoaded = function(i) {
    return (self.sounds[i]["snd"].readyState == 3);
  }

  this.setPosition = function(pos) {
    var snd = self.sounds[this.track]["snd"];
    soundManager.setPosition("mp3_" + p + "_" + this.track, pos * this.duree(this.track));
    this.position = snd.position;
  }

  this.play = function(i) {
    //soundManager._writeDebug(' * play ' + "mp3_" + p + "_" + i);
    var snd = self.sounds[i]["snd"];
    if (!snd)
      snd = this.soundManagerCreateSound(i, self.sounds[i]["url"]);
    if (!snd)
      return false;
    if (snd.paused) {
      soundManager._writeDebug(' * resume ' + "mp3_" + p + "_" + i);
      soundManager.resume("mp3_" + p + "_" + i);
      self.GUI.togglePause();
    }
    else if ((i==this.track) && (snd.playState==1) && ((snd.readyState==1) || (snd.readyState==3))) {
      soundManager._writeDebug(' * pause ' + "mp3_" + p + "_" + i);
      soundManager.pause("mp3_" + p + "_" + i);
      self.GUI.togglePause();
    }
    else {
      if (i!=this.track) this.stop();
      soundManager.play("mp3_" + p + "_" + i);
      this.track = i;
      self.GUI.play(i);
    }
  }

  this.stop = function() {
    if (self.sounds[this.track]["snd"]) {
      //soundManager._writeDebug(' * stop ' + "mp3_" + p + "_" + this.track);
      soundManager.stop("mp3_" + p + "_" + this.track);
    }
    else
      soundManager.stopAll();
    self.GUI.stop(this.track);
  }

  this.next = function() {
    if (this.track > this.tracks) {
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
}

/* Set the player GUI and connect it to the soundPlayer object */
function spGUI(player,sp) {
  var self = this;
  this.id3pos = 0;
  this.lastX = 0;
  this.tid3max = 100;
  this.playAnimTime = 150;

  var ctl = jQuery(player).find(".btn");
  ctl.find(".b_play").get(0).onclick = function() { sp.play(sp.track); };
  ctl.find(".b_stop").get(0).onclick = function() { sp.stop(); };
  ctl.find(".b_prev").get(0).onclick = function() { sp.prev(); };
  ctl.find(".b_next").get(0).onclick = function() { sp.next(); };
  ctl.find("li img").each(function(i) {
    jQuery(this).hover(function(){
      jQuery(this).attr("src",acsAudio_img_pack + jQuery(this).attr("id") + '_over.png');
    },function(){
      jQuery(this).attr("src",acsAudio_img_pack + jQuery(this).attr("id") + '.png');
    });
  });
  this.bw = jQuery(player).find(".progressBarBorder").width();
  this.mode = 'play';
  if (sp.tracks > 0)  jQuery(player).css('opacity',1);
  if (sp.tracks > 1) {
    jQuery(player).find(".track_control").removeClass('track_control');
  }
  jQuery(player).find(".progressBarBorder").get(0).onclick = function(e) {
    var ev=e?e:event;
    var pos = (ev.clientX - self.getOffX(jQuery(player).find(".progressBarBorder").get(0)) ) / self.bw;
    sp.setPosition(pos);
    self.lastPos = 0;
  }

  this.play = function(i) {
    jQuery(player).find(".track_index").html(i);
    jQuery(player).find("..").find(".pl_l" + i).addClass("play_on");
    jQuery(player).find(".b_play").addClass("play_on");
    if (sp.isLoaded(i))
      jQuery(player).find(".loadBar").width(self.bw);
    self.togglePause();
    if (this.mode == 'play') self.togglePause();
  };


  this.stop = function(i) {
    jQuery(player).find(".loadBar").width(0);
    jQuery(player).find(".progressBar").width(0);
    jQuery(player).find("..").find(".pl_l" + i).removeClass("play_on");
    jQuery(player).find(".b_play").removeClass("play_on");
    jQuery(player).find(".btn").find("li img").each(function(i) {
        jQuery(this).attr("src",acsAudio_img_pack + jQuery(this).attr("id") + '.png');
    });
    jQuery(player).find(".slider").get(0).style.left = "-" + parseInt(jQuery(player).find(".slider").width() / 2) + "px";
    jQuery(player).find(".position").html('000:000');
    jQuery(player).find(".track_id3").html("");
    this.id3pos = 0;
    this.lastPos = 0;
    if (this.mode == 'pause') self.togglePause();
  };

  this.togglePause = function() {
    if (this.mode == 'pause') {
      this.mode = 'play';
      jQuery(player).find("img.b_play").attr("id", "b_play");
    }
    else {
      this.mode = 'pause';
      jQuery(player).find("img.b_play").attr("id", "b_pause");
    }
    var img = jQuery(player).find("img.b_pause").attr("src");
    jQuery(player).find("img.b_pause").attr("src", jQuery(player).find("img.b_play").attr("src"));
    jQuery(player).find("img.b_play").attr("src", img);
  };

  this.whileLoading = function(track) {
    this.update(track);
  };

  this.whilePlaying = function(track) {
    this.update(track);
    if (sp.track == track)
      this.playAnimation(track);
  };

  this.update = function(track) {
    var loaded = sp.sounds[track]["loaded"];
    if (sp.track == track)
      jQuery(player).find(".loadBar").width( parseInt(this.bw * loaded) + "px");
    loaded = Math.round(loaded*100);
    if (loaded >= 99)
      jQuery(player).find("..").find(".pl_dl" + track).html(".");
    else
      jQuery(player).find("..").find(".pl_dl" + track).html(loaded + "%");
  }

  this.playAnimation = function(track) {
    if ((this.lastPos + this.playAnimTime) > sp.position)
      return false;
    this.lastPos = sp.position;
    var x = parseInt(self.bw * sp.position / sp.duree(track));
    x = Math.min(x, self.bw); /* antibug ;-) */
    if (x != this.lastX) {
      jQuery(player).find(".progressBar").width( x + "px");
      jQuery(player).find(".slider").get(0).style.left = parseInt(x - jQuery(player).find(".slider").width() / 2) + "px";
      this.lastX = x;
    }
    var duree = Math.round(sp.duree(track)/1000);
    var chiffres = Math.ceil((Math.log(duree+1))/Math.log(10));
    jQuery(player).find(".position").html(this.nb(Math.round(sp.position/1000), chiffres) + ':' + duree);
    if (sp.sounds[track]["id3"]) {
      if (this.id3pos >= sp.sounds[track]["id3"].length)
        this.id3pos = 0;
      var txt = sp.sounds[track]["id3"].substr(this.id3pos++, this.tid3max);
      while (txt.length < this.tid3max)
        txt = txt + ' ' + sp.sounds[track]["id3"].substr(0, Math.min(sp.sounds[track]["id3"].length, this.tid3max - txt.length));
      jQuery(player).find(".track_id3").html(txt.substr(0, this.tid3max));
    }
  }

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