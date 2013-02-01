/**
 * 
 * MediaSPIP player
 * 
 * Player html5 pour les balises <audio> et <video>
 * avec fallback vers version flash pour flv/mp4/mp3/aac
 * 
 * Version : 0.8.0
 * © GNU/GPL v3 - kent1 (kent1@arscenic.info)
 * cf : http://www.mediaspip.net/technical-documentation/plugins-used-by-mediaspip/html5-player-video-sound-media/
 * 
 * Remplace les controles des lecteurs html5 par défaut des navigateurs
 * Remplace la balise <video> par un lecteur flash si on ne peut jouer le media :
 * - sur Firefox si seulement la version mp4 ou flv est disponible
 * - sur Safari si seulement la version ogv ou ogg est disponible
 * - sur IE dans tous les cas
 * 
 * Options : 
 * - autoplay boolean true/false : lit automatiquement la video ou le son (défaut : false)
 * - autoload boolean true/false : précharge automatiquement la video ou le son (défaut : true)
 * - minwidth int 
 * - movieSize string 'adapt' uniquement pour l'instant
 * - cookie_volume boolean true/false : met dans un cookie mediaspip_volume le niveau de volume 
 *   et dans html_volume_muted la valeur 'muted' si on a désactivé le son
 * - messages boolean : affiche ou pas des messages à même le lecteur : 
 *   play / pause / autres changements d'état... (defaut : true)
 * - volume_slider_orientation vertical|horizontal : permet de définir l'orientation du slider de volume (défaut vertical)
 */

