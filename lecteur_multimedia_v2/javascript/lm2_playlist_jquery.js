/**
 * media_playliste - jQuery Plugin
 * http://dev.aires-de-confluxence.info
 *
 * Copyright (c) 2009 kent1, BoOz
 * Licensed under the GNU GPL v3 licence
 *
 * $version: 0.0.1
 */

(function($){
	var liens = [];
	var track_index;
	var live_track = false;
	var isPlaying = false;
	var isPaused = false;
	var typePlaying = false;
	var isvideo;
	var issound = /\.(mp3|aac)(\?.*)?$/i;
	var options = {};
	var sm2_events = {};
	
	$.fn.playlist = function(options) {
		var defaults = {
			sources : $('a[type=application/mp4],a[type=audio/mpeg],a[type=video/x-flv]'),
			smUrl : "soundmanager/swf/",
			smnullUrl : "soundmanager/null.mp3",
			logo : false,
			playNext: false, // stop after one sound, or play through list until end
			autoLoad: false,
			autoPlay: false,
			movie: true,
			bgcolor: '#000000',
			fullscreen: false,
			wmode: 'transparent',
			debug: false
		};
		
		sm2_events = {
			play:function(){
				isPlaying = true;
				isPaused = false;
				$("#sm2_loading").css("cursor","hand");
				$(".sm2_duration").html(sm2_getHMSTime(this.durationEstimate,true));
				sm2_update_button();
			},
			finish:function(){
				if(options.playNext){
					sm2_player_play(i+1);
				}
				isPlaying = false;
				isPaused = false;
				sm2_update_button();
			},     
			id3:function(){
				
			},         
			pause:function(){
				isPaused = true;
				isPlaying = false;
				sm2_update_button();
			},
			resume:function(){
				isPaused = false;
				isPlaying = true;
				sm2_update_button();
			},
			stop:function(){
				isPlaying = false;
				isPaused = false;
				$('.playliste .sm2_play_on').removeClass('sm2_play_on');
				sm2_update_button();
			},
			load:function(){
			},              
			whileloading:function(){
				var timer = this.bytesLoaded / this.bytesTotal * 100 ;
				$(".sm2_duration").html(sm2_getHMSTime(this.durationEstimate,true));
				$("#sm2_loading").css({width:Math.round(timer) +"%"});
			},
			whileplaying:function(){
				var timer2 = this.position / this.durationEstimate * 100 ;
				$("#sm2_position").css({width:Math.round(timer2) +"%"});
				$(".sm2_position").html(sm2_getHMSTime(this.position,true));
				sm2_update_button();
			}	
		}
		options = $.extend(defaults, options);
		
		soundManager.bgColor = options.bgcolor;
		soundManager.wmode = options.wmode;
		soundManager.url = options.smUrl;
		soundManager.nullURL = options.smnullUrl;
		soundManager.allowFullScreen = options.fullscreen;
		soundManager.useHighPerformance = true;
		
		if(options.movie){
			isvideo = /\.(flv|mov|mp4|m4v|f4v|m4a|mp4v|3gp|3g2)(\?.*)?$/i;
			soundManager.useMovieStar = true;
			soundManager.flashVersion = 9;
			soundManager.defaultOptions = {
				useVideo: true
			}
		}
		
		if(options.debug){
			soundManager.consoleOnly = true;
			soundManager.debugMode = true;	
		}else{
			soundManager.consoleOnly = false;
			soundManager.debugMode = false;
		}
		
		$this = $(this);
		var playliste_text = '';
		var nb = 0;
		options.sources.each(function() {
			$source = $(this);
			var sURL = $source.attr('href');
			if(($.inArray(sURL,liens)<0) ) {
				playliste_text += '<li class="sm2_play"><a href='+$source.attr('href')+'>'+sm2_joli_titre($source.attr('href'))+'</a></li>';
				liens.push($source.attr('href'));
				nb++;
			}
		});
		if(nb>0){
			/**
			 * 
			 * Création de la playliste
			 * 
			 */
			var lecteur = '<div id="sm2_player">'
				+ '<div id="sm2-container"> </div>'
				+ '<div id="sm2_controllers">'
				+ '<div id="sm2_player_play" class="button play">PLAY</div>'
				+ '<div id="sm2_player_pause" class="button pause">PAUSE</div>';
			if(nb>1){
				lecteur += '<div id="sm2_player_prev" class="button prev">PRECEDENT</div>'
				+ '<div id="sm2_player_next" class="button next">SUIVANT</div>';
			}
			lecteur += '<div id="sm2_player_stop" class="button stop">STOP</div>'
				+ '</div>'
				+ '<div><span class="sm2_position"></span> / <span class="sm2_duration"></span>'
				+ '<div id="sm2_scrollbar"><div id="sm2_loading"></div><div id="sm2_position"></div></div>'
				+ '</div>'
				+ '</div>';
			playliste_text = $('<ul class="playliste"></ul>').append(playliste_text);
			$this.html(playliste_text);
			$('ul.playliste').wrap(lecteur);
			$this.find('li.sm2_play a').click(function(e){
				e.preventDefault();
				sm2_player_play($.inArray($(this).attr('href'),liens));
			});
			
			/**
			 * 
			 * Les actions des boutons de la playliste
			 * 
			 */
			$('#sm2_player_play').click(function(e){
				e.preventDefault();
				if(!isPlaying && !isPaused && !live_track){
					sm2_player_play(0);
				}else if(!isPlaying && !isPaused){
					sm2_player_play(live_track);
				}else{
					sm2_player_togglePause();
				}
			});
			$('#sm2_player_stop').click(function(e){
				e.preventDefault();
				if(isPlaying||isPaused){
					sm2_player_stop();
				}
			});
			$('#sm2_player_pause').click(function(e){
				e.preventDefault();
				if(!isPaused){
					sm2_player_togglePause();
				}
			});
			$('#sm2_player_prev').click(function(e){
				e.preventDefault();
				if(live_track>0){
					sm2_player_play(live_track - 1);
				}
			});
			$('#sm2_player_next').click(function(e){
				e.preventDefault();
				if(live_track != (nb - 1)){
					sm2_player_play(live_track + 1);
				}
			});
			$('#sm2_loading,#sm2_position').click(function(e){
				e.preventDefault();
				if(live_track){
					var son = soundManager.getSoundById('media_'+live_track);
					var duree = son.durationEstimate;
					var offset = jQuery("#sm2_loading").offset();
					var x = Math.round((e.pageX - offset.left) / jQuery("#sm2_scrollbar").width() * 100);
					var temps = Math.round(duree * x / 100);
					son.setPosition(temps,true);
					$("#sm2_position").css({width:Math.round(x) +"%"});
					if(son.playState == 0){
						soundManager.play('media_'+live_track);
						soundManager.pause('media_'+live_track);
						$(".sm2_position").html(sm2_getHMSTime(son.position,true));
						sm2_update_button();
					}
				}
			});
		}else{
			playliste_text = 'no media elements found';
		}
	};
	
	function sm2_player_play(i){
		$(".sm2_position").html(sm2_getHMSTime(0,true));
		$("#sm2_position").css({width:"0%"});
		if(soundManager.url != 'undefined'){
			if($.inArray('media_'+i,soundManager.soundIDs) != '-1'){
				console.log('on lit sans recréer');
			    soundManager.play('media_'+i);
			}else{
				sm2_player_stop();
				track_index = i;
				live_track = i;

				$(".playliste .sm2_play_on").removeClass("sm2_play_on");
				$(".playliste .sm2_play:eq("+i+")").addClass("sm2_play_on");
				
				sm2_player_creer_media(i);
				/**
				 * 
					var taille = file1.length;
					$("#now_playing").css("width", taille*6) ;
					$("#scroller").css("width", taille*6) ;
					$("#now_playing").html(file1) ;
					var taille =  $("#scroller").width();
			  		var min_taille = $("#scroller_container").width();
				*/
			   // adapter le defilement a la taille du texte
		      /** $.extend({scroller: {
					interval:     0,
					refresh:      300,  // Refresh Time in ms
					direction:    "left", // down,right,left,up
					speed:        2,
					id:           "#scroller",
					cont_id:      "#scroller_container",
					height:       30,
					width:        taille,
					min_height:   15,
					min_width:    min_taille
				}});
		       $("#scroller").css("left", min_taille-taille) ;
				*/
				if($.inArray('media_'+i,soundManager.soundIDs)!= '-1'){
				    soundManager.play('media_'+i,{volume:100});
				}
			}
		}
	}
	
	function sm2_player_creer_media(i){
		var video = liens[i].match(isvideo) ? true : false;
		if(soundManager.canPlayURL(liens[i])){
			if(video){
				soundManager.createVideo({
					id:'media_'+i,
					url:liens[i],
					onplay:sm2_events.play,
					onstop:sm2_events.stop,
					onpause:sm2_events.pause,
					onresume:sm2_events.resume,
					onfinish:sm2_events.finish,
					onbufferchange:sm2_events.bufferchange,
					whileloading:sm2_events.whileloading,
					whileplaying:sm2_events.whileplaying,
					'volume': 100    	
				});
				typePlaying = 'video';
			}else{
				soundManager.createSound({
					id:'media_'+i,
					url:liens[i],
					onplay:sm2_events.play,
					onstop:sm2_events.stop,
					onpause:sm2_events.pause,
					onresume:sm2_events.resume,
					onfinish:sm2_events.finish,
					onbufferchange:sm2_events.bufferchange,
					whileloading:sm2_events.whileloading,
					whileplaying:sm2_events.whileplaying,
					'volume': 100    	
				});
				typePlaying = 'sound';
			};
		}
	}
	
	function sm2_player_stop(){
		sm2_update_button();
		$("span.play_on").removeClass("play_on");
		live_track = false;
		
		$(".playliste li.play_on").removeClass("play_on");
		if($.inArray("media_"+track_index,soundManager.soundIDs)>=0){
			soundManager.stopAll();
			if(typePlaying = 'sound'){
				soundManager.destroySound("media_" + track_index);
			}else{
				soundManager.destroyVideo("media_" + track_index);
			}
		}
		$("#now_playing").html('');
	}
	
	/**
	 * 
	 */
	function sm2_player_togglePause(){
		soundManager.togglePause('media_'+live_track);
	}
	
	/**
	 * Affichage du bouton de lecture ou du bouton pause
	 */
	function sm2_update_button(){
		$("#sm2_player_play").css("display", (isPlaying)?"none":"block");
		$("#sm2_player_pause").css("display", (isPlaying)?"block":"none");
	}

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
	 * Rendre les titres des documents un peu plus joli
	 * - On supprime leurs extensions
	 * - On remplace les espaces d'Urls par de vrais espaces
	 * - On remplace les underscores et le tiret par un espace
	 * - On coupe à 90 caractères maximum
	 */
	function sm2_joli_titre(titre){
		titre = titre.replace(/(%20)/g,' ');
		titre = titre.substr(0,90);
		titre = titre.replace(/(\.mp3|\.flv|\.mov|\.mp4|\.m4v|\.f4v|\.m4a|\.mp4v|\.3gp|\.3g2)/g,' ');
		titre = titre.replace(/(_|-)/g,' ');
		return titre;
	}
	
	/**
	 * En sortie de page, on décharge soundManager de la mémoire
	 */
	jQuery(document).unload(function(){
		soundManager.unload();
	})
})($);