<?php
namespace blackprint\extensions\adapter\security\auth;

use blackprint\extensions\oauth\storage\BlackprintTokenStorage;
use OAuth\OAuth1\Signature\Signature;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\Uri;
use OAuth\ServiceFactory;

/**
 * Foursquare authentication.
 */
class Foursquare extends \lithium\core\Object {

	/**
	 * Service name (lowercase, no special characters)
	 */
	protected $_service = 'foursquare';

	/**
	 * Full service name. Title casing, spaces, etc.
	*/
	protected $_serviceName = 'Foursquare';

	/**
	 * User infos URL
	 */
	protected $_userInfoEndpoint = 'users/self';

	/**
	 * Session storage.
	 * 
	 * @var mixed
	 */
	protected $_storage ;

	/**
	 * Constructor
	 * 
	 * @param array $config [description]
	 */
	public function __construct(array $config = array()) {
		$config += array('tokenKey' => 'oauth_token', 'scope' => array());
		$this->_storage = new BlackprintTokenStorage();
		parent::__construct($config);
	}

	public function check($request, array $options = array()) {
		$here = new Uri($request->to('url'));
		$here->setQuery('') ;
		$credentials = new Credentials(
		    $this->_config['key'],
		    $this->_config['secret'],
		    $here->getAbsoluteUri()
		);

		$serviceFactory = new ServiceFactory();
		$service = $serviceFactory->createService($this->_service, $credentials, $this->_storage, $this->_config['scope']);

		if(empty($request->query['code'])) {
		    $url = $service->getAuthorizationUri();
		    header('Location: ' . $url);
		} else {
		    $token = $service->requestAccessToken($request->query['code']);
		    $credentials = json_decode($service->request($this->_userInfoEndpoint), true);
		    $result = array('socialLogin' => array());

		    if(isset($credentials['response']) && isset($credentials['response']['user'])) {
		    	$userInfo = $credentials['response']['user'];
		    	$result = array(
		    		'socialLogin' => array(
		    			// These values will all be different based on the service...But standardize "userId", "name", and "userName" for use in
			    		// new user registration OR in user lookup for already registered users.
			    		'userId' => $userInfo['id'],
			    		'name' => trim($userInfo['firstName'] . ' ' . $userInfo['lastName']),
			    		// Even if not available, the timezone, utcOffset, and locale fields should always be set (null is ok).
			    		// Have to run a timezone lookup to match what Twitter sets, I like having a text representation and the UTC offset.
			    		'timezone' => null,
			    		'utcOffset' => null,
			    		'locale' => null,
			    		'userName' => null,
			    		// 250x250 should do nicely...That's Blackprint's max size for uploaded User profile images.
			    		'profilePicture' => $userInfo['photo']['prefix'] . '250x250' . $userInfo['photo']['suffix']
		    		)
		    	);
		    }
	
			// The 'service' needs to match the adapter name (after the blackprint_ prefix)
			$result['socialLogin']['service'] = $this->_service;
			// The 'serviceName' stored in the database is the formatted name users see, it can contain spaces, etc.
			$result['socialLogin']['serviceName'] = $this->_serviceName;
			// Some extra data about the service (used for visuals and external links in the CMS)
    		// If no icon is provided, the CMS will use the favicon from the serviceUrl
	    	$result['socialLogin']['serviceIcon'] = '<i class="fa fa-foursquare"></i>';
	    	$result['socialLogin']['serviceUrl'] = 'http://www.foursquare.com';

			// Last, but not least, store the token so we can make API calls on the user's behalf.
    		// NOTE: It has to be serialized because MongoDB doesn't like the OAuth class objects.
    		// Also note that upon each login the tokens stored on the User document (for each associated service)
    		// should be checked and refreshed if necessary. They can expire.
			$result['socialLogin']['token'] = serialize($token);

			return $result;
		}
	}

	/**
	 * Prepare the data to be stored.
	 *
	 * @param [type] $data    [description]
	 * @param array  $options [description]
	 */
	public function set($data, array $options = array()) {
		return $data;
	}

	/**
	 * Clear the token session key.
	 *
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function clear(array $options = array()) {
		$this->_storage->clearToken($this->_service);
	}
}
?>