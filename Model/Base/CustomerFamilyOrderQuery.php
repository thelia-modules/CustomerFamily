<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\CustomerFamilyOrder as ChildCustomerFamilyOrder;
use CustomerFamily\Model\CustomerFamilyOrderQuery as ChildCustomerFamilyOrderQuery;
use CustomerFamily\Model\Map\CustomerFamilyOrderTableMap;
use CustomerFamily\Model\Thelia\Model\Order;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_family_order' table.
 *
 *
 *
 * @method     ChildCustomerFamilyOrderQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildCustomerFamilyOrderQuery orderByCustomerFamilyCode($order = Criteria::ASC) Order by the customer_family_code column
 *
 * @method     ChildCustomerFamilyOrderQuery groupByOrderId() Group by the order_id column
 * @method     ChildCustomerFamilyOrderQuery groupByCustomerFamilyCode() Group by the customer_family_code column
 *
 * @method     ChildCustomerFamilyOrderQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerFamilyOrderQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerFamilyOrderQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerFamilyOrderQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildCustomerFamilyOrderQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildCustomerFamilyOrderQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildCustomerFamilyOrderQuery leftJoinCustomerFamily($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyOrderQuery rightJoinCustomerFamily($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyOrderQuery innerJoinCustomerFamily($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerFamily relation
 *
 * @method     ChildCustomerFamilyOrder findOne(ConnectionInterface $con = null) Return the first ChildCustomerFamilyOrder matching the query
 * @method     ChildCustomerFamilyOrder findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerFamilyOrder matching the query, or a new ChildCustomerFamilyOrder object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerFamilyOrder findOneByOrderId(int $order_id) Return the first ChildCustomerFamilyOrder filtered by the order_id column
 * @method     ChildCustomerFamilyOrder findOneByCustomerFamilyCode(string $customer_family_code) Return the first ChildCustomerFamilyOrder filtered by the customer_family_code column
 *
 * @method     array findByOrderId(int $order_id) Return ChildCustomerFamilyOrder objects filtered by the order_id column
 * @method     array findByCustomerFamilyCode(string $customer_family_code) Return ChildCustomerFamilyOrder objects filtered by the customer_family_code column
 *
 */
abstract class CustomerFamilyOrderQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\CustomerFamilyOrderQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\CustomerFamilyOrder', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerFamilyOrderQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerFamilyOrderQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\CustomerFamilyOrderQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\CustomerFamilyOrderQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCustomerFamilyOrder|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerFamilyOrderTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerFamilyOrderTableMap::DATABASE_NAME);
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
     * @return   ChildCustomerFamilyOrder A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ORDER_ID, CUSTOMER_FAMILY_CODE FROM customer_family_order WHERE ORDER_ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildCustomerFamilyOrder();
            $obj->hydrate($row);
            CustomerFamilyOrderTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCustomerFamilyOrder|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @see       filterByOrder()
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the customer_family_code column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerFamilyCode('fooValue');   // WHERE customer_family_code = 'fooValue'
     * $query->filterByCustomerFamilyCode('%fooValue%'); // WHERE customer_family_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $customerFamilyCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByCustomerFamilyCode($customerFamilyCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($customerFamilyCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $customerFamilyCode)) {
                $customerFamilyCode = str_replace('*', '%', $customerFamilyCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CustomerFamilyOrderTableMap::CUSTOMER_FAMILY_CODE, $customerFamilyCode, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\Order object
     *
     * @param \CustomerFamily\Model\Thelia\Model\Order|ObjectCollection $order The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \CustomerFamily\Model\Thelia\Model\Order) {
            return $this
                ->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $order->getId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $order->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrder() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\Order or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Order relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Order');

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
            $this->addJoinObject($join, 'Order');
        }

        return $this;
    }

    /**
     * Use the Order relation Order object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\OrderQuery A secondary query class using the current class as primary query
     */
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\CustomerFamily\Model\Thelia\Model\OrderQuery');
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\CustomerFamily object
     *
     * @param \CustomerFamily\Model\CustomerFamily|ObjectCollection $customerFamily The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function filterByCustomerFamily($customerFamily, $comparison = null)
    {
        if ($customerFamily instanceof \CustomerFamily\Model\CustomerFamily) {
            return $this
                ->addUsingAlias(CustomerFamilyOrderTableMap::CUSTOMER_FAMILY_CODE, $customerFamily->getCode(), $comparison);
        } elseif ($customerFamily instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyOrderTableMap::CUSTOMER_FAMILY_CODE, $customerFamily->toKeyValue('PrimaryKey', 'Code'), $comparison);
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
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildCustomerFamilyOrder $customerFamilyOrder Object to remove from the list of results
     *
     * @return ChildCustomerFamilyOrderQuery The current query, for fluid interface
     */
    public function prune($customerFamilyOrder = null)
    {
        if ($customerFamilyOrder) {
            $this->addUsingAlias(CustomerFamilyOrderTableMap::ORDER_ID, $customerFamilyOrder->getOrderId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_family_order table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyOrderTableMap::DATABASE_NAME);
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
            CustomerFamilyOrderTableMap::clearInstancePool();
            CustomerFamilyOrderTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerFamilyOrder or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerFamilyOrder object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyOrderTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerFamilyOrderTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerFamilyOrderTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerFamilyOrderTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CustomerFamilyOrderQuery
