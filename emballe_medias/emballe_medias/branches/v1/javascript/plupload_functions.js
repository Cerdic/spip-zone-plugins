var uploader;

function em_plupload_queuechanged(up,debug){
	var container = up.settings.container;
	if(up.files.length == 0){
		if($('#'+container).find('#uploadfiles').is(':visible')){
			$('#'+container).find('#uploadfiles').fadeOut();
		}
	}
	if(debug)
		em_plupload_log('[QueueChanged]');
	up.refresh();
}

function em_plupload_error(up,err,debug){
	var file = err.file, message;
	$('#'+up.settings.container+' .erreur').detach();
	if (file) {
		message = err.message;

		if (err.details) {
			message += " (" + err.details + ")";
		}

		if (err.code == plupload.FILE_SIZE_ERROR) {
			//alert("Error: File too large: " + file.name);
		}

		if (err.code == plupload.FILE_EXTENSION_ERROR) {
			//alert("Error: Invalid file extension: " + file.name);
		}

		//$('#' + file.id).attr('class', 'plupload_failed').find('a').css('display', 'block').attr('title', message);
	}
	$('#'+up.settings.container+' #em_message_avant_upload').after("<div class='erreur'>" + err.message +
		(err.file ? " (File: " + err.file.name+")" : "") +
		"</div>"
	);
	if(debug)
		em_plupload_log('[error] ', args);
	up.refresh(); // Reposition Flash/Silverlight
}

function em_plupload_statechanged(up,debug){
	if(debug)
		em_plupload_log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
}

function em_plupload_refresh(up,debug){
	if(debug)
		em_plupload_log('[Refresh]');
}

function em_plupload_postinit(up,debug){
	if(debug)
		em_plupload_log('[PostInit]');
}

function em_uploader_init(up,info,debug){
	if(debug){
		em_plupload_log('[Init]');
		plupload.each(info, function(info) {
			em_plupload_log('  Info:', info);
		});
	}
	
	if($('#uploadfiles').is(':visible')){
		$('#uploadfiles').fadeOut('slow');
	}
	$('#divLoadingContent').fadeOut('slow',function(){
		up.refresh();
	});
	if(up.features.dragdrop){
		$('#dropbox_files').fadeIn('slow');	
	}
	up.refresh();
}

function em_plupload_uploadfile(up, file,debug){
	jQuery('.em_charger_fichiers').tabs('disable',1);
	isUploading = true;
	if($('#' + file.id+' .progressWrapper').size() == 0){
		iTime = new Date();
		var container= '<div id="progress_bar_container"></div>';
		var wrapper = '<div class="progressWrapper"></div>';
		var element = '<div class="progress_container"><div class="bg"><span class="bar" style="width: 4px;">0%</span></div></div><div class="upload_info"><span class="filename">'+file.name+'</span><div class="transfer"></div><div class="time_remaining"></div><div class="statut"></div></div>';
		jQuery('#' + file.id).append(container);
		jQuery('#' + file.id+' #progress_bar_container').append(wrapper);
		jQuery('#progress_bar_container').show();
		jQuery('#' + file.id).find('.progressWrapper').append(element);
		jQuery('#'+file.id+' .statut').html(emballe_medias_langue.statut+''+emballe_medias_langue.uploading);
		jQuery('#'+file.id+' a.cancel').remove();
		jQuery('#'+file.id+' .upload_info').prepend('<a href="#" class="cancel">'+emballe_medias_langue.cancel_upload+'</a>');
		jQuery('#'+file.id+' a.cancel').click(function(e){
			if(debug)
				em_plupload_log('[cancel]');
			file.status = plupload.FAILED;
			isUploading = false;
			return false;
		});
		$('#' + file.id + ' b,#'+file.id+' .file_infos').fadeOut();
	}
	var container = up.settings.container;
	if($('#'+container+' #uploadfiles').is(':visible')){
		$('#'+container+' #uploadfiles').fadeOut('slow',function(){
			up.refresh();
		});
	}
	
	if (debug)
		em_plupload_log('[UploadFile]', file);
}

function em_plupload_filesremoved(up, files,debug){
	if (debug){
		em_plupload_log('[FilesRemoved]');

		plupload.each(files, function(file) {
			em_plupload_log('  File:', file);
		});
	}
	
	var container = up.settings.container;
	$.each(files, function(i, file) {
		$('#'+container+' .files #'+file.id).fadeOut('slow',function(){
			up.refresh();
		}).detach();
		if(!$('#'+container+' .files .file').size()){
			$('#'+container+' .files').fadeOut('slow',function(){
				up.refresh();
			}).detach();
			if($('#'+container).find('#uploadfiles').is(':visible')){
				$('#'+container).find('#uploadfiles').fadeOut('slow',function(){
					up.refresh();
				});
			}
		}
	});
	nb_files = up.files.length ? up.files.length : 0;
	if(nb_files < nb_max){
		if(up.features.dragdrop && $('#'+container).find('#dropbox_files').is(':hidden')){
			$('#'+container).find('#dropbox_files').fadeIn('slow',function(){
				up.refresh();
			});
		}
		if($('#'+container).find('#spanButtonPlaceHolder').is(':hidden')){
			$('#'+container).find('#spanButtonPlaceHolder').fadeIn('slow',function(){
				up.refresh();
			});
		}
	}
	if(up.files.length == 0){
		if($('#'+container).find('#uploadfiles').is(':visible')){
			$('#'+container).find('#uploadfiles').fadeOut('slow',function(){
				up.refresh();
			});
		}
	}
	up.refresh();
}

