<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

 if(!defined('_PS_VERSION_')){
    exit;
 }

 //main class
 class MyBasicModule extends Module implements WidgetInterface
 {

    public function __construct()
    {
        $this->name = "mybasicmodule";
        $this->tab = "front_office_features";
        $this->version = "1.0";
        $this->author = "Mijail Surco";
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            "min" => "1.6",
            "max" => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("My basic module");
        $this->description = $this->l("This is a great module");
        $this->confirmUninstall = $this->l("Are you crazy buddy, you really want to remove this module?");

    }

    //install method
    public function install()
    {
        return parent::install() 
        && $this->registerHook(['registerGDPRConsent', 'displayContactContent'])
        && $this->dbInstall();
    }

    //uninstall method
    public function uninstall()
    {
        return parent::uninstall();
    }

    //sql install
    public function dbInstall()
    {
        //sql query that create a table
        return true;
    }

    public function renderWidget($hookName, array $configuration)
    {

        if($hookName === 'displayFooter'){
            return $this->fetch("module:mybasicmodule/views/templates/hook/customerAccount.tpl");
        }
        $this->context->smarty->assign($this->getWidgetVariables($hookName,$configuration));


        return $this->fetch("module:mybasicmodule/views/templates/hook/customerAccount.tpl");
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        return [
            'link' => $this->context->link->getModuleLink($this->name,"test"),
        ];
    }

    public function getContent()
    {
        if(Tools::isSubmit('submit' . $this->name))
        {
            $output = '';
            $api = Tools::getValue('api');
            if($api && !empty($api) && Validate::isGenericName($api) ){
                Configuration::updateValue('API_VALUE',$api);
                $output .= $this->displayConfirmation($this->trans('Form submitted successfully'));
            } else{
                $output .= $this->displayError($this->trans('Something went wrong with the Form'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $defaultLang=(int) Configuration::get('PS_LANG_DEFAULT');
        //form Inputs
        $fields[0]['form'] = [
            'legend'=> [ 'title'=> $this->trans('Settings - Enter the Endpoint to configure your module'),],
            'input' => 
                [
                [
                    'type' => 'text',
                    'label' => $this->l('API Endpoint'),
                    'name' => 'api',
                    'size' => 20,
                    'required' => true,
                ]
                ],
            'submit' => [
                'title'=> $this->trans('Save the api'),
                'class'=> 'btn btn-primary pull-right'
            ]
            ];

        //istance Form Helper
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name; 
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        
        // Default language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
            
        //Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back'=>[
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
                ]
            ];
        $helper->fields_value['api'] = Configuration::get('API_VALUE');
        return $helper->generateForm($fields);    
    }
 }