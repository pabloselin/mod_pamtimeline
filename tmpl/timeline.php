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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/timeline.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamtimeline.js');
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/timeline.css');
$document->addStyleSheet( Juri::base() . 'modules/mod_pamtimeline/css/pamtimeline.css');

?>

<div class="pam-timeline">
	<?php 

		foreach($persons as $person) {

			$years = ModPamTimelineHelper::getPersonYears($person['id']);
			$image = ModPamTimelineHelper::getPersonImageUrl($person['id']);

			echo '<h2>' . $person['title'] . '</h2>';
			echo '<p> <img src="' . $image .'" alt="' . $person['title'] . '"></p>';
			
			if($years) {

				foreach($years as $year) {

					echo '<p>' . $year . '</p>';

				}

			}

			
		}

	?>
</div>