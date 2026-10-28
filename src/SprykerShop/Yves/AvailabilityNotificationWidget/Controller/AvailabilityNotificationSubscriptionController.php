<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\AvailabilityNotificationWidget\Controller;

use Generated\Shared\Transfer\AvailabilityNotificationSubscriptionResponseTransfer;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerShop\Yves\AvailabilityNotificationWidget\AvailabilityNotificationWidgetFactory getFactory()
 */
class AvailabilityNotificationSubscriptionController extends AbstractController
{
    /**
     * @var string
     */
    public const GLOSSARY_KEY_SUCCESSFULLY_SUBSCRIBED = 'availability_notification.subscribed';

    /**
     * @var string
     */
    public const GLOSSARY_KEY_SUCCESSFULLY_UNSUBSCRIBED = 'availability_notification.unsubscribed';

    /**
     * @uses \Spryker\Shared\AvailabilityNotification\AvailabilityNotificationConfig::MESSAGE_SUBSCRIPTION_ALREADY_EXISTS
     */
    protected const string ERROR_MESSAGE_SUBSCRIPTION_ALREADY_EXISTS = 'Subscription already exists.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribeAction(Request $request)
    {
        $this->executeSubscribeAction($request);

        return $this->redirectResponseExternal($request->headers->get('referer'));
    }

    protected function executeSubscribeAction(Request $request): void
    {
        $subscriptionForm = $this
            ->getFactory()
            ->createAvailabilityNotificationSubscriptionForm();

        $subscriptionForm->handleRequest($request);

        if ($subscriptionForm->isSubmitted() === false || $subscriptionForm->isValid() === false) {
            /** @var array<\Symfony\Component\Form\FormError> $errors */
            $errors = $subscriptionForm->getErrors(true);
            foreach ($errors as $error) {
                $this->addErrorMessage($error->getMessage());
            }

            return;
        }

        /** @var \Generated\Shared\Transfer\AvailabilityNotificationSubscriptionTransfer $availabilityNotificationSubscriptionTransfer */
        $availabilityNotificationSubscriptionTransfer = $subscriptionForm->getData();
        $customerTransfer = $this->getFactory()->getCustomerClient()->getCustomer();

        if ($customerTransfer !== null) {
            $availabilityNotificationSubscriptionTransfer->setCustomerReference($customerTransfer->getCustomerReference());
        }

        $responseTransfer = $this->getFactory()
            ->getAvailabilityNotificationClient()
            ->subscribe($availabilityNotificationSubscriptionTransfer);

        // A repeated subscription answers like a new one, so the form, which needs no login, does not reveal who is subscribed.
        if ($this->isRepeatedSubscription($responseTransfer)) {
            $this->addSuccessMessage(static::GLOSSARY_KEY_SUCCESSFULLY_SUBSCRIBED);

            return;
        }

        if ($responseTransfer->getIsSuccess() === false) {
            $this->addErrorMessage($responseTransfer->getErrorMessage());

            return;
        }

        $this->addSuccessMessage(static::GLOSSARY_KEY_SUCCESSFULLY_SUBSCRIBED);

        if ($customerTransfer !== null) {
            $customerTransfer->addAvailabilityNotificationSubscriptionSku($responseTransfer->getAvailabilityNotificationSubscription()->getSku());
        }
    }

    protected function isRepeatedSubscription(AvailabilityNotificationSubscriptionResponseTransfer $responseTransfer): bool
    {
        return $responseTransfer->getIsSuccess() === false
            && $responseTransfer->getErrorMessage() === static::ERROR_MESSAGE_SUBSCRIPTION_ALREADY_EXISTS;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unsubscribeAction(Request $request)
    {
        $this->executeUnsubscribeAction($request);

        return $this->redirectResponseExternal($request->headers->get('referer'));
    }

    protected function executeUnsubscribeAction(Request $request): void
    {
        $unsubscribeForm = $this->getFactory()
            ->createAvailabilityUnsubscribeForm();
        $unsubscribeForm->handleRequest($request);

        if ($unsubscribeForm->isSubmitted() === false || $unsubscribeForm->isValid() === false) {
            return;
        }

        /** @var \Generated\Shared\Transfer\AvailabilityNotificationSubscriptionTransfer $subscriptionTransfer */
        $subscriptionTransfer = $unsubscribeForm->getData();
        $customerTransfer = $this->getFactory()->getCustomerClient()->getCustomer();

        if ($customerTransfer === null) {
            return;
        }

        $subscriptionTransfer->setCustomerReference($customerTransfer->getCustomerReference());

        $responseTransfer = $this->getFactory()
            ->getAvailabilityNotificationClient()
            ->unsubscribeByCustomerReferenceAndSku($subscriptionTransfer);

        if ($responseTransfer->getIsSuccess() === false) {
            $this->addErrorMessage($responseTransfer->getErrorMessage());

            return;
        }

        $this->removeAvailabilityNotificationSubscriptionFromCustomer($responseTransfer->getAvailabilityNotificationSubscription()->getSku());

        $this->addSuccessMessage(static::GLOSSARY_KEY_SUCCESSFULLY_UNSUBSCRIBED);
    }

    protected function removeAvailabilityNotificationSubscriptionFromCustomer(string $sku): void
    {
        $customerTransfer = $this->getFactory()->getCustomerClient()->getCustomer();

        if ($customerTransfer === null) {
            return;
        }

        $availabilityNotificationSubscriptionSkus = $customerTransfer->getAvailabilityNotificationSubscriptionSkus();

        $key = array_search($sku, $availabilityNotificationSubscriptionSkus);

        if ($key !== false) {
            unset($availabilityNotificationSubscriptionSkus[$key]);
        }

        $customerTransfer->setAvailabilityNotificationSubscriptionSkus($availabilityNotificationSubscriptionSkus);
    }
}
