//Sigma custom renderers

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
	
	console.log(rels.isForceAtlas2Running());
	
	rels.refresh();
	
	rels.stopForceAtlas2();
	
	
}

function pamsigmaAppend(node) {
	//Añade nuevos links
}

function pamsigmaGlobal(persons, containerID, tax, singleperson) {

	var singlematchedpersons = [];
	var db = new sigma.plugins.neighborhoods();
	
	var containerEl = jQuery('#' + containerID);

	var current_person_taxlabel = 'person_' + tax;

	containerEl.empty();

	var L = 5,
    	N = 25,
    	E = 250;
	
	var graph_rel = {
		nodes: [],
		edges: []
	}

	if(singleperson !== undefined) {
		
		var curperson = pamFindPerson(persons, singleperson).pop();

		if(curperson[current_person_taxlabel].length !== 0) {
			
				var thispersonlang = curperson[current_person_taxlabel][tax];
				

				//Navega por todas las personas
				persons.map(function(person) {
					var matchedpersonids = [];
					var matchids = {
						languages: [],
						tools: [],
						themes: []
					};
					
					//Si la persona tiene algo de la taxonomía sigo
					if(person[current_person_taxlabel].length !== 0) {

						
						var splangs = person[current_person_taxlabel][tax];
						var slangids = [];
						var slangidsmap = splangs.map(function(lang) {
							slangids.push(parseInt(lang.fieldvalueid, 10));
						});
						
						//Busco entre los IDs de la persona seleccionada
						thispersonlang.map(function(lang, idx) {

							//console.log('slangids' + slangids, 'person' + person.person_id);

							//Si es que el ID de la taxonomía de la persona seleccionada está entre los IDs de la persona que estoy iterando

							if(slangids.indexOf(parseInt(lang.fieldvalueid, 10)) !== -1 && parseInt(curperson.person_id, 10) !== parseInt(person.person_id, 10) )  {
								
								//En ese caso pongo la persona en un listado de personas que coinciden, si es que no lo he puesto antes
								
								if( matchedpersonids.indexOf(parseInt(person.person_id, 10)) === -1) {
									person.activeNode = false;
									singlematchedpersons.push(person);
									matchedpersonids.push(parseInt(person.person_id, 10));
								}
								
								//Y guardo un objeto de referencia con los IDs que calzan
								matchids[tax].push(lang.fieldvalueid);
								
							};
						});
						
					}

				});

		}

		//Añado la persona actual al lote de personas que calzan
		
		curperson.activeNode = true;

		singlematchedpersons.push(curperson);

		//reemplazo a las personas que se van a usar
		persons = singlematchedpersons;

	}

	
	for( var i = 0; i < persons.length; i ++) {
		
		var cursize = 0.1;
		var curcolor = '#ff0000';
		var artistcolor = '#ff0000';
		var curlangs = persons[i].person_languages.languages;
		var curthemes = persons[i].person_themes.themes;
		var curtools = persons[i].person_tools.tools;
		var curpersontype = persons[i].person_type.persontype;
		
		graph_rel.nodes.push({
			id: persons[i].person_id,
			label: persons[i].person_name.toUpperCase(),
			x: i * Math.random(),
			y: i * Math.random(),
			circular_x: L * Math.cos(Math.PI * 2 * i / N - Math.PI / 2),
    		circular_y: L * Math.sin(Math.PI * 2 * i / N - Math.PI / 2),
			old_x: i * Math.random(),
			old_y: i * Math.random(),
			grid_x: i % L,
    		grid_y: Math.floor(i / L),
			size:cursize,
			color: curcolor,
			labelcolor: artistcolor,
			languages: curlangs,
			themes: curthemes,
			tools: curtools,
			link: persons[i].person_url,
			image: persons[i].person_thumbnail,
			persontype: curpersontype,
			active: singleperson === undefined ? false : persons[i].activeNode
		});
		
		console.log(persons[i].activeNode);
		
		if(persons[i][current_person_taxlabel].length !== 0) {

		//Busco en todas las otras personas matches de la taxonomía correspondiente
			persons.map(function(person) {

				var matchids = {
					languages: [],
					tools: [],
					themes: []
				};

				var edgeids = [];
				
				//console.log(person);
				
					if(person[current_person_taxlabel].length !== 0) {
						
						var plangs = persons[i][current_person_taxlabel][tax];
						var langids = [];
						var langidsmap = plangs.map(function(lang) {
							langids.push(parseInt(lang.fieldvalueid, 10));
						});
						
						var thispersonlang = person[current_person_taxlabel][tax];
						
						thispersonlang.map(function(lang, idx) {

							if(langids.indexOf(parseInt(lang.fieldvalueid, 10)) !== -1 && person.person_id !== persons[i].person_id) {                     
								
								var edgeid = 'edge-' + i + '-' + person.person_id;

								if( edgeids.indexOf(edgeid) === -1) {

									graph_rel.edges.push({
										id: edgeid,
										source: person.person_id,
										target: persons[i].person_id,
										color: '#ccc',
										size: 3
									});

									edgeids.push(edgeid);

								}

								

								matchids[tax].push(lang.fieldvalueid);
							};
						});
						
					}
				
			});

		}
		
	}
	
	
	var rels = new sigma({
		graph: graph_rel,
		renderers: [{
			container: containerID,
			type: 'canvas'
		}],
		settings: {
			sideMargin: 10,
			defaultLabelColor: '#555',
			defaultLabelSize: 9,
			defaultEdgeColor: '#333',
			enabelEdgeHovering: true,
			labelSize: 'fixed',
			zoomMin: 0.3,
			zoomMax: 2,
			edgeHoverColor: '#333',
			edgeHoverExtremities: true,
			edgeHoverColor: '#ff0000',
    		defaultEdgeHoverColor: '#ff0000',
    		edgeHoverSizeRatio: 1,
    		edgeHoverExtremities: true,
			scalingMode: 'inside',
			minNodeSize: 1,
			labelHoverShadow: false,
			labelAlignment: 'bottom'
		}
	});

	var ovconfig = {
		nodeMargin: 200,
		scaleNodes: 0.5,
		gridSize: 20,
		permittedExpansion: 1.1,
		easing: 'quadraticInOut',
		duration: 2000
	};

	var listener = rels.configNoverlap(ovconfig);

	listener.bind('start stop interpolate', function(event) {
		console.log(event.type);
	});
	
	rels.startNoverlap();
	
	rels.refresh();	

	rels.bind('clickNode', function(e) {

		var nodeId = e.data.node.id;

		pamsigmaPutData(e.data.node);

		pamsigmaGlobal(persons, containerID, tax, nodeId);

		

	})
	
}

