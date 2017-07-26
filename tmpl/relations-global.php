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

	<div class="global-relations-wrapper">
		<div class="relations-switcher">
			<a class="active" href="#" data-tax="languages">Lenguajes</a>
			<a href="#" data-tax="themes">Temáticas</a>
			<a href="#" data-tax="tools">Herramientas</a>
		</div>
		
		<div class="layout-switcher">
			<a href="#" data-layout="circle"><i class="fa fa-circle-o fa-fw"></i></a>
			<a href="#" data-layout="grid"><i class="fa fa-th fa-fw"></i></a>
			<a href="#" data-layout="random"><i class="fa fa-random fa-fw"></i></a>
			<a href="#" data-class="nolabels"><i class="fa fa-font fa-fw"></i></a>
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
	</div>

	<div class="single-relations-wrapper">
				<div id="single-relations">
		
				</div>
				
				<div class="relations-info">	
					<div class="content">			
					</div>
				</div>
	</div>

	<script type="text/javascript">
		
		var curtax = 'languages';
		var insingle = false;
		var json_nodes = cleanJson('<?php echo $json_nodes;?>');
		var json_edges = cleanJson('<?php echo $json_edges;?>');
		var json_obj = JSON.parse(json_nodes);
		var json_edgeobj = JSON.parse(json_edges);
		var curedges = json_edgeobj[curtax];
		
		
	</script>

<script id="relations-template" type="x-tmpl-mustache">
				<div class="header-info">
				<img class="mobile-img" src={{thumbnail}} alt={{caption}}>
				<h2 class="artist-title"><a href={{link}}>{{caption}}</a></h2>
				
				{{#type}}
				<span class="persontype">{{type}}</span>
				{{/type}}

				<div class="mobile-nav">
					<a class="infomobile" href="#">+ info</a>
					<a class="back backmobile" href="#">Volver</a>
				</div>

				</div>

				
				
				<div class="info-wrapper">
					<img class="artist-img" src={{thumbnail}} alt={{caption}}>
					
					<div class="introtext">{{introtext}}</div>	
					
					<div class="taxsection" data-tax="languages">
						<h3 class="taxswitch" data-tax="languages" data-nodeid="{{id}}">Lenguajes</h3>
						
						{{#st-languages}}
							<span class="taxtip" data-tax="languages" data-taxid="{{id}}">{{label}}</span>
						{{/st-languages}}
					</div>
					
					<div class="taxsection" data-tax="themes">
						<h3 class="taxswitch" data-tax="themes" data-nodeid="{{id}}">Temáticas</h3>
						
						{{#st-themes}}
							<span class="taxtip" data-tax="themes" data-taxid="{{id}}">{{label}}</span>
						{{/st-themes}}
					</div>
					
					<div class="taxsection" data-tax="tools">
					<h3 class="taxswitch" data-tax="tools" data-nodeid="{{id}}">Herramientas</h3>
					
					{{#st-tools}}
						<span class="taxtip" data-tax="tools" data-taxid="{{id}}">{{label}}</span>
					{{/st-tools}}
					
					</div>
					
					<p class="link"><a href={{url}}>Ver más</a> <a class="back" href="#">Volver</a></p>
				</div>
			</script>