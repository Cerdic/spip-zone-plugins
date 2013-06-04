/**
 * 
 * MediaSPIP player
 * 
 * Player html5 pour les balises <audio> et <video>
 * avec fallback vers version flash pour flv/mp4/mp3/aac
 * 
 * $version : 1.2.5
 * © GNU/GPL v3 - kent1 (http://kent1.info - kent1@arscenic.info)
 * cf : http://www.mediaspip.net/technical-documentation/plugins-used-by-mediaspip/html5-player-video-sound-media/
 * 
 * Remplace les controles des lecteurs html5 par défaut des navigateurs
 * Remplace la balise <video> par un lecteur flash si on ne peut jouer le media :
 * - sur Firefox si seulement la version mp4 ou flv est disponible et pas de version ogv ou ogg
 * - sur Safari si seulement la version flv disponible et pas la mp4
 * - sur IE dans tous les cas
 * 
 * Plugins jQuery compatibles et utilisés:
 * - jQuery UI slider : http://jqueryui.com/slider/ (pour que les barres de progressions et de volume soient des sliders)
 * - jQuery cookies : https://github.com/carhartl/jquery-cookie (Pour l'option qui garde en mémoire le niveau de volume)
 * - jQuery mousewheel : https://github.com/brandonaaron/jquery-mousewheel (Diminue et augmente le volume lors d'un mousewheel sur le lecteur) 
 * 
 * Options : 
 * - autoplay boolean true/false : lit automatiquement la video ou le son (défaut : false)
 * - autoload boolean true/false : précharge automatiquement la video ou le son (défaut : true)
 * - minwidth int : La largeur minimale
 * - movieSize string 'adapt' uniquement pour l'instan:
 * 	- "adapt" fais prendre la largeur du bloc parent à la vidéo
 * - cookie_volume boolean true/false : met dans un cookie ms_volume le niveau de volume 
 *   et dans html_volume_muted la valeur 'muted' si on a désactivé le son
 * - messages boolean : affiche ou pas des messages à même le lecteur : 
 *   play / pause / autres changements d'état... (defaut : true)
 * - volume int : un int représentant un pourcentage de volume
 * - volume_bloque boolean : bloquer le niveau de volume
 * - volume_slider_orientation vertical|horizontal : permet de définir l'orientation du slider de volume (défaut vertical)
 * - muted boolean : true si muted à l'initialisation, false sinon
 * - muted_bloque boolean : true pour rendre impossible le changement de mute
 * - boutons_caches array : un tableau des boutons à ne pas afficher ['fullscreen','volume','loop']
 * - messages bool : si false, n'affiche pas les messages au dessus du player lors d'actions utilisateur
 */
    
