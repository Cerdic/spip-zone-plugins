function toggle_by_id(m_id){
	$('#'+m_id).toggle('slow');
}
var actived_point=Array();
function active_zone(m_id,m_src,m_src_normal){
	if (!actived_point[m_id]) actived_point[m_id]=0;
	if(actived_point[m_id]==0){
		$('#map_obj_'+m_id).src(m_src);
	}
	actived_point[m_id]++;
}
function desactive_zone(m_id,m_src_normal){
	actived_point[m_id]--;
	if(actived_point[m_id]==0) {
		$('#map_obj_'+m_id).src(m_src_normal);
	}
}
function active_point(m_id,m_src,m_src_normal,speed){
	if (!actived_point[m_id]) actived_point[m_id]=0;
	if(actived_point[m_id]==0){
		$('#map_obj_'+m_id).src(m_src);
		$('#map_obj_div_'+m_id).addClass('map_obj_on');
		$('#map_objdiv_'+m_id).show(speed);
	}
	actived_point[m_id]++;
}
function desactive_point(m_id,m_src_normal){
	actived_point[m_id]--;
	if(actived_point[m_id]==0) {
		$('#map_obj_'+m_id).src(m_src_normal);
		$('#map_objdiv_'+m_id).hide();
		$('#map_obj_div_'+m_id).removeClass('map_obj_on');
	}
}
