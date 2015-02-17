<?php
/*************************************************************************************/
/*      This file is part of the module CustomerFamily                               */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CustomerFamily\Controller\Front;

use Front\Front;
use CustomerFamily\Form\CustomerFamilyCustomerCreateForm;
use CustomerFamily\Form\CustomerFamilyCustomerProfileUpdateForm;
use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use Front\Controller\CustomerController;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\Customer\CustomerLoginEvent;
use Thelia\Core\Event\Newsletter\NewsletterEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Customer;
use Thelia\Model\NewsletterQuery;
use Thelia\Log\Tlog;

/**
 * Class CustomerFamilyFrontController
 * @package CustomerFamily\Controller\Front
 */
class CustomerFamilyFrontController extends CustomerController
{
    /**
     * Create a new customer.
     * On success, redirect to success_url if exists, otherwise, display the same view again.
     */
    public function createAction()
    {
        if (! $this->getSecurityContext()->hasCustomerUser()) {
            $message = false;

            $customerCreation = new CustomerFamilyCustomerCreateForm($this->getRequest());

            try {
                $form = $this->validateForm($customerCreation, "post");

                $customerCreateEvent = $this->createEventInstance($form->getData());

                $this->dispatch(TheliaEvents::CUSTOMER_CREATEACCOUNT, $customerCreateEvent);

                $newCustomer = $customerCreateEvent->getCustomer();

                // Newsletter
                if (true === $form->get('newsletter')->getData()) {
                    $newsletterEmail = $newCustomer->getEmail();
                    $nlEvent = new NewsletterEvent(
                        $newsletterEmail,
                        $this->getRequest()->getSession()->getLang()->getLocale()
                    );
                    $nlEvent->setFirstname($newCustomer->getFirstname());
                    $nlEvent->setLastname($newCustomer->getLastname());

                    // Security : Check if this new Email address already exist
                    if (null !== $newsletter = NewsletterQuery::create()->findOneByEmail($newsletterEmail)) {
                        $nlEvent->setId($newsletter->getId());
                        $this->dispatch(TheliaEvents::NEWSLETTER_UPDATE, $nlEvent);
                    } else {
                        $this->dispatch(TheliaEvents::NEWSLETTER_SUBSCRIBE, $nlEvent);
                    }
                }

                $this->processLogin($customerCreateEvent->getCustomer());

                $cart = $this->getCart($this->getDispatcher(), $this->getRequest());
                if ($cart->getCartItems()->count() > 0) {
                    $this->redirectToRoute('cart.view');
                } else {
                    $this->redirectSuccess($customerCreation);
                }
            } catch (FormValidationException $e) {
                $message = Translator::getInstance()->trans(
                    "Please check your input: %s",
                    ['%s' => $e->getMessage()],
                    Front::MESSAGE_DOMAIN
                );
            } catch (\Exception $e) {
                $message = Translator::getInstance()->trans(
                    "Sorry, an error occured: %s",
                    ['%s' => $e->getMessage()],
                    Front::MESSAGE_DOMAIN
                );
            }

            if ($message !== false) {
                Tlog::getInstance()->error(
                    sprintf(
                        "Error during customer creation process : %s. Exception was %s",
                        $message,
                        $e->getMessage()
                    )
                );

                $customerCreation->setErrorMessage($message);

                $this->getParserContext()
                    ->addForm($customerCreation)
                    ->setGeneralError($message)
                ;
            }
        }
    }

