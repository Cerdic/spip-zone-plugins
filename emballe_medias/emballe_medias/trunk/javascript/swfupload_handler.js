/**
   Event Handlers

   La gestion des bytes / bits / octets est réalisée en fonction du tableau :
   http://www.artlebedev.com/mandership/84/
*/

function swfupload_swfUploadPreLoad() {
	nb_files = (nb_files + this.customSettings.nb_files);
	var self = this;
	var loading = function (){
		jQuery("#divLoadingContent").show();

		var longLoad = function () {
			jQuery("#divLoadingContent").hide();
			jQuery("#divLongLoading").show();
			jQuery("#em_upload_boutons").hide();
		};
		this.customSettings.loadingTimeout = setTimeout(function () {
				longLoad.call(self)
			},
			6000
		);
	};

	this.customSettings.loadingTimeout = setTimeout(function () {
			loading.call(self);
		},
		2000
	);
}

// Appelé à la fin du chargement de SWFupload
/**
 * Fin du chargement de swfUpload:
 * -* On cache les messages de loading
 * -* On crée les boutons avec jQuery
 */
function swfupload_swfUploadLoaded() {
	clearTimeout(this.customSettings.loadingTimeout);
	jQuery("#divLoadingContent,#divLongLoading,#divAlternateContent").hide();
	$('.fileinput-button span,.btn').button();
}

/**
 * fileQueued : fonction appelée lors de l'ajout d'un ou plusieurs fichiers dans la file d'attente
 * @param file object Informations sur le flash
 * @return
 */
function swfupload_fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus('queued',locale.emballe_medias_langue.pending);
		progress.toggleCancel(true, this);
		//jQuery('#'+this.file.id).find('.ui-progressbar').progressbar(
        //        'value',
        //        parseInt(100, 10)
        //    );
		jQuery("#"+this.customSettings.progressTarget).fadeIn('slow');

	} catch (ex) {
		this.debug(ex);
	}
}

function swfupload_fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			var message_alert = locale.emballe_medias_langue.queue_limit_exceeded+"\n";
			if(message === 0){
				message_alert += locale.emballe_medias_langue.queue_limit_reached;
			}else if(message == 1){
				message_alert += locale.emballe_medias_langue.queue_limit_un;
			}else{
				message_alert += locale.emballe_medias_langue.queue_limit_max+" : "+message;
			}
			alert(message_alert)
			return;
		}

		jQuery("#"+this.customSettings.progressTarget).fadeIn();

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			var error = '<strong>'+locale.emballe_medias_langue.too_big_file+'</strong><br /> '+locale.emballe_medias_langue.max_file_size+''+this.settings.file_size_limit;
			progress.setStatus('queue_error',error);
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus('queue_error',locale.emballe_medias_langue.zero_byte_files);
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus('queue_error',locale.emballe_medias_langue.invalid_file_type);
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus('queue_error',locale.emballe_medias_langue.unhandled_error);
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}



function swfupload_uploadStart(file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		isUploading = true;
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		jQuery('.em_charger_fichiers').tabs('disable',1);
		progress.setStatus('uploading',locale.emballe_medias_langue.uploading);
		progress.toggleCancel(true, this);
	}
	catch (ex) {}

	return true;
}
/**
 *
 * Fonction uploadProgress
 *
 * @param file
 * @param bytesLoaded Nombre de bytes mis en ligne (Donné par le swf de SWFupload)
 * @param bytesTotal Nombre de bytes total du fichier (Donné par le swf de SWFupload)
 * @return
 */
function swfupload_uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var currentTime = new Date();

		/**
		 * Calcul du pourcentage effectué :
		 * nombre de bytes chargés divisé par le nombre total de bytes multiplié par 100
		 */
		if((typeof(bytesLoaded) != 'number') || (bytesLoaded == '0'))
			bytesLoaded = 1;
		if(typeof(bytesTotal) != 'number')
			bytesTotal = 1;
		
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var infos = infos_upload(currentTime,iTime,percent,bytesLoaded,bytesTotal);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent,infos.cur_kb,infos.total_kb,infos.kbs,infos.time_left,infos.time_passed);
		if(percent == '100'){
			progress.setStatus('analyzing',locale.emballe_medias_langue.analyze_document);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function swfupload_uploadError(file, errorCode, message) {
	try {
		isUploading = false;
		jQuery('.em_charger_fichiers').tabs('enable',1);
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
		//jQuery('#formulaire_upload_medias').hide();
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus('error',locale.emballe_medias_langue.upload_error);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus('error',locale.emballe_medias_langue.upload_failed);
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus('error',locale.emballe_medias_langue.server_io_error);
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus('error',locale.emballe_medias_langue.security_error);
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus('error',locale.emballe_medias_langue.upload_limit_exceeded);
			alert(locale.emballe_medias_langue.upload_limit_exceeded);
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus('error',locale.emballe_medias_langue.failed_validation);
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			progress.setStatus('error',locale.emballe_medias_langue.cancelled);
			progress.setCancelled(file);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus('error',locale.emballe_medias_langue.stopped);
			break;
		default:
			progress.setStatus('error',locale.emballe_medias_langue.unhandled_error+": " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function swfupload_uploadComplete(file) {
	//if (this.getStats().files_queued === 0) {
	//	
	//}
}

/**
 *
 * queueComplete
 *
 * @param numFilesUploaded
 * @return
 */
function swfupload_queueComplete(numFilesUploaded) {
	var status = jQuery("#divStatus");
	var uploaded = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
	status.html(uploaded);
}

/**
 * Handler de swfupload au moment de la fermeture du navigateur de fichiers
 * @param {int} numFilesSelected : le nombre de fichiers sélectionnés
 * @param {int} numFilesQueued : le nombre de fichiers dans la queue
 */
function swfupload_fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if(numFilesSelected > 0){
			var verif = emballe_medias_verifier_upload();
			if (verif.upload_ok == true) {
				this.startUpload();
			} else {
				this.cancelUpload();
				alert(locale.emballe_medias_langue.queue_limit_exceeded);
				window.location.reload(true);
			}
		}
	} catch (ex)  {
		this.debug(ex);
	}
}