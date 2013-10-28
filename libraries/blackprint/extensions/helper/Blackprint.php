<?php
/**
 * General Blackprint helper.
 *
 * This helper includes some method overrides from blackprint library's Html helper.
 * In fact, it extends Lithium's Html helper to provide some pretty useful methods
 * for view templates.
 *
*/
namespace blackprint\extensions\helper;

use blackprint\extensions\storage\FlashMessage;

use lithium\template\View;
use lithium\core\Libraries;

class Blackprint extends \lithium\template\helper\Html {

	/**
	 * We want to use our own little helper so that everything is shorter to write and
	 * so we can use fancier messages with JavaScript.
	 *
	 * @param $options
	 * @return HTML String
	*/
	public function flash($options=array()) {
		$defaults = array(
			'key' => 'flashMessage',
			// options for the layout template, some of these options are specifically for the pnotify jquery plugin
			'options' => array(
				'type' => 'growl',
				'fade_delay' => '8000',
				'pnotify_opacity' => '.8'
			)
		);
		$options += $defaults;

		$message = '';

		$flashMessage = FlashMessage::read($options['key']);
		if (!empty($flashMessage)) {
			$message = $flashMessage;
			FlashMessage::clear($options['key']);
		}

		$view = new View(array(
			'paths' => array(
				'template' => '{:library}/views/elements/{:template}.{:type}.php',
				'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php'
			)
		));

		return $view->render('all', array('options' => $options['options'], 'message' => $message), array(
			'library' => 'blackprint',
			'template' => 'flash_message',
			'type' => 'html',
			'layout' => 'blank'
		));
	}

	/**
	 * A little helpful method that returns the current URL for the page.
	 *
	 * @param $include_domain Boolean Whether or not to include the domain or just the request uri (true by default)
	 * @param $include_querystring Boolean Whether or not to also include the querystring (true by default)
	 * @return String
	*/
	public function here($include_domain=true, $include_querystring=true, $include_paging=true) {
		$pageURL = 'http';
		if ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) {$pageURL .= 's';}
		$pageURL .= '://';
		if ($_SERVER['SERVER_PORT'] != '80') {
			$pageURL .= $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}

		if($include_domain === false) {
			$pageURL = $_SERVER['REQUEST_URI'];
		}

		// always remove the querystring, we'll tack it back on at the end if we want to keep it
		if($include_querystring === false) {
			parse_str($_SERVER['QUERY_STRING'], $vars);
			unset($vars['url']);
			$querystring = http_build_query($vars);
			if(!empty($querystring)) {
				$pageURL = substr($pageURL, 0, -(strlen($querystring) + 1));
			}
		}

		// note, this also ditches the querystring
		if($include_paging === false) {
			$base_url = explode('/', $pageURL);
			$base_url = array_filter($base_url, function($val) { return (!stristr($val, 'page:') && !stristr($val, 'limit:')); });
			$pageURL = implode('/', $base_url);
		}

