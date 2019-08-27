/**
 * @file MediaElement Playlist Feature (plugin).
 * @author Andrew Berezovsky <andrew.berezovsky@gmail.com>
 * Twitter handle: duozersk
 * @author Original author: Junaid Qadir Baloch <shekhanzai.baloch@gmail.com>
 * Twitter handle: jeykeu
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * forked from https://github.com/duozersk/mep-feature-playlist
 * adapted for MEJS 4.2.7
 */

(function($) {
  $.extend(mejs.MepDefaults, {
    loopText: 'Repeat On/Off',
    shuffleText: 'Shuffle On/Off',
    nextText: 'Next Track',
    prevText: 'Previous Track',
    playlistText: 'Show/Hide Playlist'
  });

  $.extend(MediaElementPlayer.prototype, {
    // LOOP TOGGLE
    buildloop: function(player, controls, layers, media) {
      var t = this;

      var loop = $('<div class="mejs__button mejs__loop-button ' + ((player.options.loop) ? t.options.classPrefix + 'loop-on' : t.options.classPrefix + 'loop-off') + '">' +
        '<button type="button" aria-controls="' + player.id + '" title="' + player.options.loopText + '"></button>' +
        '</div>')
        // append it to the toolbar
        .appendTo(controls)
        // add a click toggle event
        .click(function(e) {
          player.options.loop = !player.options.loop;
          $(media).trigger('mep-looptoggle', [player.options.loop]);
          if (player.options.loop) {
            loop.removeClass(t.options.classPrefix + 'loop-off').addClass(t.options.classPrefix + 'loop-on');
            //media.setAttribute('loop', 'loop');
          }
          else {
            loop.removeClass(t.options.classPrefix + 'loop-on').addClass(t.options.classPrefix + 'loop-off');
            //media.removeAttribute('loop');
          }
        });

      t.loopToggle = $(t.controls).find('.' + t.options.classPrefix + 'loop-button');
    },
    loopToggleClick: function() {
      var t = this;
      t.loopToggle.trigger('click');
    },
    // SHUFFLE TOGGLE
    buildshuffle: function(player, controls, layers, media) {
      var t = this;

      var shuffle = $('<div class="mejs__button mejs__shuffle-button ' + ((player.options.shuffle) ? t.options.classPrefix + 'shuffle-on' : t.options.classPrefix + 'shuffle-off') + '">' +
        '<button type="button" aria-controls="' + player.id + '" title="' + player.options.shuffleText + '"></button>' +
        '</div>')
        // append it to the toolbar
        .appendTo(controls)
        // add a click toggle event
        .click(function(e) {
          player.options.shuffle = !player.options.shuffle;
          $(media).trigger('mep-shuffletoggle', [player.options.shuffle]);
          if (player.options.shuffle) {
            shuffle.removeClass(t.options.classPrefix + 'shuffle-off').addClass(t.options.classPrefix + 'shuffle-on');
          }
          else {
            shuffle.removeClass(t.options.classPrefix + 'shuffle-on').addClass(t.options.classPrefix + 'shuffle-off');
          }
        });

      t.shuffleToggle = $(t.controls).find('.' + t.options.classPrefix + 'shuffle-button');
    },
    shuffleToggleClick: function() {
      var t = this;
      t.shuffleToggle.trigger('click');
    },
    // PREVIOUS TRACK BUTTON
    buildprevtrack: function(player, controls, layers, media) {
      var t = this;

      var prevTrack = $('<div class="mejs__button mejs__prevtrack-button mejs__prevtrack">' +
        '<button type="button" aria-controls="' + player.id + '" title="' + player.options.prevText + '"></button>' +
        '</div>')
        .appendTo(controls)
        .click(function(e){
          $(media).trigger('mep-playprevtrack');
          player.playPrevTrack();
        });

      t.prevTrack = $(t.controls).find('.' + t.options.classPrefix + 'prevtrack-button');
    },
    prevTrackClick: function() {
      var t = this;
      t.prevTrack.trigger('click');
    },
    // NEXT TRACK BUTTON
    buildnexttrack: function(player, controls, layers, media) {
      var t = this;

      var nextTrack = $('<div class="mejs__button mejs__nexttrack-button mejs__nexttrack">' +
        '<button type="button" aria-controls="' + player.id + '" title="' + player.options.nextText + '"></button>' +
        '</div>')
        .appendTo(controls)
        .click(function(e){
          $(media).trigger('mep-playnexttrack');
          player.playNextTrack();
        });

      t.nextTrack = $(t.controls).find('.' + t.options.classPrefix + 'nexttrack-button');
    },
    nextTrackClick: function() {
      var t = this;
      t.nextTrack.trigger('click');
    },
    // PLAYLIST TOGGLE
    buildplaylist: function(player, controls, layers, media) {
      var t = this;
	    player.options.playlist = $(player.options.playlistSelector).is(':visible');

      var playlistToggle = $('<div class="mejs__button mejs__playlist-button ' + ((player.options.playlist) ? t.options.classPrefix + 'hide-playlist' : t.options.classPrefix + 'show-playlist') + '">' +
        '<button type="button" aria-controls="' + player.id + '" title="' + player.options.playlistText + '"></button>' +
        '</div>')
        .appendTo(controls)
        .click(function(e) {
          player.options.playlist = !player.options.playlist;
          $(media).trigger('mep-playlisttoggle', [player.options.playlist]);
          if (player.options.playlist) {
            $(player.options.playlistSelector).show('fast');
            playlistToggle.removeClass(t.options.classPrefix + 'show-playlist').addClass(t.options.classPrefix + 'hide-playlist');
          }
          else {
	          $(player.options.playlistSelector).hide('fast');
            playlistToggle.removeClass(t.options.classPrefix + 'hide-playlist').addClass(t.options.classPrefix + 'show-playlist');
          }
        });

      t.playlistToggle = $(t.controls).find('.' + t.options.classPrefix + 'playlist-button');
    },
    playlistToggleClick: function() {
      var t = this;
      t.playlistToggle.trigger('click');
    },
	  playlist_tracks:{},
    // PLAYLIST WINDOW
    buildplaylistfeature: function(player, controls, layers, media) {
	    var t = this;
      var getTrackName = function(trackUrl) {
        var trackUrlParts = trackUrl.split("/");
        if (trackUrlParts.length > 0) {
          return decodeURIComponent(trackUrlParts[trackUrlParts.length-1]);
        }
        else {
          return '';
        }
      };

      // calculate playlist_tracks and build playlist
      this.playlist_tracks = $(player.options.playlistSelector).find('.track[data-url]');
      //$(media).children('source').each(function(index, element) { // doesn't work in Opera 12.12
	    this.playlist_tracks.each(function(index, element) {
		    var me = $(this);
	      if (!$.trim(me.attr('title'))){
		      me.attr('title',getTrackName(me.attr('data-url')));
	      }
		    me.attr('data-index',index);
		    $('<div class="mejs__controls"><div class="mejs__button mejs__playpause-button mejs__play" >' +
							'<button type="button" aria-controls="' + t.id + '" title="' + t.options.playpauseText + '" aria-label="' + t.options.playpauseText + '"></button>' +
						'</div></div>')
          .prependTo(me)
          // play track from playlist when clicking the button
			    .click(function(e){
				    e.preventDefault();
				    var track = $(this).closest('.track');
				    if (!track.hasClass('current')) {
		          track.addClass('played');
		          player.playTrack(track);
		        }
				    else {
							if (media.paused) {
								media.play();
								track.addClass('playing').removeClass('paused');
								$('.' + t.options.classPrefix + 'play',this).addClass(t.options.classPrefix + 'pause').removeClass(t.options.classPrefix + 'play');
							} else {
								media.pause();
								track.addClass('paused').removeClass('playing');
								$('.' + t.options.classPrefix + 'pause',this).addClass(t.options.classPrefix + 'play').removeClass(t.options.classPrefix + 'pause');
							}
				    }
				    $(this).find('button').focus();
				    return false;
			    });

		    $(this).prepend();
      });

      // set the first track as current
	    t.playlist_tracks.eq(0).addClass('current played').siblings().removeClass('current');

      // when current track ends - play the next one
      media.addEventListener('ended', function(e) {
	      t.playNextTrack();
      }, false);
	    media.addEventListener('play',function(e) {
		    t.playlist_tracks.filter('.current').find('.' + t.options.classPrefix + 'play').addClass(t.options.classPrefix + 'pause').removeClass(t.options.classPrefix + 'play');
      }, false);
      media.addEventListener('pause',function(e) {
	      t.playlist_tracks.filter('.current').find('.' + t.options.classPrefix + 'pause').addClass(t.options.classPrefix + 'play').removeClass(t.options.classPrefix + 'pause');
      }, false);

	    // add key features for prev/next track
	    player.options.keyActions.push({
	  						keys: [34], // PageDown
	  						action: function(player, media) {
								  t.playNextTrack();
	  						}
	  				});
	    player.options.keyActions.push({
	  						keys: [33], // PageUp
	  						action: function(player, media) {
								  t.playPrevTrack();
	  						}
	  				});


    },
    playNextTrack: function() {
      var t = this;
      var current = t.playlist_tracks.filter('.current');
      var notplayed = t.playlist_tracks.not('.played');
      if (notplayed.length < 1) {
	      t.playlist_tracks.removeClass('played');
        notplayed = t.playlist_tracks.not('.current');
      }
	    var nxt;
      if (t.options.shuffle) {
        var random = Math.floor(Math.random()*notplayed.length);
        nxt = notplayed.eq(random);
      }
      else {
        nxt = parseInt(current.attr('data-index'))+1;
	      nxt = t.playlist_tracks.eq(nxt);
        if (nxt.length < 1 && t.options.loop) {
          nxt = t.playlist_tracks.eq(0);
        }
      }
      if (nxt.length == 1) {
        nxt.addClass('played');
        t.playTrack(nxt);
      }
    },
    playPrevTrack: function() {
      var t = this;
      var current = t.playlist_tracks.filter('.current');
      var played = t.playlist_tracks.filter('.played').not('.current');
      if (played.length < 1) {
	      t.playlist_tracks.removeClass('played');
        played = t.playlist_tracks.not('.current');
      }
	    var prev;
      if (t.options.shuffle) {
        var random = Math.floor(Math.random()*played.length);
        prev = played.eq(random);
      }
      else {
        prev = parseInt(current.attr('data-index'))-1;
	      prev = t.playlist_tracks.eq(prev);
        if (prev.length < 1 && t.options.loop) {
          prev = t.playlist_tracks.last();
        }
      }
      if (prev.length == 1) {
        current.removeClass('played');
        t.playTrack(prev);
      }
    },
    playTrack: function(track) {
      var t = this;
	    t.options.duration=0;
      t.pause();
      t.setSrc(track.attr('data-url'));
      t.load();
      setTimeout(function(){t.media.play();},10);
      track.addClass('current playing').siblings().removeClass('current').removeClass('playing').removeClass('paused');
	    t.playlist_tracks.find('.' + t.options.classPrefix + 'pause').addClass(t.options.classPrefix + 'play').removeClass(t.options.classPrefix + 'pause');
	    $('.' + t.options.classPrefix + 'play',track).addClass(t.options.classPrefix + 'pause').removeClass(t.options.classPrefix + 'play');

    },
    playTrackURL: function(url) {
      var t = this;
      var track = t.playlist_tracks.filter('[data-url="'+url+'"]');
      t.playTrack(track);
    }
  });

})(mejs.$);
