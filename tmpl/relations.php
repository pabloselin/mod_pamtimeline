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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/cy-styles.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/cy-functions.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/cy-documentready.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape/dist/cytoscape.js');
$document->addScript( 'http://weaver.js.org/api/weaver.js-1.2.0/weaver.min.js' );
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/cytoscape-spread/cytoscape-spread.js');
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/relaciones.css');
		
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

	<div id="relations-container" class="hidden"></div>
	<div class="single-relations-wrapper insingle-relations-wrapper">
				<div id="single-relations">
		
				</div>
				
				<div class="relations-info">	
					<div class="content">			
					</div>
				</div>
	</div>

	<script type="text/javascript">
		
		var curtax = 'languages';
		var current_person = '<?php echo $current_person_id;?>';
		var insingle = true;
		var json_nodes = cleanJson('<?php echo $json_nodes;?>');
		var json_edges = cleanJson('<?php echo $json_edges;?>');
		var json_obj = JSON.parse(json_nodes);
		var json_edgeobj = JSON.parse(json_edges);
		var curedges = json_edgeobj[curtax];
		
		
	</script>

<script id="relations-template" type="x-tmpl-mustache">				
				
				<div class="single-info-wrapper">
					
					
					<div class="taxsection" data-tax="languages">
						<h3 class="taxswitch" data-tax="languages" data-nodeid="{{id}}">Lenguajes</h3>
						
						{{#st-languages}}
							<span class="taxtip-single" data-tax="languages" data-taxid="{{id}}">{{label}}</span>
						{{/st-languages}}
					</div>
					
					<div class="taxsection" data-tax="themes">
						<h3 class="taxswitch" data-tax="themes" data-nodeid="{{id}}">Temáticas</h3>
						
						{{#st-themes}}
							<span class="taxtip-single" data-tax="themes" data-taxid="{{id}}">{{label}}</span>
						{{/st-themes}}
					</div>
					
					<div class="taxsection" data-tax="tools">
					<h3 class="taxswitch" data-tax="tools" data-nodeid="{{id}}">Herramientas</h3>
					
					{{#st-tools}}
						<span class="taxtip-single" data-tax="tools" data-taxid="{{id}}">{{label}}</span>
					{{/st-tools}}
					
					</div>
					
				</div>
			</script>