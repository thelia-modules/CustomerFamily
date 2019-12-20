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
use CustomerFamily\Model\CustomerFamilyAvailableBrand;
use CustomerFamily\Model\CustomerFamilyAvailableCategory;
use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Form\CustomerUpdateForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Customer;
use Thelia\Model\CustomerQuery;
use Thelia\Tools\URL;

/**
 * Class CustomerFamilyAdminController
 * @package CustomerFamily\Controller\Admin
 */
class CustomerFamilyAdminController extends BaseAdminController
{
    public function viewAction($params = [])
    {
        $categoryRestrictions = [];
        $brandRestrictions = [];

        $customerFamilies = CustomerFamilyQuery::create()
            ->find();

        $con = Propel::getConnection();

        /** @var CustomerFamily $customerFamily */
        foreach ($customerFamilies as $customerFamily) {
            $categoryRestrictionSql = "SELECT c.id, c.parent, ci18n.title, cfac.`customer_family_id`,
                            CASE 
                              WHEN cfac.`customer_family_id` IS NOT NULL THEN 1
                              ELSE 0
                            END as available 
                            FROM category c
                            LEFT JOIN `category_i18n` ci18n on c.`id` = ci18n.id AND ci18n.locale = :locale
                            LEFT JOIN `customer_family_available_category` cfac ON c.`id` = cfac.`category_id` AND cfac.`customer_family_id` = :customerFamilyId";

            $stmt = $con->prepare($categoryRestrictionSql);
            $stmt->bindValue('locale', $this->getCurrentEditionLocale());
            $stmt->bindValue('customerFamilyId', $customerFamily->getId());
            $stmt->execute();

            $categoryRestrictions[$customerFamily->getId()] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $brandRestrictionSql = "SELECT b.id, bi18n.title, cfab.`customer_family_id`,
                            CASE 
                              WHEN cfab.`customer_family_id` IS NOT NULL THEN 1
                              ELSE 0
                            END as available 
                            FROM brand b
                            LEFT JOIN `brand_i18n` bi18n on b.`id` = bi18n.id AND bi18n.locale = :locale
                            LEFT JOIN `customer_family_available_brand` cfab ON b.`id` = cfab.`brand_id` AND cfab.`customer_family_id` = :customerFamilyId";

            $stmt = $con->prepare($brandRestrictionSql);
            $stmt->bindValue('locale', $this->getCurrentEditionLocale());
            $stmt->bindValue('customerFamilyId', $customerFamily->getId());
            $stmt->execute();

            $brandRestrictions[$customerFamily->getId()] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $this->render("customer_family_module_configuration", compact('categoryRestrictions', 'brandRestrictions'));
    }

    /**
     * @param Request $request
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::CREATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyCreateForm($request);

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

    /**
     * @param Request $request
     * @param $id
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyUpdateForm($request);

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

    /**
     * Update default family
     * There must be at least one default family
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response|static
     */
    public function updateDefaultAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['CustomerFamily'], AccessManager::UPDATE)) {
            return $response;
        }

        $error = null;
        $ex = null;
        $form = $this->createForm('customer_family_update_default_form');

        try {
            $vForm = $this->validateForm($form);

            // Get customer_family to update
            $customerFamily = CustomerFamilyQuery::create()->findOneById($vForm->get('customer_family_id')->getData());

            // If the customer_family exists
            if (null !== $customerFamily) {
                // If the family to update is not already the default one
                if (!$customerFamily->getIsDefault()) {
                    // Remove old default family
                    if (null !== $defaultCustomerFamilies = CustomerFamilyQuery::create()->findByIsDefault(1)) {
                        /** @var \CustomerFamily\Model\CustomerFamily $defaultCustomerFamily */
                        foreach ($defaultCustomerFamilies as $defaultCustomerFamily) {
                            $defaultCustomerFamily
                                ->setIsDefault(0)
                                ->save();
                        }
                    }
                    // Save new default family
                    $customerFamily
                        ->setIsDefault(1)
                        ->save();
                }
            }

        } catch (FormValidationException $ex) {
            $error = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }

        if ($error !== null) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Error updating default family", [], CustomerFamily::MODULE_DOMAIN),
                $error,
                $form,
                $ex
            );
            return JsonResponse::create(['error'=>$error], 500);
        }

        return RedirectResponse::create(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));

    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::DELETE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerFamilyDeleteForm($request);

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
     * @return \Symfony\Component\HttpFoundation\Response|static
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
     * @param Request $request
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function customerUpdateAction(Request $request)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = new CustomerCustomerFamilyForm($request);
        try {
            $formValidate = $this->validateForm($form);
            $event = new CustomerCustomerFamilyEvent($formValidate->get('customer_id')->getData());
            $event
                ->setCustomerFamilyId($formValidate->get('customer_family_id')->getData())
                ->setSiret($formValidate->get('siret')->getData())
                ->setVat($formValidate->get('vat')->getData())
            ;

            $this->dispatch(CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE, $event);

            return $this->generateRedirect(URL::getInstance()->absoluteUrl(
                '/admin/customer/update?customer_id='.$formValidate->get('customer_id')->getData()
            ));
        } catch (FormValidationException $ex) {
            $error = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!empty($error)) {
            $form->setErrorMessage($error);
        }

        $this->getParserContext()
            ->addForm($form)
            ->setGeneralError($error);

        //Don't forget to fill the Customer form
        $customerId = $request->get('customer_customer_family_form')['customer_id'];
        if (null != $customer = CustomerQuery::create()->findPk($customerId)) {
            $customerForm = $this->hydrateCustomerForm($customer);
            $this->getParserContext()->addForm($customerForm);
        }

        return $this->render('customer-edit', array(
                'customer_id' => $request->get('customer_customer_family_form')['customer_id'],
                "order_creation_error" => Translator::getInstance()->trans($error, array(), CustomerFamily::MESSAGE_DOMAIN)
            ));
    }

    public function saveCustomerFamilyCategoryRestriction($customerFamilyId)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->findOneById($customerFamilyId);

        $restrictionEnabled = $this->getRequest()->get('restriction_enabled') === "on";
        $customerFamily->setCategoryRestrictionEnabled($restrictionEnabled);
        $customerFamily->save();

        $con = Propel::getConnection();
        $deleteSql =  "DELETE FROM `customer_family_available_category` WHERE `customer_family_available_category`.`customer_family_id` = :customerFamilyId";
        $stmt = $con->prepare($deleteSql);
        $stmt->bindValue('customerFamilyId', $customerFamilyId);
        $stmt->execute();

        $brands = $this->getRequest()->get('available_categories');
        if (is_array($brands)) {
            foreach ($this->getRequest()->get('available_categories') as $availableCategoryId) {
                var_dump($customerFamilyId);
                var_dump($availableCategoryId);
                $customerFamilyAvailableCategory = new CustomerFamilyAvailableCategory();
                $customerFamilyAvailableCategory->setCustomerFamilyId($customerFamilyId)
                    ->setCategoryId($availableCategoryId)
                    ->save();
            }
        }

        return $this->renderAdminConfig(null, "", "");
    }

    public function saveCustomerFamilyBrandRestriction($customerFamilyId)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->findOneById($customerFamilyId);

        $restrictionEnabled = $this->getRequest()->get('restriction_enabled') === "on";
        $customerFamily->setBrandRestrictionEnabled($restrictionEnabled);
        $customerFamily->save();

        $con = Propel::getConnection();
        $deleteSql =  "DELETE FROM `customer_family_available_brand` WHERE `customer_family_available_brand`.`customer_family_id` = :customerFamilyId";
        $stmt = $con->prepare($deleteSql);
        $stmt->bindValue('customerFamilyId', $customerFamilyId);
        $stmt->execute();

        $categories = $this->getRequest()->get('available_categories');
        if (is_array($categories)) {
            foreach ($this->getRequest()->get('available_categories') as $availableBrandId) {
                var_dump($customerFamilyId);
                var_dump($availableBrandId);
                $customerFamilyAvailableBrand = new CustomerFamilyAvailableBrand();
                $customerFamilyAvailableBrand->setCustomerFamilyId($customerFamilyId)
                    ->setBrandId($availableBrandId)
                    ->save();
            }
        }

        return $this->renderAdminConfig(null, "", "");
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
