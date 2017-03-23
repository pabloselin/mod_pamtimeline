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
			
			$persons_array[] = array(
				'person_id' => $person['id'],
				'person_name' => $person['title'],
				'person_languages' => $languages,
				'person_themes' => $themes,
				'person_tools' => $tools,
				'person_url' => ModPamTimelineHelper::getItemLink($person['id'], $person['alias'], $person['catid']),
				'person_thumbnail' => ModPamTimelineHelper::getItemImageUrl($person['id'], 'S')
				);
		}

		$json_persons = json_encode($persons_array, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);

	?>

	<div class="pam-relaciones-global">

	<div class="relations-switcher">
		<a href="#" data-tax="languages">Lenguajes</a>
		<a href="#" data-tax="tools">Herramientas</a>
		<a href="#" data-tax="themes">Temas</a>
	</div>

    <div id="relations-container" data-highlight="relations-highlight" data-select="relations-select" data-subhighlight="relations-subhighlight">

	</div>

	<div class="relations-info">
		<script id="relations-template" type="x-tmpl-mustache">
			<h2 class="artist-title">{{label}}</h2>
			<img src="{{&image}}" alt="{{label}}">
			{{#languages}}
				<li>{{fieldvaluename}}</li>	
			{{/languages}}

			<p class="link"><a href="{{&link}}">Link</a></p>
		</script>
			
		<div class="content">
		
		</div>
	</div>

	</div>

	<script>

		var json_relations_raw = cleanJson('<?php echo $json_persons;?>');
		var json_relations = JSON.parse( json_relations_raw );

		var graph_form = 'grid';

		pamsigmaGlobal(json_relations, 'relations-container', 'languages');
		jQuery('.relations-switcher a[data-tax="languages"]').addClass('active');

		jQuery('.relations-switcher a').on('click', function(e) {

			e.preventDefault();
			var thisEl = jQuery(this);
			var others = jQuery('.relations-switcher a');
			var thistax = thisEl.attr('data-tax');

			if( !thisEl.hasClass('active')) {

				others.removeClass('active');
				thisEl.addClass('active');
				
				jQuery('.pam-relaciones-global').removeClass('active');
				jQuery('.pam-relaciones-global .relations-info > div').empty();
				jQuery('#relations-container').removeClass('active');
				jQuery('.relations-info').removeClass('active');

				pamsigmaGlobal(json_relations, 'relations-container', thistax);

			}

			

		})

		
	</script>

</div>