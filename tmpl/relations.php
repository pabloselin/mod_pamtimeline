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
error_reporting(0);
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

<div class="pam-relations">
	
	<?php 
		$article_id = JFactory::getApplication()->input->get('id');

		$field_id = ModPamTimelineHelper::getFieldIdByName( 'ID Persona' );
		$current_person_id = ModPamTimelineHelper::getItemField( $article_id, 'personid' );
	
		$persons = ModPamTimelineHelper::getPersons();
		$persons_array = [];
		$current_person_data = [];

		foreach($persons as $person) {

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
		$json_current_person = json_encode($current_person_data, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
		
		echo '<!--<p>ID:' . $current_person_id . '</p>-->';
	?>

	<div class="pam-relaciones">

		<div class="relations-switcher" id="relations-switcher">
			<a href="#" data-tax="languages">Lenguajes</a>
			<a href="#" data-tax="tools">Herramientas</a>
			<a href="#" data-tax="themes">Temas</a>
		</div>

    	<div id="relations-container">
		</div>

		<div class="relations-info">	
			<div class="content">			
			</div>
		</div>

		</div>

	</div>

	<script>

		var json_relations_raw = cleanJson('<?php echo $json_persons;?>');
		var json_relations = JSON.parse( json_relations_raw );
		var current_person = <?php echo $current_person_id;?>;
		var current_person_data = JSON.parse('<?php echo $json_current_person;?>');

		pamsigmaGlobal(json_relations, 'relations-container', 'languages', <?php echo $current_person_id;?>);
		jQuery('.relations-switcher a[data-tax="languages"]').addClass('active');

		//pam sigma implementation
		jQuery(document).ready(function($) {
			
			$('.relations-switcher a').on('click', function(e) {
					e.preventDefault();
					var thisEl = $(this);
					var others = $('.relations-switcher a');
					var thistax = thisEl.attr('data-tax');

					if( !thisEl.hasClass('active')) {

						others.removeClass('active');
						thisEl.addClass('active');
						
						pamsigmaToggleInfo();

						pamsigmaGlobal(json_relations, 'relations-container', thistax, <?php echo $current_person_id;?>);

					}	

				});
		});
	</script>

</div>