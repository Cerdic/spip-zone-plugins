/**
 * HTML5 to fallback flash
 *
 * Copyright (c) 2010 kent1
 * Licensed under the GNU GPL v3 licence
 *
 * $version: 0.1.2
 */
(function($){
	var slider = false;
	var timer = false;
	
	$.extend($.fn, {
		mediaspip_fallback_flash : function(options) {
			if((typeof($.ui) == 'object') && (typeof($.ui.slider) == 'function')){
				slider = true;
			}
			var defaults = {
				sources : $('source[type="video/x-flv"],source[type="video/mp4"],source[type="application/mp4"],source[type="audio/mpeg"]'),
				flowurl : "../flash/flowplayer.swf",
				autoload: false,
				autoplay: false,
				movieSize : 'adapt',
				bgcolor: '#000000',
				wmode: 'transparent',
				volume: 100,
				width:null,
				height:null,
				poster:null,
				cookie_volume: false
			};
			
			var $this = $(this);
			if($this.find('audio').size() >0){
				var isSound = true;
				var isVideo = false;
				defaults.volume_slider_orientation = 'vertical';
			}else{
				var isVideo = true;
				var isSound = false;
				defaults.volume_slider_orientation = 'horizontal';
			}
			
			options = $.extend(defaults, options);
			var liens = [];
			liens = sm2_chercher_liens(options.sources,liens);
			if(liens.length>0){
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
				var lecteur = '<div class="media_wrapper loading"'+(style ? styles : '') +'>';
					if(options.poster){
						lecteur +='<div class="html5_cover"><img src="'+options.poster+'" /></div>';
					}
					lecteur +='<div class="flowplayer"'+(style ? styles : '') +'></div>';
					lecteur +='<div class="controls small">'
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
						+'<span class="volume_button" title="'+mediaspip_player_lang.bouton_volume+' ('+options.volume+'%)"></span>';
					/**
					 * Si on a les sliders, on ajoute une div ici pour avoir un slider de volume
					 */
					if(slider){
						lecteur += '<span class="volume_slider_container '+options.volume_slider_orientation+'"><span class="volume_slider"></span></span>';
					}
					lecteur +='</div>'
					+ '</div>';
	
				$this.html(lecteur);
			    
			    var controls = $this.find('.controls');
			    var wrapper = $this.find('.media_wrapper');
			    
			    if(options.poster && isSound){
			    	var width = wrapper.find('.html5_cover img').width();
			    	var height = wrapper.find('.html5_cover img').height();
			    	wrapper.height(height).width(width);
			    	wrapper.find('.flowplayer').height(height).width(width);
			    }
			    
			    if(isSound){
			    	var allowfullscreen = false;
			    }else{
			    	var allowfullscreen = true;
			    }
			    var resized = false;
			    var media_options = {
			        clip:{
			    		url:liens[0],
			            autoPlay:false,
			            scaling:'fit',
			            autoBuffering:options.autoload,
			            bufferLength:5,
			            onBeforeBegin:function(){
		            	},
		            	onBeforeLoad:function(){
		            	},
			            onBegin:function(clip){
		            		if(typeof(clip.duration) != 'undefined'){
			            		var duration = mediaspip_second_to_time(clip.duration);
			            		if(wrapper.find(".remaining_time").is('.remaining')){
			            			wrapper.find(".remaining_time").html('-'+duration);
								}else{
									wrapper.find(".remaining_time").html(duration);
								}
								$this.find(".elapsed_time").html(mediaspip_second_to_time(0));
								wrapper.parent().progress_resize();
		            		}
	            		},
	            		onCuepoint:function(content) {
	            		},
	            		onMetaData:function(clip) {
	            			if(isVideo){
			            		var video_width = clip.metaData.width;
			            		var video_height = clip.metaData.height;
			            		var parent_width = wrapper.parent().parent().width()
			            		var ratio = parent_width/video_width;
			            		var parent_height = video_height*ratio;
			            		wrapper.css({height:parent_height.toFixed()+'px',width:parent_width+'px'});
			            		wrapper.find('.flowplayer').height(parent_height.toFixed()).width(parent_width);
			            		wrapper.parent().progress_resize();
	            			}
		            		if(typeof(clip.duration) != 'undefined'){
			            		var duration = mediaspip_second_to_time(clip.duration);
			            		if(wrapper.find(".remaining_time").is('.remaining')){
			            			wrapper.find(".remaining_time").html('-'+duration);
								}else{
									wrapper.find(".remaining_time").html(duration);
								}
								$this.find(".elapsed_time").html(mediaspip_second_to_time(0));
								this.former_duration = clip.duration;
								wrapper.parent().progress_resize();
		            		}
			            },
	            		onFinish:function(){
	            			wrapper.flow_play_pause('stop');
		            	},
		            	onLastSecond:function(){
		            	},
			            onPause:function(clip){
			            	wrapper.flow_play_pause('pause');
		            	},
		            	onResume:function(clip){
		            		if((clip.duration != 'undefined') && (clip.duration != this.former_duration)){
		            			this.former_duration = clip.duration;
		            			var duration = mediaspip_second_to_time(this.former_duration);
		            			if(typeof(this.slider_control) == 'object')
		            				this.slider_control.slider('option', 'max',this.former_duration);
		            			if(wrapper.find(".remaining_time").is('.remaining')){
			            			wrapper.find(".remaining_time").html('-'+duration);
								}else{
									wrapper.find(".remaining_time").html(duration);
								}
		            			wrapper.find(".elapsed_time").html(mediaspip_second_to_time(this.getStatus().time || 0));
								wrapper.parent().progress_resize();
		            		}
		            		wrapper.flow_play_pause('play');
		            	},
		            	onSeek:function(){
		            	},
		            	onStop:function(){
		            		wrapper.flow_play_pause('stop');
		            	},
		            	onUpdate:function(clip){
		            	},
		            	onBufferEmpty:function(){
		            	},
		            	onBufferFull:function(clip){
		            	},
		            	onBufferStop:function(){
		            	},
		            	onNetStreamEvent:function(){
		            	}
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
			    $this.find('.flowplayer').flowplayer({
			    	cachebusting: $.browser.msie,
			    	src:options.flowurl,
			    	version: [10, 0],
			    	wmode:'transparent',
			    	allowfullscreen: allowfullscreen,
			    	onFail: function() {
			    		wrapper.find('.controls').detach();
			    		wrapper.css('background-color','#ffffff');
			    		wrapper.find('.html5_cover').css('background-color','#ffffff');
			    		wrapper.find('.html5_cover img').fadeTo('slow', 0.4);
			    		wrapper.find('.flowplayer').html(options.flasherror);
			    	}
			    }, media_options);
			    
			    wrapper.parent().progress_resize();
			    var timer = null;
			    var bufferfull = null;
			    wrapper.find('.flowplayer').flowplayer().each(function() {
			    	var duration = false;
			    	this.onLoad(function(clip) {
			    		var player = this;
						wrapper.removeClass('loading').addClass('paused');
						controls.find('.play_pause_button').attr('title',mediaspip_player_lang.bouton_lire);
						controls.find('.play_pause_button').click(function(e){
							e.preventDefault();
							if (player.isLoaded()) {
								player.toggle();	
							} else {
								player.play();	
							}
						});
						if(isSound){
							wrapper.find('.flowplayer').click(function(){
								if (player.isLoaded()) {
									player.toggle();		
								} else {
									player.play();	
								}
							});
						}
			    		controls.find('.volume_button').click(function(e){
			    			var status = player.getStatus();
							if(status.muted){
								if(options.cookie_volume)
									$.cookie('mediaspip_volume_muted',null);
								if(typeof(player.slider_volume) == 'object')
									player.slider_volume.slider('enable');
								var volume_title = mediaspip_player_lang.bouton_volume+' ('+Math.floor(player.getVolume())+'%)';
								controls.find('.volume_button').removeClass('muted').attr('title',volume_title);
								player.unmute();
							}else{
								if(options.cookie_volume)
									$.cookie('mediaspip_volume_muted','muted');
								if(typeof(player.slider_volume) == 'object')
									player.slider_volume.slider('disable');
								controls.find('.volume_button').addClass('muted').attr('title',mediaspip_player_lang.bouton_volume_muted);
								player.mute();
							}
						});
			    	});
			    	this.onStart(function(clip) {
			    		var player = this;
			    		var status_start = player.getStatus();
			    		if(slider){
							controls.find('.progress_indicator').hide();
							player.slider_volume = controls.find('.volume_slider').slider({
								value:100,
								orientation: options.volume_slider_orientation,
								min:0,
								max:100,
								range: "min",
								slide: function(event,ui){
									/**
									 * On change le volume
									 */
									var volume_new = ui.value;
									if((volume_new <= 100) && (volume_new >= 0)){
										player.setVolume(volume_new);
										wrapper.flow_change_volume(volume_new,player.slider_volume);
										if(options.cookie_volume){
											$.cookie('mediaspip_volume', volume_new/100);
										}
									}
								},
								stop: function(event,ui){
									/**
									 * On change le volume et on le sauvegarde dans le cookie si nécessaire
									 */
									var volume_new = ui.value;
									if((volume_new <= 100) && (volume_new >= 0)){
										player.setVolume(volume_new);
										wrapper.flow_change_volume(volume_new,player.slider_volume);
										if(options.cookie_volume){
											$.cookie('mediaspip_volume', volume_new/100);
										}
									}
									
								}
							});
			    		}
						
						$this.progress_resize();
			    		
			    		bufferfull = false;
			    		var statustime = 0;
			    		player.slider_done = false;
	
			    		// clear previous timer
			    		clearInterval(timer);
	
			    		// begin timer
			    		timer = setInterval(function(){
			    			if(typeof(clip.duration) == 'undefined'){
			    				player.play();
			    				player.pause();
			    			}
			    			
			    			var status = player.getStatus();
			    			if (typeof(status.time) == 'undefined') {
			    				clearInterval(timer);
			    				return;
			    			}
			    			
			    			statustime = status.time;
			    			if((typeof(statustime) != 'undefined')){
			    				var duree = player.former_duration;
			    				/**
			    				 * On doit le mettre ici car on n'a pas de duration sur les mp3 dès le load
			    				 */
			    				if(!player.slider_done && slider){
			    					var replay = false;
									this.slider_control = controls.find('.progress_back').slider({
										min: 0,
										max: player.former_duration ? player.former_duration : 100,
										range: "min",
										start: function(event,ui){
											if(player.isPlaying()){
												player.pause();
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
											var width = ui.value/player.former_duration*100;
											controls.find('.progress_elapsed_time').css('width',width+'%');
											var percent = width;
											wrapper.find(".elapsed_time").html(mediaspip_second_to_time(ui.value));
											if(wrapper.find(".remaining_time").is('.remaining')){
												wrapper.find(".remaining_time").html('-'+mediaspip_second_to_time(player.former_duration-ui.value));
											}
											player.seek(ui.value);
											wrapper.progress_resize();
										},
										stop: function(event,ui){
											/**
											 * On saute la lecture au bon endroit ?
											 */
											var temps = ui.value;
											wrapper.find(".elapsed_time").html(mediaspip_second_to_time(temps));
											if(wrapper.find(".remaining_time").is('.remaining')){
												wrapper.find(".remaining_time").html('-'+mediaspip_second_to_time(player.former_duration-temps));
											}
											player.seek(temps);
											if(replay){
												player.resume();
											}
											wrapper.progress_resize();
										}
									});
									player.slider_done = true;
			    				}
			    				if(wrapper.find(".remaining_time").html() == '-00:00'){
									wrapper.parent().progress_resize();
			    				}
				    			if (!player.isPaused()) {
				    				var timer2 = status.time / player.former_duration * 100;
									var position = Math.round(timer2);
									wrapper.find('.progress_elapsed_time,.progress_back > .ui-slider-range').css('width',position+'%');
									wrapper.find('.progress_indicator,.progress_back > .ui-slider-handle').css('left',position+'%');
									wrapper.find(".elapsed_time").html(mediaspip_second_to_time(status.time));
									if($this.find(".remaining_time").is('.remaining')){
										wrapper.find(".remaining_time").html('-'+mediaspip_second_to_time(player.former_duration-status.time));
									}
									wrapper.parent().progress_resize();
				    			}
				    			if(!bufferfull){
				    				var buffer = mediaspip_anything_to_percent(status.bufferEnd,player.former_duration);
				    				if(buffer > 100)
				    					buffer = 100;
				    				if(buffer == 100)
				    					bufferfull = true;
									wrapper.find('.progress_buffered').css('width',buffer+'%');
				    			}
			    			}
			    		}, 500);
			    		wrapper.find(".remaining_time").unbind('click').click(function(e){
							if($(this).is('.remaining')){
								$(this)
									.removeClass('remaining')
									.addClass('total_time')
									.attr('title',mediaspip_player_lang.info_total)
									.html(mediaspip_second_to_time(Math.floor(player.former_duration)));
							}else{
								$(this)
									.removeClass('total_time')
									.addClass('remaining')
									.attr('title',mediaspip_player_lang.info_restant)
									.html('-'+mediaspip_second_to_time(Math.floor(player.former_duration) - statustime));
							}
							$this.progress_resize();
	    				});
			    	});
			    	this.onFinish(function(clip) {		
			    		clearInterval(timer);	
			    	});
			    	this.onError(function(error){
	
			    	});
			    });
			    
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
			if(typeof($.fn.mousewheel) != "undefined"){
				wrapper.find('.flowplayer,.html5_cover,.flowplayer > object').mousewheel(function(event, delta) {
					event.preventDefault();
					wrapper.find('.flowplayer').flowplayer().each(function() {
						var status = this.getStatus();
						if(!status.muted){
							var volume = this.getVolume();
							var volume_diff = (delta > 0) ? 10 : -10;
							var volume_new = volume + volume_diff;
							if(volume_new < 0)
								volume_new = 0;
							if(volume_new > 100)
								volume_new = 100;
							this.setVolume(volume_new);
							wrapper.flow_change_volume(volume_new,this.slider_volume);
							if(options.cookie_volume && (typeof($.fn.cookie) != "undefined")){
								$.cookie('mediaspip_volume', volume_new/100);
							}
						}
					});
				});
			}
		},
		flow_play_pause : function(action){
			if(action == 'pause'||action == 'stop'){
				$(this).addClass('paused');
				$(this).find('.play_pause_button').removeClass('pause').attr('title',mediaspip_player_lang.bouton_lire);
			}else{
				$(this).removeClass('paused');
				$(this).find('.play_pause_button').addClass('pause').attr('title',mediaspip_player_lang.bouton_pause);
			}
		},
		flow_change_volume : function(volume_new,slider_volume,mute){
			if(slider && typeof(slider_volume == 'object')){
				slider_volume.slider({value:volume_new});
			}
			if((volume_new <= 100) && (volume_new >= 0)){
				var sound_button = $(this).find('.volume_button');
				var class_remove = sound_button.attr('class').match('volume_button_[0-9]{1,3}');
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
				var sound_title = mediaspip_player_lang.bouton_volume+' ('+volume_new+'%)';
				sound_button.attr('title',sound_title);
			}
		},
		progress_resize : function(){
			/**
			 * Attention la série de isNaN est pour IE qui plante à ces endroits
			 */
			var buttons_right_width = 5;
		    $('.buttons_right').children().each(function(){
		    	if($(this).css('position') != 'absolute'){
		    		if(!isNaN(parseFloat($(this).outerWidth())))
		    			buttons_right_width =  parseFloat(buttons_right_width)+parseFloat($(this).outerWidth());
		    		if(!isNaN(parseFloat($(this).css('margin-left'))))
		    			buttons_right_width = parseFloat(buttons_right_width) + parseFloat($(this).css('margin-left'));
		    		if(!isNaN(parseFloat($(this).css('margin-right'))))
		    			buttons_right_width = parseFloat(buttons_right_width) + parseFloat($(this).css('margin-right'));
		    	}
		    });
		    $('.buttons_right').width(buttons_right_width);
			var width_container = $(this).find('.media_wrapper').width();
			
			var play_width = parseFloat($(this).find('.buttons_left').outerWidth())+2;
			if(!isNaN(parseFloat($(this).find('.buttons_left').css('margin-left'))))
				play_width += parseFloat($(this).find('.buttons_left').css('margin-left'));
			if(!isNaN(parseFloat($(this).find('.buttons_left').css('margin-right'))))
				play_width += parseFloat($(this).find('.buttons_left').css('margin-right'));
			
			var sound_width = parseFloat($(this).find('.buttons_right').outerWidth())+2;
			if(!isNaN(parseFloat($(this).find('.buttons_right').css('margin-left'))))
				sound_width += parseFloat($(this).find('.buttons_right').css('margin-left'));
			if(!isNaN(parseFloat($(this).find('.buttons_left').css('margin-right'))))
				sound_width += parseFloat($(this).find('.buttons_right').css('margin-right'));
			
			var progresswidth = parseFloat(width_container)-parseFloat(play_width)-parseFloat(sound_width);
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('border-left-width'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('border-left-width'));
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('border-right-width'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('border-right-width'));
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('margin-right'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('margin-right'));
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('margin-left'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('margin-left'));
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('margin-left'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('padding-right'));
			if(!isNaN(parseFloat($(this).find('.progress_bar').css('padding-left'))))
				progresswidth -= parseFloat($(this).find('.progress_bar').css('padding-left'));
			$(this).find('.progress_bar').width(progresswidth);

			var remaining_width = parseFloat($(this).find(".remaining_time").outerWidth())+2;
			if(!isNaN(parseFloat($(this).find(".remaining_time").css('margin-left'))))
				remaining_width += parseFloat($(this).find(".remaining_time").css('margin-left'));
			if(!isNaN(parseFloat($(this).find(".remaining_time").css('margin-right'))))
				remaining_width += parseFloat($(this).find(".remaining_time").css('margin-right'));
			
			var elapsed_width = parseFloat($(this).find(".elapsed_time").outerWidth())+2;
			if(!isNaN(parseFloat($(this).find(".elapsed_time").css('margin-left'))))
				elapsed_width += parseFloat($(this).find(".elapsed_time").css('margin-left'));
			if(!isNaN(parseFloat($(this).find(".elapsed_time").css('margin-right'))))
				elapsed_width += parseFloat($(this).find(".elapsed_time").css('margin-right'));
			
			var progressback_width = progresswidth - elapsed_width - remaining_width;
			if(!isNaN(parseFloat($(this).find(".progress_back").css('border-left-width'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('border-left-width'));
			if(!isNaN(parseFloat($(this).find(".progress_back").css('border-right-width'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('border-right-width'));
			if(!isNaN(parseFloat($(this).find(".progress_back").css('margin-right'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('margin-right'));
			if(!isNaN(parseFloat($(this).find(".progress_back").css('margin-left'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('margin-left'));
			if(!isNaN(parseFloat($(this).find(".progress_back").css('padding-right'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('padding-right'));
			if(!isNaN(parseFloat($(this).find(".progress_back").css('padding-left'))))
				progressback_width -= parseFloat($(this).find(".progress_back").css('padding-left'));
			$(this).find('.progress_back').width(progressback_width);
		}
	});

	function sm2_chercher_liens(sources,liens){
		sources.each(function(){
			var $source = $(this);
			var sURL = $source.attr('src');
			if($.inArray(sURL,liens)<0) {
				liens.push(sURL);
			}
		});
		return liens;
	}

})(jQuery);