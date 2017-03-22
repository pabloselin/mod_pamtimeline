//Sigma custom renderers

//Poner el label debajo
sigma.canvas.labels.def = function(node, context, settings) {
    var fontSize,
        prefix = settings('prefix') || '',
        size = node[prefix + 'size'],
        labelWidth = 0,
        labelPlacementX,
        labelPlacementY,
        alignment;

    if (size < settings('labelThreshold'))
      return;

    if (typeof node.label !== 'string')
      return;

    if (settings('labelAlignment') === undefined){ 
      alignment = settings('defaultLabelAlignment');
    } else {
      alignment = settings('labelAlignment');
    }

    fontSize = (settings('labelSize') === 'fixed') ?
      settings('defaultLabelSize') :
      settings('labelSizeRatio') * size;

    context.font = (settings('fontStyle') ? settings('fontStyle') + ' ' : '') +
      fontSize + 'px ' + settings('font');
    context.fillStyle = 'black';

    labelWidth = context.measureText(node.label).width;
    labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
    labelPlacementY = Math.round(node[prefix + 'y'] + fontSize / 3);

    switch (alignment) {
      case 'inside':
        if (labelWidth <= size * 2){
          labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        }
        break;
      case 'center':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        break;
      case 'left':
        labelPlacementX = Math.round(node[prefix + 'x'] - size - labelWidth - 3 );
        break;
      case 'right':
        labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
        break;
      case 'top':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY - size - fontSize;
        break;
      case 'bottom':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
      default:
        // Default is aligned 'right'
        labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
        break;
    }

	context.beginPath();

    context.fillStyle = settings('labelHoverBGColor') === 'node' ?
      (node.color || settings('defaultNodeColor')) :
      settings('defaultHoverLabelBGColor');

    if (node.label && typeof node.label === 'string') {
      w = Math.round(
        context.measureText(node.label).width + fontSize / 2 + size + 7
      );
      h = Math.round(fontSize + 4);
      e = Math.round(fontSize / 2 + 2);
	  var bglabelposY = labelPlacementY - h + 1;

      context.moveTo(labelPlacementX, bglabelposY);
      context.lineTo(labelPlacementX, bglabelposY, labelPlacementX + e, bglabelposY, e);
      context.lineTo(labelPlacementX + w, bglabelposY);
      context.lineTo(labelPlacementX + w, bglabelposY + h);
      context.lineTo(labelPlacementX + e, bglabelposY + h);
      context.lineTo(labelPlacementX, bglabelposY + h, labelPlacementX, bglabelposY + h - e, e);
      context.lineTo(labelPlacementX, bglabelposY + e);

      context.closePath();
      context.fill();

      context.shadowOffsetX = 0;
      context.shadowOffsetY = 0;
      context.shadowBlur = 0;
    }

	context.closePath();
    context.fill();

	// Node:
    var nodeRenderer = sigma.canvas.nodes[node.type] || sigma.canvas.nodes.def;
    nodeRenderer(node, context, settings);

    // Display the label:
    if (node.label && typeof node.label === 'string') {
      context.fillStyle = (settings('labelHoverColor') === 'node') ?
        (node.color || settings('defaultNodeColor')) :
        settings('defaultLabelHoverColor');

      context.fillText(
        node.label,
        Math.round(labelPlacementX + size + 3),
        Math.round(labelPlacementY - fontSize / 3)
      );
    }


  };

//Hover
sigma.canvas.hovers.def = function(node, context, settings) {
    var fontSize,
        prefix = settings('prefix') || '',
        size = node[prefix + 'size'],
        labelWidth = 0,
        labelPlacementX,
        labelPlacementY,
        alignment;

    if (size < settings('labelThreshold'))
      return;

    if (typeof node.label !== 'string')
      return;

    if (settings('labelAlignment') === undefined){ 
      alignment = settings('defaultLabelAlignment');
    } else {
      alignment = settings('labelAlignment');
    }

    fontSize = 16;

    context.font = (settings('fontStyle') ? settings('fontStyle') + ' ' : '') +
      fontSize + 'px ' + settings('font');
    context.fillStyle = '#ff0000';

    labelWidth = context.measureText(node.label).width;
    labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
    labelPlacementY = Math.round(node[prefix + 'y'] + fontSize / 3);

    switch (alignment) {
      case 'inside':
        if (labelWidth <= size * 2){
          labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        }
        break;
      case 'center':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        break;
      case 'left':
        labelPlacementX = Math.round(node[prefix + 'x'] - size - labelWidth - 3 );
        break;
      case 'right':
        labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
        break;
      case 'top':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY - size - fontSize;
        break;
      case 'bottom':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
      default:
        // Default is aligned 'right'
        labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
        break;
    }

	context.beginPath();

    context.fillStyle = settings('labelHoverBGColor') === 'node' ?
      (node.color || settings('defaultNodeColor')) :
      settings('defaultHoverLabelBGColor');

    if (node.label && typeof node.label === 'string') {
      w = Math.round(
        context.measureText(node.label).width + fontSize / 2 + size + 7
      );
      h = Math.round(fontSize + 4);
      e = Math.round(fontSize / 2 + 2);
	  var bglabelposY = labelPlacementY - h + 1;

      context.moveTo(labelPlacementX, bglabelposY);
      context.lineTo(labelPlacementX, bglabelposY, labelPlacementX + e, bglabelposY, e);
      context.lineTo(labelPlacementX + w, bglabelposY);
      context.lineTo(labelPlacementX + w, bglabelposY + h);
      context.lineTo(labelPlacementX + e, bglabelposY + h);
      context.lineTo(labelPlacementX, bglabelposY + h, labelPlacementX, bglabelposY + h - e, e);
      context.lineTo(labelPlacementX, bglabelposY + e);

      context.closePath();
      context.fill();

      context.shadowOffsetX = 0;
      context.shadowOffsetY = 0;
      context.shadowBlur = 0;
    }

	context.closePath();
    context.fill();

	// Node:
    var nodeRenderer = sigma.canvas.nodes[node.type] || sigma.canvas.nodes.def;
    nodeRenderer(node, context, settings);

    // Display the label:
    if (node.label && typeof node.label === 'string') {
      context.fillStyle = '#ff0000';

      context.fillText(
        node.label,
        Math.round(labelPlacementX + size + 3),
        Math.round(labelPlacementY - fontSize / 3)
      );
    }


  };


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

