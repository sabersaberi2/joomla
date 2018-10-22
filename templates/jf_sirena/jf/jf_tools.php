<?php
/**
* @version		JF_PDT_090
* @author		JoomForest http://www.joomforest.com
* @copyright	Copyright (C) 2011-2016 JoomForest.com
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Error Report
// ini_set('display_errors', 'On');
// error_reporting(E_ALL | E_STRICT);

JHtml::_('jquery.framework');


/*#######
#########
##  1  ##	[FIGUREOUT]
#########
#######*/
/** START ---------------------------------------------------------------------------------------------------------------------------------*/
	$jf_doc 			= JFactory::getDocument();
	$jf_base 			= JURI::base(true);
	$jf_assets_path 	= $jf_base.'/templates/'.$gantry['theme.name'].'/jf/assets/';
/** END   ---------------------------------------------------------------------------------------------------------------------------------*/




/*#######
#########
##  2  ##	[REMOVE BULLSHIT]
#########
#######*/
/** START ---------------------------------------------------------------------------------------------------------------------------------*/
	// GLOBAL GET SCRIPTS & STYLESHEETS
		$headerdata 	= $jf_doc->getHeadData();
		$styleSheets 	= $headerdata['styleSheets'];
		$headerdata['styleSheets'] = array();
		$scripts 		= $headerdata['scripts'];
		$headerdata['scripts'] = array();
	
	// REMOVE ############ - [FONTAWESOME.CSS]
		// search - for attached stylesheet
			$search_fontawesome = 'media\/gantry5\/assets\/css\/font-awesome';
		// remove - attached stylesheet
			//unset($this->_styleSheets[JURI::root(true).'/media/gantry5/assets/css/font-awesome.min.css']);
			foreach ($styleSheets as $url => $type) {
				if (preg_match('/'.$search_fontawesome.'/i', $url)) {
					// $headerdata['styleSheets'][$url] = $type;
					unset($this->_styleSheets[$url]);
					// $jf_doc->addScriptDeclaration('alert("boom + '.$url.'");');
				}
			}
		
	// REMOVE ############ - [BOOTSTRAP-GANTRY.CSS]
		// search - for attached stylesheet
			$search_bootstrap_gantry = 'media\/gantry5\/assets\/css\/bootstrap-gantry';
		// remove - attached stylesheet
			//unset($this->_styleSheets[JURI::root(true).'/media/gantry5/assets/css/bootstrap-gantry.css']);
			foreach ($styleSheets as $url => $type) {
				if (preg_match('/'.$search_bootstrap_gantry.'/i', $url)) {
					// $headerdata['styleSheets'][$url] = $type;
					unset($this->_styleSheets[$url]);
					// $jf_doc->addScriptDeclaration('alert("boom + '.$url.'");');
				}
			}
	
	// REMOVE ############ - [OTHERS]
		// unset($this->_styleSheets[JURI::root(true).'/media/gantry5/engines/nucleus/css-compiled/nucleus.css']);
		// unset($this->_styleSheets[JURI::root(true).'/media/gantry5/engines/nucleus/css-compiled/joomla.css']);
	
	// REMOVE ############ - [ICONMOON.CSS]
		unset($this->_styleSheets[JURI::root(true).'/media/jui/css/icomoon.css']);
	
	// REMOVE ############ - [CAPTION.JS]
		// search - for attached script
			$search_caption = 'media\/system\/js\/caption';
		// remove - attached script
			foreach ($scripts as $url => $type) {
				if (preg_match('/'.$search_caption.'/i', $url)) {
					// $headerdata['scripts'][$url] = $type;
					unset($this->_scripts[$url]);
					// $jf_doc->addScriptDeclaration('alert("boom + '.$url.'");');
				}
			}
		// remove - inline script
			if (isset($this->_script['text/javascript'])){
				$this->_script['text/javascript'] = preg_replace('%jQuery\(\window\)\.on\(\'load\',\s*function\(\)\s*{\s*new\s*JCaption\(\'img.caption\'\);\s*}\);\s*%', '', $this->_script['text/javascript']);
				if (empty($this->_script['text/javascript']))
				unset($this->_script['text/javascript']);
			}
	
	// REMOVE ############ - [BOOTSTRAP.JS]
		unset($this->_scripts[JURI::root(true).'/media/jui/js/bootstrap.min.js']);
		// FOR FRONTENT EDITING AND TAB STATE
			// search - for attached stylesheet
				$tabs_state = 'media\/system\/js\/tabs-state';
			// remove - attached stylesheet
				//unset($this->_styleSheets[JURI::root(true).'/media/gantry5/assets/css/font-awesome.min.css']);
				foreach ($scripts as $url => $type) {
					if (preg_match('/'.$tabs_state.'/i', $url)) {
						// DO NITHING< FOUND !!!
						$jf_doc->addScript(JURI::root(true).'/media/jui/js/bootstrap.min.js');
					}
				}
				
	// REMOVE ############ - [bootstrap-rtl.CSS]
		unset($this->_styleSheets[JURI::root(true).'/media/jui/css/bootstrap-rtl.css']);
/** END   ---------------------------------------------------------------------------------------------------------------------------------*/






/*#######
#########
##  3  ##	[JF Template CORE: STYLES]
#########
#######*/
/** START ---------------------------------------------------------------------------------------------------------------------------------*/
	// JF - STYLES
		
	// JF - CROSS BROSER
	
	// JF - RTL DIRECTION
/** END  ---------------------------------------------------------------------------------------------------------------------------------*/



/*#######
#########
##  4  ##	[JF Template CORE: SCRIPTS]
#########
#######*/
/** START ---------------------------------------------------------------------------------------------------------------------------------*/
	$jf_doc->addScript($jf_assets_path.'js/jf.min.js');
	$jf_doc->addStyleSheet($jf_assets_path.'css/jf_custom.css');
	// $jf_doc->addScriptDeclaration('alert("loaded \'jf_tools.php\' ");');
/** END   ---------------------------------------------------------------------------------------------------------------------------------*/




/*#######
#########
##  5  ##	[JF PARAMS: MAIN FEATURES]
#########
#######*/
/** START -------------------------------------------------------------------------------------------------------------------------*/
// JF - jQuery SCRIPT
		// if($this->template->get('jf_es_jQuery')){
			// $jf_doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
			// $jf_doc->addScriptDeclaration('alert("loaded - jf_es_jQuery");');
		// }
	
	// JF - jQuery noConflict
		// if($this->template->get('jf_es_jQuerynoConflict')){
			// $jf_doc->addScriptDeclaration('jQuery.noConflict();');
			// $jf_doc->addScriptDeclaration('alert("loaded - jf_es_jQuerynoConflict");');
		// }
	
/** END   -------------------------------------------------------------------------------------------------------------------------*/

/*#######
#########
##  6  ##	[JF PARAMS: STYLES]
#########
#######*/
/** START -------------------------------------------------------------------------------------------------------------------------
	// JF - Google Web Font Library
	
	// JF - Unlimited Colors
	
	// JF - CUSTOM CSS STYLES feature
/** END   -------------------------------------------------------------------------------------------------------------------------*/

