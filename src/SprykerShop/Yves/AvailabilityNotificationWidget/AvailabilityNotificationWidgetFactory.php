<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AvailabilityNotificationWidget;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToAvailabilityNotificationClientInterface;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToCustomerClientInterface;
use SprykerShop\Yves\AvailabilityNotificationWidget\Form\AvailabilityNotificationSubscriptionForm;
use SprykerShop\Yves\AvailabilityNotificationWidget\Form\AvailabilityNotificationUnsubscriptionForm;
use SprykerShop\Yves\AvailabilityNotificationWidget\Form\DataProvider\AvailabilityNotificationSubscriptionFormDataProvider;
use SprykerShop\Yves\AvailabilityNotificationWidget\Form\DataProvider\AvailabilityNotificationUnsubscriptionFormDataProvider;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class AvailabilityNotificationWidgetFactory extends AbstractFactory
{
    public function createAvailabilityNotificationSubscriptionForm(): FormInterface
    {
        return $this->getFormFactory()->create(AvailabilityNotificationSubscriptionForm::class);
    }

    public function createAvailabilityUnsubscribeForm(): FormInterface
    {
        return $this->getFormFactory()->create(AvailabilityNotificationUnsubscriptionForm::class);
    }

    public function createAvailabilityNotificationSubscriptionFormDataProvider(): AvailabilityNotificationSubscriptionFormDataProvider
    {
        return new AvailabilityNotificationSubscriptionFormDataProvider($this->getCustomerClient());
    }

    public function createAvailabilityUnsubscribeFormDataProvider(): AvailabilityNotificationUnsubscriptionFormDataProvider
    {
        return new AvailabilityNotificationUnsubscriptionFormDataProvider($this->getCustomerClient());
    }

    public function getAvailabilityNotificationClient(): AvailabilityNotificationWidgetToAvailabilityNotificationClientInterface
    {
        return $this->getProvidedDependency(AvailabilityNotificationWidgetDependencyProvider::CLIENT_AVAILABILITY_NOTIFICATION);
    }

    public function getFormFactory(): FormFactory
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    public function getCustomerClient(): AvailabilityNotificationWidgetToCustomerClientInterface
    {
        return $this->getProvidedDependency(AvailabilityNotificationWidgetDependencyProvider::CLIENT_CUSTOMER);
    }
}
