<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
 */

$tmp     = false;
$referer = explode('/', $_SERVER['HTTP_REFERER']);
if (is_array($referer) && count($referer)) {
    $path = dirname(__FILE__).'/../../../'.$referer[count($referer) - 2];
    if (file_exists($path.'/init.php')) {
        define('_PS_ADMIN_DIR_', $path);
        define('PS_ADMIN_DIR', $path);
        $tmp = true;
    }
}

require_once dirname(__FILE__).'/../../../config/config.inc.php';
if ($tmp) {
    require_once $path.'/init.php';
} else {
    require_once dirname(__FILE__).'/../../../init.php';
}

require_once dirname(__FILE__).'/../onepagecheckoutps.php';

$onepagecheckoutps = new OnePageCheckoutPS();
echo $onepagecheckoutps->uploadImage(Tools::getValue('name'));

//die('sss');



//
//
//
//
//
//
//
//
//
//
//
//require_once(dirname(__FILE__).'/../../../config/config.inc.php');
//require_once(dirname(__FILE__).'/../../../init.php');
//require_once(dirname(__FILE__)."/../onepagecheckoutps.php");
//
//$onepagecheckoutps = new OnePageCheckoutPS();
//echo $onepagecheckoutps->uploadImage(Tools::getValue('name'));