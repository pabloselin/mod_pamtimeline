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

jQuery(document).ready(function($) {
	console.log('Timeline Init');
});