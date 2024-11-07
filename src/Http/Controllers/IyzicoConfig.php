<?php

namespace Aghaeian\ZiraatBankasi\Http\Controllers;

use ZiraatBankasiSDK\Options;

class ZiraatBankasiConfig
{
    public function options()
    {
        $options = new Options();
        $merchantId = core()->getConfigData('sales.payment_methods.ziraatbankasi.merchant_id');
        $terminalId = core()->getConfigData('sales.payment_methods.ziraatbankasi.terminal_id');
        $storeKey   = core()->getConfigData('sales.payment_methods.ziraatbankasi.store_key');

        $options->setMerchantId($merchantId);
        $options->setTerminalId($terminalId);
        $options->setStoreKey($storeKey);
        
        $baseUrl = $this->environment();
        $options->setBaseUrl($baseUrl);
        
        return $options;
    }

    /**
     * Ziraat Bankası sanal POS için ortam ayarlarını yapar ve döndürür.
     */
    public function environment()
    { 
        $sandboxMode = core()->getConfigData('sales.payment_methods.ziraatbankasi.sandbox');

        if ($sandboxMode) {			
            return "https://test.ziraatbank.com.tr/api";
        }

        return "https://sanalpos2.ziraatbank.com.tr/fim/est3Dgate";
    }
}
