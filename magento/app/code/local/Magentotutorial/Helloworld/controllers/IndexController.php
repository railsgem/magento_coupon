<?php
class Magentotutorial_Helloworld_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction() {

        echo 'Hello World';
        $this->loadLayout();
    	$this->renderLayout();
    }
    public function goodbyeAction() {
	    echo 'Goodbye World!';
        $this->loadLayout();
    	$this->renderLayout();
	}

	public function paramsAction() {
	    echo '
	';            
	    foreach($this->getRequest()->getParams() as $key=>$value) {
	        echo '
	Param: '.$key.'
	';
	        echo '
	Value: '.$value.'
	';
	    }
	    echo '
	';
	}


}