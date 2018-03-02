<?php
/**
 * Created by PhpStorm.
 * User: shaun
 * Date: 07/02/18
 * Time: 11:48
 */

class Shaun_Cogitron_Model_Cogsales extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cogitron/cogsales');
    }

    public function getCartav()
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";

        //WE ONLY WANT COMPLETED SALES THAT AREN'T REFUNDED - INCLUDES THOSE PROCESSING

        $sql="SELECT entity_id, total_paid, created_at FROM ".$table." WHERE status='complete' ORDER BY created_at DESC";


        $results = $readConnection->fetchAll($sql);
        $i =0;
        $total = 0;
        $i =  count($results);

        $latest = $results[0]['created_at'];

        $date = date( "m/d/Y", strtotime($latest));
        $sql = "SELECT SUM(total_paid) FROM ".$table." WHERE status='complete'";

        $result = $readConnection->fetchAll($sql);
        $total = number_format($result[0]['SUM(total_paid)'],2);

        $rtn = array($i, $total, $date);

        return $rtn;
    }

    public function getCartLifetimeAverages()
    {
        //Control how much spews out onto the dash
        $limit = intval(Mage::getStoreConfig('cogitron/settings/cogitron_dashsales'));
        if(!$limit){$limit = 12;}

        $rtn = array();
        
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');

        $table=Mage::getConfig()->getTablePrefix()."sales_flat_order";

        //get latest
        $sqlA="SELECT created_at FROM ".$table." WHERE status='complete' ORDER BY created_at DESC";

        $results = $readConnection->fetchAll($sqlA);
        $i =1;
        $monthsub = 1;
        $total = 0;

        $latest = $results[0]['created_at'];

        $date = date( "d/m/Y", strtotime($latest));
        $d = new DateTime( $date );



        while($i >= 1 && $monthsub != $limit) {

        $sql="SELECT entity_id, subtotal_invoiced, created_at FROM ".$table." WHERE status='complete' AND created_at <= DATE_SUB(STR_TO_DATE('" . $latest . "', '%Y-%m-%d'),INTERVAL " . $monthsub . " MONTH) ORDER BY created_at DESC ";

        $results = $readConnection->fetchAll($sql);

        $i =  count($results);


            $d->modify( 'last day of last month' );
            $sql = "SELECT SUM(subtotal_invoiced) FROM " . $table . " WHERE status='complete' AND created_at <= DATE_SUB(STR_TO_DATE('" . $latest . "', '%Y-%m-%d'),INTERVAL " . $monthsub . " MONTH)";
            $result = $readConnection->fetchAll($sql);
            //$date = date("m/Y", strtotime("-".$monthsub." months",$latest));

            $total = number_format($result[0]['SUM(subtotal_invoiced)'], 2);
            if($total > 0){
            $rtn[$monthsub] = array( $i, $total, $d->format('d-m-Y'));
            }
            $monthsub++;
        }
        return $rtn;
    }
};