<?php

	class LayoutManagerController extends Controller {
		
		var $name = "LayoutManager";
	    var $components = array('RequestHandler');
	    var $layout = 'ajax';
	    var $autoRender=false;
	 
	    function index() {
	        $this->redirect("../");
	    } // function index
	    
	    function stringCall() {
	        $this->respond('Einfacher Text');
	    } // function stringCall
	    
	    function getContainerID(){
	    	$this->loadModel('Container');
	    	$this->loadModel('Content');
	    	
	    	$data = $this->Container->query('SELECT max(id) FROM containers');
	    	$containerid = $data[0][0]['max(id)']+1; 
	    	$data = $this->Content->query('SELECT max(id) FROM contents');
			$contentid = $data[0][0]['max(id)']+1;
	    	
	    	$this->set('layout', $containerid.'|'.$contentid);
	    	$this->render('layout');
	    } // function getContainerID
	 
	    function jsonCall() {
	    	
        	$this->loadModel('LayoutType');
	    	
	    	$data = $this->LayoutType->find('all');
	    	
	    	$layout = array();
	    	for ($i = 0; $i < sizeof($data); $i++) {
	    		$layout[] = $data[$i]['LayoutType'];
	    	} // for
	    	
	    	$this->respond($layout,true);
	    	/*
	        $this->respond(array(	array('name'=>array('test'=>'test'), 'value'=>'50:50'),
	        						array('name'=>'Left', 'value'=>'25:75'),
	        						array('name'=>'Normal', 'value'=>'10:90'),
	        						array('name'=>'Normal', 'value'=>'33:33:33'),
	        						array('name'=>'Normal', 'value'=>'50:90')
	        						), true);
	        */
	    } // function jsonCall
	    
	    function getLayoutTree($pagename='',$respond=true){
	    	
	    	if(empty($pageid)){
	    		$pagedata = $this->getPageID($this->request->query['pagename']);
	    	}else{
	    		$pagedata = $this->getPageID($pagename);
	    	} // if
	    	
    		$pageid = $pagedata['id'];
    		$pagecontainerid = $pagedata['container_id'];
	    	
	    	$this->loadModel('Container');
	    	$this->loadModel('LayoutType');
	    	$this->loadModel('Content');
	    	
	    	$data = $this->Container->find('first', array('conditions' => array('Container.id' => $pagecontainerid)));

	    	$layoutTree['layoutobjects']['containers'][] = $data['Container'];

    		
    		for ($i = 0; $i < sizeof($data['ChildContainer']); $i++) {
    			$layoutTree['layoutobjects']['containers'][] = $data['ChildContainer'][$i];
    			$layoutTree = $this->getLayoutNode($data['ChildContainer'][$i]['id'],$layoutTree);
    		} // for
    		
    		if(sizeof($data['Content'] > 0)){
    			for ($k = 0; $k < sizeof($data['Content']); $k++) {
    				$layoutTree['layoutobjects']['plugins'][] = $data['Content'][$k];
    			} // for
    		} // if
    		
    		for ($i = 0; $i < sizeof($layoutTree['layoutobjects']['containers']); $i++) {
    			$pluginsid[] =  $layoutTree['layoutobjects']['containers'][$i]['id'];
    		} // for
    		
    		$data = $this->LayoutType->find('all');
    		for ($i = 0; $i < sizeof($data); $i++) {
    			$layout[$i]['id'] = $data[$i]['LayoutType']['id'];
    			$layout[$i]['weight'] = $data[$i]['LayoutType']['weight'];
    		} // for
    		
    		if($respond){
    			$layoutTree['layoutobjects']['layouts'] = $layout;
	    		$this->respond($layoutTree,true);
    		}else{
    			return $layoutTree;
    		} // if
	    } // function getLayoutTree
	    
	    function getLayoutNode($id,$layoutTree){
	    	$this->loadModel('Container');
	    	$data = $this->Container->find('first', array('conditions' => array('Container.id' => $id)));
	    	
	    	if(sizeof($data['ChildContainer']) > 0){
	    		for ($i = 0; $i < sizeof($data['ChildContainer']); $i++) {
	    			$layoutTree['layoutobjects']['containers'][] = $data['ChildContainer'][$i];
	    			if(sizeof($data['Content'] > 0)){
	    				for ($k = 0; $k < sizeof($data['Content']); $k++){
	    					$layoutTree['layoutobjects']['plugins'][] = $data['Content'][$k];
	    				} // for
    				} // if
	    			$layoutTree = $this->getLayoutNode($data['ChildContainer'][$i]['id'],$layoutTree);
	    		} // for
	    	}else{
	    		if(sizeof($data['Content'] > 0)){
	    			for ($k = 0; $k < sizeof($data['Content']); $k++){
	    				$layoutTree['layoutobjects']['plugins'][] = $data['Content'][$k];
	    			} // for
    			} // if
	    	} //if
	    	return $layoutTree;
	    } // function getLayoutNode
	    
	    function savePageLayout(){
	    	
	    	$pagename = $this->request->query['pagename'];
	    	$json = $this->request->query['jsonstring'];
	    	$pageid = $this->getPageID($pagename);
	    	$this->loadModel('Container');
	    	$this->loadModel('Content');
	    	$this->loadModel('Page');
	    	
	    	$jsonArray = json_decode($json,true);
	    	
	    	
	    	if(!empty($pageid)){
	    		
	    		$data = $this->getLayoutTree($pageid,false);
	    		if(!empty($data['layoutobjects']['containers'][0])){
	    			$containers['Container'] = $data['layoutobjects']['containers'];
		    		$containers['Content'] = $data['layoutobjects']['plugins'];
		    		
		    		$this->Page->query('UPDATE pages SET container_id=null WHERE container_id='.$containers['Container'][0]['id']);
	    		} //if
	    		
	    		for ($i = sizeof($containers['Content'])-1; $i >= 0; $i--) {
	    			$this->Content->delete($containers['Content'][$i]['id']);
	    		} // for
	    		for ($i = sizeof($containers['Container'])-1; $i >= 0; $i--) {
	    			$this->Container->delete($containers['Container'][$i]['id']);
	    		} // for
	    		
	    	} // if
	    	
	    	$this->loadModel('Container');
	    	$this->loadModel('Content');
	    	
	    	
	    	for ($i = 0; $i < sizeof($jsonArray['layoutobjects'][0]['containers']); $i++) {
	    		$container['Container'] = $jsonArray['layoutobjects'][0]['containers'][$i];
	    		
		    	if($i == 0){
		    		$this->Page->query('UPDATE pages SET container_id=null WHERE container_id='.$container['Container']['id']);

		    		unset($container['Container']['parent_id']);
		    		unset($container['Container']['layout_type_id']);
		    	} // if
		    	
	    		$this->Container->create();
	    		$this->Container->save($container);
	    		
	    	} // for
	    	
	    	for ($i = 0; $i < sizeof($jsonArray['layoutobjects'][1]['plugins']); $i++) {
	    		
	    		$plugins['Content'] = $jsonArray['layoutobjects'][1]['plugins'][$i];
	    		$this->Content->create();
	    		$this->Content->save($plugins);
	    		
	    	} // for
	    	
	    	$this->loadModel('Page');
	    	$this->Page->read(null,$pageid);
	    	$this->Page->set('container_id',$jsonArray['layoutobjects'][0]['containers'][0]['id']);
	    	$this->Page->save();
	    	
	    } // function savePageLayout
	    
	    function getContentList(){
	    	
	    	$this->loadModel('contents');
	    	$data = $this->contents->find('all');
	    	$this->contents->delete($data[10]['contents']['id']);
	    	
	    	print "<pre>";
	    	print_r($this->contents->find('all'));die;
	    } // function getContentList
	    
	    function getPageID($pagename){
	    	$this->loadModel('Page');
	    	$data = $this->Page->find('first', array('conditions' => array('Page.title' => $pagename)));   	
	    	return $data['Page'];   	
	    } // function getPageID
	    
	    function getPluginList(){
	    	
	    	$this->loadModel('Plugin');
	    	$data = $this->Plugin->find('all');
	    	for ($i = 0; $i < sizeof($data); $i++) {
	    		$plugins[] = $data[$i]['Plugin'];
	    	} // for
	    	$this->respond($plugins,true);
	    	/*
	    	die;
	    	print "<pre>";
	    	print_r($plugins);
	    	die;
	    	*/
	    }
	    
	    function respond($jsonString=null, $json=false) {
	        if ($jsonString!=null) {
	            if ($json==true) {
	                //$this->RequestHandler->setContent('json', 'application/json');
	                $this->response->type('json');
	                $jsonString=json_encode($jsonString);
	            }
	            $this->set('layout', $jsonString);
	        }
	        $this->render('layout');
	    } // function respond
	 
	} // class LayoutManagerController
	
?>