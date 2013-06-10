/**
 * 
 * MediaSPIP player
 * HTML5 to fallback flash
 *
 * $version : 1.2.5
 * © GNU/GPL v3 - kent1 (http://kent1.info - kent1@arscenic.info)
 * cf : http://www.mediaspip.net/technical-documentation/plugins-used-by-mediaspip/html5-player-video-sound-media/
 *
 */
(function($){
	var slider = (typeof($.ui) == 'object') && (typeof($.ui.slider) == 'function'),
		cookies = (typeof($.cookie) == 'function');

	$.extend($.fn, {
		ms_fallback_flash : function(options) {
			var defaults = {
				sources : $('source[type*="video/x-flv"],source[type*="video/mp4"],source[type*="application/mp4"],source[type*="audio/mpeg"]'),
				flowurl : "../flash/flowplayer.swf",
				autoload: false,
				autoplay: false,
				movieSize : null,
				bgcolor: '#000000',
				wmode: 'transparent',
				volume: 100,
				width:null,
				height:null,
				ratio:null,
				poster:null,
				loop:false,
				cookie_volume: false,
				messages: true,
				muted : false, // Le lecteur n'est pas mute par défaut
				muted_bloque : false, // On autorise le switch mute/unmute sur le lecteur
				volume_slider_orientation: 'horizontal',
				flasherror: 'Error : Flash is not installed',
				boutons_caches:[]
			},
			media = $(this),
			liens = [],
			options = $.extend(defaults, options);

			if(media.is('audio')){
				options.isSound = true;
				options.isVideo = false;
			}else{
				options.isVideo = true;
				options.isSound = false;
			}
			
			liens = sm2_chercher_liens(options.sources,liens);
			
			if(liens.length>0){
				var width = options.width,
					height = options.height,
					ratio = options.ratio;
				
				if(!width){
					if(!height && media.attr('width')) width = media.attr('width');
					else if(!height && media.width() > 0) width = media.width();
					else if(ratio && height)
						width = height*ratio;
				}
				if(!height){
					if(!width && media.attr('height'))
						height = media.attr('height').toFixed();
					else if(!width && media.height() > 0)
						height = media.height().toFixed();
					else if(ratio && width)
						height = (width/ratio).toFixed();
				}
				
				if(options.width && options.width == '100%')
					options.movieSize = 'adapt';
				
				if(options.poster && $(this).prev().is('img'))
					$(this).prev().detach();
				
				media.wrap('<div class="media_wrapper loading" />');
				var wrapper = media.parents('.media_wrapper');

				var controls = '';
					/**
					 * Le bloc html pour afficher les messages
					 */
					controls += (options.messages) ? '<div class="messages" style="display:none"></div>' : '';
					controls += (options.poster) ? '<div class="html5_cover"></div>' : '';
					controls +='<div class="flowplayer"></div>';
					controls +='<div class="controls small">'
					+'<div class="buttons_left">'
						+'<span class="play_pause_button" title="'+ms_player_lang.bouton_loading+'"></span>'
					+'</div>'
					+'<div class="progress_bar">'
						+'<em class="elapsed_time" title="'+ms_player_lang.info_ecoule+'">00:00</em>'
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
					controls += ($.inArray('volume',options.boutons_caches) == 0) ? '' : '<span class="volume_button" title="'+ms_player_lang.bouton_volume+' ('+Math.floor(options.volume*100)+'%)"></span>';
					/**
					 * Si on a les sliders, on ajoute une div ici pour avoir un slider de volume
					 */
					controls += (slider && $.inArray('volume',options.boutons_caches) == '-1') ? '<span class="volume_slider_container '+options.volume_slider_orientation+'"><span class="volume_slider"></span></span>' : '';
					controls += ($.inArray('loop',options.boutons_caches) == '-1') ? '<span class="loop_button" title="'+ms_player_lang.bouton_loop+'"></span>' : '';
					controls +='</div>';
				
					wrapper.html(controls);

				if(options.poster && options.isSound){
					wrapper.find('.html5_cover').html('<img src="'+options.poster+'" />');
					var width_poster = ((parseInt(wrapper.find('.html5_cover img').width()) > 0) ? wrapper.find('.html5_cover img').width() : width),
						height_poster = ((parseInt(wrapper.find('.html5_cover img').height()) > 0) ? wrapper.find('.html5_cover img').height() : height);
					wrapper.height(height_poster).width(width_poster).find('.flowplayer').height(height_poster).width(width_poster);
				}else wrapper.height(height).width(width);
				
				
				/**
				 * Si le wrapper est vraiment petit, pas de messages 
				 */
				if(wrapper.height() < 100) options.messages = false;
				
				wrapper[0].options = options;
				var allowfullscreen = options.isSound ? false : true,
					media_options = {
						onLoad:function(){
							this.timer = null;
							this.options = options;
							this.bufferfull = false;
							this.slider_done = false;
							this.former_duration = 0;
							
				    		var player = this,
				    			wrapper = $(this.getParent()).parents('.media_wrapper');
				    		
				    		wrapper.removeClass('loading').addClass('paused');
							
							if(wrapper[0].options.isSound){
								wrapper.find('.flowplayer').click(function(){
									if (player.isLoaded() && !options.poster) player.toggle();
									else if(!options.poster) player.play();
								});
							}
							wrapper.find('.play_pause_button').attr('title',ms_player_lang.bouton_lire).unbind('click').click(function(e){ 
								player.toggle();
							});
							if($.inArray('volume',wrapper[0].options.boutons_caches) == '-1' && !wrapper[0].options.muted_bloque){
								wrapper.find('.volume_button').click(function(e){
					    			var status = player.getStatus();
									if(status.muted){
										if(cookies && wrapper[0].options.cookie_volume) $.cookie('ms_volume_muted',null);
										if(typeof(player.slider_volume) == 'object') player.slider_volume.slider('enable');
										var volume_title = ms_player_lang.bouton_volume+' ('+Math.floor(player.getVolume())+'%)';
										wrapper.find('.volume_button').removeClass('muted').attr('title',volume_title);
										player.unmute();
										wrapper.ms_messages('mute',ms_player_lang.statut_unmute,wrapper[0].options);
									}else{
										if(cookies && wrapper[0].options.cookie_volume) $.cookie('ms_volume_muted','muted');
										if(typeof(player.slider_volume) == 'object') player.slider_volume.slider('disable');
										wrapper.find('.volume_button').addClass('muted').attr('title',ms_player_lang.bouton_volume_muted);
										player.mute();
										wrapper.ms_messages('mute',ms_player_lang.statut_mute,wrapper[0].options);
									}
								});
							}
							if(slider){
								wrapper.find('.progress_indicator').hide();
								if($.inArray('volume',wrapper[0].options.boutons_caches) == '-1'){
									player.slider_volume = wrapper.find('.volume_slider').slider({
										value:100,
										orientation: wrapper[0].options.volume_slider_orientation,
										min:0,
										max:100,
										disabled: (wrapper[0].options.volume_bloque) ? true : false,
										range: "min",
										slide: function(event,ui){
											/**
											 * On change le volume
											 */
											if((ui.value <= 100) && (ui.value >= 0)){
												player.setVolume(ui.value);
												wrapper.flow_change_volume(ui.value,player.slider_volume,false,wrapper[0].options);
												if(cookies && wrapper[0].options.cookie_volume) $.cookie('ms_volume', ui.value/100);
											}
										},
										stop: function(event,ui){
											/**
											 * On change le volume et on le sauvegarde dans le cookie si nécessaire
											 */
											if((ui.value <= 100) && (ui.value >= 0)){
												player.setVolume(ui.value);
												wrapper.flow_change_volume(ui.value,player.slider_volume,false,wrapper[0].options);
												if(cookies && wrapper[0].options.cookie_volume) $.cookie('ms_volume', ui.value/100);
											}
										}
									});
								}
				    		}
				    		
							wrapper.dblclick(function(e){ return false; });
							wrapper.flow_resize_controls();
				    	},
				    	onClipAdd :function(clip){},
				    	onError:function(error){},
				    	onUnload:function(error){},
				    	onBeforeKeyPress:function(e){
				    		if(e == 76){
				    			if($.inArray('loop',$(this.getParent()).parents('.media_wrapper')[0].options.boutons_caches) == '-1')
				    				$(this.getParent()).parents('.media_wrapper').find('.loop_button').click();
				    			return false;
				    		}
				    	},
				    	onKeyPress: function(e){
				    		var wrapper = $(this.getParent()).parents('.media_wrapper');
				    		if(e == 39 || e == 37){
				    			if(this.isPaused()){
				    				this.topause = true;
				    				this.play();
				    			}
				    		}
				    		if(e == 70 && !this.isFullscreen())
				    			this.toggleFullscreen();

				    		if(e == 38 || e == 40){
				    			if(!wrapper[0].options.volume_bloque){
					    			var volume = this.getVolume(),
										volume_diff = (e == 38) ? '10' : '-10',
										volume_new = Math.round((volume + parseFloat(volume_diff))*10)/10;
					    			wrapper.flow_change_volume(volume_new,this.slider_volume,false,wrapper[0].options);
				    			}
				    		}
				    		if(e == 186 || e == 77){
				    			if($.inArray('volume',this.options.boutons_caches) == '-1' && !wrapper[0].options.muted_bloque){
				    				wrapper.find('.volume_button').click();
				    			}
				    		}
				    	},
			        clip:{
			    		url:liens[0],
			    		coverImage: options.poster,
			            autoPlay:options.autoplay,
			            scaling:'fit',
			            autoBuffering: (options.isSound ? (options.autoplay ? true : false) : options.autoload),
			            bufferLength:5,
		            	onBeforeLoad:function(){},
		            	onBeforeBegin:function(){},
			            onBegin:function(clip){
			            	var player = this,
			            		wrapper = $(this.getParent()).parents('.media_wrapper');
			            	if(wrapper[0].options.loop){
			            		clip.looped = true;
			            		wrapper.addClass('loop');
			            	}
			            	if(typeof(clip.looped) == 'undefined')
			            		clip.looped = false;
			            	
			            	if($.inArray('loop',wrapper[0].options.boutons_caches) == '-1'){
					    		wrapper.find('.loop_button').unbind('click').click(function(e){
					    			if(clip.looped){
					    				clip.looped = false;
					    				delete(wrapper[0].options.loop);
					    				$(this).attr('title',ms_player_lang.bouton_loop);
					    				wrapper.removeClass('loop').ms_messages('loop',ms_player_lang.statut_unloop,wrapper[0].options);
					    			}else{
					    				clip.looped = true;
					    				$(this).attr('title',ms_player_lang.bouton_loop_looped);
					    				wrapper.addClass('loop').ms_messages('loop',ms_player_lang.statut_loop,wrapper[0].options);
					    			}
								});
			            	}
		            		if(typeof(clip.duration) != 'undefined'){
			            		var duration = ms_second_to_time(clip.duration);
			            		if(wrapper.find(".remaining_time").is('.remaining'))
			            			wrapper.find(".remaining_time").html('-'+duration);
								else
									wrapper.find(".remaining_time").html(duration);
								wrapper.find(".elapsed_time").html(ms_second_to_time(0));
		            		}else
		            			wrapper.flow_resize_controls();
		            		wrapper.flow_play_pause('play',wrapper[0].options);
	            		},
	            		onStart:function(clip) {
	            			// clear previous timer
				    		clearInterval(this.timer);

							var player = this,
								statustime = 0,
								wrapper = $(this.getParent()).parents('.media_wrapper');

				    		// begin timer
				    		this.timer = setInterval(function(){
				    			if(typeof(clip.duration) == 'undefined') return;
				    			
				    			var statustime = player.getStatus().time;
				    			if (typeof(statustime) == 'undefined') return;
				    			
				    			if((typeof(statustime) != 'undefined')){
				    				var duree = player.former_duration;
				    				/**
				    				 * On doit le mettre ici car on n'a pas de duration sur les mp3 dès le load
				    				 */
				    				if(!player.slider_done && slider){
				    					var replay = false;
										player.slider_control = wrapper.find('.progress_back').slider({
											min: 0,
											max: player.former_duration ? player.former_duration : 100,
											range: "min",
											start: function(event,ui){
												if(player.isPlaying())
													replay = true;
												else
													replay = false;
											},
											slide: function(event,ui){
												if(wrapper[0].options.isSound){
													if(replay)
														player.seek(ui.value);
													else{
														player.play();
														player.seek(ui.value);
													}
												}
											},
											stop: function(event,ui){
												if(replay){
													player.resume();
													player.seek(ui.value);
												}
												else{
													if(wrapper[0].options.isSound){
														player.play();
														player.seek(ui.value);
														player.pause();
													}else{
														player.play();
														player.topause = true;
														player.seek(ui.value);
													}
												}
											}
										});
										player.slider_done = true;
				    				}
					    			if (!player.isPaused()) {
					    				var timer2 = statustime / player.former_duration * 100,
											position = Math.round(timer2);
										wrapper.find('.progress_elapsed_time,.progress_back > .ui-slider-range').css('width',position+'%');
										wrapper.find('.progress_indicator,.progress_back > .ui-slider-handle').css('left',position+'%');
										wrapper.find(".elapsed_time").html(ms_second_to_time(statustime));
										if(wrapper.find(".remaining_time").is('.remaining'))
											wrapper.find(".remaining_time").html('-'+ms_second_to_time(player.former_duration-statustime));
										wrapper.flow_resize_controls();
					    			}
					    				
					    			if(!player.bufferfull){
					    				var buffer = ms_anything_to_percent(player.getStatus().bufferEnd,player.former_duration);
					    				if(buffer > 100) buffer = 100;
					    				if(buffer == 100) player.bufferfull = true;
										wrapper.find('.progress_buffered').css('width',buffer+'%');
					    			}
				    			}
				    		},100);
				    		wrapper.find(".remaining_time").unbind('click').click(function(e){
								if($(this).is('.remaining'))
									$(this).removeClass('remaining').addClass('total_time').attr('title',ms_player_lang.info_total).html(ms_second_to_time(Math.floor(player.former_duration)));
								else
									$(this).removeClass('total_time').addClass('remaining').attr('title',ms_player_lang.info_restant).html('-'+ms_second_to_time(Math.floor(player.former_duration) - statustime));
								wrapper.flow_resize_controls();
		    				});
				    	},
	            		onCuepoint:function(content) {},
	            		onMetaData:function(clip) {
	            			var wrapper = $(this.getParent()).parents('.media_wrapper'),
	            				options = wrapper[0].options;
	            			if((clip.duration != 'undefined') && (clip.duration != this.former_duration)){
		            			this.former_duration = clip.duration;
		            			var duration = ms_second_to_time(this.former_duration);
		            			if(typeof(this.slider_control) == 'object')
		            				this.slider_control.slider('option', 'max',this.former_duration);
		            			if(wrapper.find(".remaining_time").is('.remaining'))
			            			wrapper.find(".remaining_time").html('-'+duration);
								else
									wrapper.find(".remaining_time").html(duration);
		            			wrapper.find(".elapsed_time").html(ms_second_to_time(this.getStatus().time || 0));
		            		}
	            			if(options.isVideo){
	            				var ratio_video = clip.metaData.width/clip.metaData.height;
	        					wrapper[0].ratio = ratio_video;
	            				if(options.movieSize == 'adapt' && !wrapper.hasClass('noresize') && (options.movieSize != 'noresize')){
	        						/**
	        						 * En mode adapt :
	        						 * - on dimensionne la largeur à 100%
	        						 * - on dimensionne la hauteur à un ratio correspondant au ratio réel de la vidéo 
	        						 * par rapport à la largeur du bloc parent
	        						 */
	        						width_container = wrapper.parent().width();
	        						var ratio = (width_container/clip.metaData.width),
	        							height_final = (clip.metaData.height*ratio).toFixed();
	        						wrapper.animate({height:height_final+'px',width:'100%'},500,function(){
	        							wrapper.flow_resize_controls();
	        						});
	        						var handler_media_resize = function(){
        								wrapper.css({width:'auto'}).css({height:(wrapper.parent().width()/wrapper[0].ratio)+'px'}).flow_resize_controls();
	        						}
	        						$(window).unbind('resize',handler_media_resize).bind('resize',handler_media_resize);
	        					}else if(!wrapper.hasClass('noresize') || options.movieSize != 'noresize'){
	        						/**
	        						 * En mode normal, on redimentionne la hauteur de la vidéo en fonction 
	        						 * du ratio réel récupéré des métadonnées
	        						 */
	        						wrapper.css({'height':(wrapper.width()/wrapper[0].ratio)+'px'});
	        					}
	            			}
	            			wrapper.flow_resize_controls();
			            },
			            onLastSecond:function(){},
			            onBeforeFinish: function(clip) {},
	            		onFinish:function(clip){
	            			clearInterval(this.timer);
	            			if(clip.looped) {this.play(clip);}
	            			else{
	            				var wrapper = $(this.getParent()).parents('.media_wrapper');
	            				var duration = ms_second_to_time(this.former_duration);
	            				wrapper.find(".remaining_time").html(duration);
	            				wrapper.find(".elapsed_time").html('00:00');
	            				if(typeof(this.slider_control) == 'object')
			            			this.slider_control.slider('value', 0);
	            				wrapper.flow_play_pause('stop',wrapper[0].options).flow_resize_controls();
	            			}
		            	},
			            onPause:function(clip){
			            	$(this.getParent()).parents('.media_wrapper').flow_play_pause('pause',wrapper[0].options);
		            	},
		            	onResume:function(clip){
		            		$(this.getParent()).parents('.media_wrapper').flow_play_pause('play',wrapper[0].options).flow_resize_controls();
		            	},
		            	onBeforeSeek:function(clip,time){
		            		this.former_time = this.getStatus().time;
		            		$(this.getParent()).parents('.media_wrapper').addClass('seeking').find('.play_pause_button').attr('title',ms_player_lang.bouton_seeking);
							if(this.isPlaying()) clip.status = 'playing';
							else if(this.isPaused()) clip.status = 'paused';
		            	},
		            	onSeek:function(clip,time){
		            		var wrapper = $(this.getParent()).parents('.media_wrapper'),
		            			time_affiche = ms_second_to_time(time),
		            			width = time/clip.duration*100;
		            		
		            		if(this.former_time < time)
		            			wrapper.ms_messages('seek_to',ms_player_lang.statut_seek_to+' '+time_affiche,wrapper[0].options);
		            		else
			            		wrapper.ms_messages('seek_to',ms_player_lang.statut_seek_back+' '+time_affiche,wrapper[0].options);
		            		if(clip.status == 'paused')
								wrapper.find('.play_pause_button').removeClass('pause').attr('title',ms_player_lang.bouton_pause);
							else
								wrapper.find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_lire);
		            		if(this.topause){
		            			this.topause = false;
		            			this.pause();
		            		}
		            		
							if(wrapper.find(".remaining_time").is('.remaining'))
								wrapper.find(".remaining_time").html('-'+ms_second_to_time(clip.duration-time));
							
							wrapper.find('.progress_elapsed_time,.progress_back > .ui-slider-range').css('width',width+'%');
							wrapper.find('.progress_indicator,.progress_back > .ui-slider-handle').css('left',width+'%');
		            		wrapper.removeClass('seeking').find(".elapsed_time").html(time_affiche).flow_resize_controls();
		            	},
		            	onStop:function(){
		            		$(this.getParent()).parents('.media_wrapper').flow_play_pause('stop',wrapper[0].options);
		            	},
		            	onUpdate:function(clip){},
		            	onBufferEmpty:function(){},
		            	onBufferFull:function(clip){},
		            	onBufferStop:function(){},
		            	onNetStreamEvent:function(){}
		            },
		            canvas:{ 
		            	background:'transparent',
		            	backgroundGradient:'none'
		     		},
		     		play: {
		     			opacity: 0
		     		},
			        plugins: {
		     			controls: null
		     		}
			    };
				
			    wrapper.find('.flowplayer').flowplayer({
			    	cachebusting: $.browser.msie,
			    	src:options.flowurl,
			    	version: [10, 0],
			    	wmode:'transparent',
			    	allowfullscreen: allowfullscreen,
			    	onFail: function() {
			    		var wrapper = $(this.getParent()).parents('.media_wrapper');
			    		wrapper.removeClass('loading').addClass('player_error').css('background-color','inherit').find('.controls').detach();
			    		wrapper.find('.html5_cover').css('background-color','#ffffff').find('.img').fadeTo('slow', 0.4);
			    		wrapper.find('.flowplayer').css('position','relative').html(' ');
			    		wrapper.ms_messages('error',wrapper[0].options.flasherror);
			    	}
			    }, media_options);
			}
			
			/**
			 * Fonction modifiant le niveau de volume au scroll sur le lecteur
			 * Cette action fonctionne au scroll au hover du lecteur.
			 * Nécessite le plugin jQuery mousewheel :
			 * http://github.com/brandonaaron/jquery-mousewheel
			 *
			 * - Bloque l'évènement normal du scroll (descendre dans la page)
			 * - Baisse le volume de 10% lors d'un scroll bas avec la souris
			 * - Augmente le volume de 10% lors d'un scroll haut avec la souris
			 * - Si le volume est "muted", l'action ne fait rien
			 */
			if(!options.volume_bloque && typeof($.fn.mousewheel) != "undefined"){
				wrapper.find('.flowplayer,.html5_cover,.flowplayer > object').mousewheel(function(event, delta) {
					event.preventDefault();
					wrapper.find('.flowplayer').flowplayer().each(function() {
						if(!this.getStatus().muted){
							var volume_new = this.getVolume() + ((delta > 0) ? 10 : -10);
							if(volume_new < 0) volume_new = 0;
							else if(volume_new > 100) volume_new = 100;
							this.setVolume(volume_new);
							wrapper.flow_change_volume(volume_new,this.slider_volume,false,options);
							if(cookies && options.cookie_volume) $.cookie('ms_volume', volume_new/100);
						}
					});
					return false;
				});
			}
		},
		flow_play_pause : function(action,options){
			if(action == 'pause'||action == 'stop'){
				$(this).addClass('paused').find('.play_pause_button').removeClass('pause').attr('title',ms_player_lang.bouton_lire);
				if(action == 'pause')
					$(this).ms_messages('pause',ms_player_lang.statut_pause,options);
			}else{
				$(this).removeClass('paused').find('.play_pause_button').addClass('pause').attr('title',ms_player_lang.bouton_pause);
				$(this).ms_messages('play',ms_player_lang.statut_play,options);
			}
			return $(this);
		},
		flow_change_volume : function(volume_new,slider_volume,mute,options){
			if(slider && slider_volume && typeof(slider_volume == 'object'))
				slider_volume.slider({value:volume_new});
			if((volume_new <= 100) && (volume_new >= 0)){
				var sound_button = $(this).find('.volume_button'),
					class_remove = sound_button.attr('class').match('volume_button_[0-9]{1,3}');
				if((volume_new <= 66) && (volume_new > 33)){
					if(class_remove != null)
						sound_button.removeClass(class_remove[0]);
					sound_button.addClass('volume_button_66');
				}else if((volume_new <= 100) && (volume_new > 66)){
					if(class_remove != null)
						sound_button.removeClass(class_remove[0]);
					sound_button.addClass('volume_button_100');
				}else if((volume_new <= 33) && (volume_new > 0)){
					if(class_remove != null)
						sound_button.removeClass(class_remove[0]);
					sound_button.addClass('volume_button_33');
				}else if(volume_new == 0){
					if(class_remove != null)
						sound_button.removeClass(class_remove[0]);
					sound_button.addClass('volume_button_0');
				}
				var volume_title = ms_player_lang.bouton_volume+' ('+volume_new+'%)';
				$(this).ms_messages('volume',volume_title,options);
				sound_button.attr('title',volume_title);
			}
			return $(this);
		},
		flow_resize_controls : function(force){
			/**
			 * Attention la série de isNaN est pour IE qui plante à ces endroits
			 */
			var progress_back = $(this).find(".progress_back"),
				progress_bar = $(this).find('.progress_bar'),
				buttons_right_width = 0;

		    $(this).find('.buttons_right').children().each(function(){
		    	if($(this).is(':visible') && $(this).css('position') != 'absolute'){
		    		buttons_right_width += isNaN(parseFloat($(this).outerWidth())) ? 0 : parseFloat($(this).outerWidth());
		    		buttons_right_width += isNaN(parseFloat($(this).css('margin-left'))) ? 0 : parseFloat($(this).css('margin-left'));
		    		buttons_right_width += isNaN(parseFloat($(this).css('margin-right'))) ? 0 : parseFloat($(this).css('margin-right'));
		    	}
		    });
		    $(this).find('.buttons_right').width(buttons_right_width);
		    
			var width_container = $(this).width(),
				buttons_left = $(this).find('.buttons_left'),
				buttons_right = $(this).find('.buttons_right'),
				remaining_time = $(this).find(".remaining_time"),
				elapsed_time = $(this).find(".elapsed_time"),
				play_width = parseFloat(buttons_left.outerWidth()),
				sound_width = parseFloat(buttons_right.outerWidth()),
				elapsed_width = parseFloat(elapsed_time.outerWidth()),
				remaining_width = 0;
			
			play_width += isNaN(parseFloat(buttons_left.css('margin-left'))) ? 0 : parseFloat(buttons_left.css('margin-left'));
			play_width += isNaN(parseFloat(buttons_left.css('margin-right'))) ? 0 : parseFloat(buttons_left.css('margin-right'));
			
			sound_width += isNaN(parseFloat(buttons_right.css('margin-left'))) ? 0 : parseFloat(buttons_right.css('margin-left'));
			sound_width += isNaN(parseFloat(buttons_right.css('margin-right'))) ? 0 : parseFloat(buttons_right.css('margin-right'));
			
			var progresswidth = parseFloat(width_container)-parseFloat(play_width)-parseFloat(sound_width);
			progresswidth -= isNaN(parseFloat(progress_bar.css('border-left-width'))) ? 0 : parseFloat(progress_bar.css('border-left-width'));
			progresswidth -= isNaN(parseFloat(progress_bar.css('border-right-width'))) ? 0 : parseFloat(progress_bar.css('border-right-width'));
			progresswidth -= isNaN(parseFloat(progress_bar.css('margin-right'))) ? 0 : parseFloat(progress_bar.css('margin-right'));
			progresswidth -= isNaN(parseFloat(progress_bar.css('margin-left'))) ? 0 : parseFloat(progress_bar.css('margin-left'));
			progresswidth -= isNaN(parseFloat(progress_bar.css('margin-left'))) ? 0 : parseFloat(progress_bar.css('padding-right'));
			progresswidth -= isNaN(parseFloat(progress_bar.css('padding-left'))) ? 0 : parseFloat(progress_bar.css('padding-left'));
			progresswidth = progresswidth - 2;
			progress_bar.width(progresswidth);

			if(remaining_time.is(':visible')){
				remaining_width += parseFloat(remaining_time.outerWidth());
				remaining_width += isNaN(parseFloat(remaining_time.css('margin-left'))) ? 0 : parseFloat(remaining_time.css('margin-left'));
				remaining_width += isNaN(parseFloat(remaining_time.css('margin-right'))) ? 0 : parseFloat(remaining_time.css('margin-right'));
				remaining_width += $.browser.msie ? 4 : 0;
			}
			
			elapsed_width += isNaN(parseFloat(elapsed_time.css('margin-left'))) ? 0 : parseFloat(elapsed_time.css('margin-left'));
			elapsed_width += isNaN(parseFloat(elapsed_time.css('margin-right'))) ? 0 : parseFloat(elapsed_time.css('margin-right'));
			
			var progressback_width = progresswidth - elapsed_width - remaining_width;
			progressback_width -= isNaN(parseFloat(progress_back.css('border-left-width'))) ? 0 : parseFloat(progress_back.css('border-left-width'));
			progressback_width -= isNaN(parseFloat(progress_back.css('border-right-width'))) ? 0 : parseFloat(progress_back.css('border-right-width'));
			progressback_width -= isNaN(parseFloat(progress_back.css('margin-right'))) ? 0 : parseFloat(progress_back.css('margin-right'));
			progressback_width -= isNaN(parseFloat(progress_back.css('margin-left'))) ? 0 : parseFloat(progress_back.css('margin-left'));
			progressback_width -= isNaN(parseFloat(progress_back.css('padding-right'))) ? 0 : parseFloat(progress_back.css('padding-right'));
			progressback_width -= isNaN(parseFloat(progress_back.css('padding-left'))) ? 0 : parseFloat(progress_back.css('padding-left'));
			
			$(this).find('.progress_back').width(progressback_width);
			if(slider && progressback_width < 30 && !force){
				var player = $(this).find('.flowplayer').flowplayer(),
					wrapper = $(this);
				player.each(function() {
					if(typeof(this.slider_volume) == 'object'){
						this.slider_volume.slider('option',{'orientation':'vertical'});
						wrapper.find('.volume_slider_container').removeClass('horizontal').addClass('vertical');
					}
				});
				wrapper.flow_resize_controls(true);
			}else{
				if(remaining_time.is(':hidden') && $(this).find('.loop_button').is(':visible') && progressback_width < 30){
					$(this).find('.loop_button').hide();
					$(this).flow_resize_controls(true);
				}
				else if(remaining_time.is(':visible') && progressback_width < 30){
					remaining_time.hide();
					$(this).flow_resize_controls(true);
				}
				else if(remaining_time.is(':hidden') && progressback_width < 30){
					$(this).find('.progress_back').hide();
					return $(this);
				}
				progress_back.width(progressback_width);
			}
			return $(this);
		}
	});

	function sm2_chercher_liens(sources,liens){
		sources.each(function(){
			var source = $(this),
				sURL = source.attr('src');
			if($.inArray(sURL,liens)<0)
				liens.push(sURL);
		});
		return liens;
	}

})(jQuery);