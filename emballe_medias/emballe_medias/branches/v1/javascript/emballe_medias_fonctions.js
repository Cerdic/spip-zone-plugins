function gethtml5uploadfunctions(){
	var xhr, hasXhrSupport, hasProgress, canSendBinary, dataAccessSupport, sliceSupport, ua;
	
	// in some cases sniffing is the only way around (@see triggerDialog feature), sorry
	ua = (function() {
			var nav = navigator, userAgent = nav.userAgent, vendor = nav.vendor, webkit, opera, safari;
			
			webkit = /WebKit/.test(userAgent);
			safari = webkit && vendor.indexOf('Apple') !== -1;
			opera = window.opera && window.opera.buildNumber;
			
			return {
				ie : !webkit && !opera && (/MSIE/gi).test(userAgent) && (/Explorer/gi).test(nav.appName),
				webkit: webkit,
				gecko: !webkit && /Gecko/.test(userAgent),
				safari: safari,
				safariwin: safari && navigator.platform.indexOf('Win') !== -1,
				opera: !!opera
			};
		}());

	hasXhrSupport = hasProgress = dataAccessSupport = sliceSupport = false;
	
	if (window.XMLHttpRequest) {
		xhr = new XMLHttpRequest();
		hasProgress = !!xhr.upload;
		hasXhrSupport = !!(xhr.sendAsBinary || xhr.upload);
	}

	// Check for support for various features
	if (hasXhrSupport) {
		canSendBinary = !!(xhr.sendAsBinary || (window.Uint8Array && window.ArrayBuffer));
		
		// Set dataAccessSupport only for Gecko since BlobBuilder and XHR doesn't handle binary data correctly				
		dataAccessSupport = !!(File && (File.prototype.getAsDataURL || window.FileReader) && canSendBinary);
		sliceSupport = !!(File && (File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice)); 
	}

	// sniff out Safari for Windows and fake drag/drop
	fakeSafariDragDrop = ua.safariwin;

	return {
		html5: hasXhrSupport, // This is a special one that we check inside the init call
		dragdrop: (function() {
			// this comes directly from Modernizr: http://www.modernizr.com/
			var div = document.createElement('div');
			return ('draggable' in div) || ('ondragstart' in div && 'ondrop' in div);
		}()),
		jpgresize: dataAccessSupport,
		pngresize: dataAccessSupport,
		multipart: dataAccessSupport || !!window.FileReader || !!window.FormData,
		canSendBinary: canSendBinary,
		// gecko 2/5/6 can't send blob with FormData: https://bugzilla.mozilla.org/show_bug.cgi?id=649150 
		cantSendBlobInFormData: !!(ua.gecko && window.FormData && window.FileReader && !FileReader.prototype.readAsArrayBuffer),
		progress: hasProgress,
		// WebKit and Gecko 2+ can trigger file dialog progrmmatically
		//triggerDialog: (ua.gecko && window.FormData || ua.webkit)
		chunks: sliceSupport
	};
}

function infos_upload(currentTime,iTime,percent,bytesLoaded,bytesTotal){
	/**
	 * Calcul du temps écoulé depuis le début de l'upload en seconde : uTime
	 */
	var uTime = roundNumber((Math.ceil(currentTime-iTime)/1024),0);

	/**
	 * Calcul du temps passé pour l'upload sous la forme hh:mm:ss
	 *
	 * @var hour_passed : nombre d'heures
	 * @var min_passed : nombre de minutes (heures décomptées)
	 * @var sec_passed : nombre de secondes (heures et minutes décomptées)
	 * @var time_passed_end : concaténation de ces valeurs sous la forme hh:mm:ss
	 */
	var hour_passed=Math.floor(uTime/3600);
	hour_passed=hour_passed<10?'0'+hour_passed:hour_passed;
	var min_passed=(Math.floor(uTime/60)%60);
	min_passed=min_passed<10?'0'+min_passed:min_passed;
	var sec_passed=(uTime%60);
	sec_passed=sec_passed<10?'0'+sec_passed:sec_passed;
	var time_passed_end = hour_passed+':'+min_passed+':'+sec_passed;

	/**
	 * Calcul de la vitesse d'upload
	 * On donne une valeur en kb (kilo bits) donc on divise par 1000
	 */
	var uSpeed = Math.floor(roundNumber(((bytesLoaded/uTime)/1000),2));

	/**
	 * Calcul du nombre de KB ou ko déjà mis en ligne :
	 * nombre total de bytes mis en ligne divisé par 1024 (1KB = 1024 bytes = 1024 octets = 1ko)
	 */
	var cur_KB=format_file_size(bytesLoaded);

	/**
	 * Calcul de la taille totale du fichier en KB ou ko : total_KB
	 * nombre total de bytes du fichier divisé par 1024 (1KB = 1024 bytes = 1024 octets = 1ko)
	 */
	var total_KB=format_file_size(bytesTotal);

	/**
	 * Calcul de la moyenne : kbs
	 */
	if(bytesLoaded) var kbs=Math.ceil((bytesLoaded/1000)/uTime);

	/**
	 * Calcul du temps restant en secondes : time_left
	 */
	var time_left=Math.ceil((bytesTotal-bytesLoaded)/(kbs*1000));

	/**
	 * Calcul du temps restant pour l'upload sous la forme hh:mm:ss
	 *
	 * @var hour_left : nombre d'heures
	 * @var min_left : nombre de minutes (heures décomptées)
	 * @var sec_left : nombre de secondes (heures et minutes décomptées)
	 * @var time_left_end : concaténation de ces valeurs sous la forme hh:mm:ss
	 */
	var hour_left=Math.floor(time_left/3600);
	hour_left=hour_left<10?'0'+hour_left:hour_left;
	var min_left=(Math.floor(time_left/60)%60);
	min_left=min_left<10?'0'+min_left:min_left;
	var sec_left=(time_left%60);
	sec_left=sec_left<10?'0'+sec_left:sec_left;
	var time_left_end = hour_left+':'+min_left+':'+sec_left;
	
	var ret = {};
	ret.time_left = time_left_end;
	ret.kbs = kbs;
	ret.total_kb = total_KB;
	ret.cur_kb = cur_KB;
	ret.uspeed = uSpeed;
	ret.time_passed = time_passed_end;

	return ret;
}

function format_file_size(bytes){
	if (typeof bytes !== 'number') {
        return false;
    }
    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }
    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }
    return (bytes / 1000).toFixed(2) + ' KB';
}

//roundNumber found via google
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}