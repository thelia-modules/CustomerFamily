<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\CustomerFamilyPrice as ChildCustomerFamilyPrice;
use CustomerFamily\Model\CustomerFamilyPriceQuery as ChildCustomerFamilyPriceQuery;
use CustomerFamily\Model\Map\CustomerFamilyPriceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'customer_family_price' table.
 *
 *
 *
 * @method     ChildCustomerFamilyPriceQuery orderByCustomerFamilyId($order = Criteria::ASC) Order by the customer_family_id column
 * @method     ChildCustomerFamilyPriceQuery orderByPromo($order = Criteria::ASC) Order by the promo column
 * @method     ChildCustomerFamilyPriceQuery orderByUseEquation($order = Criteria::ASC) Order by the use_equation column
 * @method     ChildCustomerFamilyPriceQuery orderByAmountAddedBefore($order = Criteria::ASC) Order by the amount_added_before column
 * @method     ChildCustomerFamilyPriceQuery orderByAmountAddedAfter($order = Criteria::ASC) Order by the amount_added_after column
 * @method     ChildCustomerFamilyPriceQuery orderByMultiplicationCoefficient($order = Criteria::ASC) Order by the multiplication_coefficient column
 * @method     ChildCustomerFamilyPriceQuery orderByIsTaxed($order = Criteria::ASC) Order by the is_taxed column
 *
 * @method     ChildCustomerFamilyPriceQuery groupByCustomerFamilyId() Group by the customer_family_id column
 * @method     ChildCustomerFamilyPriceQuery groupByPromo() Group by the promo column
 * @method     ChildCustomerFamilyPriceQuery groupByUseEquation() Group by the use_equation column
 * @method     ChildCustomerFamilyPriceQuery groupByAmountAddedBefore() Group by the amount_added_before column
 * @method     ChildCustomerFamilyPriceQuery groupByAmountAddedAfter() Group by the amount_added_after column
 * @method     ChildCustomerFamilyPriceQuery groupByMultiplicationCoefficient() Group by the multiplication_coefficient column
 * @method     ChildCustomerFamilyPriceQuery groupByIsTaxed() Group by the is_taxed column
 *
 * @method     ChildCustomerFamilyPriceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCustomerFamilyPriceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCustomerFamilyPriceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCustomerFamilyPriceQuery leftJoinCustomerFamily($relationAlias = null) Adds a LEFT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyPriceQuery rightJoinCustomerFamily($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CustomerFamily relation
 * @method     ChildCustomerFamilyPriceQuery innerJoinCustomerFamily($relationAlias = null) Adds a INNER JOIN clause to the query using the CustomerFamily relation
 *
 * @method     ChildCustomerFamilyPrice findOne(ConnectionInterface $con = null) Return the first ChildCustomerFamilyPrice matching the query
 * @method     ChildCustomerFamilyPrice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCustomerFamilyPrice matching the query, or a new ChildCustomerFamilyPrice object populated from the query conditions when no match is found
 *
 * @method     ChildCustomerFamilyPrice findOneByCustomerFamilyId(int $customer_family_id) Return the first ChildCustomerFamilyPrice filtered by the customer_family_id column
 * @method     ChildCustomerFamilyPrice findOneByPromo(int $promo) Return the first ChildCustomerFamilyPrice filtered by the promo column
 * @method     ChildCustomerFamilyPrice findOneByUseEquation(int $use_equation) Return the first ChildCustomerFamilyPrice filtered by the use_equation column
 * @method     ChildCustomerFamilyPrice findOneByAmountAddedBefore(string $amount_added_before) Return the first ChildCustomerFamilyPrice filtered by the amount_added_before column
 * @method     ChildCustomerFamilyPrice findOneByAmountAddedAfter(string $amount_added_after) Return the first ChildCustomerFamilyPrice filtered by the amount_added_after column
 * @method     ChildCustomerFamilyPrice findOneByMultiplicationCoefficient(string $multiplication_coefficient) Return the first ChildCustomerFamilyPrice filtered by the multiplication_coefficient column
 * @method     ChildCustomerFamilyPrice findOneByIsTaxed(int $is_taxed) Return the first ChildCustomerFamilyPrice filtered by the is_taxed column
 *
 * @method     array findByCustomerFamilyId(int $customer_family_id) Return ChildCustomerFamilyPrice objects filtered by the customer_family_id column
 * @method     array findByPromo(int $promo) Return ChildCustomerFamilyPrice objects filtered by the promo column
 * @method     array findByUseEquation(int $use_equation) Return ChildCustomerFamilyPrice objects filtered by the use_equation column
 * @method     array findByAmountAddedBefore(string $amount_added_before) Return ChildCustomerFamilyPrice objects filtered by the amount_added_before column
 * @method     array findByAmountAddedAfter(string $amount_added_after) Return ChildCustomerFamilyPrice objects filtered by the amount_added_after column
 * @method     array findByMultiplicationCoefficient(string $multiplication_coefficient) Return ChildCustomerFamilyPrice objects filtered by the multiplication_coefficient column
 * @method     array findByIsTaxed(int $is_taxed) Return ChildCustomerFamilyPrice objects filtered by the is_taxed column
 *
 */
abstract class CustomerFamilyPriceQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\CustomerFamilyPriceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\CustomerFamilyPrice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCustomerFamilyPriceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCustomerFamilyPriceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\CustomerFamilyPriceQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\CustomerFamilyPriceQuery();
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
     * @param array[$customer_family_id, $promo] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCustomerFamilyPrice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CustomerFamilyPriceTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CustomerFamilyPriceTableMap::DATABASE_NAME);
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
     * @return   ChildCustomerFamilyPrice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CUSTOMER_FAMILY_ID, PROMO, USE_EQUATION, AMOUNT_ADDED_BEFORE, AMOUNT_ADDED_AFTER, MULTIPLICATION_COEFFICIENT, IS_TAXED FROM customer_family_price WHERE CUSTOMER_FAMILY_ID = :p0 AND PROMO = :p1';
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
            $obj = new ChildCustomerFamilyPrice();
            $obj->hydrate($row);
            CustomerFamilyPriceTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildCustomerFamilyPrice|array|mixed the result, formatted by the current formatter
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
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CustomerFamilyPriceTableMap::PROMO, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CustomerFamilyPriceTableMap::PROMO, $key[1], Criteria::EQUAL);
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
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByCustomerFamilyId($customerFamilyId = null, $comparison = null)
    {
        if (is_array($customerFamilyId)) {
            $useMinMax = false;
            if (isset($customerFamilyId['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerFamilyId['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $customerFamilyId, $comparison);
    }

    /**
     * Filter the query on the promo column
     *
     * Example usage:
     * <code>
     * $query->filterByPromo(1234); // WHERE promo = 1234
     * $query->filterByPromo(array(12, 34)); // WHERE promo IN (12, 34)
     * $query->filterByPromo(array('min' => 12)); // WHERE promo > 12
     * </code>
     *
     * @param     mixed $promo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByPromo($promo = null, $comparison = null)
    {
        if (is_array($promo)) {
            $useMinMax = false;
            if (isset($promo['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::PROMO, $promo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promo['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::PROMO, $promo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::PROMO, $promo, $comparison);
    }

    /**
     * Filter the query on the use_equation column
     *
     * Example usage:
     * <code>
     * $query->filterByUseEquation(1234); // WHERE use_equation = 1234
     * $query->filterByUseEquation(array(12, 34)); // WHERE use_equation IN (12, 34)
     * $query->filterByUseEquation(array('min' => 12)); // WHERE use_equation > 12
     * </code>
     *
     * @param     mixed $useEquation The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByUseEquation($useEquation = null, $comparison = null)
    {
        if (is_array($useEquation)) {
            $useMinMax = false;
            if (isset($useEquation['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::USE_EQUATION, $useEquation['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($useEquation['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::USE_EQUATION, $useEquation['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::USE_EQUATION, $useEquation, $comparison);
    }

    /**
     * Filter the query on the amount_added_before column
     *
     * Example usage:
     * <code>
     * $query->filterByAmountAddedBefore(1234); // WHERE amount_added_before = 1234
     * $query->filterByAmountAddedBefore(array(12, 34)); // WHERE amount_added_before IN (12, 34)
     * $query->filterByAmountAddedBefore(array('min' => 12)); // WHERE amount_added_before > 12
     * </code>
     *
     * @param     mixed $amountAddedBefore The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByAmountAddedBefore($amountAddedBefore = null, $comparison = null)
    {
        if (is_array($amountAddedBefore)) {
            $useMinMax = false;
            if (isset($amountAddedBefore['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_BEFORE, $amountAddedBefore['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amountAddedBefore['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_BEFORE, $amountAddedBefore['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_BEFORE, $amountAddedBefore, $comparison);
    }

    /**
     * Filter the query on the amount_added_after column
     *
     * Example usage:
     * <code>
     * $query->filterByAmountAddedAfter(1234); // WHERE amount_added_after = 1234
     * $query->filterByAmountAddedAfter(array(12, 34)); // WHERE amount_added_after IN (12, 34)
     * $query->filterByAmountAddedAfter(array('min' => 12)); // WHERE amount_added_after > 12
     * </code>
     *
     * @param     mixed $amountAddedAfter The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByAmountAddedAfter($amountAddedAfter = null, $comparison = null)
    {
        if (is_array($amountAddedAfter)) {
            $useMinMax = false;
            if (isset($amountAddedAfter['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_AFTER, $amountAddedAfter['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amountAddedAfter['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_AFTER, $amountAddedAfter['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::AMOUNT_ADDED_AFTER, $amountAddedAfter, $comparison);
    }

    /**
     * Filter the query on the multiplication_coefficient column
     *
     * Example usage:
     * <code>
     * $query->filterByMultiplicationCoefficient(1234); // WHERE multiplication_coefficient = 1234
     * $query->filterByMultiplicationCoefficient(array(12, 34)); // WHERE multiplication_coefficient IN (12, 34)
     * $query->filterByMultiplicationCoefficient(array('min' => 12)); // WHERE multiplication_coefficient > 12
     * </code>
     *
     * @param     mixed $multiplicationCoefficient The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByMultiplicationCoefficient($multiplicationCoefficient = null, $comparison = null)
    {
        if (is_array($multiplicationCoefficient)) {
            $useMinMax = false;
            if (isset($multiplicationCoefficient['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::MULTIPLICATION_COEFFICIENT, $multiplicationCoefficient['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($multiplicationCoefficient['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::MULTIPLICATION_COEFFICIENT, $multiplicationCoefficient['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::MULTIPLICATION_COEFFICIENT, $multiplicationCoefficient, $comparison);
    }

    /**
     * Filter the query on the is_taxed column
     *
     * Example usage:
     * <code>
     * $query->filterByIsTaxed(1234); // WHERE is_taxed = 1234
     * $query->filterByIsTaxed(array(12, 34)); // WHERE is_taxed IN (12, 34)
     * $query->filterByIsTaxed(array('min' => 12)); // WHERE is_taxed > 12
     * </code>
     *
     * @param     mixed $isTaxed The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByIsTaxed($isTaxed = null, $comparison = null)
    {
        if (is_array($isTaxed)) {
            $useMinMax = false;
            if (isset($isTaxed['min'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::IS_TAXED, $isTaxed['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isTaxed['max'])) {
                $this->addUsingAlias(CustomerFamilyPriceTableMap::IS_TAXED, $isTaxed['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CustomerFamilyPriceTableMap::IS_TAXED, $isTaxed, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\CustomerFamily object
     *
     * @param \CustomerFamily\Model\CustomerFamily|ObjectCollection $customerFamily The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function filterByCustomerFamily($customerFamily, $comparison = null)
    {
        if ($customerFamily instanceof \CustomerFamily\Model\CustomerFamily) {
            return $this
                ->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $customerFamily->getId(), $comparison);
        } elseif ($customerFamily instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID, $customerFamily->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
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
     * @param   ChildCustomerFamilyPrice $customerFamilyPrice Object to remove from the list of results
     *
     * @return ChildCustomerFamilyPriceQuery The current query, for fluid interface
     */
    public function prune($customerFamilyPrice = null)
    {
        if ($customerFamilyPrice) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CustomerFamilyPriceTableMap::CUSTOMER_FAMILY_ID), $customerFamilyPrice->getCustomerFamilyId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CustomerFamilyPriceTableMap::PROMO), $customerFamilyPrice->getPromo(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the customer_family_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyPriceTableMap::DATABASE_NAME);
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
            CustomerFamilyPriceTableMap::clearInstancePool();
            CustomerFamilyPriceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCustomerFamilyPrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCustomerFamilyPrice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CustomerFamilyPriceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CustomerFamilyPriceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CustomerFamilyPriceTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CustomerFamilyPriceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CustomerFamilyPriceQuery
