<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */
class CRM_Utils_Cache_Mongodb {
  /**
   * The actual mongodb collection object
   *
   * @var resource
   */
  protected $_cache;

  /**
   * Constructor
   *
   * @param array   $config  an array of configuration params
   *
   * @return void
   */
  function __construct($config) {
    try {
      $host = CRM_Utils_Array::value('host', $config, 'localhost');
      $port = CRM_Utils_Array::value('port', $config, '27017');
      $connection = new MongoClient("mongodb://{$host}:{$port}"); // connects to localhost:27017

      $db = CRM_Utils_Array::value('db', $config, 'civicrm');
      $this->_db = $connection->selectDB($db);
      $this->_cache = $this->_db->cache;
    }
    catch ( MongoConnectionException $e ) {
      echo '<p>Couldn\'t connect to mongodb, is the "mongo" process running?</p>';
      CRM_Utils_System::civiExit();
    }
  }

  function set($key, &$value) {
    $doc = array('key' => $key, 'value' => serialize($value));
    $this->_cache->insert($doc);
    return TRUE;
  }

  function &get($key) {
    $result = $this->_cache->findOne(array('key' => $key), array('value'));
    $result = @unserialize($result['value']);
    return $result;
  }

  function delete($key) {
    $result = $this->_cache->remove(array('key' => $key));
    return $result;
  }

  function flush() {
    $result = $this->_cache->drop();
    return $result;
  }
}

