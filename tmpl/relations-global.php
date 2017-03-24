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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.plugins.animate.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.noverlap.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.plugin.neighborhoods.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/mustache.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamsigma_renderers.js');
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
		$current_person_data = [];

		foreach($persons as $person) {

			//$personFields = ModPamTimelineHelper::getItemFields( $person['id'] );

			$languages = ModPamTimelineHelper::getItemField( $person['id'], 'languages' );
			$themes = ModPamTimelineHelper::getItemField( $person['id'], 'themes' );
			$tools = ModPamTimelineHelper::getItemField( $person['id'], 'tools' );
			$persontype = ModPamTimelineHelper::getItemField( $person['id'], 'persontype' );
			
			$persons_array[] = array(
				'person_id' => $person['id'],
				'person_name' => $person['title'],
				'person_languages' => $languages,
				'person_themes' => $themes,
				'person_tools' => $tools,
				'person_url' => ModPamTimelineHelper::getItemLink($person['id'], $person['alias'], $person['catid']),
				'person_thumbnail' => ModPamTimelineHelper::getItemImageUrl($person['id'], 'S'),
				'person_type' => $persontype
				);
		}

		$json_persons = json_encode($persons_array, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);

	?>

	<div class="pam-relaciones-global">

		<div class="relations-switcher">
			<a href="#" data-tax="languages">Lenguajes</a>
			<a href="#" data-tax="tools">Herramientas</a>
			<a href="#" data-tax="themes">Temáticas</a>
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
		
		var json_relations_raw = cleanJson('<?php echo $json_persons;?>');
		var json_relations = JSON.parse( json_relations_raw );
		var curtax = 'languages';
		var graph_form = 'grid';
		var othertaxs = $('.relations-switcher a');
		var togglers = $('.pam-relaciones-global, #relations-container, .relations-info');

		pamsigmaGlobal(json_relations, 'relations-container', 'languages');
		$('.relations-switcher a[data-tax="languages"]').addClass('active');

		$('.relations-switcher a').on('click', function(e) {

			e.preventDefault();
			var thisEl = $(this);
			
			var thistax = thisEl.attr('data-tax');

			if( !thisEl.hasClass('active')) {
				othertaxs.removeClass('active');
				thisEl.addClass('active');
				pamsigmaToggleInfo(togglers);
				pamsigmaGlobal(json_relations, 'relations-container', thistax);
				curtax = thistax;
			}
		});

		$('.relations-info').on('click', 'a.back', function(e) {
			//var curtax = jQuery('.relations-switcher a.active').attr('data-tax');
			
			e.preventDefault();
			pamsigmaToggleInfo(togglers);

			pamsigmaGlobal(json_relations, 'relations-container', curtax);

		});

	});
</script>

<script id="relations-template" type="x-tmpl-mustache">
				<h2 class="artist-title">{{label}}
				 {{#persontype}}
					- <span class="persontype">{{fieldvaluename}}</span>
				{{/persontype}}</h2>
				
				

				<img src={{image}} alt={{label}}>
				
				<div class="introtext">{{introtext}}</div>	

				<div class="taxsection">
				<h3>Lenguajes</h3>
				
				{{#languages}}
					<span>{{fieldvaluename}}</span>
				{{/languages}}
				</div>

				<div class="taxsection">
				<h3>Herramientas</h3>
				
				{{#tools}}
					<span>{{fieldvaluename}}</span>
				{{/tools}}
				
				</div>

				
				<div class="taxsection">
					<h3>Temáticas</h3>
					
					{{#themes}}
						<span>{{fieldvaluename}}</span>
					{{/themes}}
					
				</div>

				<p class="link"><a href={{link}}>Link</a> <a class="back" href="#">Volver</a></p>
			</script>