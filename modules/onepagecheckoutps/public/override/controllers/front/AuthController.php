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

class AuthController extends AuthControllerCore
{
    public function init()
    {
        Tools::redirectLink('index.php?controller=orderopc?rc=1&'.$_SERVER['QUERY_STRING']);
    }
}
