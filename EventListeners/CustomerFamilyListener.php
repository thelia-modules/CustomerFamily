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

namespace CustomerFamily\EventListeners;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Event\CustomerCustomerFamilyEvent;
use CustomerFamily\Event\CustomerFamilyEvent;
use CustomerFamily\Event\CustomerFamilyEvents;
use CustomerFamily\Model\CustomerCustomerFamily;
use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Customer\CustomerCreateOrUpdateEvent;
use Thelia\Core\Event\Customer\CustomerEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Template\ParserInterface;
use Thelia\Mailer\MailerFactory;

/**
 * Class CustomerFamilyListener
 * @package CustomerFamily\EventListeners
 */
class CustomerFamilyListener implements EventSubscriberInterface
{
    const THELIA_CUSTOMER_CREATE_FORM_NAME = 'thelia_customer_create';
    const THELIA_CUSTOMER_UPDATE_FORM_NAME = 'thelia_customer_profile_update';

    /** @var \Thelia\Core\HttpFoundation\Request */
    protected $request;

    /** @var \Thelia\Core\Template\ParserInterface */
    protected $parser;

    /** @var \Thelia\Mailer\MailerFactory */
    protected $mailer;

    /**
     * @param Request $request
     * @param ParserInterface $parser
     * @param MailerFactory $mailer
     */
    public function __construct(Request $request, ParserInterface $parser, MailerFactory $mailer)
    {
        $this->request = $request;
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::AFTER_CREATECUSTOMER => array(
                'afterCreateCustomer', 100
            ),
            TheliaEvents::CUSTOMER_UPDATEPROFILE => array(
                'customerUpdateProfile', 100
            ),
            CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE => array(
                "customerCustomerFamilyUpdate", 128
            ),
            CustomerFamilyEvents::CUSTOMER_FAMILY_CREATE => array(
                'create', 128
            ),
            CustomerFamilyEvents::CUSTOMER_FAMILY_UPDATE => array(
                'update', 128
            ),
            CustomerFamilyEvents::CUSTOMER_FAMILY_DELETE => array(
                'delete', 128
            ),
        );
    }

    /**
     * @param CustomerFamilyEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function create(CustomerFamilyEvent $event)
    {
        if (CustomerFamilyQuery::create()
            ->filterByCode($event->getCode())
            ->findOne() !== null
        ) {
            throw new \Exception("Customer family code is already in use");
        }

        $event->getCustomerFamily()->save();
    }

    /**
     * @param CustomerFamilyEvent $event
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function update(CustomerFamilyEvent $event)
    {
        if (CustomerFamilyQuery::create()
                ->filterByCode($event->getCode())
                ->filterById($event->getId(), Criteria::NOT_EQUAL)
                ->findOne() !== null
        ) {
            throw new \Exception("Customer family code is already in use");
        }

        $event->getCustomerFamily()->save();
    }

    /**
     * @param CustomerFamilyEvent $event
     */
    public function delete(CustomerFamilyEvent $event)
    {
        $event->getCustomerFamily()->delete();
    }

    /**
     * @param CustomerEvent $event
     */
    public function afterCreateCustomer(CustomerEvent $event)
    {
        $form = $this->request->request->get(self::THELIA_CUSTOMER_CREATE_FORM_NAME);

        if (is_null($form) or !array_key_exists(CustomerFamilyFormListener::CUSTOMER_FAMILY_CODE_FIELD_NAME, $form)) {
            // Nothing to create the new CustomerCustomerFamily => stop here !
            return;
        }

        $customerFamily = CustomerFamilyQuery::create()->findOneByCode($form[CustomerFamilyFormListener::CUSTOMER_FAMILY_CODE_FIELD_NAME]);

        if (is_null($customerFamily)) {
            // No family => no CustomerCustomerFamily to update.
            return;
        }

        $customerFamilyId = $customerFamily->getId();

        // Ignore SIRET and VAT if the customer is not professional
        $siret = $customerFamily->getCode() == CustomerFamily::CUSTOMER_FAMILY_PROFESSIONAL ? $form[CustomerFamilyFormListener::CUSTOMER_FAMILY_SIRET_FIELD_NAME] : '';
        $vat = $customerFamily->getCode() == CustomerFamily::CUSTOMER_FAMILY_PROFESSIONAL ? $form[CustomerFamilyFormListener::CUSTOMER_FAMILY_VAT_FIELD_NAME] : '';

        $updateEvent = new CustomerCustomerFamilyEvent($event->getCustomer()->getId());
        $updateEvent
            ->setCustomerFamilyId($customerFamilyId)
            ->setSiret($siret)
            ->setVat($vat)
        ;

        $event->getDispatcher()->dispatch(CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE, $updateEvent);
    }

    /**
     * @param CustomerCreateOrUpdateEvent $event
     */
    public function customerUpdateProfile(CustomerCreateOrUpdateEvent $event)
    {
        $form = $this->request->request->get(self::THELIA_CUSTOMER_UPDATE_FORM_NAME);

        if (is_null($form) or !array_key_exists(CustomerFamilyFormListener::CUSTOMER_FAMILY_CODE_FIELD_NAME, $form)) {
            // Nothing to update => stop here !
            return;
        }

        // Erase SIRET and VAT if the customer is now in the 'particular' customer family.
        if ($form[CustomerFamilyFormListener::CUSTOMER_FAMILY_CODE_FIELD_NAME] == CustomerFamily::CUSTOMER_FAMILY_PARTICULAR) {
            $siret = '';
            $vat = '';
        } else {
            $siret = $form[CustomerFamilyFormListener::CUSTOMER_FAMILY_SIRET_FIELD_NAME];
            $vat = $form[CustomerFamilyFormListener::CUSTOMER_FAMILY_VAT_FIELD_NAME];
        }

        $newCustomerFamily = CustomerFamilyQuery::create()->findOneByCode($form[CustomerFamilyFormListener::CUSTOMER_FAMILY_CODE_FIELD_NAME]);


        $updateEvent = new CustomerCustomerFamilyEvent($event->getCustomer()->getId());
        $updateEvent
            ->setCustomerFamilyId($newCustomerFamily->getId())
            ->setSiret($siret)
            ->setVat($vat)
        ;

        $event->getDispatcher()->dispatch(CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE, $updateEvent);
    }

    /**
     * @param CustomerCustomerFamilyEvent $event
     */
    public function customerCustomerFamilyUpdate(CustomerCustomerFamilyEvent $event)
    {
        $customerCustomerFamily = CustomerCustomerFamilyQuery::create()->findOneByCustomerId($event->getCustomerId());

        if ($customerCustomerFamily === null) {
            $customerCustomerFamily = new CustomerCustomerFamily();
            $customerCustomerFamily
                ->setCustomerId($event->getCustomerId())
            ;
        }

        $customerCustomerFamily
            ->setCustomerFamilyId($event->getCustomerFamilyId())
            ->setSiret($event->getSiret())
            ->setVat($event->getVat())
            ->save()
        ;
    }

    /**
     * @return mixed|null
     */
    protected function getCustomerFamilyForm()
    {
        if (null != $form = $this->request->request->get("customer_family_customer_profile_update_form")) {
            return $form;
        }

        if (null != $form = $this->request->request->get(self::THELIA_CUSTOMER_CREATE_FORM_NAME)) {
            return $form;
        }

        return null;
    }
}
