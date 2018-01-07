<?php

class discordWebhooksAnnounce_PlainFormatter extends XenForo_BbCode_Formatter_Text {
	public function handleTagQuote(array $tag, array $rendererStates){
		$output = $this->renderSubTree($tag['children'], $rendererStates);
		return "\n««" . trim($output) . "»»\n";
	}
	
	public function handleTagList(array $tag, array $rendererStates){
		$bullets = explode('[*]', trim($this->renderSubTree($tag['children'], $rendererStates)));

		$output = '';
		foreach ($bullets AS $bullet){
			$bullet = trim($bullet);
			if($bullet !== '')
				$output .= '• ' . $bullet . "\n";
		}

		return $output;
	}
}
