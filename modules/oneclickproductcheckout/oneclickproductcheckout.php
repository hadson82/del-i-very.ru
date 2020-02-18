<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(dirname(__FILE__).'/config.php');
require_once(dirname(__FILE__).'/classes/analytics/GoogleAnalytics.php');

class OneClickProductCheckout extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'oneclickproductcheckout';
		$this->tab = 'front_office_features';
		$this->version = '2.0.4';
		$this->author = 'SeoSA';
		$this->bootstrap = true;
		$this->need_instance = 0;
		parent::__construct();
		$this->displayName = $this->l('One click product checkout');
		$this->description = $this->l('Checkout on product page in one click');
		$this->module_key = 'e89db4c0f6f907e545c94cd4548a1ea4';

		if (defined('_PS_ADMIN_DIR_') && Tools::getValue('ajax') == 'ocpc')
			$this->callAjax();
	}

	public function callAjax()
	{
		$method = 'ajaxProcess'.Tools::toCamelCase(Tools::getValue('method'));
		if (method_exists($this, $method))
			call_user_func(array($this, $method));
	}

	public function getConfigurationFields($disable_email_field = false)
	{
		$fields = unserialize(Configuration::get('OCPC_FIELDS'));
		if ($disable_email_field && isset($fields['email']))
			unset($fields['email']);
		return $fields;
	}
	public function setConfigurationFields($fields)
	{
		Configuration::updateValue('OCPC_FIELDS', serialize($fields));
	}
	public function getDefaultConfiguratonFields()
	{
		return array(
			'firstname' => array(
				'type' => 'text',
				'label' => 'Firsname',
				'validate' => 'isName',
				'required' => true,
				'visible' => true,
				'position' => 0
			),
			'lastname' => array(
				'type' => 'text',
				'label' => 'Lastname',
				'validate' => 'isName',
				'required' => true,
				'visible' => true,
				'position' => 2
			),
			'email' => array(
				'type' => 'text',
				'label' => 'Email',
				'validate' => 'isEmail',
				'required' => true,
				'visible' => true,
				'position' => 3
			),
			'phone' => array(
				'type' => 'text',
				'label' => 'Home phone',
				'validate' => 'isPhoneNumber',
				'required' => true,
				'visible' => true,
				'position' => 4,
				'mask_visible' => true
			),
			'phone_mobile' => array(
				'type' => 'text',
				'label' => 'Mobile phone',
				'validate' => 'isPhoneNumber',
				'required' => true,
				'visible' => true,
				'position' => 5,
				'mask_visible' => true
			),
			'address1' => array(
				'type' => 'text',
				'label' => 'Address',
				'validate' => 'isAddress',
				'required' => true,
				'visible' => true,
				'position' => 6
			),
			'postcode' => array(
				'type' => 'text',
				'label' => 'Postcode',
				'validate' => 'isPostCode',
				'required' => true,
				'visible' => true,
				'position' => 7
			),
			'city' => array(
				'type' => 'text',
				'label' => 'City',
				'validate' => 'isCityName',
				'required' => true,
				'visible' => true,
				'position' => 8
			),
			'country' => array(
				'type' => 'select',
				'label' => 'Country',
				'validate' => 'isCountryName',
				'required' => true,
				'visible' => true,
				'position' => 9
			)
		);
	}

	public function install()
	{
		$this->installConfiguration();
		if (!parent::install() || !$this->registerHook('displayProductButtons')
			|| !$this->registerHook('displayHeader'))
			return false;
		if(version_compare(_PS_VERSION_, '1.7', '>=') && !$this->registerHook('actionFrontControllerSetMedia'))
			return false;
		return true;
	}

	public function uninstall()
	{
		$this->deleteConfiguration();
		if (!parent::uninstall())
			return false;
		return true;
	}

	public function installConfiguration()
	{
		$languages = Language::getLanguages(false);
		$ocpc_submit_text = array();
		foreach ($languages as $language)
			$ocpc_submit_text[$language['id_lang']] = 'Checkout order';
		Configuration::updateValue('OCPC_SUBMIT_TEXT', serialize($ocpc_submit_text));
		$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
		if (count($carriers))
			Configuration::updateValue('OCPC_ID_CARRIER', $carriers[0]['id_reference']);
		Configuration::updateValue('OCPC_ORDER_STATE', 3);
		Configuration::updateValue('OCPC_GROUP_CUSTOMER', 3);
		Configuration::updateValue('OCPC_ENABLED_PAYMENT', false);
        Configuration::updateValue('OCPC_CART_ADD', 0);
		Configuration::updateValue('OCPC_ALLOW_REPEAT_MAIL_ORDER', 0);
		$this->setConfigurationFields($this->getDefaultConfiguratonFields());
	}

	public function deleteConfiguration()
	{
		Configuration::deleteByName('OCPC_FIELDS');
		Configuration::deleteByName('OCPC_SUBMIT_TEXT');
		Configuration::deleteByName('OCPC_ID_CARRIER');
		Configuration::deleteByName('OCPC_ORDER_STATE');
		Configuration::deleteByName('OCPC_GROUP_CUSTOMER');
		Configuration::deleteByName('OCPC_ENABLED_PAYMENT');
        Configuration::deleteByName('OCPC_CART_ADD');
		Configuration::deleteByName('OCPC_ALLOW_REPEAT_MAIL_ORDER');
	}

	public function checkShowButton()
	{
		$id_product = (int)Tools::getValue('id_product');
		if (!$id_product)
			return false;

		$products = array();
		if (Configuration::get('OCPC_DISABLE_SHOW_BUTTON'))
			$products = explode(',', Configuration::get('OCPC_DISABLE_SHOW_BUTTON'));

		if (!in_array($id_product, $products))
			return true;
		return false;
	}

	public function hookDisplayProductButtons()
	{
		if (!$this->checkShowButton())
			return '';
		$this->registerSmartyFunctions();
		ToolsModuleOCC::registerSmartyFunctions();
//		$this->context->controller->addJS($this->_path.'views/js/oneclickproductcheckout.js');
//		$this->context->controller->addCSS($this->_path.'views/css/oneclickproductcheckout.css');
		$id_product = Tools::getValue('id_product');
		$product = new Product($id_product, true, $this->context->language->id);
		$fields = $this->getConfigurationFields($this->context->cookie->logged);

		$fields_mask = array();
        $script = null;
		$default_config = $this->getDefaultConfiguratonFields();
		foreach ($fields as $name => &$field)
		{
			$field['name'] = $name;
			if (isset($default_config[$name]['mask_visible']) && $default_config[$name]['mask_visible'])
				if ($field['visible'])
					if (isset($field['mask_value']) && $field['mask_value'])
						$fields_mask[$name] = $field['mask_value'];
		}

		if (version_compare(_PS_VERSION_, '1.7', '<'))
		{
			if (count($fields_mask))
			{
				$this->context->controller->addJS($this->_path . 'views/js/inputmask/js/inputmask.js');
				$this->context->controller->addJS($this->_path . 'views/js/inputmask/js/jquery.inputmask.js');
			}
		}

		$cover = Product::getCover($product->id);
		$combinations = $product->getAttributeCombinations($this->context->language->id);
		$combination_images = $product->getCombinationImages($this->context->language->id);
		foreach ($combinations as &$combination)
		{
			$combination['price'] = Product::getPriceStatic($combination['id_product'], true, $combination['id_product_attribute']);
			$combination['quantity'] = Product::getQuantity($product->id, $combination['id_product_attribute']);
		}

		$payments = array();
		if ((int)Configuration::get('OCPC_ENABLED_PAYMENT'))
			$payments = $this->getPaymentMethods();

		$help = array();
		foreach ($this->getConfigurationFields() as $key => $value)
			if (isset($value['help']))
				$help[$key] = Tools::unSerialize($value['help']);

        $id_address = Address::getFirstCustomerAddressId($this->context->customer->id);

		$this->context->smarty->assign(array(
			'fields' => $fields,
			'product_obj' => $product,
			'allow_oosp' => $product->isAvailableWhenOutOfStock((int)$product->out_of_stock),
			'ocpc_combinations' => $combinations,
			'ocpc_combination_images' => $combination_images,
			'cover_product' => $cover,
			'id_default_lang' => $this->context->language->id,
			'OCPC_SUBMIT_TEXT' => unserialize(Configuration::get('OCPC_SUBMIT_TEXT')),
			'auto_complete_fields' => $this->getAutoCompleteFields(),
			'payments' => $payments,
			'enabled_payment' => (int)Configuration::get('OCPC_ENABLED_PAYMENT'),
			'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
			'help' => $help,
			'fields_mask' => Tools::jsonEncode($fields_mask),
            'link_address' => $this->context->link->getPageLink('address'),
            'id_address' => $id_address
		));

		return $this->display(__FILE__, 'right_column_product.tpl');
	}

	public function hookActionFrontControllerSetMedia($params)
	{
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if ('product' === $this->context->controller->php_self) {
                $this->context->controller->registerJavascript('module-ocpc-inputmask',
                    $this->_path.'views/js/inputmask/js/inputmask.js',
                    array('server' => 'remote', 'position' => 'bottom', 'priority' => 150));
                $this->context->controller->registerJavascript('module-ocpc-jq-inputmask',
                    $this->_path.'views/js/inputmask/js/jquery.inputmask.js',
                    array('server' => 'remote', 'position' => 'bottom', 'priority' => 150));
            }
        }
	}

	/**
	 * @void
	 */
	public function registerSmartyFunctions()
	{
		$smarty = Context::getContext()->smarty;
		if (!array_key_exists('no_escape', $smarty->registered_plugins['modifier']))
			smartyRegisterFunction($smarty, 'modifier', 'no_escape', array(__CLASS__, 'noEscape'));

		if (!array_key_exists('convertPrice', $smarty->registered_plugins['function']))
			smartyRegisterFunction($smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));

		if (!array_key_exists('displayPrice', $smarty->registered_plugins['function']))
			smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));

		$smarty = $this->context->smarty;
		if (!array_key_exists('get_image_lang', $smarty->registered_plugins['function']))
			smartyRegisterFunction($smarty, 'function', 'get_image_lang', array($this, 'getImageLang'));
		if (class_exists('TransModOCC'))
		{
			if (!array_key_exists('ld', $smarty->registered_plugins['modifier']))
				smartyRegisterFunction($smarty, 'modifier', 'ld', array(TransModOCC::getInstance(), 'ld'));
		}
	}

	public static function noEscape($string)
	{
		return $string;
	}

	public function stringToCss($string)
	{
		$css_features = array();

		if (!$string)
			return $css_features;
		$features = explode(';', $string);

		if (is_array($features) && count($features))
		{
			foreach ($features as $feature)
			{
				list($property, $value) = explode(':', $feature);
				$css_features[trim($property)] = trim($value);
			}
		}

		return $css_features;
	}

	public function getPaymentMethods()
	{
        $payments = array();
		$default_payment_icon = _MODULE_DIR_.$this->name.'/views/img/default_payment_icon.png';
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $payment_methods_hook = Hook::exec('displayPayment', array(), null, true);
        } else {
            $payment_finder = new PaymentOptionsFinder();
            $payment_finder->setHookName('paymentReturn');
            $payment_finder->find();
            $find_payments = $payment_finder->present();

            foreach ($find_payments as $find_payment) {
                $id_payment = preg_replace('/[^0-9]/', '', $find_payment[0]['id']);
                $payments[$id_payment]['img'] = $this->_path.'../'.$find_payment[0]['module_name'].'/logo.png';
                $payments[$id_payment]['html'] = '<a href="'.$find_payment[0]['action'].'"></a>';
                $payments[$id_payment]['name'] = $find_payment[0]['call_to_action_text'];
            }
            return $payments;
        }

		if (is_array($payment_methods_hook) && count($payment_methods_hook))
		{
			foreach ($payment_methods_hook as $payment_method_hook)
			{
				$q_payments = phpQuery::newDocument($payment_method_hook);
				if ($q_payments->find('.payment_module')->size())
				{
					foreach ($q_payments->find('.payment_module') as $q_payment)
					{
						$payment = array();

						if (pq($q_payment)->find('img')->size())
							$payment['img'] = pq($q_payment)->find('img')->attr('src');
						elseif (pq($q_payment)->find('a')->size())
						{
							if (pq($q_payment)->find('a')->attr('style'))
							{
								$css = $this->stringToCss(pq($q_payment)->find('a')->attr('style'));
								if (array_key_exists('background-image', $css))
								{
									if (strpos($css['background-image'], 'url(') !== false)
									{
										$img = preg_replace('/^url\((\'|\")/', '', $css['background-image']);
										$img = preg_replace('/(\'|\")\)$/', '', $img);
										$payment['img'] = $img;
									}
								}
								elseif (array_key_exists('background', $css))
								{
									if (strpos($css['background'], 'url(') !== false)
									{
										$img = $default_payment_icon;
										preg_match('/url\((\'|\")(.+)(\'|\")\)/', $css['background'], $matches);
										if (array_key_exists(2, $matches))
											$img = $matches[2];
										$payment['img'] = $img;
									}
								}
								else
									$payment['img'] = $default_payment_icon;
							}
							else
								$payment['img'] = $default_payment_icon;
						}
						else
							$payment['img'] = $default_payment_icon;

						if (pq($q_payment)->find('a')->size())
							$payment['name'] = trim(pq($q_payment)->find('a')->text());
						else
							$payment['name'] = $this->l('Unknown payment method');

						if ($q_payments->find('.payment_module')->size() > 1)
							$payment['html'] = pq($q_payment)->htmlOuter();
						else
							$payment['html'] = $q_payments->htmlOuter();

						$payments[] = $payment;
					}
				}
			}
		}
		return $payments;
	}

	public function getAutoCompleteFields()
	{
		$fields = array(
			'firstname' => '',
			'lastname' => '',
			'email' => '',
			'phone' => '',
			'phone_mobile' => '',
			'country' => '',
			'country_default' => Configuration::get('PS_COUNTRY_DEFAULT'),
			'postcode' => '',
			'city' => '',
			'address1' => '',
		);

		if ($this->context->cookie->logged)
		{
			$fields['firstname'] = $this->context->customer->firstname;
			$fields['lastname'] = $this->context->customer->lastname;
			$fields['email'] = $this->context->customer->email;
			$fields['country'] = Country::getCountries((int)$this->context->customer->id_lang, true);
			$id_address = (int)Address::getFirstCustomerAddressId((int)$this->context->customer->id);
			$address = new Address($id_address);
			if (Validate::isLoadedObject($address))
			{
				$fields['phone'] = $address->phone;
				$fields['phone_mobile'] = $address->phone_mobile;
				$fields['address1'] = $address->address1;
				$fields['city'] = $address->city;
				$fields['postcode'] = $address->postcode;
			}
		}
		else
		{
			$id_lang_default = Configuration::get('PS_LANG_DEFAULT');
			$sql = 'SELECT `id_country`, `name` 
					FROM `'._DB_PREFIX_.'country_lang`
					WHERE `id_lang` = "'.$id_lang_default.'" ORDER BY `name`';
			$fields['country'] = Db::getInstance()->ExecuteS($sql);
		}

		return $fields;
	}

	public function hookDisplayHeader()
	{
		if (Tools::getValue('controller') != 'product')
			return '';

		if (!$this->checkShowButton())
			return '';

		$fields = $this->getConfigurationFields();
		foreach ($fields as $name => &$field)
			$field['name'] = $name;

		$precision = (Configuration::get('PS_PRICE_DISPLAY_PRECISION') ? Configuration::get('PS_PRICE_DISPLAY_PRECISION') : null);
		$newpresta = (version_compare(_PS_VERSION_, '1.7.0', '>') ? true : null);

		$this->context->smarty->assign(array(
			'precision' => $precision,
			'newpresta' => $newpresta,
			'fields_json' => Tools::jsonEncode($fields),
		));

		if (Tools::isSubmit('ajax'))
		{
			if (Tools::getValue('method') == 'submitOneClickCheckout')
				$this->submitOneClickCheckout();
			if (Tools::getValue('method') == 'loadPaymentMethods')
				$this->loadPaymentMethods();
			if (Tools::getValue('method') == 'getAttributesProduct')
				$this->getAttributesProduct();
		}
		$this->context->controller->addJS($this->_path.'views/js/oneclickproductcheckout.js');
		$this->context->controller->addCSS($this->_path.'views/css/oneclickproductcheckout.css');

        if(version_compare(_PS_VERSION_, '1.7', '>='))
        {
            $this->context->controller->addCSS($this->_path.'views/css/front17.css');
        }

		return $this->display(__FILE__, 'header.tpl');
	}

	public function loadPaymentMethods()
	{
		$this->registerSmartyFunctions();
		$this->context->smarty->assign('payments', $this->getPaymentMethods());
		echo $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/list_payments.tpl');
	}

	public function submitOneClickCheckout()
	{
		$t = TransModOCC::getInstance();
		$this->registerSmartyFunctions();
		$errors = array();
		$id_product = Tools::getValue('id_product');
		$check_payment_method = (int)Tools::getValue('check_payment_method');
		$id_product_attribute = Tools::getValue('id_product_attribute');
		$qty = Tools::getValue('qty');

		$fields = $this->getConfigurationFields($this->context->cookie->logged);

		$country = new Country();
		if(!$country->id)
			$country->id = Configuration::get('PS_COUNTRY_DEFAULT');

		if (!$country->active)
		{
			$one_active_country = $this->getOneActiveCountry();
			if (is_array($one_active_country) && count($one_active_country))
				$country = new Country($one_active_country['id_country'], $this->context->language->id);
			else
				$errors[] = $this->l('All countries are disabled');
		}

		$this->context->country = $country;
		$postcode = '000000000000';
		$default_fields = array(
			'firstname' => 'default',
			'lastname' => 'default',
			'email' => md5(time()).'@default.ru',
			'password' => md5(time()._COOKIE_KEY_),
			'id_country' => $country->id,
			'postcode' => Tools::substr($postcode, 0, Tools::strlen($country->zip_code_format)),
			'city' => 'default',
			'phone' => '89000000000',
			'phone_mobile' => '89000000000',
			'address1' => 'default',
			'address2' => null,
			'id_state' => null
		);

		$post_fields = array();
		foreach (array_keys($default_fields) as $name)
		{
			if (isset($fields[$name]) && $fields[$name]['visible'])
			{
				$field_value = Tools::getValue($name);
				if ($fields[$name]['required'] && empty($field_value))
					$errors[] = '<b>'.$t->ld($fields[$name]['label']).'</b> '.$this->l('not filled');
				if (!empty($field_value) && !forward_static_call(array('Validate', $fields[$name]['validate']), $field_value))
					$errors[] = '<b>'.$t->ld($fields[$name]['label']).'</b> '.$this->l('incorrect');
				if ($name == 'email' && Customer::getCustomersByEmail($field_value))
				{

					if (!Configuration::get('OCPC_ALLOW_REPEAT_MAIL_ORDER'))
						$errors[] = $this->l('This is user exists');
					else
					{
						$obj_customer = new Customer();
						$obj_customer->getByEmail($field_value);
						$obj_customer->is_guest = true;
						$obj_customer->save();
					}
				}
				if (empty($field_value) && !$fields[$name]['required'])
					$post_fields[$name] = $default_fields[$name];
				else
					$post_fields[$name] = $field_value;
			}
			else
				$post_fields[$name] = $default_fields[$name];
		}

		$default_carrier = Configuration::get('OCPC_ID_CARRIER');
		if (!$default_carrier)
			$errors[] = $this->l('Carrier undefined');
		if ($default_carrier)
		{
			$carrier = Carrier::getCarrierByReference($default_carrier);
			if (!$carrier || !$carrier->active)
				$errors[] = $this->l('Carrier undefined or not exists');
		}

		if ((int)Configuration::get('OCPC_ENABLED_PAYMENT') && !$check_payment_method)
			$errors[] = $this->l('Please, select payment method!');

		if (!count($errors))
		{
			$customer = new Customer($this->context->cookie->logged ? $this->context->customer->id : null);
			$customer->firstname = $post_fields['firstname'];
			$customer->lastname = $post_fields['lastname'];

			if (!$this->context->cookie->logged)
			{
				$customer->email = $post_fields['email'];
				$customer->passwd = Tools::encrypt($post_fields['password']);
				$customer->newsletter = false;
				$customer->id_default_group = (Configuration::get('OCPC_GROUP_CUSTOMER') ? Configuration::get('OCPC_GROUP_CUSTOMER') : null);
				$customer->optin = false;
			}
			try{
				$customer->save();
			}
			catch (Exception $e)
			{
				$this->addlog('OneClickCheckout($customer->save()) - '.$e->getMessage());
			}
			catch (PrestaShopException $e)
			{
				$this->addlog('OneClickCheckout($customer->save()) - '.$e->getMessage());
			}

			$id_address = null;
			if ($this->context->cookie->logged)
				$id_address = (int)Address::getFirstCustomerAddressId($customer->id);

			$address = new Address($id_address);

			if (!$id_address)
			{
				$address->id_customer = $customer->id;
				$address->alias = md5(time());
				foreach ($address as $name => &$value)
					if (!$value && isset($default_fields[$name]))
						$value = $post_fields[$name] ? $post_fields[$name] : $default_fields[$name];

			}

			if (!$this->context->cookie->logged)
			{
				$states = State::getStatesByIdCountry($post_fields['id_country'] ? $post_fields['id_country'] : $country->id);
				$address->id_country = $post_fields['id_country'] ? $post_fields['id_country'] : $country->id;
				$address->id_state = (isset($post_fields['state']) && $post_fields['state'] ? $post_fields['state']
					:  (count($states) ? $states[0]['id_state'] : null));
				$address->postcode = $post_fields['postcode'];
				$address->city = $post_fields['city'];
				$address->phone_mobile = $post_fields['phone_mobile'];
				$address->address1 = $post_fields['address1'];
				$address->address2 = $post_fields['address2'];
				$address->id_customer = $customer->id;
				$address->alias = md5(time());
			}

			$address->phone = $post_fields['phone'];
			$address->firstname = $customer->firstname;
			$address->lastname = $customer->lastname;

			try{
				$address->save();
			}
			catch (Exception $e)
			{
				$this->addlog('OneClickCheckout($address->save()) - '.$e->getMessage());
			}
			catch (PrestaShopException $e)
			{
				$this->addlog('OneClickCheckout($address->save()) - '.$e->getMessage());
			}

			if (Validate::isLoadedObject($customer) && Validate::isLoadedObject($address))
			{
				if (!$this->context->cookie->logged && array_key_exists('email', $fields))
				{
					try{
						Mail::Send(
							$this->context->language->id,
							'account',
							Mail::l('Welcome!'),
							array(
								'{firstname}' => $customer->firstname,
								'{lastname}' => $customer->lastname,
								'{email}' => $customer->email,
								'{passwd}' => $post_fields['password']),
							$customer->email,
							$customer->firstname.' '.$customer->lastname);
					}
					catch (Exception $e)
					{
						$this->addlog('OneClickCheckout(Mail::Send-"password") - '.$e->getMessage());
					}
					catch (PrestaShopException $e)
					{
						$this->addlog('OneClickCheckout(Mail::Send-"password") - '.$e->getMessage());
					}

				}

				$cart = new Cart();
				$cart->id_customer = $customer->id;
				$cart->id_currency = $this->context->cookie->id_currency;
				$cart->id_address_invoice = $address->id;
				$cart->id_address_delivery = $address->id;
				$cart->id_carrier = $carrier->id;
				$cart->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
				$cart->setDeliveryOption(array($address->id => $carrier->id.','));
				$cart->secure_key = $customer->secure_key;

				try{
					$cart_add = $cart->add();
				}
				catch (Exception $e)
				{
					$this->addlog('OneClickCheckout($cart->add) - '.$e->getMessage());
				}
				catch (PrestaShopException $e)
				{
					$this->addlog('OneClickCheckout($cart->add) - '.$e->getMessage());
				}

				if (!isset($cart_add)  || !$cart_add)
				{
					$address->delete();
					$customer->delete();
					die(Tools::jsonEncode(array(
						'hasError' => true,
						'errors' => array(
							$this->l('When you create a shopping cart error occurred')
						)
					)));
				}

				try {
					$updateQty = $cart->updateQty((int)$qty,
						$id_product,
						($id_product_attribute && $id_product_attribute > 0 ? $id_product_attribute : null));
				}
				catch (Exception $e)
				{
					$this->addlog('OneClickCheckout(updateQty) - '.$e->getMessage());
				}
				catch (PrestaShopException $e)
				{
					$this->addlog('OneClickCheckout(updateQty) - '.$e->getMessage());
				}

				if (!isset($updateQty) || !$updateQty)
				{
					$cart->delete();
					$address->delete();
					$customer->delete();
					die(Tools::jsonEncode(array(
						'hasError' => true,
						'errors' => array(
							$this->l('Product no added in cart')
						)
					)));
				}
				if (is_null($this->context->currency))
					$this->context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
				$total = $cart->getOrderTotal(true, Cart::BOTH);

				if (!(int)Configuration::get('OCPC_ENABLED_PAYMENT'))
				{
					try {
						$validationOrder = $this->validateOrder((int)$cart->id,
							Configuration::get('OCPC_ORDER_STATE'),
							$total,
							$this->l('OneClickCheckout'), null, array(), null, false, $customer->secure_key);
					}
					catch (Exception $e)
					{
						$this->addlog('OneClickCheckout(validateOrder) - '.$e->getMessage());
					}
					catch (PrestaShopException $e)
					{
						$this->addlog('OneClickCheckout(validateOrder) - '.$e->getMessage());
					}

					if (!isset($validationOrder) || !$validationOrder)
					{
						$cart->delete();
						$address->delete();
						$customer->delete();
						die(Tools::jsonEncode(array(
							'hasError' => true,
							'errors' => array(
								$this->l('Checkout error')
							)
						)));
					}
				}
				else
				{
					$this->context->cart = new Cart($cart->id);
					$cookie = $this->context->cookie;
					$cookie->id_cart = (int)$this->context->cart->id;
					$cookie->write();
					$this->context->cart->autosetProductAddress();

					CartRule::autoRemoveFromCart($this->context);
					CartRule::autoAddToCart($this->context);
				}

				if (!$this->context->cookie->logged)
				{
					$cookie = $this->context->cookie;
					$cookie->id_customer = (int)$customer->id;
					$cookie->customer_lastname = $customer->lastname;
					$cookie->customer_firstname = $customer->firstname;
					$cookie->logged = 1;
					$customer->logged = 1;
					$cookie->is_guest = $customer->isGuest();
					$cookie->passwd = $customer->passwd;
					$cookie->email = $customer->email;
					$this->context->customer = $customer;

					if (Configuration::get('PS_CART_FOLLOWING')
						&& (empty($cookie->id_cart)
							|| Cart::getNbProducts($cookie->id_cart) == 0)
						&& $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
						$this->context->cart = new Cart($id_cart);
					else
					{
						$id_carrier = (int)$this->context->cart->id_carrier;
						$this->context->cart->id_carrier = 0;
						$this->context->cart->setDeliveryOption(null);
						$this->context->cart->id_address_delivery
							= (int)Address::getFirstCustomerAddressId((int)$customer->id);
						$this->context->cart->id_address_invoice
							= (int)Address::getFirstCustomerAddressId((int)$customer->id);
					}
					$this->context->cart->id_customer = (int)$customer->id;
					$this->context->cart->secure_key = $customer->secure_key;

					if (isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE'))
					{
						$delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
						$this->context->cart->setDeliveryOption($delivery_option);
					}

					$this->context->cart->save();
					$cookie->id_cart = (int)$this->context->cart->id;
					$cookie->write();
					$this->context->cart->autosetProductAddress();

					CartRule::autoRemoveFromCart($this->context);
					CartRule::autoAddToCart($this->context);
				}

                if (Configuration::get('OCPC_ACTIVE_ANALYTICS')) {
                    $ga = new \Seosa\Analytics\GoogleAnalytics();
                    $this->context->smarty->assign(array(
                        'ga_data' => $ga->getData($cart, Configuration::get('OCPC_ENHANCED_ANALITICS')),
                        'key' => Configuration::get('OCPC_ANALYTICS_ACCOUNT_ID')
                    ));
                    $script_analytics = $this->context->smarty->fetch(
                        _PS_MODULE_DIR_.$this->name.'/views/templates/analytics/google_analytics.tpl'
                    );
                }

                die(Tools::jsonEncode(array(
                    'hasError' => false,
                    'google_analytics_script' => isset($script_analytics) ? $script_analytics : ''
                )));
			}
			else
			{
				die(Tools::jsonEncode(array(
					'hasError' => true,
					'errors' => array(
						$this->l('User was created wrong')
					)
				)));
			}
		}
		else
		{
			die(Tools::jsonEncode(array(
				'hasError' => true,
				'errors' => $errors
			)));
		}
	}


	public function addLog($error)
	{
		PrestaShopLogger::addLog($error);
	}

	public function getAttributesProduct()
	{
		$data = explode('&', pSQL(Tools::getValue('form')));

		$html = '';
		$form_data = array();
		if (count($data))
			foreach ($data as $value)
			{
				$a = explode('=', $value);
				if (count($a) != 2)
					continue;
				$form_data[$a[0]] = (int)$a[1];
			}

		if (array_key_exists('id_product', $form_data))
			$product = new Product($form_data['id_product']);

		if (Validate::isLoadedObject($product) && array_key_exists('id_product_attribute', $form_data))
		{
			$groups = $product->getAttributesGroups($this->context->cart->id_lang);
			$attributes = $product->getAttributeCombinationsById($form_data['id_product_attribute'], $this->context->cart->id_lang);
			$attr_color_list = Product::getAttributesColorList(array($product->id));

			$this->context->smarty->assign('groups', $groups);
			$this->context->smarty->assign('attributes', $attributes);
			$this->context->smarty->assign('attr_color_list', $attr_color_list);
			$this->context->smarty->assign('col_img_dir', _PS_COL_IMG_DIR_);
			$html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/attributes.tpl');
		}

		$qty = isset($form_data['qty']) ? $form_data['qty'] : 1;

		if ($html)
			die(Tools::jsonEncode(array(
				'hasError' => false,
				'html' => $html,
				'qty' => $qty
			)));

		die(Tools::jsonEncode(array(
			'hasError' => true
		)));
	}

	public function adminCustomizeOrderStateForm()
	{
		$fields = array(
			'settings' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Settings')
					),
					'input' => array(
						array(
							'label' => $this->l('Default state order'),
							'name' => 'OCPC_ORDER_STATE',
							'type' => 'select',
							'options' => array(
								'query' => OrderState::getOrderStates($this->context->language->id),
								'id' => 'id_order_state',
								'name' => 'name'
							)
						),
						array(
							'label' => $this->l('Carrier default'),
							'name' => 'OCPC_ID_CARRIER',
							'type' => 'select',
							'options' => array(
								'query' => Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS),
								'id' => 'id_reference',
								'name' => 'name'
							)
						),
						array(
							'label' => $this->l('Select group of customers, in which he will added after creating order'),
							'name' => 'OCPC_GROUP_CUSTOMER',
							'type' => 'select',
							'options' => array(
								'query' => Group::getGroups($this->context->language->id),
								'id' => 'id_group',
								'name' => 'name'
							)
						),
						array(
							'label' => $this->l('Text on button'),
							'name' => 'OCPC_SUBMIT_TEXT',
							'type' => 'text',
							'lang' => true
						),
						array(
							'label' => $this->l('Select product, where not show button'),
							'name' => 'OCPC_DISABLE_SHOW_BUTTON',
							'type' => 'search_product'
						),
						array(
							'label' => $this->l('Allow repeat email in quick order'),
							'name' => 'OCPC_ALLOW_REPEAT_MAIL_ORDER',
							'type' => 'switch',
							'values' => array(
								array(
									'id' => 'OCPC_ALLOW_REPEAT_MAIL_ORDER_on',
									'value' => 1,
									'label' => $this->l('Enabled')
								),
								array(
									'id' => 'OCPC_ALLOW_REPEAT_MAIL_ORDER_off',
									'value' => 0,
									'label' => $this->l('Disabled')
								)
							),
						),
						array(
							'label' => $this->l('Payment methods enabled?'),
							'name' => 'OCPC_ENABLED_PAYMENT',
							'type' => 'switch',
							'values' => array(
								array(
									'id' => 'OCPC_ENABLED_PAYMENT_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => 'OCPC_ENABLED_PAYMENT_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'is_bool' => true
						),
                        array(
                            'label' => $this->l('Add products to cart?'),
                            'name' => 'OCPC_CART_ADD',
                            'type' => 'switch',
                            'values' => array(
                                array(
                                    'id' => 'OCPC_CART_ADD_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'OCPC_CART_ADD_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'is_bool' => true
                        )
					),
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button',
						'desc' => ''
					)
				)
			)
		);
		$helper_form = new HelperForm();

		$languages = Language::getLanguages(false);
		foreach ($languages as &$language)
			if ($language['id_lang'] == $this->context->language->id)
				$language['is_default'] = 1;
			else
				$language['is_default'] = 0;

		$helper_form->languages = $languages;
		$helper_form->allow_employee_form_lang = 0;
		$helper_form->default_form_language = $this->context->language->id;

		$products = array();
		if (Configuration::get('OCPC_DISABLE_SHOW_BUTTON'))
		{
			$ids_product = explode(',', Configuration::get('OCPC_DISABLE_SHOW_BUTTON'));
			$products = $this->getSearchProducts($ids_product);
		}

		$ocpc_submit_text = unserialize(Configuration::get('OCPC_SUBMIT_TEXT'));
		foreach ($languages as $value)
		{
			if (!array_key_exists($value['id_lang'], $ocpc_submit_text))
				$ocpc_submit_text[$value['id_lang']] = null;
		}

		$helper_form->fields_value = array(
			'OCPC_ORDER_STATE' => Configuration::get('OCPC_ORDER_STATE'),
			'OCPC_ID_CARRIER' => Configuration::get('OCPC_ID_CARRIER'),
			'OCPC_SUBMIT_TEXT' => $ocpc_submit_text,
			'OCPC_GROUP_CUSTOMER' => Configuration::get('OCPC_GROUP_CUSTOMER'),
			'OCPC_DISABLE_SHOW_BUTTON_ARRAY' => $products,
			'OCPC_DISABLE_SHOW_BUTTON' => Configuration::get('OCPC_DISABLE_SHOW_BUTTON'),
			'OCPC_ALLOW_REPEAT_MAIL_ORDER' => (int)Configuration::get('OCPC_ALLOW_REPEAT_MAIL_ORDER'),
			'OCPC_ENABLED_PAYMENT' => (int)Configuration::get('OCPC_ENABLED_PAYMENT'),
            'OCPC_CART_ADD' => (int)Configuration::get('OCPC_CART_ADD')
		);
		$helper_form->module = $this;
		$helper_form->override_folder = 'customize_order_state_form/';
		$helper_form->table = 'customize_order_state_form';
		$helper_form->show_toolbar = false;
		$helper_form->token = Tools::getValue('token');
		$helper_form->currentIndex = 'index.php?controller=AdminModules&configure='.$this->name
			.'&tab_module=front_office_features&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
		$helper_form->tpl_vars['ajax_path'] = $helper_form->currentIndex;
		$helper_form->submit_action = 'submitSaveCustomizeOrderState';
		return $helper_form->generateForm($fields);
	}

    public function adminCustomizeAnalyticsForm()
    {
        $fields = array(
            'settings' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings Google analytics')
                    ),
                    'input' => array(
                        array(
                            'label' => $this->l('Active'),
                            'name' => 'OCPC_ACTIVE_ANALYTICS',
                            'type' => 'switch',
                            'values' => array(
                                array(
                                    'id' => 'OCPC_ACTIVE_ANALYTICS_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'OCPC_ACTIVE_ANALYTICS_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Enhanced analytics?'),
                            'name' => 'OCPC_ENHANCED_ANALITICS',
                            'type' => 'switch',
                            'values' => array(
                                array(
                                    'id' => 'OCPC_ENHANCED_ANALITICS_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'OCPC_ENHANCED_ANALITICS_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'is_bool' => true
                        ),
                        array(
                            'label' => $this->l('The account ID'),
                            'name' => 'OCPC_ANALYTICS_ACCOUNT_ID',
                            'type' => 'text',
                            'required' => true
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'button',
                        'desc' => ''
                    )
                )
            )
        );
        $helper_form = new HelperForm();
        $helper_form->fields_value = array(
            'OCPC_ACTIVE_ANALYTICS' => Configuration::get('OCPC_ACTIVE_ANALYTICS'),
            'OCPC_ENHANCED_ANALITICS' => Configuration::get('OCPC_ENHANCED_ANALITICS'),
            'OCPC_ANALYTICS_ACCOUNT_ID' => Configuration::get('OCPC_ANALYTICS_ACCOUNT_ID')
        );
        $helper_form->module = $this;
        $helper_form->override_folder = 'customize_google_analytics_form/';
        $helper_form->table = 'customize_google_analytics_form';
        $helper_form->show_toolbar = false;
        $helper_form->token = Tools::getValue('token');
        $helper_form->currentIndex = 'index.php?controller=AdminModules&configure='.$this->name
            .'&tab_module=front_office_features&module_name='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
        $helper_form->tpl_vars['ajax_path'] = $helper_form->currentIndex;
        $helper_form->submit_action = 'submitSaveCustomizeGoogleAnalytics';
        return $helper_form->generateForm($fields);
    }

	public function getMaskInputVisible($name)
	{
		$fields = $this->getDefaultConfiguratonFields();
		if (isset($fields[$name]['mask_visible']) && $fields[$name]['mask_visible'])
			return true;

		return false;
	}

	public function adminCustomizeForm()
	{
		$fields_form = array();
		$fields_value = array();
		$fields = $this->getConfigurationFields();

		foreach ($fields as $name => $field)
		{
			if (array_key_exists ('help', $field))
				$help = Tools::unSerialize($field['help']);
			else
				foreach (Language::getLanguages(true, false, true) as $ids)
					$help[$ids] = '';

			$fields_form[] = array(
				'label' => $this->l($field['label']),
				'name' => $name,
				'type' => 'ocpc_field',
				'ps_version' => _PS_VERSION_,
				'visible' => $field['visible'],
				'required' => $field['required'],
				'position' => $field['position'],
				'help' => $help,
				'form_group_class' => 'ui-state-default',
				'mask_visible' => $this->getMaskInputVisible($name),
				'values' => array(
					array(
						'id' => $name.'_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => $name.'_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'is_bool' => false,
			);
			$post_field = Tools::getValue('fields');

			$fields_value[$name] = array(
				'visible' => (ToolsCore::getIsset($post_field[$name]['visible']) ?
					$post_field[$name]['visible'] : $field['visible']),
				'required' => (Tools::getIsset($post_field[$name]['required']) ?
					$post_field[$name]['required'] : $field['required']),
				'position' => (Tools::getIsset($post_field[$name]['position']) ?
					$post_field[$name]['position'] : $field['position'])
			);

			if ($this->getMaskInputVisible($name))
				$fields_value[$name]['mask_value'] = Tools::getIsset($post_field[$name]['mask_value']) ?
					$post_field[$name]['mask_value'] : isset($field['mask_value']) ?
						$field['mask_value'] : null;
		}

		$fields = array(
			'setting_fields' => array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting fields')
					),
					'input' => $fields_form,
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button',
						'desc' => ''
					)
				)
			)
		);

		$languages = Language::getLanguages(false);
		foreach ($languages as &$language)
			if ($language['id_lang'] == $this->context->language->id)
				$language['is_default'] = 1;
			else
				$language['is_default'] = 0;

		$helper_form = new HelperForm();
		$helper_form->languages = $languages;
		$helper_form->default_form_language = $this->context->language->id;
		$helper_form->fields_value = $fields_value;
		$helper_form->module = $this;
		$helper_form->table = 'customize_form';
		$helper_form->show_toolbar = false;
		$helper_form->allow_employee_form_lang = 0;
		$helper_form->token = Tools::getValue('token');
		$helper_form->currentIndex = 'index.php?controller=AdminModules&configure='.$this->name
			.'&tab_module=front_office_features&module_name='.$this->name;
		$helper_form->submit_action = 'submitSaveFields';
		$helper_form->override_folder = 'customize_form/';
		return $helper_form->generateForm($fields);
	}

	public function saveAdminCustomizeForm()
	{
		$configuration = $this->getConfigurationFields();
		$post_fields = Tools::getValue('fields');

		foreach (array_keys($post_fields) as $name)
		{
			$configuration[$name]['required'] = $post_fields[$name]['required'];
			$configuration[$name]['visible'] = $post_fields[$name]['visible'];
			$configuration[$name]['position'] = $post_fields[$name]['position'];
			$configuration[$name]['help'] = serialize($post_fields[$name]['help']);
			if ($this->getMaskInputVisible($name))
				$configuration[$name]['mask_value'] = $post_fields[$name]['mask_value'];
		}

		$form_fields = $this->sortByPosition($configuration);
		$this->setConfigurationFields($form_fields);
		Tools::redirectAdmin($this->getModuleAdminLink());
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitSaveFields'))
			$this->saveAdminCustomizeForm();
		if (Tools::isSubmit('submitSaveCustomizeOrderState'))
		{
			Configuration::updateValue('OCPC_ORDER_STATE', Tools::getValue('OCPC_ORDER_STATE'));
			Configuration::updateValue('OCPC_ID_CARRIER', Tools::getValue('OCPC_ID_CARRIER'));
			Configuration::updateValue('OCPC_GROUP_CUSTOMER', Tools::getValue('OCPC_GROUP_CUSTOMER'));
			$languages = Language::getLanguages(false);
			$ocpc_submit_text = array();
			foreach ($languages as $language)
				$ocpc_submit_text[$language['id_lang']] = (Tools::getValue('OCPC_SUBMIT_TEXT_'.$language['id_lang']) ?
					Tools::getValue('OCPC_SUBMIT_TEXT_'.$language['id_lang']) :
					Tools::getValue('OCPC_SUBMIT_TEXT_'.$this->context->language->id));
			Configuration::updateValue('OCPC_SUBMIT_TEXT', serialize($ocpc_submit_text));
			Configuration::updateValue('OCPC_DISABLE_SHOW_BUTTON', Tools::getValue('OCPC_DISABLE_SHOW_BUTTON'));
			Configuration::updateValue('OCPC_ALLOW_REPEAT_MAIL_ORDER', Tools::getValue('OCPC_ALLOW_REPEAT_MAIL_ORDER'));
			Configuration::updateValue('OCPC_ENABLED_PAYMENT', (int)Tools::getValue('OCPC_ENABLED_PAYMENT'));
            Configuration::updateValue('OCPC_CART_ADD', (int)Tools::getValue('OCPC_CART_ADD'));
			Tools::redirectAdmin($this->getModuleAdminLink());
		}
        if (Tools::isSubmit('submitSaveCustomizeGoogleAnalytics'))
        {
            Configuration::updateValue('OCPC_ACTIVE_ANALYTICS', Tools::getValue('OCPC_ACTIVE_ANALYTICS'));
            Configuration::updateValue('OCPC_ENHANCED_ANALITICS', Tools::getValue('OCPC_ENHANCED_ANALITICS'));
            Configuration::updateValue('OCPC_ANALYTICS_ACCOUNT_ID', Tools::getValue('OCPC_ANALYTICS_ACCOUNT_ID'));
            Tools::redirectAdmin($this->getModuleAdminLink());
        }
	}

	public function getContentWrap()
	{
		$this->postProcess();
		$this->registerSmartyFunctions();
		$html = '<link rel="stylesheet" href="'._MODULE_DIR_.$this->name.'/views/css/search_products.css">';
		$html .= '<script src="'._MODULE_DIR_.$this->name.'/views/js/search_product.js"></script>';
		$html .= '<script src="'._MODULE_DIR_.$this->name.'/views/js/jquery-ui-1.10.4.custom.js"></script>';
		$html .= $this->adminCustomizeOrderStateForm();
		$html .= $this->adminCustomizeForm();
        $html .= $this->adminCustomizeAnalyticsForm();
		$this->context->controller->addJS('https://seosaps.com/ru/module/seosamanager/manager?ajax=1&action=script&iso_code='
			.Context::getContext()->language->iso_code);

		$html .= '<script>
					$("#fieldset_setting_fields, #fieldset_setting_fields_1").sortable({
						stop: function (event, ui)
						{
							var i = 0;
							$(".position_fields").each(function () {
								$(this).val(i);
								i++;
							});
						},
						items: ".ui-state-default"
					});
				</script>
				<style>
					.ui-state-default
					{
						background: #FFFFFF;
						padding-top: 5px;
						padding-bottom: 5px;
						border-radius: 5px;
						cursor: move;
					}
					.ui-state-default:hover
					{
						 background: #EEEEEE;
					}
					sup
					{
						display: none;
					}
					.v15
					{
						margin-bottom: 10px;
						border: 1px #555555 solid;
						box-shadow: 0 0 5px rgba(0,0,0, 0.3);
					}
				</style>';
		return $html;
	}

	public function sortByPosition($array)
	{
		uasort($array, array('OneClickProductCheckout', 'sortByPositionFunc'));
		return $array;
	}

	public static function sortByPositionFunc($a, $b)
	{
		return ((int)$a['position'] < (int)$b['position']) ? - 1 : 1;
	}

	public function getModuleAdminLink()
	{
		if (_PS_VERSION_ < 1.5)
			return 'index.php?tab=AdminModules&configure='.
			$this->name.'&token='.Tools::getValue('token').'&tab_module=front_office_features&module_name='.$this->name;
		else
			return $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module=front_office_features&module_name='.$this->name;
	}

	public function getOneActiveCountry()
	{
		return Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'country WHERE active = 1');
	}

	public function ajaxProcessGetProducts()
	{
		$query = Tools::getValue('query');
		$select_products = Tools::getValue('select_products');
		if (!is_array($select_products) || !count($select_products))
			$select_products = array();
		$result = Db::getInstance()->executeS('SELECT pl.`id_product`, pl.`name` FROM '._DB_PREFIX_.'product_shop p
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.
			' WHERE pl.`name` LIKE "%'.pSQL($query).'%" AND p.`id_shop` = '.$this->context->shop->id.
			(count($select_products) ?
				' AND p.id_product NOT IN('.implode(',', array_map('intval', $select_products)).') '
				: ''));
		if (!$result)
			$result = array();
		die(Tools::jsonEncode($result));
	}

	public function getSearchProducts($ids_product)
	{
		if (!is_array($ids_product) || !count($ids_product))
			return array();

		$result = Db::getInstance()->executeS('SELECT pl.`id_product`, pl.`name` FROM '._DB_PREFIX_.'product_shop p
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.
			' WHERE p.id_product IN('.implode(',', array_map('intval', $ids_product)).')');
		return $result;
	}

	public function getImageLang($smarty)
	{
		if (_PS_VERSION_ < 1.5)
			$cookie = &$GLOBALS['cookie'];
		else
		{
			$cookie = $this->context->cookie;
			$cookie->id_lang = $this->context->language->id;
		}

		$path = $smarty['path'];
		$module_path = 'oneclickproductcheckout/views/img/';
		$current_language = new Language($cookie->id_lang);
		$module_lang_path = $module_path.$current_language->iso_code.'/';
		$module_lang_default_path = $module_path.'en/';
		$path_image = false;
		if (file_exists(_PS_MODULE_DIR_.$module_lang_path.$path))
			$path_image = _MODULE_DIR_.$module_lang_path.$path;
		elseif (file_exists(_PS_MODULE_DIR_.$module_lang_default_path.$path))
			$path_image = _MODULE_DIR_.$module_lang_default_path.$path;

		if ($path_image)
			return '<img class="thumbnail" src="'.$path_image.'">';
		else
			return '[can not load image "'.$path.'"]';
	}
	public function getContent()
	{
		$this->registerSmartyFunctions();
		$this->context->controller->addJS('https://seosaps.com/ru/module/seosamanager/manager?ajax=1&action=script&iso_code='
			.Context::getContext()->language->iso_code
		);
		$this->context->smarty->assign(array(
			'content_tab' => $this->getContentWrap(),
			'documentation' => $this->getDocumentation()
		));

		return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/content.tpl');
	}

	/**
	 *
	 */
	public function assignDocumentation()
	{
		$this->registerSmartyFunctions();

		$this->context->controller->addCSS($this->getLocalPath().'views/css/documentation.css');
		$documentation_folder = $this->getLocalPath().'views/templates/admin/documentation';
		$documentation_pages = self::globRecursive($documentation_folder.'/**.tpl');
		natsort($documentation_pages);

		if (version_compare(_PS_VERSION_, '1.6.0', '<'))
			$this->context->controller->addCSS(_MODULE_DIR_.'oneclickproductcheckout/views/css/admin-theme.css');
		$this->context->controller->addCSS(_MODULE_DIR_.'oneclickproductcheckout/views/css/documentation.css');

		$tree = array();
		if (is_array($documentation_pages) && count($documentation_pages))
			foreach ($documentation_pages as &$documentation_page)
			{
				$name = str_replace(array($documentation_folder.'/', '.tpl'), '', $documentation_page);
				$path = explode('/', $name);

				$tmp_tree = &$tree;
				foreach ($path as $key => $item)
				{
					$part = $item;
					if ($key == (count($path) - 1))
						$tmp_tree[$part] = $name;
					else
					{
						if (!isset($tmp_tree[$part]))
							$tmp_tree[$part] = array();
					}
					$tmp_tree = &$tmp_tree[$part];
				}
			}

		$this->context->smarty->assign('tree', $tree);
		$this->context->smarty->assign('documentation_pages', $documentation_pages);
		$this->context->smarty->assign('documentation_folder', $documentation_folder);
	}

	public function getDocumentation()
	{
		$this->assignDocumentation();
		return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/documentation.tpl');
	}

	/**
	 * @param string $pattern
	 * @param int $flags
	 * @return array
	 */
	public static function globRecursive($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);
		if (!$files)
			$files = array();

		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
			/** @noinspection SlowArrayOperationsInLoopInspection */
			$files = array_merge($files, self::globRecursive($dir.'/'.basename($pattern), $flags));

		return $files;
	}
}