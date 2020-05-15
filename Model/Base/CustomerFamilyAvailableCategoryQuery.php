<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\CustomerFamilyAvailableCategory as ChildCustomerFamilyAvailableCategory;
use CustomerFamily\Model\CustomerFamilyAvailableCategoryQuery as ChildCustomerFamilyAvailableCategoryQuery;
use CustomerFamily\Model\Map\CustomerFamilyAvailableCategoryTableMap;
use CustomerFamily\Model\Thelia\Model\Category;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_family_available_category' table.
 *
 *
 *
 * @method     ChildCustomerFamilyAvailableCategoryQuery orderByCustomerFamilyId($order = Criteria::ASC) Order by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableCategoryQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 *
 * @method     ChildCustomerFamilyAvailableCategoryQuery groupByCustomerFamilyId() Group by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableCategoryQuery groupByCategoryId() Group by the category_id column
 *
 * @method     ChildCustomerFamilyAvailableCategoryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerFamilyAvailableCategoryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerFamilyAvailableCategoryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerFamilyAvailableCategoryQuery leftJoinCustomerFamily($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyAvailableCategoryQuery rightJoinCustomerFamily($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyAvailableCategoryQuery innerJoinCustomerFamily($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerFamily relation
 *
 * @method     ChildCustomerFamilyAvailableCategoryQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method     ChildCustomerFamilyAvailableCategoryQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method     ChildCustomerFamilyAvailableCategoryQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method     ChildCustomerFamilyAvailableCategory findOne(ConnectionInterface $con = null) Return the first ChildCustomerFamilyAvailableCategory matching the query
 * @method     ChildCustomerFamilyAvailableCategory findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerFamilyAvailableCategory matching the query, or a new ChildCustomerFamilyAvailableCategory object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerFamilyAvailableCategory findOneByCustomerFamilyId(int $customer_family_id) Return the first ChildCustomerFamilyAvailableCategory filtered by the customer_family_id column
 * @method     ChildCustomerFamilyAvailableCategory findOneByCategoryId(int $category_id) Return the first ChildCustomerFamilyAvailableCategory filtered by the category_id column
 *
 * @method     array findByCustomerFamilyId(int $customer_family_id) Return ChildCustomerFamilyAvailableCategory objects filtered by the customer_family_id column
 * @method     array findByCategoryId(int $category_id) Return ChildCustomerFamilyAvailableCategory objects filtered by the category_id column
 *
 */
abstract class CustomerFamilyAvailableCategoryQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\CustomerFamilyAvailableCategoryQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\CustomerFamilyAvailableCategory', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerFamilyAvailableCategoryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\CustomerFamilyAvailableCategoryQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\CustomerFamilyAvailableCategoryQuery();
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
     * @param array[$customer_family_id, $category_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCustomerFamilyAvailableCategory|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerFamilyAvailableCategoryTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerFamilyAvailableCategoryTableMap::DATABASE_NAME);
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
     * @return   ChildCustomerFamilyAvailableCategory A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CUSTOMER_FAMILY_ID, CATEGORY_ID FROM customer_family_available_category WHERE CUSTOMER_FAMILY_ID = :p0 AND CATEGORY_ID = :p1';
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
            $obj = new ChildCustomerFamilyAvailableCategory();
            $obj->hydrate($row);
            CustomerFamilyAvailableCategoryTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildCustomerFamilyAvailableCategory|array|mixed the result, formatted by the current formatter
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
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $key[1], Criteria::EQUAL);
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
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByCustomerFamilyId($customerFamilyId = null, $comparison = null)
    {
        if (is_array($customerFamilyId)) {
            $useMinMax = false;
            if (isset($customerFamilyId['min'])) {
                $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerFamilyId['max'])) {
                $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id > 12
     * </code>
     *
     * @see       filterByCategory()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\CustomerFamily object
     *
     * @param \CustomerFamily\Model\CustomerFamily|ObjectCollection $customerFamily The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByCustomerFamily($customerFamily, $comparison = null)
    {
        if ($customerFamily instanceof \CustomerFamily\Model\CustomerFamily) {
            return $this
                ->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $customerFamily->getId(), $comparison);
        } elseif ($customerFamily instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID, $customerFamily->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
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
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\Category object
     *
     * @param \CustomerFamily\Model\Thelia\Model\Category|ObjectCollection $category The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function filterByCategory($category, $comparison = null)
    {
        if ($category instanceof \CustomerFamily\Model\Thelia\Model\Category) {
            return $this
                ->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $category->getId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID, $category->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategory() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\Category or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Category relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Category');

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
            $this->addJoinObject($join, 'Category');
        }

        return $this;
    }

    /**
     * Use the Category relation Category object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\CategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\CustomerFamily\Model\Thelia\Model\CategoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCustomerFamilyAvailableCategory $customerFamilyAvailableCategory Object to remove from the list of results
     *
     * @return ChildCustomerFamilyAvailableCategoryQuery The current query, for fluid interface
     */
    public function prune($customerFamilyAvailableCategory = null)
    {
        if ($customerFamilyAvailableCategory) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CustomerFamilyAvailableCategoryTableMap::CUSTOMER_FAMILY_ID), $customerFamilyAvailableCategory->getCustomerFamilyId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CustomerFamilyAvailableCategoryTableMap::CATEGORY_ID), $customerFamilyAvailableCategory->getCategoryId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_family_available_category table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyAvailableCategoryTableMap::DATABASE_NAME);
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
            CustomerFamilyAvailableCategoryTableMap::clearInstancePool();
            CustomerFamilyAvailableCategoryTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerFamilyAvailableCategory or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerFamilyAvailableCategory object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyAvailableCategoryTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerFamilyAvailableCategoryTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerFamilyAvailableCategoryTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerFamilyAvailableCategoryTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CustomerFamilyAvailableCategoryQuery
