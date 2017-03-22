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

require_once dirname(__FILE__) . '/helper.php';


$layout = $params->get('layoutChoice');

if($layout == 'timeline') {
    require JModuleHelper::getLayoutPath('mod_pamtimeline', $layout);
} elseif($layout == 'relations') {
    require JModuleHelper::getLayoutPath('mod_pamtimeline', 'relations');  
} elseif($layout == 'relations_global') {
    require JModuleHelper::getLayoutPath('mod_pamtimeline', 'relations-global');  
}