function sigmaAnimateNodes() {
	sigma.plugins.animate(
			rels,
			{
				x: graph_form + '_x',
				y: graph_form + '_y'
			},
			{
				nodes: [nodeId],
				easing: 'quadraticInOut',
				duration: 1000,
				onComplete: function() {
					if(graph_form == 'old') {
						graph_form = 'circular';
					} else if(graph_form == 'grid') {
						graph_form = 'old';
					} else if(graph_form == 'circular') {
						graph_form = 'grid';
					}
				}
			}
		);
}

function pamFindPerson(persons, personID) {
	return persons.filter(
		function(persons) {
			return persons.person_id == personID
		}
	);
}

function pamsigmaPutData(data) {

	console.log(data);

	var canvas = jQuery('#relations-container');
	var container = jQuery('.pam-relaciones-global');
	var ficha = jQuery('.relations-info', container);
	
	var fichatemplate = jQuery('#relations-template').html();
	Mustache.parse(fichatemplate);
	var rendered_content = Mustache.render(fichatemplate, data);
	
	jQuery('.content', ficha).empty().append(rendered_content);
	
	ficha.addClass('active');
	canvas.addClass('active');

	

}

function pamsigmaToggleInfo() {
	jQuery('.pam-relaciones-global').removeClass('active');
	jQuery('#relations-container').removeClass('active');
	jQuery('.relations-info').removeClass('active');
}