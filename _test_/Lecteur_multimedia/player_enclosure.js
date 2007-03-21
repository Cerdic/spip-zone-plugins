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

//tableau des mp3 de la page
mp3Array = new Array();

$(document).ready(function(){

//mettre le player aflax en bas de page
//$("#aflax_obj_0").appendTo("body");


var aff= $("a[@rel='enclosure'][@href$=mp3],a[@rel='enclosure'][@href$=mp3]").size();

	//$("body").css({background:"#FF0000"});
	playa  =  '<div id="musicplayer" style="">' +
	         '</div>';
			
			$('body').append(playa);
			$('div#musicplayer').css({position:"fixed",top:"10px", right:"10px",width:"0",height:"0"});
	

	$("a[@rel='enclosure'][@href$=mp3]").each(
		function(i) {	 
				// we store mp3 links in an array
				mp3Array.push(this.href);
				//demarrer le lecteur lors d'un click
				$(this).click(
		             function(e)
		             {
		                 e.preventDefault();
		                 player_play(i);
		             }
		         );
		         //a passer en .ajoute_musicplayer()	
				$(this).before('<span class="play_">play</span>&nbsp;');
		}
	);



	$("span.play_").each(
	function(i) {
	 
		$(this).toggle(
			             function(e){ 
			             player_play(i) 						
						 },function(e){
						 player_stop();
						 }		
			         );
	
	}
	);


	//pas de boutons play dans la playliste
	$(".playliste").find("span").remove();

	$(".playliste li").hover(function(){
	  $(this).addClass("over");
	},function(){
	  $(this).removeClass("over");
	});	


});


// .play() plugin jquery

function player_play(i){
	player_stop();
	$("span.play_:eq("+i+")").html("stop").addClass("play_on");		
	$(".playliste li:eq("+i+")").addClass("play_on");
	//Ajouter le musicplayer
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
	


function player_stop(){

						//reinit d'un autre play
						
						$("span.play_on").html('play');
						$("span.play_on").removeClass("play_on");
						
						$(".playliste li.play_on").removeClass("play_on");
						//stop le musicplayer en flash < 8
						$("#musicplayer").html('');
}	


	
	function player_next()
	{	
		
		track_index++;
		file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		$("#pos").html(file1) ;
		player_play(track_index);
		
	}
	

	
	function player_prev()
	{	
		track_index--;	
		file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		player_play(track_index);
		
	}


	
	
//player one pix	

var ap_instances = new Array();

function ap_stopAll(playerID) {
	for(var i = 0;i<ap_instances.length;i++) {
		try {
			if(ap_instances[i] != playerID) document.getElementById("audioplayer" + ap_instances[i].toString()).SetVariable("closePlayer", 1);
			else document.getElementById("audioplayer" + ap_instances[i].toString()).SetVariable("closePlayer", 0);
		} catch( errorObject ) {
			// stop any errors
		}
	}
}

function ap_registerPlayers() {
	var objectID;
	var objectTags = document.getElementsByTagName("object");
	for(var i=0;i<objectTags.length;i++) {
		objectID = objectTags[i].id;
		if(objectID.indexOf("audioplayer") == 0) {
			ap_instances[i] = objectID.substring(11, objectID.length);
		}
	}
}

var ap_clearID = setInterval( ap_registerPlayers, 100 );


function play() {
    document.monFlash.SetVariable("player:jsPlay", "");
}
function pause() {
    document.monFlash.SetVariable("player:jsPause", "");
}
function stop() {
    document.monFlash.SetVariable("player:jsStop", "");
}
function volume(n) {
    document.monFlash.SetVariable("player:jsVolume", n);
}
