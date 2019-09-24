<?php

namespace CustomerFamily\Controller\Admin;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Form\CustomerFamilyPriceModeForm;
use CustomerFamily\Model\CustomerFamilyPrice;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

/**
 * Class CustomerFamilyPriceController
 * @package CustomerFamily\Controller
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class CustomerFamilyPriceController extends BaseAdminController
{
    /**
     * Add or update amounts and factor to calculate prices for customer families
     *
     * @return mixed|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response|static
     */
    public function updateAction()
    {
        // Check rights
        if (null !== $response = $this->checkAuth(
                [AdminResources::MODULE],
                ['CustomerFamily'],
                [AccessManager::VIEW, AccessManager::CREATE, AccessManager::UPDATE]
            )) {
            return $response;
        }

        $form = $this->createForm('customer_family_price_update');
        $error = null;
        $ex = null;

        try {
            $vForm = $this->validateForm($form);

            // If no entry exists for the given CustomerFamilyId & promo, create it
            if (null === $customerFamilyPrice = CustomerFamilyPriceQuery::create()
                    ->findPk([$vForm->get('customer_family_id')->getData(), $vForm->get('promo')->getData()])) {
                // Create new CustomerFamilyPrice
                $customerFamilyPrice = new CustomerFamilyPrice();
                $customerFamilyPrice
                    ->setCustomerFamilyId($vForm->get('customer_family_id')->getData())
                    ->setPromo($vForm->get('promo')->getData());
            }

            // Save data
            $customerFamilyPrice
                ->setUseEquation($vForm->get('use_equation')->getData())
                ->setAmountAddedBefore($vForm->get('amount_added_before')->getData())
                ->setAmountAddedAfter($vForm->get('amount_added_after')->getData())
                ->setMultiplicationCoefficient($vForm->get('coefficient')->getData())
                ->setIsTaxed($vForm->get('is_taxed')->getData())
                ->save();

        } catch (FormValidationException $ex) {
            $error = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }

        if ($error !== null) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("CustomerFamily configuration", [], CustomerFamily::MODULE_DOMAIN),
                $error,
                $form,
                $ex
            );
            return $this->render('module-configure', ['module_code' => 'CustomerFamily']);
        }

        return RedirectResponse::create(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));
    }

    public function updatePriceModeAction()
    {
        $form = new CustomerFamilyPriceModeForm($this->getRequest());
        $vForm = $this->validateForm($form);

        $mode = $vForm->get('price_mode')->getData();

        CustomerFamily::setConfigValue('customer_family_price_mode', $mode);

        return RedirectResponse::create(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));
    }
}