//Sigma custom renderers

function pamsigmaAppend(node) {
	//Añade nuevos links
}

function pamsigmaGlobal(persons, containerID, tax, singleperson) {

	var singlematchedpersons = [];
	//guardo las otras personas por si acaso
	var oldpersons = persons;
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
		persons = pamsigmaMatchSingle(persons, singleperson, tax);
	}

	
	for( var i = 0; i < persons.length; i ++) {
		
		var cursize = 1;
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
			sideMargin: 70,
			defaultLabelColor: '#555',
			defaultLabelSize: 9,
			defaultEdgeColor: '#D65B56',
			enableEdgeHovering: true,
			labelSize: 'fixed',
			zoomMin: 0.3,
			zoomMax: 2,
			edgeHoverColor: 'default',
    		defaultEdgeHoverColor: '#D65B56',
    		edgeHoverSizeRatio: 1.2,
    		edgeHoverExtremities: false,
			scalingMode: 'inside',
			minNodeSize: 1,
			maxNodeSize: 8,
			labelHoverShadow: false,
			labelAlignment: 'bottom',
			labelThreshold: 3
		}
	});	

	rels.bind('clickNode', function(e) {

		var nodeId = e.data.node.id;

		pamsigmaPutData(e.data.node);

		pamsigmaGlobal(oldpersons, containerID, tax, nodeId);

	});

	var ovconfig = {
		nodeMargin: 55.0,
		scaleNodes: 1.2,
		gridSize: 20,
		permittedExpansion: 1.1,
		easing: 'quadraticInOut',
		duration: 2000,
		speed: 4,
		maxIterations: 200
	};

	var listener = rels.configNoverlap(ovconfig);
	
	rels.startNoverlap();
	
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

function pamsigmaMatchSingle(persons, singleperson, tax) {

	var current_person_taxlabel = 'person_' + tax;
	var singlematchedpersons = [];
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
		return singlematchedpersons;
}