var FINCR = 1;
jQuery('#slideshowtype').change(function(){
	if(jQuery(this).val() == 'CUSTOM'){
		jQuery("#pix_fileset_slideshow").hide();
		jQuery("#pix_custom_slideshow").show();
	}
	if(jQuery(this).val() == 'FILESET'){
		jQuery("#pix_custom_slideshow").hide();
		jQuery("#pix_fileset_slideshow").show();
	}
});
PixSetFINCR=function(fincr){
	FINCR = parseInt(fincr)+1;
}
PixAddSlide = function(){
   
	var fidhtml = '<div class="mage25-file-manager-select"><input type="hidden" value="0" name="fID[]" id="pix_filepicker_'+FINCR+'_value"><span id="pix_filepicker_'+FINCR+'_image"><a href="'+SITE_URL+DIRNAME_APP+'/dispatcher.php?popup_action=file_manager&parent_selector='+FINCR+'&magecon_tocken='+SECURITY_TOCKEN+'" class="mage25_file_selector" dialog-modal="true" dialog-width="900" dialog-height="600" dialog-title="File Manager" >Choose Image</a></span></div>';
	
	
	var tabledata='<table class="table table-bordered" width="90%"><tr><td align="right"><button type="button" class="btn-warning pix_slide_move_up">Move Up<i class="icon-arrow-up icon-white"></i></button> <button type="button" class="btn-info pix_slide_move_down">Move Down<i class="icon-arrow-down icon-white"></i></button><a href="javascript:void(0)" onClick="PixRemoveSlideRow(this)" class="btn btn-xs btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span> remove</a></td></tr><tr><td><div id="mage25-block-fields"><div class="mage25-block-field-group"><div class="clearfix"><label class="control-label" for="fid">Image</label><div class="input">'+fidhtml+'</div></div><div class="clearfix"><label class="col-md-4 control-label" for="title">Title</label><div class="input"><input id="title" name="title[]" type="text" placeholder="Title" class="form-control input-md"></div></div><div class="clearfix"><label class="col-md-4 control-label" for="description">Description</label><div class="input"><textarea class="form-control" id="description" name="description[]"></textarea></div></div><div class="clearfix"><label class="col-md-4 control-label" for="url[]">Url</label><div class="input"><input id="url[]" name="url[]" type="text" placeholder="Url" class="form-control input-md"></div></div><div class="clearfix"><label class="col-md-4 control-label" for="link_text">Link Text</label><div class="input"><input id="link_text" name="link_text[]" type="text" placeholder="Link Text" class="form-control input-md"></div></div><div class="clearfix"><label class="col-md-4 control-label" for="link_text">Target</label><div class="input"><select name="target[]" class="form-control"><option value="_self">_self</option> <option value="_blank">_blank</option> <option value="_new">_new</option> <option value="_parent">_parent</option> <option value="_top">_top</option></select></div></div></div></div></td></tr></table>';

	jQuery("#pix_slides_ul").prepend(tabledata);
	jQuery("#pix_filepicker_"+FINCR+"_image a.mage25_file_selector").dialog();
	
	FINCR++;
	activateSlideUpDownClick();
}

PixPickFileFromFileManager = function(fincr){
	Pix_LoadFilePickerSelector(SITE_URL+DIRNAME_APP+"/dispatcher.php?popup_action=file_manager&parent_selector=fID"+fincr,'900','700','File Manager');
}

PixRemoveSlideRow = function(obj){
	jQuery(obj).closest('table').remove();
}

jQuery(document).ready(function(){
	
	alert("kjhgkjhgkjhg");
	activateSlideUpDownClick();
	
});

function activateSlideUpDownClick(){
	
	
	jQuery('.pix_slide_move_up').click(function(e){
        var jQueryparent = jQuery(this).closest('table');
        jQueryparent.insertBefore(jQueryparent.prev());           
    
    });
    
    
    jQuery('.pix_slide_move_down').click(function(e){
        var jQueryparent = jQuery(this).closest('table');
        
        jQueryparent.insertAfter(jQueryparent.next());           
    
    });
	
	
}
