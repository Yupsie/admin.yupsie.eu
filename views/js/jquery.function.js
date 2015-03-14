$().ready(function() {

	$("textarea").tinymce({
//		base_url : "http://www.yupsie.eu/data/",
//		plugins : "inlinepopups,noneditable,style,",
//		content_css : "http://www.bsr-venlo.nl/views/css/style.css",
		menubar: false,
	    theme: "modern",
	    language: "nl",
	    plugins: [
	        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
	        "searchreplace wordcount visualblocks visualchars code fullscreen",
	        "insertdatetime media nonbreaking save table contextmenu directionality",
	        "emoticons template paste textcolor colorpicker textpattern"
	    ],
	    toolbar1: "insertfile undo redo | cut copy paste | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | sub sup | tablecontrols | search replace | blockquote | anchor cleanup help code | emoticons charmap | removeformat",
	    image_advtab: true,
	    templates: [
	        {title: 'Test template 1', content: 'Test 1'},
	        {title: 'Test template 2', content: 'Test 2'}
	    ],
	    file_browser_callback: yFiles
	});


	//	Use Ctrl + S to save records
	var isCtrl = false; 
	$(document).keyup(function (e) { 
		if (e.which == 17) {
			isCtrl = false;
		}
	}).keydown(function (e) { 
		if (e.which == 17) {
			isCtrl = true;
		}
		if (e.which == 83 && isCtrl == true) { 
			$("form").submit();
		} 
	});


	//	Append stylesheet to iframe contents
	$("iframe.content").each(function() {
		$(this).attr("srcdoc", '<link rel="stylesheet" type="text/css" href="/views/css/cms.css"><style type="text/css">html,body {background-color:transparent;color:#444444;}</style>' + $(this).attr("srcdoc"));
	});

});

//	Callback function for TinyMCE
function yFiles(field_name, url, type, win) {

	//	Show the dialog when the browse button is clicked and show the required filetype in the header
	$('.dialog').show();
	$('.dialog').find('span').html('[Bestandstype: ' + type + ']');

	//	Select a file when the containing table row is clicked and close the dialog
	$('.dialog iframe').find('tr').click(function() {console.log('test');
		$('#' + field_name).val($(this).find('a.file').attr('href'));
		$('.dialog').hide();
	});

	//	When there is no file selected, the dialog can be closed by clicking the header
	$('.dialog').find('h2').click(function() {
		$('.dialog').hide();
	});
}