$(function() {

	var lastSound = '' ;

	/*	*/
	// chopper les liens
	$("a[rel='enclosure'][href$=mp3]").each(
		function(i) {	 
			$(this).attr('data-soundId',i);
			// longueur des bare de progression
			var bloc_audio = $(this).parents(".audio");
			bloc_detail_l = bloc_audio.find(".file").width() + bloc_audio.find(".duration").width() + bloc_audio.find(".time").width();
	});
	
	
	// animation des boutons
		
	$("button.play").html("<span class='ui-icon-play'>play</span>")
	.click(function() {
		$(".playing").removeClass("playing");
		
		if ( $( this ).text() === "play" ) {
		
			$( this ).html("<span class='ui-icon-pause'>pause</span>");
		} else {
			$( this ).html("<span class='ui-icon-play'>play</span>");
		}
		
		var media_index = $(this).index();
		var parent_track =  $(this).parents(".audio").eq(0) ;
		var lienMp3 = parent_track.addClass("playing").find("a[rel='enclosure']") ;
		var soundURL = lienMp3.attr('href');
	    var soundId = "media_" + lienMp3.attr('data-soundId');
	    var thisSound = soundManager.getSoundById(soundId);
	  		  		  	 
	    if (thisSound) {

	      // already exists
	      if (thisSound == lastSound) {
	        // and was playing (or paused)
	        thisSound.togglePause();
	      } else {
	        // different sound
	        thisSound.togglePause(); // start playing current

	        if (lastSound) {
	        	soundManager.stop(lastSound.sID);
    			soundManager.unload(lastSound.sID);
	        }
	      }
	    } else {

	    	// create sound

	    	thisSound = soundManager.createSound({

	    		id:soundId,
	    		url:soundURL,
	    		onplay:function(){
	    			$("." + this.sID +" .position .ui-progressbar-value").eq(1).css('background','#900000');
	    		},
	    		whileloading:function(){
					var timer = this.bytesLoaded / this.bytesTotal * 100 ;
					var minutes = Math.floor(this.durationEstimate / 1000 / 60) ;
					var secondes = Math.floor((this.durationEstimate - minutes*1000*60) /1000);
					
					if(secondes < 10) secondes = "0" + secondes ;
					if(minutes < 10) minutes = "0" + minutes ;
										
					$("." + this.sID +" .duration").html("/ " + minutes + ":" + secondes);
					
					$("." + this.sID +" .loading").css({width:Math.round(timer) +"%"});
				},
	    		whileplaying:function(){
					var minutes = Math.floor(this.position / 1000 / 60) ;
					var secondes = Math.floor((this.position - minutes*1000*60) /1000);
					if(secondes < 10) secondes = "0" + secondes ;
					if(minutes < 10) minutes = "0" + minutes ;
					
					var timer = this.position / this.durationEstimate * 100 ;
					$("." + this.sID +" .position").css({width:Math.round(timer) +"%"}); 
				
					$("." + this.sID +" .time").html(minutes + ":" + secondes);
				}
	    	});

			if (lastSound) {
	        	soundManager.stop(lastSound.sID);
    			soundManager.unload(lastSound.sID);
	        }	      
	        
	       	$(parent_track).addClass(thisSound.id)

	        thisSound.play();
			
	      	// stop last sound
	      	
	      	// deplacer le son
			$('.playing .loading').click(function(e){
				e.preventDefault();
				var duree = thisSound.durationEstimate;
				var offset = $(".playing .loading").offset();
				var x = Math.round((e.pageX - offset.left) / $(".playing .loading").width() * 100);
				var temps = Math.round(duree * x / 100);
				/* $(".playing .position").progressbar("value",Math.round(x)); */
				if(thisSound.playState == 0){
					soundManager.play(thisSound);
					thisSound.setPosition(temps);
				}else{
					thisSound.setPosition(temps);
				}
			});	      

	    }	
	});
	
});
