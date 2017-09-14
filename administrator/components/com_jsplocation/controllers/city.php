<?php
	/**
		* JSP Location components for Joomla!
		* JSP Location is an interactive store locator
		*
		* @author      $Author: Ajay Lulia $
		* @copyright   Joomla Service Provider - 2016
		* @package     JSP Store Locator 2.2
		* @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
		* @version     $Id: city.php  $
	*/
	// no direct access
	defined('_JEXEC') or die('Restricted access');
	jimport( 'joomla.application.component.controller' );
	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/'.'models'.'/'.'city.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/'.'models'.'/'.'state.php');
	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/'.'models'.'/'.'country.php');
	class jspLocationControllerCity extends JControllerLegacy {
		function display($cachable=false,$urlparams=false){
			$model=$this->getModel('city');
			$view=$this->getView('city');
			$view->setModel( $model, true );
			$view->setLayout("list");
			$view->display();	
		}
		function add(){
			$model=$this->getModel('city');
			$view=$this->getView('city');
			$view->setModel( $model, true );
			$view->setLayout("form");
			$view->formadd();		// newly added
		}
		function edit(){
			//$this->add();
			$model=$this->getModel('city');
			$view=$this->getView('city');
			$view->setModel( $model, true );
			$view->setLayout("form");
			$view->form();		
		}
		function save($tpl = null){
			$model=$this->getModel('city');
			$view=$this->getView('city');
			$view->setModel( $model, true );
			$view->saveData();
		}
		function apply($tpl = null){
			$this->save();
		}
		function remove(){
			$model=$this->getModel('city');		
			$view=$this->getView('city');
			$view->setModel( $model, true );
			$view->deleteData();
		}
		function publish($state = 1){
			$mainframe=Jfactory::GetApplication();
			// Initialize variables
			$db=JFactory::getDBO();
			$cid=JRequest::getVar('cid',array(),'post','array');
			JArrayHelper::toInteger($cid);
			$option=JRequest::getCmd('option');
			$task=JRequest::getCmd('task');
			$rtask=JRequest::getCmd( 'returntask', '', 'post' );
			if($rtask) {
				$rtask='&task='.$rtask;
			}
			if(count($cid)<1) {
				$redirect=JRequest::getVar( 'redirect', '', 'post', 'int' );
				$action=($state == 1) ? 'publish' : ($state == -1 ? 'archive' : 'unpublish');
				$msg=JText::_('Select an item to') . ' ' . JText::_($action);
				$mainframe->redirect('index.php?option='.$option.$rtask.'&sectionid='.$redirect, $msg, 'error');
			}
			// Get some variables for the query
			$uid	= $user->get('id');
			$total	= count($cid);
			$cids	= implode(',', $cid);
			$query = 'UPDATE #__jsplocation_city' .
			' SET published = '. (int) $state .
			' WHERE id IN ( '. $cids .' ) ';
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
			switch ($state)
			{
				case -1 :
				$msg = JText::sprintf('Item(s) successfully Archived', $total);
				break;
				case 1 :
				$msg = JText::sprintf('Item(s) successfully Published', $total);
				break;
				case 0 :
				default :
				if ($task == 'unarchive') {
					$msg = JText::sprintf('Item(s) successfully Unarchived', $total);
					} else {
					$msg = JText::sprintf('Item(s) successfully Unpublished', $total);
				}
				break;
			}
			$cache =  JFactory::getCache('com_jsplocation');
			$cache->clean();
			$mainframe->redirect('index.php?option='.$option.$rtask.'&controller=city&task=city', $msg, 'MESSAGE');
		}
		function unpublish($state = 0){
			$this->publish($state);
		}
		function getState(){
			$countryId = JRequest::getVar('country');
			if($countryId != ''){
				$model = $this->getModel("state");
				$stateData = $model->loadStateList($countryId);
				$str ='';
				if(is_array($stateData)){
					foreach ($stateData as $k => $v){
						$str .= $v->id."|".$v->title."*";
					}
					$str = substr($str,0,-1);		
				}
				echo $str;
			}
			exit;
		}
		function getCity(){
			$stateId = JRequest::getVar('state');
			$countryId = JRequest::getVar('country');
			if($stateId!=''){
				$model = $this->getModel("city");
				$cityData = $model->loadCityList($stateId,$countryId);
				$count= count($cityData);
				$str ='';
				if(is_array($cityData)){
					foreach ($cityData as $k => $v){
						$str .= $v->id."|".$v->title."*";
					}
					$str = substr($str,0,-1);        
				}
				echo $str;
			}
			exit;
		}	
	}
?>