(function($) {
	
	/**
	 * Vérifier si on a accès à l'API fullscreen de html5
	 * http://johndyer.name/native-fullscreen-javascript-api-plus-jquery-plugin/
	 */ 
	var fullScreenApi = {
			supportsFullScreen: false,
			isFullScreen: function() { return false; },
			requestFullScreen: function() {},
			cancelFullScreen: function() {},
			fullScreenEventName: '',
			prefix: ''
	},browserPrefixes = 'webkit moz o ms khtml'.split(' ');

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
	// update methods to do something useful
	if (fullScreenApi.supportsFullScreen) {
		fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';
	    fullScreenApi.isFullScreen = function() {
	    	switch (this.prefix) {
	    		case '':
	    			return document.fullScreen;
	    		case 'webkit':
	    			return document.webkitIsFullScreen;
	    		default:
	    			return document[this.prefix + 'FullScreen'];
	    	}
	    }
	    fullScreenApi.requestFullScreen = function(el) {
	    	return (this.prefix === '') ? el.requestFullScreen() : el[this.prefix + 'RequestFullScreen']();
	    }
	    fullScreenApi.cancelFullScreen = function(el) {
	    	return (this.prefix === '') ? document.cancelFullScreen() : document[this.prefix + 'CancelFullScreen']();
	    }
	}
	
	window.fullScreenApi = fullScreenApi;
	
	var slider = (typeof($.ui) == 'object') && (typeof($.ui.slider) == 'function'),
		cookies = (typeof($.cookie) == 'function'),
		stop_message_timeout = false,
		browser = $.browser,
	    IS_IE = browser.msie,
	    UA = navigator.userAgent,
		IS_IPAD = /iPad|MeeGo/.test(UA),
		IS_IPHONE = /iP(hone|od)/i.test(UA),
		IS_ANDROID = /Android/.test(UA),
		IPAD_VER = IS_IPAD ? parseFloat(/Version\/(\d\.\d)/.exec(UA)[1], 10) : 0,
		dataload = !IS_IPAD && !IS_IPHONE,
		zeropreload = !IS_IE && !IS_ANDROID,
		touch= ('ontouchstart' in window);
	
	/**
	 * Fonction d'initialisation du lecteur sur une balise <audio> ou <video>
	 *
	 * Exemple d'appel :
	 * $('video').ms_player_init();
	 */
	$.extend($.fn, {
		ms_player_init : function(options){
			/**
			 * Si c'est iTruc, cela ne fonctionne pas => on sort direct et on laisse la balise html5 faire ce qu'elle peut
			 */
			if(IS_IPAD || IS_IPHONE || IS_ANDROID) return;
			
			var defaults = {
				autoplay:false, // Lire automatiquement au chargement
				autoload:true, // Précharger automatiquement au chargement
				minwidth:null, // Largeur minimale
				movieSize:null,
				ratio:null,
				messages:true, // Afficher ou non les messages sur le lecteur
				volume:100, // Niveau de volume au chargement
				volume_bloque:false, // bloque le niveau de volume
				volume_slider_orientation:'horizontal', // Si on a les sliders, orientation du slider de volume
				muted:false, // Le lecteur n'est pas mute par défaut
				muted_bloque:false, // On autorise le switch mute/unmute sur le lecteur
				cookie_volume:false, // Garder le niveau de volume de l'utilisateur dans un cookie
				messages:true, // Affiche ou non les messages sur le lecteur
				boutons_caches:[]
			};
			
			options = $.extend(defaults, options);
			
			var media = $(this),
				id = $(this)[0],
				playable = false;
			
			if(media.is(':hidden')) $(this).show();
			if(media.is('audio')) options.movieSize = null;
			
			/**
			 * Test si le navigateur dispose du support des balises <video> ou <audio>
			 */
			if(typeof(id) != "undefined" && typeof(id.canPlayType) != "undefined"){
				media.children('source').each(function(){
					if(($(this).attr('type') != 'video/x-flv') && (id.canPlayType($(this).attr('type')) != '')){
						if(($(this).attr('type').match('video/ogg') || $(this).attr('type').match('video/webm')) && /Safari/i.test(navigator.userAgent)){
							playable = false;
						}else{
							playable = true;
							return false;
						}
					}
				});
				/**
				 * Si le navigateur ne peut pas :
				 * - Utiliser correctement les balises <audio> et <video>
				 * - Jouer une des sources disponibles
				 *
				 * On essaie de faire un fallback en flash (mp3/flv/mp4/aac)
				 */
				if(!playable && (typeof($.fn.ms_test_fallback) == 'function')){
					media.ms_test_fallback(options);
				}else{
					var	wrapper = '',
						class_wrapper = '',
						styles = ' style="',
						style = true,
						control = null,
						bloc_messages = '',
						height = options.height,
						width = options.width;
					
					id.mediacanplay = id.isFullScreen = false;
					id.options = options;
					id.percent_loaded = 0;
					id.slider_control = id.slider_volume = false;
					id.messages = options.messages;
					id.type = (media.is('video')) ? 'video' : 'audio';
					
					if(slider) id.slider = true;
					
					if(typeof(media.attr('loop')) == 'string'){
						class_wrapper += 'loop';
						id.islooping = true;
					}
					else id.islooping = false;
					
					if(id.controls) id.addcontrols = true;
					id.controls = false;
					
					if(!width){
						if(media.attr('width')) width = media.attr('width');
						else if(media.width() > 0) width = media.width();
					}
					
					if(!height){
						if(media.attr('height')) height = media.attr('height');
						else if(media.height() > 0) height = media.height();
					}
					
					media.parent().wrapInner('<div class="media_wrapper loading no_metadata '+id.type+' '+class_wrapper+'"></div>');
					if(id.type == 'video'){
						if(options.height)
							media.parents('.media_wrapper').height(height);
						if(options.width)
							media.parents('.media_wrapper').width(width);
					}else
						media.parents('.media_wrapper').width(width);
					wrapper = media.parent();
					
					if(id.addcontrols){
						if(id.type == 'video')
							controls = '<div class="ms_splash"></div>'
						else
							controls = '';
								
						controls += '<div class="controls small">'
							+'<div class="buttons_left">'
								+'<span class="play_pause_button" title="'+ms_player_lang.bouton_loading+'"></span>'
							+'</div>'
							+'<div class="progress_bar">'
								+'<em class="elapsed_time" title="'+ms_player_lang.info_ecoule+'"></em>'
								+'<div class="progress_back">'
									+'<div class="progress_loading_wrapper">'
										+'<div class="progress_loading_stripes"></div>'
									+'</div>'
									+'<div class="progress_buffered"></div>'
									+'<div class="progress_elapsed_time"></div>'
									+'<span class="progress_indicator"></span>'
								+'</div>'
								+'<em class="remaining_time remaining" title="'+ms_player_lang.info_restant+'"></em>'
							+'</div>'
							+'<div class="buttons_right">';
						controls += ($.inArray('volume',options.boutons_caches) == 0) ? '' : '<span class="volume_button" title="'+ms_player_lang.bouton_volume+' ('+Math.floor(id.volume*100)+'%)"></span>';
		
						/**
						 * Si on a les sliders, on ajoute une div ici pour avoir un slider de volume
						 */
						controls += (slider && $.inArray('volume',options.boutons_caches) == '-1') ? '<span class="volume_slider_container '+options.volume_slider_orientation+'"><span class="volume_slider"></span></span>' : '';
						controls += (media.is('video') && $.inArray('fullscreen',options.boutons_caches) == '-1') ? '<span class="fullwindow_button" title="'+ms_player_lang.bouton_fullscreen+'"></span>' : '';
						controls += ($.inArray('loop',options.boutons_caches) == '-1') ? '<span class="loop_button" title="'+ms_player_lang.bouton_loop+'"></span>' : '';
						controls +='</div>';
		
						wrapper.append(controls);
						control = wrapper.find('.controls');
					}

					if(id.type== 'video' && !height){
						height = media.parents('.media_wrapper').find('.controls').height();
						media.parents('.media_wrapper').add(media).height(height)
					}
						
					if(options.minwidth && $(this).width() > options.minwidth){
						wrapper.width(options.minwidth);
					}
					
					if(wrapper.height() < 100) options.messages = false;
					
					if(options.messages){
						bloc_messages = '<div class="messages" style="display:none"></div>';
						wrapper.append(bloc_messages);
					}
					
					if(media.prev().is('img')){
						
						media.prev().wrap('<div class="html5_logo"></div>');
						media.prev().click(function(){
							media.ms_play_pause();
						});
					}
					if(control){
						var elapsed_time = control.find('.elapsed_time'),
							remaining_time = control.find('.remaining_time'),
							progress_indicator = control.find('.progress_indicator'),
							progress_elapse = control.find('.progress_elapsed_time');
						media.ms_resize_controls();
					}
					
					media.bind("loadedmetadata",function(e){
						media.ms_start('loadedmetadata');
						$(this).parents('.media_wrapper').removeClass('no_metadata').addClass('has_metadata');
						/**
						 * Cas d'un stream audio (Radio)
						 */
						if(id.duration == 'Infinity'){
							id.isstream = true;
							control.find('.progress_back').detach();
							remaining_time.addClass('total_time').attr('title','').html(ms_player_lang.info_streaming);
						}
						media.ms_resize_controls();
					}).bind("error", function(e){
						if (id.error) {							
							switch (id.error.code) {
								case 1:
									error_message = ms_player_lang.statut_error_stopped;
									break;
								case 2:
									error_message = ms_player_lang.statut_error_network;
									break;
								case 3:
									error_message = ms_player_lang.statut_error_broken;
									break;
								case 4:
									error_message = ms_player_lang.statut_error;
									break;
								default:
									error_message = ms_player_lang.statut_error;
									break
							}
							if(options.messages && error_message)
								media.ms_messages('error',error_message);
							wrapper.removeClass('loading').addClass('player_error').find('.play_pause_button').attr('title',ms_player_lang.info_erreur);
						}
					}).bind("timeupdate", function(e){
						if(control && id.percent_loaded != 100) media.ms_update_loaded(e);

						var percent_time = ms_anything_to_percent(id.currentTime,id.duration);
						if(remaining_time.is('.remaining') && (id.duration != 'Infinity'))
							remaining_time.text('-'+ms_second_to_time(id.duration - id.currentTime));
						
						elapsed_time.text(ms_second_to_time(id.currentTime));
						if(slider && (typeof(id.slider_control) == 'object')){
							progress_elapse.css('width',percent_time+'%');
							id.slider_control.slider("value", percent_time);
						}else
							progress_indicator.css('left',percent_time+'%');

						media.ms_resize_controls();
					}).bind("seeking",function(e){
						wrapper.addClass('seeking').find('.play_pause_button').attr('title',ms_player_lang.bouton_seeking);
					}).bind("seeked",function(e){
						wrapper.removeClass('seeking');
						if(id.paused)
							wrapper.find('.play_pause_button').removeClass('pause').attr('title',ms_player_lang.bouton_pause);
						else
							wrapper.find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_lire);
					}).bind("progress", function(e){
						if(control && id.percent_loaded != 100) media.ms_update_loaded(e);
					}).bind("pause", function(e){
						media.ms_buttons();
					}).bind("play", function(e){
						if(control && id.percent_loaded != 100) media.ms_update_loaded(e);
						media.ms_buttons();
					}).bind("ended", function(e){
						if(!id.islooping){
							wrapper.addClass('paused').find('.play_pause_button').removeClass('pause').attr('title',ms_player_lang.bouton_lire);
							id.paused = true;
						}else{
							id.currentTime = 0;
						    id.play();
						}
					}).bind("loadeddata", function(e){
						if(control) media.ms_update_loaded(e);
					}).bind("volumechange", function(e){
						media.ms_volume(false);
					}).click(function(){
						media.ms_play_pause();
					}).dblclick(function(e){
						media.ms_fullscreen();
						e.preventDefault();
						e.stopPropagation();
					});
	
					document.addEventListener(fullScreenApi.fullScreenEventName, function(e){
						if(id.isFullScreen && !fullScreenApi.isFullScreen())
							media.ms_fullscreen();
						media.ms_resize_controls();
					}, true);
					
					media.parent().find('.ms_splash').click(function(){
						if(id.paused && $(this).is(':visible'))
							media.ms_play_pause();
					});
					
					var stop_timeout = false,
						last_moved=0,
						clientx = 0,
						clienty = 0;
					
					wrapper.mousemove(function(e){
						if(id.isFullScreen && !id.paused){
							if ((e.timeStamp - last_moved > 1000) && ((e.clientX != clientx) || (e.clientY != clienty))) {
								wrapper.addClass('hover');
								clearTimeout(stop_timeout);
								var full_onmousestop = function() { wrapper.removeClass('hover'); };
								stop_timeout = setTimeout(full_onmousestop, 1500);
								last_moved = e.timeStamp;
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
					if(control){
						control.find('.play_pause_button').click(function(){
							media.ms_play_pause();
						});
						
						control.find('.progress_back').click(function(e){
							if(!slider)
								media.ms_seek_to(e.clientX,slider);
						});
						
						if($.inArray('volume',options.boutons_caches) == '-1' && !options.muted_bloque){
							control.find('.volume_button').click(function(e){
								media.ms_volume(true);
							});
						}
						
						if($.inArray('fullscreen',options.boutons_caches) == '-1'){
							control.find('.fullwindow_button').click(function(e){
								media.ms_fullscreen();
							});
						}
						
						if($.inArray('loop',options.boutons_caches) == '-1'){
							control.find('.loop_button').click(function(e){
								media.ms_loop();
							});
						}
						remaining_time.click(function(e){
							if(!id.isstream){
								if($(this).is('.remaining')){
									$(this).removeClass('remaining').addClass('total_time')
										.attr('title',ms_player_lang.info_total)
										.html(ms_second_to_time(id.duration));
								}else{
									$(this).removeClass('total_time').addClass('remaining')
										.attr('title',ms_player_lang.info_restant)
										.html('-'+ms_second_to_time(id.duration - id.currentTime));
								}
								media.ms_resize_controls();
							}
						});
					}
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
					if(!options.volume_bloque && typeof($.fn.mousewheel != "undefined")){
						wrapper.mousewheel(function(event, delta) {
							if(!id.muted){
								var volume_new = Math.round((id.volume + parseFloat((delta > 0) ? '0.1' : '-0.1'))*10)/10;
								if((volume_new <= 1) && (volume_new >= 0)) id.volume = volume_new;
							}
							event.preventDefault();
						});
					}

					media.ms_start('canplay');
				}
			}else
				media.ms_test_fallback(options);
			this.trigger('init');
		},
		ms_start : function(action){
			var media = $(this),
				id = media[0],
				options = id.options,
				wrapper = media.parent(),
				control = wrapper.find('.controls'),
				elapsed_time = control.find('.elapsed_time'),
				remaining_time = control.find('.remaining_time');

			if(!id.mediacanplay || action == 'loadedmetadata'){
				if(wrapper.hasClass('player_error')) 
					wrapper.removeClass('player_error').addClass('loading');

				id.mediacanplay = true;

				var width_container = media.width(), parent_width = wrapper.parent().width();
				
				if(id.videoHeight && id.videoWidth)
					id.ratio = id.videoWidth/id.videoHeight;
				else if(options.ratio)
					id.ratio = options.ratio;
				else
					id.ratio = media.width()/media.height();
				
				if(options.movieSize == 'adapt' && !id.isFullScreen && (!media.hasClass('noresize') || (options.movieSize != 'noresize'))){
					/**
					 * En mode adapt :
					 * - on dimensionne la largeur à 100%
					 * - on dimensionne la hauteur à un ratio correspondant au ratio réel de la vidéo 
					 * par rapport à la largeur du bloc parent
					 */
					width_container = parent_width;
					if(id.videoHeight && id.videoWidth)
						var ratio = (width_container/id.videoWidth),
							height_final = (id.videoHeight*ratio).toFixed();
					else
						var height_final = width_container/id.ratio;
					
					if(id.type == 'video'){
						wrapper.animate({height:height_final+'px',width:'100%'},'fast',function(){
							media.animate({height:'100%',width:'100%'},'fast').attr('height',false).ms_resize_controls();
						});
					}else{
						wrapper.animate({width:'100%'},'fast',function(){
							media.animate({width:'100%'},'fast').ms_resize_controls();
						});
					}
				}else if(!media.hasClass('noresize') && (options.movieSize != 'noresize')){
					/**
					 * En mode normal, on redimentionne la hauteur de la vidéo en fonction 
					 * du ratio réel récupéré des métadonnées
					 */
					if(!options.height){
						var media_height = media.width()/id.ratio;
						media.attr('height','');
						if(id.type == 'video')
							wrapper.height(media_height);
					}else{
						var media_width = media.height()*id.ratio;
						if(id.type == 'video'){
							wrapper.width(media_width);
							media.width('100%').attr('width','');
						}
					}
				}

				if(wrapper.hasClass('loading'))
					wrapper.removeClass('loading').addClass('paused').find('.play_pause_button').attr('title',ms_player_lang.bouton_lire);
				
				if(remaining_time.is('.remaining') && (id.duration != 'Infinity') && !isNaN(id.duration))
					remaining_time.text('-'+ms_second_to_time(id.duration));

				elapsed_time.text(ms_second_to_time(id.currentTime));
                
				try {
                    arg.buffer = id.buffered.end(null);
                } catch (ignored) {}
                
				if((id.networkState == 2) && id.duration && !isNaN(id.duration) && id.buffer){
					var percent_load = ms_anything_to_percent(id.buffered.end(0),id.duration);
					control.find('.progress_buffered').css('width',percent_load+'%');
				}
				
				if(slider){
					var replay = false;
					control.find('.progress_indicator').hide();
					control.find('.progress_back').unbind('click');
					id.slider_control = control.find('.progress_back').slider({
						min: 0,
						max: 100,
						range: "min",
						start: function(event,ui){},
						slide: function(event,ui){
							/**
							 * - On change les valeurs de temps
							 * - On fait avancer la barre de lecture sans lancer la lecture
							 */
							control.find('.progress_elapsed_time').css('width',ui.value+'%');
							if(remaining_time.is('.remaining') && (id.duration != 'Infinity'))
								remaining_time.text('-'+ms_percent_to_time((100 - ui.value),id.duration));
							elapsed_time.text(ms_percent_to_time(ui.value,id.duration));
						},
						stop: function(event,ui){
							/**
							 * On saute la lecture au bon endroit ?
							 */
							media.ms_seek_to(event.clientX,true,false);
						}
					});

					if($.inArray('volume',options.boutons_caches) == '-1'){
						id.slider_volume = control.find('.volume_slider').slider({
							value:Math.floor(id.volume*100),
							orientation: options.volume_slider_orientation,
							min:0,
							max:100,
							disabled: (options.volume_bloque) ? true : false,
							range: "min",
							slide: function(event,ui){
								/**
								 * On change le volume
								 */
								var volume_new = ui.value/100;
								if((volume_new <= 1) && (volume_new >= 0)) id.volume = volume_new;
							},
							stop: function(event,ui){
								/**
								 * On change le volume et on le sauvegarde dans le cookie si nécessaire
								 */
								var volume_new = ui.value/100;
								if((volume_new <= 1) && (volume_new >= 0)) id.volume = volume_new;
							}
						});
					}
				}
				
				id.volume = Math.floor(parseInt(options.volume)/100);
				if(options.muted){
					var options_messages = id.options.messages;
					id.options.messages = false;
					media.ms_volume(true);
					id.options.messages = options_messages;
				}

				if(!options.volume_bloque && !options.volume && cookies && options.cookie_volume){
					var volume_cookie = parseFloat($.cookie('ms_volume'));
					if((volume_cookie >= 0) && (volume_cookie <= 1))
						id.volume = volume_cookie;
					if(($.cookie('ms_volume_muted') == 'muted') && !id.muted)
						media.ms_volume(true);
				}
			}
			if(options.autoplay && id.mediacanplay){
				wrapper.removeClass('paused').find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_pause);
				id.play();
			}
			$(this).parents('.media_wrapper').attr("tabindex",-1).hover(function(){
				$(this).focus();
			});
			$(this).parents('.media_wrapper').unbind('keydown').keydown(function(e) {
				$(this).unbind('keyup').keyup(function(e){e.stopPropagation();e.preventDefault();}).ms_activate_keys(e);
			});
			media.ms_resize_controls();
			this.trigger('start');
		},
		ms_buttons : function(){
			if($(this)[0].paused){
				$(this).parent('.media_wrapper').addClass('paused').find('.play_pause_button').removeClass('pause').attr('title',ms_player_lang.bouton_lire);
				$(this).ms_messages('pause',ms_player_lang.statut_pause);
			}
			else if ($(this)[0].ended){
				$(this).parent('.media_wrapper').removeClass('paused').find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_pause);
				$(this)[0].currentTime = $(this)[0].startTime ? $(this)[0].startTime : '0';
				if(!$(this)[0].options.volume_bloque && cookies && $(this)[0].options.cookie_volume){
					var volume_cookie = parseFloat($.cookie('ms_volume'));
					if((volume_cookie >= 0) && (volume_cookie <= 1))
						$(this)[0].volume = volume_cookie;
					if($.cookie('ms_volume_muted') == 'muted')
						$(this)[0].muted = true;
				}
			}
			else{
				$(this).parent('.media_wrapper').removeClass('paused').find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_pause);
				$(this).ms_messages('play',ms_player_lang.statut_play);
				if(!$(this)[0].options.volume_bloque && cookies && $(this)[0].options.cookie_volume){
					var volume_cookie = parseFloat($.cookie('ms_volume'));
					if((volume_cookie >= 0) && (volume_cookie <= 1))
						$(this)[0].volume = volume_cookie;
					if($.cookie('ms_volume_muted') == 'muted')
						$(this)[0].muted = true;
				}
			}
		},
		ms_play_pause : function(){
			if($(this)[0].mediacanplay && !$(this)[0].seeking){
				var options = $(this)[0].options;
				if($(this)[0].paused){
					$(this)[0].play();
					this.trigger('play');
				}else if ($(this)[0].ended){
					$(this)[0].play();
					this.trigger('play');
				}else{
					$(this)[0].pause();
					this.trigger('pause');
				}
			}
		},
		/**
		 * Fonction de modification du volume
		 * 
		 * Paramètres :
		 * - mute boolean (true/false) : signifie si le media est muted ou pas;
		 */
		ms_volume : function(mute){
			if($(this)[0].mediacanplay){
				var control = $(this).parent().find('.controls'), options = $(this)[0].options;
				if(mute){
					if($(this)[0].muted){
						$(this).ms_messages('mute',ms_player_lang.statut_unmute);
						var volume_title = ms_player_lang.bouton_volume+' ('+Math.floor($(this)[0].volume*100)+'%)';
						control.find('.volume_button').removeClass('muted').attr('title',volume_title);
						$(this)[0].muted = false;
						if($(this)[0].slider && (typeof($(this)[0].slider_volume) == 'object'))
							$(this)[0].slider_volume.slider('value',($(this)[0].volume*100)).slider((options.volume_bloque) ? 'disable' : 'enable');
						if(cookies && options.cookie_volume)
							$.cookie('ms_volume_muted',null);
					}else{
						$(this).ms_messages('mute',ms_player_lang.statut_mute);
						control.find('.volume_button').addClass('muted').attr('title',ms_player_lang.bouton_volume_muted);
						$(this)[0].muted = true;
						if($(this)[0].slider && (typeof($(this)[0].slider_volume) == 'object'))
							$(this)[0].slider_volume.slider('disable');
						if(cookies && options.cookie_volume)
							$.cookie('ms_volume_muted','muted');
					}
				}else if(!$(this)[0].muted){
					var volume = $(this)[0].volume,
						sound_button = control.find('.volume_button'),
						class_remove = sound_button.attr('class').match('volume_button_[0-9]{1,3}');
					if(options.volume_bloque && options.volume) volume = $(this)[0].volume = (options.volume) ? $(this)[0].volume : options.volume;
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
					var volume_title = ms_player_lang.bouton_volume+' ('+Math.floor($(this)[0].volume*100)+'%)';
					control.find('.volume_button').attr('title',volume_title);
					if($(this)[0].slider && (typeof($(this)[0].slider_volume) == 'object')){
						$(this)[0].slider_volume.slider('value',($(this)[0].volume*100));
						$(this)[0].slider_volume.slider((options.volume_bloque) ? 'disable' : 'enable');
					}
					$(this).ms_messages('volume',volume_title);
						
					if(cookies && options.cookie_volume)
						$.cookie('ms_volume', $(this)[0].volume);
				}
			}
		},
		/**
		 * Fonction de saut d'un point à un autre dans le média
		 * Utilisée lors d'un click sur la barre de progression
		 *
		 * Prends deux arguments :
		 * - La position du curseur de la souris lors du click sur la barre de progression
		 * - slider : si on a ou non les sliders d'activé
		 */
		ms_seek_to : function(cursor_position){
			var was_playing = false,
				control = $(this).parent().find('.controls'),
				progress = control.find('.progress_back'),
				percent = Math.floor(((parseInt(cursor_position)-parseInt(progress.offset().left))/ parseInt(progress.width())) * 100),
				time = Math.floor(($(this)[0].duration * percent) / 100),
				time_affiche = ms_second_to_time(time);
			
			if(typeof($(this)[0].slider_control) != "object"){
				control.find('.progress_elapsed_time').css('width',percent+'%');
				control.find('.progress_indicator').css('left',percent+'%');
			}
			
			if($(this)[0].currentTime > time)
				$(this).ms_messages('seek_back',ms_player_lang.statut_seek_back+' '+time_affiche);
			else
				$(this).ms_messages('seek_to',ms_player_lang.statut_seek_to+' '+time_affiche);
			
			$(this)[0].currentTime = time;
		},
		/**
		 * ms_seek_to_percent
		 * 
		 * On seek à X% de la video ou du son
		 * 
		 * Paramètres : 
		 * - percent int : le pourcentage;
		 * - slider boolean true/false : le slider si présents;
		 * - update_slider boolean true/false : doit on mettre à jour le slider (pas utile quand on vient du slider justement);
		 */
		ms_seek_to_percent : function(percent,update_slider){
			var duration = $(this)[0].duration, 
				currenttime = $(this)[0].currentTime;
			
			if(((currenttime == duration) && (percent == 100)) || ((currenttime == 0) && (percent == 0)))
				return false;
			
			var time = (percent == 0) ? 0 : ((duration * percent) / 100),
				time_affiche = ms_second_to_time(time);
			
			if(currenttime > time)
				$(this).ms_messages('seek_back',ms_player_lang.statut_seek_back+' '+time_affiche);
			else
				$(this).ms_messages('seek_to',ms_player_lang.statut_seek_to+' '+time_affiche);
			
			$(this)[0].currentTime = time;
			
			if(typeof($(this)[0].slider_control) != "object"){
				var control = $(this).parent().find('.controls');
				control.find('.progress_elapsed_time').css('width',percent+'%');
				control.find('.progress_indicator').css('left',percent+'%');
			}else if(update_slider){
				$(this)[0].slider_control.slider("value", percent);
			}
		},
		/**
		 * Activer ou désactiver la boucle (mode loop)
		 */
		ms_loop : function(){
			var container = $(this).parent();
			if(!$(this)[0].loop && !container.hasClass('loop')){
				$(this).attr('title',ms_player_lang.bouton_loop_looped);
				container.addClass('loop');
				$(this).ms_messages('loop',ms_player_lang.statut_loop);
				if (typeof($(this)[0].loop) == 'boolean')
					$(this)[0].loop = true;
				$(this)[0].islooping = true;
			}else{
				$(this).attr('title',ms_player_lang.bouton_loop);
				container.removeClass('loop');
				$(this).removeAttr('loop').ms_messages('unloop',ms_player_lang.statut_unloop);
				if (typeof($(this)[0].loop) == 'boolean')
					$(this)[0].loop = false;
				$(this)[0].islooping = false;
			}
		},
		ms_fullscreen : function(){
			var container = $(this).parent(),
				id_container = container[0];
			if($(this)[0].mediacanplay){
				if(!$(this)[0].isFullScreen){
					$(this)[0].videoOrigWidth = $(this).width();
					$(this)[0].videoOrigHeight = $(this).height();
					id_container.origWidth = container.width();
					id_container.origHeight = container.height();
					if (fullScreenApi.supportsFullScreen)
						$(this).ms_fullscreen_resize();
					else{
						$('body').css({'overflow' : 'hidden', '-moz-user-select' : 'none'});
						$(this).ms_fullscreen_resize();
						$(window).unbind('resize').resize(function(){
							$(this).ms_fullscreen_resize();
						});
					}
					$(this)[0].isFullScreen = true;
				}else{
					if (fullScreenApi.supportsFullScreen) {
						(fullScreenApi.prefix === '') ? document.cancelFullScreen() : document[fullScreenApi.prefix + 'CancelFullScreen']();
						container.bind('fullscreen_resize',function(){
							container.css({width:id_container.origWidth,height:id_container.origHeight}).removeClass('media_wrapper_full');
							$(this).ms_resize_controls();
						});
					}else{
						container.bind('fullscreen_resize',function(){
							container.removeClass('media_wrapper_full').animate({width:id_container.origWidth+'px',height:id_container.origHeight+'px',left:'0',top:'0'},500,function(){
								$(this).ms_resize_controls();
							});
						});
						$(this).bind('fullscreen_resize',function(){
							$(this).animate({width:$(this)[0].videoOrigWidth+'px',height:$(this)[0].videoOrigHeight+'px',left:'0',top:'0'},500,function(){
								$(this).ms_resize_controls();
							});
						});
						$('body').css({'overflow' : 'inherit', '-moz-user-select' : 'inherit'});
						$(window).unbind('resize');
					}
					container.add($(this)).trigger('fullscreen_resize').unbind('fullscreen_resize');
					container.find('.controls').addClass('small').find('span.fullwindow_button').attr('title',ms_player_lang.bouton_fullscreen);
					$(this)[0].isFullScreen = false;
				}
			}
		},
		ms_fullscreen_resize : function(){
			var container = $(this).parent(),
				id_container = container[0],
				window_width = window.innerWidth,
		    	window_height = window.innerHeight,
				ratio = (window_height/$(this)[0].videoHeight),
				width_final = ($(this)[0].videoWidth*ratio).toFixed();
	
			container.find('span.fullwindow_button').attr('title',ms_player_lang.bouton_fullscreen_full);
			
			if (fullScreenApi.supportsFullScreen) {
				(fullScreenApi.prefix === '') ? id_container.requestFullScreen() : id_container[fullScreenApi.prefix + 'RequestFullScreen']();
				container.css({width:'100%',height:'100%',left:'0',top:'0'}).addClass('media_wrapper_full').find('.controls').removeClass('small');
				$(this).ms_resize_controls();
			}else{
				container.css({width:'100%',height:'100%',left:'0',top:'0'}).addClass('media_wrapper_full').find('.controls').removeClass('small');
				if(width_final > window_width){
					var ratio = (window_width/$(this)[0].videoWidth);
					var height_final = ($(this)[0].videoHeight*ratio).toFixed();
					var top = ((window_height-height_final)/2).toFixed();
					$(this).css({position:'absolute',width:window_width+'px',height:height_final+'px',top:top+'px',left:'0'});
				}else{
					var left = ((window_width-width_final)/2).toFixed();
					$(this).css({position:'auto',width:width_final+'px',height:window_height+'px',left:left+'px',top:'0'});
				}
				$(this).ms_resize_controls();
			}
		},
	
		/**
		 * Change dynamiquement la taille de la barre de progression et de son conteneur
		 * en fonction de la taille du lecteur.
		 * 
		 * @param force boolean true / false : permet d'éviter de boucle lorsque la taille du lecteur est petite
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
		 * 
		 * Si on a un petit lecteur, que l'on a le slider de volume et que celui-ci est horizontal, on le force à passer vertical
		 * 
		 */
		ms_resize_controls : function(force){
			var control = $(this).parent().find('.controls'),
				play_width = control.find('.buttons_left').outerWidth()+parseFloat(control.find('.buttons_left').css('margin-left'))+parseFloat(control.find('.buttons_left').css('margin-right')),
				sound_width = control.find('.buttons_right').outerWidth()+parseFloat(control.find('.buttons_right').css('margin-left'))+parseFloat(control.find('.buttons_right').css('margin-right')),
				progresswidth = parseFloat(control.width())-parseFloat(play_width)-parseFloat(sound_width) - parseFloat(control.find('.progress_bar').css('border-left-width')) - parseFloat(control.find('.progress_bar').css('border-right-width'))-parseFloat(control.find('.progress_bar').css('margin-right')) - parseFloat(control.find('.progress_bar').css('margin-left')) - parseFloat(control.find('.progress_bar').css('padding-right')) - parseFloat(control.find('.progress_bar').css('padding-left')) -1;
			
			control.find('.progress_bar').width(progresswidth);
			
			var remaining_width = control.find(".remaining_time").outerWidth()+parseFloat(control.find('.remaining_time').css('margin-left'))+parseFloat(control.find('.remaining_time').css('margin-right')),
				elapsed_width = control.find(".elapsed_time").outerWidth()+parseFloat(control.find('.elapsed_time').css('margin-left'))+parseFloat(control.find('.elapsed_time').css('margin-right'));
			
			if(control.find(".remaining_time").is(':hidden')) remaining_width = 0;

			var progressback_width = progresswidth - elapsed_width - remaining_width - parseFloat(control.find('.progress_back').css('border-left-width')) - parseFloat(control.find('.progress_back').css('border-right-width'))-parseFloat(control.find('.progress_back').css('margin-right')) - parseFloat(control.find('.progress_back').css('margin-left')) - parseFloat(control.find('.progress_back').css('padding-right')) - parseFloat(control.find('.progress_back').css('padding-left'))-2;
			
			if(slider && progressback_width < 0 && !force){
				if($(this)[0].slider && (typeof($(this)[0].slider_volume) == 'object')){
					$(this)[0].slider_volume.slider('option',{'orientation':'vertical'});
					control.find('.volume_slider_container').removeClass('horizontal').addClass('vertical');
				}
				$(this).ms_resize_controls(true);
			}else{
				if(control.find('.remaining_time').is(':hidden') && control.find('.loop_button').is(':visible') && progressback_width < 30){
					control.find('.loop_button').hide();
					control.ms_resize_controls(true);
				}
				else if(control.find('.remaining_time').is(':visible') && progressback_width < 30){
					control.find('.remaining_time').hide();
					$(this).ms_resize_controls(true);
				}else if(control.find('.remaining_time').is(':hidden') && progressback_width < 30){
					control.find('.progress_back').hide();
					return;
				}
				control.find('.progress_back').width(progressback_width);
			}
		},
		/**
		 * Modifie le message dans la boite de message si présente
		 * 
		 * @param type string : type de message (utilisé comme class sur le span entourant le message)
		 * @param message string : le contenu du message 
		 */
		ms_messages : function(type,message){
			var messages, options = $(this)[0].options,
				message = '<span class="'+type+'">'+message+'</span>';

			if(!options.messages) return;
			
			if($(this).is('.media_wrapper')) messages = $(this).find('.messages');
			else messages = $(this).parents('.media_wrapper').find('.messages');
			
			if(messages.is(':visible')){
				messages.hide();
				clearTimeout(stop_message_timeout);
			}
			
			var fade_play = function() {
				messages.fadeOut(function(){ $(this).html(''); });
			};
			
			messages.html(message);
			if(messages.is(':hidden')){
				messages.fadeIn('normal',function(){
					if(type != 'error') stop_message_timeout = setTimeout(fade_play, 1500);
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
		 * on regarde si on a une fonction de fallback flash et dans ce cas on l'utilise.
		 */
		ms_test_fallback : function(options){
			var me = $(this);
			if(typeof($.fn.ms_fallback_flash) == 'function'){
				if(jQuery.browser.msie)
					var sources = $(this).parent().children("source");
				else
					var sources = $(this).children("source");
				$.each(sources, function(index, value){
					if(($(this).attr('type').match('video/x-flv'))||($(this).attr('type').match('video/mp4'))||($(this).attr('type').match('audio/mpeg'))){
						var defaults_flash = {
							flowurl:options.flowurl,
							flasherror:options.flasherror ? options.flasherror : '',
							autoplay:options.autoplay,
							autoload:options.autoload,
							wmode : 'transparent',
							width : $(this).attr('width'),
							height : $(this).attr('height')?$(this).attr('height'):$(this).parent().height(),
							poster : $(this).attr('poster'),
							sources : $(this),
							loop : (typeof(me.attr('loop')) == 'undefined') ? false : true
						}
						var options_flash = $.extend(options,defaults_flash);
						me.ms_fallback_flash(options_flash);
						/**
						 * On s'arrête au premier élément qui nous convient
						 */
						return false;
					}
				});
			}
		},
		ms_update_loaded : function(e){
			var percent_loaded = null;
			if($(this)[0].buffered && $(this)[0].buffered.length)
				percent_loaded = ms_anything_to_percent($(this)[0].buffered.end(0),$(this)[0].duration);
			else if((typeof(e.loaded) != 'undefined') && (typeof(e.total) != 'undefined'))
				percent_loaded = ms_anything_to_percent(e.loaded,e.total);
			if(percent_loaded != null){
				$(this)[0].percent_loaded = percent_loaded;
				$(this).parent().find('.progress_buffered').css('width',percent_loaded+'%');
			}
		},
		/**
		 * Quelques évènements à la pression de touches sur le clavier
		 * -* esc : sort du fullscreen
		 * -* f : passe en fullscreen sur la video en lecture ou sur celle en hover/focus
		 * -* l : active ou désactive la lecture en boucle
		 * -* space : lance la lecture d'une vidéo ou la met en pause
		 * -* flèches haut et bas : augmente ou baisse le volume
		 * -* flèches gauche et droite : recule ou avance la lecture de 5%
		 */
		ms_activate_keys : function(e){
			var meta_key_pressed = e.ctrlKey || e.metaKey || e.altKey || e.shiftKey;
			if(!meta_key_pressed){
				switch (e.keyCode) {
					case 27 :
						/**
					     * Touche esc
					     * Sort du mode fullscreen (uniquement sur videos)
					     */
						e.preventDefault();
			        	if (!fullScreenApi.supportsFullScreen) {
			        		if($(this).find('video')[0].isFullScreen)
			        			$(this).find('video').ms_fullscreen();
			        	}
						break;
					case 70 :
						/**
					     * Touche f
					     * Active le fullscreen sur la video en lecture ou la video en hover/focus (uniquement sur videos)
					     */
			        	if(!$(this).find('video')[0].isFullScreen && ($('input:focus,textarea:focus').size() == 0)){
			        		$(this).find('video').ms_fullscreen();
			        		e.preventDefault();
			        	}
						break;
					case 76 :
						/**
					     * Touche l
					     * Active ou désactive le mode boucle (loop) sur le média en cours de lecture
					     */
						if($(this).find('video,audio')[0].isFullScreen || $('input:focus,textarea:focus').size() == 0){
							$(this).find('video,audio').ms_loop();
				        	e.preventDefault();
						}
						break;
					case 77 :
						/**
					     * Touche M
					     * Mute ou unmute
					     */
						if($(this).find('video,audio')[0].isFullScreen || $('input:focus,textarea:focus').size() == 0){
							$(this).find('video,audio').ms_volume(true);
			        		e.preventDefault();
						}
						break;
					case 32 :
						 /**
					      * Touche Space
					      * Lance la lecture ou met le media en pause
					      */
						
			    		if($(this).find('video,audio')[0].isFullScreen || ($('input:focus,textarea:focus').size() == 0)){
			    			$(this).find('video,audio').ms_play_pause();
			        		e.preventDefault();
			    		}
				        break;
					case 38 : case 40 :
					    /**
					     * Touches Up (38) et Down (40)
					     * Baisse ou augmente de 10% le volume de la video en cours de lecture
					     */
			    		if($(this).find('video,audio')[0].isFullScreen||($('input:focus,textarea:focus').size() == 0)){
			        		if(!$(this).find('video,audio')[0].muted){
								var delta  = (e.keyCode == 38) ? 1 : -1,
									volume = $(this).find('video,audio')[0].volume,
									volume_diff = (delta > 0) ? '0.1' : '-0.1',
									volume_new = Math.round((volume + parseFloat(volume_diff))*10)/10;
								if((volume_new <= 1) && (volume_new >= 0))
									$(this).find('video,audio')[0].volume = volume_new;
							}
			        		e.preventDefault();
			        		e.stopPropagation();
			    		}
				        break;
					case 37 : case 39 :
					    /**
					     * Gauche (37) et droite (39)
					     * Avance ou recule de 5% la video en cours de lecture
					     * Il faut également modifier la valeur de la barre
					     */
			    		if($(this).find('video,audio')[0].isFullScreen||($('input:focus,textarea:focus,select:focus').size() == 0)){
			    			var pourcent_actuel = (($(this).find('video,audio')[0].currentTime / $(this).find('video,audio')[0].duration) * 100);
			    			if(e.keyCode == 37)
				    			var new_percent = (pourcent_actuel >= 5) ? (pourcent_actuel - 5) : 0;
			    			else
				    			var new_percent = (pourcent_actuel > 95) ? 100 : (pourcent_actuel + 5);
			    			$(this).find('video,audio').ms_seek_to_percent(new_percent,true);
			    			e.preventDefault();
			    			e.stopPropagation();
			        	}
			    		break;
				}
			}
		}
	});
})(jQuery);

/**
 * Converti un nombre de secondes en une heure lisible hh:mm:ss
 *
 * @param seconds int Le nombre de secondes
 * @return
 */
function ms_second_to_time(seconds){
	if(seconds == 0) return '00:00';
	var uTime = Math.round(seconds*Math.pow(10,0))/Math.pow(10,0),
		hours = Math.floor(uTime/3600);
	hours = (hours >0) ? (hours<10?'0'+hours:hours)+':' : '';
	var minutes = (Math.floor(uTime/60)%60);
	minutes= minutes<10?'0'+minutes:minutes;
	seconds = (uTime%60);
	seconds = seconds<10?'0'+seconds:seconds;
	return hours+minutes+':'+seconds;
}

function ms_percent_to_time(percent,total){
	return ms_second_to_time(Math.ceil((total * percent) / 100));
}

function ms_anything_to_percent(current,total){
	return Math.ceil((current / total) * 100);
}