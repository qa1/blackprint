<?php
/*
 * A general utility class. This includes several useful methods used throughout the app.
*/
namespace blackprint\extensions;

use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;
use lithium\core\Libraries;
use lithium\util\Set;
use lithium\util\Inflector;

class Util {

	/*
	 * in_array recursive function using Spl libraries. Quite useful.
	*/
	public static function inArrayRecursive($needle=null, $haystack=null) {
		if((empty($needle)) || (empty($haystack))) {
			return false;
		}
		$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));
		foreach($it AS $element) {
			if($element === $needle) {
				return true;
			}
		}
		return false;
	}

	/*
	 * A simple method to return a unique string, useful for approval codes and such.
	 * An md5 hash of the unique id will be 32 characters long and the sha1 will be 40 characters long.
	 * Without hashing, the unique id will be 13 characters long and 23 long if more entropy is used.
	 * 
	 * @params $options Array
	 *		- hash: The hash method to use to hash the uid, md5, sha1, or false (default is md5)
	 *		- prefix: The prefix to use for uniqid() method
	 *		- entropy: Boolean, whether or not to add additional entropy (more unique)
	*/
	public static function uniqueString($options=array()) {
		$options += array('hash' => 'md5', 'prefix' => '', 'entropy' => false);
		switch($options['hash']) {
			case 'md5':
				return md5(uniqid($options['prefix'], $options['entropy']));
			default:
			break;
			case 'sha1':
				return sha1(uniqid($options['prefix'], $options['entropy']));
			break;
			case false:
				return uniqid($options['prefix'], $options['entropy']);
			break;
		}
	}

	/**
	 * Generate a unique pretty url for the model's record.
	 * This should work if using MongoDB or MySQL ("documents" and "records").
	 * 
	 * @params $options Array
	 *		- url: The requested url (typically the inflector::slug() for a title)
	 *		- id: The current id (optional, only if editing a record, so it knows to exclude itself as a conflict)
	 *		- model: The model that's used as the lookup (to run the find() on)
	 *		- separator: The optional separator symbol for spaces (default: -)
	 * @return String The unique pretty url.
	*/
	public static function uniqueUrl($options=array()) {
		$options += array('url' => null, 'id' => null, 'model' => null, 'separator' => '-');
		if((!$options['url']) || (!$options['model'])) {
			return null;
		}

		// First off, all URLs are lowercase.
		$options['url'] = strtolower($options['url']);

		$records = $options['model']::find('all', array('fields' => array('url'), 'conditions' => array('url' => array('like' => '/'.$options['url'].'/'))));
		$conflicts = array();
		
		// Set all of the potential conflicts, ignoring any that match a passed id.
		// Documents should be able to be updated without having their own URL changed.
		foreach($records as $record) {
			if(is_object($record)) {
				if((string)$record->{$options['model']::key()} != (string)$options['id']) {
					$conflicts[] = $record->url;
				}
			}
			if(is_array($record)) {
				if((string)$record[$options['model']::key()] != (string)$options['id']) {
					$conflicts[] = $record['url'];
				}
			}
		}
		
		/**
		 * If there any possible conflicts and the current pretty URL to be 
		 * used exists in that set of conflicts, increment a number value 
		 * to append to the current URL until it no longer has a conflict.
		 */
		if(!empty($conflicts) && in_array($options['url'], $conflicts)) {
			$firstSlug = $options['url'];
			$i = 1;
			while($i > 0) {
				if (!in_array($firstSlug . $options['separator'] . $i, $conflicts)) {
					$options['url'] = $firstSlug . $options['separator'] . $i;
					$i = -1;
				}
			$i++;
			}
		}

		return $options['url'];
	}

	/**
	 * Formats the order array for the find() method's order option.
	 * By default uses id descending, if invalid, an empty array is returned.
	 * The order string is passed as dot separated field.direciton.
	 * ex. created.desc or created.asc
	 * Also valid: created.DESC or created.descending
	 *
	 * @param $order String The dot separated field.direction
	*/
	public static function formatDotOrder($order='id.desc') {
		$order_pieces = explode('.', $order);
		if(count($order_pieces) > 1) {
			switch(strtolower($order_pieces[1])) {
				case 'desc':
				case 'descending':
				default:
					$direction = 'desc';
					break;
				case 'asc':
				case 'ascending':
					$direction = 'asc';
					break;
			}
			return array($order_pieces[0], $direction);
		}
		return array();
	}

}
?>