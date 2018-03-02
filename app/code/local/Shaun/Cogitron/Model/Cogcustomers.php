<?php

class Shaun_Cogitron_Model_Cogcustomers extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cogitron/cogproducts');
    }

    public function getNumbercustomers()
    {
        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";

        $sql="SELECT DISTINCT customer_email, customer_id
                FROM ".$table."
                GROUP BY customer_email";

        $results = $readConnection->fetchAll($sql);
        
        $count = count($results);
        return $count;
    }

    public function getNumberRepeatCustomers()
    {



        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";

        $sql="SELECT customer_email, customer_id, COUNT(*) AS custCount 
                FROM ".$table."
                GROUP BY customer_email
                HAVING custCount > 1";


        $results = $readConnection->fetchAll($sql);
        
        $rtn = count($results);

        return $rtn;
    }
    public function getHigestSpendCustomers()
    {


        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";



        return "<h3>Highest spending customers - sooner still(tm)</h3>";
    }

    public function getMostFrequentCustomers()
    {

        $rtn = array();

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";

        $sql="SELECT customer_email, customer_id, COUNT(customer_email) AS custCount 
                FROM ".$table."
                GROUP BY customer_email
                ORDER BY COUNT(customer_email) DESC LIMIT 5";

        $results = $readConnection->fetchAll($sql);
        $i=0;
        foreach($results as $row){

            $rtn[$i]=array($row['customer_id'],$row['customer_email'],$row['custCount']);
            $i++;
        }

        return $rtn;
    }
}