/**
 * @author kent1
 */

(function($){
	var liens = [];
	var track_index;
	var live_track;
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
				$("#loading").css("cursor","hand");
				$(".duration").html(sm2_getHMSTime(this.durationEstimate,true));
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
			load:function(){
			},              
			whileloading:function(){
				var timer = this.bytesLoaded / this.bytesTotal * 100 ;
				$(".duration").html(sm2_getHMSTime(this.durationEstimate,true));
				$("#loading").css({width:Math.round(timer) +"%"});
			},
			whileplaying:function(){
				var timer2 = this.position / this.durationEstimate * 100 ;
				$("#position").css({width:Math.round(timer2) +"%"});
				$(".position").html(sm2_getHMSTime(this.position,true));
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
				playliste_text += '<li class="sm2_play"><a href='+$source.attr('href')+'>'+$source.attr('href')+'</a></li>';
				liens.push($source.attr('href'));
				nb++;
			}
		});
		if(nb>0){
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
				+ '<div><span class="position"></span> / <span class="duration"></span></div>'
				+ '</div>';
			playliste_text = $('<ul class="playliste"></ul>').append(playliste_text);
			$this.html(playliste_text);
			$('ul.playliste').wrap(lecteur);
			$this.find('li.sm2_play a').click(function(e){
				e.preventDefault();
				sm2_player_play($.inArray($(this).attr('href'),liens));
			});
			$('#sm2_player_play').click(function(e){
				e.preventDefault();
				if(!isPlaying && !isPaused){
					sm2_player_play(0);
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
			sm2_update_button();
			return;
		}else{
			playliste_text = 'no media elements found';
		}
	};
	
	function sm2_player_play(i){
		sm2_player_stop();
		console.log('on joue le fichier '+i);
		track_index = i;
		live_track = i;

		//$("span.play_:eq("+i+")").html("stop").addClass("play_on");
		//$("span.play_:eq("+i+")").html("<img src='" + image_pause + "'/>").addClass("play_on");	
		// i c pas forcemment bon si t'as un player avant le lien, il faut retrancher le nb d'item de la playlist du lecteur 
		// (ne pas mettre enclosure aux deux ?)	
		// limiter une playliste a son parent plutot qu'a la page ?
		
		$(".play_:eq("+i+")").addClass("play_on");

		if(soundManager.url != 'undefined'){
			
			sm2_player_creer_media(i);
			/**
			 * 
				 file1 = mp3Titles[i];
				file1 = file1.replace(/(%20)/g,' ');
				file1 = file1.substr(0,90);
				file1 = file1.replace(/(.mp3)/g,' ');
				file1 = file1.replace(/(_|-)/g,' ');

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
			if($.inArray(liens[i],soundManager.soundIDs)){
			    soundManager.play('media_'+i,{volume:100}) ;
			    isPlaying = true ;
			    sm2_update_button();
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
		isPlaying = false;
		sm2_update_button();
		$("span.play_on").removeClass("play_on");
		live_track = false;
		
		$(".playliste li.play_on").removeClass("play_on");
		if($.inArray("media_"+track_index,soundManager.soundIDs)>=0){
			if(typePlaying = 'sound'){
				soundManager.destroySound("media_" + track_index);
			}else{
				soundManager.destroyVideo("media_" + track_index);
			}
			soundManager.stopAll();
		}
		$("#now_playing").html('');
	}
	
	function sm2_player_togglePause(){
		soundManager.togglePause('media_'+live_track);
		if(!isPaused){
			isPaused = true;
			isPlaying = false;
		}else{
			isPaused = false;
			isPlaying = true;
		}
		sm2_update_button();
	}
	
	function sm2_update_button(){
		$("#sm2_player_play").css("display", (isPlaying)?"none":"block");
		$("#sm2_player_pause").css("display", (isPlaying)?"block":"none");
	}

	function sm2_getHMSTime(nbMSec,bAsString){
		// convert milliseconds to mm:ss, return as object literal or string
		var nbSec = Math.floor(nbMSec/1000);
		var min = Math.floor(nbSec/60);
		var sec = nbSec-(min*60);
		// if (min == 0 && sec == 0) return null; // return 0:00 as null
		return (bAsString?(min+':'+(sec<10?'0'+sec:sec)):{'min':min,'sec':sec});
	}

	jQuery(document).unload(function(){
		soundManager.unload();
	})
})($);