    /**
     * Update profile of a customer
     */
    public function updateAction()
    {
        if ($this->getSecurityContext()->hasCustomerUser()) {
            $message = false;

            $customerProfileUpdateForm = new CustomerFamilyCustomerProfileUpdateForm($this->getRequest());

            try {
                /** @var Customer $customer */
                $customer = $this->getSecurityContext()->getCustomerUser();
                $newsletterOldEmail = $customer->getEmail();

                $form = $this->validateForm($customerProfileUpdateForm, "post");

                $customerChangeEvent = $this->createEventInstance($form->getData());
                $customerChangeEvent->setCustomer($customer);
                $this->dispatch(TheliaEvents::CUSTOMER_UPDATEPROFILE, $customerChangeEvent);

                $updatedCustomer = $customerChangeEvent->getCustomer();

                // Newsletter
                if (true === $form->get('newsletter')->getData()) {
                    $nlEvent = new NewsletterEvent($updatedCustomer->getEmail(), $this->getRequest()->getSession()->getLang()->getLocale());
                    $nlEvent->setFirstname($updatedCustomer->getFirstname());
                    $nlEvent->setLastname($updatedCustomer->getLastname());

                    if (null !== $newsletter = NewsletterQuery::create()->findOneByEmail($newsletterOldEmail)) {
                        $nlEvent->setId($newsletter->getId());
                        $this->dispatch(TheliaEvents::NEWSLETTER_UPDATE, $nlEvent);
                    } else {
                        $this->dispatch(TheliaEvents::NEWSLETTER_SUBSCRIBE, $nlEvent);
                    }
                } else {
                    if (null !== $newsletter = NewsletterQuery::create()->findOneByEmail($newsletterOldEmail)) {
                        $nlEvent = new NewsletterEvent($updatedCustomer->getEmail(), $this->getRequest()->getSession()->getLang()->getLocale());
                        $nlEvent->setId($newsletter->getId());
                        $this->dispatch(TheliaEvents::NEWSLETTER_UNSUBSCRIBE, $nlEvent);
                    }
                }

                $this->processLogin($updatedCustomer);

                $this->redirectSuccess($customerProfileUpdateForm);

            } catch (FormValidationException $e) {
                $message = Translator::getInstance()->trans("Please check your input: %s", ['%s' => $e->getMessage()], Front::MESSAGE_DOMAIN);
            } catch (\Exception $e) {
                $message = Translator::getInstance()->trans("Sorry, an error occured: %s", ['%s' => $e->getMessage()], Front::MESSAGE_DOMAIN);
            }

            if ($message !== false) {
                Tlog::getInstance()->error(sprintf("Error during customer modification process : %s.", $message));

                $customerProfileUpdateForm->setErrorMessage($message);

                $this->getParserContext()
                    ->addForm($customerProfileUpdateForm)
                    ->setGeneralError($message)
                ;
            }
        }
    }

    /**
     * Update customer data. On success, redirect to success_url if exists.
     * Otherwise, display the same view again.
     */
    public function viewAction()
    {
        $this->checkAuth();

        /** @var Customer $customer */
        $customer = $this->getSecurityContext()->getCustomerUser();

        $data = array(
            'id'           => $customer->getId(),
            'title'        => $customer->getTitleId(),
            'firstname'    => $customer->getFirstName(),
            'lastname'     => $customer->getLastName(),
            'email'        => $customer->getEmail(),
            'newsletter'   => null !== NewsletterQuery::create()->findOneByEmail($customer->getEmail()),
        );

        if (null != $customerCustomerFamily = CustomerCustomerFamilyQuery::create()->findPk($customer->getId())) {
            $data["customer_family_id"] = $customerCustomerFamily->getCustomerFamilyId();
            $data["siret"] = $customerCustomerFamily->getSiret();
            $data["vat"] = $customerCustomerFamily->getVat();
        }

        $customerProfileUpdateForm = new CustomerFamilyCustomerProfileUpdateForm($this->getRequest(), 'form', $data);

        // Pass it to the parser
        $this->getParserContext()->addForm($customerProfileUpdateForm);
    }


    /**
     * Dispatch event for customer login action
     *
     * @param Customer $customer
     */
    protected function processLogin(Customer $customer)
    {
        $this->dispatch(TheliaEvents::CUSTOMER_LOGIN, new CustomerLoginEvent($customer));
    }

    /**
     * @param $data
     * @return \Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent
     */
    private function createEventInstance($data)
    {
        $customerCreateEvent = new CustomerCreateOrUpdateEvent(
            isset($data["title"])?$data["title"]:null,
            isset($data["firstname"])?$data["firstname"]:null,
            isset($data["lastname"])?$data["lastname"]:null,
            isset($data["address1"])?$data["address1"]:null,
            isset($data["address2"])?$data["address2"]:null,
            isset($data["address3"])?$data["address3"]:null,
            isset($data["phone"])?$data["phone"]:null,
            isset($data["cellphone"])?$data["cellphone"]:null,
            isset($data["zipcode"])?$data["zipcode"]:null,
            isset($data["city"])?$data["city"]:null,
            isset($data["country"])?$data["country"]:null,
            isset($data["email"])?$data["email"]:null,
            isset($data["password"]) ? $data["password"]:null,
            $this->getRequest()->getSession()->getLang()->getId(),
            isset($data["reseller"])?$data["reseller"]:null,
            isset($data["sponsor"])?$data["sponsor"]:null,
            isset($data["discount"])?$data["discount"]:null,
            isset($data["company"])?$data["company"]:null,
            null
        );

        return $customerCreateEvent;
    }
}
