<?php

class discordWebhooksAnnounce_userDataExtractor {
	public static function startsWith($haystack, $needle)
	{//http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
		 $length = strlen($needle);
		 return (substr($haystack, 0, $length) === $needle);
	}
	
	protected static function getCustomUserFieldsArray_noCache(){
		$dbc=XenForo_Application::get('db'); 
		$q = $dbc->fetchRow("SELECT `data_value`
							 FROM   `xf_data_registry`
							 WHERE  `data_key` = 'languages';");
		unset($dbc);
		$q=$q['data_value'];
		$oq=unserialize($q)[1]['phrase_cache'];
		
		$q=array('username'=>'User name',
				 'gender'=>'Gender',
				 'location'=>'Location',
				 'homepage'=>'Home Page',
				 'occupation'=>'Occupation',
				 'about'=>'About Me',
				 'signature'=>'Signature',
				 'dob_day'=>'DOB day',
				 'dob_month'=>'DOB month',
				 'dob_year'=>'DOB year',
		);
		$k=array_keys($oq);
		for ($i=0;$i<count($k);$i++)
		{
			if(self::startsWith($k[$i],'user_field_'))
			{
				$str=$k[$i];
				$len=count($str);
				$str=substr($str,11);
				$q[$str]=$oq['user_field_'.$str];
			}
		}
		unset($k);
		unset($oq);
		
		return $q;
	}
	
	protected static $_cache_getCustomUserFieldsArray = null;
	public static function getCustomUserFieldsArray(){
		if(is_null(self::$_cache_getCustomUserFieldsArray))
			self::$_cache_getCustomUserFieldsArray = self::getCustomUserFieldsArray_noCache();
		return self::$_cache_getCustomUserFieldsArray;
	}
	
	public static function getFields($xfuser){
		$user = [];
		$user['username']=$xfuser['username'];
		$user['gender']=$xfuser['gender'];
		$user['location']=$xfuser['location'];
		$user['homepage']=$xfuser['homepage'];
		$user['occupation']=$xfuser['occupation'];
		$user['about']=$xfuser['about'];
		$user['signature']=$xfuser['signature'];
		$user['dob_day']=$xfuser['dob_day'];
		$user['dob_month']=$xfuser['dob_month'];
		$user['dob_year']=$xfuser['dob_year'];
		if(array_key_exists('custom_fields',$xfuser)) {
			$tcuf = unserialize($xfuser['custom_fields']);
			if (is_array($tcuf)){
				foreach(array_keys($tcuf) as $key=>$val){
					$user[$val]=$tcuf[$val];
				}
			}
		}
		//$user['ips']=$xfuser['ips'];
		return $user;
	}
}
