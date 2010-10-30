/*

  SoundManager 2 Demo: Play MP3 links "in-place"
  ----------------------------------------------

  http://schillmania.com/projects/soundmanager2/

  A simple demo making MP3s playable "inline"
  and easily styled/customizable via CSS.

  Requires SoundManager 2 Javascript API.

*/

$(document).ready(function(){

liveTrackIndex = 0 ;


function InlinePlayer() {
  var self = this;
  var pl = this;
  var sm = soundManager; // soundManager instance
  this.excludeClass = 'inline-exclude'; // CSS class for ignoring MP3 links
  this.links = [];
  this.sounds = [];
  this.soundsByURL = [];
  this.indexByURL = [];
  this.lastSound = null;
  this.soundCount = 0;
  var isIE = (navigator.userAgent.match(/msie/i));

  this.config = {
    playNext: true, // stop after one sound, or play through list until end
	autoPlay: false  // start playing the first sound right away
  }

  this.css = {
    // CSS class names appended to link during various states
    sDefault: 'sm2_link', // default state
    sLoading: 'sm2_loading',
    sPlaying: 'sm2_playing',
    sPaused: 'sm2_paused'
  }

  this.addEventHandler = function(o,evtName,evtHandler) {
    typeof(attachEvent)=='undefined'?o.addEventListener(evtName,evtHandler,false):o.attachEvent('on'+evtName,evtHandler);
  }

  this.removeEventHandler = function(o,evtName,evtHandler) {
    typeof(attachEvent)=='undefined'?o.removeEventListener(evtName,evtHandler,false):o.detachEvent('on'+evtName,evtHandler);
  }

  this.classContains = function(o,cStr) {
	return (typeof(o.className)!='undefined'?o.className.match(new RegExp('(\\s|^)'+cStr+'(\\s|$)')):false);
  }

  this.addClass = function(o,cStr) {
    if (!o || !cStr || self.classContains(o,cStr)) return false;
    o.className = (o.className?o.className+' ':'')+cStr;
  }

  this.removeClass = function(o,cStr) {
    if (!o || !cStr || !self.classContains(o,cStr)) return false;
    o.className = o.className.replace(new RegExp('( '+cStr+')|('+cStr+')','g'),'');
  }

  this.getSoundByURL = function(sURL) {
    return (typeof self.soundsByURL[sURL] != 'undefined'?self.soundsByURL[sURL]:null);
  }

  this.isChildOfNode = function(o,sNodeName) {
    if (!o || !o.parentNode) {
      return false;
    }
    sNodeName = sNodeName.toLowerCase();
    do {
      o = o.parentNode;
    } while (o && o.parentNode && o.nodeName.toLowerCase() != sNodeName);
    return (o.nodeName.toLowerCase() == sNodeName?o:null);
  }

  this.events = {

    // handlers for sound events as they're started/stopped/played

    play: function() {
      liveTrackIndex = pl.indexByURL[this._data.oLink.href] ;
      sm2_update_button();	
      pl.removeClass(this._data.oLink,this._data.className);
      this._data.className = pl.css.sPlaying;
      pl.addClass(this._data.oLink,this._data.className);
      $("#sm2_loading").css("cursor","hand");
    },
    
    whileloading: function() {
	  $(".sm2_duration").html(sm2_getHMSTime(this.durationEstimate,true));
	  var timer = this.bytesLoaded / this.bytesTotal * 100 ;
	  $("#sm2_loading").css({width:Math.round(timer) +"%"});
    },

	whileplaying: function() {
	  var timer2 = this.position / this.durationEstimate * 100 ;
	  $("#sm2_position").css({width:Math.round(timer2) +"%"});
	  $(".sm2_position").html(sm2_getHMSTime(this.position,true));
    },

    stop: function() {
      pl.removeClass(this._data.oLink,this._data.className);
      this._data.className = '';
    },

    pause: function() {
      sm2_update_button();	
      pl.removeClass(this._data.oLink,this._data.className);
      this._data.className = pl.css.sPaused;
      pl.addClass(this._data.oLink,this._data.className);
    },

    resume: function() {
      sm2_update_button();
      pl.removeClass(this._data.oLink,this._data.className);
      this._data.className = pl.css.sPlaying;
      pl.addClass(this._data.oLink,this._data.className);
    },

    finish: function() {
      sm2_update_button();	
      pl.removeClass(this._data.oLink,this._data.className);
      this._data.className = '';
      if (pl.config.playNext) {
        var nextLink = (pl.indexByURL[this._data.oLink.href]+1);
        if (nextLink<pl.links.length) {
          pl.handleClick({'target':pl.links[nextLink]});
        }
      }
    }

  }

  this.stopEvent = function(e) {
   if (typeof e != 'undefined' && typeof e.preventDefault != 'undefined') {
      e.preventDefault();
    } else if (typeof event != 'undefined' && typeof event.returnValue != 'undefined') {
      event.returnValue = false;
    }
    return false;
  }

  this.getTheDamnLink = (isIE)?function(e) {
    // I really didn't want to have to do this.
    return (e && e.target?e.target:window.event.srcElement);
  }:function(e) {
    return e.target;
  }

  this.handleClick = function(e) {
    // a sound link was clicked
    if (typeof e.button != 'undefined' && e.button>1) {
	  // ignore right-click
	  return true;
    }
    var o = self.getTheDamnLink(e);
    if (o.nodeName.toLowerCase() != 'a') {
      o = self.isChildOfNode(o,'a');
      if (!o) return true;
    }
    var sURL = o.getAttribute('href');
    if (!o.href || !(o.href.match(/\.mp3(\\?.*)$/i) && o.rel.match(/enclosure/i)) || self.classContains(o,self.excludeClass)) {
      if (isIE && o.onclick) {
        return false; // IE will run this handler before .onclick(), everyone else is cool?
      }
      return true; // pass-thru for non-MP3/non-links
    }
    sm._writeDebug('handleClick()');
    var soundURL = (o.href);
    var thisSound = self.getSoundByURL(soundURL);
    if (thisSound) {
      // already exists
      if (thisSound == self.lastSound) {
        // and was playing (or paused)
        thisSound.togglePause();
      } else {
        // different sound
        thisSound.togglePause(); // start playing current
        sm._writeDebug('sound different than last sound: '+self.lastSound.sID);
        if (self.lastSound) self.stopSound(self.lastSound);
      }
    } else {
      // create sound
      thisSound = sm.createSound({
       id:'media'+(self.soundCount++),
       url:soundURL,
       onplay:self.events.play,
       whileloading:self.events.whileloading,
       whileplaying:self.events.whileplaying,
       onstop:self.events.stop,
       onpause:self.events.pause,
       onresume:self.events.resume,
       onfinish:self.events.finish
      });
      // tack on some custom data
      thisSound._data = {
        oLink: o, // DOM node for reference within SM2 object event handlers
        className: self.css.sPlaying
      };
      self.soundsByURL[soundURL] = thisSound;
      self.sounds.push(thisSound);
      if (self.lastSound) self.stopSound(self.lastSound);
      thisSound.play();
      // stop last sound
    }
    

    self.lastSound = thisSound; // reference for next call

    if (typeof e != 'undefined' && typeof e.preventDefault != 'undefined') {
      e.preventDefault();
    } else {
      event.returnValue = false;
    }
    return false;
  }

  this.stopSound = function(oSound) {
    soundManager.stop(oSound.sID);
    soundManager.unload(oSound.sID);
  }

  this.init = function() {
    sm._writeDebug('inlinePlayer.init()');
    var oLinks = document.getElementsByTagName('a');
    // grab all links, look for .mp3
    var foundItems = 0;
    for (var i=0; i<oLinks.length; i++) {
      if (oLinks[i].href.match(/\.mp3/i) && oLinks[i].rel.match(/enclosure/i) && !self.classContains(oLinks[i],self.excludeClass)) {
        self.addClass(oLinks[i],self.css.sDefault); // add default CSS decoration
        self.links[foundItems] = (oLinks[i]);
        self.indexByURL[oLinks[i].href] = foundItems; // hack for indexing
     
     /**
      * creation ici déja pour les features graphiques 
      */
           thisSound = sm.createSound({
	       id:'media'+(foundItems),
	       url:oLinks[i].href,
	       onplay:self.events.play,
	       whileloading:self.events.whileloading,
	       whileplaying:self.events.whileplaying,
	       onstop:self.events.stop,
	       onpause:self.events.pause,
	       onresume:self.events.resume,
	       onfinish:self.events.finish
	       });
	       
	       // tack on some custom data
      	   thisSound._data = {
           oLink: oLinks[i], // DOM node for reference within SM2 object event handlers
           className: self.css.sPlaying
           };
           
           self.soundsByURL[oLinks[i].href] = thisSound;

	 // fin ajout

        foundItems++;
      }
    }
    // au debut lastSound est le premier
    var thisSound = soundManager.getSoundById('media0');
    self.lastSound = thisSound ; // reference for next call
	
	// afficher les titres avant de démarrer
	afficher_titres();	

    if (foundItems>0) {
      self.addEventHandler(document,'click',self.handleClick);
	  if (self.config.autoPlay) {
	    self.handleClick({target:self.links[0],preventDefault:function(){}});
	  }
    }
    sm._writeDebug('inlinePlayer.init(): Found '+foundItems+' relevant items.');
  }

  this.init();

}

var inlinePlayer = null;

// soundManager.debugMode = true; // disable or enable debug output

soundManager.url = REPSWF ; // path to directory containing SM2 SWF

soundManager.onready(function() {
  if (soundManager.supported()) {
    // soundManager.createSound() etc. may now be called
    inlinePlayer = new InlinePlayer();
  }
});

	


	
	/**
	* Fonction play
	*/
	
	function sm2_play_pause(tIndex){
		var thisSound = soundManager.getSoundById('media'+tIndex);
	      		    
	    if (thisSound) {
	      // already exists
	      if (thisSound == inlinePlayer.lastSound) {
	        // and was playing (or paused)
	        thisSound.togglePause();
	      } else {
	        // different sound
	        soundManager._writeDebug('sound different than last sound: '+inlinePlayer.lastSound.sID);
	        if (inlinePlayer.lastSound){ 
				soundManager.stopAll();
	        	soundManager.stop(inlinePlayer.lastSound);
	        	soundManager.unload(inlinePlayer.lastSound);
	        }
	        thisSound.togglePause(); // start playing current
	        inlinePlayer.lastSound = thisSound; // reference for next call
	      }
	    } 
	
	}
	
	/**
	* interface graphique
	*/
	
	/**
	 *
	 * Les actions des boutons de la playliste
	 *
	 */
	 
	$('#sm2_player_play,#sm2_player_pause').click(function(e){
		e.preventDefault();
		sm2_play_pause(liveTrackIndex);
	});
	
	// couper / remettre le son
	
	$('#sm2_player_volume,#sm2_player_mute').click(function(){
		var thisSound = soundManager.getSoundById('media'+liveTrackIndex);
		if(thisSound.muted){
			soundManager.unmute();
		}else{
			soundManager.mute();
		}
		sm2_update_button();
	});
	
	
	/**
	 * Affichage du bouton de lecture ou du bouton pause et du son on / off
	 */
	function sm2_update_button(){
		var thisSound = soundManager.getSoundById('media'+liveTrackIndex);
		$("#sm2_player_play").css("display", (!thisSound.paused)?"none":"block");
		$("#sm2_player_pause").css("display", (!thisSound.paused)?"none":"block");
		$("#sm2_player_play").css("display", (thisSound.paused)?"block":"none");
		$("#sm2_player_pause").css("display", (!thisSound.paused)?"block":"none");
		$("#sm2_player_volume").css("display", (thisSound.muted)?"none":"block");
		$("#sm2_player_mute").css("display", (thisSound.muted)?"block":"none");
		
		afficher_titres(liveTrackIndex);
	}
	
	// precedent / suivant
	
	$('#sm2_player_prev').click(function(e){
		e.preventDefault();
		if(liveTrackIndex>0){
			sm2_play_pause(liveTrackIndex -1);
		}
	});
	
	$('#sm2_player_next').click(function(e){
		e.preventDefault();
		if(liveTrackIndex != (inlinePlayer.soundsByURL.length - 1)){
			sm2_play_pause(liveTrackIndex + 1);
		}
	});
	
	// click et survol des scrollbar
	
	$('#sm2_loading,#sm2_position').click(function(e){
		e.preventDefault();
		if('media'+liveTrackIndex){
			var son = soundManager.getSoundById('media'+liveTrackIndex);
			var duree = son.durationEstimate;
			var offset = jQuery("#sm2_loading").offset();
			var x = Math.round((e.pageX - offset.left) / jQuery("#sm2_scrollbar").width() * 100);
			var temps = Math.round(duree * x / 100);
			$("#sm2_position").css({width:Math.round(x) +"%"});
			if(son.playState == 0){
				soundManager.play('media'+liveTrackIndex);
				son.setPosition(temps);
				soundManager.pause('media'+liveTrackIndex);
				$(".sm2_position").html(sm2_getHMSTime(son.position,true));
				sm2_update_button();
			}else{
				son.setPosition(temps);
			}
		}
	});
	
	$('#sm2_scrollbar').prepend('<div class="sm2_jump_position"></div>');
	$('#sm2_scrollbar').hover(function(e){
		if('media'+liveTrackIndex){
			var son = soundManager.getSoundById('media'+liveTrackIndex);
			if(son){
			var duree = son.durationEstimate;
			var scroll_width = $('#sm2_scrollbar').width();
			var scroll_left = jQuery("#sm2_scrollbar").offset().left;
			var percent_scrollbar = (((e.pageX - scroll_left)) / scroll_width * 100);
			var duree_ml = Math.round(duree * percent_scrollbar /100);
			var temps = sm2_getHMSTime(duree_ml,true);
			$('.sm2_jump_position').html(temps).fadeIn();
			$('#sm2_scrollbar').unbind('mousemove').mousemove(function(e){
				var percent_scrollbar = (((e.pageX - scroll_left)) / scroll_width * 100);
				var duree_ml = Math.round(duree * percent_scrollbar /100);
				var temps = sm2_getHMSTime(duree_ml,true);
				$('.sm2_jump_position').html(temps).css('left',percent_scrollbar+'%');
			});
			}
		}
		},function(){
			if('media'+liveTrackIndex){$('.sm2_jump_position').fadeOut();}		
	});

	/**
	 * Conversion de millisecondes en temps mm:ss
	 * Retourne un objet javascript ou une chaîne de caractères
	 */
	function sm2_getHMSTime(nbMSec,bAsString){
		// convert milliseconds to mm:ss, return as object literal or string
		var nbSec = Math.floor(nbMSec/1000);
		var min = Math.floor(nbSec/60);
		var sec = nbSec-(min*60);
		return (bAsString?(min+':'+(sec<10?'0'+sec:sec)):{'min':min,'sec':sec});
	}
	
	/**
	 * Affichage des titres sur le player
	 */
	
	function afficher_titres(tIndex){
	// chopper le titre du lien actuel, et celui du suivant
		
		var thisSound = soundManager.getSoundById('media'+liveTrackIndex);
		
		// console.log(thisSound);
		
		var thisTitle =  $(thisSound._data.oLink).html() ;
		$("#sm2_title_value").html(thisTitle.substr(0,80));
		
		var nextTrack = tIndex + 1 ;
		var nextSound = soundManager.getSoundById('media'+ nextTrack);
		if(nextSound)
			var nextTitle = $(nextSound._data.oLink).html() ;

		$("#sm2_title_next_value").css("cursor","pointer").html("").html(nextTitle);
		$("#sm2_title_next_value").click(function(e){
			e.preventDefault();
			if(liveTrackIndex != (inlinePlayer.soundsByURL.length - 1)){
				sm2_play_pause(liveTrackIndex + 1);
			}
		});
	}
	

});