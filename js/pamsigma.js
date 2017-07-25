//Sigma custom renderers
var pamcolors = {
		black: '#000',
		red: '#ff0000',
		gray: '#808080',
		lightgray: '#ccc',
		white: '#ffffff'
	}

function getRandomArbitrary(min, max) {
  return Math.random() * (max - min) + min;
}


function pamPutData(data) {
	var container = jQuery('.single-relations-wrapper');
	var ficha = jQuery('.relations-info', container);
	
	var fichatemplate = jQuery('#relations-template').html();
	Mustache.parse(fichatemplate);
	var rendered_content = Mustache.render(fichatemplate, data);
	jQuery('.content', ficha).empty().append(rendered_content);
}

////////// OLD STUFF

function pamsigmaGlobal(instance, persons, containerID, tax, singleperson, ymult) {

	var singlematchedpersons = [];
	var oldpersons = persons;	
	var containerEl = jQuery('#' + containerID);
	var current_person_taxlabel = 'person_' + tax;
	var graph_rel = {
		nodes: [],
		edges: []
	}
	if(singleperson !== null) {
		persons = pamsigmaMatchSingle(persons, singleperson, tax);
	}
	
	for( var i = 0; i < persons.length; i ++) {
		
		var cursize = 0.5;
		var curlangs = persons[i].person_languages.languages;
		var curthemes = persons[i].person_themes.themes;
		var curtools = persons[i].person_tools.tools;
		var curpersontype = persons[i].person_type.persontype;
		
		graph_rel.nodes.push({
			id: persons[i].person_id,
			label: persons[i].person_name.toUpperCase(),
			x: -10 * i,
			y: Math.random() * i * ymult,
			size:cursize,
			prevcolor: pamcolors.gray,
			color: pamcolors.gray,
			hovercolor: pamcolors.red,
			labelcolor: pamcolors.gray,
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
										relations: [lang],
										color: pamcolors.lightgray,
										size: 1,
										label: lang.fieldvaluename
									});

									edgeids.push(edgeid);

								} else {
									for(var j = 0; j < graph_rel.edges.length; j++) {
										if(graph_rel.edges[j].id === edgeid) {
											graph_rel.edges[j].relations.push(lang);
											var prevlabel = graph_rel.edges[j].label;
											graph_rel.edges[j].label = prevlabel + ' - ' + lang.fieldvaluename;
											//console.log(graph_rel.edges[j].relations);
										}
									}
								}
								matchids[tax].push(lang.fieldvalueid);
							};
						});
						
					}
				
			});

		}
		
	}

	pamDeploySigma(instance, graph_rel, containerID, oldpersons, tax);
	
}

function pamInitSigma(containerID) {
	sigma.classes.graph.addMethod('neighbors', function(nodeId) {
		var k,
			neighbors = {},
			index = this.allNeighborsIndex[nodeId] || {};

			for (k in index)
			neighbors[k] = this.nodesIndex[k];

			return neighbors;
			});

	var instance = new sigma(
		{
			settings: {
				enableEdgeHovering: true,
				minNodeSize: 6,
				maxNodeSize: 6,
				labelThreshold: 6,
				sideMargin: 40,
				defaultLabelSize: 10,
				scalingMode: 'inside',
				labelHoverShadow: false,
				labelAlignment: 'bottom'
			}
		}
	);
	var defaultcamera = instance.addCamera();

	instance.addRenderer({
			container: containerID,
			type: 'canvas',
			camera: defaultcamera,
			settings: {
				font: 'Open Sans',
				edgeHoverColor: pamcolors.red,
				defaultEdgeHoverColor: pamcolors.red,
				labelSize: 'fixed',
				zoomMin: 0.3,
				zoomMax: 2,
				nodeHoverColor: pamcolors.red,
				edgeHoverExtremities: false,
				pamcolors: pamcolors
			}
	});

	instance.bind('overEdge', function(e) {
			e.data.edge.color = pamcolors.red;
			e.data.edge.active = true;

			instance.graph.nodes().forEach(function(n) {
				if(n.id === e.data.edge.source || n.id === e.data.edge.target)
					n.active = true;
			});

			instance.refresh({skipIndexation: true});
			
		});

		instance.bind('outEdge', function(e) {

				instance.graph.nodes().forEach(function(n) {
					n.active = false;
					n.color = pamcolors.gray;
				});

				pamDefaultState(instance);

				instance.refresh({skipIndexation: true});
			
		});	

		instance.bind('overNode', function(e) {
			nodeId = e.data.node.id;
			neighbor = instance.graph.neighbors(nodeId);
			neighbor[nodeId] = e.data.node;
			pamDefaultState(instance);
			instance.graph.nodes().forEach(function(n){
				if(neighbor[n.id])
					n.active = true;
			});

			instance.graph.edges().forEach(function(e) {
				if(neighbor[e.source] && neighbor[e.target]) {
					e.color = pamcolors.red;
				}
			});

			instance.refresh({skipIndexation: true});
			
		});

		instance.bind('outNode', function(e) {
		
			pamDefaultState(instance);	

			instance.refresh({skipIndexation: true});
		});

	return instance;
}

