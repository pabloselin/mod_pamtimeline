<?php
/**
 * @package    pam_timeline
 *
 * @author     Pablo Selín Carrasco - APie <pablo@apie.cl>
 * @copyright  APie
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://apie.cl
 */

defined('_JEXEC') or die;
error_reporting(1);
//Scripts and styles
$document = JFactory::getDocument();
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pam_common.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/mustache.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape/dist/cytoscape.js');
$document->addScript( 'http://weaver.js.org/api/weaver.js-1.2.0/weaver.min.js' );
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape-spread/cytoscape-spread.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape-cola/cola.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape-cola/cytoscape-cola.js');



$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamsigma.js');
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/relaciones.css');

?>

<div class="pam-relations-global">
	
	<?php 
		$article_id = JFactory::getApplication()->input->get('id');

		$field_id = ModPamTimelineHelper::getFieldIdByName( 'ID Persona' );
		$current_person_id = ModPamTimelineHelper::getItemField( $article_id, 'personid' );
	
		$persons = ModPamTimelineHelper::getPersons();
		$persons_array = [];
		$graph_items = [];
		$current_person_data = [];
		$edges = [];

		$all_tools = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['tools'] ));
		$all_languages = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['languages'] ));
		$all_themes = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['themes'] ));

		foreach($persons as $key=>$person) {

			//$personFields = ModPamTimelineHelper::getItemFields( $person['id'] );

			$languages = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'languages' );
			$themes = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'themes' );
			$tools = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'tools' );
			$persontype = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'persontype' );
			$graph_items['nodes'][] = array(
				'data' => array(
					'id' => $person['id'],
					'caption' => $person['title'],
					'languages' => $languages['languages'],
					'themes' => $themes['themes'],
					'tools' => $tools['tools'],
					'url' => ModPamTimelineHelper::getItemLink($person['id'], $person['alias'], $person['catid']),
					'thumbnail' => ModPamTimelineHelper::getItemImageUrl($person['id'], 'S'),
					'type' => ModPamTimelineHelper::valuesToNames($persontype['persontype'][0], 'persontype'),
					'color' => '#808080'
				)
			);

			foreach($languages['languages'] as $language) {
				$graph_items['nodes'][$key]['data']['dt-languages-' . $language] = true;
				$graph_items['nodes'][$key]['data']['st-languages'][] = array(
																		'id' => $language,
																		'label' => ModPamTimelineHelper::valuesToNames($language, 'languages')
																		);
			}

			foreach($themes['themes'] as $theme) {
				$graph_items['nodes'][$key]['data']['dt-themes-' . $theme][] = true;
				$graph_items['nodes'][$key]['data']['st-themes'][] = array(
																		'id' => $theme,
																		'label' => ModPamTimelineHelper::valuesToNames($theme, 'themes')
																		);
			}

			foreach($tools['tools'] as $tool) {
				$graph_items['nodes'][$key]['data']['dt-tools-' . $tool] = true;
				$graph_items['nodes'][$key]['data']['st-tools'][] = array(
																		'id' => $tool,
																		'label' => ModPamTimelineHelper::valuesToNames($tool, 'tools')
																		);
			}
			

		}
		
		//$languages_edges = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'languages');
		$edges['languages']['edges'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'languages');
		$edges['themes']['edges'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'themes');
		$edges['tools']['edges'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'tools');


		$json_edges = json_encode($edges, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
		$json_nodes = json_encode($graph_items, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
	?>

	<div class="pam-relaciones-global">

		<div class="relations-switcher">
			<a class="active" href="#" data-tax="languages">Lenguajes</a>
			<a href="#" data-tax="themes">Temáticas</a>
			<a href="#" data-tax="tools">Herramientas</a>
		</div>

		<div id="taxitems" class="visible">
			<ul data-tax="languages" class="active">
				<?php 
					foreach($all_languages as $language) {
						echo '<li data-tax="languages" data-taxid="' . $language->value. '"><a data-tax="languages" data-taxid="' . $language->value . '" href="#language-' .$language->value . '">' . $language->name . '</a></li>';
					}
				?>
			</ul>
			<ul data-tax="themes">
			<?php 
					foreach($all_themes as $theme) {
						echo '<li data-tax="themes" data-taxid="' . $theme->value. '"><a data-tax="themes" data-taxid="' . $theme->value . '" href="#theme-' .$theme->value . '">' . $theme->name . '</a></li>';
					}
				?>
			</ul>
			<ul data-tax="tools">
				<?php 
					foreach($all_tools as $tool) {
						echo '<li data-tax="tools" data-taxid="' . $tool->value. '"><a data-tax="tools" data-taxid="' . $tool->value . '" href="#tool-' .$tool->value . '">' . $tool->name . '</a></li>';
					}
				?>
			</ul>
		</div>

    	<div id="relations-container">
		</div>

		<div class="relations-info">	
			<div class="content">			
			</div>
		</div>

		</div>

	</div>

	<script type="text/javascript">
		
		var curtax = 'languages';
		
		var pamcolors = {
			black: '#000',
			red: '#ff0000',
			gray: '#808080',
			lightgray: '#ccc',
			white: '#ffffff'
		}

		var json_nodes = cleanJson('<?php echo $json_nodes;?>');
		var json_edges = cleanJson('<?php echo $json_edges;?>');
		var json_obj = JSON.parse(json_nodes);
		var json_edgeobj = JSON.parse(json_edges);
		var curedges = json_edgeobj[curtax];
		
		//DOM Interactions
		jQuery(document).ready(function($) {
			var relcont = $('#relations-container');
			var taxitems = $('#taxitems');
			//var json_graph = $.extend(json_obj, curedges);
			//console.log(json_graph);
			var cy = cytoscape({
				container: relcont,
				elements: json_obj,
				zoomingEnabled: false,
				minZoom: 1,
				maxZoom: 1,
				style: [
					{
						selector: 'node',
						style: {
							'background-color': pamcolors.gray,
							'text-background-color': 'white',
							'text-background-opacity': 1,
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
						selector: 'node.selected',
						style: {
							'background-color': pamcolors.red
						}
					},
					{
						selector: 'node.under',
						style: {
							'label': ''
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
							'line-color': pamcolors.red
						}
					}
				]
			});

			var layout = cy.layout({
							name: 'spread',
							animate: true,
							randomize: false,
							fit: true,
							padding:40,
							minDist: 40
						});

			layout.run();

			var oldnodes = [];

			cy.add(json_edgeobj[curtax]);
			var curtaxedges = cy.elements('edge');
			

			cy.on('mouseover', 'node', function(event) {
				var node = event.target;
				var nodeId = node.id();
				var neighbors = cy.$('node#' + nodeId).closedNeighborhood();
				neighbors.map(function(neighnode) {
					cy.$('#' + neighnode.id()).addClass('hover');	
				});
			});

			cy.on('mouseout', 'node', function(event) {
				var node = event.target;
				var nodeId = node.id();
				
				var neighbors = cy.$('node#' + nodeId).closedNeighborhood();

				neighbors.map(function(neighnode) {
					cy.$('#' + neighnode.id()).removeClass('hover');	
				});
			});

			cy.on('mouseover', 'edge', function(event) {
				var edge = event.target;
				var edgeId = edge.id();
				var edgeSource = edge.source();
				var edgeTarget = edge.target();
				
				cy.$('edge#' + edgeId).addClass('hoveredge');
				cy.$('node').addClass('under');
				edge.connectedNodes().removeClass('under');
				cy.$('node#' + edgeSource.id() + ', ' + 'node#' + edgeTarget.id()).addClass('hover');
			});

			cy.on('mouseout', 'edge', function(event) {
				var edge = event.target;
				var edgeId = edge.id();
				var edgeSource = edge.source();
				var edgeTarget = edge.target();
				cy.$('node').removeClass('under');
				cy.$('edge#' + edgeId).removeClass('hoveredge');
				cy.$('node#' + edgeSource.id() + ', ' + 'node#' + edgeTarget.id()).removeClass('hover');
			});

			cy.on('click', 'node', function(event) {
				var node = event.target;
				var neighbors = cy.$('node#' + node.id()).closedNeighborhood();
				relcont.addClass('inartist');
				taxitems.removeClass('visible');
				pamPutData(node.data());
				
				
				$('.relations-info').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(e) {
					cy.$('node').removeClass('hover').remove();
					cy.$('edge').removeClass('hover');
					cy.add(neighbors);	
					cy.$('node#' + node.id()).addClass('selected');
					cy.animate({
						center: cy.$('node#' + node.id()),
						fit: {
							eles: cy.$('edge'),
							padding: '40px'
						}
				});

				});
				
			});
			
			$('.relations-switcher a').on('click', function(e) {
				e.preventDefault;
				if(!$(this).hasClass('active')) {
					curtax = $(this).attr('data-tax');
					var taxlinks = $('#taxitems ul li a');
					$('.relations-switcher a, #taxitems ul.active').removeClass('active');
					$(this).addClass('active');

					$('#taxitems ul[data-tax="' + curtax + '"]').addClass('active');
					cy.elements('node').removeClass('hover');
					cy.remove( curtaxedges );
					cy.add(json_edgeobj[curtax]);
					
					curtaxedges = cy.elements('edge');
					taxlinks.removeClass('active');
				}
			});

			$('#taxitems ul li a').on('click', function(e) {
				e.preventDefault;
				if(!$(this).hasClass('active')) {
					var others = $('#taxitems ul li a');
					var tax = $(this).attr('data-tax');
					var taxid = $(this).attr('data-taxid');
					els = cy.elements('node[dt-' + tax + '-' + taxid +']');
					cy.elements('node').removeClass('hover');
					els.addClass('hover');
					others.removeClass('active');
					$(this).addClass('active');
				}
			});

			$('.pam-relaciones-global').on('click', 'a.back', function(e) {
				e.preventDefault;
				
				cy.$('node').remove();
				cy.$('edge').remove();
				relcont.removeClass('inartist');
				taxitems.addClass('visible');
				cy.add(json_obj);
				cy.add(json_edgeobj[curtax]);
				$('.relations-info').removeClass('active');
				relcont.removeClass('active');
				cy.resize();
				$('.relations-info').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(e) {	
					cy.center();
					cy.resize();
					cy.fit();
					layout.run();
					
				});	
			});
			


		});
</script>

<script id="relations-template" type="x-tmpl-mustache">
				<h2 class="artist-title"><a href={{link}}>{{caption}}</a></h2>
				
				{{#type}}
				<span class="persontype">{{type}}</span>
				{{/type}}

				<div class="mobile-nav">
					<a class="infomobile" href="#">+ info</a>
					<a class="back backmobile" href="#">Volver</a>
				</div>
				
				
				<div class="info-wrapper">
					<img src={{thumbnail}} alt={{caption}}>
					
					<div class="introtext">{{introtext}}</div>	
					
					<div class="taxsection">
						<h3>Lenguajes</h3>
						
						{{#st-languages}}
							<span class="taxtip" data-tax="languages" data-taxid="{{id}}">{{label}}</span>
						{{/st-languages}}
					</div>
					
					<div class="taxsection">
						<h3>Temáticas</h3>
						
						{{#st-themes}}
							<span class="taxtip" data-tax="themes" data-taxid="{{id}}">{{label}}</span>
						{{/st-themes}}
					</div>
					
					<div class="taxsection">
					<h3>Herramientas</h3>
					
					{{#st-tools}}
						<span class="taxtip" data-tax="tools" data-taxid="{{id}}">{{label}}</span>
					{{/st-tools}}
					
					</div>
					
					<p class="link"><a href={{url}}>Ver más</a> <a class="back" href="#">Volver</a></p>
				</div>
			</script>