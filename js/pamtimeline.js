var decodeEntities = (function() {
  // this prevents any overhead from creating the object each time
  var element = document.createElement('div');

  function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
      // strip script/html tags
      str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
      str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
      element.innerHTML = str;
      str = element.textContent;
      element.textContent = '';
    }

    return str;
  }

  return decodeHTMLEntities;
})();

var cleanJson = (function() {
  function cleanJsonString( json_string ) {

    json_string = json_string.replace(/\\n/g, "\\n")  
                             .replace(/\\'/g, "\\'")
                             .replace(/\\"/g, '\\"')
                             .replace(/\\&/g, "\\&")
                             .replace(/\\r/g, "\\r")
                             .replace(/\\t/g, "\\t")
                             .replace(/\\b/g, "\\b")
                             .replace(/\\f/g, "\\f");

    json_string = json_string.replace(/[\u0000-\u0019]+/g,""); 

    return json_string;
  }

  return cleanJsonString;
})();

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
		language: 'es',
		timenav_position: 'bottom',
    height: 400,
    timenav_height: 200,
    scale_factor: 1
	}
  
  window.timeline = new TL.Timeline(container, timeline_json, timeline_options);
    
}

jQuery(document).ready(function($) {
	console.log('Timeline Init');

  //Tab control para eras
  var eratab = $('.era-tab');
  var erabtn = $('.erabtn');
  var firsteracontainer = $('#era-1').attr('data-eracontainer');
  var firsteracontent = json_content[$('#era-1').attr('data-dataId')];

  eratab.hide();
  
  $('#era-1').show().addClass('active');
  $('.erabtn-1').addClass('active');
  
  startTimeline(firsteracontainer, firsteracontent);
  $('.tl-timegroup').eq(1).addClass('grupo-eventos-globales');
  $('.tl-timegroup').eq(2).addClass('grupo-eventos-latinos');

  erabtn.on('click', function(event) {

    event.preventDefault();
    var thiseracontainerplain = $(this).attr('data-target');
    var thiseracontainer = $( '#' + thiseracontainerplain);
    var thiseratimelinecontainer = thiseracontainer.attr('data-eracontainer');
    var thiseradata = json_content[thiseracontainer.attr('data-dataId')];
    console.log(thiseracontainer);

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