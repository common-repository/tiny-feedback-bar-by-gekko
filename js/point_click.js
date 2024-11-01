jQuery(function(){
    
	jQuery("body").addClass("context-menu-one");
	
	jQuery.contextMenu({
        selector: '.context-menu-one', 
        callback: function(key, options) {
    		tfbProcess(key);
        },
        items: {
            "task": {name: "Create Task", icon: "tfb-task-icon"},
            "bug": {name: "Report Bug", icon: "tfb-bug-icon"},
            "nfr": {name: "New Feature Request", icon: "tfb-nfr-icon"}
        }
    });
	
	jQuery('a.cluetip').cluetip({cluetipClass: 'cluetip-rounded', dropShadow: false, activation: 'click'});
});


jQuery(document).ready(function() {
	
	jQuery('#tfbw').val(jQuery(window).width());
	tfb_get_markings();
	jQuery('body').on('mousemove', function() {
		jQuery('a.cluetip').cluetip({cluetipClass: 'cluetip-rounded', sticky: true, dropShadow: false});
	});
});

jQuery(window).resize(function() {
	jQuery('#tfbw').val(jQuery(window).width());
	jQuery('#tfbmarkings').html('');
	tfb_get_markings();
})

jQuery(document).click(function(event) { 
    if(jQuery(event.target).parents().index(jQuery('.ui-widget')) == -1) {
        if(jQuery('.ui-widget').is(":visible")) {
            jQuery('.ui-widget').hide()
        }
    }        
})

if (!document.addEventListener) {
    window.attachEvent("keydown", function(event) {
	  if (event.keyCode == 18) {
		var value = jQuery('#inpagetodosstate').val();
		if (value == '') {
			jQuery('.inpagetodos').hide();
			jQuery('#inpagetodosstate').val('none');
		} else {
			jQuery('.inpagetodos').show();
			jQuery('#inpagetodosstate').val('');
		}
	  }
	}, false);
} else {
    window.addEventListener("keydown", function(event) {
	  if (event.keyCode == 18) {
		var value = jQuery('#inpagetodosstate').val();
		if (value == '') {
			jQuery('.inpagetodos').hide();
			jQuery('#inpagetodosstate').val('none');
		} else {
			jQuery('.inpagetodos').show();
			jQuery('#inpagetodosstate').val('');
		}
	  }
	}, false);
}