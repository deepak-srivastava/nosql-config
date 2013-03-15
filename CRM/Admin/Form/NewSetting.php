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
 * This class generates form components generic to CiviCRM settings
 *
 */
class CRM_Admin_Form_NewSetting extends CRM_Core_Form {

  protected $_gname;

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function preProcess() {
    $this->_gname = CRM_Utils_Request::retrieve('gname', 'String', $this, true);
    CRM_Utils_System::setTitle(ts('CiviCRM Settings: ') . $this->_gname);

    global $civicrm_root;
    $metaDataFolder = $civicrm_root. '/settings';
    $settingsFile = CRM_Utils_File::findFiles($metaDataFolder, $this->_gname . '.php');
    $settingsFile =  $settingsFile[0];

    $this->_settings = include $settingsFile;
  }

  /**
   * This function sets the default values for the form.
   * default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    $this->_defaults = CRM_Core_BAO_Setting::getItem($this->_gname);

    if (empty($this->_defaults)) {
      foreach ($this->_settings as $setting => $group) {
        if (array_key_exists('value', $group)) {
          $this->_defaults[$setting] = $group['value'];
        } else {
          $this->_defaults[$setting] = CRM_Utils_Array::value('default', $group);
        }
      }
    }
    return $this->_defaults;
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $elements = array();
    foreach ($this->_settings as $setting => $group){
      // FIXME: $settingMetaData should get it from API
      $settingMetaData['values'][$setting] = $group;
      if(isset($settingMetaData['values'][$setting]['quick_form_type'])){
        $add = 'add' . $settingMetaData['values'][$setting]['quick_form_type'];
        if($add == 'addElement'){
          $this->$add(
            $settingMetaData['values'][$setting]['html_type'],
            $group['name'],
            ts($settingMetaData['values'][$setting]['title']),
            CRM_Utils_Array::value('html_attributes', $settingMetaData['values'][$setting], array())
          );
        }
        else{
          $this->$add($group['name'], ts($settingMetaData['values'][$setting]['title']));
        }
        $this->assign("{$setting}_description", ts($settingMetaData['values'][$setting]['description']));
        $elements[$group['name']] = $group;
      }
    }
    $this->assign('elements', $elements);

    $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'next',
          'name' => ts('Reset to defaults'),
          'subName' => 'defaults',
        ),
        array(
          'type' => 'next',
          'name' => ts('Restore from disk'),
          'subName' => 'disk',
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ),
      )
    );
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    // store the submitted values in an array
    $params    = $this->controller->exportValues($this->_name);
    $className = CRM_Utils_String::getClassName($this->_name);
    if ($this->controller->getButtonName('submit') == "_qf_{$className}_next_defaults") {
      // restore
      CRM_Core_BAO_NewSetting::restoreIntoDB($this->_gname);
      CRM_Core_Session::setStatus(" ", ts('Setting reset to defaults.'), "success");
    }     
    else if ($this->controller->getButtonName('submit') == "_qf_{$className}_next_disk") {
      CRM_Core_BAO_NewSetting::restoreIntoDB($this->_gname, 'config');
      CRM_Core_Session::setStatus(" ", ts('Setting restored from disk to db.'), "success");
    }
    else {
      $params = array_intersect_key($params, $this->_settings);
      foreach ($params as $name => $value) {
        CRM_Core_BAO_Setting::setItem(
          $value,
          $this->_settings[$name]['group_name'],
          $name
        );
      }
      // FIXME: reset cache here
      CRM_Core_Session::setStatus(" ", ts('Changes Saved.'), "success");
    }
    CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/setting",
        "reset=1&gname={$this->_gname}"));
  }
}

