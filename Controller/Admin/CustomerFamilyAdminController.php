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

namespace CustomerFamily\Controller\Admin;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Event\CustomerCustomerFamilyEvent;
use CustomerFamily\Event\CustomerFamilyEvent;
use CustomerFamily\Event\CustomerFamilyEvents;
use CustomerFamily\Form\CustomerCustomerFamilyForm;
use CustomerFamily\Form\CustomerFamilyCreateForm;
use CustomerFamily\Form\CustomerFamilyDeleteForm;
use CustomerFamily\Form\CustomerFamilyUpdateForm;
use CustomerFamily\Model\CustomerFamilyQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Form\CustomerUpdateForm;
use Thelia\Model\Base\CustomerQuery;
use Thelia\Model\Customer;
use Thelia\Tools\URL;

/**
 * Class CustomerFamilyAdminController
 * @package CustomerFamily\Controller\Admin
 */
class CustomerFamilyAdminController extends BaseAdminController
{

    public function createAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::CREATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyCreateForm($this->getRequest());

        try {
            $formValidate = $this->validateForm($form);

            $event = new CustomerFamilyEvent();
            $event->hydrateByForm($formValidate);

            $this->dispatch(CustomerFamilyEvents::CUSTOMER_FAMILY_CREATE, $event);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was created successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return self::renderAdminConfig($form, $message, $error);
    }

    public function updateAction($id)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyUpdateForm($this->getRequest());

        try {
            $formValidate = $this->validateForm($form);

            $customerFamily = CustomerFamilyQuery::create()->findPk($id);

            if ($customerFamily === null) {
                throw new \Exception("Customer Family not found by Id");
            }

            $event = new CustomerFamilyEvent($customerFamily);
            $event->hydrateByForm($formValidate);

            $this->dispatch(CustomerFamilyEvents::CUSTOMER_FAMILY_UPDATE, $event);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was updated successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return self::renderAdminConfig($form, $message, $error);
    }

    public function deleteAction($id)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::DELETE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyDeleteForm($this->getRequest());

        try {
            $formValidate = $this->validateForm($form);

            $customerFamily = CustomerFamilyQuery::create()->findPk($id);

            if ($customerFamily === null) {
                throw new \Exception("Customer Family not found by Id");
            }

            $event = new CustomerFamilyEvent($customerFamily);

            $this->dispatch(CustomerFamilyEvents::CUSTOMER_FAMILY_DELETE, $event);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was deleted successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return self::renderAdminConfig($form, $message, $error);
    }

    /**
     * @param BaseForm $form
     * @param string $successMessage
     * @param string $errorMessage
     * @return \Thelia\Core\HttpFoundation\Response
     */
    protected function renderAdminConfig($form, $successMessage, $errorMessage)
    {
        if (!empty($errorMessage)) {
            $form->setErrorMessage($errorMessage);

            $this->getParserContext()
                ->addForm($form)
                ->setGeneralError($errorMessage);
        }

        //for compatibility 2.0
        if (method_exists($this->getSession(), "getFlashBag")) {
            if (empty($errorMessage)) {
                $this->getSession()->getFlashBag()->add("success", $successMessage);
            } else {
                $this->getSession()->getFlashBag()->add("danger", $errorMessage);
            }
        }

        return RedirectResponse::create(
            URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily")
        );
    }

    /**
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function customerUpdateAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerCustomerFamilyForm($this->getRequest());
        try {
            $formValidate = $this->validateForm($form);
            $event = new CustomerCustomerFamilyEvent($formValidate->get('customer_id')->getData());
            $event
                ->setCustomerFamilyId($formValidate->get('customer_family_id')->getData())
                ->setSiret($formValidate->get('siret')->getData())
                ->setVat($formValidate->get('vat')->getData())
            ;

            $this->dispatch(CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE, $event);

            $this->redirect(URL::getInstance()->absoluteUrl(
                '/admin/customer/update?customer_id='.$formValidate->get('customer_id')->getData()
            ));

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $form->setErrorMessage($error);

        $this->getParserContext()
            ->addForm($form)
            ->setGeneralError($error);

        //Don't forget to fill the Customer form
        $customerId = $this->getRequest()->request->get('customer_customer_family_form')['customer_id'];
        if (null != $customer = CustomerQuery::create()->findPk($customerId)) {
            $customerForm = $this->hydrateCustomerForm($customer);
            $this->getParserContext()->addForm($customerForm);
        }

        return $this->render('customer-edit', array(
                'customer_id' => $this->getRequest()->request->get('customer_customer_family_form')['customer_id'],
                "order_creation_error" => Translator::getInstance()->trans($error, array(), CustomerFamily::MESSAGE_DOMAIN)
            ));
    }

    /**
     * @param Customer $customer
     * @return CustomerUpdateForm
     */
    protected function hydrateCustomerForm(Customer $customer)
    {
        // Get default adress of the customer
        $address = $customer->getDefaultAddress();

        // Prepare the data that will hydrate the form
        $data = array(
            'id'        => $customer->getId(),
            'firstname' => $customer->getFirstname(),
            'lastname'  => $customer->getLastname(),
            'email'     => $customer->getEmail(),
            'title'     => $customer->getTitleId(),
            'discount'  => $customer->getDiscount(),
            'reseller'  => $customer->getReseller(),
        );

        if ($address !== null) {
            $data['company']   = $address->getCompany();
            $data['address1']  = $address->getAddress1();
            $data['address2']  = $address->getAddress2();
            $data['address3']  = $address->getAddress3();
            $data['phone']     = $address->getPhone();
            $data['cellphone'] = $address->getCellphone();
            $data['zipcode']   = $address->getZipcode();
            $data['city']      = $address->getCity();
            $data['country']   = $address->getCountryId();
        }

        // A loop is used in the template
        return new CustomerUpdateForm($this->getRequest(), 'form', $data);
    }
}
