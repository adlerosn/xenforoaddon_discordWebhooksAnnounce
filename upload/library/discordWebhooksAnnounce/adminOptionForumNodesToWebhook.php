<?php

class discordWebhooksAnnounce_adminOptionForumNodesToWebhook {
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
		$t
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
					'ph'=>$f['node_type_id']=='Forum'?'Webhook URL':'============',
					'ck'=>in_array($f['node_id'],$t),
					'wh'=>array_key_exists($f['node_id'],$t)?implode('|',$t[$f['node_id']]):''
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
		return $view->createTemplateObject('kiror_option_template_discord_wh_forum_nodes_each', array(
			'fieldPrefix' => $fieldPrefix,
			'listedFieldName' => $fieldPrefix . '_listed[]',
			'preparedOption' => $preparedOption,
			'formatParams' => $preparedOption['formatParams'],
			'editLink' => $editLink,
			
			'forums' => $forums,
		));
	}
	
	public static function validate(array &$fields, XenForo_DataWriter $dw, $fieldName){
		$output = array();
		
		foreach($fields as $k=>$vs){
			foreach(explode('|',$vs) as $v){
				if(substr($v,0,4)=='http'){
					if(!array_key_exists($k,$output)){
						$output[$k]=[];
					}
					$output[$k][]=$v;
				}
			}
		}
		
		$fields = $output;
		
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		$fields
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		
		return true;
	}
}