function pamResetZoom(instance) {
	var camera = instance.camera;
	var coordinates = {
		//ratio: camera.ratio * instance.settings('zoomingRatio')
		ratio: 1,
		x: 0,
		y: 0
	}
	instance.camera.goTo(coordinates);
}


function pamDeploySigma(instance, graph_rel, containerID, oldpersons, tax) {
	
	instance.graph.clear();

	for(var n = 0; n < graph_rel.nodes.length; n++) {
		graph_rel.nodes[n].active = false;
		instance.graph.addNode(graph_rel.nodes[n]);
	}

	for(var e = 0; e < graph_rel.edges.length; e++) {
		instance.graph.addEdge(graph_rel.edges[e]);
	}

	

		instance.bind('clickNode', function(e) {
			var nodeId = e.data.node.id;
			pamsigmaPutData(e.data.node);
			pamsigmaGlobal(instance, oldpersons, containerID, tax, nodeId, 10);
			pamToggleTax('hide');
			pamResetZoom(instance);
		});

		var ovconfig = {
			nodeMargin: 40,
			scaleNodes: 1.1,
			gridSizeX: 20,
			gridSizeY: 20,
			permittedExpansion: 1.1,
			easing: 'quadraticInOut',
			duration: 1000,
			speed: 4,
			maxIterations: 500
		};

	var listener = instance.configNoverlap(ovconfig);
	instance.refresh();
	instance.startNoverlap();
}

function pamTaxDropdown(instance) {
	jQuery('#taxitems ul li a').on('click', function(e) {
		e.preventDefault();
		jQuery('#taxitems ul li a').removeClass('active');
		var tax = jQuery(this).attr('data-tax');
		var taxid = jQuery(this).attr('data-taxid');
		
		updateinstance = pamHighlightNodes(instance, tax, taxid);

		updateinstance.refresh({skipIndexation: true});
		jQuery(this).addClass('active');
	});

}

function pamHighlightNodes(instance, tax, taxid) {
	jQuery('.relations-switcher a[data-tax="' + tax + '"]').trigger('click');
	jQuery('#taxitems ul li a').removeClass('active');
	var container = jQuery('#taxitems ul[data-tax="' + tax + '"]');
	var curtaxitem = jQuery('li a[data-taxid="' + taxid + '"]', container);
	
	
	container.animate({
			scrollTop: curtaxitem.offset().top - container.offset().top + container.scrollTop()
		});

	pamDefaultState(instance);
	
	curtaxitem.addClass('active');

	instance.graph.nodes().forEach(function(n) {
				taxitems = n[tax];
				
				n.active = false;
				n.color = pamcolors.gray;

				if(taxitems) {
					for(var t = 0;t < taxitems.length; t++) {
						if(taxitems[t].fieldvalueid === taxid)
							n.active = true;
					}
				}
			});

	instance.graph.edges().forEach(function(e) {
		if(e.relations) {
			for(var r = 0; r < e.relations.length; r++) {
				if(e.relations[r].fieldvalueid === taxid)
					e.color = pamcolors.red;
			}
		}
	});

	return instance;
}

function pamResetNodes(instance) {
	instance.graph.nodes().forEach(function(n) {
		n.color = pamcolors.gray;
		n.active = false;
	});
}

function pamResetEdges(instance) {
	instance.graph.edges().forEach(function(e){
		e.color = pamcolors.lightgray;
		e.active = false;
	});
}

function pamResetTaxList() {
	jQuery('body #taxitems ul li a').removeClass('active');
}

function pamDefaultState(instance) {
	pamResetNodes(instance);
	pamResetEdges(instance);
	pamResetTaxList();
}

function pamHiglightTaxItemList(tax, taxid) {

}

function pamSquareNodes(instance) {

}

function pamToggleTax(what) {
	var taxlist = jQuery('#taxitems');
	var container = jQuery('#relations-container');
	if(what == 'hide') {
		taxlist.removeClass('visible');
		container.addClass('inartist');
	} else {
		taxlist.addClass('visible');
		container.removeClass('inartist');
	}
} 

function pamFindPerson(persons, personID) {
	return persons.filter(
		function(persons) {
			return persons.person_id == personID
		}
	);
}



function pamsigmaToggleInfo(elements) {
	elements.removeClass('active');
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