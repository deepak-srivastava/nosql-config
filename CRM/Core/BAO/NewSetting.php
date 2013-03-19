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

/**
 * BAO object for civicrm_setting table. This table is used to store civicrm settings that are not used
 * very frequently (i.e. not on every page load)
 *
 * The group column is used for grouping together all settings that logically belong to the same set.
 * Thus all settings in the same group are retrieved with one DB call and then cached for future needs.
 *
 */
class CRM_Core_BAO_NewSetting extends CRM_Core_DAO_Setting {

  static function writeToDisk($group) {
    global $civicrm_root;
    $metaDataFolder = $civicrm_root. '/settings';
    $settingsFile   = CRM_Utils_File::findFiles($metaDataFolder, "{$group}.php");

    // if there is only one file present matching with $group, we proceed with writing to disk
    if (!empty($settingsFile) && count($settingsFile) == 1) {
      $settingsInDB = CRM_Core_BAO_Setting::getItem($group);
      $settingsFile = $settingsFile[0];
      $settings = include $settingsFile;

     foreach ($settings as $settingKey => $settingVal) {
        if (array_key_exists($settingKey, $settingsInDB)) {
          $settings[$settingKey]['value'] = $settingsInDB[$settingKey];
        }
      }

      $config = CRM_Core_Config::singleton();
      @file_put_contents("{$config->configAndLogDir}{$group}.php", "<?php \n return " . var_export($settings, true) . ";");
    }
  }

  function restoreIntoDB($group, $restoreDir = 'default') {
    if ($restoreDir == 'default') {
      global $civicrm_root;
      $metaDataFolder = $civicrm_root. '/settings';
      $settingsFile   = CRM_Utils_File::findFiles($metaDataFolder, "{$group}.php");
    } else if ($restoreDir == 'config') {
      $config = CRM_Core_Config::singleton();
      $settingsFile   = CRM_Utils_File::findFiles($config->configAndLogDir, "{$group}.php");
    }

    // if there is only one file present matching with $group, we proceed with writing to disk
    if (!empty($settingsFile) && count($settingsFile) == 1) {
      $settingsFile = $settingsFile[0];
      $settings = include $settingsFile;
      $params   = array();

      foreach ($settings as $settingKey => $settingVal) {
        if ($restoreDir == 'default') {
          $params[$settingVal['name']] = CRM_Utils_Array::value('default', $settingVal);
        } else if ($restoreDir == 'config') {
          $params[$settingVal['name']] = CRM_Utils_Array::value('value', $settingVal);
        }
      }
      
      if (!empty($params))
        $result = civicrm_api('setting', 'create', $params + array('version' => 3));
      
      //FIXME: reset cache if required 
    }
  }
}
