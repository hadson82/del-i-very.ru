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

class OnePageCheckoutPSLoginModuleFrontController extends ModuleFrontController
{
    public $ssl                 = true;
    public $display_column_left = false;
    public $name                = 'login';

    public function initContent()
    {
        parent::initContent();

        if (!class_exists('http_class')) {
            include _PS_MODULE_DIR_.'onepagecheckoutps/lib/social_network/http.php';
        }
        if (!class_exists('oauth_client_class_pts')) {
            include _PS_MODULE_DIR_.'onepagecheckoutps/lib/social_network/oauth_client.php';
        }

        $client = new oauth_client_class_pts;

        $opc_social_networks = $this->module->jsonDecode($this->module->config_vars['OPC_SOCIAL_NETWORKS']);

        $server = Tools::strtolower(Tools::getValue('sv'));

        if (!empty($server) && !empty($opc_social_networks)) {
            $client->server             = Tools::getValue('sv');
            $client->redirect_uri       = $this->context->link->getModuleLink(
                'onepagecheckoutps',
                'login',
                array('sv' => Tools::getValue('sv'))
            );
            $client->client_id          = $opc_social_networks->{$server}->client_id;
            $client->client_secret      = $opc_social_networks->{$server}->client_secret;
            $client->scope              = $opc_social_networks->{$server}->scope;
            $client->configuration_file = dirname(__FILE__).'/../../lib/social_network/oauth_configuration.json';

            switch ($client->server) {
                case 'Google':
                    $client->offline = true;
                    break;
            }

            $headers     = array();
            $return_data = array();

            if (($success = $client->Initialize())) {
                if (($success = $client->Process())) {
                    if (Tools::strlen($client->access_token)) {
                        switch ($client->server) {
                            case 'Facebook':
                                $success = $client->CallAPI(
                                    'https://graph.facebook.com/v2.3/me?fields=email,first_name,last_name',
                                    'GET',
                                    array(),
                                    array('FailOnAccessError' => true),
                                    $return_data
                                );

                                $headers['facebook'] = array(
                                    'firstname' => 'first_name',
                                    'lastname'  => 'last_name',
                                    'email'     => 'email'
                                );
                                break;
                            case 'Google':
                                $success = $client->CallAPI(
                                    'https://www.googleapis.com/oauth2/v1/userinfo',
                                    'GET',
                                    array(),
                                    array('FailOnAccessError' => true),
                                    $return_data
                                );

                                $headers['google'] = array(
                                    'firstname' => 'given_name',
                                    'lastname'  => 'family_name',
                                    'email'     => 'email'
                                );
                                break;
                        }
                    }
                }
                $success = $client->Finalize($success);
            }

            if ($success && !empty($return_data) && property_exists($return_data, 'email')) {
                $customer = Customer::getByEmail($return_data->email);
                $customer = new Customer((int) $customer->id);

                if (!Validate::isLoadedObject($customer)) {
                    foreach ($headers[$server] as $field => $value) {
                        $customer->{$field} = $return_data->{$value};
                    }

                    $password = Tools::passwdGen();

                    $customer->passwd = md5(pSQL(_COOKIE_KEY_.$password));

                    $delivery_address = null;
                    $invoice_address  = null;

                    $this->module->createCustomer($customer, $delivery_address, $invoice_address, $password, false);
                } else {
                    $this->module->singInCustomer($customer);
                }

                Db::getInstance(_PS_USE_SQL_SLAVE_)->delete('opc_social_customer', 'id = '.$return_data->id);
                Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(
                    'opc_social_customer',
                    array(
                        'id' => $return_data->id,
                        'id_customer' => $customer->id,
                        'network' => $server
                    )
                );
            }

            $redirect_url = $this->context->link->getPageLink('my-account');
            if ($this->context->cart->nbProducts()) {
                $redirect_url = $this->context->link->getPageLink('order-opc');
            }

            echo '<script>window.opener.location.href="'.$redirect_url.'";</script>';
            echo '<script>window.opener.focus();</script>';
            echo '<script>self.close();</script>';

            if ($client->exit) {
                exit;
            }
        }
    }
}
