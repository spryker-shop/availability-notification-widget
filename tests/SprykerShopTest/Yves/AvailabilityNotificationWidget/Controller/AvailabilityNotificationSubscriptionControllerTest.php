<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShopTest\Yves\AvailabilityNotificationWidget\Controller;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AvailabilityNotificationSubscriptionResponseTransfer;
use Generated\Shared\Transfer\AvailabilityNotificationSubscriptionTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use SprykerShop\Yves\AvailabilityNotificationWidget\AvailabilityNotificationWidgetFactory;
use SprykerShop\Yves\AvailabilityNotificationWidget\Controller\AvailabilityNotificationSubscriptionController;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToAvailabilityNotificationClientInterface;
use SprykerShop\Yves\AvailabilityNotificationWidget\Dependency\Client\AvailabilityNotificationWidgetToCustomerClientInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerShopTest
 * @group Yves
 * @group AvailabilityNotificationWidget
 * @group Controller
 * @group AvailabilityNotificationSubscriptionControllerTest
 * Add your own group annotations below this line
 */
class AvailabilityNotificationSubscriptionControllerTest extends Unit
{
    /**
     * @uses \Spryker\Shared\AvailabilityNotification\AvailabilityNotificationConfig::MESSAGE_SUBSCRIPTION_ALREADY_EXISTS
     */
    protected const string ERROR_MESSAGE_SUBSCRIPTION_ALREADY_EXISTS = 'Subscription already exists.';

    /**
     * @uses \Spryker\Shared\AvailabilityNotification\AvailabilityNotificationConfig::MESSAGE_PRODUCT_NOT_FOUND
     */
    protected const string ERROR_MESSAGE_PRODUCT_NOT_FOUND = 'Product not found.';

    protected const string SKU = 'sku-1';

    protected const string MESSAGE_TYPE_SUCCESS = 'success';

    protected const string MESSAGE_TYPE_ERROR = 'error';

    public function testGivenANewSubscriptionWhenSubscribingThenItShowsTheSubscribedMessage(): void
    {
        // Arrange
        $controller = $this->createController(
            (new AvailabilityNotificationSubscriptionResponseTransfer())
                ->setIsSuccess(true)
                ->setAvailabilityNotificationSubscription((new AvailabilityNotificationSubscriptionTransfer())->setSku(static::SKU)),
        );

        // Act
        $controller->subscribeAction(new Request());

        // Assert
        $this->assertSame(
            [[static::MESSAGE_TYPE_SUCCESS, AvailabilityNotificationSubscriptionController::GLOSSARY_KEY_SUCCESSFULLY_SUBSCRIBED]],
            $controller->messages,
        );
    }

    public function testGivenAnExistingSubscriptionWhenSubscribingAgainThenItShowsTheSameSubscribedMessageAsANewSubscription(): void
    {
        // Arrange
        $controller = $this->createController(
            (new AvailabilityNotificationSubscriptionResponseTransfer())
                ->setIsSuccess(false)
                ->setErrorMessage(static::ERROR_MESSAGE_SUBSCRIPTION_ALREADY_EXISTS),
        );

        // Act
        $controller->subscribeAction(new Request());

        // Assert
        $this->assertSame(
            [[static::MESSAGE_TYPE_SUCCESS, AvailabilityNotificationSubscriptionController::GLOSSARY_KEY_SUCCESSFULLY_SUBSCRIBED]],
            $controller->messages,
        );
    }

    public function testGivenALoggedInCustomerWithAnExistingSubscriptionWhenSubscribingAgainThenTheCustomerSessionIsLeftUnchanged(): void
    {
        // Arrange
        $customerTransfer = (new CustomerTransfer())->setCustomerReference('customer-reference');
        $controller = $this->createController(
            (new AvailabilityNotificationSubscriptionResponseTransfer())
                ->setIsSuccess(false)
                ->setErrorMessage(static::ERROR_MESSAGE_SUBSCRIPTION_ALREADY_EXISTS),
            $customerTransfer,
        );

        // Act
        $controller->subscribeAction(new Request());

        // Assert
        $this->assertSame([], $customerTransfer->getAvailabilityNotificationSubscriptionSkus());
    }

    public function testGivenAProductThatDoesNotExistWhenSubscribingThenItStillShowsTheError(): void
    {
        // Arrange
        $controller = $this->createController(
            (new AvailabilityNotificationSubscriptionResponseTransfer())
                ->setIsSuccess(false)
                ->setErrorMessage(static::ERROR_MESSAGE_PRODUCT_NOT_FOUND),
        );

        // Act
        $controller->subscribeAction(new Request());

        // Assert
        $this->assertSame([[static::MESSAGE_TYPE_ERROR, static::ERROR_MESSAGE_PRODUCT_NOT_FOUND]], $controller->messages);
    }

    protected function createController(
        AvailabilityNotificationSubscriptionResponseTransfer $responseTransfer,
        ?CustomerTransfer $customerTransfer = null
    ): AvailabilityNotificationSubscriptionController {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->method('isSubmitted')->willReturn(true);
        $formMock->method('isValid')->willReturn(true);
        $formMock->method('getData')->willReturn(
            (new AvailabilityNotificationSubscriptionTransfer())->setEmail('subscriber@spryker.local')->setSku(static::SKU),
        );

        $availabilityNotificationClientMock = $this->createMock(AvailabilityNotificationWidgetToAvailabilityNotificationClientInterface::class);
        $availabilityNotificationClientMock->method('subscribe')->willReturn($responseTransfer);

        $customerClientMock = $this->createMock(AvailabilityNotificationWidgetToCustomerClientInterface::class);
        $customerClientMock->method('getCustomer')->willReturn($customerTransfer);

        $factoryMock = $this->createMock(AvailabilityNotificationWidgetFactory::class);
        $factoryMock->method('createAvailabilityNotificationSubscriptionForm')->willReturn($formMock);
        $factoryMock->method('getAvailabilityNotificationClient')->willReturn($availabilityNotificationClientMock);
        $factoryMock->method('getCustomerClient')->willReturn($customerClientMock);

        return new class ($factoryMock) extends AvailabilityNotificationSubscriptionController
        {
            /**
             * @var list<array{string, string}>
             */
            public array $messages = [];

            public function __construct(protected AvailabilityNotificationWidgetFactory $factoryMock)
            {
            }

            protected function getFactory(): AvailabilityNotificationWidgetFactory
            {
                return $this->factoryMock;
            }

            protected function addSuccessMessage(mixed $message): static
            {
                $this->messages[] = ['success', $message];

                return $this;
            }

            protected function addErrorMessage(mixed $message): static
            {
                $this->messages[] = ['error', $message];

                return $this;
            }

            protected function redirectResponseExternal(mixed $absoluteUrl, mixed $code = 302): RedirectResponse
            {
                return new RedirectResponse('/');
            }
        };
    }
}
