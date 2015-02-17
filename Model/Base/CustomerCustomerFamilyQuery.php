<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\CustomerCustomerFamily as ChildCustomerCustomerFamily;
use CustomerFamily\Model\CustomerCustomerFamilyQuery as ChildCustomerCustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use CustomerFamily\Model\Thelia\Model\Customer;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_customer_family' table.
 *
 *
 *
 * @method     ChildCustomerCustomerFamilyQuery orderByCustomerId($order = Criteria::ASC) Order by the customer_id column
 * @method     ChildCustomerCustomerFamilyQuery orderByCustomerFamilyId($order = Criteria::ASC) Order by the customer_family_id column
 * @method     ChildCustomerCustomerFamilyQuery orderBySiret($order = Criteria::ASC) Order by the siret column
 * @method     ChildCustomerCustomerFamilyQuery orderByVat($order = Criteria::ASC) Order by the vat column
 *
 * @method     ChildCustomerCustomerFamilyQuery groupByCustomerId() Group by the customer_id column
 * @method     ChildCustomerCustomerFamilyQuery groupByCustomerFamilyId() Group by the customer_family_id column
 * @method     ChildCustomerCustomerFamilyQuery groupBySiret() Group by the siret column
 * @method     ChildCustomerCustomerFamilyQuery groupByVat() Group by the vat column
 *
 * @method     ChildCustomerCustomerFamilyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerCustomerFamilyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerCustomerFamilyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerCustomerFamilyQuery leftJoinCustomer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Customer relation
 * @method     ChildCustomerCustomerFamilyQuery rightJoinCustomer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Customer relation
 * @method     ChildCustomerCustomerFamilyQuery innerJoinCustomer($relationAlias = null) Adds a INNER JOIN clause to the query using the Customer relation
 *
 * @method     ChildCustomerCustomerFamilyQuery leftJoinCustomerFamily($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerCustomerFamilyQuery rightJoinCustomerFamily($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerCustomerFamilyQuery innerJoinCustomerFamily($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerFamily relation
 *
 * @method     ChildCustomerCustomerFamily findOne(ConnectionInterface $con = null) Return the first ChildCustomerCustomerFamily matching the query
 * @method     ChildCustomerCustomerFamily findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerCustomerFamily matching the query, or a new ChildCustomerCustomerFamily object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerCustomerFamily findOneByCustomerId(int $customer_id) Return the first ChildCustomerCustomerFamily filtered by the customer_id column
 * @method     ChildCustomerCustomerFamily findOneByCustomerFamilyId(int $customer_family_id) Return the first ChildCustomerCustomerFamily filtered by the customer_family_id column
 * @method     ChildCustomerCustomerFamily findOneBySiret(string $siret) Return the first ChildCustomerCustomerFamily filtered by the siret column
 * @method     ChildCustomerCustomerFamily findOneByVat(string $vat) Return the first ChildCustomerCustomerFamily filtered by the vat column
 *
 * @method     array findByCustomerId(int $customer_id) Return ChildCustomerCustomerFamily objects filtered by the customer_id column
 * @method     array findByCustomerFamilyId(int $customer_family_id) Return ChildCustomerCustomerFamily objects filtered by the customer_family_id column
 * @method     array findBySiret(string $siret) Return ChildCustomerCustomerFamily objects filtered by the siret column
 * @method     array findByVat(string $vat) Return ChildCustomerCustomerFamily objects filtered by the vat column
 *
 */
abstract class CustomerCustomerFamilyQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\CustomerCustomerFamilyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\CustomerCustomerFamily', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerCustomerFamilyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerCustomerFamilyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\CustomerCustomerFamilyQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\CustomerCustomerFamilyQuery();
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
     * @return ChildCustomerCustomerFamily|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerCustomerFamilyTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerCustomerFamilyTableMap::DATABASE_NAME);
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
     * @return   ChildCustomerCustomerFamily A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CUSTOMER_ID, CUSTOMER_FAMILY_ID, SIRET, VAT FROM customer_customer_family WHERE CUSTOMER_ID = :p0';
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
            $obj = new ChildCustomerCustomerFamily();
            $obj->hydrate($row);
            CustomerCustomerFamilyTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCustomerCustomerFamily|array|mixed the result, formatted by the current formatter
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
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the customer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerId(1234); // WHERE customer_id = 1234
     * $query->filterByCustomerId(array(12, 34)); // WHERE customer_id IN (12, 34)
     * $query->filterByCustomerId(array('min' => 12)); // WHERE customer_id > 12
     * </code>
     *
     * @see       filterByCustomer()
     *
     * @param     mixed $customerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByCustomerId($customerId = null, $comparison = null)
    {
        if (is_array($customerId)) {
            $useMinMax = false;
            if (isset($customerId['min'])) {
                $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerId['max'])) {
                $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customerId, $comparison);
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
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByCustomerFamilyId($customerFamilyId = null, $comparison = null)
    {
        if (is_array($customerFamilyId)) {
            $useMinMax = false;
            if (isset($customerFamilyId['min'])) {
                $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerFamilyId['max'])) {
                $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId, $comparison);
    }

    /**
     * Filter the query on the siret column
     *
     * Example usage:
     * <code>
     * $query->filterBySiret('fooValue');   // WHERE siret = 'fooValue'
     * $query->filterBySiret('%fooValue%'); // WHERE siret LIKE '%fooValue%'
     * </code>
     *
     * @param     string $siret The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterBySiret($siret = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($siret)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $siret)) {
                $siret = str_replace('*', '%', $siret);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::SIRET, $siret, $comparison);
    }

    /**
     * Filter the query on the vat column
     *
     * Example usage:
     * <code>
     * $query->filterByVat('fooValue');   // WHERE vat = 'fooValue'
     * $query->filterByVat('%fooValue%'); // WHERE vat LIKE '%fooValue%'
     * </code>
     *
     * @param     string $vat The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByVat($vat = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($vat)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $vat)) {
                $vat = str_replace('*', '%', $vat);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CustomerCustomerFamilyTableMap::VAT, $vat, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\Customer object
     *
     * @param \CustomerFamily\Model\Thelia\Model\Customer|ObjectCollection $customer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByCustomer($customer, $comparison = null)
    {
        if ($customer instanceof \CustomerFamily\Model\Thelia\Model\Customer) {
            return $this
                ->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customer->getId(), $comparison);
        } elseif ($customer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCustomer() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\Customer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Customer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function joinCustomer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Customer');

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
            $this->addJoinObject($join, 'Customer');
        }

        return $this;
    }

    /**
     * Use the Customer relation Customer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\CustomerQuery A secondary query class using the current class as primary query
     */
    public function useCustomerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCustomer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Customer', '\CustomerFamily\Model\Thelia\Model\CustomerQuery');
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\CustomerFamily object
     *
     * @param \CustomerFamily\Model\CustomerFamily|ObjectCollection $customerFamily The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function filterByCustomerFamily($customerFamily, $comparison = null)
    {
        if ($customerFamily instanceof \CustomerFamily\Model\CustomerFamily) {
            return $this
                ->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID, $customerFamily->getId(), $comparison);
        } elseif ($customerFamily instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID, $customerFamily->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
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
     * @param   ChildCustomerCustomerFamily $customerCustomerFamily Object to remove from the list of results
     *
     * @return ChildCustomerCustomerFamilyQuery The current query, for fluid interface
     */
    public function prune($customerCustomerFamily = null)
    {
        if ($customerCustomerFamily) {
            $this->addUsingAlias(CustomerCustomerFamilyTableMap::CUSTOMER_ID, $customerCustomerFamily->getCustomerId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_customer_family table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerCustomerFamilyTableMap::DATABASE_NAME);
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
            CustomerCustomerFamilyTableMap::clearInstancePool();
            CustomerCustomerFamilyTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerCustomerFamily or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerCustomerFamily object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerCustomerFamilyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerCustomerFamilyTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerCustomerFamilyTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerCustomerFamilyTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CustomerCustomerFamilyQuery
