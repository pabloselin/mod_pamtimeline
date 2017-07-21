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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/alchemyjs/dist/scripts/vendor.js' );
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/bower_components/alchemyjs/dist/alchemy.js' );
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/bower_components/alchemyjs/dist/alchemy.css' );
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
				'id' => $person['id'],
				'caption' => $person['title'],
				// 'x' => rand(-980,-200),
				// 'y' => rand(-380,-100),
				'languages' => $languages['languages'],
				'themes' => $themes['themes'],
				'tools' => $tools['tools'],
				'url' => ModPamTimelineHelper::getItemLink($person['id'], $person['alias'], $person['catid']),
				'thumbnail' => ModPamTimelineHelper::getItemImageUrl($person['id'], 'S'),
				'type' => $persontype,
				'color' => '#808080',
				'size' => 6
			);

		}
		
		//$languages_edges = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'languages');
		$edges['languages'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'languages');
		$edges['themes'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'themes');
		$edges['tools'] = ModPamTimelineHelper::MakeEdges($graph_items['nodes'], 'tools');

		// //Test con mas nodos
		// for($i = 0; $i < 200; $i++) {
		// 	$graph_items['nodes'][] = array(
		// 		'id' => 'fakenode-' . $i,
		// 		'label' => 'fakenode:' . $i,
		// 		'x' => rand(-980,-200),
		// 		'y' => rand(-380,-100),
		// 		'url' => '',
		// 		'thumbnail' => '',
		// 		'type' => 'cool guy',
		// 		'color' => '#808080',
		// 		'size' => 6
		// 	);
		// }

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
		//Sigma Init
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

		var config = {
			"divSelector": '#relations-container',
			"dataSource": json_obj
		}

		var relaciones = new Alchemy(config);
		

		//DOM Interactions
		jQuery(document).ready(function($) {
			
			$('.relations-switcher a').on('click', function(e) {
				e.preventDefault;
				if(!$(this).hasClass('active')) {
					curtax = $(this).attr('data-tax');
					
					$('.relations-switcher a, #taxitems ul.active').removeClass('active');
					$(this).addClass('active');

					$('#taxitems ul[data-tax="' + curtax + '"]').addClass('active');
					
				}
				
			});
		});

</script>

<script id="relations-template" type="x-tmpl-mustache">
				<h2 class="artist-title"><a href={{link}}>{{label}}</a></h2>
				
				{{#persontype}}
				<span class="persontype">{{fieldvaluename}}</span>
				{{/persontype}}

				<div class="mobile-nav">
					<a class="infomobile" href="#">+ info</a>
					<a class="back backmobile" href="#">Volver</a>
				</div>
				
				
				<div class="info-wrapper">
					<img src={{image}} alt={{label}}>
					
					<div class="introtext">{{introtext}}</div>	
					
					<div class="taxsection">
					<h3>Lenguajes</h3>
					
					{{#languages}}
						<span class="taxtip" data-tax="languages" data-taxid="{{fieldvalueid}}">{{fieldvaluename}}</span>
					{{/languages}}
					</div>
					
					<div class="taxsection">
						<h3>Temáticas</h3>
						
						{{#themes}}
							<span class="taxtip" data-tax="themes" data-taxid="{{fieldvalueid}}">{{fieldvaluename}}</span>
						{{/themes}}
						
					</div>
					
					<div class="taxsection">
					<h3>Herramientas</h3>
					
					{{#tools}}
						<span class="taxtip" data-tax="tools" data-taxid="{{fieldvalueid}}">{{fieldvaluename}}</span>
					{{/tools}}
					
					</div>
					
					<p class="link"><a href={{link}}>Ver más</a> <a class="back" href="#">Volver</a></p>
				</div>
			</script>