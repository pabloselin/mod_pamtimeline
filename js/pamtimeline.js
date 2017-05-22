function startTimeline(container, timeline_content) {
  
  var timeline_json = JSON.parse(timeline_content, function(key, value) {
		
		if(key === 'text' && typeof value === 'string') {

			return decodeEntities(value);			

		} else {

			return value;	

		}
		
	});

	var timeline_options = {
		debug: false,
		timenav_position: 'bottom',
    language: 'es',
    height: 600,
    timenav_height: 200,
    scale_factor: 1,
    start_at_slide: 1,
    zoom_sequence: [1, 2, 3, 5, 8, 13, 21, 34, 55, 89]
	}
  
  window.timeline = new TL.Timeline(container, timeline_json, timeline_options);
    
}

jQuery(document).ready(function($) {
	console.log('Timeline Init');

  //Tab control para eras
  var eratab = $('.era-tab');
  var erabtn = $('.erabtn');
  var firsteracontainer = $('#era1').attr('data-eracontainer');
  var firsteracontent = json_content[$('#era1').attr('data-dataid')];


  eratab.hide();
  
  $('#era1').show().addClass('active');
  $('.erabtn-1').addClass('active');
  
  startTimeline(firsteracontainer, firsteracontent);
  $('.tl-timegroup').eq(1).addClass('grupo-eventos-globales');
  $('.tl-timegroup').eq(2).addClass('grupo-eventos-latinos');

  erabtn.on('click', function(event) {

    event.preventDefault();
    var thiseracontainerplain =  $(this).attr('data-target');
    var thiseracontainer = $( '#' + thiseracontainerplain);
    var thiseratimelinecontainer = thiseracontainer.attr('data-eracontainer');
    var thiseradata = json_content[thiseracontainer.attr('data-dataid')];

    if($(this).hasClass('active')) {
      
      return false;
    
    } else {

    eratab.removeClass('active').hide();
    erabtn.removeClass('active');

    thiseracontainer.show().addClass('active');

    startTimeline(thiseratimelinecontainer, thiseradata);
    
    $(this).addClass('active');
    
    }

  });

});