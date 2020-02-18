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

/* get folder admin */
$tmp     = false;
$referer = explode('/', $_SERVER['HTTP_REFERER']);
if (is_array($referer) && count($referer)) {
    $path = dirname(__FILE__).'/../../'.$referer[count($referer) - 2];
    if (file_exists($path.'/init.php')) {
        define('_PS_ADMIN_DIR_', $path);
        define('PS_ADMIN_DIR', $path);
        $tmp = true;
    }
}

require_once dirname(__FILE__).'/../../config/config.inc.php';
if (version_compare(_PS_VERSION_, '1.5') >= 0) {
    if ($tmp) {
        require_once $path.'/init.php';
    } else {
        require_once dirname(__FILE__).'/../../init.php';
    }
} else {
    require_once dirname(__FILE__).'/../../init.php';
}

require_once dirname(__FILE__).'/onepagecheckoutps.php';

if (!Tools::isSubmit('token')
    || Tools::encrypt('onepagecheckoutps/index') != Tools::getValue('token')
    || !Module::isInstalled('onepagecheckoutps')
) {
    die('Bad token');
}

if (Tools::isSubmit('action')) {
    $action = Tools::getValue('action');
    $module = new OnePageCheckoutPS();

    if (method_exists($module, $action)) {
        define('_PTS_SHOW_ERRORS_', true);

        $data_type = 'json';
        if (Tools::isSubmit('dataType')) {
            $data_type = Tools::getValue('dataType');
        }

        switch ($data_type) {
            case 'html':
                die($module->$action());
            case 'json':
                $response = $module->jsonEncode($module->$action());
                die($response);
            default:
                die('Invalid data type.');
        }
    } else {
        die('403 Forbidden');
    }
} else {
    die('403 Forbidden');
}
