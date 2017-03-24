function pamsigmaReload(tax, persons, currentperson, currentpersondata, containerID) {
	
	// var json_relations_raw = cleanJson(persons);
	// var json_relations = JSON.parse( json_relations_raw );
	// var current_person = currentperson;
	// var current_person_data = JSON.parse(currentpersondata);
	
	var containerEl = jQuery('#' + containerID);
	//console.log(containerEl);
	var highlight = jQuery('#' + containerEl.attr('data-highlight') );
	var subhighlight = jQuery('#' + containerEl.attr('data-subhighlight') );
	
	containerEl.empty();
	highlight.empty();
	subhighlight.empty();
	
	var current_person_taxlabel = 'person_' + tax;
	//console.log(current_person_taxlabel, tax);
	var current_tax_filter = current_person_data[current_person_taxlabel][tax];
	
	//poner los items señalados en la sección highlight
	
	highlight.append('<span class="tagporter">' + current_person_data.person_name + ' &gt; </span>');
	
	for(var i = 0; i < current_tax_filter.length; i++) {
		highlight.append('<span class="tagtax" data-tagid="' + tax + '-' + current_tax_filter[i].fieldvalueid + '">' + current_tax_filter[i].fieldvaluename + '</span>');
	}
	
	var langids = [];
	var langidsmap = current_tax_filter.map(function(lang) {
		langids.push(parseInt(lang.fieldvalueid, 10));
	});
	
	//console.log(langids);
	
	var graph_rel = {
		nodes: [],
		edges: []
	}
	
	var matchedpersons = [];
	
	persons.map(function(person) {
		
		var match = false;
		var matchids = {
			languages: [],
			tools: [],
			themes: []
		};
		
		
		if(person[current_person_taxlabel].length !== 0) {
			
			var thispersonlang = person[current_person_taxlabel][tax];
			
			thispersonlang.map(function(lang, idx) {
				
				if(langids.indexOf(parseInt(lang.fieldvalueid, 10)) != -1) {
					match = true;
					matchids[tax].push(lang.fieldvalueid);
					//console.log(langids, lang.fieldvalueid );
				};
			});
			
			if(match == true) {
				matchedpersons.push(person);
				//console.log(matchedpersons);
			}
		}
		
	});
	
	//console.log('matchs', matchedpersons);
	
	for( var i = 0; i < matchedpersons.length; i ++) {
		
		if( current_person == matchedpersons[i].person_id) {
			var cursize = 2;
			var curcolor = '#ff0000';
			var artistcolor = '#ff0000';
		} else {
			var cursize = 2;
			var curcolor = '#555';
			var artistcolor = 'default';
		}
		
		graph_rel.nodes.push({
			id: matchedpersons[i].person_id,
			label: matchedpersons[i].person_name,
			x: i * Math.random(),
			y: i * Math.random(),
			size:cursize,
			color: curcolor,
			labelcolor: artistcolor,
			languages: matchedpersons[i].person_languages.languages,
			themes: matchedpersons[i].person_themes.themes,
			tools: matchedpersons[i].person_tools.tools
		});
		
		graph_rel.edges.push({
			id: 'edge-' + i,
			source: matchedpersons[i].person_id,
			target: current_person,
			color: '#ccc',
			size: 3
		});
	}
	
	
	
	var rels = new sigma({
		graph: graph_rel,
		renderers: [{
			container: containerID,
			type: 'canvas'
		}],
		settings: {
			sideMargin: 2,
			defaultLabelColor: '#555',
			zoomMin: 1.2,
			zoomMax: 2
		}
	});
	
	rels.bind('overNode', function(e) {
		var tags = e.data.node[tax];
		subhighlight.empty();
		subhighlight.append('<span class="tagporter">' + e.data.node.label + ' &gt; </span>');
		
		jQuery('span.tagtax', highlight).removeClass('matched');
		
		for(var i = 0; i < tags.length; i++) {
			var tagname = tags[i].fieldvaluename;
			
			var matched = 'unmatched';
			
			if(langids.indexOf(parseInt(tags[i].fieldvalueid)) != -1) {
				matched = 'matched';
				
				jQuery('span.tagtax[data-tagid="' + tax + '-' + tags[i].fieldvalueid + '"]', highlight).addClass('matched');
			}
			
			subhighlight.append('<span class="tagtax ' + matched + '" data-tagid="' + tax + '-' + tags[i].fieldvalueid + '">' + tagname + '</span>');
		}
		
		
	});
	
	//var dragListener = sigma.plugins.dragNodes(rels, rels.renderers[0]);
	rels.startForceAtlas2({
		slowDown: 1
	});
	
	//console.log(rels.isForceAtlas2Running());
	
	rels.refresh();
	
	rels.stopForceAtlas2();
	
	
}