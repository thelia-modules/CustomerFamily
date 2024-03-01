<?php

namespace CustomerFamily\Controller\Admin;

use CustomerFamily\CustomerFamily;
use CustomerFamily\Form\CustomerFamilyPriceForm;
use CustomerFamily\Form\CustomerFamilyPriceModeForm;
use CustomerFamily\Model\CustomerFamilyPrice;
use CustomerFamily\Model\CustomerFamilyPriceQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="customer_family")
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
     * @Route("/module/CustomerFamily/update-price-calculation", name="_update_price_calculation", methods="POST")
     */
    public function updateAction(Translator $translator)
    {
        // Check rights
        if (null !== $response = $this->checkAuth(
                [AdminResources::MODULE],
                ['CustomerFamily'],
                [AccessManager::VIEW, AccessManager::CREATE, AccessManager::UPDATE]
            )) {
            return $response;
        }

        $form = $this->createForm(CustomerFamilyPriceForm::getName());
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
                $translator->trans("CustomerFamily configuration", [], CustomerFamily::MODULE_DOMAIN),
                $error,
                $form,
                $ex
            );
            return $this->render('module-configure', ['module_code' => 'CustomerFamily']);
        }

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));
    }

    /**
     * @Route("/CustomerFamily/selectPriceMode", name="_update_price", methods="POST")
     */
    public function updatePriceModeAction()
    {
        $form = $this->createForm(CustomerFamilyPriceModeForm::getName());
        $vForm = $this->validateForm($form);

        $mode = $vForm->get('price_mode')->getData();

        CustomerFamily::setConfigValue('customer_family_price_mode', $mode);

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/CustomerFamily"));
    }
}