		return $pageURL;
	}

	/**
	 * Basic date function.
	 * TODO: Make or find a better one
	 *
	 * @param $value The date object from MongoDB (or a unix time, ie. MongoDate->sec)
	 * @param $format The format to return the date in
	 * @return String The parsed date
	*/
	public function date($value=null, $format='Y-M-d h:i:s') {
		$date = '';
		if(is_object($value)) {
			$date = date($format, $value->sec);
		} elseif(is_numeric($value)) {
			$date = date($format, $value);
		} elseif(!empty($value)) {
			$date = $value;
		}
		return $date;
	}

	/**
	 * A pretty date function that displays time as, "X days ago" or "minutes ago" etc.
	 *
	 * @param mixed $value The date object from MongoDB (or a unix timestamp)
	 * @return string The parsed date with "ago" language
	*/
	public function dateAgo($value=null){
		$querydate = date('ymdHi');
		if(is_object($value)) {
			$querydate = date('ymdHi', $value->sec);
		} elseif(is_numeric($value)) {
			$querydate = date('ymdHi', $value);
		}
		$date_string = '';

		$minusdate = date('ymdHi') - $querydate;
		if($minusdate > 88697640 && $minusdate < 100000000){
			$minusdate = $minusdate - 88697640;
		}
		switch ($minusdate) {
			case ($minusdate < 99):
						if($minusdate == 1){
							$date_string = '1 minute ago';
						}
						elseif($minusdate > 59){
							$date_string =  ($minusdate - 40).' minutes ago';
						}
						elseif($minusdate > 1 && $minusdate < 59){
							$date_string = $minusdate.' minutes ago';
						}
			break;
			case ($minusdate > 99 && $minusdate < 2359):
						$flr = floor($minusdate * .01);
						if($flr == 1){
							$date_string = '1 hour ago';
						}
						else {
							$date_string =  $flr.' hours ago';
						}
			break;
			case ($minusdate > 2359 && $minusdate < 310000):
						$flr = floor($minusdate * .0001);
						if($flr == 1){
							$date_string = '1 day ago';
						}
						else{
							$date_string =  $flr.' days ago';
						}
			break;
			case ($minusdate > 310001 && $minusdate < 12320000):
						$flr = floor($minusdate * .000001);
						if($flr == 1){
							$date_string = "1 month ago";
						}
						else{
							$date_string =  $flr.' months ago';
						}
			break;
			case ($minusdate > 100000000):
					$flr = floor($minusdate * .00000001);
					if($flr == 1){
							$date_string = '1 year ago.';
					}
					else{
							$date_string = $flr.' years ago';
					}
			}
		return $date_string;
	}

	/**
	 * Renders a compact search form using queryForm()
	 * This could be replicated without this method, but this makes it easier.
	 *
	 * @return [type] [description]
	 */
	public function compactSearch($options=array()) {
		$options += array(
			'formClass' => 'form-search',
			'placeholder' => 'search...'
		);
		$output = '<style type="text/css">.compact-search button.add-on { height: inherit !important; }</style>';
		$options['divClass'] = 'input-append compact-search';
		$options['buttonClass'] = 'btn add-on';
		$options['inputClass'] = '';
		$options['buttonLabel'] = '<i class="icon-search"></i>';

		$output .= $this->queryForm($options);
		return $output;
	}

	/**
	 * A generic form field input that passes a querystring to the URL for the current page.
	 * Great for search boxes.
	 *
	 * @options Array Various options for the form and HTML
	 * @return String HTML and JS for the form
	*/
	public function queryForm($options=array()) {
		$options += array(
			'key' => 'q',
			'formClass' => 'form-search form-inline',
			'inputClass' => 'form-control search-query',
			'buttonClass' => 'btn btn-default',
			'labelClass' => '',
			'buttonLabel' => 'Submit',
			'div' => true,
			'divClass' => '',
			'label' => false,
			'bootstrapPrepend' => false,
			'bootstrapAppend' => false,
			'placeholder' => ''
		);
		$output = '';

		$form_id = sha1('asd#@jsklvSx893S@gMp8oi' . time());

		$output .= ($options['div']) ? '<div class="' . $options['divClass'] . '">':'';
			$output .= (!empty($options['label'])) ? '<label class="' . $options['labelClass'] . '">' . $options['label'] . '</label>':'';
			$output .= '<form role="form" class="' . $options['formClass'] . '" id="' . $form_id . '" onSubmit="';
			$output .= 'window.location = window.location.href + \'?\' + $(\'#' . $form_id . '\').serialize();';
			$output .= '">';
				$output .= '<div class="form-group">';
				$output .= ($options['bootstrapAppend']) ? '<div class="input-append">':'';
				$output .= ($options['bootstrapPrepend']) ? '<div class="input-prepend">':'';

				$value = (isset($_GET[$options['key']])) ? $_GET[$options['key']]:'';
				if($options['bootstrapPrepend'] === true) {
					$output .= '<button type="submit" class="' . $options['buttonClass'] . '">' . $options['buttonLabel'] . '</button>';
				}

				$output .= '<input type="text" placeholder="' . $options['placeholder'] . '" name="' . $options['key'] . '" value="' . $value . '" class="' . $options['inputClass'] . '" />';

				if($options['bootstrapPrepend'] === false) {
					$output .= '<button type="submit" class="' . $options['buttonClass'] . '">' . $options['buttonLabel'] . '</button>';
				}

				$output .= ($options['bootstrapAppend']) ? '</div>':'';
				$output .= ($options['bootstrapPrepend']) ? '</div>':'';
				$output .= '</div>';
			$output .= '</form>';

		$output .= ($options['div']) ? '</div>':'';

		return $output;
	}

	/**
	 * Encodes a URL so it can be used as an argument.
	 * The retruned string will not contain any slashes that could be mistaken for a route
	 * and will also not include any extension like .php etc. which could also be mistaken
	 * for an atual URL rather than an argument being passed to an action.
	 *
	 * @param string $url
	 * @return string
	*/
	public function urlAsArg($url=null) {
		return strtr(base64_encode(addslashes(gzcompress(serialize($url),9))), '+/=', '-_,');
	}

	/**
	 * Unescpaes code in <code> elements that have been escaped by JavaScript
	 * to avoid TinyMCE cleanup.
	 */
	public function containsSyntax($content=null) {
		if($content) {
			$content = preg_replace_callback('/(\<pre\>\<code.*\>)(.*)(\<\/code\>\<\/pre>)/i', function($matches) {
				if(isset($matches[0])) { return $matches[1] . urldecode($matches[2]) . $matches[3]; } }, $content);
			//$content = rawurldecode($content);

		}

		return $content;
	}

}
?>