function pamsigmaGlobal(persons, containerID, tax) {
	
	// var json_relations_raw = cleanJson(persons);
	// var json_relations = JSON.parse( json_relations_raw );
	// var current_person = currentperson;
	// var current_person_data = JSON.parse(currentpersondata);

	var db = new sigma.plugins.neighborhoods();
	
	var containerEl = jQuery('#' + containerID);
	var highlight = jQuery('#' + containerEl.attr('data-highlight') );
	var subhighlight = jQuery('#' + containerEl.attr('data-subhighlight') );
	
	var current_person_taxlabel = 'person_' + tax;

	containerEl.empty();
	highlight.empty();
	subhighlight.empty();
	
	
	//console.log(langids);

	var L = 5,
    	N = 25,
    	E = 250;
	
	var graph_rel = {
		nodes: [],
		edges: []
	}
	
	for( var i = 0; i < persons.length; i ++) {
		
		var cursize = 0.1;
		var curcolor = '#ff0000';
		var artistcolor = '#ff0000';
		var curlangs = persons[i].person_languages.languages;
		var curthemes = persons[i].person_themes.themes;
		var curtools = persons[i].person_tools.tools;
		var canvaswidth = jQuery(containerEl).innerWidth();
		var canvasheight = jQuery(containerEl).innerHeight();
		var center = [canvaswidth/2, canvasheight/2];

		console.log(center);
		
		
		graph_rel.nodes.push({
			id: persons[i].person_id,
			label: persons[i].person_name.toUpperCase(),
			x: i * Math.random(),
			y: i * Math.random(),
			center_x: center[0],
			center_y: center[1],
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
			tools: curtools
		});

		matchedpersons = [];
		
		//Busco en todas las otras personas matches de lenguaje
		persons.map(function(person) {
			
			var match = false;
			var matchids = {
				languages: [],
				tools: [],
				themes: []
			};
			
			//console.log(person);
			
			if(person.person_languages.length !== 0) {
				
				var plangs = person.person_languages.languages;
				var langids = [];
				var langidsmap = plangs.map(function(lang) {
					langids.push(parseInt(lang.fieldvalueid, 10));
				});
				
				var thispersonlang = person.person_languages.languages;
				
				thispersonlang.map(function(lang, idx) {
					
					if(langids.indexOf(parseInt(lang.fieldvalueid, 10)) != -1) {
						match = true;
						matchids.languages.push(lang.fieldvalueid);
						//console.log(langids, lang.fieldvalueid );
					};
				});
				
				if(match == true) {

					graph_rel.edges.push({
						id: 'edge-' + i + '-' + person.person_id,
						source: person.person_id,
						target: persons[i].person_id,
						color: '#ccc',
						size: 3
					});
				}
			}
			
		});
		
	}
	
	
	var rels = new sigma({
		graph: graph_rel,
		renderers: [{
			container: containerID,
			type: 'canvas'
		}],
		settings: {
			sideMargin: 0,
			defaultLabelColor: '#555',
			defaultLabelSize: 10,
			defaultEdgeColor: '#333',
			enabelEdgeHovering: true,
			//labelSize: 'proportional',
			zoomMin: 0.3,
			zoomMax: 2,
			edgeHoverColor: '#333',
			edgeHoverExtremities: true,
			edgeHoverColor: 'edge',
    		defaultEdgeHoverColor: '#000',
    		edgeHoverSizeRatio: 1,
    		edgeHoverExtremities: true,
			scalingMode: 'inside',
			minNodeSize: 0.2,
			labelHoverShadow: false,
			borderSize: 1,
			labelAlignment: 'bottom'
		}
	});

	var ovconfig = {
		nodeMargin: 130.0,
		scaleNodes: 0.2
	};

	var listener = rels.configNoverlap(ovconfig);

	listener.bind('start stop interpolate', function(event) {
		console.log(event.type);
	});
	
	rels.startNoverlap();
	
	rels.refresh();	

	rels.bind('clickNode', function(e) {
		
		var nodeId = e.data.node.id;
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

	})
	
}