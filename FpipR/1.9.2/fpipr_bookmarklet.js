function fpipr_add_photo(url) {
	var photo1;
	for each(photo in global_photos) {
		photo1 = photo;
		break;
	}
	if(url && photo1)
		window.location = url+'&id='+encodeURIComponent(photo1.id)+'&secret='+encodeURIComponent(photo1.secret);
}

fpipr_add_photo(fpipr_retour);