function em_plupload_uploadprogress(up,file,debug){
	if((typeof(file) != 'undefined') && (typeof(file.cancelled) != 'undefined')){
		return;
	}else if((file.status == plupload.FAILED) && (typeof(file.cancelled) == 'undefined')){
		if(debug)
			em_plupload_log('[UploadProgress] : FAILED');
		em_spipmotion_cancel(up,file);
		var url = up.settings.url;
		var complement = up.settings.multipart_params;
		complement.delete_tmp = 'oui';
		complement.name = file.target_name;
		jQuery.ajax({
			url: url,
			type: "POST",
			data: (complement)
		});
		up.stop();
		return;
	}
	if(debug)
		em_plupload_log('[UploadProgress]', 'File:', file, "Total:", up.total);
	var currentTime = new Date();
	
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
	var uSpeed = Math.floor(roundNumber(((file.loaded/uTime)/1000),2));

	/**
	 * Calcul du nombre de KB ou ko déjà mis en ligne :
	 * nombre total de bytes mis en ligne divisé par 1024 (1KB = 1024 bytes = 1024 octets = 1ko)
	 */
	var cur_KB=Math.ceil(file.loaded/1024);

	/**
	 * Calcul de la taille totale du fichier en KB ou ko : total_KB
	 * nombre total de bytes du fichier divisé par 1024 (1KB = 1024 bytes = 1024 octets = 1ko)
	 */
	var total_KB=Math.ceil(file.size/1024);

	/**
	 * Calcul de la moyenne : kbs
	 */
	if(file.loaded) 
		var kbs=Math.ceil((file.loaded/1000)/uTime);

	/**
	 * Calcul du temps restant en secondes : time_left
	 */
	var time_left = Math.ceil((file.size-file.loaded)/(kbs*1000));

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
	jQuery('#'+file.id+' .bg .bar').css('width',file.percent+'%').html(file.percent+'%');
	jQuery('#'+file.id+' .transfer').html(cur_KB+'KB of '+total_KB+'KB ('+kbs+' kb/s)');
	jQuery('#'+file.id+' .time_remaining').html(time_left_end+' '+emballe_medias_langue.temps_restant+' ('+time_passed_end+' '+emballe_medias_langue.temps_passe+')');
	if(file.percent == '100'){
		jQuery('#'+file.id+' .transfer').html(cur_KB+'KB of '+total_KB+'KB');
		jQuery('#'+file.id).animeajax();
		jQuery('#'+file.id+' .statut').html(emballe_medias_langue.statut+''+emballe_medias_langue.analyze_document);
	}
}
function em_plupload_log() {
		var str = "";
		if($('#logger').size() == 0){
			$('#em_form_upload').append('<textarea id="logger" rows="20"></textarea>')
		}
		plupload.each(arguments, function(arg) {
			var row = "";

			if (typeof(arg) != "string") {
				plupload.each(arg, function(value, key) {
					// Convert items in File objects to human readable form
					if (arg instanceof plupload.File) {
						// Convert status to human readable
						switch (value) {
							case plupload.QUEUED:
								value = 'QUEUED';
								break;

							case plupload.UPLOADING:
								value = 'UPLOADING';
								break;

							case plupload.FAILED:
								value = 'FAILED';
								break;

							case plupload.DONE:
								value = 'DONE';
								break;
						}
					}

					if (typeof(value) != "function") {
						row += (row ? ', ' : '') + key + '=' + value;
					}
				});

				str += row + " ";
			} else { 
				str += arg + " ";
			}
		});

	$('#logger').prepend(str + "\n");
}

function em_spipmotion_cancel(up,file){
	if((typeof(file) != 'undefined') && (typeof(file.cancelled) == 'undefined')){
		file.cancelled = 'oui';
		jQuery('#'+file.id+' .statut').html(emballe_medias_langue.cancelled);
		jQuery('#'+file.id+' #progress_bar_container > div').addClass('red');
		jQuery('#'+file.id+' .bg .bar').toggleClass('progressBarError').css('width','');
		setTimeout(function () {
			jQuery('#'+file.id).fadeOut('slow',function(){
				jQuery('#'+file.id).remove();
				up.removeFile(file);
				jQuery('.em_charger_fichiers').tabs('enable',1);
			});
		}, 1500);
	}
}