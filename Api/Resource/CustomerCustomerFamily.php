<?php

namespace CustomerFamily\Api\Resource;

use ApiPlatform\Metadata\Operation;
use CustomerFamily\Model\CustomerCustomerFamilyQuery;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\Customer as CustomerRessource;
use Thelia\Api\Resource\Order as OrderRessource;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\ResourceAddonInterface;
use Thelia\Api\Resource\ResourceAddonTrait;

class CustomerCustomerFamily implements ResourceAddonInterface
{
    use ResourceAddonTrait;

    public ?int $id = null;

    #[Groups([CustomerRessource::GROUP_ADMIN_READ, CustomerRessource::GROUP_ADMIN_WRITE, CustomerRessource::GROUP_FRONT_READ_SINGLE, OrderRessource::GROUP_ADMIN_READ])]
    public ?string $code = null;

    #[Ignore] public static function getResourceParent(): string
    {
        return \Thelia\Api\Resource\Customer::class;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new CustomerCustomerFamilyTableMap();
    }

    public static function extendQuery(ModelCriteria $query, Operation $operation = null, array $context = []): void
    {
        // TODO: Implement extendQuery() method.
    }

    /**
     * @throws PropelException
     */
    public function buildFromModel(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        if (null === $customerCustomerFamily = CustomerCustomerFamilyQuery::create()->filterByCustomerId($activeRecord->getId())->findOne()) {
            return $this;
        }

        $this->setCode(
            $activeRecord->hasVirtualColumn('CustomerCustomerFamily_code')
                ? $activeRecord->getVirtualColumn('CustomerCustomerFamily_code')
                : $customerCustomerFamily->getCustomerFamily()->getCode()
        );


        return $this;
    }

    public function buildFromArray(array $data, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        $this->setCode($data['code'] ?? null);

        return $this;
    }

    /**
     * @throws PropelException
     */
    public function doSave(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): void
    {
        $model = new \CustomerFamily\Model\CustomerCustomerFamily();

        if ($activeRecord->getCustomerCustomerFamily()) {
            $id = $activeRecord->getCustomerCustomerFamily()->getCustomerId();
            $model = CustomerCustomerFamilyQuery::create()->filterByCustomerId($id)->findOne();
        }

        $model->setCustomerId($activeRecord->getId());
        $model->setCustomerFamilyId(CustomerFamilyQuery::create()->findOneByCode($this->getCode())?->getId());
        $model->save();
    }

    public function doDelete(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): void
    {
        $activeRecord->getCustomerCustomerFamily()?->delete();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): CustomerCustomerFamily
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): CustomerCustomerFamily
    {
        $this->code = $code;
        return $this;
    }
}