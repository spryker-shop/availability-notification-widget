<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AvailabilityNotificationWidget\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class AvailabilityNotificationWidgetControllerProvider extends AbstractYvesControllerProvider
{
    public const ROUTE_AVAILABILITY_NOTIFICATION_UNSUBSCRIBE = 'availability-notification/unsubscribe';
    public const ROUTE_AVAILABILITY_NOTIFICATION_SUBSCRIBE = 'availability-notification/subscribe';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app): void
    {
        $this->addAvailabilityNotificationSubscribeRoute()
            ->addAvailabilityNotificationUnsubscribeRoute();
    }

    /**
     * @return $this
     */
    protected function addAvailabilityNotificationSubscribeRoute(): self
    {
        $this->createPostController('/{notificationAvailability}/subscribe', static::ROUTE_AVAILABILITY_NOTIFICATION_SUBSCRIBE, 'AvailabilityNotificationWidget', 'Subscription', 'subscribe')
            ->assert('notificationAvailability', $this->getAllowedLocalesPattern() . 'availability-notification|availability-notification')
            ->value('notificationAvailability', 'availability-notification');

        return $this;
    }

    /**
     * @return $this
     */
    protected function addAvailabilityNotificationUnsubscribeRoute(): self
    {
        $this->createPostController('/{notificationAvailability}/unsubscribe', self::ROUTE_AVAILABILITY_NOTIFICATION_UNSUBSCRIBE, 'AvailabilityNotificationWidget', 'Subscription', 'unsubscribe')
            ->assert('notificationAvailability', $this->getAllowedLocalesPattern() . 'availability-notification|availability-notification')
            ->value('notificationAvailability', 'availability-notification');

        return $this;
    }
}
