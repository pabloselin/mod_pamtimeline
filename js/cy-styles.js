var pamcolors = {
		black: '#000',
		red: '#ff0000',
		gray: '#808080',
		lightgray: '#ccc',
		white: '#ffffff'
	};

var generalstyle = [{
						selector: 'node',
						style: {
							'background-color': pamcolors.gray,
							'text-background-color': 'white',
							'text-background-opacity': 0.8,
							'width': '10px',
							'height': '10px',
							'label': 'data(caption)',
							'text-valign': 'bottom',
							'font-family': 'Open Sans, sans-serif',
							'color': '#555',
							'text-transform': 'uppercase',
							'font-size': '11px',
							'text-margin-y': '4px',
							'text-wrap': 'wrap',
							'text-max-width': '90px'
						}
					},
					{
						selector: 'node.hover',
						style: {
							'background-color': pamcolors.red,
							'font-size': '12px',
							'color': '#000',
							'width': '15px',
							'height': '15px'
						}
					},
					{
						selector: 'node.nolabels',
						style: {
							label:''
						}
					},
					{
						selector: 'node.nolabels.hover',
						style: {
							label: 'data(caption)'
						}
					},
                    {
                        selector: 'node.bigger',
                        style: {
                            width: '15px',
                            height: '15px'
                        }
                    },
					{
						selector: 'node.selected',
						style: {
							'background-color': pamcolors.red,
							'width': '15px',
							'height': '15px'
						}
					},
					{
						selector: 'node.hidden',
						style: {
							'display': 'none'
						}
					},
					{
						selector: '.under',
						style: {
                            'opacity': 0.5
						}
					},
					{
						selector: 'edge',
						style: {
							'width': '1px'
						}
					},
					{
						selector: 'edge.hover',
						style: {
							
							'line-color': pamcolors.red
						}
					},
					{
						selector: 'edge.hoveredge',
						style: {
							'font-size': '10px',
							'font-family': 'Open Sans, sans-serif',
							'text-background-color': pamcolors.red,
							'text-background-opacity': 1,
							'text-background-shape': 'rectangle',
							'text-background-padding': '2px',
							'color': 'white',
							'label': 'data(label)',
							'line-color': pamcolors.red,
							'text-max-width': '90px',
							'text-wrap': 'wrap'
						}
					},
					{
						selector: 'edge:selected, edge:active',
						style: {
							'line-color': pamcolors.gray
						}
					}
				];