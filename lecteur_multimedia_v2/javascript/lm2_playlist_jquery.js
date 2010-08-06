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
	var live_track = null;
	var isPlaying = false;
	var isPaused = false;
	var typePlaying = false;
	var isvideo;
	var issound = /\.(mp3|aac)(\?.*)?$/i;
	var options = {};
	var sm2_events = {};
	var isMuted = false;

	$.fn.playlist = function(options) {
		var defaults = {
			sources : $('a[type=application/mp4],a[type=audio/mpeg],a[type=video/x-flv],object[type=video/x-flv]').not('.inline-exclude'),
			smUrl : "soundmanager/swf/",
			smnullUrl : "soundmanager/null.mp3",
			logo : false,
			logo_top: '50%',
			logo_left: '50%',
			logo_repeat: 'no-repeat',
			playNext: false, // stop after one sound, or play through list until end
			autoLoad: false,
			autoPlay: false,
			isMuted: false,
			movie: true,
			movieSize : 'adapt',
			bgcolor: '#000000',
			fullscreen: true,
			wmode: 'transparent',
			volume: 100,
			scrollPositionInfo:true,
			debug: false,
			sm2_container : $('#sm2_container')
		};

		sm2_events = {
			play:function(){
				isPlaying = true;
				isPaused = false;
				if(this.readyState == 3){
					$("#sm2_loading").css({width:"100%"});
				}
				$("#sm2_loading").css("cursor","hand");
				$(".sm2_duration").html(sm2_getHMSTime(this.durationEstimate,true));
				$('#sm2-container').unbind().click(function(e){
					e.preventDefault();
					sm2_player_togglePause();
				});
				if($('#sm2_cover:visible')){
					$('#sm2_cover').removeClass().fadeOut();
				}
				sm2_update_button();
			},
			finish:function(){
				if(options.playNext){
					sm2_player_play(live_track+1);
				}
				isPlaying = false;
				isPaused = false;
				sm2_update_button();
			},
			id3:function(){

			},
			metadata:function(){
				if(this.width && this.height){
					if(options.movieSize == 'adapt'){
						var width_container = $('#sm2-container').width();
						var ratio = (width_container/this.width);
						var height_final = (this.height*ratio).toFixed();
						$('#sm2-container').css('height',height_final+'px');
					}
				}
			},
			buffering:function(){
				options.sm2_container.addClass('buffering');
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
				if(this.readyState == 3){
					$("#sm2_loading").css({width:"100%"});
				}
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
			if($source.is('a')){
				var sURL = $source.attr('href');
				if($.inArray(sURL,liens)<0) {
					playliste_text += '<li class="sm2_play"><a href='+sURL+'>'+sm2_joli_titre($source.html())+'</a></li>';
					liens.push(sURL);
					nb++;
				}
			}else if ($source.is('object')){
				var sURL = $source.find('param[name=src]').attr('value');
				if($.inArray(sURL,liens)<0) {
					playliste_text += '<li class="sm2_play"><a href='+sURL+'>'+sm2_joli_titre(sURL)+'</a></li>';
					liens.push(sURL);
					nb++;
				}
			}
		});
		if(nb>0){
			/**
			 *
			 * Création de la playliste
			 *
			 */
			var lecteur = '<div id="sm2_player">'
				+ '<div id="sm2-container"><div id="sm2_cover">&nbsp;</div></div>'
				+ '<div id="sm2_controllers">'
				+ '<div id="sm2_player_play" class="button play">&nbsp;</div>'
				+ '<div id="sm2_player_pause" class="button pause">&nbsp;</div>';
			if(nb>1){
				lecteur += '<div id="sm2_player_prev" class="button prev">&nbsp;</div>'
				+ '<div id="sm2_player_next" class="button next">&nbsp;</div>';
			}
			lecteur += '<div id="sm2_player_stop" class="button stop">&nbsp;</div>'
				+ '<div id="sm2_player_mute" class="button mute">&nbsp;</div>'
				+ '<div id="sm2_player_volume" class="button volume">&nbsp;</div>'
				+ '</div>'
				+ '<div class=""><a class="sm2_vol_plus"></a><span class="sm2_vol_valeur"></span><a class="sm2_vol_moins"></a></div>'
				+ '<div class="sm2_timer"><span class="sm2_position"></span> / <span class="sm2_duration"></span></div>'
				+ '<div id="sm2_scrollbar"><div id="sm2_loading"></div><div id="sm2_position"></div></div>'
				+ '</div>';
			$this.html(lecteur);
			if(nb>1){
				playliste_text = $('<ul class="playliste"></ul>').append(playliste_text);
				$('#sm2_player').append(playliste_text);
			}

			if(options.logo){
				$('#sm2-container').css({'background-image' : 'url('+options.logo+')', 'background-position' : options.logo_top+' '+options.logo_left, 'background-repeat': options.logo_repeat});
			}

			$this.find('li.sm2_play a').click(function(e){
				e.preventDefault();
				if($(this).parent().hasClass('sm2_play_on')){
					sm2_player_togglePause();
				}else{
					sm2_player_play($.inArray($(this).attr('href'),liens));
				}
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
				if(isPlaying|isPaused){
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
				if(live_track|live_track == 0){
					var son = soundManager.getSoundById('media_'+live_track);
					var duree = son.durationEstimate;
					var offset = jQuery("#sm2_loading").offset();
					var x = Math.round((e.pageX - offset.left) / jQuery("#sm2_scrollbar").width() * 100);
					var temps = Math.round(duree * x / 100);
					$("#sm2_position").css({width:Math.round(x) +"%"});
					if(son.playState == 0){
						soundManager.play('media_'+live_track);
						son.setPosition(temps);
						soundManager.pause('media_'+live_track);
						$(".sm2_position").html(sm2_getHMSTime(son.position,true));
						sm2_update_button();
					}else{
						son.setPosition(temps);
					}
				}
			});

			if(options.scrollPositionInfo){
				$('#sm2_scrollbar').prepend('<div class="sm2_jump_position"></div>');
				$('#sm2_scrollbar').hover(function(e){
					if(live_track|live_track == 0){
						var son = soundManager.getSoundById('media_'+live_track);
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
						if(live_track|live_track == 0){$('.sm2_jump_position').fadeOut();}
				});
			}
			$('#sm2_player_volume,#sm2_player_mute').click(function(){
				sm2_player_toggleVolume();
			});

			if(options.autoPlay){
				soundManager.onready(function(){
					sm2_player_play(0);
				});
			}else if(options.autoLoad){
				soundManager.onready(function(){
					sm2_player_play(0);
					sm2_player_togglePause();
				});
			}
		}else{
			playliste_text = 'no media elements found';
		}
	};

	function sm2_player_play(i){
		$(".sm2_position").html(sm2_getHMSTime(0,true));
		$("#sm2_position").css({width:"0%"});
		if(soundManager.url != 'undefined'){
			if($.inArray('media_'+i,soundManager.soundIDs) != '-1'){
				if(i != live_track){
					sm2_player_stop();
				}
				soundManager.setPosition('media_'+i,0);
				soundManager.play('media_'+i);
			}else{
				sm2_player_stop();

				sm2_player_creer_media(i);

				if($.inArray('media_'+i,soundManager.soundIDs)!= '-1'){
				    soundManager.play('media_'+i,{volume:options.volume});
				}
			}
			live_track = i;
			$(".playliste .sm2_play_on").removeClass("sm2_play_on");
			$(".playliste .sm2_play:eq("+i+")").addClass("sm2_play_on");
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
					onmetadata:sm2_events.metadata,
					ondataerror:sm2_events.dataerror,
					whileloading:sm2_events.whileloading,
					whileplaying:sm2_events.whileplaying,
					'volume': options.volume
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
					ondataerror:sm2_events.dataerror,
					onbufferchange:sm2_events.bufferchange,
					whileloading:sm2_events.whileloading,
					whileplaying:sm2_events.whileplaying,
					'volume': options.volume
				});
				typePlaying = 'sound';
			};
		}
	}

	function sm2_player_stop(){
		sm2_update_button();
		$("span.play_on").removeClass("play_on");
		$(".sm2_duration,.sm2_position").html(sm2_getHMSTime(0,true));
		$("#sm2_position,#sm2_loading").css('width','0px');
		$(".playliste li.play_on").removeClass("play_on");
		if($.inArray("media_"+live_track,soundManager.soundIDs)>=0){
			soundManager.stopAll();
			// On ne supprime que si le fichier n'est pas complêtement téléchargé
			// Pour éviter d'avoir plusieurs téléchargements en même temps
			if(soundManager.getSoundById("media_" + live_track).readyState != 3){
				if(typePlaying = 'sound'){
					soundManager.destroySound("media_" + live_track);
				}else{
					soundManager.destroyVideo("media_" + live_track);
				}
			}
		}
		live_track = null;
	}

	/**
	 * Pause / resume
	 */
	function sm2_player_togglePause(){
		soundManager.togglePause('media_'+live_track);
		if(isPaused){
			$('#sm2_cover').css('opacity','.5').addClass('sm2_cover_paused').fadeIn('normal');
		}else{
			$('#sm2_cover').removeClass('sm2_cover_paused').fadeOut();
		}
	}

	/**
	 * Affichage du bouton de lecture ou du bouton pause
	 */
	function sm2_update_button(){
		$("#sm2_player_play").css("display", (isPlaying)?"none":"block");
		$("#sm2_player_pause").css("display", (isPlaying)?"block":"none");
		$("#sm2_player_volume").css("display", (isMuted)?"none":"block");
		$("#sm2_player_mute").css("display", (isMuted)?"block":"none");
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
		titre = titre.replace(/(\.mp3|\.flv|\.mov|\.mp4|\.m4v|\.f4v|\.m4a|\.mp4v|\.3gp|\.3g2)/gi,' ');
		titre = titre.replace(/(_|-)/g,' ');
		return titre;
	}

	function sm2_player_toggleVolume(){
		if(isMuted){
			isMuted = false;
			soundManager.unmute();
		}else{
			isMuted = true;
			soundManager.mute();
		}
		sm2_update_button();
	}
	/**
	 * En sortie de page, on décharge soundManager de la mémoire
	 */
	jQuery(document).unload(function(){
		soundManager.unload();
	})
})($);