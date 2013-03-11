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

  /* protected $_defaults; */
  /* protected $_settings = array(); */

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function preProcess() {
    $this->_gname = CRM_Utils_Request::retrieve('gname', 'String', $this, true);

    global $civicrm_root;
    $metaDataFolder = $civicrm_root. '/settings';
    $settingsFiles = CRM_Utils_File::findFiles($metaDataFolder, $this->_gname . '.php');

    foreach ($settingsFiles as $file) {
      $this->_settings = include $file;
      break;
    }
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
    return array();
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
    $params = $this->controller->exportValues($this->_name);
  }
}

