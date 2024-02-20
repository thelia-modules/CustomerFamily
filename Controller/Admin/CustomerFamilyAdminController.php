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
use CustomerFamily\Form\CustomerFamilyUpdateDefaultForm;
use CustomerFamily\Form\CustomerFamilyUpdateForm;
use CustomerFamily\Model\CustomerFamilyAvailableBrand;
use CustomerFamily\Model\CustomerFamilyAvailableCategory;
use CustomerFamily\Model\CustomerFamilyQuery;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Form\CustomerUpdateForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Customer;
use Thelia\Model\CustomerQuery;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/CustomerFamily", name="customer_family")
 * Class CustomerFamilyAdminController
 * @package CustomerFamily\Controller\Admin
 */
class CustomerFamilyAdminController extends BaseAdminController
{
    /**
     * @Route("", name="_view", methods="GET")
     */
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
     * @Route("/create", name="_create", methods="POST")
     */
    public function createAction(EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::CREATE)) {
            return $response;
        }

        $error = "";
        $form = $this->createForm(CustomerFamilyCreateForm::getName());

        try {
            $formValidate = $this->validateForm($form);

            $event = new CustomerFamilyEvent();
            $event->hydrateByForm($formValidate);

            $eventDispatcher->dispatch($event, CustomerFamilyEvents::CUSTOMER_FAMILY_CREATE);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was created successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return $this->renderAdminConfig($form, $message, $error, $parserContext, $requestStack->getCurrentRequest()->getSession());
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     * @Route("/update/{id}", name="_update", methods="POST")
     */
    public function updateAction($id, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = $this->createForm(CustomerFamilyUpdateForm::getName());

        try {
            $formValidate = $this->validateForm($form);

            $customerFamily = CustomerFamilyQuery::create()->findPk($id);

            if ($customerFamily === null) {
                throw new \Exception("Customer Family not found by Id");
            }

            $event = new CustomerFamilyEvent($customerFamily);
            $event->hydrateByForm($formValidate);

            $eventDispatcher->dispatch($event, CustomerFamilyEvents::CUSTOMER_FAMILY_UPDATE);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was updated successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return $this->renderAdminConfig($form, $message, $error, $parserContext, $requestStack->getCurrentRequest()->getSession());
    }

    /**
     * Update default family
     * There must be at least one default family
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response|static
     * @Route("/update-default", name="_update-default", methods="POST")
     */
    public function updateDefaultAction(Translator $translator)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['CustomerFamily'], AccessManager::UPDATE)) {
            return $response;
        }

        $error = null;
        $ex = null;
        $form = $this->createForm(CustomerFamilyUpdateDefaultForm::getName());

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
                $translator->trans("Error updating default family", [], CustomerFamily::MODULE_DOMAIN),
                $error,
                $form,
                $ex
            );
            return new JsonResponse(['error'=>$error], 500);
        }

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));

    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     * @Route("/delete/{id}", name="_delete", methods="POST")
     */
    public function deleteAction($id, EventDispatcherInterface $eventDispatcher, RequestStack $requestStack, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::DELETE)) {
            return $response;
        }

        $error = "";
        $form = $this->createForm(CustomerFamilyDeleteForm::getName());

        try {
            $formValidate = $this->validateForm($form);

            $customerFamily = CustomerFamilyQuery::create()->findPk($id);

            if ($customerFamily === null) {
                throw new \Exception("Customer Family not found by Id");
            }

            $event = new CustomerFamilyEvent($customerFamily);

            $eventDispatcher->dispatch($event, CustomerFamilyEvents::CUSTOMER_FAMILY_DELETE);

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $message = Translator::getInstance()->trans(
            "Customer family was deleted successfully",
            array(),
            CustomerFamily::MODULE_DOMAIN
        );

        return $this->renderAdminConfig($form, $message, $error, $parserContext, $requestStack->getCurrentRequest()->getSession());
    }

    /**
     * @param BaseForm $form
     * @param string $successMessage
     * @param string $errorMessage
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    protected function renderAdminConfig($form, $successMessage, $errorMessage, ParserContext $parserContext, SessionInterface $session)
    {
        if (!empty($errorMessage)) {
            $form->setErrorMessage($errorMessage);

            $parserContext
                ->addForm($form)
                ->setGeneralError($errorMessage);
        }

        //for compatibility 2.0
        if (method_exists($session, "getFlashBag")) {
            if (empty($errorMessage)) {
                $session->getFlashBag()->add("success", $successMessage);
            } else {
                $session->getFlashBag()->add("danger", $errorMessage);
            }
        }

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily")
        );
    }

    /**
     * @param Request $request
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     * @Route("/customer/update", name="_customer_update", methods="POST")
     */
    public function customerUpdateAction(RequestStack $requestStack, ParserContext $parserContext, EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('CustomerFamily'), AccessManager::UPDATE)) {
            return $response;
        }

        $error = "";
        $form = $this->createForm(CustomerCustomerFamilyForm::getName());
        try {
            $formValidate = $this->validateForm($form);
            $event = new CustomerCustomerFamilyEvent($formValidate->get('customer_id')->getData());
            $event
                ->setCustomerFamilyId($formValidate->get('customer_family_id')->getData())
            ;

            $eventDispatcher->dispatch($event, CustomerFamilyEvents::CUSTOMER_CUSTOMER_FAMILY_UPDATE);

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

        $parserContext
            ->addForm($form)
            ->setGeneralError($error);

        //Don't forget to fill the Customer form
        $customerId = $requestStack->getCurrentRequest()->get('customer_customer_family_form')['customer_id'];
        if (null != $customer = CustomerQuery::create()->findPk($customerId)) {
            $customerForm = $this->hydrateCustomerForm($customer);
            $parserContext->addForm($customerForm);
        }

        return $this->render('customer-edit', array(
                'customer_id' => $requestStack->getCurrentRequest()->get('customer_customer_family_form')['customer_id'],
                "order_creation_error" => Translator::getInstance()->trans($error, array(), CustomerFamily::MESSAGE_DOMAIN)
            ));
    }

    /**
     * @Route("/category_restriction/{customerFamilyId}", name="_category_restriction", methods="POST")
     */
    public function saveCustomerFamilyCategoryRestriction($customerFamilyId, ParserContext $parserContext, RequestStack $requestStack)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->findOneById($customerFamilyId);

        $request = $requestStack->getCurrentRequest();
        $restrictionEnabled = $request->get('restriction_enabled') === "on";
        $customerFamily->setCategoryRestrictionEnabled($restrictionEnabled);
        $customerFamily->save();

        $con = Propel::getConnection();
        $deleteSql =  "DELETE FROM `customer_family_available_category` WHERE `customer_family_available_category`.`customer_family_id` = :customerFamilyId";
        $stmt = $con->prepare($deleteSql);
        $stmt->bindValue('customerFamilyId', $customerFamilyId);
        $stmt->execute();

        $brands = $request->get('available_categories');
        if (is_array($brands)) {
            foreach ($request->get('available_categories') as $availableCategoryId) {
                var_dump($customerFamilyId);
                var_dump($availableCategoryId);
                $customerFamilyAvailableCategory = new CustomerFamilyAvailableCategory();
                $customerFamilyAvailableCategory->setCustomerFamilyId($customerFamilyId)
                    ->setCategoryId($availableCategoryId)
                    ->save();
            }
        }

        return $this->renderAdminConfig(null, "", "", $parserContext, $request->getSession());
    }

    /**
     * @Route("/brand_restriction/{customerFamilyId}", name="_brand_restriction", methods="POST")
     */
    public function saveCustomerFamilyBrandRestriction($customerFamilyId, ParserContext $parserContext, RequestStack $requestStack)
    {
        $customerFamily = CustomerFamilyQuery::create()
            ->findOneById($customerFamilyId);

        $request = $requestStack->getCurrentRequest();
        $restrictionEnabled = $request->get('restriction_enabled') === "on";
        $customerFamily->setBrandRestrictionEnabled($restrictionEnabled);
        $customerFamily->save();

        $con = Propel::getConnection();
        $deleteSql =  "DELETE FROM `customer_family_available_brand` WHERE `customer_family_available_brand`.`customer_family_id` = :customerFamilyId";
        $stmt = $con->prepare($deleteSql);
        $stmt->bindValue('customerFamilyId', $customerFamilyId);
        $stmt->execute();

        $categories = $request->get('available_categories');
        if (is_array($categories)) {
            foreach ($request->get('available_categories') as $availableBrandId) {
                var_dump($customerFamilyId);
                var_dump($availableBrandId);
                $customerFamilyAvailableBrand = new CustomerFamilyAvailableBrand();
                $customerFamilyAvailableBrand->setCustomerFamilyId($customerFamilyId)
                    ->setBrandId($availableBrandId)
                    ->save();
            }
        }

        return $this->renderAdminConfig(null, "", "", $parserContext, $request->getSession());
    }

    /**
     * @param Customer $customer
     * @return BaseForm
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
        return $this->createForm(CustomerUpdateForm::getName(), FormType::class, $data);
    }
}
