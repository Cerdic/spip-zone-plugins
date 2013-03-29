// Constructor
// file is a SWFUpload file object
// targetID is the HTML element id attribute that the FileProgress HTML structure will be added to.
// Instantiating a new FileProgress object with an existing file will reuse/update the existing DOM elements

function FileProgress(file, targetID) {
	this.fileProgressID = file.id;
	this.id = this.fileProgressID;
	this.fileProgressWrapper = jQuery('#'+this.fileProgressID)[0];

	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = '<div class="progressWrapper" id="'+this.fileProgressID+'"></div>';
		this.fileProgressElement = '<div class="progress_container" style="display:none"><div class="bg"><span class="bar" style="width: 4px;">0%</span></div></div><div class="upload_info"><span class="filename">'+file.name+'</span><div class="transfer" style="display:none"></div><div class="time_remaining" style="display:none"></div><div class="statut"></div></div>';
		jQuery('#'+targetID).append(this.fileProgressWrapper);
		jQuery('#'+this.fileProgressID).append(this.fileProgressElement);
	}
};

FileProgress.prototype.setProgress = function (percentage,loaded,total,kbs,time_left,time_passed) {
	var id = this.fileProgressID;
	jQuery('#'+id+' .bg .bar').css('width',percentage+'%').html(percentage+'%');
	jQuery('#'+id+' .transfer').html(loaded+' / '+total+' ('+kbs+' kb/s)');
	jQuery('#'+id+' .time_remaining').html(time_left+' '+emballe_medias_langue.temps_restant+' ('+time_passed+' '+emballe_medias_langue.temps_passe+')');
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
	conteneur.addClass('blue');
	jQuery('#'+id+' .bg .bar').toggleClass('progressBarComplete').css('width','');
	setTimeout(function () {
		conteneur.fadeOut('slow',function(){
			conteneur.remove();
		}).removeClass('blue');
	}, 5000);
};

FileProgress.prototype.setError = function () {
	var id = this.fileProgressID;
	jQuery('#'+id).parent().addClass('red');
	jQuery('#'+id+' .bg .bar').toggleClass('progressBarError').css('width','');
	setTimeout(function () {
		jQuery('#'+id).parent().fadeOut('slow',function(){
			jQuery('#'+id).remove();
		}).removeClass('red');
	}, 5000);
};

FileProgress.prototype.setCancelled = function () {
	var id = this.fileProgressID;
	jQuery('#'+id).addClass('red');
	jQuery('#'+id+' .bg .bar').toggleClass('progressBarError').css('width','');
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
	jQuery('#'+id+' .statut').html(emballe_medias_langue.statut+''+statut_message);
	if(statut == 'analyzing'){
		jQuery('#'+id).animeajax();
		this.toggleCancel(true);
	}
};

FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
	var id = this.fileProgressID;
	if (swfUploadInstance) {
		jQuery('#'+id+' a.cancel').remove();
		jQuery('#'+id+' .upload_info').prepend('<a href="#" class="cancel">'+emballe_medias_langue.cancel_upload+'</a>');
		jQuery('#'+id+' a.cancel').click(function () {
			swfUploadInstance.cancelUpload(id);
			return false;
		});
	}
	else{
		jQuery('#'+id+' a.cancel').remove();
	}
};