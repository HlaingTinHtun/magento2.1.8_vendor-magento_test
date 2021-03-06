<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Gateway\Request\Soap;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

class RefundDataBuilder implements BuilderInterface
{
    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        /** @var Payment $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($paymentInfo);

        return [
            'ccCreditService' => [
                'run' => 'true',
                'captureRequestID' => $paymentInfo->getParentTransactionId()
            ],
            'purchaseTotals' => [
                'currency' => $paymentDO->getOrder()->getCurrencyCode(),
                'grandTotalAmount' => SubjectReader::readAmount($buildSubject)
            ]
        ];
    }
}
