<?php
class AppsHelper {

	static function getAJAXUrl($fragment) {
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$route_prefix		= $componentParams->get('route_prefix', 'index.php?option=com_apps&format=json');
		
		if (!$route_prefix) {
			return $fragment;
		}
		
		$uri = JURI::getInstance($route_prefix);
		$query = $uri->getQuery();
		$query .= '&'.$fragment;
		$uri->setQuery($query);
		$url = $uri->toString();
		
		return $url;
	}
}
