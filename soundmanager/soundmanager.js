$(document).ready(function(){
	soundManager.setup({
	  url: '/plugins/soundmanager/swf/',
	  flashVersion: 9, // optional: shiny features (default = 8)
	  useFlashBlock: false, // optionally, enable when you're ready to dive in
	  debugMode: false,
	  /**
	   * read up on HTML5 audio support, if you're feeling adventurous.
	   * iPad/iPhone and devices without flash installed will always attempt to use it.
	   */
	  onready: function() {
	    // Ready to use; soundManager.createSound() etc. can now be called.
		lastSound = soundManager.createSound({id:'Hello',url:''});
		sound_manager_init();
	  }
	});
});


function sound_manager_init(){

	// indexer les liens mp3
	$("a[rel='enclosure'][href$=mp3]").each(
		function(i) {	 
			$(this).attr('data-soundId',i);
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
		var media_url = lienMp3.attr('href');
   		var media_id = "media_" + lienMp3.attr('data-soundId');

		// ajouter une class pour cibler les barres de progression de ce son
		parent_track.addClass(media_id);
		
		jouer_son(media_id, media_url,lastSound);

	});

}

function jouer_son(media_id, media_url){
	
	var soundURL = media_url;
    var soundId = media_id ;
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
			lastSound = thisSound ;
        }
      }
    } else {

    	// create sound
    	thisSound = soundManager.createSound({

    		id:soundId,
    		url:soundURL,
	        multiShot: false,
		    autoPlay: false,
			autoLoad: true,
    		onplay:function(){
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

      	// stop last sound
		if (lastSound) {
        	soundManager.stop(lastSound.sID);
   			soundManager.unload(lastSound.sID);
        }	      
	    lastSound = thisSound ;
        
        thisSound.play();

      	// deplacer le son
		$("." + soundId + " .progress_bar").click(function(e){
			e.preventDefault();
			var duree = thisSound.durationEstimate;
			var offset = $(this).offset();
			var x = (e.pageX - offset.left) / $(this).width() ;
			var temps = duree * x;
			$(this).find(".position").css({width:x * 100 +"%"});
			if(thisSound.playState == 0){
				soundManager.play(thisSound);
				thisSound.setPosition(temps);
			}else{
				thisSound.setPosition(temps);
			}
		});	      

    }


}