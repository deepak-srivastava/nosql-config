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
/*
 * Settings metadata file
 */

return array(
  'mongodb_connections_default_host' => array(
    'group_name' => 'system.cache.mongodb',
    'group' => 'cache',
    'name' => 'host',
    'type' => 'String',
    'html_type' => 'Text',
    'quick_form_type' => 'Element',
    'default' => 'localhost',
    'title'   => 'Host',
    'is_domain' => 1,
    'description' => 'default is localhost if not set',
    'help_text'   => 'default is localhost if not set',
  ),
  'mongodb_connections_default_db' => array(
    'group_name' => 'system.cache.mongodb',
    'group' => 'cache',
    'name' => 'db',
    'type' => 'String',
    'html_type' => 'Text',
    'quick_form_type' => 'Element',
    'default' => 'civicrm',
    'title'   => 'Database',
    'is_domain' => 1,
    'description' => 'default is civicrm if not set',
    'help_text'   => 'default is civicrm if not set',
  ),
  'mongodb_connections_default_port' => array(
    'group_name' => 'system.cache.mongodb',
    'group' => 'cache',
    'name' => 'port',
    'type' => 'Integer',
    'html_type' => 'Text',
    'quick_form_type' => 'Element',
    'default' => '27017',
    'title'   => 'Port',
    'is_domain' => 1,
    'description' => 'default is 27017 if not set',
    'help_text'   => 'default is 27017 if not set',
  ),
);