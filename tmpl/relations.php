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
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/sigma.min.js');
$document->addScript( Juri::base() . 'modules/mod_pamtimeline/js/pamsigma.js');
?>

<div class="pam-relations">
	
	<?php 
		$article_id = JFactory::getApplication()->input->get('id');
		echo 'Relations GRAPH para: ' . $article_id;
	?>

</div>