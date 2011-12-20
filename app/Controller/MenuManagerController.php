<?php

	class MenuManagerController extends Controller {
		
		var $name = "MenuManager";
	    var $components = array('RequestHandler');
	    var $layout = 'ajax';
	    var $autoRender=false;
	 
	    function index() {
	        $this->redirect("../");
	    } // function index
	    
	    function stringCall() {
	        $this->respond('Einfacher Text');
	    } // function stringCall
	 
	    function getMenus() {
	    	
	    	$this->loadModel('MenuEntry');
	    	$data = $this->MenuEntry->find('all');
	    	
	    	//print "<pre>";
	    	//print_r($data);die;
	    	
	    	for ($i = 0; $i < sizeof($data); $i++) {
	    		
	    		$menu[$i]['id'] = $this->getHTMLID($data[$i]['Page']['title'],$data[$i]['Page']['id']);
	    		$menu[$i]['name'] = $data[$i]['Page']['title'];
	    		
	    	} // for
	    	//print "<pre>";
	    	//print_r($menu);die;
	    	$this->respond($menu,true);
	    	/*
	        $this->respond(array(	array(	'name'=>'Home', 
	        								'id'=>'home',
	        								'submenu'=>array(
        													array('name'=>'Home Sub1','id'=>'sub_home1'),
        													array('name'=>'Home Sub2','id'=>'sub_home2')
        												)
        								),
        							array(	'name'=>'Manager', 
	        								'id'=>'manager',
	        								'submenu'=>array(
        													array('name'=>'Manager Sub1','id'=>'sub_manager1'),
        													array('name'=>'Manager Sub2','id'=>'sub_manager2')
        												)
        								)
	        						), true);
	        						
	        						*/
	    } // function getMenus
	    
	    function getHTMLID($title,$id){
	    	
	    	$htmlid = "";
	    	
	    	$htmlid = str_replace(" ", "_", strtolower($title))."_".$id;
	    	
	    	return $htmlid;
	    	
	    } // function 
	    
	    function respond($jsonString=null, $json=false) {
	        if ($jsonString!=null) {
	            if ($json==true) {
	                //$this->RequestHandler->setContent('json', 'application/json');
	                $this->response->type('json');
	                $jsonString=json_encode($jsonString);
	            }
	            $this->set('menu', $jsonString);
	        }
	        $this->render('menu');
	    } // function respond
	 
	} // class MenuManagerController
	
?>