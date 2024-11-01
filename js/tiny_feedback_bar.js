
function tfbProcess(type) {
	
	if (type == 'task') {
		tfb_markthespot('task');
	} else if (type == 'bug') {
		tfb_markthespot('bug');
	} else if (type == 'nfr') {
		tfb_markthespot('nfr');
	} else {
		
	}
}

function tfbToggleTodos() {
	
	var elements = new Array();
	elements = document.getElementsByClassName('inpagetodos');
	var curr_state = document.getElementById('inpagetodosstate').value;

	for (var i=0;i<elements.length;i++) {
		 if (curr_state == '') {
		 	elements[i].style.display = "none";
		 } else {
			elements[i].style.display = "block";
		 }
	}
	if (curr_state == '') {
		document.getElementById('inpagetodosstate').value = 'none';
	} else {
		document.getElementById('inpagetodosstate').value = '';
	}
}

var u;

function tfb_start(pageid) {
	
	tfb_ajax('getlogo', 'tfb_logo', '', '', '', '', '');
	tfb_ajax('getbranding', 'tfb_branding', '', '', '', '', '');
	tfb_ajax('gettodos', 'tfb_todos', pageid, '', '', '', '');
}

function tfb_build(url, page_id) {
	
	u = url;
	
	if (page_id == '0') {
		
		document.write('<!-- Feedback Bar begin --><div class="tiny_feedback_bar"><div class="tfb_left"><div class="tfb_left_logo"><span name="tfb_logo" id="tfb_logo">&nbsp;</span></div><div class="tfb_divider_left">&nbsp;</div><div class="tfb_left_tfb"><a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank" class="tfb_orange"><span class="tfb_orange">Tiny</span></a> <a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank" class="tfb_blue vtip">Feedback Bar</a></div><div class="tfb_divider_left">&nbsp;</div><div class="tfb_left_pb" style="padding-left:15px;"><span name="tfb_branding" id="tfb_branding"></span></div></div><div class="tfb_right"><div class="tfb_right_td" style="padding-left:15px;"><span name="tfb_todos" id="tfb_todos"></span></div></div></div></div><script language="javascript">tfb_start(\'0\');</script><input name="tfbw" id="tfbw" type="hidden" value="0" /><input name="tfbx" id="tfbx" type="hidden" value="0" /><input name="tfby" id="tfby" type="hidden" value="0" /><input name="inpagetodosstate" id="inpagetodosstate" type="hidden" value="" /><br><br><br><style type="text/css" media="screen">body { position: relative; }</style>');
	} else {
		document.write('<!-- Feedback Bar begin --><div class="tiny_feedback_bar"><div class="tfb_left"><div class="tfb_left_logo"><span name="tfb_logo" id="tfb_logo">&nbsp;</span></div><div class="tfb_divider_left">&nbsp;</div><div class="tfb_left_tfb"><a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank" class="tfb_orange"><span class="tfb_orange">Tiny</span></a> <a href="http://www.tinyfeedbackbar.com/" title="Tiny Feedback Bar: feedback made easy" target="_blank" class="tfb_blue vtip">Feedback Bar</a></div><div class="tfb_divider_left">&nbsp;</div><div class="tfb_left_pb" style="padding-left:15px;"><span name="tfb_branding" id="tfb_branding"></span></div></div><div class="tfb_right"><div class="tfb_right_status nfr_link"><a href="Javascript:tfb_exec(5,' + page_id + ');" class="tfb_blue">Status</a></div><div class="tfb_divider_right">&nbsp;</div><div class="tfb_right_nfr nfr_link"><a href="Javascript:tfb_exec(2,' + page_id + ');" class="tfb_blue">New Feature Request</a></div><div class="tfb_divider_right">&nbsp;</div><div class="tfb_right_bug nfr_link"><a href="Javascript:tfb_exec(4,' + page_id + ');" class="tfb_blue">Report Bug</a></div><div class="tfb_divider_right">&nbsp;</div><div class="tfb_right_task nfr_link"><a href="Javascript:tfb_exec(3,' + page_id + ');" class="tfb_blue">Create Task</a></div><div class="tfb_divider_right">&nbsp;</div><div class="tfb_right_nfr nfr_link"><a href="Javascript:tfbToggleTodos();" class="tfb_blue">Show/hide all</a></div><div class="tfb_divider_right">&nbsp;</div><div class="tfb_right_td" style="padding-left:15px;"><span name="tfb_todos" id="tfb_todos"></span></div></div></div><script language="javascript">tfb_start(' + page_id + ');</script><input name="tfbw" id="tfbw" type="hidden" value="0" /><input name="tfbx" id="tfbx" type="hidden" value="0" /><input name="tfby" id="tfby" type="hidden" value="0" /><input name="inpagetodosstate" id="inpagetodosstate" type="hidden" value="" /><br><br><br><style type="text/css" media="screen">body { position: relative; }</style>');
	}
}