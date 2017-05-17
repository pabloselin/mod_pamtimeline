//Sigma custom renderers
var pamcolors = {
		black: '#000',
		red: '#ff0000',
		gray: '#808080',
		lightgray: '#ccc',
		white: '#ffffff'
	}

function pamsigmaAppend(node) {
	//Añade nuevos links
}

function getRandomArbitrary(min, max) {
  return Math.random() * (max - min) + min;
}

function pamsigmaGlobal(instance, persons, containerID, tax, singleperson) {
	
	var singlematchedpersons = [];
	var oldpersons = persons;	
	var containerEl = jQuery('#' + containerID);
	var current_person_taxlabel = 'person_' + tax;
	var graph_rel = {
		nodes: [],
		edges: []
	}
	if(singleperson !== undefined) {
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
			y: Math.random() * i,
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

	var instance = new sigma({
			graph: {},
			renderers: [{
				container: containerID,
				type: 'canvas'
			}],
			settings: {
				sideMargin: 20,
				defaultLabelSize: 10,
				font: 'Open Sans',
				enableEdgeHovering: true,
				edgeHoverColor: pamcolors.red,
				defaultEdgeHoverColor: pamcolors.red,
				labelSize: 'fixed',
				zoomMin: 0.3,
				zoomMax: 2,
				nodeHoverColor: pamcolors.red,
				edgeHoverExtremities: false,
				scalingMode: 'inside',
				minNodeSize: 1,
				maxNodeSize: 6,
				labelHoverShadow: false,
				labelAlignment: 'bottom',
				labelThreshold: 3,
				pamcolors: pamcolors
			}
		});

	return instance;
}

function pamDeploySigma(instance, graph_rel, containerID, oldpersons, tax) {

	instance.graph.clear();

	for(var n = 0; n < graph_rel.nodes.length; n++) {
		instance.graph.addNode(graph_rel.nodes[n]);
	}

	for(var e = 0; e < graph_rel.edges.length; e++) {
		instance.graph.addEdge(graph_rel.edges[e]);
	}

	

		instance.bind('clickNode', function(e) {

			var nodeId = e.data.node.id;
			
			pamsigmaPutData(e.data.node);

			instance.graph.clear();

			pamsigmaGlobal(instance, oldpersons, containerID, tax, nodeId);
			pamToggleTax('hide');

		});

		instance.bind('overNode', function(e) {
			nodeId = e.data.node.id;
			neighbor = instance.graph.neighbors(nodeId);
			neighbor[nodeId] = e.data.node;

			instance.graph.nodes().forEach(function(n){
				if(neighbor[n.id]) {
					n.active = true;
				} else {
					//n.color = pamcolors.gray;
					n.active = false;
					//n.hidden = true;
				}
			});

			instance.graph.edges().forEach(function(e) {
				if(neighbor[e.source] && neighbor[e.target]) {
					e.color = pamcolors.red;
					//e.active = true;
				} else {
					e.color = pamcolors.lightgray;
					//e.active = false;
				}
			});

			instance.refresh({skipIndexation: true});
			//console.log('overnode');
		});

		instance.bind('outNode', function(e) {
			
			//console.log('outnode');

			instance.graph.nodes().forEach(function(n) {
				n.active = false;
				n.color = pamcolors.gray;
				//n.hidden = false;
			});

			instance.graph.edges().forEach(function(e) {
				e.color = pamcolors.lightgray;
				//e.active = false;
			});

			instance.refresh({skipIndexation: true});
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
				e.data.edge.color = pamcolors.lightgray;
				e.data.edge.active = false;

				instance.graph.nodes().forEach(function(n) {
					n.active = false;
					n.color = pamcolors.gray;
				});

				instance.refresh({skipIndexation: true});
			
		});	

		var ovconfig = {
			nodeMargin: 40,
			scaleNodes: 1.1,
			gridSizeX: 60,
			gridSizeY: 60,
			permittedExpansion: 1.1,
			easing: 'quadraticInOut',
			duration: 1000,
			speed: 4,
			maxIterations: 500
	};

	var listener = instance.configNoverlap(ovconfig);
	instance.refresh({skipIndexation: true});
	instance.startNoverlap();
	//instance.startForceAtlas2();
	//rels.stopForceAtlas2();

	// var flsettings = {
	// 	linLogMode: false,
	// 	outboundAttractionDistribution: true,
	// 	autoStop: true,
	// 	gravity: 1,
	// 	maxIterations: 100,
	// 	alignNodeSiblings: false,
	// 	nodeSiblingsScale: 2,
	// 	nodeSiblingsAngleMin: 1.6,
	// 	randomize: 'globally'
	// }
	// var forcelink = sigma.layouts.startForceLink(instance, flsettings);
}

function pamTaxDropdown(instance) {
	jQuery('#taxitems ul li a').on('click', function(e) {
		console.log
		var tax = jQuery(this).attr('data-tax');
		var taxid = jQuery(this).attr('data-taxid');
		
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

		instance.refresh({skipIndexation: true});
	});

}

function pamToggleTax(what) {
	var taxlist = jQuery('#taxitems');
	var container = jQuery('#relations-container');
	if(what == 'hide') {
		taxlist.hide();
		container.addClass('inartist');
	} else {
		taxlist.show();
		container.removeClass('inartist');
	}
} 

// function sigmaAnimateNodes() {
// 	sigma.plugins.animate(
// 			rels,
// 			{
// 				x: graph_form + '_x',
// 				y: graph_form + '_y'
// 			},
// 			{
// 				nodes: [nodeId],
// 				easing: 'quadraticInOut',
// 				duration: 500,
// 				onComplete: function() {
// 					if(graph_form == 'old') {
// 						graph_form = 'circular';
// 					} else if(graph_form == 'grid') {
// 						graph_form = 'old';
// 					} else if(graph_form == 'circular') {
// 						graph_form = 'grid';
// 					}
// 				}
// 			}
// 		);
// }

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

// sigma.classes.graph.addMethod('neighbors', function(nodeId) {
// 		var k,
// 			neighbors = {},
// 			index = this.allNeighborsIndex[nodeId] || {};

// 		for (k in index)
// 		neighbors[k] = this.nodesIndex[k];

// 		return neighbors;
// 	});

//   s.bind('clickNode', function(e) {
//         var nodeId = e.data.node.id,
//             toKeep = s.graph.neighbors(nodeId);
//         toKeep[nodeId] = e.data.node;

//         s.graph.nodes().forEach(function(n) {
//           if (toKeep[n.id])
//             n.color = n.originalColor;
//           else
//             n.color = '#eee';
//         });

//         s.graph.edges().forEach(function(e) {
//           if (toKeep[e.source] && toKeep[e.target])
//             e.color = e.originalColor;
//           else
//             e.color = '#eee';
//         });

//         // Since the data has been modified, we need to
//         // call the refresh method to make the colors
//         // update effective.
//         s.refresh();
//       });