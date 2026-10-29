<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PhoneCustomerSearch extends Module
{
    private $adminAssetsAdded = false;
    public function __construct()
    {
        $this->name = 'phonecustomersearch';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Mohammad Babaei';
        $this->url = 'https://adschi.com';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('جستجوی مشتری با شماره موبایل');
        $this->description = $this->l('افزودن جستجوی مشتری بر اساس شماره موبایل در صفحه ایجاد سفارش مدیریت.');
        $this->ps_versions_compliancy = array('min' => '1.7.8.0', 'max' => '9.2.0');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->installAdminTab();
    }

    public function uninstall()
    {
        return $this->uninstallAdminTab() && parent::uninstall();
    }

    private function installAdminTab()
    {
        if (Tab::getIdFromClassName('AdminPhoneCustomerSearch')) {
            return true;
        }
        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = 'AdminPhoneCustomerSearch';
        $tab->name = array();
        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = 'Phone customer search';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders');
        $tab->module = $this->name;
        return (bool) $tab->add();
    }

    private function uninstallAdminTab()
    {
        $id = (int) Tab::getIdFromClassName('AdminPhoneCustomerSearch');
        if (!$id) {
            return true;
        }
        $tab = new Tab($id);
        return (bool) $tab->delete();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->registerOrderSearchAssets();
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->registerOrderSearchAssets();
    }

    private function registerOrderSearchAssets()
    {
        if ($this->adminAssetsAdded || !$this->isOrderCreationPage()
            || !isset($this->context->controller)
            || !method_exists($this->context->controller, 'addJS')) {
            return;
        }
        $this->adminAssetsAdded = true;
        $this->context->controller->addJS($this->_path . 'views/js/admin-order-phone-search.js?v=1.1.0');
        if (method_exists($this->context->controller, 'addCSS')) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin-order-phone-search.css?v=1.1.0');
        }
        Media::addJsDef(array(
            'phoneCustomerSearchEndpoint' => $this->context->link->getAdminLink('AdminPhoneCustomerSearch'),
        ));
    }

    private function isOrderCreationPage()
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (strpos($requestUri, '/sell/orders/new') !== false) {
            return true;
        }

        return Tools::getValue('controller') === 'AdminOrders'
            && (bool) Tools::getValue('addorder');
    }
}
