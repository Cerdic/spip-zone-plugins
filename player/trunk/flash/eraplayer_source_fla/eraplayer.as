//---------------------------------------
// era player: 
// a minimalistic player, you can easily skin
//
// author: erational.org
// date:   2006.12.01
//---------------------------------------

// mp3 is given with FlashVars


//---------------------------------------
// misc. function
//---------------------------------------

function explode(separator:String, string:String) {

	var list = new Array();

	if (separator == null) return false;
	if (string == null) return false;

	var currentStringPosition = 0;
	while (currentStringPosition<string.length) {
		var nextIndex = string.indexOf(separator, currentStringPosition);
		if (nextIndex == -1) break;
		var word = string.slice(currentStringPosition, nextIndex);
		list.push(word);
		currentStringPosition = nextIndex+1;
	}
	if (list.length<1) {
		list.push(string);
	} else {
		list.push(string.slice(currentStringPosition, string.length));
	}
	return list;
}


//---------------------------------------
// main
//---------------------------------------
isPlaying = false;
userAction = false;
pos = 0;				// position of current track being played
playlist = new Array(); // tracklisting
playlist_pos = -1;		// position of the track being played

var my_sound:Sound = new Sound();
my_sound.start();
if (mp3!=undefined) {
	playlist = new Array();
	playlist = explode(",",mp3);	
	if (playlist.length>0) {
		playlist_pos = 0;
		trace(playlist[playlist_pos]);
		my_sound.loadSound(playlist[playlist_pos], true);
	}
}
my_sound.setVolume(0); // to load the stream in cache

//
// tracklisting
//
my_sound.onSoundComplete = function() {
	trace("complete");
	playlist_pos++;
	if (playlist_pos < playlist.length && userAction) {
		trace(playlist[playlist_pos]);
		my_sound.loadSound(playlist[playlist_pos], true);
		
	}
}

//
// button play / stop
//
but.onRelease = function() {		
	if (mp3!="") {
		// one time only - reinit the sound
		if (!userAction) { 
			my_sound.stop();
			my_sound.setVolume(100);
			userAction = true;
		}		
		// but play / pause		
		if (!isPlaying) {			
			my_sound.start(pos/1000);
			isPlaying = true;
			// end of track ? replay
			if (pos ==  my_sound.duration) my_sound.start();			
		} else {
			pos = my_sound.position;			
			my_sound.stop();
			isPlaying = false;
		}		
	}	
}
