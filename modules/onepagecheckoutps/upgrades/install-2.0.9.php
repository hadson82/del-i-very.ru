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

function upgrade_module_2_0_9($object)
{
    $object = $object;
    
    $modules_without_popup = Configuration::get('OPC_MODULES_WITHOUT_POPUP');
    $modules_without_popup .= ',sequrapayment';

    Configuration::updateValue('OPC_MODULES_WITHOUT_POPUP', $modules_without_popup);

    return true;
}
