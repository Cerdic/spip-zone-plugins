/*
	Appelee par le body onload, cette fonction affiche les players mp3 et genere les playlistes associees
	Auteur : BoOz <booz CHEZ rezo POINT net>
	Licence : GNU/GPL

	compatibilite firefox par Vincent Ramos <www-lansargues CHEZ kailaasa POINT net> et erational <http://www.erational.org>
*
* Fonctionne avec jQuery.
**/

var track_index = 0;
var playa='';

live_track = 'stop' ; 
live_video = 'stop' ; 
isVideoPlaying = 'false' ; 
videoPause = false ;     

//tableau des mp3 de la page
mp3Array = new Array();
mp3Titles = new Array();

flvArray = new Array();
flvTitles = new Array();
	
function Player_init(url_player) {

soundManager.onload = function() {
  // soundManager is initialised, ready to use. Create a sound for this demo page.
  soundManager.createSound('aDrumSound',url_player);
  }
  
}


$(document).ready(function(){


/*
soundManager.onload = function() {
// soundManager is initialised, ready to use. Create a sound for this demo page.
soundManager.defaultOptions.volume = 80;    // set global default volume
}
*/

var aff= $("a[@rel='enclosure'][@href$=mp3]").size(); 

	//$("body").css({background:"#FF0000"});
	// preparer un plan B si flash < 8
	playa  =  '<div id="musicplayer" style="">' +
	         '</div>';
			
	$('body').append(playa);
	$('div#musicplayer').css({position:"fixed",top:"10px", right:"10px",width:"0",height:"0"});
	
	// lister les mp3 de la page et ajouter un bouton "play" devant
	$("a[@rel='enclosure'][@href$=mp3]").each(
		function(i) {	 
				// we store mp3 links in an array
				mp3Array.push(this.href);
				mp3Titles.push($(this).html());

				//demarrer le lecteur lors d'un click
				$(this).click(
		             function(e)
		             {
		                 e.preventDefault();
		                 player_play(i);
		             }
		         );
		         $(this).parent().click(
		             function(e)
		             {
		                 player_play(i);
		             }
		         );
		         //a passer en .ajoute_musicplayer()	
				//$(this).before('<span class="play_">play</span>&nbsp;');
				$(this).before('<span class="play_"><img src="' + image_play + '"/></span>&nbsp;');

		}
	);


	$("a[@rel='video']").each(
		function(i) {	 
				// we store swf links in an array
				flvArray.push(this.href);
				flvTitles.push($(this).html());

				//demarrer le lecteur lors d'un click
				$(this).click(
		             function(e)
		             {
		                e.preventDefault();
		                video_play(i);	
						// $("#now_playing").html($(this).html());                
		             }
		         );
		        
		}
	);

	// toggle play / pause
	// toggle play / pause
	$("span.play_").each(
	function(i) {
	 
		$(this).toggle(
			             function(e){ 
			            if(live_track !=='stop'){
			              player_stop();
			             }else{
			            player_play(i) ;
			            }  						
						 },function(e){
						
			              player_stop();
			              
						 }		
			         );
	
	}
	);



	// pas de boutons play dans la playliste
	// mais un joli fond
	$(".playliste").find("span").remove();

	$(".playliste li").hover(function(){
	  $(this).addClass("over");
	},function(){
	  $(this).removeClass("over");
	});	



	// chopper les coordonnées du clic dans la barre de progression
	$("#scrollbar").click(function(e){
	var x = Math.round((e.pageX - this.offsetLeft) / $(this).width() * 100);
     if(live_track !== 'stop'){
     var mySound = soundManager.getSoundById('son_' + track_index);
     var newposition = Math.round(mySound.durationEstimate * x / 100) ;
     soundManager.setPosition('son_' + track_index , newposition) ;
     }
     // pareil pour les videos
     if(isVideoPlaying){
     var position = Math.round(myListener.duration * x / 100) ;
     getFlashObject().SetVariable("method:setPosition", position);
     }
     /*console.log( mySound.position + 'hop' + newposition + ' ' + x +'%');*/
  	 });

  	 $("#now_playing").change(function(){
  	      	 scroller_init();
	 });
	 
	 // taille player video
	 /*  $("#myFlash").toggle(function(){
	  this.width = 2 * this.width ;
	  this.height = 2 * this.height ;
	  }
	  ,function(){
	  this.width =  this.width / 2 ;
	  this.height = this.height / 2 ;
	 }); */
	 
	 
});


// .play() plugin jquery

