<?php

class discordWebhooksAnnounce_adminOptionForumNodes {
	public static function renderView(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit){
		$t = $preparedOption['option_value'];
		
		$fn = [];
		$fn2 = XenForo_Model::create('XenForo_Model_Node')->getAllNodes();
		$fnref = array_keys(XenForo_Model::create('XenForo_Model_Node')->getNodeDataForListDisplay(false,0)['nodeParents']);
		foreach($fnref as $nid){
			if(array_key_exists($nid,$fn2)){
				$fn[$nid] = $fn2[$nid];
			}
		}
		
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		$fn
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		
		$forums = [];
		
		foreach($fn as $f){
			if($f['node_type_id']=='Forum' || $f['node_type_id']=='Category'){
				$forums[] = [
					'id'=>$f['node_id'],
					'nm'=>$f['title'],
					'dp'=>$f['depth'],
					'nf'=>$f['node_type_id']!='Forum',
					'ck'=>in_array($f['node_id'],$t),
				];
			}
		}
		
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		//$fn
		$forums
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		
		$editLink = $view->createTemplateObject('option_list_option_editlink', array(
			'preparedOption' => $preparedOption,
			'canEditOptionDefinition' => $canEdit
		));
		return $view->createTemplateObject('kiror_option_template_discord_wh_forum_nodes_bc', array(
			'fieldPrefix' => $fieldPrefix,
			'listedFieldName' => $fieldPrefix . '_listed[]',
			'preparedOption' => $preparedOption,
			'formatParams' => $preparedOption['formatParams'],
			'editLink' => $editLink,
			
			'forums' => $forums,
		));
	}
	
	protected static function _isNode($node){
		return (
			!empty($node) &&
			is_array($node) &&
			array_key_exists('node_id', $node) &&
			array_key_exists('parent_node_id', $node)
		);
	}
	
	public static function validate(array &$templates, XenForo_DataWriter $dw, $fieldName){
		$output = array();
		
		foreach($templates as $k=>$v)
			if($v=='on')
				$output[]=$k;
		
		$templates = $output;
		
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		$templates
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		
		return true;
	}
}
