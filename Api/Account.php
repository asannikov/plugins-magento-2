<?php
/**
 * 26.04.18
 * LimeSoda - Limesoda M2Demo
 *
 * Created by Anton Sannikov.
 *
 * @category    Lime_Soda
 * @package     Limesoda M2Demo
 * @copyright   Copyright (c) 2018 LimeSoda. (http://www.limesoda.com)
 *
 * @file Account.php
 */

namespace LimeSoda\Cashpresso\Api;

use DomainException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception;

class Account extends Base
{
    /** @deprecated **/
    const METHOD_TARGET_ACCOUNTS = 'partner/targetAccounts';

    protected $postData;

    /**
     * @throws LocalizedException
     */
    public function getContent(): array
    {
        $data = [
            'partnerApiKey' => $this->getPartnerApiKey(),
            'verificationHash' => hash('sha512', $this->getSecretKey() . ';' . $this->getPartnerApiKey())
        ];

        $this->postData = $data;

        return $data;
    }

    /**
     * @deprecated as target account is not used anymore. Later it will be removed completely 
     * 
     * @throws Exception
     * @throws DomainException
     */
    public function getTargetAccounts()
    {
        return [];

        if ($this->getConfig()->isDebugEnabled()) {
            $this->logger->debug(print_r($this->postData, true));
        }

        $request = $this->getRequest(Account::METHOD_TARGET_ACCOUNTS);

        $response = $request->send();

        if ($response->isSuccess()) {
            $respond = $this->json->deserialize($response->getBody());

            if (is_array($respond)) {
                if (!empty($respond['success']) && !empty($respond['targetAccounts'])) {
                    return $respond['targetAccounts'];
                }
            }

            return [];
        }

        throw new DomainException(__('cashpresso target account request error: %1', $response->getReasonPhrase()));
    }
}