function player_play(i){
	player_stop();
	track_index = i ;
	live_track = i ;

	//$("span.play_:eq("+i+")").html("stop").addClass("play_on");		
	$("span.play_:eq("+i+")").html("<img src='" + image_pause + "'/>").addClass("play_on");		
	$(".playliste li:eq("+i+")").addClass("play_on");

	if(soundManager.url != 'undefined'){
		soundManager.createSound({
	  	id:'son_'+i,url:mp3Array[i],
	 	  onfinish:function(){
	 	  /*console.log(this.sID+' finished playing'),*/
	 	  player_play(i+1)
	 	  },     
		  onid3:function(){
		  /*console.log(this.id3['songname'])*/
		  },                
		  onload:function(){
		  /*console.log(this.sID+' finished loading')*/
		  },              
		  whileloading:function(){
		  /*console.log('sound '+this.sID+' loading, '+this.bytesLoaded+' of '+this.bytesTotal);*/
		  var timer = this.bytesLoaded / this.bytesTotal * 100 ;
		  var minutes = Math.floor(this.durationEstimate / 1000 / 60) ;
		  var secondes = Math.floor((this.durationEstimate - minutes*1000*60) /1000);
		  $(".duration").html(minutes + "'" + secondes +"''");
		  $("#loading").css({width:Math.round(timer) +"%"});
		  },          // callback function for "download progress update" (X of Y bytes received)
		  onplay:function(){
		  $("#loading").css("cursor","hand");
		  var minutes = Math.floor(this.durationEstimate / 1000 / 60) ;
		  var secondes = Math.floor((this.durationEstimate - minutes*1000*60) /1000);
		  $(".duration").html(minutes + "'" + secondes +"''");		 
		  },                // callback for "play" start
		  whileplaying:function(){
		  var minutes = Math.floor(this.position / 1000 / 60) ;
		  var secondes = Math.floor((this.position - minutes*1000*60) /1000);
		  var timer2 = this.position / this.durationEstimate * 100 ;
		  $("#position").css({width:Math.round(timer2) +"%"});
		  $(".position").html(minutes + "'" + secondes +"''");
		  },          // callback during play (position update)
		  //'onstop':unLoad(this.sID),                // callback for "user stop"
		  //'onbeforefinish': null,        // callback for "before sound finished playing (at [time])"
		  //'onbeforefinishtime': 5000,    // offset (milliseconds) before end of sound to trigger beforefinish..
		  //'onbeforefinishcomplete':null, // function to call when said sound finishes playing
		  //'onjustbeforefinish':null,     // callback for [n] msec before end of current sound
		  //'onjustbeforefinishtime':200,  // [n] - if not using, set to 0 (or null handler) and event will not fire.
		  //'multiShot': true,             // let sounds "restart" or layer on top of each other when played multiple times..
		  //'pan': 0,                      // "pan" settings, left-to-right, -100 to 100
		  'volume': 100    	
	 	 });
	  
	  	//$("span#now_playing").html(i+"("+mp3Array[i]+")"+track_index);
	  	//$("span#now_playing").append("son_"+i.id3.artist);
		file1 = mp3Titles[track_index];
		file1 = file1.replace(/(%20)/g,' ');
		file1 = file1.substr(0,90);
		file1 = file1.replace(/(.mp3)/g,' ');
		file1 = file1.replace(/(_|-)/g,' ');
		//$("img[@alt='play']").attr()
		var taille = file1.length;
		$("#now_playing").css("width", taille*6) ;
		$("#scroller").css("width", taille*6) ;
		$("#now_playing").html(file1) ;
		var taille =  $("#scroller").width();
  		var min_taille = $("#scroller_container").width();

	   // adapter le defilement a la taille du texte
       $.extend({scroller: {
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


		
	    soundManager.play('son_'+i,{volume:100}) ;
	}else{
	
	//Ajouter le musicplayer de secours
	playlist='';
	deb=0;
	for(j=i; j < mp3Array.length ; j++) {
		if(deb > 0){
// Modification du code original. Voir ci-dessous.
			playlist = playlist + '|' + mp3Array[j];
// Fin modification
		}else{
			playlist = mp3Array[j];
			deb=1;
		}
	}

$("#musicplayer").html('<object '+
	'type="application/x-shockwave-flash" '+
	'data="'+musicplayerurl+'" '+
	'width="1" height="1" align="middle">'+
	'<param name="FlashVars" value="song_url='+playlist+'" />'+
	'<param name="wmode" value="transparent" />'+
	'<param name="movie" value="'+musicplayerurl+'" />'+
	'</object>');
// Fin modification

}

}
	

function player_stop(){
						//reinit d'un autre play
						
						//$("span.play_on").html('play');
						$("span.play_on").html('<img src="' + image_play + '"/>');
						$("span.play_on").removeClass("play_on");
						live_track = 'stop' ;
						
						$(".playliste li.play_on").removeClass("play_on");
						soundManager.destroySound("son_" + track_index);
						soundManager.stopAll();
						//stop le musicplayer en flash < 8
						$("#musicplayer").html('');
						$("#now_playing").html('');
}	


function unLoad(i){
	soundManager.unload(i);
	/*console.log(i+' unload hop');*/

}

	
	function player_next()
	{	
		unLoad("son_" + track_index);
		track_index++;
		//file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		//$("#now_playing").html(file1) ;
		player_play(track_index);
		
	}
	

	
	function player_prev()
	{	
		unLoad("son_" + track_index);
		track_index--;	
		//file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		//$("#now_playing").html(file1) ;
		player_play(track_index);
		
	}


	// lecteur video
	// doc : http://flv-player.net/players/js/documentation/


 function video_play(i){

	track_index = i ;
	live_video = i ;
	
			if (!videoPause) {
			video_stop();
  	 		getFlashObject().SetVariable("method:setUrl", flvArray[i]);
  	 		}          
     		getFlashObject().SetVariable("method:play", "");
     		videoPause = false ; 
     		$(".playliste li:eq("+i+")").addClass("play_on");

 }

function video_pause()
            {
                if(videoPause){ videoPause = false } else { videoPause = true }
                getFlashObject().SetVariable("method:pause", "");
            }

function video_next()
	{	
		track_index++;
		video_play(track_index);
		
	}
	
	
	function video_prev()
	{	
		track_index--;	
		video_play(track_index);
		
	}
	
	function video_stop()
	{	
   	 $(".playliste li.play_on").removeClass("play_on");
	 getFlashObject().SetVariable("method:stop", "");
	 getFlashObject().SetVariable("method:setUrl", videoNullUrl);          
     getFlashObject().SetVariable("method:play", "");
     getFlashObject().SetVariable("method:stop", "");
     getFlashObject().SetVariable("method:setPosition", 0);
	}


	function video_setVolume()
            {
            	var volume = document.getElementById("inputVolume").value;
            	getFlashObject().SetVariable("method:setVolume", volume);
            }
   