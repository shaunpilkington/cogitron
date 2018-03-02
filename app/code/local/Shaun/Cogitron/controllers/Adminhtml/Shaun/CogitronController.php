<?php
/**
 * Created by PhpStorm.
 * User: shaun
 * Date: 02/02/2018
 * Time: 16:07
 */


class Shaun_Cogitron_Adminhtml_Shaun_CogitronController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
      //  echo 'Hello Index!';
        $this->loadLayout();
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('cogitron/dash.phtml'));



        $this->renderLayout();
    }
}