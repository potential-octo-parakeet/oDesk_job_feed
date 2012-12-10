<?php
/**
* Plugin Name: oDesk Job Feeds
* Plugin URI: http://blog.mvcejas.com/odesk-job-feed-wordpress-plugin
* Description: oDesk job feed let you see the latest jobs posted on oDesk.
* Author: Mar Cejas
* Version: 1.0
* Author URI: mvcejas.com
*/

add_action('wp_enqueue_scripts','oStyle');
add_shortcode('oDeskFeed','oJobs');

function oStyle(){
  wp_register_style('style',plugins_url('style.css',__FILE__));
	wp_enqueue_style('style');
}

function categories($cat){
	is_array($cat) ? extract($cat) : 0;	
	switch($cat){
		case 0: 
			return 'https://www.odesk.com/jobs/rss?c1=Web+Development';
			break;
		case 1: 
			return 'https://www.odesk.com/jobs/rss?c1=Software+Development';
			break;
		case 2:
			return 'https://www.odesk.com/jobs/rss?c1=Networking+%26+Information+Systems';
			break;
		case 3:
			return 'https://www.odesk.com/jobs/rss?c1=Customer+Service';
			break;
		case 4: 
			return 'https://www.odesk.com/jobs/rss?c1=Business+Services';
			break;
		case 5:
			return 'https://www.odesk.com/jobs/rss?c1=Administrative+Support&c2[0]=Data+Entry';
			break;
		case 6:
			return 'https://www.odesk.com/jobs/rss?c1=Writing+%26+Translation';
			break;
		case 7:
			return 'https://www.odesk.com/jobs/rss?c1=Administrative+Support';
			break;
		case 8: 
			return 'https://www.odesk.com/jobs/rss?c1=Design+%26+Multimedia';
			break;
		case 9:
			return 'https://www.odesk.com/jobs/rss?c1=Sales+%26+Marketing';
			break;
		default:
			return 'https://www.odesk.com/jobs/rss?c1=Web+Development';
			break;
	}
}

function oJobs($cat = 0){
	$o = '<div id="oDeskJobFeed">';
	$o.= '<ul class="oFeedList">';
	if($rss = simplexml_load_file(categories($cat))){
		foreach($rss->channel->item as $item):
			$o.= '<li>';			
			$o.= '<h3><a href="'.$item->link.'" target=_blank>'.$item->title .'</a></h3>';			   
			$o.= '<i>Job posted on '.$item->pubDate. '</i>';
			$o.=  content($item->description);
			$o.= '</li>';
		endforeach; 
		if(!isset($item)) 
			$o.= '<li><h2>No latest jobs at this category. Try browsing on other categories.</h2></li>';
	}
	else{
		$o.= '<li><h2>System undergoing maintenance. Please try again later.</h2></li>';
	}
	$o.= '</ul>';
	$o.= '</div>';
	return $o;
}

function content($str){
	$dom = new DOMDocument();
	$dom->preserveWhitespace = FALSE;
	$dom->loadHTML($str);
	
	foreach($dom->getElementsByTagName('a') as $anchor){			
			
			if($anchor->nodeValue=='click to apply'){	
				$anchor->setAttribute('class','btn btn-success');
				$anchor->setAttribute('rel','nofollow');
				$anchor->setAttribute('target','_blank');
			}
			else{
				$href = $anchor->attributes->getNamedItem('href')->value;
				$href = preg_replace("/https\:\/\/www\.odesk\.com\/leaving\_odesk\.php\?ref\=/",'',$href);
				$anchor->setAttribute('href',rawurldecode(urldecode($href)));
				$anchor->removeAttribute('title');
			}			
			
	}

	$c = '';
	$body = $dom->getElementsByTagName('body')->item(0);
	foreach($body->childNodes as $child) {
		$c .= $body->ownerDocument->saveHTML($child);
	}

	return $c;	
}
?>