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

//Scripts and styles
$document = JFactory::getDocument();
//$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/timeline.js');
$document->addScript( 'https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js' );
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamtimeline.js');
$document->addStyleSheet( 'https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css' );
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/pamtimeline.css');

$timeline_json = ModPamTimelineHelper::prepareEventsForTimeline(ModPamTimelineHelper::$pammilestonecat, $params->get('timeline_title'), $params->get('timeline_description'));

?>

<div id="pam-timeline" style="width:100%; height:600px;">

</div>

<script>
	var json_content = cleanJson('<?php echo $timeline_json;?>');
	
	var timeline_json = JSON.parse(json_content, function(key, value) {
		
		if(key === 'text' && typeof value === 'string') {

			return decodeEntities(value);			

		} else {

			return value;	

		}
		
	});
	var timeline_options = {
		debug: false,
		language: 'es'
	}
	window.timeline = new TL.Timeline('pam-timeline', timeline_json, timeline_options);
</script>