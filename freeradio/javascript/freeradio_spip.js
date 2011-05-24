/*
* freeradio_spip.js
* franck ruzzin
* le 10 mai 2011
*/


m_freeradio_swfUrl="swf/freeradio.1.0.swf";		//Chemin et nom vers le fichier swf "freeradio.swf"
m_express_swfUrl="swf/expressInstall.swf";		//Chemin et nom vers le fichier swf "expressInstall.swf"

freeRadio = {
	//tableau des instances FreeRadio (indexé par l'id de l'instance)
	radioArray : new Array(),
	embedRadio : function(flashvars,params,attributes)
	{
		loc_freeradio_swfUrl= freeradioRoot + m_freeradio_swfUrl;	//Chemin et nom vers le fichier swf "freeradio.swf", prenant en compte le répertoire auto des plugins
		loc_express_swfUrl= freeradioRoot + m_express_swfUrl;		//Chemin et nom vers le fichier swf "expressInstall.swf", prenant en compte le répertoire auto des plugins
		
		//tableau associé à chaque objet FreeRadio
		myRadio=new Object;
		myRadio.isReady=false;
		myRadio.onVolumeChange_cb=flashvars.onVolumeChange;
		myRadio.onPanChange_cb = flashvars.onPanChange;
		myRadio.onStop_cb = flashvars.onStop;
		myRadio.onPlay_cb = flashvars.onPlay;
		
		if (typeof myRadio.onVolumeChange_cb != 'function' && typeof onVolumeChange=='function') myRadio.onVolumeChange_cb=onVolumeChange;
		if (typeof myRadio.onPanChange_cb != 'function' && typeof onPanChange=='function') myRadio.onPanChange_cb=onPanChange;
		if (typeof myRadio.onStop_cb != 'function' && typeof onStop=='function') myRadio.onStop_cb=onStop;
		if (typeof myRadio.onPlay_cb != 'function' && typeof onPlay=='function') myRadio.onPlay_cb=onPlay;
		this.radioArray[attributes.altContentId]=myRadio;
		
		params.menu = "false";
		params.scale = "noScale";
		params.allowScriptAccess = "always";
		// pour attribuer une classe d'alignement (frleft, frcenter, frright)
		if (attributes.style) suffix=" "+attributes.style; else suffix="";
		attributes.styleclass="freeRadio"+suffix;
		attributes.name=attributes.altContentId;
		swfobject.embedSWF(loc_freeradio_swfUrl, attributes.altContentId, "118px", "70px", "10.0.0", loc_express_swfUrl, flashvars, params, attributes,freeRadio.onEmbedded);
		
	},
	// L'élément d'id objId est il implanté dans la page ?
	isReady : function(objId) {
		return this.radioArray[objId].isReady;
	},
	theMovie : function (flashObjectId) {
		if (navigator.appName.indexOf("Microsoft") != -1) {
			return window[flashObjectId];
		} else {
			return document[flashObjectId];
		}
	},
	// L'implantation est réalisée, fixer conséquemment la variable radioArray[evt.ref.name]
	// L'évènement evt a comme attributs :
	// - success : l'implantation a réussi  (success = true or false)
	// - id : indique l'ID utilisé dans la balise lors de l'insertion
	// - ref : référence de l'élément HTML (renvoie undefined si success = false)
	onEmbedded : function (evt) {
		if (evt.success)
		{
			freeRadio.radioArray[evt.ref.name].isReady=true;
		}
	},
	// Communication avec FreeRadio (objet swf)
	play : function (objId) {
		this.theMovie(objId).play();
	},
	stop : function (objId) {
		this.theMovie(objId).stop();
	},
	isPlaying : function (objId) {
		return this.theMovie(objId).isPlaying();
	},
	getVolume : function (objId) {
		return this.theMovie(objId).getVolume();
	},
	setVolume : function (objId,volume) {
		this.theMovie(objId).setVolume(volume);
	},
	getPan : function (objId) {
		return this.theMovie(objId).getPan();
	},
	setPan : function (objId,pan) {
		this.theMovie(objId).setPan(pan);
	},
	getRadioUrl : function (objId) {
		return this.theMovie(objId).getRadioUrl();
	},
	setRadioUrl : function (objId,radioURL) {
		this.theMovie(objId).setRadioUrl(radioURL);
	},
	onVolumeChange : function (objID,newVol) {
		if (typeof this.radioArray[objID].onVolumeChange_cb == 'function')
			this.callFunction(this.radioArray[objID].onVolumeChange_cb,objID,newVol);
	},
	onPanChange : function (objID,newPan) {
		if (typeof this.radioArray[objID].onPanChange_cb == 'function')
			this.callFunction(this.radioArray[objID].onPanChange_cb,objID,newPan);
	},
	onStop : function (objID) {
		if (typeof this.radioArray[objID].onStop_cb == 'function')
			this.callFunction(this.radioArray[objID].onStop_cb,objID);
	},
	onPlay : function (objID) {
		if (typeof this.radioArray[objID].onPlay_cb == 'function')
			this.callFunction(this.radioArray[objID].onPlay_cb,objID);
	},
	callFunction : function (f,a,b)
	{
		if ((f!=undefined) && (typeof f=='function')) f(a,b);
	}
}


