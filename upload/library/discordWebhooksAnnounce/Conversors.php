<?php

class discordWebhooksAnnounce_Conversors {
	const USERFIELD_DISCORD = 'da_discord_id';
	public static function html2plain($html){
		$plain = '';
		$isntHtml = true;
		$htmlArr = str_split($html);
		foreach($htmlArr as $char){
			if($char=='<'){
				$isntHtml = false;
				continue;
			}
			if($char=='>'){
				$isntHtml = true;
				continue;
			}
			if($isntHtml){
				$plain.=$char;
			}
		}
		return $plain;
	}
	public static function markdownEscape($markdown){
		$markdown=str_replace('\\','\\\\',$markdown);
		$markdown=str_replace('*','\\*',$markdown);
		$markdown=str_replace('_','\\_',$markdown);
		$markdown=str_replace('~','\\~',$markdown);
		$markdown=str_replace('`','\\`',$markdown);
		return $markdown;
	}
	public static function markdownUnescape($markdown){
		$markdown=str_replace('\\*','*',$markdown);
		$markdown=str_replace('\\_','_',$markdown);
		$markdown=str_replace('\\~','~',$markdown);
		$markdown=str_replace('\\`','`',$markdown);
		$markdown=str_replace('\\\\','\\',$markdown);
		return $markdown;
	}
	public static function bbcode2discord($bbcode){
		$conv = [];
		$bbcode = static::markdownEscape($bbcode);
		foreach(['b'=>'**','i'=>'*','u'=>'__','s'=>'~~'] as $bbc=>$md){
			$conv['['.strtolower($bbc).']']=$md;
			$conv['[/'.strtolower($bbc).']']=$md;
			$conv['['.strtoupper($bbc).']']=$md;
			$conv['[/'.strtoupper($bbc).']']=$md;
		}
		foreach($conv as $from=>$to){
			$bbcode=str_replace($from,$to,$bbcode);
		}
		$matches = [];
		preg_match_all('/\[color=\'?"?(#?[0-9a-fA-F]+)\'?"?\]/i',$bbcode,$matches);
		foreach($matches as $match){
			if(count($match)<1) continue;
			$bbcode=str_replace($match[0],'',$bbcode);
		}
		$matches = [];
		preg_match_all('/\[\/color]/i',$bbcode,$matches);
		foreach($matches as $match){
			if(count($match)<1) continue;
			$bbcode=str_replace($match[0],'',$bbcode);
		}
		$matches = [];
		$xmu = XenForo_Model::create('XenForo_Model_User');
		preg_match_all('/\[USER=([0-9]+)]([^\[]+)\[\/USER\]/i',$bbcode,$matches);
		for($i = 0; $i<count($matches[0]); $i++){
			$match = [$matches[0][$i],$matches[1][$i],$matches[2][$i]];
			$toRep = '@'.$match[2];
			$user = $xmu->getUserById(intval($match[1]));
			if(is_array($user) && count($user)>1){
				$toRep = '@'.$user['username'];
				if(array_key_exists(static::USERFIELD_DISCORD,$user) && $user[static::USERFIELD_DISCORD]!=null){
					$toRep = '<@'.$user[static::USERFIELD_DISCORD].'>';
				}
			}
			$bbcode=str_replace($match[0],$toRep,$bbcode);
		}
		$matches = [];
		preg_match_all('/\[URL\](.+?)\[\/URL\]/i',$bbcode,$matches);
		for($i = 0; $i<count($matches[0]); $i++){
			$match = [$matches[0][$i],$matches[1][$i]];
			$bbcode=str_replace($match[0],static::markdownUnescape($match[1]),$bbcode);
		}
		$matches = [];
		preg_match_all('/\[URL="(.+?)"\](.+?)\[\/URL\]/i',$bbcode,$matches);
		for($i = 0; $i<count($matches[0]); $i++){
			$match = [$matches[0][$i],$matches[1][$i],$matches[2][$i]];
			$bbcode=str_replace($match[0],'<'.static::markdownUnescape($match[1]).'|'.$match[2].'>',$bbcode);
		}
		$matches = [];
		preg_match_all("/\[URL='(.+?)'\](.+?)\[\/URL\]/i",$bbcode,$matches);
		for($i = 0; $i<count($matches[0]); $i++){
			$match = [$matches[0][$i],$matches[1][$i],$matches[2][$i]];
			$bbcode=str_replace($match[0],'<'.static::markdownUnescape($match[1]).'|'.$match[2].'>',$bbcode);
		}
		$matches = [];
		preg_match_all('/\[URL=(.+?)\](.+?)\[\/URL\]/i',$bbcode,$matches);
		for($i = 0; $i<count($matches[0]); $i++){
			$match = [$matches[0][$i],$matches[1][$i],$matches[2][$i]];
			$bbcode=str_replace($match[0],'<'.static::markdownUnescape($match[1]).'|'.$match[2].'>',$bbcode);
		}
		return $bbcode;
	}
}
