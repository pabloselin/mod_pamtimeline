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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.min.js');
//$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.parsers.json.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.plugins.animate.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.noverlap.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.forceAtlas2.min.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.forceLink_supervisor.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.forceLink.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.plugin.neighborhoods.min.js');
//$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.renderers.edgeLabels.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.canvas.edgehovers_labels.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/mustache.min.js');
// $document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamsigma_renderers.js');
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

		$all_tools = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['tools'] ));
		$all_languages = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['languages'] ));
		$all_themes = json_decode(ModPamTimelineHelper::getFieldValues( ModPamTimelineHelper::$pamfieldassocs['themes'] ));

		foreach($persons as $person) {

			//$personFields = ModPamTimelineHelper::getItemFields( $person['id'] );

			$languages = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'languages' );
			$themes = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'themes' );
			$tools = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'tools' );
			$persontype = ModPamTimelineHelper::getItemFieldIds( $person['id'], 'persontype' );

			$graph_items['nodes'][] = array(
				'id' => $person['id'],
				'label' => $person['title'],
				'x' => 1,
				'y' => 2,
				'languages' => $languages['languages'],
				'themes' => $themes['themes'],
				'tools' => $tools['tools'],
				'url' => ModPamTimelineHelper::getItemLink($person['id'], $person['alias'], $person['catid']),
				'thumbnail' => ModPamTimelineHelper::getItemImageUrl($person['id'], 'S'),
				'type' => $persontype,
				'color' => '#808080',
				'size' => 3
			);
		
			
		}
		$json_nodes = json_encode($graph_items, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
	?>

	<div class="pam-relaciones-global">

		<div class="relations-switcher">
			<a href="#" data-tax="languages">Lenguajes</a>
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

	jQuery(document).ready(function($) {
		var curtax = 'languages';

		var json_nodes = cleanJson('<?php echo $json_nodes;?>');
		var json_obj = JSON.parse(json_nodes);
		console.log(json_obj);
		relaciones = new sigma({
			graph: json_obj,
			container: 'relations-container',
			settings: {
				mouseWheelEnabled: false
			}
		});

		var config = {
			nodeMargin: 3.0,
			scaleNodes: 1.3
		};

		
		var listener = relaciones.configNoverlap(config);

		
		listener.bind('start stop interpolate', function(event) {
			//console.log(event.type);
		});

		pamEdges(curtax, json_obj);

		
		relaciones.startNoverlap();


		// var json_relations_raw = cleanJson('<?php echo $json_persons;?>');
		// var json_relations = JSON.parse( json_relations_raw );
		// var curtax = 'languages';
		// var graph_form = 'grid';
		// var othertaxs = $('.relations-switcher a');
		// var togglers = $('.pam-relaciones-global, #relations-container, .relations-info');

		// var rels = pamInitSigma('relations-container');

		// pamsigmaGlobal(rels, json_relations, 'relations-container', 'languages',null, 1);
		// pamTaxDropdown(rels);

		// $('.relations-switcher a[data-tax="languages"]').addClass('active');

		// $('.relations-switcher a').on('click', function(e) {

		// 	e.preventDefault();
		// 	var thisEl = $(this);
			
		// 	var thistax = thisEl.attr('data-tax');

		// 	if( !thisEl.hasClass('active')) {
		// 		othertaxs.removeClass('active');
		// 		thisEl.addClass('active');
		// 		pamsigmaToggleInfo(togglers);
		// 		pamsigmaGlobal(rels, json_relations, 'relations-container', thistax, null, 1);
		// 		curtax = thistax;
		// 		$('#taxitems ul').hide().removeClass('active');
		// 		$('ul[data-tax="' + thistax + '"]').show().addClass('active');
		// 		pamToggleTax('show');
		// 	}
		// });

		// $('.relations-info').on('click', 'a.back, span.taxtip', function(e) {
		// 	//var curtax = jQuery('.relations-switcher a.active').attr('data-tax');
		// 	e.preventDefault();
		// 	pamsigmaToggleInfo(togglers);
		// 	pamToggleTax('show');
		// 	pamsigmaGlobal(rels, json_relations, 'relations-container', curtax, null, 5);
		// 	pamResetZoom(rels);
		// 	$('.relations-info').removeClass('expanded');
		// 	if($(this).attr('data-taxid'))
		// 		pamHighlightNodes(rels, $(this).attr('data-tax'), $(this).attr('data-taxid'))
		// });

		// $('.relations-info').on('click', 'a.infomobile', function(e) {
		// 	e.preventDefault();
		// 	var relbox = $('.relations-info');
		// 	if(relbox.hasClass('expanded')) {
		// 		relbox.removeClass('expanded');
		// 		$(this).empty().text('+ info');
		// 	} else {
		// 		relbox.addClass('expanded');
		// 		$(this).empty().text('cerrar');
		// 	}
				
		// });

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