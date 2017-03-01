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

$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pam_common.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/timeline.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamtimeline.js');
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/lineatiempo.css');

//$timeline_json = ModPamTimelineHelper::prepareEventsForTimeline(ModPamTimelineHelper::$pammilestonecat, $params->get('timeline_title'), $params->get('timeline_description'));

$timeline_json = ModPamTimelineHelper::prepareEventsForGroupedTimeline($params->get('timeline_title'), $params->get('timeline_description'));

//var_dump($timeline_json);

?>

<div id="pam-timeline" style="width:100%;">
	
	<div class="era-tab" id="era-1" data-eracontainer="eracontainer-1" data-dataId="era1">
		<div class="timeline-interior" id="eracontainer-1" height="400"></div>		
	</div>
	
	<div class="era-tab" id="era-2" data-eracontainer="eracontainer-2"  data-dataId="era2">
		<div class="timeline-interior" id="eracontainer-2" height="400"></div>		
	</div>

	<div class="era-tab" id="era-3" data-eracontainer="eracontainer-3" data-dataId="era3">
		<div class="timeline-interior" id="eracontainer-3" height="400"></div>		
	</div>

	<div class="era-nav">
		<ul>
			<li><a class="erabtn erabtn-1" href="#" data-target="era-1" ><span>S XX Primera Mitad</span></a></li>
			<li><a class="erabtn erabtn-2" href="#" data-target="era-2"><span>S XX Segunda Mitad</span></a></li>
			<li><a class="erabtn erabtn-3" href="#" data-target="era-3"><span>S XX Masificación Computadores - Internet</span></a></li>
		</ul>
	</div>
</div>

<script>
	var era1_content = cleanJson('<?php echo $timeline_json['era1'];?>');
	var era2_content = cleanJson('<?php echo $timeline_json['era2'];?>');
	var era3_content = cleanJson('<?php echo $timeline_json['era3'];?>');

	var json_content = {
		era1: era1_content,
		era2: era2_content,
		era3: era3_content
	}

</script>