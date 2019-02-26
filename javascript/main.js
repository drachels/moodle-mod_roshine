var _options = {
	showKBRow : true,
	showFullScreen : false
	
};

$(document).ready(function(){
    $('#btnOptions').click(function() {
        $('#modalOptions').modal('show');
    });
	
	 $('#btnStatus').click(function() {
        $('#modalLessonComplete').modal('show');
    });

	setOptionModalAttributes();
});

function setOptionModalAttributes() {
	$('#modalOptions').on('shown', function() {
		$('#optKBRow').attr('checked', _options.showKBRow);
		$('#optFullScreen').attr('checked', _options.showFullScreen);
	}); 

	$('#option-cancel').click(function() {
		$('#modalOptions').modal('hide');
		return false;
	});
	
	$('#option-submit').click(function() {
		_options.showKBRow = $('#optKBRow').is(':checked');
		_options.showFullScreen = $('#optFullScreen').is(':checked');
		
		callbackOptionChange(); // Take actions based on new options
		$('#modalOptions').modal('hide');
		return false;
	});
}

// Viet : them vao mot so ham xu ly Fullscreen
function callbackOptionChange() {
	o = _options;
	
	if (o.showKBRow) {

			document.getElementById('keyboardContainerRow').className ="fade in";
			
	} else {
        document.getElementById('keyboardContainerRow').className ="fade";
	}
	
	if (o.showFullScreen) {
		makeFullScreen();
	} else {
		cancelmakeFullScreen() ;
	}
}
function cancelFullScreen(el) {
		var requestMethod = el.cancelFullScreen||el.webkitCancelFullScreen||el.mozCancelFullScreen||el.exitFullscreen;
		if (requestMethod) { // Cancel full screen.
			requestMethod.call(el);
		} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
			var wscript = new ActiveXObject("WScript.Shell");
			if (wscript !== null) {
				wscript.SendKeys("{F11}");
			}
		}
	}

function requestFullScreen(el) {
	// Supports most browsers and their versions.
	var requestMethod = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;

	if (requestMethod) { // Native full screen.
		requestMethod.call(el);
	} else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
		var wscript = new ActiveXObject("WScript.Shell");
		if (wscript !== null) {
			wscript.SendKeys("{F11}");
		}
	}
	return false
}

function makeFullScreen() {
	var elem = document.body; // Make the body go full screen.
	var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) ||  (document.mozFullScreen || document.webkitIsFullScreen);

	if (isInFullScreen) {
	} else {
		requestFullScreen(elem);
	}
	return false;
}
function cancelmakeFullScreen() {
	var elem = document.body; 
	var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) ||  (document.mozFullScreen || document.webkitIsFullScreen);

	if (isInFullScreen) {
		cancelFullScreen(document);
	} else {
	}
	return false;
}