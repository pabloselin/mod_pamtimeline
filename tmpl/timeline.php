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
//$document->addStyleSheet( 'https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css');
//$timeline_json = ModPamTimelineHelper::prepareEventsForTimeline(ModPamTimelineHelper::$pammilestonecat, $params->get('timeline_title'), $params->get('timeline_description'));

$timeline_json = ModPamTimelineHelper::prepareEventsForGroupedTimeline($params->get('timeline_title'), $params->get('timeline_description'));
//var_dump($timeline_json);

?>

<div id="pam-timeline" style="width:100%;">

	<?php 	$timeline = new ModPamTimelineHelper;  
			$taberas = $timeline::$eras;
			$idx = 1;
		
	foreach ($taberas as $key=>$tabera) {
		$curidx = $idx++;
		?>

		<div class="era-tab" id="<?php echo $key;?>" data-eracontainer="eracontainer-<?php echo $curidx;?>" data-dataId="<?php echo $key;?>">
		<div class="timeline-interior" id="eracontainer-<?php echo $curidx;?>" height="400"></div>		
		</div>

		<?php
	}
	?>

	<div class="leyenda_hitos">
		<ul class="zonahitos">
			<li class="zonahito-1">Hitos latinoamericanos</li>
			<li class="zonahito-2">Hitos mundiales</li>
			<li class="zonahito-3">Obras</li>
		</ul>
		<ul class="tipohitos">
			<li class="tipohito-2"><span class="tl-icon-image"></span> Hitos Artísticos / Obras</li>
			<li class="tipohito-5"><span class="tl-icon-image"></span> Hitos Históricos</li>
			<li class="tipohito-4"><span class="tl-icon-image"></span> Hitos Teóricos</li>
			<li class="tipohito-3"><span class="tl-icon-image"></span> Hitos Técnicos</li>
		</ul>
	</div>

	<div class="era-nav">
		<ul>
			<?php	
				$idx = 1;		
				foreach($taberas as $key=>$tabera) { 
					$curidx = $idx++;
					?>

					<li><a class="erabtn erabtn-<?php echo $curidx;?>" href="#" data-target="<?php echo $key;?>" ><span><?php echo $tabera['title'];?></span></a></li>

				<?php 
					}
				?>
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