<?php

namespace CustomerFamily\Model\Base;

use \Exception;
use \PDO;
use CustomerFamily\Model\ProductPurchasePrice as ChildProductPurchasePrice;
use CustomerFamily\Model\ProductPurchasePriceQuery as ChildProductPurchasePriceQuery;
use CustomerFamily\Model\Map\ProductPurchasePriceTableMap;
use CustomerFamily\Model\Thelia\Model\Currency;
use CustomerFamily\Model\Thelia\Model\ProductSaleElements;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product_purchase_price' table.
 *
 *
 *
 * @method     ChildProductPurchasePriceQuery orderByProductSaleElementsId($order = Criteria::ASC) Order by the product_sale_elements_id column
 * @method     ChildProductPurchasePriceQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildProductPurchasePriceQuery orderByPurchasePrice($order = Criteria::ASC) Order by the purchase_price column
 *
 * @method     ChildProductPurchasePriceQuery groupByProductSaleElementsId() Group by the product_sale_elements_id column
 * @method     ChildProductPurchasePriceQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildProductPurchasePriceQuery groupByPurchasePrice() Group by the purchase_price column
 *
 * @method     ChildProductPurchasePriceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductPurchasePriceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductPurchasePriceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductPurchasePriceQuery leftJoinProductSaleElements($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductSaleElements relation
 * @method     ChildProductPurchasePriceQuery rightJoinProductSaleElements($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductSaleElements relation
 * @method     ChildProductPurchasePriceQuery innerJoinProductSaleElements($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductSaleElements relation
 *
 * @method     ChildProductPurchasePriceQuery leftJoinCurrency($relationAlias = null) Adds a LEFT JOIN clause to the query using the Currency relation
 * @method     ChildProductPurchasePriceQuery rightJoinCurrency($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Currency relation
 * @method     ChildProductPurchasePriceQuery innerJoinCurrency($relationAlias = null) Adds a INNER JOIN clause to the query using the Currency relation
 *
 * @method     ChildProductPurchasePrice findOne(ConnectionInterface $con = null) Return the first ChildProductPurchasePrice matching the query
 * @method     ChildProductPurchasePrice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProductPurchasePrice matching the query, or a new ChildProductPurchasePrice object populated from the query conditions when no match is found
 *
 * @method     ChildProductPurchasePrice findOneByProductSaleElementsId(int $product_sale_elements_id) Return the first ChildProductPurchasePrice filtered by the product_sale_elements_id column
 * @method     ChildProductPurchasePrice findOneByCurrencyId(int $currency_id) Return the first ChildProductPurchasePrice filtered by the currency_id column
 * @method     ChildProductPurchasePrice findOneByPurchasePrice(string $purchase_price) Return the first ChildProductPurchasePrice filtered by the purchase_price column
 *
 * @method     array findByProductSaleElementsId(int $product_sale_elements_id) Return ChildProductPurchasePrice objects filtered by the product_sale_elements_id column
 * @method     array findByCurrencyId(int $currency_id) Return ChildProductPurchasePrice objects filtered by the currency_id column
 * @method     array findByPurchasePrice(string $purchase_price) Return ChildProductPurchasePrice objects filtered by the purchase_price column
 *
 */
abstract class ProductPurchasePriceQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CustomerFamily\Model\Base\ProductPurchasePriceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CustomerFamily\\Model\\ProductPurchasePrice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductPurchasePriceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductPurchasePriceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CustomerFamily\Model\ProductPurchasePriceQuery) {
            return $criteria;
        }
        $query = new \CustomerFamily\Model\ProductPurchasePriceQuery();
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
     * @param array[$product_sale_elements_id, $currency_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildProductPurchasePrice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductPurchasePriceTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductPurchasePriceTableMap::DATABASE_NAME);
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
     * @return   ChildProductPurchasePrice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT PRODUCT_SALE_ELEMENTS_ID, CURRENCY_ID, PURCHASE_PRICE FROM product_purchase_price WHERE PRODUCT_SALE_ELEMENTS_ID = :p0 AND CURRENCY_ID = :p1';
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
            $obj = new ChildProductPurchasePrice();
            $obj->hydrate($row);
            ProductPurchasePriceTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildProductPurchasePrice|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(ProductPurchasePriceTableMap::CURRENCY_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the product_sale_elements_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductSaleElementsId(1234); // WHERE product_sale_elements_id = 1234
     * $query->filterByProductSaleElementsId(array(12, 34)); // WHERE product_sale_elements_id IN (12, 34)
     * $query->filterByProductSaleElementsId(array('min' => 12)); // WHERE product_sale_elements_id > 12
     * </code>
     *
     * @see       filterByProductSaleElements()
     *
     * @param     mixed $productSaleElementsId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByProductSaleElementsId($productSaleElementsId = null, $comparison = null)
    {
        if (is_array($productSaleElementsId)) {
            $useMinMax = false;
            if (isset($productSaleElementsId['min'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $productSaleElementsId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productSaleElementsId['max'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $productSaleElementsId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $productSaleElementsId, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @see       filterByCurrency()
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the purchase_price column
     *
     * Example usage:
     * <code>
     * $query->filterByPurchasePrice(1234); // WHERE purchase_price = 1234
     * $query->filterByPurchasePrice(array(12, 34)); // WHERE purchase_price IN (12, 34)
     * $query->filterByPurchasePrice(array('min' => 12)); // WHERE purchase_price > 12
     * </code>
     *
     * @param     mixed $purchasePrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByPurchasePrice($purchasePrice = null, $comparison = null)
    {
        if (is_array($purchasePrice)) {
            $useMinMax = false;
            if (isset($purchasePrice['min'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::PURCHASE_PRICE, $purchasePrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($purchasePrice['max'])) {
                $this->addUsingAlias(ProductPurchasePriceTableMap::PURCHASE_PRICE, $purchasePrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductPurchasePriceTableMap::PURCHASE_PRICE, $purchasePrice, $comparison);
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\ProductSaleElements object
     *
     * @param \CustomerFamily\Model\Thelia\Model\ProductSaleElements|ObjectCollection $productSaleElements The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByProductSaleElements($productSaleElements, $comparison = null)
    {
        if ($productSaleElements instanceof \CustomerFamily\Model\Thelia\Model\ProductSaleElements) {
            return $this
                ->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $productSaleElements->getId(), $comparison);
        } elseif ($productSaleElements instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID, $productSaleElements->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProductSaleElements() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\ProductSaleElements or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductSaleElements relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function joinProductSaleElements($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductSaleElements');

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
            $this->addJoinObject($join, 'ProductSaleElements');
        }

        return $this;
    }

    /**
     * Use the ProductSaleElements relation ProductSaleElements object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\ProductSaleElementsQuery A secondary query class using the current class as primary query
     */
    public function useProductSaleElementsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductSaleElements($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductSaleElements', '\CustomerFamily\Model\Thelia\Model\ProductSaleElementsQuery');
    }

    /**
     * Filter the query by a related \CustomerFamily\Model\Thelia\Model\Currency object
     *
     * @param \CustomerFamily\Model\Thelia\Model\Currency|ObjectCollection $currency The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function filterByCurrency($currency, $comparison = null)
    {
        if ($currency instanceof \CustomerFamily\Model\Thelia\Model\Currency) {
            return $this
                ->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $currency->getId(), $comparison);
        } elseif ($currency instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductPurchasePriceTableMap::CURRENCY_ID, $currency->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrency() only accepts arguments of type \CustomerFamily\Model\Thelia\Model\Currency or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Currency relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function joinCurrency($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Currency');

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
            $this->addJoinObject($join, 'Currency');
        }

        return $this;
    }

    /**
     * Use the Currency relation Currency object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CustomerFamily\Model\Thelia\Model\CurrencyQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCurrency($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Currency', '\CustomerFamily\Model\Thelia\Model\CurrencyQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProductPurchasePrice $productPurchasePrice Object to remove from the list of results
     *
     * @return ChildProductPurchasePriceQuery The current query, for fluid interface
     */
    public function prune($productPurchasePrice = null)
    {
        if ($productPurchasePrice) {
            $this->addCond('pruneCond0', $this->getAliasedColName(ProductPurchasePriceTableMap::PRODUCT_SALE_ELEMENTS_ID), $productPurchasePrice->getProductSaleElementsId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(ProductPurchasePriceTableMap::CURRENCY_ID), $productPurchasePrice->getCurrencyId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product_purchase_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductPurchasePriceTableMap::DATABASE_NAME);
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
            ProductPurchasePriceTableMap::clearInstancePool();
            ProductPurchasePriceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProductPurchasePrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProductPurchasePrice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductPurchasePriceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductPurchasePriceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        ProductPurchasePriceTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ProductPurchasePriceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProductPurchasePriceQuery
