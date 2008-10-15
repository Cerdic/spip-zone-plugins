//---------------------------------------
// eraplayer playlist 
// a minimalistic player for playlist
// alternative for musicplayer.swf
//
// author:  erational.org
// version: 1.1
// date:    2007.02.04
// licence: GPL
//---------------------------------------

// song_url is given with  jquery

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
//song_url = "a.mp3|b.mp3|c.mp3";

isPlaying = false;
userAction = true; //false;
pos = 0;				// position of current track being played
playlist = new Array(); // tracklisting
playlist_pos = -1;		// position of the track being played

var my_sound:Sound = new Sound();
my_sound.start();
if (song_url!=undefined) {
	playlist = new Array();
	playlist = explode("|,",song_url);	
	if (playlist.length>0) {
		playlist_pos = 0;
		trace(playlist[playlist_pos]);
		my_sound.loadSound(playlist[playlist_pos], true);
	}
}

//
// tracklisting
//
my_sound.onSoundComplete = function() {
	playlist_pos++;
	if (playlist_pos < playlist.length && userAction) {
		trace(playlist[playlist_pos]);
		my_sound.loadSound(playlist[playlist_pos], true);
		
	}
}
