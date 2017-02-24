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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamtimeline.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.layout.forceAtlas2.min.js');
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

			//$personFields = ModPamTimelineHelper::getItemFields( $person['id'] );

			$languages = ModPamTimelineHelper::getItemField( $person['id'], 'languages' );
			$themes = ModPamTimelineHelper::getItemField( $person['id'], 'themes' );
			$tools = ModPamTimelineHelper::getItemField( $person['id'], 'tools' );
			
			$persons_array[] = array(
				'person_id' => $person['id'],
				'person_name' => $person['title'],
				'person_languages' => $languages,
				'person_themes' => $themes,
				'person_tools' => $tools
				);

			if($current_person_id == $person['id']) {

				$current_person_data['person_id'] = $person['id'];
				$current_person_data['person_name'] = $person['title'];
				$current_person_data['person_languages'] = $languages;
				$current_person_data['person_themes'] = $themes;
				$current_person_data['person_tools'] = $tools;

			}

		}


		$json_persons = json_encode($persons_array, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
		$json_current_person = json_encode($current_person_data, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP);
		
		echo '<p>ID:' . $current_person_id . '</p>';
	?>

	<div class="pam-relaciones">
	
	<div id="relations-select">
		<select name="select_tax">
			<option value="languages">Lenguajes</option>
			<option value="themes">Temáticas</option>
			<option value="tools">Herramientas</option>
		</select>
	</div>
	<div id="relations-highlight"></div>
	<div id="relations-subhighlight"></div>
	<div id="relations-container" data-highlight="relations-highlight" data-select="relations-select" data-subhighlight="relations-subhighlight">
	</div>

	</div>

	<script>

		var json_relations_raw = cleanJson('<?php echo $json_persons;?>');
		var json_relations = JSON.parse( json_relations_raw );
		var current_person = <?php echo $current_person_id;?>;
		var current_person_data = JSON.parse('<?php echo $json_current_person;?>');

		pamsigmaReload('languages', json_relations, current_person, current_person_data, 'relations-container');

		//pam sigma implementation
	jQuery(document).ready(function($) {
		$('select[name="select_tax"]').on('change', function() {
			
			var value = this.value;
			console.log( this.value );
			
			pamsigmaReload(value, json_relations, current_person, current_person_data, 'relations-container');

		});
	});

		
	</script>

</div>