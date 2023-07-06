<?php

class MyBasicModuleTestModuleFrontController extends ModuleFrontController
{
    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet(
            'module-mybasicmodule-style',
            'modules/mybasicmodule/css/custom.css',
            [
                'media' => 'all',
                'priority' => 200,
            ]
        );
    }

    public function initContent()
    {
        parent::initContent();
        $customer = (int)$this->context->cookie->id_customer;
        $jsonData = array(
            'userId' => $customer,
        );
        $url=Configuration::get("API_VALUE");

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode($jsonData),
            ),
        );

        $apiResponse = file_get_contents($url, false, stream_context_create($options));

        if (!empty($apiResponse)) {
            $apiData = json_decode($apiResponse, true);
    
            if (is_array($apiData) && (empty($apiData) || (count($apiData) === 1 && empty($apiData[0])))) {
                return $this->setTemplate("module:mybasicmodule/views/templates/front/error.tpl");
            }
            $this->context->smarty->assign(array(
                'apiData' => $apiData,
            ));
            return $this->setTemplate("module:mybasicmodule/views/templates/front/myAddresses.tpl");
        }
    
        return $this->setTemplate("module:mybasicmodule/views/templates/front/error.tpl");
    }

    public function postProcess()
    {
        if (Tools::isSubmit("form")) {

            return Tools::redirect("Url");
        }
    }
}
