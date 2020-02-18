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

class OnePageCheckoutPSPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;

    public function initContent()
    {
        parent::initContent();

        $module = Module::getInstanceByName(Tools::getValue('pm'));

        if (Validate::isLoadedObject($module)) {
            $this->context->smarty->assign(
                'HOOK_PAYMENT_METHOD',
                $module->hookPayment(array('cart' => $this->context->cart))
            );

            $this->setTemplate('payment_execution.tpl');
        }
    }

    public function postProcess()
    {
        $this->context->smarty->assign('page_name', 'order-opc');
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS($this->module->getPathUri().'views/css/front/override.css', 'all');
    }
}
