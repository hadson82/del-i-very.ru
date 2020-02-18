<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LGCanonicalurls extends Module
{
    public $bootstrap;
    private $postErrors = array();

    const LGOT_HOME         = 0;
    const LGOT_PRODUCT      = 1;
    const LGOT_CATEGORY     = 2;
    const LGOT_CMS          = 3;
    const LGOT_SUPPLIER     = 4;
    const LGOT_MANUFACTURER = 5;

    const LGCU_AUTO   = 1;
    const LGCU_CUSTOM = 2;

    const LGCU_LOG_FILE = 'log.txt';

    private $debug                = false;
    private $log                  = false;
    private $vsn                  = '';
    private $formobjects_template = '';
    private $id_object            = 0;
    private $type_object          = null;

    public function __construct()
    {
        $this->name          = 'lgcanonicalurls';
        $this->tab           = 'seo';
        $this->version       = '1.0.15';
        $this->author        = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->module_key    = '427f80e80afc4f2435b27a14f28ea49d';

        $this->initLGCanonicalUrlModule();
        $this->initLGCanonicalObject();
        $this->getCommonFormValues();

        parent::__construct();

        $this->displayName = $this->l('Canonical URLs to Avoid Duplicate Content - SEO');
        $this->description =
            $this->l('Add canonical tags to your pages in order to avoid duplicate content and improve your SEO.');

        $this->initContext();
    }

    private function initLGCanonicalUrlModule()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->bootstrap = true;
            $this->vsn = '17';
            $this->formobjects_template = '/views/templates/admin/form-objects.tpl';
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->bootstrap = true;
            $this->vsn = '16';
            $this->formobjects_template = '/views/templates/admin/form-objects.tpl';
        } else {
            $this->bootstrap = false;
            $this->vsn = '15';
            $this->formobjects_template = '/views/templates/admin/form-objects_15.tpl';
        }
    }

    private function initLGCanonicalObject()
    {
        if ((Tools::strtolower(Tools::getValue('controller')) == 'product')
            ||
            (Tools::strtolower(Tools::getValue('controller')) == 'category')
            ||
            (Tools::strtolower(Tools::getValue('controller')) == 'supplier')
            ||
            (Tools::strtolower(Tools::getValue('controller')) == 'manufacturer')
            ||
            (Tools::strtolower(Tools::getValue('controller')) == 'cms')
            ||
            (Tools::strtolower(Tools::getValue('controller')) == 'index')
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'admincategories'
                && Tools::getIsset('updatecategory')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminsuppliers'
                && Tools::getIsset('updatesupplier')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminsuppliers'
                && Tools::getIsset('addsupplier')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminmanufacturers'
                && Tools::getIsset('updatemanufacturer')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'admincmscontent'
                && Tools::getIsset('updatecms')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminproducts'
                && Tools::getIsset('updateproduct')
            )
        ) {
            // El controlador nos va a ayudar a saber que tipo es y que parametro coger como id
            switch (Tools::strtolower(Tools::getValue('controller'))) {
                // FRONTOFFICE
                case 'product': // Productos
                    $this->id_object   = Tools::getValue('id_product');
                    $this->type_object = LGCanonicalurls::LGOT_PRODUCT;
                    break;
                case 'category': // Categorias
                    $this->id_object   = Tools::getValue('id_category');
                    $this->type_object = LGCanonicalurls::LGOT_CATEGORY;
                    break;
                case 'supplier': // Proveedores
                    $this->id_object   = Tools::getValue('id_supplier');
                    $this->type_object = LGCanonicalurls::LGOT_SUPPLIER;
                    break;
                case 'manufacturer': // Fabricantes
                    $this->id_object   = Tools::getValue('id_manufacturer');
                    $this->type_object = LGCanonicalurls::LGOT_MANUFACTURER;
                    break;
                case 'cms': // Paginas CMS
                    $this->id_object   = Tools::getValue('id_cms');
                    $this->type_object = LGCanonicalurls::LGOT_CMS;
                    break;
                case 'index': // Paginas Home
                    $this->id_object   = null;
                    $this->type_object = LGCanonicalurls::LGOT_HOME;
                    break;
                // BACKOFFICE
                case 'admincategories':
                    $this->id_object   = Tools::getValue('id_category');
                    $this->type_object = LGCanonicalurls::LGOT_CATEGORY;
                    break;
                case 'adminsuppliers':
                    $this->id_object   = Tools::getValue('id_supplier');
                    $this->type_object = LGCanonicalurls::LGOT_SUPPLIER;
                    break;
                case 'adminmanufacturers':
                    $this->id_object   = Tools::getValue('id_manufacturer');
                    $this->type_object = LGCanonicalurls::LGOT_MANUFACTURER;
                    break;
                case 'admincmscontent':
                    $this->id_object   = Tools::getValue('id_cms');
                    $this->type_object = LGCanonicalurls::LGOT_CMS;
                    break;
                case 'adminproducts':
                    $this->id_object   = Tools::getValue('id_product');
                    $this->type_object = LGCanonicalurls::LGOT_PRODUCT;
                    break;
            }
        }
    }

    private function getP()
    {
        $default_lang = $this->context->language->id;
        $lang = Language::getIsoById($default_lang);
        $pl = array('es', 'fr');
        if (!in_array($lang, $pl)) {
            $lang = 'en';
        }

        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/publi/style.css');
        $base = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ?
            'https://'.$this->context->shop->domain_ssl :
            'http://'.$this->context->shop->domain);
        $uri = $base.$this->context->shop->getBaseURI();
        $path = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'publi'.
            DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'index.php';
        $object = Tools::file_get_contents($path);
        $object = str_replace('src="/modules/', 'src="'.$uri.'modules/', $object);

        return $object;
    }

    private function logAdd($msg)
    {
        if ($this->log) {
            $file_log = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.LGCanonicalurls::LGCU_LOG_FILE;
            $log      = fopen($file_log, "a+");
            if ($log) {
                fputs($log, $msg);
                fclose($log);
            }
        }
    }

    /* Retrocompatibility 1.4/1.5 */
    private function initContext()
    {
        $this->context = Context::getContext();
    }

    private function proccessQueries($queries)
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->Execute($query)) {
                $this->logAdd('ERROR: CONSULTA - '.$query."\n");
                return false;
            } else {
                $this->logAdd('EXITO: CONSULTA - '.$query."\n");
            }
        }

        return true;
    }

    private function createTables()
    {
        $queries = array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."lgcanonicalurls` (\n".
            " `id_object` int(10) unsigned NOT NULL, \n".
            " `type_object` enum('LGOT_PRODUCT','LGOT_CATEGORY','LGOT_CMS','LGOT_SUPPLIER', 'LGOT_MANUFACTURER')".
            " NOT NULL, \n".
            " `type` enum('LGCU_AUTO','LGCU_CUSTOM') NOT NULL DEFAULT 'LGCU_AUTO',\n".
            " `parameters` TEXT\n".
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8",
            "ALTER TABLE `"._DB_PREFIX_."lgcanonicalurls`".
            " ADD PRIMARY KEY (`id_object`,`type_object`);",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."lgcanonicalurls_lang`(\n".
            " `id_object` int(10) unsigned NOT NULL, \n".
            " `type_object` enum('LGOT_PRODUCT','LGOT_CATEGORY','LGOT_CMS','LGOT_SUPPLIER', 'LGOT_MANUFACTURER')".
            " NOT NULL, \n".
            " `canonical_url` TEXT,\n".
            " `id_lang` int(10) unsigned NOT NULL\n".
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8",
            "ALTER TABLE `"._DB_PREFIX_."lgcanonicalurls_lang`".
            " ADD PRIMARY KEY (`id_object`,`type_object`,`id_lang`);",
        );
        return $this->proccessQueries($queries);
    }

    private function deleteTables()
    {
        $queries = array(
            "DROP TABLE IF EXISTS `"._DB_PREFIX_."lgcanonicalurls`;",
            "DROP TABLE IF EXISTS `"._DB_PREFIX_."lgcanonicalurls_lang`;"
        );
        return $this->proccessQueries($queries);
    }

    private function instalarHook($hook)
    {
        $resultado = $this->registerHook($hook);
        if (!$resultado) {
            $this->logAdd('ERROR: no se pudo instalar el hook: '.$hook.' - RESULTADO:'.print_r($resultado, true)."\n");
            return false;
        } else {
            $this->logAdd('EXITO: Instalado el hook: '.$hook."\n");
            return true;
        }
    }

    public function desinstalarHook($hook)
    {
        $resultado = $this->unregisterHook($hook);
        if (!$resultado) {
            $this->logAdd(
                'ERROR: no se pudo desinstalar el hook: '.$hook.' - RESULTADO:'.print_r($resultado, true)."\n"
            );
            return false;
        } else {
            $this->logAdd('EXITO: Desinstalado el hook: '.$hook."\n");
            return true;
        }
    }

    private function desintalarConfig($key)
    {
        $resultado = Configuration::deleteByName($key);
        if (!$resultado) {
            $this->logAdd('ERROR: Eliminando al variable de configuración: '.$key."\n");
            return false;
        } else {
            $this->logAdd('EXITO: Eliminando al variable de configuración: '.$key."\n");
            return true;
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (parent::install()
            && $this->instalarHook('header')
            && $this->instalarHook('displayBackOfficeHeader')
            && $this->instalarHook('displayAdminForm')
            && $this->instalarHook('displayAdminProductsExtra')
//            && $this->instalarHook('actionProductAdd')
//            && $this->instalarHook('actionProductUpdate')
//            && $this->instalarHook('actionProductDelete')
            && $this->instalarHook('actionObjectProductAddAfter')
            && $this->instalarHook('actionObjectProductUpdateAfter')
            && $this->instalarHook('actionObjectProductDeleteAfter')
            && $this->instalarHook('actionObjectCategoryAddAfter')
            && $this->instalarHook('actionObjectCategoryUpdateAfter')
            && $this->instalarHook('actionObjectCategoryDeleteAfter')
            && $this->instalarHook('actionObjectCmsAddAfter')
            && $this->instalarHook('actionObjectCmsUpdateAfter')
            && $this->instalarHook('actionObjectCmsDeleteAfter')
            && $this->instalarHook('actionObjectSupplierAddAfter')
            && $this->instalarHook('actionObjectSupplierUpdateAfter')
            && $this->instalarHook('actionObjectSupplierDeleteAfter')
            && $this->instalarHook('actionObjectManufacturerAddAfter')
            && $this->instalarHook('actionObjectManufacturerUpdateAfter')
            && $this->instalarHook('actionObjectManufacturerDeleteAfter')
            && $this->createTables()
            && Configuration::updateValue('LGCANONICALURLS_IGNORE_PARAMS', false)
            && Configuration::updateValue('LGCANONICALURLS_PARAMS', 'orderby,orderway,n,search_query')
            && Configuration::updateValue('LGCANONICALURLS_CANHOMESTATUS', 1)
            && Configuration::updateValue('LGCANONICALURLS_CANONICALHOME', '')
            && Configuration::updateValue('LGCANONICALURLS_CANHOME_TEXT', '')
            && Configuration::updateValue('LGCANONICALURLS_CANHOME_TYPE', 'default')
        ) {
            return true;
        }
        return false;
    }

    public function uninstall()
    {
        if (!$this->desinstalarHook('header')
            || !$this->desinstalarHook('displayBackOfficeHeader')
            || !$this->desinstalarHook('displayAdminForm')
            || !$this->desinstalarHook('displayAdminProductsExtra')
//            || !$this->desinstalarHook('actionProductAdd')
//            || !$this->desinstalarHook('actionProductUpdate')
//            || !$this->desinstalarHook('actionProductDelete')
            || !$this->desinstalarHook('actionObjectProductUpdateAfter')
            || !$this->desinstalarHook('actionObjectProductDeleteAfter')
            || !$this->desinstalarHook('actionObjectCategoryAddAfter')
            || !$this->desinstalarHook('actionObjectCategoryUpdateAfter')
            || !$this->desinstalarHook('actionObjectCategoryDeleteAfter')
            || !$this->desinstalarHook('actionObjectCmsAddAfter')
            || !$this->desinstalarHook('actionObjectCmsUpdateAfter')
            || !$this->desinstalarHook('actionObjectCmsDeleteAfter')
            || !$this->desinstalarHook('actionObjectSupplierAddAfter')
            || !$this->desinstalarHook('actionObjectSupplierUpdateAfter')
            || !$this->desinstalarHook('actionObjectSupplierDeleteAfter')
            || !$this->desinstalarHook('actionObjectManufacturerAddAfter')
            || !$this->desinstalarHook('actionObjectManufacturerUpdateAfter')
            || !$this->desinstalarHook('actionObjectManufacturerDeleteAfter')
            || !$this->deleteTables()
            || !$this->desintalarConfig('LGCANONICALURLS_CANONICDOMAIN')
            || !$this->desintalarConfig('LGCANONICALURLS_HTTP_HEADERS')
            || !$this->desintalarConfig('LGCANONICALURLS_FORCEHTTPHTTPS')
            || !$this->desintalarConfig('LGCANONICALURLS_HTTPHTTPS_VAL')
            || !$this->desintalarConfig('LGCANONICALURLS_IGNORE_PARAMS')
            || !$this->desintalarConfig('LGCANONICALURLS_PARAMS')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOMESTATUS')
            || !$this->desintalarConfig('LGCANONICALURLS_CANONICALHOME')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOME_TEXT')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOME_TYPE')
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    private function postProcess()
    {
        // Tools::dieObject($_REQUEST);
        if (Tools::isSubmit('lgcanonicalurls_config_submit')) {
            Configuration::updateValue(
                'LGCANONICALURLS_CANONICDOMAIN',
                trim(Tools::getValue('lgcanonicalurls_canonical_domain'), '')
            );
            Configuration::updateValue(
                'LGCANONICALURLS_HTTP_HEADERS',
                (int)Tools::getValue('lgcanonicalurls_http_headers', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_FORCEHTTPHTTPS',
                (int)Tools::getValue('lgcanonicalurls_force_http_https', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_HTTPHTTPS_VAL',
                trim(Tools::getValue('lgcanonicalurls_force_http_https_value', ''))
            );
            Configuration::updateValue(
                'LGCANONICALURLS_IGNORE_PARAMS',
                (int)Tools::getValue('lgcanonicalurls_ignoreparams', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_PARAMS',
                trim(Tools::getValue('lgcanonicalurls_params', Configuration::get('LGCANONICALURLS_PARAMS', '')))
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CANONICALHOME',
                (int)(
                    Tools::getValue(
                        'lgcanonicalurls_canonicalhome',
                        (
                            Configuration::get('LGCANONICALURLS_CANONICALHOME')?
                                Configuration::get('LGCANONICALURLS_CANONICALHOME'):0
                        )
                    )
                )
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CANHOME_TYPE',
                trim(
                    Tools::getValue(
                        'lgcanonicalurls_canonicalhome_type',
                        (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom')?'custom':'default'
                    )
                )
            );

            $languages      = LanguageCore::getLanguages();
            $langs_received = array();
            $langs_saved    = Tools::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);
            foreach ($languages as $lang) {
                $aux_text = Tools::getValue(
                    'LGCANONICALURLS_CANHOME_TEXT_'.$lang['id_lang'],
                    ''
                );
//                if(trim($aux_text) != '') {
                $langs_received[$lang['id_lang']] = $aux_text;
//                }
            }
            foreach ($languages as $lang) {
                if ((isset($langs_saved['id_lang']) && $langs_saved['id_lang'] != $langs_received[$lang['id_lang']])
                    || !isset($langs_saved['id_lang'])
                ) {
                    $langs_saved[$lang['id_lang']] = $langs_received[$lang['id_lang']];
                }
            }
            Configuration::updateValue('LGCANONICALURLS_CANHOME_TEXT', Tools::jsonEncode($langs_saved));
        }
    }

    public function getConfigFormValues()
    {
        $defaults = array(
            'lgcanonicalurls_canonical_domain'       => Configuration::get('LGCANONICALURLS_CANONICDOMAIN'),
            'lgcanonicalurls_http_headers'           => Configuration::get('LGCANONICALURLS_HTTP_HEADERS'),
            'lgcanonicalurls_force_http_https'       => Configuration::get('LGCANONICALURLS_FORCEHTTPHTTPS'),
            'lgcanonicalurls_force_http_https_value' => Configuration::get('LGCANONICALURLS_HTTPHTTPS_VAL'),
            'lgcanonicalurls_ignoreparams'           => Configuration::get('LGCANONICALURLS_IGNORE_PARAMS'),
            'lgcanonicalurls_params'                 => Configuration::get('LGCANONICALURLS_PARAMS'),
            'description'                            => '',
            'lgcanonicalurls_canonicalhome'          =>
                Configuration::get('LGCANONICALURLS_CANONICALHOME')?
                    Configuration::get('LGCANONICALURLS_CANONICALHOME'):false,
            'lgcanonicalurls_canonicalhome_type'     =>
                (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom')?'custom':'default',
        );

        $languages   = Language::getLanguages();
        $langs_saved = Tools::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);

        foreach ($languages as $lang) {
            if (isset($langs_saved[$lang['id_lang']])) {
                $defaults['LGCANONICALURLS_CANHOME_TEXT'][$lang['id_lang']] = $langs_saved[$lang['id_lang']];
            } else {
                $defaults['LGCANONICALURLS_CANHOME_TEXT'][$lang['id_lang']] = '';
            }
        }

        return $defaults;
    }

    private function getCommonFormValues()
    {
        $this->fields_value                         = array();
        $this->fields_value['lgcanonicalurls_type'] = $this->getTipo($this->id_object, $this->type_object);

        if (!$this->fields_value['lgcanonicalurls_type']) {
            $this->fields_value['lgcanonicalurls_type'] = LGCanonicalurls::LGCU_AUTO;
        }

        $langs = $this->formatFormLanguages();
        foreach ($langs as $lang) {
            $value = $this->getLocalCanonicalUrl($this->id_object, $this->type_object, $lang['id_lang']);
            if ($value === false) {
                $value = '';
            }
            $this->fields_value['lgcanonicalurls_canonical_url'][$lang['id_lang']] = $value;
        }
    }

    private function getCommonFormFields()
    {
//        $switch_or_radio = ($this->vsn == '16')?'switch':'radio';
        $deshabilitar = true;
        if (!is_null($this->id_object)) {
            $tipo = $this->getTipo($this->id_object, $this->type_object);
            if ($tipo == LGCanonicalurls::LGCU_CUSTOM) {
                $deshabilitar = false;
            }
        }

        $input = array();


        $input = array_merge(
            $input,
            array(
                array(
                    'type'     => 'radio',
                    'label'    => $this->l('URL configuration:'),
                    'desc'     =>
                        $this->l('If you choose the option "By default",').'&nbsp;'.
                        $this->l('the module will apply the canonical URL configuration set in the module interface.'),
                    'name'     => 'lgcanonicalurls_type',
                    'required' => true,
                    'class'    => 't',
                    'is_bool'  => false,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_type_1',
                            'value' => LGCanonicalurls::LGCU_AUTO,
                            'label' => $this->l('By default'),
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_type_2',
                            'value' => LGCanonicalurls::LGCU_CUSTOM,
                            'label' => $this->l('Custom URL')
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Custom URL:'),
                    'desc'     =>
                        $this->l('Write here the custom canonical URL for this page.').'&nbsp;'.
                        $this->l('It must start with http:// or https://.'),
                    'name'     => 'lgcanonicalurls_canonical_url',
                    'required' => false,
                    'class'    => 't',
                    'disabled' => $deshabilitar,
                    'lang'     => true
                ),
            )
        );
        return $input;
    }

    private function displayForm()
    {
        $switch_or_radio = ($this->vsn == '16')?'switch':'radio';

        $fields_form = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('General configuration'),
                'image' => '../img/admin/cog.gif'
            ),
            'input' => array(
                array(
                    'type'  => 'free',
                    'label' => $this->l('How it works:'),
                    'desc'  =>
                        $this->l('The module automatically adds canonical urls to all your product,').
                        '&nbsp;'.
                        $this->l('category, CMS, manufacturer and supplier pages only (in the source code of').
                        '&nbsp;'.
                        $this->l('these pages, do a CTRL+F and search for the word "canonical" to find it).').
                        '&nbsp;'.
                        $this->l(
                            'By default, the module uses the current url of the page as the default canonical url.'
                        ),
                    'name'  => 'description'
                ),
                array(
                    'type'  => 'free',
                    'desc'  =>
                        $this->l('- Use the general configuration below to apply general changes').'&nbsp;'.
                        $this->l('to all your default canonical urls. You can change automatically').'&nbsp;'.
                        $this->l('the domain (if you have duplicate content between domains),').'&nbsp;'.
                        $this->l('the protocol (if you have duplicate content between http and https)').'&nbsp;'.
                        $this->l('or to remove parameters (if you have duplicate content due to urls').'&nbsp;'.
                        $this->l('with parameters) in the default canonical urls.'),
                    'name'  => 'description'
                ),
                array(
                    'type'  => 'free',
                    'desc'  =>
                        $this->l('- If you want to set a custom canonical URL for a specific product, category,').
                        '&nbsp;'.
                        $this->l('CMS page... then go to the configuration of this page,choose the option').
                        '&nbsp;'.
                        $this->l('"Custom URL" and write the custom canonical url of your choice.'),
                    'name'  => 'description'
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Main domain:'),
                    'name'     => 'lgcanonicalurls_canonical_domain',
                    'required' => true,
                    'desc'     =>
                        $this->l('Choose the domain name you want to include in the canonical URL ').'&nbsp;'.
                        $this->l('(ex: www.mydomain.com or mydomain.com). Do not include the last slash "/",').
                        '&nbsp;'.
                        $this->l('or the "/index.php" suffix, or the "http(s)://" prefix.'),
                    'disabled' => false,
                ),
                array(
                    'type'     => $switch_or_radio,
                    'label'    => $this->l('Force to use HTTP or HTTPS:'),
                    'name'     => 'lgcanonicalurls_force_http_https',
                    'required' => true,
                    'class'    => 't',
                    'desc'     =>
                        $this->l('If you enable this option,').'&nbsp;'.
                        $this->l('choose below the type of protocol (HTTP or HTTPS) you want to apply.'),
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_force_http_https_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_force_http_https_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type'     => 'radio',
                    'name'     => 'lgcanonicalurls_force_http_https_value',
                    'required' => true,
                    'class'    => 't',
                    'desc'     => $this->l('Automatically add HTTP or HTTPS before the domain of the canonical URL.'),
                    'is_bool'  => false,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_force_http_https_value_https',
                            'value' => 'https',
                            'label' => $this->l('Force HTTPS for the canonical URL'),
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_force_http_https_value_http',
                            'value' => 'http',
                            'label' => $this->l('Force HTTP for the canonical URL')
                        ),
                    ),
                ),
                array(
                    'type'     => $switch_or_radio,
                    'label'    => $this->l('Ignore parameters:'),
                    'name'     => 'lgcanonicalurls_ignoreparams',
                    'required' => true,
                    'class'    => 't',
                    'desc'     =>
                        $this->l('Enable this option if you want to ignore parameters from the canonical url.'),
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_ignoreparams_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_ignoreparams_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'lgcanonicalurls_params',
                    'desc'     =>
                        $this->l('List the parameters you want to exclude from the canonical URLs').'&nbsp;'.
                        $this->l('(do not include the sign ? or & and separe parameters with a coma and space).'),
                    'disabled' => Configuration::get('LGCANONICALURLS_PARAMS')?false:true
                ),
                array(
                    'type'     => $switch_or_radio,
                    'label'    => $this->l('HTTP headers:'),
                    'name'     => 'lgcanonicalurls_http_headers',
                    'required' => true,
                    'class'    => 't',
                    'desc'     =>
                        $this->l('Visible by web browsers only. Enable this option if you want').'&nbsp;'.
                        $this->l('web browsers to see the canonical URL inside the HTTP header of the page.'),
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_http_headers_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_http_headers_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type'     => $switch_or_radio,
                    'label'    => $this->l('Enable canonical url on homepage:'),
                    'name'     => 'lgcanonicalurls_canonicalhome',
                    'required' => true,
                    'class'    => 't',
                    'desc'     =>
                        $this->l('Enable this option if you want to have a canonical url on your homepage'),
                    'is_bool'  => true,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_canonicalhome_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_canonicalhome_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type'     => 'radio',
                    'label'    => $this->l('Type:'),
                    'name'     => 'lgcanonicalurls_canonicalhome_type',
                    'required' => true,
                    'class'    => 't',
                    'is_bool'  => false,
                    'values'   => array(
                        array(
                            'id'    => 'lgcanonicalurls_canonicalhome_type_default',
                            'value' => 'default',
                            'label' => $this->l('Default: ').$this->getBaseUri().__PS_BASE_URI__.'{iso_lang}',
                        ),
                        array(
                            'id'    => 'lgcanonicalurls_canonicalhome_type_custom',
                            'value' => 'custom',
                            'label' => $this->l('Custom: Please set the canonical url in the field below'),
                        ),
                    ),
                ),
                array(
                    'type'     => 'text',
                    'name'     => 'LGCANONICALURLS_CANHOME_TEXT',
                    'label'    => $this->l('Custom canonical url:'),
                    'lang'     => true,
                    'desc'     =>
                        $this->l('List the parameters you want to exclude from the canonical URLs').'&nbsp;'.
                        $this->l('(do not include the sign ? or & and separe parameters with a coma and space).'),
                    'disabled' => Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom' ?false:true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name'  => 'lgcanonicalurls_config_submit',
                'class' => 'button btn btn-default',
                'id'    => 'lgcanonicalurls_config_submit',
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->languages       = $this->formatFormLanguages(); //Language::getLanguages();
        $helper->module          = $this;
        $helper->fields_value    = $this->getConfigFormValues();
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex    = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $default_lang = $this->context->language->id;
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title          = $this->displayName;
        $helper->show_toolbar   = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action  = 'submit'.$this->name;
        $helper->toolbar_btn    = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to the list')
            )
        );
        return $this->getP().$helper->generateForm($fields_form);
    }

    public function getContent()
    {
        $out = array();
        if (Tools::getValue('action') == 'ajaxSaveProductForm') {
            $result = $this->updateObject(Tools::getValue('id_product'), LGCanonicalurls::LGOT_PRODUCT);
            if ($result) {
                $out['status']       = 'ok';
                $out['confirmation'] = $this->l('The changes have been saved successfully');
            } else {
                $out['status'] = 'nok';
                $out['error']  = $this->l('An error occurred while saving the changes');
            }
            echo Tools::jsonEncode($out);
            die();
        }

        $this->_html = '<h2>'.$this->displayName.'</h2>';

        if (Tools::isSubmit('lgcanonicalurls_config_submit')) {
            if (!sizeof($this->postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->_html .= '<div class="alert error">'.$err.'</div>';
                }
            }
        }
        return $this->displayForm();
    }

    public function hookHeader($params)
    {
        //die(Tools::getValue('controller'));
        if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] >= 300) {
            return;
        }

        $rel_canonical = '';
        $canonical_url = $this->getCanonicalUrl();

        if ($canonical_url !== false) {
//            $canonical_url_backup = $canonical_url;

            if (Configuration::get('LGCANONICALURLS_HTTP_HEADERS') && $canonical_url && !headers_sent()) {
                header('Link: <' . $canonical_url . '>; rel="canonical"');
            }

            if ($canonical_url !== false) {
                $rel_canonical = '<link rel="canonical" href="' . $canonical_url . '" />';
            }

            $rel_previous = '';
            $rel_next = '';

            // Carlos Utrera: Se ha decidido que a partir de ahora, dado que en las categorías, por defecto la url
            //                canónica será la página sin parámetros, y que se prescinde de los atributos rel=next
            //                y rel=previous, el && false, anula este cálculo
            if ($this->context->controller instanceof CategoryController) {
                // Carlos Utrera: Se ha decidido que a partir de ahora, dado que en las categorías, por defecto la url
                // Carlos Utrera: Se ha decidido que a partir de ahora, dado que en las categorías, por defecto la url
                //            canónica será la página sin parámetros, y que se prescinde de los atributos rel=next
                //            y rel=previous, el && false, anula este cálculo
                $base = $this->getBaseUri();
                return '<link rel="canonical" href="'
                    .$base
                    .Tools::substr($_SERVER['REQUEST_URI'], 0, Tools::strpos($_SERVER['REQUEST_URI'], '?'))
                    . '" />';

//                if (version_compare(_PS_VERSION_, '1.5.6.0', '<')) {
//                    $category = new Category($this->context->cookie->__get('last_visited_category'));
//                } else {
//                    $category = $this->context->controller->getCategory();
//                }
//
//                $nbProducts = $category->getProducts(
//                    null,
//                    null,
//                    null,
//                    $this->context->controller->orderBy,
//                    $this->context->controller->orderWay,
//                    true
//                );
//                $pagination = $this->context->controller->pagination((int)$nbProducts);
//                $number_of_pages = (int)$this->context->smarty->tpl_vars['pages_nb']->value;
//                unset($pagination); // Only needed for perform operations needed
//
//                $procesar_paginacion = true;
//                if ((bool)Configuration::get('LGCANONICALURLS_IGNORE_PARAMS') != false) {
//                    $parametros_a_escapar = Configuration::get('LGCANONICALURLS_PARAMS');
//                    if ($parametros_a_escapar != '') {
//                        $parametros = explode(',', $parametros_a_escapar);
//                        if (!empty($parametros)) {
//                            array_map('trim', $parametros);
//                            if (in_array('p', $parametros)) {
//                                $procesar_paginacion = false;
//                            }
//                        }
//                    }
//                }
//
//                if ((int)$number_of_pages > 1 && $procesar_paginacion) {
//                    if (Tools::getValue('p', 0) != 0) {
//                        $p = Tools::getValue('p');
//                        if ($p == 1) {
//                            $rel_next = '<link rel="next" href="'
//                                . $this->cambiarParametro('p', ((int)$p + 1), $canonical_url)
//                                . '" />';
//                            // la pagina 1 y l pagina sin parametro es lo mismo
//                            $canonical_url = $this->quitarParametro('p', $canonical_url);
//                        } elseif ($p == $number_of_pages) {
//                            $rel_previous = '<link rel="previous" href="'
//                                . $this->cambiarParametro('p', ((int)$p - 1), $canonical_url)
//                                . '" />';
////                            $canonical_url = $this->quitarParametros($canonical_url, array('p', 'n'));
//                        } else {
//                            $rel_next = '<link rel="next" href="'
//                                . $this->cambiarParametro('p', ((int)$p + 1), $canonical_url)
//                                . '" />';
//                            $rel_previous = '<link rel="previous" href="'
//                                . $this->cambiarParametro('p', ((int)$p - 1), $canonical_url)
//                                . '" />';
////                            $canonical_url = $this->quitarParametros($canonical_url, array('p', 'n'));
//                        }
//                        if ($p == 2) {
//                            $rel_previous = '<link rel="previous" href="'
//                                . $this->quitarParametro('p', $canonical_url_backup)
//                                . '" />';
////                            $canonical_url = $this->quitarParametros($canonical_url, array('p', 'n'));
//                        }
//                    } else {
//                        $rel_next = '<link rel="next" href="'
//                            . $this->cambiarParametro('p', 2, $canonical_url)
//                            . '" />';
//                    }
//                    // Así evitamos contenido duplicado entre la llamada sin parametro y con parametro p=1
//                    if ($canonical_url != '') {
//                        $rel_canonical = '<link rel="canonical" href="' . $canonical_url . '" />';
//                    }
//                    //Tools::d($rel_canonical);
//                } else {
//                    // Así evitamos contenido duplicado entre la llamada sin parametro y con parametro p=1
//                    $rel_canonical = '<link rel="canonical" href="' .
//                        $this->quitarParametro('p', $canonical_url) .
//                        '" />';
//                }
            } else {
                $rel_canonical = '<link rel="canonical" href="' . $this->quitarParametros($canonical_url) . '" />';
            }
            return $rel_previous . $rel_canonical . $rel_next;
        }
    }

    private function getDefaultCanonicalHome($id_lang)
    {
        $rel_canonical = '';
        $canonical_langs = Tools::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'));
        if ($canonical_langs[$this->context->language->id]) {
            $rel_canonical = '<link rel="canonical" href="'.$canonical_langs[$id_lang].'" />';
        }
        return $rel_canonical;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if ((
                Tools::strtolower(Tools::getValue('controller')) == 'adminproducts'
                && Tools::getIsset('updateproduct')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'admincategories'
                && Tools::getIsset('updatecategory')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminsuppliers'
                && Tools::getIsset('updatesupplier')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminmanufacturers'
                && Tools::getIsset('updatemanufacturer')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'admincmscontent'
                && Tools::getIsset('updatecms')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminsuppliers'
                && Tools::getIsset('addsupplier')
            )
            ||
            (
                Tools::strtolower(Tools::getValue('controller')) == 'adminmodules'
                && Tools::getValue('configure') == $this->name
            )
        ) {
            $this->context->controller->addJquery();
            if (version_compare(_PS_VERSION_, '1.6', '>=')) {
                $this->context->controller->addJS($this->_path.'views/js/admin-header.js');
            } else {
                $this->context->controller->addJS($this->_path.'views/js/admin-header-15.js');
            }
        }
    }

    public function hookDisplayAdminForm($params)
    {
        return $this->displayCommonFormFields();
    }

    public function displayCommonFormFields()
    {
        // In configuration module page $this->idObject = 0 and $this->type_object = null, we don't want display
        // form common fields in this page
        if ($this->id_object != 0 && !is_null($this->type_object)) {
            $langs = $this->formatFormLanguages();

            if ($this->type_object != LGCanonicalurls::LGOT_PRODUCT) {
                $this->context->smarty->assign(
                    array(
                        'field' => array_merge(
                            array(
                                array(
                                    'type'         => 'html',
                                    'name'         => 'lgcanonicalurls_label',
                                    'label'        => $this->l('Canonical URL configuration'),
                                    'html_content' => ''
                                )
                            ),
                            $this->getCommonFormFields()
                        ),
                    )
                );
            } else {
                // Utilizamos la plantilla del formulario que a su vez carga la de form_object
                // pero con lo necesario para un tab
                $this->formobjects_template = $this->formobjects_template = ''
                    .DIRECTORY_SEPARATOR.'views'
                    .DIRECTORY_SEPARATOR.'templates'
                    .DIRECTORY_SEPARATOR.'admin'
                    .DIRECTORY_SEPARATOR.'controllers'
                    .DIRECTORY_SEPARATOR.'products'
                    .DIRECTORY_SEPARATOR.'moduleLgcanonicalurls.tpl';

                $this->context->smarty->assign(
                    array(
                        'field' => $this->getCommonFormFields()
                    )
                );
            }

            if (version_compare(_PS_VERSION_, '1.6', '>=')
                && Tools::strtolower(Tools::getValue('controller')) == 'adminproducts'
                && Tools::getIsset('updateproduct')
            ) {
                $link = new LinkCore();
                $this->context->smarty->assign(
                    array(
                        'show_cancel_button' => true,
                        'back_url' => $link->getAdminLink(Tools::getValue('controller')),
                        'submit' => array(
                            'title' => $this->l('Save'),
                            'name'  => 'submitAddproduct',
                            'class' => 'pull-right',
                        ),
                        'buttons' => array(
                            array(
                                'title' => $this->l('Save and stay'),
                                'name'  => 'submitAddproductAndStay',
                                'class' => 'pull-right',
                                'type'    => 'submit',
                                'icon'  => 'process-icon-save'
                            ),
                        ),
                    )
                );
            }

            $this->context->smarty->assign(
                array(
                    'langs'               => $langs,
                    'fields_value'        => $this->fields_value,
                    'languages'           => $this->context->controller->_languages,
                    'default_language'    => (int)Configuration::get('PS_LANG_DEFAULT'),
                    'defaultFormLanguage' => (int)Configuration::get('PS_LANG_DEFAULT'),
                    'lgcanonicalurl_psversion' => $this->vsn
                )
            );

            return $this->display(__FILE__, $this->formobjects_template);
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        return $this->displayCommonFormFields();
    }

    public function formatFormLanguages()
    {
        $langs = Language::getLanguages();
        foreach ($langs as &$lang) {
            $lang['is_default'] = (int)($lang['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        return $langs;
    }

    public function getProductFormValues($id_object = null)
    {
        $out = array();

        if (Tools::getValue('controller') == 'AdminProducts') {
            $out['moduleLgcanonicalurls_loaded'] = 1;
            $out['form_token']                 = Tools::getAdminTokenLite('AdminModules');
            $out['lgcanonicalurls_product_id'] = Tools::getValue('id_product');

            $langs = $this->formatFormLanguages();

            $out['lgcanonicalurls_type'] = $this->getTipo($id_object, LGCanonicalurls::LGOT_PRODUCT);
            if (!$out['lgcanonicalurls_type']) {
                $out['lgcanonicalurls_type'] = LGCanonicalurls::LGCU_AUTO;
            }
            foreach ($langs as $lang) {
                $value = $this->getLocalCanonicalUrl($id_object, LGCanonicalurls::LGOT_PRODUCT, $lang['id_lang']);
                if ($value === false) {
                    $value = '';
                }
                $out['lgcanonicalurls_canonical_url'][$lang['id_lang']] = $value;
            }
        }

        return $out;
    }

//    public function hookActionProductAdd($params)
//    {
//        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object']) ) {
//            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
//        }
//    }
//
//    public function hookActionProductUpdate($params)
//    {
//        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
//            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
//        }
//    }
//
//    public function hookActionProductDelete($params)
//    {
//        return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
//    }

    public function hookActionObjectProductAddAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
        }
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
        }
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_PRODUCT);
    }

    public function hookActionObjectCategoryAddAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_CATEGORY);
        }
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_CATEGORY);
        }
    }

    public function hookActionObjectCategoryDeleteAfter($params)
    {
        if (Validate::isLoadedObject($params['object'])) {
            return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_CATEGORY);
        }
    }

    public function hookActionObjectCMSAddAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_CMS);
        }
    }

    public function hookActionObjectCMSUpdateAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_CMS);
        }
    }

    public function hookActionObjectCMSDeleteAfter($params)
    {
        if (Validate::isLoadedObject($params['object'])) {
            return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_CMS);
        }
    }

    public function hookActionObjectSupplierAddAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_SUPPLIER);
        }
    }

    public function hookActionObjectSupplierUpdateAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_SUPPLIER);
        }
    }

    public function hookActionObjectSupplierDeleteAfter($params)
    {
        if (Validate::isLoadedObject($params['object'])) {
            return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_SUPPLIER);
        }
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->addObject($params['object']->id, LGCanonicalurls::LGOT_MANUFACTURER);
        }
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        if (Tools::getIsset('lgcanonicalurls_type') && Validate::isLoadedObject($params['object'])) {
            return $this->updateObject($params['object']->id, LGCanonicalurls::LGOT_MANUFACTURER);
        }
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        if (Validate::isLoadedObject($params['object'])) {
            return $this->deleteObject($params['object']->id, LGCanonicalurls::LGOT_MANUFACTURER);
        }
    }

    private function getObject($id_object, $type_object)
    {
        $sql = 'SELECT * '
            .'FROM `'._DB_PREFIX_.'lgcanonicalurls` '
            .'WHERE `id_object` = '.$id_object
            .'  AND `type_object` = '.$type_object;
        return Db::getInstance()->getRow($sql);
    }

    /*
     * Guarda la url canonica a nivel local si se ha establecido
     */
    private function addObject($object_id, $tipo)
    {
        $queries = array();
        $langs = Language::getLanguages();
        $canonical_url = array();

//        if (Tools::getIsset('lgcanonicalurls_type')
//            &&
//            Tools::getIsset('lgcanonicalurls_ignoreparams')
//        ) {
        if (Tools::getIsset('lgcanonicalurls_type')) {
            $queries[] = "INSERT INTO `"._DB_PREFIX_."lgcanonicalurls`(`id_object`,`type_object`,`type`,`parameters`)".
                " VALUES("
                .pSQL($object_id).", "
                .pSQL($tipo).", "
                .pSQL(Tools::getValue('lgcanonicalurls_type')).", '"
                .pSQL(Tools::getValue('lgcanonicalurls_params', null))
                ."');";

            // Si se especifica una url canonica
            if (Tools::getValue('lgcanonicalurls_type') == LGCanonicalurls::LGCU_CUSTOM) {
                foreach ($langs as $lang) {
                    if (Tools::getIsset('lgcanonicalurls_canonical_url_'.$lang['id_lang'])
                        &&
                        trim(Tools::getValue('lgcanonicalurls_canonical_url_'.$lang['id_lang'])) != ''
                    ) {
                        $canonical_url[$lang['id_lang']] = Tools::getValue(
                            'lgcanonicalurls_canonical_url_'.$lang['id_lang']
                        );
                    }
                }
                foreach ($canonical_url as $lang => $url) {
                    $queries[] = "INSERT INTO `"
                        ._DB_PREFIX_."lgcanonicalurls_lang`(`id_object`,`type_object`,`id_lang`, `canonical_url`)".
                        " VALUES("
                        .pSQL($object_id).", "
                        .pSQL($tipo).", "
                        .pSQL($lang).",'"
                        .pSQL($url, true)
                        ."')";
                }
            }

            return $this->proccessQueries($queries);
        } else {
            return false;
        }
    }

    /*
     * Actualiza la url canonica a nivel local
     */
    private function updateObject($object_id, $tipo)
    {
        $queries       = array();
        $canonical_url = array();
        $langs         = Language::getLanguages();



        if (Tools::getIsset('lgcanonicalurls_type')) {
            $queries[] = "REPLACE `"._DB_PREFIX_."lgcanonicalurls` \n".
                "SET ".
                " `type` = ".pSQL(Tools::getValue('lgcanonicalurls_type')).", ".
                " `parameters` = '".pSQL(Tools::getValue('lgcanonicalurls_params', null))."', ".
                " `id_object`= ".pSQL($object_id).", ".
                " `type_object` = ".pSQL($tipo).";";

            // Si se especifica una url canonica

            ////carlos
            if (Tools::getValue('lgcanonicalurls_type') == LGCanonicalurls::LGCU_CUSTOM) {
                foreach ($langs as $lang) {
                    if (Tools::getIsset('lgcanonicalurls_canonical_url_'.$lang['id_lang'])
                        &&
                        trim(Tools::getValue('lgcanonicalurls_canonical_url_'.$lang['id_lang'])) != ''
                    ) {
                        $canonical_url[$lang['id_lang']] = Tools::getValue(
                            'lgcanonicalurls_canonical_url_'.$lang['id_lang']
                        );
                    }
                }

                if (!empty($canonical_url)) {
                    foreach ($canonical_url as $lang => $url) {
                        $queries[] = "REPLACE `"._DB_PREFIX_."lgcanonicalurls_lang` ".
                            "SET ".
                            " `canonical_url` = '".pSQL($url)."', ".
                            " `id_object`= ".pSQL($object_id).", ".
                            " `type_object` = ".pSQL($tipo).", ".
                            " `id_lang` = ".pSQL($lang).";";
                    }
                }
            }

            return $this->proccessQueries($queries);
        }
    }

    /*
     * Elimina la url canonica a nivel local cuando el producto se elimina
     */
    private function deleteObject($id_object, $type_object)
    {
        $queries = array(
            "DELETE FROM `"._DB_PREFIX_."lgcanonicalurls` ".
            "WHERE `id_object` = ".pSQL($id_object).
            " AND `type_object` = ".pSQL($type_object),

            "DELETE FROM `"._DB_PREFIX_."lgcanonicalurls_lang` ".
            "WHERE `id_object` = ".pSQL($id_object).
            "  AND `type_object` = ".pSQL($type_object)
        );

        return $this->proccessQueries($queries);
    }

    /*
     * Obtiene la url canonica
     */
    private function getCanonicalUrl()
    {
        $lang = Context::getContext()->language->id;

        $canonical_url = false;

        // if (!is_null($id_object) && !is_null($type_object)) {
        if (!is_null($this->type_object)) {
            $tipo = $this->getTipo($this->id_object, $this->type_object);
//          if ($tipo !== false) {
            if ($tipo == LGCanonicalurls::LGCU_CUSTOM) {
                $canonical_url = $this->getLocalCanonicalUrl($this->id_object, $this->type_object, $lang);
                // Si no se rellena algún lenguaje se debe usar la por defecto
                if (trim($canonical_url) == '') {
                    $canonical_url = $this->getDefaultCanonicalUrl();
                }
            } if ($tipo == LGCanonicalurls::LGCU_AUTO) {
                $canonical_url = $this->getDefaultCanonicalUrl(); //'devolver el link del producto';
            }
//          }
        }
        return $canonical_url;
//        } else { // Si no usamos las por defecto
//            return $this->getDefaultCanonicalUrl();
//        }
    }

    private function getTipo($id_object, $type_object)
    {
        if (is_null($id_object) || $id_object == '' || is_null($type_object) || $type_object == '') {
            if ($type_object == LGCanonicalurls::LGOT_HOME) {
                if (Configuration::get('LGCANONICALURLS_CANONICALHOME') == 1) {
                    if (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom') {
                        return LGCanonicalurls::LGCU_CUSTOM;
                    } else {
                        return LGCanonicalurls::LGCU_AUTO;
                    }
                }
            }
//            return false;
            // Para que devuelva la configuración por defecto en las paginas de lista de proveedores, fabrcantes, ..
            return LGCanonicalurls::LGCU_AUTO;
        }
        $query  = "SELECT type+0 AS type ".
            'FROM `'._DB_PREFIX_.'lgcanonicalurls` '.
            'WHERE `id_object` = '.pSQL($id_object).' '.
            '  AND `type_object` = '.pSQL($type_object).';';
        $return = Db::getInstance()->getValue($query);

        //die("<pre>".print_r($query,true)."<pre>");
        if (!empty($return)) {
            //die($return);
            return $return; //$return[0]['type'];
        } else {
            return LGCanonicalurls::LGCU_AUTO;
        }
    }

    private function getLocalCanonicalUrl($id_object, $type_object, $lang)
    {
        if (is_null($id_object)
            || $id_object == ''
        ) {
            if ($type_object == LGCanonicalurls::LGOT_HOME) {
                $home_langs = Tools::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);
                $languages  = Language::getLanguages();
                $return = '';
                foreach ($languages as $language) {
                    if ($language['id_lang'] == $lang && isset($home_langs[$language['id_lang']])) {
                        $return = $home_langs[$language['id_lang']];
                    }
                }
                return $this->getProtocol().$return.$this->getBaseDir();
            }
            return false;
        }

        if (is_null($type_object)
            || $type_object == ''
            || is_null($lang)
            || $lang == ''
        ) {
            return false;
        }

        $query  = 'SELECT canonical_url '.
            'FROM `'._DB_PREFIX_.'lgcanonicalurls_lang` '.
            'WHERE `id_object` = '.pSQL($id_object).' '.
            '  AND `type_object` = '.pSQL($type_object).' '.
            '  AND `id_lang` = '.pSQL($lang).';';

        $return = Db::getInstance()->ExecuteS($query);

        if (!empty($return)) {
            return $return[0]['canonical_url'];
        } else {
            return false;
        }
    }

    public function getProtocol()
    {
        $base = (
        (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'))
            ?
            'https://'
            :
            'http://'
        );

        if (Configuration::get('LGCANONICALURLS_FORCEHTTPHTTPS')) {
            $base = Configuration::get('LGCANONICALURLS_HTTPHTTPS_VAL').'://';
        }

        return $base;
    }

    public function getBaseDir()
    {
        return Context::getContext()->shop->physical_uri;
    }

    public function getBaseUri($id_shop = false)
    {
        $base = $this->getProtocol();

        if (!$id_shop) {
            $id_shop = $this->context->shop->id;
        }

        $shop      = new Shop($id_shop);

        /*$languages = Language::getLanguages();
        if (
            $id_lang && count($languages) > 1
            && Configuration::get('PS_REWRITING_SETTINGS', null, null, $shop->id)
        ) {
            $iso = Language::getIsoById($id_lang).'/';
        } else {
            $iso = '';
        }*/

        if (Configuration::get('LGCANONICALURLS_CANONICDOMAIN') != '') {
            $domain = Configuration::get('LGCANONICALURLS_CANONICDOMAIN');
        } else {
            $domain = $shop->domain;
        }

        return $base.$domain; //.$shop->getBaseURI().$iso;
    }

    private function procesarParametros($uri)
    {
        $context = Context::getContext();
        //global $cookie, $protocol_content, $protocol, $page_name, $link;

        if (trim(Configuration::get('LGCANONICALURLS_PARAMS')) != '' // Los parametros no estan vacíos
            && count($params = explode(',', trim(Configuration::get('LGCANONICALURLS_PARAMS')))) > 0
            && strpos($uri, '?')  // La Uri tiene parametros
            && Configuration::get('LGCANONICALURLS_IGNORE_PARAMS') // Ignorar parámetros activado
        ) {
            // Anulamos el paramtero
            if ($context->cookie->id_lang == Configuration::get('PS_LANG_DEFAULT')) {
                $params[] = 'id_lang';
            }
            // Anulamos la eliminación de la paginación, se debe hacer especificándolo
//            if ((int)Tools::getValue('p') <= 1) {
//                $params[] = 'p';
//            }

            foreach ($params as &$param) {
                if (trim($param) != '') {
                    $param = '/'.trim($param).'\=[^\&]*\&?/';
                }
            }

            $params[] = '/\&$/';
            $uri      = explode('?', $uri);
            $uri[1]   = preg_replace($params, '', $uri[1]);
            $uri      = $uri[0].($uri[1] ? '?'.$uri[1] : '');
        }
        return $uri;
    }

    private function cambiarParametro($p, $valor, $uri)
    {
        $parsed = parse_url($uri);
        if (!isset($parsed['query'])) {
            $parsed['query'] = $p.'='.$valor;
        }
        parse_str($parsed['query'], $parametros);
        $params_str = '';
        if (!empty($parametros)) {
            $params_str .= '?';
            $p_aux = array();
            foreach ($parametros as $key => $value) {
                if ($key == $p) {
                    if (!is_null($valor) && trim($valor) != '') {
                        $p_aux[] = $key.'='.$valor;
                    } else {
                        $p_aux[] = $key;
                    }
                } else {
                    if (!is_null($value) && trim($value) != '') {
                        $p_aux[] = $key.'='.$value;
                    } else {
                        $p_aux[] = $key;
                    }
                }
            }
            $params_str .= implode('&', $p_aux);
        }

        $final_uri = $parsed['scheme'].'://'.$parsed['host'].$parsed['path'].$params_str;
        return $final_uri;
    }

    private function quitarParametros($uri, $exclusion_array = null)
    {
        if (Configuration::get('LGCANONICALURLS_IGNORE_PARAMS')) {
            if (trim(Configuration::get('LGCANONICALURLS_PARAMS')) != '') {
                $params = explode(',', Configuration::get('LGCANONICALURLS_PARAMS'));
                foreach ($params as $param) {
                    if (!is_null($exclusion_array) && !empty($exclusion_array)) {
                        if (in_array($param, $exclusion_array)) {
                            continue;
                        } else {
                            $uri = $this->quitarParametro($param, $uri);
                        }
                    } else {
                        $uri = $this->quitarParametro($param, $uri);
                    }
                }
            }
        }

        return $this->rtrimString($uri, '/'); // NOTE: Custom version compatible for all PS versions
    }

    private function removeAllparameters($uri)
    {
        $parsed = parse_url($uri);
        return Tools::rtrimString($parsed['scheme'].'://'.$parsed['host'].$parsed['path'], '/');
    }

    private function quitarParametro($p, $uri)
    {
        $parsed = parse_url($uri);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $parametros);
            $params_str = '';
            if (!empty($parametros)) {
                if (count($parametros)>1) {
                    $params_str .= '?';
                }
                $p_aux = array();
                foreach ($parametros as $key => $value) {
                    if ($key != $p) {
                        if (!is_null($value) && trim($value) != '') {
                            $p_aux[] = $key.'='.$value; //$parametros[$key];
                        } else {
                            $p_aux[] = $key; //$parametros[$key];
                        }
                    }
                }
                $params_str .= implode('&', $p_aux);
            }

            $final_uri = $parsed['scheme'].'://'.$parsed['host'].$parsed['path'].$params_str;
            return $final_uri;
        } else {
            return $uri;
        }
    }

    private function getDefaultCanonicalUrl()
    {
        $base = $this->getBaseUri();
        return $base.$this->procesarParametros($_SERVER['REQUEST_URI']);
    }

    public function getCustomField($id_object, $type_object)
    {
        $fields = array();
        $result = $this->getLocalCanonicalUrl($id_object, $type_object);

        if ($result) {
            foreach ($result as $field) {
                $fields[$field['id_lang']] = $field['canonical_url'];
            }
        }

        return $fields;
    }

    private function rtrimString($string, $char = null)
    {
        if (is_null($char)) {
            return $string;
        }

        if (version_compare(_PS_MODULE_DIR_, '1.5.6.2', '>=')) {
            return Tools::rtrimString($string, $char);
        } else {
            return rtrim($string, $char); // NOTE: Tools::rtrimString is not available for versions priors to 1.5.6.2
        }
    }
}
