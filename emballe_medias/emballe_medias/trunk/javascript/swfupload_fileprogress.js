// Constructor
// file is a SWFUpload file object
// targetID is the HTML element id attribute that the FileProgress HTML structure will be added to.
// Instantiating a new FileProgress object with an existing file will reuse/update the existing DOM elements

function FileProgress(file, targetID) {
	this.fileProgressID = file.id;
	this.id = this.fileProgressID;
	this.fileProgressWrapper = jQuery('#'+this.fileProgressID);
	if (this.fileProgressWrapper.size() < 1) {
		this.fileProgressWrapper = '<div class="template-upload fade upload_info progressWrapper" id="'+this.fileProgressID+'"></div>';
		this.fileProgressElement = '<div class="progress_container"><span class="filename name">'+file.name+'</span><div class="upload_info"><div class="transfer"></div><div class="time_remaining"></div><div class="statut"></div><div><div class="progress progress_container ui-progressbar .progress-animated progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div></div></div></div>';
		jQuery('#'+targetID).append(this.fileProgressWrapper);
		jQuery('#'+this.fileProgressID).append(this.fileProgressElement);
		this.fileProgressWrapper.find('.progress_container').fadeIn();
	}
};

FileProgress.prototype.setProgress = function (percentage,loaded,total,kbs,time_left,time_passed) {
	var id = this.fileProgressID;
	jQuery('#'+id).find('.ui-progressbar').progressbar();
	jQuery('#'+id).find('.ui-progressbar').progressbar('value',parseInt(percentage));
	jQuery('#'+id+' .transfer').html(loaded+' / '+total+' ('+kbs+' kb/s)');
	jQuery('#'+id+' .time_remaining').html(time_left+' '+locale.emballe_medias_langue.temps_restant+' ('+time_passed+' '+locale.emballe_medias_langue.temps_passe+')');
	if(percentage == '100'){
		jQuery('#'+id+' .transfer').html(loaded+' / '+total);
	}
};

FileProgress.prototype.setComplete = function (queued) {
	var id = this.fileProgressID;
	var conteneur = jQuery('#'+id);
	if(queued === 0){
		conteneur = jQuery('#'+id).parent();
	}
	conteneur.addClass('complete');
};

FileProgress.prototype.setError = function () {
	var id = this.fileProgressID;
	jQuery('#'+id).addClass('red');
	jQuery('#'+id+' .image_loading').detach(); 
	jQuery('#'+id).children().css('opacity', 1); 
};

FileProgress.prototype.setCancelled = function () {
	var id = this.fileProgressID;
	jQuery('#'+id).addClass('red');
	setTimeout(function () {
		jQuery('#'+id).fadeOut('slow',function(){
			jQuery('#'+id).remove();
		}).removeClass('red');
	}, 2000);
};

/**
 * On change le statut de l'élément de la queue
 *
 * Statuts possible : queued, queue_error, uploading, complete, error
 *
 */
FileProgress.prototype.setStatus = function (statut,statut_message) {
	var id = this.fileProgressID;
	if(statut == 'uploading'){
		jQuery('#'+id+' .progress_container,#'+id+' .transfer, #'+id+' .time_remaining').fadeIn();
	}
	jQuery('#'+id+' .statut').html(locale.emballe_medias_langue.statut+''+statut_message);
	if(statut == 'analyzing'){
		jQuery('#'+id).animeajax();
		//this.toggleCancel(true);
	}
};

FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
	var id = this.fileProgressID;
	if (swfUploadInstance) {
		jQuery('#'+id+' div.cancel').detach();
		jQuery('#'+id).append('<div class="cancel"><button title="'+locale.emballe_medias_langue.cancel_upload+'" class="btn btn-warning "><span class="btn">'+locale.emballe_medias_langue.cancel_upload+'</span></button></div>');
		jQuery('#'+id+' div.cancel button').button();
		jQuery('#'+id+' div.cancel button').click(function () {
			swfUploadInstance.cancelUpload(id);
			return false;
		});
	}
	else{
		jQuery('#'+id+' div.cancel').detach();
	}
};