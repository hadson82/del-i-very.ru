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

function upgrade_module_2_1_4($object)
{
    $object = $object;

    Configuration::updateValue('OPC_ENABLE_DEBUG', '0');
    Configuration::updateValue('OPC_IP_DEBUG', '');

    return true;
}
