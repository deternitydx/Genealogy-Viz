$(document).ready(function() {

	$('.toggle-header').on('click',function(){
		$($(this).attr('href')).slideToggle();
		$(this).parent('div').toggleClass('dropup');
		return false;
	});

	$('#accordion a:first .custom-radio i, #accordion2 a:first .custom-radio i').addClass('custom-radio-selected');
    $('#artifact_type').val('Beads');
    $('#artifact_type_list').html('Beads');
    $('#subset_by').val('Context');
    $('#subset_data_by_list').html('Context');
	$('#accordion h4.panel-title a').on('click', function(){
        $('#accordion h4.panel-title a i').removeClass('custom-radio-selected');
        $(this).find('.custom-radio i').addClass('custom-radio-selected');
        var artifact_type = $(this).find('label').html().split(' ').pop();
        $('#artifact_type').val(artifact_type);
        $('#artifact_type_list').html(artifact_type);
	});
    $('#accordion2 h4.panel-title a').on('click', function(){
        $('#accordion2 h4.panel-title a i').removeClass('custom-radio-selected');
        $(this).find('.custom-radio i').addClass('custom-radio-selected');
        var subset = $(this).find('label').html().split(' ').pop();
        $('#subset_by').val(subset);
        $('#subset_data_by_list').html(subset);
    });

    // function to test existence of items

	$.fn.exists = function(){return this.length>0;}

    // turn select fields into chosen fields

    $("select").each(function() {
        if($(this).attr('id').indexOf("_NUM_") == -1) {
            var chosen_width = 300;
			if($(this).attr('width') > 0) chosen_width = $(this).attr('width');
			var threshold = 1;
			if($(this).hasClass('no-autocomplete')) threshold = 100;
            $(this).chosen({width: chosen_width+"px",disable_search_threshold: threshold});
        }
    });

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
