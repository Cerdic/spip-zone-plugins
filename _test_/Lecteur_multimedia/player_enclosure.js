/*
	appelee par le body onload, cette fonction affiche les players mp3 et genere les playlistes associées
	auteur :BoOz - booz@rezo.net
	code gnu - gpl

 par BoOz booz AT rezo.net
*
* Fonctionne avec jQuery.
**/



var soundObj = null;
var track_index = 0;
var playa='';
//afficher un player invisible
aflax.insertFlash(1, 1, "#FFFFFF", "go", true, true);
var requiredVersion=new com.deconcept.PlayerVersion([8,0,0]);
var installedVersion=com.deconcept.FlashObjectUtil.getPlayerVersion();
if(installedVersion.versionIsValid(requiredVersion)==true){
var player_aflax_ok=true;
}
//tableau des mp3 de la page
mp3Array = new Array();
setInterval("timer_()", 1000);

$(document).ready(function(){

//mettre le player aflax en bas de page
//$("#aflax_obj_0").appendTo("body");


var aff= $("a[@rel='enclosure'][@href$=mp3]").size();


	if(player_aflax_ok==true && aff > 0){
	//$("body").css({background:"#00FF00"});
	//interface du player en html
	
	
	/*
	// Activer le player js
	 
	playa  = '<form id="player_interface" style="background-color:#CFD4E6;padding:10px;margin-bottom:10px">' +		
			 '<input type="text" id="posi" style="float:right;width:100px" />' +
			 '<div id="pos" style="width:160px;color : #FFFFFF"/></div>' +
			 
			 '<div style="margin-top:5px">' +
			 '<input type="text" id="etat" value="Loading..." style="float:right;width:100px" />' +
				'<input type="button"  name="joe" id="play" onClick="player_play(0)" value="Play" />' +
				'<input type="button"  name="jack" onClick="player_stop()" value="Stop" />' +
				'<input type="button"  name="william"" id="next" onClick="player_prev()" value="<" />' +
				'<input type="button"  name="avrell" id="next" onClick="player_next()" value=">" />' +
			'</div>' +
			
			'</form>';
	
	
	//Afficher l'interface du player dans la page
	
	if($('div#player').size() !='0')
	{
	$('div#player').html(playa);
	}else{
	$('body').prepend(playa);
	//$("body").css({background:"#FF0000"});
	
	}
	
	
	$("#player_interface").css({position:"fixed", bottom:"0%", right:"0%", background:"#000", width:"380px", margin:"0px", cursor:"pointer"});
	setTimeout('$("#player_interface").slideUp("slow");', 5000);
	setTimeout('$("#player_interface").toggle().css({height:"3px"})',6000);
	$("#player_interface").hover(function(){ $(this).css({height:"60px"}); },function(){ $(this).css({height:"3px"}); });
	
	//$("#player_interface").css({height:"1px",width:"1px",overflow:"hidden"});
	//setTimeout('$("#player_interface").hide();',7000);
	**/
	
	
	}else{
	//$("body").css({background:"#FF0000"});
	playa  = '<form id="player_interface" style="background-color:#CFD4E6;padding:5px;margin-bottom:10px">' +
			  '<div id="musicplayer" style="float:right;width:30px">' +
	         '</div>' +
			'</form>';
			
			$('div#player').html(playa);
			$('div#player').css({height:"1px",width:"1px",background:"#FFFFFF",overflow:"hidden"});
	
	}






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
	
	//Si flash 8 et aflax OK
	if(player_aflax_ok==true){

	//jouer le son
	soundObj.stop(); 
	$('#etat').val("Loading...");
	file1=(mp3Array[i].split("/"))[(mp3Array[i].split("/")).length-1];
	$("#pos").html(file1) ;
	soundObj.loadSound( mp3Array[i] , true);
	track_index=i; 
	}else{
	//si flash < 8	
	//Ajouter le musicplayer
	 playlist='';
						deb=0;
							for(j=i; j < mp3Array.length ; j++) {
								if(deb > 0){
								playlist = playlist + ',' + mp3Array[j];
								}else{
								playlist = mp3Array[j];
								deb=1;
								}
							}

						$("#musicplayer").html('<object ' +
						'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ' +
						'codebase="' +
						'http://fpdownload.macromedia.com/pub/shockwave/cabs/'+
						'flash/swflash.cab#version=6,0,0,0"' +
						'width="18" height="18" align="middle">' +
						'<param name="wmode" value="transparent" />' +
						'<param name="allowScriptAccess" value="sameDomain" />' +
						'<param name="flashVars" value="song_url='+playlist+'" />' +
						'<param name="movie" value="'+musicplayerurl+'?autoplay=true" />' +
						'<param name="quality" value="high" />' +
						'<embed style="margin-left:0.1em" ' +
						'src="'+musicplayerurl+'?autoplay=true" '+
						'flashVars="song_url='+playlist+'"' +
						'quality="high" wmode="transparent" width="18" height="18" name="player"' +
						' allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
						' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
											
							
							
	}
	
	}

function player_stop(){

						//reinit d'un autre play
						
						$("span.play_on").html('play');
						$("span.play_on").removeClass("play_on");
						
						$(".playliste li.play_on").removeClass("play_on");
						
						soundObj.stop();
						//stop le musicplayer en flash < 8
						$("#musicplayer").html('');
}	

// autres fonctions player

	function go()
	{
		AFLAX.trace("Here")
		
		soundObj = new AFLAX.FlashObject(aflax, "Sound");

		AFLAX.trace(soundObj.id);
		
		soundObj.exposeFunction("loadSound", soundObj);		
		soundObj.exposeFunction("start", soundObj);		
		soundObj.exposeFunction("stop", soundObj);		
		soundObj.exposeProperty("position", soundObj);
		soundObj.exposeProperty("id3", soundObj);

/*	
soundObj.onID3 = function(){
    for( var prop in soundObj.id3 ){
     $("#pos").html(prop + " : "+ my_sound.id3[prop] + kiki +" ") ;
    }
}
   
*/ 
    
		soundObj.mapFunction("addEventHandler");		
		soundObj.addEventHandler("onLoad", "readyToPlay");
		soundObj.addEventHandler("onSoundComplete", "finished");
		
	}
	


	
	
	function readyToPlay()
	{
		$('#etat').val("Loaded !");
	}
	
	function finished()
	{
		$('#aflaxlogger').html("Fini");	
		player_next();
		
	}
	
	function player_next()
	{	
		
		track_index++;

		$('#aflaxlogger').html("Lecture");	
		$('#etat').val("Loading...");
		file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		$("#pos").html(file1) ;
		player_play(track_index);
		
	}
	

	
	function player_prev()
	{	
		track_index--;	

		file1=(mp3Array[track_index].split("/"))[(mp3Array[track_index].split("/")).length-1];
		$("#pos").html(file1) ;
		player_play(track_index);
		
	}


	function timer_()
	{
		if(soundObj != null)
		{
			var t = soundObj.getPosition();
			$("#posi").val("Time : " + t/1000 ) ;
		}
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
