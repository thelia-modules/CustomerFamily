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

namespace CustomerFamily\Event;

use CustomerFamily\Model\CustomerFamily;
use Symfony\Component\Form\Form;
use Thelia\Core\Event\ActionEvent;

/**
 * Class CustomerFamilyEvent
 * @package CustomerFamily\Event
 */
class CustomerFamilyEvent extends ActionEvent
{
    /** @var CustomerFamily */
    private $customerFamily;

    public function __construct(CustomerFamily $customerFamily = null)
    {
        if ($customerFamily !== null) {
            $this->customerFamily = $customerFamily;
        } else {
            $this->customerFamily = new CustomerFamily();
        }
    }

    /**
     * @param CustomerFamily $customerFamily
     */
    public function setCustomerFamily(CustomerFamily $customerFamily)
    {
        $this->customerFamily = $customerFamily;
    }

    /**
     * @return CustomerFamily
     */
    public function getCustomerFamily()
    {
        return $this->customerFamily;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->customerFamily->getId();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->customerFamily->getCode();
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->customerFamily->setCode($code);
    }

    /**
     * @param $locale
     * @return string
     */
    public function getTitle($locale = null)
    {
        if ($locale === null) {
            $locale = $this->customerFamily->getLocale();
        }

        $this->customerFamily->setLocale($locale);

        return $this->customerFamily->getTitle();
    }

    /**
     * @param $title
     * @param null $locale
     */
    public function setTitle($title, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->customerFamily->getLocale();
        }

        $this->customerFamily->setLocale($locale);
        $this->customerFamily->setTitle($title);
    }

    /**
     * @param Form $form
     */
    public function hydrateByForm(Form $form)
    {
        //code
        if ($form->get('code') !== null) {
            self::setCode($form->get('code')->getData());
        }

        //title
        if ($form->get('title') !== null && $form->get('locale') !== null) {
            self::setTitle($form->get('title')->getData(), $form->get('locale')->getData());
        }
    }
}
