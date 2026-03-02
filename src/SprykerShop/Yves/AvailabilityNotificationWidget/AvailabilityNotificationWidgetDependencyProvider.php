<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AvailabilityNotificationWidget;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToAvailabilityNotificationClientBridge;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToCustomerClientBridge;

class AvailabilityNotificationWidgetDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_AVAILABILITY_NOTIFICATION = 'CLIENT_AVAILABILITY_NOTIFICATION';

    /**
     * @var string
     */
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';

    /**
     * @var string
     */
    public const CLIENT_SESSION = 'CLIENT_SESSION';

    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addAvailabilityNotificationClient($container);
        $container = $this->addCustomerClient($container);

        return $container;
    }

    protected function addAvailabilityNotificationClient(Container $container): Container
    {
        $container->set(static::CLIENT_AVAILABILITY_NOTIFICATION, function (Container $container) {
            return new AvailabilityNotificationWidgetToAvailabilityNotificationClientBridge($container->getLocator()->availabilityNotification()->client());
        });

        return $container;
    }

    protected function addCustomerClient(Container $container): Container
    {
        $container->set(static::CLIENT_CUSTOMER, function (Container $container) {
            return new AvailabilityNotificationWidgetToCustomerClientBridge($container->getLocator()->customer()->client());
        });

        return $container;
    }
}
