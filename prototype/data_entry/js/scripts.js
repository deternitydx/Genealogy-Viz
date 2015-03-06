$(document).ready(function() {

	$('.toggle-header').on('click',function(){
		$($(this).attr('href')).slideToggle();
		$(this).parent('div').toggleClass('dropup');
		return false;
	});

    // function to test existence of items

	$.fn.exists = function(){return this.length>0;}


    // variable for delete confirmations

	var completeness_for_delete;

    // throw message if unsaved changes when moving away from page
    // handle the submision of the record to be saved

	if($('#button-record-save').exists()) {
		var unsaved = false;
		$('#button-record-save').click(function(){
            // save the data
            $.post("save.php", $("#nauvoo_form").serialize(), function (data) {
                // what to do after the save
                if (data == "success") {
                    $('.alert-success').slideDown();
                    setTimeout(function(){
                        $('.alert-success').slideUp();
                    }, 3000);
                }
                unsaved = false;
            });
            return false;
		});
		$(":input").change(function(){
			unsaved = true;
		});
		function unloadPage(){ 
			if(unsaved){
				var message = 'You have made changes to this form. To avoid losing data, please stay on this page and select the "SAVE" button before leaving.',
				e = e || window.event;
				// For IE and Firefox
				if (e) { e.returnValue = message; }
				// For Safari
				return message;
			}
		}
		window.onbeforeunload = unloadPage;
	}

	if (window.PIE) {
		$('.ie-fix').each(function() {
			PIE.attach(this);
		});
	}
	if ($('.tabs .section').exists()){
		$('.tabs .section:last-child').addClass('last-child');
	}
	if ($('.fancybox').exists()){
		$('.fancybox').fancybox({
			prevEffect : 'fade',
			nextEffect : 'fade',
		});
	}
	if ($('.popover-area').exists()){
		$('.popover-area .btn').popover();
		$('.popover-area .btn').click(function () {
			$('.popover-area .btn').not(this).popover('hide');
			$('.popover-area .btn').removeClass('active');
			$(this).addClass('active');
		});
	}
	if ($('.panel-group .panel').exists()){
		$('.panel-group .panel:last-child').addClass('last-child');
	}
   

    // turn select fields into select2 fields
    selectsToSelect2();
    // Load in places
    loadPlacesSelect2();
    // Load in marriages
    loadMarriagesSelect2();

    // Code to handle adding new marriages to the page
    var marriageid = 2;
    if ($('#button-add-marriage').exists()){
		$('#button-add-marriage').click(function(){
			var text = $('#marriage-entry-hidden').clone();
            var html = text.html().replace(/ZZ/g, marriageid);
            $('#marital-sealings-formarea').append(html);
            marriageid = marriageid + 1;
            return false;
		});
	}

    // Code to handle adding new nonmaritals to the page
    var nonmaritalid = 2;
    if ($('#button-add-nonmarital').exists()){
		$('#button-add-nonmarital').click(function(){
			var text = $('#nonmarital-entry-hidden').clone();
            var html = text.html().replace(/ZZ/g, nonmaritalid);
            $('#nonmarital-sealings-formarea').append(html);
            nonmaritalid = nonmaritalid + 1;
            return false;
		});
	}

    // Code to handle adding new rites to the page
    var riteid = 2;
    if ($('#button-add-rite').exists()){
		$('#button-add-rite').click(function(){
			var text = $('#rite-entry-hidden').clone();
            var html = text.html().replace(/ZZ/g, riteid);
            $('#temple-rites-formarea').append(html);
            riteid = riteid + 1;
            return false;
		});
	}

    // Code to handle adding new names to the page
    var nameid = 3;
    if ($('#button-add-name').exists()){
		$('#button-add-name').click(function(){
			var text = $('#name-entry-hidden').clone();
            var html = text.html().replace(/ZZ/g, nameid);
            $('#alternative-names').append(html);
            nameid = nameid + 1;
            return false;
		});
	}


});

$(document).ready(function(){
    // Fade effect
    var _parentFade = '.fade-block';
    var _linkFade = '.open-close';
    var _fadeBlock = '.slide-block';
    var _openClassF = 'active';
    var _textOpenF = 'Open block';
    var _textCloseF = 'Close block';
    var _durationFade = 300;
	
    $(_parentFade).each(function(){
		if (!$(this).is('.'+_openClassF)) {
			$(this).find(_fadeBlock).css('display','none');
		}
    });
    $(_linkFade,_parentFade).click(function(){
		if ($(this).parents(_parentFade).is('.'+_openClassF)) {
			$(this).parents(_parentFade).removeClass(_openClassF);
			$(this).parents(_parentFade).find(_fadeBlock).fadeOut(_durationFade);
		} else {
			$(this).parents(_parentFade).addClass(_openClassF);
			$(this).parents(_parentFade).find(_fadeBlock).fadeIn(_durationFade);
		}
		return false;
    });
    
});


// Helper Function: load places into the select boxes
function loadPlacesSelect2() {
    $("select").each(function() {
        // Only modify the places
        if($(this).attr('id').indexOf("place_id") != -1) {
            $(this).select2({
                ajax: {
                    url: "../api/get_places.php",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data, page) {
                        return { results: data };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 4,
                width: '400px',
                theme: 'classic'
            });
        }
    });
}

// Helper Function: load marriages into the select boxes
function loadMarriagesSelect2() {
    $("select").each(function() {
        // Only modify the places
        if($(this).attr('id').indexOf("marriage_id") != -1) {
            $(this).select2({
                ajax: {
                    url: "../api/get_marriages.php",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data, page) {
                        return { results: data };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 4,
                width: '400px',
                theme: 'classic'
            });
        }
    });
}

// Helper Function: Make Selects Select2 objects
function selectsToSelect2() {
    $("select").each(function() {
        // Only modify non-places
        if($(this).attr('id').indexOf("place_id") == -1 && $(this).attr('id').indexOf("marriage_id") == -1) {
            $(this).select2({
                width: '400px',
                theme: 'classic'
            });
        }
    });
}
