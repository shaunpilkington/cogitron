<?php

class Shaun_Cogitron_Model_Cogproducts extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cogitron/cogproducts');
    }

    public function getProductstock()
    {
        $red = intval(Mage::getStoreConfig('cogitron/settings/cogitron_stockred'));
        $orange = intval(Mage::getStoreConfig('cogitron/settings/cogitron_stockorange'));

        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $tableA=Mage::getConfig()->getTablePrefix()."cataloginventory_stock_item";
        $tableB=Mage::getConfig()->getTablePrefix()."catalog_product_flat_1";

        $sql="SELECT ".$tableA.".product_id, ".$tableA.".manage_stock, ".$tableA.".qty, ".$tableB.".sku FROM ".$tableA."
              LEFT JOIN ".$tableB." ON ".$tableA.".product_id = ".$tableB.".entity_id 
              WHERE ".$tableA.".manage_stock =1 AND status =1 AND qty <= ".$red. " AND sku IS NOT null";

        $results = $readConnection->fetchAll($sql);

        $redcount = count($results);

        $sql="SELECT ".$tableA.".product_id, ".$tableA.".manage_stock, ".$tableA.".qty, ".$tableB.".sku FROM ".$tableA."
              LEFT JOIN ".$tableB." ON ".$tableA.".product_id = ".$tableB.".entity_id 
              WHERE ".$tableA.".manage_stock =1 AND status =1 AND qty <= ".$orange. " AND sku IS NOT null";

        $results = $readConnection->fetchAll($sql);

        $orangecount = count($results);

        $rtn="<p class=\"orangestock\">You have ".$orangecount." products with ".$orange." or fewer in stock.</p><p class=\"redstock\">You have ".$redcount." products with ".$red." or fewer in stock.</p>";
        $rtn.="<p><a href=\"\">Click here to see full breakdown.</a></p>";

        return $rtn;
    }
    public function getBestseller()
    {
        $limit = intval(Mage::getStoreConfig('cogitron/settings/cogitron_mostfreq'));

        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order_item";

        $sql="SELECT sku, name, product_id, COUNT(sku) AS skuCount 
                FROM ".$table."
                GROUP BY sku
                ORDER BY COUNT(sku) DESC LIMIT ".$limit;

        $results = $readConnection->fetchAll($sql);

        return $results;
    }
    public function getMostseller()
    {
        $limit = intval(Mage::getStoreConfig('cogitron/settings/cogitron_mostunits'));

        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order_item";

        $sql="SELECT sku, name, product_id, qty_shipped, SUM(qty_shipped) AS shipCount 
                FROM ".$table."
                GROUP BY sku
                ORDER BY SUM(qty_shipped) DESC LIMIT ".$limit;

        $results = $readConnection->fetchAll($sql);

        return $results;
    }
}