(function($) {
	var media_hover = false;
	var slider = false;
	var stop_message_timeout = false;
	
	/**
	 * Vérifier si on a accès à l'API fullscreen de html5
	 */ 
	var fullScreenApi = {
			supportsFullScreen: false,
			isFullScreen: function() { return false; },
			requestFullScreen: function() {},
			cancelFullScreen: function() {},
			fullScreenEventName: '',
			prefix: ''
	},
	browserPrefixes = 'webkit moz o ms khtml'.split(' ');

	if (typeof document.cancelFullScreen != 'undefined') {
		// check for native support
		fullScreenApi.supportsFullScreen = true;
	} else {
		// check for fullscreen support by vendor prefix
		for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {
			fullScreenApi.prefix = browserPrefixes[i];
			if (typeof document[fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
				fullScreenApi.supportsFullScreen = true;
				break;
			}
		}
	}
	
	/**
	 * Fonction d'initialisation du lecteur sur une balise <audio> ou <video>
	 *
	 * Exemple d'appel :
	 * $('video').mediaspip_player_init();
	 */
	$.extend($.fn, {
			mediaspip_player_init : function(options){
			var defaults = {
					autoplay:false,
					autoload:true,
					minwidth : 300,
					movieSize : null,
					cookie_volume: false,
					messages: true,
					volume_slider_orientation: 'vertical'
				};
			
			if(isiPhone() || isiPad()){
				return;
			}
			options = $.extend(defaults, options);
	
			var id = $(this)[0];
			var media = $(this);
			var class_wrapper = '';
			if((typeof($.ui) == 'object') && (typeof($.ui.slider) == 'function')){
				id.slider = true;
				slider = true;
			}
			id.slider_control = false;
			id.slider_volume = false;
			if(typeof(media.attr('loop')) == 'string'){
				class_wrapper += 'loop';
				id.islooping = true;
			}
			else
				id.islooping = false;
			id.messages = options.messages;
			id.options = options;
			
			if(id.controls)
				id.addcontrols = true;
			id.controls = false;
			if($(this).is(':hidden')){
				$(this).show();
			}
			var playable = false;
			var wrapper;
	
			if($(this).is('audio')){
				options.movieSize = null;
			}
			/**
			 * Test si le navigateur dispose du support des balises <video> ou <audio>
			 */
			if(typeof(id.canPlayType) != "undefined"){
				$(this).children('source').each(function(){
					var type = $(this).attr('type');
					if((type != 'video/x-flv') && (id.canPlayType(type) != '')){
						playable = true;
					};
				});
				/**
				 * Si le navigateur ne peut pas :
				 * - Utiliser correctement les balises <audio> et <video>
				 * - Jouer une des sources disponibles
				 *
				 * On essaie de faire un fallback en flash (mp3/flv/mp4/aac)
				 */
				if(!playable && (typeof($.fn.mediaspip_test_fallback) == 'function')){
					media.mediaspip_test_fallback(options);
				}else{
					id.mediacanplay = false;
					id.isFullScreen = false;
					var styles = ' style="';
					var style = false;
					if(options.width){
						style = true;
						styles += 'width:'+options.width+'px;';
					}
					if(options.height){
						style = true;
						styles += 'height:'+options.height+'px;';
					}
					styles += '"';
					media.parent().wrapInner('<div class="media_wrapper loading '+class_wrapper+'"'+(style ? styles : '') +'></div>');
					wrapper = media.parent();
					
					var bloc_messages = '';
					if(options.messages){
						bloc_messages = '<div class="messages" style="display:none"></div>';
					}
					wrapper.append(bloc_messages);
					if(id.addcontrols){
						var controls = '<div class="controls small">'
							+'<div class="buttons_left">'
								+'<span class="play_pause_button" title="'+mediaspip_player_lang.bouton_loading+'"></span>'
							+'</div>'
							+'<div class="progress_bar">'
								+'<em class="elapsed_time" title="'+mediaspip_player_lang.info_ecoule+'"></em>'
								+'<div class="progress_back">'
									+'<div class="progress_loading_wrapper">'
										+'<div class="progress_loading_stripes"></div>'
									+'</div>'
									+'<div class="progress_buffered"></div>'
									+'<div class="progress_elapsed_time"></div>'
									+'<span class="progress_indicator"></span>'
								+'</div>'
								+'<em class="remaining_time remaining" title="'+mediaspip_player_lang.info_restant+'"></em>'
							+'</div>'
							+'<div class="buttons_right">'
								+'<span class="volume_button" title="'+mediaspip_player_lang.bouton_volume+' ('+Math.floor(id.volume*100)+'%)"></span>';
		
							/**
							 * Si on a les sliders, on ajoute une div ici pour avoir un slider de volume
							 */
							if(slider){
								controls += '<span class="volume_slider_container '+options.volume_slider_orientation+'"><span class="volume_slider"></span></span>';
							}
							if(media.is('video')){
								controls += '<span class="fullwindow_button" title="'+mediaspip_player_lang.bouton_fullscreen+'"></span>';
							}
							controls += '<span class="loop_button" title="'+mediaspip_player_lang.bouton_loop+'"></span>';
							controls +='</div>';
							+'</div>';
		
						wrapper.append(controls);
						var control = wrapper.find('.controls');
					}else{
						var control = null;
					}
					if($(this).width() > options.minwidth){
						wrapper.width($(this).width());
					}else{
						wrapper.width(options.minwidth);
					}
	
					var elapsed_time = control.find('.elapsed_time');
					var remaining_time = control.find('.remaining_time');
					var progress_indicator = control.find('.progress_indicator');
					var progress_elapse = control.find('.progress_elapsed_time');
					
					if(media.prev().is('img')){
						if(media.prev().width() > options.minwidth){
							wrapper.width(media.prev().width());
							var img_height = media.prev().height();
							wrapper.height(img_height);
						}else{
							var img_width = media.prev().width();
							var img_height = media.prev().height();
							var ratio = options.minwidth / img_width;
							wrapper.width(options.minwidth);
							wrapper.height(img_height*ratio);
							media.prev().width(options.minwidth);
							media.prev().height(img_height*ratio);
	
						}
						media.prev().wrap('<div class="html5_logo"></div>');
						media.prev().click(function(){
							media.mediaspip_play_pause(options);
						});
					}
	
					media.mediaspip_resize_controls();
	
					id.addEventListener("canplay", function(e){
						media.mediaspip_start(options,'canplay');
					}, true);
					
					id.addEventListener("loadedmetadata",function(e){
						media.mediaspip_start(options,'loadedmetadata');
						/**
						 * Cas d'un stream audio (Radio)
						 */
						if(id.duration == 'Infinity'){
							id.isstream = true;
							control.find('.progress_back').detach();
							remaining_time.addClass('total_time').attr('title','').html(mediaspip_player_lang.info_streaming);
						}
					},true);
					
					id.addEventListener("error", function(e){
						wrapper.removeClass('loading').addClass('error');
						wrapper.find('.play_pause_button').attr('title',mediaspip_player_lang.info_erreur);
					}, true);
	
					id.addEventListener("timeupdate", function(e){
						var percent_time = mediaspip_anything_to_percent(id.currentTime,id.duration);
						if(remaining_time.is('.remaining') && (id.duration != 'Infinity')){
							remaining_time.text('-'+mediaspip_second_to_time(id.duration - id.currentTime));
						}
						elapsed_time.text(mediaspip_second_to_time(id.currentTime));
						if(slider && (typeof(id.slider_control) == 'object')){
							progress_elapse.css('width',percent_time+'%');
							id.slider_control.slider("value", percent_time);
						}else{
							progress_indicator.css('left',percent_time+'%');
						}
						media.mediaspip_resize_controls();
					}, true);
	
					id.addEventListener("seeking",function(e){
						wrapper.addClass('seeking');
						wrapper.find('.play_pause_button').attr('title',mediaspip_player_lang.bouton_seeking);
					},true);
					
					id.addEventListener("seeked",function(e){
						wrapper.removeClass('seeking');
						if(id.paused)
							wrapper.find('.play_pause_button').removeClass('pause').attr('title',mediaspip_player_lang.bouton_pause);
						else
							wrapper.find('.play_pause_button').addClass('pause').attr('title',mediaspip_player_lang.bouton_lire);
					},true);
	
					id.addEventListener("progress", function(e){
						var percent_loaded = null;
						if((typeof(e.loaded) != 'undefined') && (typeof(e.total) != 'undefined')){
							percent_loaded = mediaspip_anything_to_percent(e.loaded,e.total);
						}else if((typeof(id.buffered) != 'undefined') && (id.buffered.length > 0)){
							percent_loaded = mediaspip_anything_to_percent(id.buffered.end(0),id.duration);
						}
						if(percent_loaded != null)
							control.find('.progress_buffered').css('width',percent_loaded+'%');
					}, true);
	
					id.addEventListener("ended", function(e){
						if(!id.islooping){
							wrapper.addClass('paused');
							id.paused = true;
							wrapper.find('.play_pause_button').removeClass('pause').attr('title',mediaspip_player_lang.bouton_lire);
						}else{
							id.currentTime = 0;
						    id.play();
						}
					}, true);
	
					id.addEventListener("volumechange", function(e){
						media.mediaspip_volume(false,options);
					}, true);
	
					media.click(function(){
						media.mediaspip_play_pause(options);
					});
					
					media.dblclick(function(e){
						e.preventDefault();
						media.mediaspip_fullscreen(control);
					});
					
					var stop_timeout = false;
					var last_moved=0;
					var clientx = 0;
					var clienty = 0;
					wrapper.mousemove(function(e){
						var now = e.timeStamp;
						media_hover = $(this).find('audio,video');
						if(id.isFullScreen && !id.paused){
							if ((now - last_moved > 1000) && ((e.clientX != clientx) || (e.clientY != clienty))) {
								wrapper.addClass('hover');
								clearTimeout(stop_timeout);
								var full_onmousestop = function() {
									wrapper.removeClass('hover');
								};
								stop_timeout = setTimeout(full_onmousestop, 1500);
								last_moved = now;
								clientx = e.clientX;
								clienty = e.clientY;
							}
						}
					});
	
					/**
					 * Les actions sur les éléments des controles :
					 *
					 * - Le switch Play/Pause sur le click du bouton adéquat
					 * - Seek lors d'un click sur la barre de progression
					 * - Le switch Mute/Unmute sur le click du bouton de volume
					 * - Le switch fullscreen/normal screen sur le bouton adéquat
					 */
					control.find('.play_pause_button').click(function(){
						media.mediaspip_play_pause(options);
					});
	
					control.find('.progress_back,.progress_elapsed_time,.progress_buffered').click(function(e){
						media.mediaspip_seek_to(e.clientX,slider,options);
					});
	
					control.find('.volume_button').click(function(e){
						media.mediaspip_volume(true,options);
					});
	
					control.find('.fullwindow_button').click(function(e){
						media.mediaspip_fullscreen(control);
					});
					
					control.find('.loop_button').click(function(e){
						media.mediaspip_loop(options);
					});
					
					remaining_time.click(function(e){
						if(!id.isstream){
							if($(this).is('.remaining')){
								$(this)
									.removeClass('remaining')
									.addClass('total_time')
									.attr('title',mediaspip_player_lang.info_total)
									.html(mediaspip_second_to_time(id.duration));
							}else{
								$(this)
									.removeClass('total_time')
									.addClass('remaining')
									.attr('title',mediaspip_player_lang.info_restant)
									.html('-'+mediaspip_second_to_time(id.duration - id.currentTime));
							}
							media.mediaspip_resize_controls();
						}
					});
	
					/**
					 * Fonction modifiant le niveau de volume au scroll sur le lecteur
					 * Cette action fonctionne au scroll au hover du lecteur.
					 * Nécessite le plugin jQuery mousewheel :
					 * http://github.com/brandonaaron/jquery-mousewheel
					 *
					 * - bloque l'évènement normal du scroll (descendre dans la page);
					 * - baisse le volume de 10% lors d'un scroll bas avec la souris;
					 * - augmente le volume de 10% lors d'un scroll haut avec la souris;
					 * - si le volume est "muted", l'action ne fait rien sur le volume;
					 */
					if(typeof($.fn.mousewheel != "undefined")){
						wrapper.mousewheel(function(event, delta) {
							event.preventDefault();
							if(!id.muted){
								var volume = id.volume;
								var volume_diff = (delta > 0) ? '0.1' : '-0.1';
								var volume_new = Math.round((volume + parseFloat(volume_diff))*10)/10;
								if((volume_new <= 1) && (volume_new >= 0)){
									id.volume = volume_new;
								}
							}
						});
					}
					if((id.readyState == "4") || (id.readyState == "3")){
						media.mediaspip_start(options,'canplay');
					}
				}
			}else{
				media.mediaspip_test_fallback(options);
			}
		},
		mediaspip_start : function(options,action){
			var media = this;
			var id = this.get(0);
			var wrapper = media.parent();
			var control = wrapper.find('.controls');
			var elapsed_time = control.find('.elapsed_time');
			var remaining_time = control.find('.remaining_time');
			var progress_indicator = control.find('.progress_indicator');
			var progress_elapse = control.find('.progress_elapsed_time');
			
			if(!id.mediacanplay){
				if(wrapper.hasClass('error')){
					wrapper.removeClass('error').addClass('loading');
				}
				id.mediacanplay = true;
				var width_container = media.width();
				var parent_width = wrapper.parent().width();
				
				if(id.videoHeight && id.videoWidth){
					if(options.movieSize == 'adapt' && !id.isFullScreen && !media.hasClass('noresize')){
						width_container = parent_width;
						var ratio = (width_container/id.videoWidth);
						var height_final = (id.videoHeight*ratio).toFixed();
						wrapper.add(media).animate({height:height_final+'px',width:width_container+'px'},500,function(){
							media.mediaspip_resize_controls();
						});
					}
				}
				if(wrapper.hasClass('loading')){
					wrapper.removeClass('loading').addClass('paused');
					wrapper.find('.play_pause_button').attr('title',mediaspip_player_lang.bouton_lire);
				}
				
				if(remaining_time.is('.remaining') && (id.duration != 'Infinity')){
					remaining_time.text('-'+mediaspip_second_to_time(id.duration));
				}
				elapsed_time.text(mediaspip_second_to_time(id.currentTime));
				media.mediaspip_resize_controls();
				if((typeof(id.buffered) != 'undefined') && (typeof(id.buffered.end(0)) == 'number')){
					var percent_load = mediaspip_anything_to_percent(id.buffered.end(0),id.duration);
					control.find('.progress_buffered').css('width',percent_load+'%');
				}
				
				if(slider){
					progress_indicator.hide();
					var replay = false;
					id.slider_control = control.find('.progress_back').slider({
						min: 0,
						max: 100,
						range: "min",
						start: function(event,ui){
							if(!id.paused && !id.ended){
								id.pause();
								replay = true;
							}else{
								replay = false;
							}
						},
						slide: function(event,ui){
							/**
							 * - On change les valeurs de temps
							 * - On fait avancer la barre de lecture sans lancer la lecture
							 */
							progress_elapse.css('width',ui.value+'%');
							var time_passed = mediaspip_percent_to_time(ui.value,id.duration);
							var time_left = mediaspip_percent_to_time((100 - ui.value),id.duration);
							if(remaining_time.is('.remaining') && (id.duration != 'Infinity')){
								remaining_time.text('-'+time_left);
							}
							elapsed_time.text(time_passed);
						},
						stop: function(event,ui){
							/**
							 * On saute la lecture au bon endroit ?
							 */
							media.mediaspip_seek_to_percent(ui.value,true,options,false);
							if(replay){
								id.play();
							}
						}
					});
					id.slider_volume = control.find('.volume_slider').slider({
						value:Math.floor(id.volume*100),
						orientation: options.volume_slider_orientation,
						min:0,
						max:100,
						range: "min",
						slide: function(event,ui){
							/**
							 * On change le volume
							 */
							var volume_new = ui.value/100;
							if((volume_new <= 1) && (volume_new >= 0)){
								id.volume = volume_new;
							}
						},
						stop: function(event,ui){
							/**
							 * On change le volume et on le sauvegarde dans le cookie si nécessaire
							 */
							var volume_new = ui.value/100;
							if((volume_new <= 1) && (volume_new >= 0)){
								id.volume = volume_new;
							}
						}
					});
					media.mediaspip_resize_controls();
				}
				if(options.cookie_volume){
					var volume_cookie = parseFloat($.cookie('mediaspip_volume'));
					if((volume_cookie >= 0) && (volume_cookie <= 1)){
						id.volume = volume_cookie;
					}
					var volume_muted = $.cookie('mediaspip_volume_muted');
					if((volume_muted == 'muted') && !id.muted){
						media.mediaspip_volume(true,options);
					}
				}
			}
		},
		mediaspip_play_pause : function(options){
			var id = this;
			if($(id)[0].mediacanplay && !$(id)[0].seeking){
				if($(id)[0].paused){
					id.parent('.media_wrapper').removeClass('paused');
					id.mediaspip_messages('play',mediaspip_player_lang.statut_play,options);
					id.parent('.media_wrapper').find('.play_pause_button').addClass('pause').attr('title',mediaspip_player_lang.bouton_pause);
					if(options.cookie_volume){
						var volume_cookie = parseFloat($.cookie('mediaspip_volume'));
						if((volume_cookie >= 0) && (volume_cookie <= 1)){
							id[0].volume = volume_cookie;
						}
						if($.cookie('mediaspip_volume_muted') == 'muted'){
							id[0].muted = true;
						}
					}
					id[0].play();
				}else if ($(id)[0].ended){
					id.parent('.media_wrapper').removeClass('paused');
					id.parent('.media_wrapper').find('.play_pause_button').addClass('pause').attr('title',mediaspip_player_lang.bouton_pause);
					$(id)[0].currentTime = $(id)[0].startTime ? $(id)[0].startTime : '0';
					if(options.cookie_volume){
						var volume_cookie = parseFloat($.cookie('mediaspip_volume'));
						if((volume_cookie >= 0) && (volume_cookie <= 1)){
							id[0].volume = volume_cookie;
						}
						if($.cookie('mediaspip_volume_muted') == 'muted'){
							id[0].muted = true;
						}
					}
					id[0].play();
				}else{
					id.parent('.media_wrapper').addClass('paused');
					id.mediaspip_messages('pause',mediaspip_player_lang.statut_pause,options);
					id.parent('.media_wrapper').find('.play_pause_button').removeClass('pause').attr('title',mediaspip_player_lang.bouton_lire);
					id[0].pause();
				}
			}
		},
		/**
		 * Fonction de modification du volume
		 * 
		 * Paramètres :
		 * - mute boolean (true/false) : signifie si le media est muted ou pas;
		 * - slider boolean (true/false) : y a t il un slider;
		 * - options Object : les options du lecteur;
		 */
		mediaspip_volume : function(mute,options){
			if($(this)[0].mediacanplay){
				var id= $(this);
				var control = id.parent().find('.controls');
				if(mute){
					if(id[0].muted){
						id.mediaspip_messages('mute',mediaspip_player_lang.statut_unmute,options);
						var volume_title = mediaspip_player_lang.bouton_volume+' ('+Math.floor(id[0].volume*100)+'%)';
						control.find('.volume_button').removeClass('muted').attr('title',volume_title);
						id[0].muted = false;
						if(id[0].slider && (typeof(id[0].slider_volume) == 'object')){
							id[0].slider_volume.slider('value',(id[0].volume*100));
							id[0].slider_volume.slider('enable');
						}
						if(options.cookie_volume)
							$.cookie('mediaspip_volume_muted',null);
					}else{
						id.mediaspip_messages('mute',mediaspip_player_lang.statut_mute,options);
						control.find('.volume_button').addClass('muted').attr('title',mediaspip_player_lang.bouton_volume_muted);
						id[0].muted = true;
						if(id[0].slider && (typeof(id[0].slider_volume) == 'object'))
							id[0].slider_volume.slider('disable');
						if(options.cookie_volume)
							$.cookie('mediaspip_volume_muted','muted');
					}
				}else if(!id[0].muted){
					var volume = id[0].volume;
					var sound_button = control.find('.volume_button');
					var class_remove = sound_button.attr('class').match('volume_button_[0-9]{1,3}');
					if((volume <= 0.66) && (volume > 0.33)){
						if(class_remove != null)
							sound_button.removeClass(class_remove[0]);
						sound_button.addClass('volume_button_66');
					}else if((volume <= 1) && (volume > 0.66)){
						if(class_remove != null)
							sound_button.removeClass(class_remove[0]);
						sound_button.addClass('volume_button_100');
					}else if((volume <= 0.33) && (volume > 0)){
						if(class_remove != null)
							sound_button.removeClass(class_remove[0]);
						sound_button.addClass('volume_button_33');
					}else if(volume == 0){
						if(class_remove != null)
							sound_button.removeClass(class_remove[0]);
						sound_button.addClass('volume_button_0');
					}
					var volume_title = mediaspip_player_lang.bouton_volume+' ('+Math.floor(id[0].volume*100)+'%)';
					control.find('.volume_button').attr('title',volume_title);
					if(id[0].slider && (typeof(id[0].slider_volume) == 'object')){
						id[0].slider_volume.slider('value',(id[0].volume*100));
						id[0].slider_volume.slider('enable');
					}
					id.mediaspip_messages('seek_back',volume_title,options);
						
					if(options.cookie_volume)
						$.cookie('mediaspip_volume', id[0].volume);
				}
			}
		},
		/**
		 * Fonction de saut d'un point à un autre dans le média
		 * Utilisée lors d'un click sur la barre de progression
		 *
		 * Prends un arguments :
		 * - La position du curseur de la souris lors du click sur la barre de progression
		 */
		mediaspip_seek_to : function(cursor_position,slider,options){
			var id = $(this);
			var was_playing = false;
			if(!$(id)[0].paused){
				was_playing = true;
				$(id)[0].pause();
			}
			var duration = id[0].duration;
			var currenttime = id[0].currentTime;
			var control = id.parent().find('.controls');
			var progress = control.find('.progress_back');
			var offset = progress.offset();
			width = cursor_position-offset.left;
			var width_total = progress.width();
			var percent = Math.floor((width / width_total) * 100);
			if(!slider){
				control.find('.progress_elapsed_time').css('width',percent+'%');
				control.find('.progress_indicator').css('left',percent+'%');
			}
			var time = Math.floor((duration * percent) / 100);
			id[0].currentTime = time;
			
			var time_affiche = mediaspip_second_to_time(time);
			if(currenttime > time)
				id.mediaspip_messages('seek_back',mediaspip_player_lang.statut_seek_back+' '+time_affiche,options);
			else
				id.mediaspip_messages('seek_to',mediaspip_player_lang.statut_seek_to+' '+time_affiche,options);
			if(was_playing){
				$(id)[0].play();
			}
		},
		/**
		 * mediaspip_seek_to_percent
		 * 
		 * On seek à X% de la video ou du son
		 * 
		 * Paramètres : 
		 * - percent int : le pourcentage;
		 * - slider boolean true/false : le slider si présents;
		 * - options object : les options du lecteur;
		 * - update_slider boolean true/false : doit on mettre à jour le slider (pas utile quand on vient du slider justement);
		 */
		mediaspip_seek_to_percent : function(percent,slider,options,update_slider){
			var id = $(this);
			var duration = id[0].duration;
			var currenttime = id[0].currentTime;
			if(((currenttime == duration) && (percent == 100)) || ((currenttime == 0) && (percent == 0))){
				return false;
			}
			if(percent == 0)
				var time = 0;
			else
				var time = Math.ceil((duration * percent) / 100);
				
			id[0].currentTime = time;
			var time_affiche = mediaspip_second_to_time(time);
			if(currenttime > time)
				id.mediaspip_messages('seek_back',mediaspip_player_lang.statut_seek_back+' '+time_affiche,options);
			else
				id.mediaspip_messages('seek_to',mediaspip_player_lang.statut_seek_to+' '+time_affiche,options);
			if(!slider){
				var control = id.parent().find('.controls');
				control.find('.progress_elapsed_time').css('width',percent+'%');
				control.find('.progress_indicator').css('left',percent+'%');
			}else if(update_slider){
				id[0].slider_control.slider("value", percent);
			}
		},
		/**
		 * 
		 */
		mediaspip_loop : function(options){
			var media = $(this);
			var id = $(this)[0];
			var container = $(this).parent();
			if(!id.loop && !container.hasClass('loop')){
				media.attr('title',mediaspip_player_lang.bouton_loop_looped);
				container.addClass('loop');
				media.mediaspip_messages('loop',mediaspip_player_lang.statut_loop,options);
				if (typeof id.loop == 'boolean') {
					id.loop = true;
				}else {
					id.islooping = true;
				}
			}else{
				media.attr('title',mediaspip_player_lang.bouton_loop);
				container.removeClass('loop');
				$(this).mediaspip_messages('unloop',mediaspip_player_lang.statut_unloop,options);
				if (typeof id.loop == 'boolean') {
					id.loop = true;
				}else {
					id.islooping = false;
				}
				media.removeAttr('loop');
			}
		},
		mediaspip_fullscreen : function(control){
			var media = $(this);
			var id = media[0];
			var container = $(this).parent();
			var id_container = container.get(0);
			if(id.mediacanplay){
				if(!id.isFullScreen){
					if (fullScreenApi.supportsFullScreen) {
						fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';
				        (fullScreenApi.prefix === '') ? $(this)[0].requestFullScreen() : $(this)[0][fullScreenApi.prefix + 'RequestFullScreen']();
					}else{
						id.videoOrigWidth = media.width();
						id.videoOrigHeight = media.height();
						id_container.origWidth = container.width();
						id_container.origHeight = container.height();
						$('body').css({'overflow' : 'hidden', '-moz-user-select' : 'none'});
						media.mediaspip_fullscreen_resize();
						$(window).unbind('resize').resize(function(){
							media.mediaspip_fullscreen_resize();
							media.mediaspip_resize_controls();
						});
					}
					id.isFullScreen = true;
					container.find('.controls').removeClass('small');
					container.addClass('media_wrapper_full');
					container.find('span.fullwindow_button').attr('title',mediaspip_player_lang.bouton_fullscreen_full);
				}else{
					if (fullScreenApi.supportsFullScreen) {
						(fullScreenApi.prefix === '') ? document.cancelFullScreen() : document[fullScreenApi.prefix + 'CancelFullScreen']();
					}else{
						container.bind('fullscreen_resize',function(){
							$(this).removeClass('media_wrapper_full').animate({width:id_container.origWidth+'px',height:id_container.origHeight+'px',left:'0',top:'0'},500,function(){
								media.mediaspip_resize_controls();
							});
						});
						$(this).bind('fullscreen_resize',function(){
							$(this).animate({width:id.videoOrigWidth+'px',height:id.videoOrigHeight+'px',left:'0',top:'0'},500,function(){
								media.mediaspip_resize_controls();
							});
						});
						$(window).unbind('resize');
						$('body').css({'overflow' : 'inherit', '-moz-user-select' : 'inherit'});
						width_orig = id.videoOrigWidth;
						height_orig = id.videoOrigHeight;
						container.add($(this)).trigger('fullscreen_resize').unbind('fullscreen_resize');
					}
					container.removeClass('media_wrapper_full');
					container.find('.controls').addClass('small');
					container.find('span.fullwindow_button').attr('title',mediaspip_player_lang.bouton_fullscreen);
					media.mediaspip_resize_controls();
					id.isFullScreen = false;
				}
			}
		},
		mediaspip_fullscreen_resize : function(){
			var media = $(this);
			var id = media.get(0);
			var container = $(this).parent();
			var id_container = container.get(0);
			var window_width = window.innerWidth;
		    var window_height = window.innerHeight;
	
		    var ratio = (window_height/id.videoHeight);
			var width_final = (id.videoWidth*ratio).toFixed();
	
			container.css({width:'100%',height:'100%',left:'0',top:'0'});
	
			if(width_final > window_width){
				var ratio = (window_width/id.videoWidth);
				var height_final = (id.videoHeight*ratio).toFixed();
				var top = ((window_height-height_final)/2).toFixed();
				$(this).css({position:'absolute',width:window_width+'px',height:height_final+'px',top:top+'px',left:'0'});
			}else{
				var left = ((window_width-width_final)/2).toFixed();
				$(this).css({position:'auto',width:width_final+'px',height:window_height+'px',left:left+'px',top:'0'});
			}
		},
	
		/**
		 * Change dynamiquement la taille de la barre de progression et de son conteneur
		 * en fonction de la taille du lecteur.
		 *
		 * La largeur du conteneur de la barre de progession correspond à :
		 * - La largeur du lecteur
		 * - Moins la largeur des boutons de gauche (inclus leurs padding, leurs marges, et leurs bordure droite et gauche)
		 * - Moins la largeur des boutons de droite (inclus leurs padding, leurs marges, et leurs bordure droite et gauche)
		 * - Moins les marges, padding et margin css de la barre de progression elle-même
		 *
		 * La largeur de la barre de progression elle-même correspond à :
		 * - La largeur de son conteneur
		 * - Moins la largeur de l'indication de temps ecoulé (inclus ses margins, padding et bordures droite et gauche)
		 * - Moins la largeur de l'indication de temps restant (inclus ses margins, padding et bordures droite et gauche)
		 */
		mediaspip_resize_controls : function(){
			var media = $(this);
			var control = media.parent().find('.controls');
			var width = control.width();
			var play_width = control.find('.buttons_left').outerWidth()+parseFloat(control.find('.buttons_left').css('margin-left'))+parseFloat(control.find('.buttons_left').css('margin-right'));
			var sound_width = control.find('.buttons_right').outerWidth()+parseFloat(control.find('.buttons_right').css('margin-left'))+parseFloat(control.find('.buttons_right').css('margin-right'));
			var progresswidth = parseFloat(width)-parseFloat(play_width)-parseFloat(sound_width) - parseFloat(control.find('.progress_bar').css('border-left-width')) - parseFloat(control.find('.progress_bar').css('border-right-width'))-parseFloat(control.find('.progress_bar').css('margin-right')) - parseFloat(control.find('.progress_bar').css('margin-left')) - parseFloat(control.find('.progress_bar').css('padding-right')) - parseFloat(control.find('.progress_bar').css('padding-left'));
			control.find('.progress_bar').width(progresswidth);
	
			var remaining_width = control.find(".remaining_time").outerWidth()+parseFloat(control.find('.remaining_time').css('margin-left'))+parseFloat(control.find('.remaining_time').css('margin-right'));
			var elapsed_width = control.find(".elapsed_time").outerWidth()+parseFloat(control.find('.elapsed_time').css('margin-left'))+parseFloat(control.find('.elapsed_time').css('margin-right'));
			var progressback_width = progresswidth - elapsed_width - remaining_width - parseFloat(control.find('.progress_back').css('border-left-width')) - parseFloat(control.find('.progress_back').css('border-right-width'))-parseFloat(control.find('.progress_back').css('margin-right')) - parseFloat(control.find('.progress_back').css('margin-left')) - parseFloat(control.find('.progress_back').css('padding-right')) - parseFloat(control.find('.progress_back').css('padding-left'))-2;
			control.find('.progress_back').width(progressback_width);
		},
		/**
		 * Modifie le message dans la boite de message si présente
		 */
		mediaspip_messages : function(type,message,options){
			if(!options.messages)
				return;
			var messages = $(this).parent('.media_wrapper').find('.messages');
			var fade_play = function() {
				messages.fadeOut();
			};
			messages.html(message);
			if(messages.is(':hidden')){
				messages.fadeIn('normal',function(){
					stop_message_timeout = setTimeout(fade_play, 1500);
				});
			}else{
				clearTimeout(stop_message_timeout);
				stop_message_timeout = setTimeout(fade_play, 1500);
			}
		},
		/**
		 * La fonction de test de fallback en flash 
		 * 
		 * Si le navigateur ne sait pas lire le media en html5,
		 * on regarde si on a une fonction de fallback flas et dans ce cas on l'utilise.
		 */
		mediaspip_test_fallback : function(options){
			if(typeof($.fn.mediaspip_fallback_flash) == 'function'){
				var id = $(this);
				if(jQuery.browser.msie){
					var sources = id.parent().children("source");
				}else{
					var sources = id.children("source");
				}
				$.each(sources, function(index, value){
					if(($(this).attr('type') == 'video/x-flv')||($(this).attr('type') == 'video/mp4')||($(this).attr('type') == 'audio/mpeg')){
						id.parent('div').mediaspip_fallback_flash({
							flowurl:options.flowurl,
							flasherror:options.flasherror?options.flasherror:'',
							autoplay:options.autoplay,
							autoload:options.autoload,
							wmode : 'transparent',
							width : id.attr('width'),
							height : id.attr('height')?id.attr('height'):id.parent().height(),
							poster : id.attr('poster'),
							sources : $(this)
						});
						/**
						 * On s'arrête au premier élément qui nous convient
						 */
						return false;
					}
				});
			}
		}
	});
	
	/**
	 * Quelques évènements à la pression de touches sur le clavier
	 * -* esc : sort du fullscreen
	 * -* f : passe en fullscreen sur la video en lecture ou sur celle en hover/focus
	 * -* space : lance la lecture d'une vidéo ou la met en pause
	 */
	$(document).keydown(function(e) {
	    /**
	     * Touche esc
	     * Sort du mode fullscreen (uniquement sur videos)
	     */
	    if (e.keyCode == 27) {
	        $('video').each(function(){
	        	if($(this)[0].isFullScreen){
	        		$(this).mediaspip_fullscreen($(this).parent().find('.controls'));
	        		return;
	        	}
	        });
	    }
	    /**
	     * Touche enter
	     * Toggle le fullscreen
	     */
	   
	    /**
	     * Touche f
	     * Active le fullscreen sur la video en lecture ou la video en hover/focus
	     * (uniquement sur videos)
	     */
	    if (e.keyCode == 70) {
	        $('video').each(function(){
	        	if(!$(this)[0].isFullScreen && (($('input:focus,textarea:focus').size() == 0) && (!$(this)[0].paused && !$(this)[0].ended))){
	        		$(this).mediaspip_fullscreen($(this).parent().find('.controls'));
	        		return;
	        	}
	        });
	    }
	    /**
	     * Touche l
	     * Active ou désactive le mode boucle (loop) sur le média en cours de lecture
	     */
	    if (e.keyCode == 76) {
	        $('video,audio').each(function(){
	        	if((($('input:focus,textarea:focus').size() == 0) && (!$(this)[0].paused && !$(this)[0].ended))){
	        		$(this).mediaspip_loop($(this)[0].options);
	        		return;
	        	}
	        });
	    }
	    /**
	     * Touche Space
	     * Lance la lecture ou met le media en pause
	     */
	    if (e.keyCode == 32) {
	    	if(media_hover){
	    		if(media_hover.get(0).isFullScreen||($('input:focus,textarea:focus').size() == 0)){
	        		e.preventDefault();
	        		media_hover.mediaspip_play_pause(media_hover[0].options);
	        		return;
	        	}
	    	}
	        $('video,audio').each(function(){
	        	if($(this)[0].isFullScreen||(($('input:focus,textarea:focus').size() == 0) && (!$(this)[0].paused && !$(this)[0].ended))){
	        		e.preventDefault();
	        		media_hover = $(this);
	        		media_hover.mediaspip_play_pause(media_hover.get(0).options);
	        		return;
	        	}
	        });
	    }
	    /**
	     * Touches Up et Down
	     * Baisse ou augmente de 10% le volume de la video en cours de lecture
	     */
	    if (e.keyCode == 38 || e.keyCode == 40) {
	    	if(media_hover){
	    		if(media_hover[0].isFullScreen||($('input:focus,textarea:focus').size() == 0)){
	    			e.preventDefault();
	        		if(!media_hover[0].muted){
						var volume = media_hover[0].volume;
						if(e.keyCode == 38){
							delta  = 1;
						}else{
							delta = -1;
						}
						var volume_diff = (delta > 0) ? '0.1' : '-0.1';
						var volume_new = Math.round((volume + parseFloat(volume_diff))*10)/10;
						if((volume_new <= 1) && (volume_new >= 0)){
							media_hover[0].volume = volume_new;
						}
					}
	        		return;
	    		}
	    	}
	        $('video,audio').each(function(){
	        	if($(this)[0].isFullScreen||(($('input:focus,textarea:focus').size() == 0) && (!$(this)[0].paused && !$(this)[0].ended))){
	        		e.preventDefault();
	        		if(!$(this)[0].muted){
						var volume = $(this)[0].volume;
						if(e.keyCode == 38){
							delta  = 1;
						}else{
							delta = -1;
						}
						var volume_diff = (delta > 0) ? '0.1' : '-0.1';
						var volume_new = Math.round((volume + parseFloat(volume_diff))*10)/10;
						if((volume_new <= 1) && (volume_new >= 0)){
							$(this)[0].volume = volume_new;
						}
					}
	        		return;
	        	}
	        });
	    }
	    /**
	     * Gauche (37) et droite (39)
	     * Avance ou recule de 5% la video en cours de lecture
	     * Il faut également modifier la valeur de la barre
	     */
	    if (e.keyCode == 37 || e.keyCode == 39) {
	    	if(media_hover){
	    		if(media_hover[0].isFullScreen||($('input:focus,textarea:focus,select:focus').size() == 0)){
	        		e.preventDefault();
	    			var duration = media_hover[0].duration;
	    			var pourcent_actuel = ((media_hover[0].currentTime / duration) * 100);
	    			if(e.keyCode == 37){
	    				var new_percent = pourcent_actuel - 5;
	    				if(new_percent < 0)
	    					new_percent = 0;
	    				media_hover.mediaspip_seek_to_percent(new_percent,media_hover[0].slider,media_hover[0].options,true);
	    			}else{
	    				var new_percent = pourcent_actuel + 5;
	    				if(new_percent > 100)
	    					new_percent = 100;
	    				media_hover.mediaspip_seek_to_percent(new_percent,media_hover[0].slider,media_hover[0].options,true);
	    			}
	    			return;
	        	}
	    	}
    		$('video,audio').each(function(){
	        	if($(this)[0].isFullScreen||(($('input:focus,textarea:focus,select:focus').size() == 0) && (!$(this)[0].paused && !$(this)[0].ended))){
	        		e.preventDefault();
	        		var id = $(this);
	    			var duration = id[0].duration;
	    			var pourcent_actuel = ((id[0].currentTime / duration) * 100);

	    			if(e.keyCode == 37){
	    				var new_percent = pourcent_actuel - 5;
	    				if(new_percent < 0)
	    					new_percent = 0;
	    				id.mediaspip_seek_to_percent(new_percent,id[0].slider,id[0].options,true);
	    			}else{
	    				var new_percent = pourcent_actuel + 5;
	    				if(new_percent > 100)
	    					new_percent = 100;
	    				id.mediaspip_seek_to_percent(new_percent,id[0].slider,id[0].options,true);
	    			}
	        	}
    	  });
	    }
	});
})(jQuery);

/**
 * Converti un nombre de secondes en une heure lisible hh:mm:ss
 *
 * @param seconds int Le nombre de secondes
 * @return
 */
function mediaspip_second_to_time(seconds){
	if(seconds == 0)
		return '00:00';
	var uTime = Math.round(seconds*Math.pow(10,0))/Math.pow(10,0);
	var hours = Math.floor(uTime/3600);
	hours = (hours >0) ? (hours<10?'0'+hours:hours)+':' : '';
	var minutes = (Math.floor(uTime/60)%60);
	minutes= minutes<10?'0'+minutes:minutes;
	seconds = (uTime%60);
	seconds = seconds<10?'0'+seconds:seconds;
	var time = hours+minutes+':'+seconds;
	return time;
}

function mediaspip_percent_to_time(percent,total){
	var seconds = Math.ceil((total * percent) / 100);
	return mediaspip_second_to_time(seconds);
}

function mediaspip_anything_to_percent(current,total){
	return Math.ceil((current / total) * 100);
}

function isiPhone() {
    var agent = navigator.userAgent.toLowerCase();
    return agent.match(/iPhone/i);
}
function isiPad(){
	var agent = navigator.userAgent.toLowerCase();
    return agent.match(/iPad/i);
}