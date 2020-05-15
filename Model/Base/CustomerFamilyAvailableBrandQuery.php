<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\CustomerFamilyAvailableBrand as ChildCustomerFamilyAvailableBrand;
use CustomerFamily\Model\CustomerFamilyAvailableBrandQuery as ChildCustomerFamilyAvailableBrandQuery;
use CustomerFamily\Model\Map\CustomerFamilyAvailableBrandTableMap;
use CustomerFamily\Model\Thelia\Model\Brand;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_family_available_brand' table.
 *
 *
 *
 * @method     ChildCustomerFamilyAvailableBrandQuery orderByCustomerFamilyId($order = Criteria::ASC) Order by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableBrandQuery orderByBrandId($order = Criteria::ASC) Order by the brand_id column
 *
 * @method     ChildCustomerFamilyAvailableBrandQuery groupByCustomerFamilyId() Group by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableBrandQuery groupByBrandId() Group by the brand_id column
 *
 * @method     ChildCustomerFamilyAvailableBrandQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerFamilyAvailableBrandQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerFamilyAvailableBrandQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerFamilyAvailableBrandQuery leftJoinCustomerFamily($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyAvailableBrandQuery rightJoinCustomerFamily($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyAvailableBrandQuery innerJoinCustomerFamily($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerFamily relation
 *
 * @method     ChildCustomerFamilyAvailableBrandQuery leftJoinBrand($relationAlias = null) Adds a LEFT JOIN clause to the query using the Brand relation
 * @method     ChildCustomerFamilyAvailableBrandQuery rightJoinBrand($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Brand relation
 * @method     ChildCustomerFamilyAvailableBrandQuery innerJoinBrand($relationAlias = null) Adds a INNER JOIN clause to the query using the Brand relation
 *
 * @method     ChildCustomerFamilyAvailableBrand findOne(ConnectionInterface $con = null) Return the first ChildCustomerFamilyAvailableBrand matching the query
 * @method     ChildCustomerFamilyAvailableBrand findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerFamilyAvailableBrand matching the query, or a new ChildCustomerFamilyAvailableBrand object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerFamilyAvailableBrand findOneByCustomerFamilyId(int $customer_family_id) Return the first ChildCustomerFamilyAvailableBrand filtered by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableBrand findOneByBrandId(int $brand_id) Return the first ChildCustomerFamilyAvailableBrand filtered by the brand_id column
 *
 * @method     array findByCustomerFamilyId(int $customer_family_id) Return ChildCustomerFamilyAvailableBrand objects filtered by the customer_family_id column
 * @method     array findByBrandId(int $brand_id) Return ChildCustomerFamilyAvailableBrand objects filtered by the brand_id column
 *
 */
abstract class CustomerFamilyAvailableBrandQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\CustomerFamilyAvailableBrandQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\CustomerFamilyAvailableBrand', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerFamilyAvailableBrandQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerFamilyAvailableBrandQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\CustomerFamilyAvailableBrandQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\CustomerFamilyAvailableBrandQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$customer_family_id, $brand_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCustomerFamilyAvailableBrand|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerFamilyAvailableBrandTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerFamilyAvailableBrandTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildCustomerFamilyAvailableBrand A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CUSTOMER_FAMILY_ID, BRAND_ID FROM customer_family_available_brand WHERE CUSTOMER_FAMILY_ID = :p0 AND BRAND_ID = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildCustomerFamilyAvailableBrand();
            $obj->hydrate($row);
            CustomerFamilyAvailableBrandTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCustomerFamilyAvailableBrand|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the customer_family_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerFamilyId(1234); // WHERE customer_family_id = 1234
     * $query->filterByCustomerFamilyId(array(12, 34)); // WHERE customer_family_id IN (12, 34)
     * $query->filterByCustomerFamilyId(array('min' => 12)); // WHERE customer_family_id > 12
     * </code>
     *
     * @see       filterByCustomerFamily()
     *
     * @param     mixed $customerFamilyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByCustomerFamilyId($customerFamilyId = null, $comparison = null)
    {
        if (is_array($customerFamilyId)) {
            $useMinMax = false;
            if (isset($customerFamilyId['min'])) {
                $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerFamilyId['max'])) {
                $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId, $comparison);
    }

    /**
     * Filter the query on the brand_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBrandId(1234); // WHERE brand_id = 1234
     * $query->filterByBrandId(array(12, 34)); // WHERE brand_id IN (12, 34)
     * $query->filterByBrandId(array('min' => 12)); // WHERE brand_id > 12
     * </code>
     *
     * @see       filterByBrand()
     *
     * @param     mixed $brandId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByBrandId($brandId = null, $comparison = null)
    {
        if (is_array($brandId)) {
            $useMinMax = false;
            if (isset($brandId['min'])) {
                $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $brandId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($brandId['max'])) {
                $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $brandId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $brandId, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\CustomerFamily object
     *
     * @param \CustomerFamily\Model\CustomerFamily|ObjectCollection $customerFamily The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByCustomerFamily($customerFamily, $comparison = null)
    {
        if ($customerFamily instanceof \CustomerFamily\Model\CustomerFamily) {
            return $this
                ->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $customerFamily->getId(), $comparison);
        } elseif ($customerFamily instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID, $customerFamily->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomerFamily() only accepts arguments of type \CustomerFamily\Model\CustomerFamily or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CustomerFamily relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function joinCustomerFamily($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CustomerFamily');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CustomerFamily');
        }

        return $this;
    }

    /**
     * Use the CustomerFamily relation CustomerFamily object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\CustomerFamilyQuery A secondary query class using the current class as primary query
     */
    public function useCustomerFamilyQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomerFamily($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CustomerFamily', '\CustomerFamily\Model\CustomerFamilyQuery');
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\Brand object
     *
     * @param \CustomerFamily\Model\Thelia\Model\Brand|ObjectCollection $brand The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function filterByBrand($brand, $comparison = null)
    {
        if ($brand instanceof \CustomerFamily\Model\Thelia\Model\Brand) {
            return $this
                ->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $brand->getId(), $comparison);
        } elseif ($brand instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyAvailableBrandTableMap::BRAND_ID, $brand->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByBrand() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\Brand or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Brand relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function joinBrand($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Brand');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Brand');
        }

        return $this;
    }

    /**
     * Use the Brand relation Brand object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\BrandQuery A secondary query class using the current class as primary query
     */
    public function useBrandQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBrand($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Brand', '\CustomerFamily\Model\Thelia\Model\BrandQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCustomerFamilyAvailableBrand $customerFamilyAvailableBrand Object to remove from the list of results
     *
     * @return ChildCustomerFamilyAvailableBrandQuery The current query, for fluid interface
     */
    public function prune($customerFamilyAvailableBrand = null)
    {
        if ($customerFamilyAvailableBrand) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CustomerFamilyAvailableBrandTableMap::CUSTOMER_FAMILY_ID), $customerFamilyAvailableBrand->getCustomerFamilyId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CustomerFamilyAvailableBrandTableMap::BRAND_ID), $customerFamilyAvailableBrand->getBrandId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_family_available_brand table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyAvailableBrandTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CustomerFamilyAvailableBrandTableMap::clearInstancePool();
            CustomerFamilyAvailableBrandTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerFamilyAvailableBrand or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerFamilyAvailableBrand object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyAvailableBrandTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerFamilyAvailableBrandTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerFamilyAvailableBrandTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerFamilyAvailableBrandTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CustomerFamilyAvailableBrandQuery
