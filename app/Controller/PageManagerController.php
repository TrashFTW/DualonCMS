<?php

	class PageManagerController extends Controller {
		
		var $name = "PageManager";
	    var $components = array('RequestHandler');
	    var $layout = 'ajax';
	    var $autoRender = false;
	 
	    function index() {
	        $this->redirect("../");
	    } // function index
	    
	    function stringCall() {
	        $this->respond('Einfacher Text');
	    } // function stringCall
	 
	    function getPages() {
	    	
	    	$this->loadModel('Page');
	    	
	    	$data = $this->Page->find('all');
	    	
	    	for ($i = 0; $i < sizeof($data); $i++) {
	    		
	    		$pages[$i]['Page'] =  $data[$i]['Page'];
	    		$pages[$i]['Page']['id'] = $this->getHTMLID($pages[$i]['Page']['title'],$pages[$i]['Page']['id']);
	    		$pages[$i]['Page']['name'] = $pages[$i]['Page']['title'];
	    		$pages[$i]['Container'] =  $data[$i]['Container'];
	    		
	    	} // for
	    	
	    	$this->respond($pages,true);
	    	/*
	    	$this->respond(array(	array(	'name'=>'Home', 
	        								'id'=>'home'
        								),
        							array(	'name'=>'Manager', 
	        								'id'=>'manager'
        								)
	        						), true);
	        */
	    } // function jsonCall
	    
	    function setPage(){
	    	$this->loadModel('Page');
	    	$this->loadModel('MenuEntry');
	    	/*
	    	print "<pre>";
	    	print_r($this->request->query);die;*/
	    	$pagename = $this->request->query['pagename'];
	    	
	    	$data = $this->Page->find('first', array('conditions' => array('Page.title' => $pagename)));
	    	//$data = $this->Page->find('all');
	    	print sizeof($data['Page']);
	    	
	    	if(sizeof($data['Page']) == 0){
	    		$data = null;
	    		
	    		$data['Page']['user_id'] = 1;
	    		$data['Page']['title'] = $pagename;
	    		$data['Page']['container_id'] = null;
	    		$data['Page']['validFrom'] = 0;
	    		$data['Page']['validTo'] = 0;
	    		$data['Page']['published'] = 0;
	    		$data['Page']['dateCreated'] = 0;
	    		$data['Page']['dateLastChange'] = 0;
	    		
	    		$this->Page->create();
	    		$this->Page->save($data);
	    		
	    		$data = $this->Page->find('first', array('conditions' => array('Page.title' => $pagename)));
	    		$pageid = $data['Page']['id'];
	    		
	    		$data = null;
	    		
	    		$data['MenuEntry']['parent_id'] = 1;
	    		$data['MenuEntry']['role_id'] = 2;
	    		$data['MenuEntry']['page_id'] = $pageid;
	    		$data['MenuEntry']['validFrom'] = 0;
	    		$data['MenuEntry']['validTo'] = 0;
	    		$data['MenuEntry']['order'] = 1;
	    		$data['MenuEntry']['published'] = 0;
	    		
	    		$this->MenuEntry->create();
	    		$this->MenuEntry->save($data);
	    		
	    		print "Save '".$pagename."'...";
	    	} // if
	    
	    } // function setPage
	    
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
	            $this->set('page', $jsonString);
	        }
	        $this->render('page');
	    } // function respond
	 
	} // class MenuManagerController
	
?>