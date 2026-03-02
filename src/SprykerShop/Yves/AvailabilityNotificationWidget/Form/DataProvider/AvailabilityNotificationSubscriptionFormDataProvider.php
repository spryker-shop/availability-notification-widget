<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AvailabilityNotificationWidget\Form\DataProvider;

use Generated\Shared\Transfer\AvailabilityNotificationSubscriptionTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToCustomerClientInterface;

class AvailabilityNotificationSubscriptionFormDataProvider
{
    /**
     * @var \SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToCustomerClientInterface
     */
    protected $customerClient;

    public function __construct(AvailabilityNotificationWidgetToCustomerClientInterface $customerClient)
    {
        $this->customerClient = $customerClient;
    }

    public function getData(ProductViewTransfer $productViewTransfer): AvailabilityNotificationSubscriptionTransfer
    {
        $subscription = new AvailabilityNotificationSubscriptionTransfer();
        $subscription->setSku($productViewTransfer->getSku());

        $customerTransfer = $this->customerClient->getCustomer();

        if ($customerTransfer !== null) {
            $subscription->setEmail($customerTransfer->getEmail());
        }

        return $subscription;
    }
}
