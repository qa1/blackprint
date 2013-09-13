<?php
namespace blackprint\models;

use lithium\util\Validator;
use lithium\util\Inflector as Inflector;
use \MongoDate;

class Asset extends \lithium\data\Model {

	// Use the gridfs in MongoDB
	protected $_meta = array(
		'source' => 'fs.files'
	);

	// I get appended to with the plugin's Asset model (a good way to add extra meta data).
	public static $fields = array(
		'_thumbnail' => array('type' => 'boolean'),
		// not technically required, but is common.
		'filename' => array('type' => 'string'),
		// file extension is not needed by Mongo, we use it for working with resizing/generating images.
		'fileExt' => array('type' => 'string'),
		// the mime-type
		'contentType' => array('type' => 'string'),
		// This represents the 'type' of asset, or what it's associated to.
		'ref' => array('type' => 'string'),
		'file' => array('label' => 'Profile Image', 'type' => 'file')
	);

	public static $validate = array(
	);

	public static function __init() {
		self::$fields += static::$fields;
		self::$validate += static::$validate;

		parent::__init();
	}
}

/* FILTERS
 *
*/
Asset::applyFilter('save', function($self, $params, $chain) {
	// Set the mime-type based on file extension.
	// This is used in the Content-Type header later on.
	// Doing this here in a filter saves some work in other places and all
	// that's required is a file extension.
	$ext = isset($params['entity']->fileExt) ? strtolower($params['entity']->fileExt):null;
	switch($ext) {
		default:
			$mimeType = 'text/plain';
		break;
		case 'jpg':
		case 'jpeg':
			$mimeType = 'image/jpeg';
		break;
		case 'png':
			$mimeType = 'image/png';
		break;
		case 'gif':
			$mimeType = 'image/gif';
		break;
	}
	$params['data']['contentType'] = $mimeType;

	return $chain->next($self, $params, $chain);
});

// Second, let's get the validation rules picked up from our $validate property
Asset::applyFilter('validates', function($self, $params, $chain) {
	$params['options']['rules'] = Asset::$validate;
	return $chain->next($self, $params, $chain);
});
?>