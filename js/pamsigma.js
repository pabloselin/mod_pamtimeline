function pamsigmaReload(tax, persons, currentperson, currentpersondata, containerID) {

        // var json_relations_raw = cleanJson(persons);
		// var json_relations = JSON.parse( json_relations_raw );
		// var current_person = currentperson;
		// var current_person_data = JSON.parse(currentpersondata);

        if(tax == 'languages') {
            var current_tax_filter = current_person_data.person_languages.languages;
        } else if(tax == 'themes') {
            var current_tax_filter = current_person_data.person_themes.themes;
        } else if(tax == 'tools') {
            var current_tax_filter = current_person_data.person_tools.tools;
        } else {
            var current_tax_filter = current_person_data.person_languages.languages;
        };

		
		var langids = [];
		var langidsmap = current_tax_filter.map(function(lang) {
			langids.push(parseInt(lang.fieldvalueid, 10));
		});

		//console.log('langids', langids);

		//console.log(current_tax_filter);

		console.log(json_relations);

		graph_rel = {
			nodes: [],
			edges: []
		}

		//console.log(json_relations);
		var matchedpersons = [];

		json_relations.map(function(person) {
			if(person.person_languages.length !== 0) {
				var thispersonlang = person.person_languages.languages;
				var match = false;
				thispersonlang.map(function(lang) {
					//console.log(lang.fieldvalueid);
					if(langids.indexOf(lang.fieldvalueid)) {
						match = true;
					};
				});

				if(match == true) {
					matchedpersons.push(person);
				}
			}
			
		});

		console.log('matchs', matchedpersons);

		for( var i = 0; i < matchedpersons.length; i ++) {

			if( current_person == matchedpersons[i].person_id) {
				var cursize = 2;
				var curcolor = '#ff0000';
			} else {
				var cursize = 1;
				var curcolor = '#333333';
			}

			graph_rel.nodes.push({
				id: matchedpersons[i].person_id,
				label: matchedpersons[i].person_name,
				x: i * Math.random(),
				y: i * Math.random(),
				size:cursize,
				color: curcolor
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
			container: containerID
		});

		rels.refresh();

}