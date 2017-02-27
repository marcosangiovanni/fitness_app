<?php

namespace AppBundle\Util;

class YoutubeManager{
	
	public function getYoutubeVideoId($complete_video_url){
		preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $complete_video_url, $matches);
		$video_id = end($matches);
		return $video_id;
	}
	
	public function isValidYoutubeVideo($complete_video_url){
		try{
			//Get video ID
			$video_id = $this->getYoutubeVideoId($complete_video_url);
			//Call youtube to check video status
			$listResponse = $this->youtubeservice->videos->listVideos('status', array('id' => $video_id));
			
			if(count($listResponse->getItems()) === 0){
				throw new \RuntimeException('Could not find video',404);
			}
	
			$infos = $listResponse['modelData']['items'][0]['status'];
				
			if(!$infos || $infos['privacyStatus'] === 'private'){
				throw new \Exception('The video is private and cannot be used',409);
			}
			return true;
		}
		catch(\Exception $e){
			return false;
		}
	}
	
}
