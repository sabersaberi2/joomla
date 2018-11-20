<?php

/**
 * @version     $Id: email.php 20196 2011-03-04 02:40:25Z mrichey $
 * @package     plg_auth_email
 * @copyright   Copyright (C) 2005 - 2011 Michael Richey. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgAuthenticationEmail extends JPlugin {

    /*
     * This method should handle any authentication and report back to the subject
     */

    function onUserAuthenticate($credentials, $options, &$response) {

        $response->type = 'email';


        // We do not like blank passwords!
        if (empty($credentials['password'])) {
            $response->status        = JAuthentication::STATUS_FAILURE;
            $response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
            return false;
        }


        // Get a database object
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $result = null;


        // email login
        if (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',$credentials['username']))
        {
            $query
                ->select($db->quoteName(array('users.id', 'users.username', 'users.password', 'comprofiler.cb_mellicod')))
                ->from($db->quoteName('#__users','users'))
                ->join('INNER', $db->quoteName('#__comprofiler', 'comprofiler') . ' ON (' . $db->quoteName('users.id') . ' = ' . $db->quoteName('comprofiler.user_id') . ')')
                ->where($db->quoteName('users.email') . ' LIKE ' . $db->quote($credentials['username']));

            $db->setQuery($query);
            $result = $db->loadObject();
            $query->clear();
        }


        // iranian mobile number or melli code login
        elseif ( (preg_match('/^(\+98|0)?(9\d{9})$/iD',$credentials['username'],$mobile_matches)) || ($this->CheckMeliCode($credentials['username'])) )
        {
            if ( (file_exists(JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php')) && (file_exists(JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' )) )
            {
                $query
                    ->select($db->quoteName(array('users.id', 'users.username', 'users.password', 'comprofiler.cb_mellicod')))
                    ->from($db->quoteName('#__comprofiler','comprofiler'))
                    ->join('INNER', $db->quoteName('#__users', 'users') . ' ON (' . $db->quoteName('comprofiler.user_id') . ' = ' . $db->quoteName('users.id') . ')');

                if ( $this->CheckMeliCode($credentials['username']) )
                    $query->where($db->quoteName('comprofiler.cb_mellicod') . ' = ' . $db->quote($credentials['username']));
                else
                    $query->where($db->quoteName('comprofiler.cb_mobilenumber') . ' LIKE ' . $db->quote('%'.$mobile_matches[2].'%'));
                    // $query->where($db->quoteName('comprofiler.cb_mobilenumber') . ' = ' . $db->quote($credentials['username']));

                $db->setQuery($query);
                $result = $db->loadObject();
                $query->clear();
            }
        }


        // username login if password is cb_mellicod
        else
        {
            $query
                ->select($db->quoteName(array('users.id', 'users.username', 'users.password', 'comprofiler.cb_mellicod')))
                ->from($db->quoteName('#__users','users'))
                ->join('INNER', $db->quoteName('#__comprofiler', 'comprofiler') . ' ON (' . $db->quoteName('users.id') . ' = ' . $db->quoteName('comprofiler.user_id') . ')')
                ->where($db->quoteName('users.username') . ' = ' . $db->quote($credentials['username']));

            $db->setQuery($query);
            $result = $db->loadObject();
            $query->clear();

            if (isset($result->cb_mellicod))
                if ( $credentials['password'] != $result->cb_mellicod )
                    $result = null;
        }


        if ($result) {

            if (method_exists('JUserHelper','verifyPassword'))
                $pass_match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
            else
            {
                if (substr($result->password, 0, 4) == '$2y$') {
                    // BCrypt passwords are always 60 characters, but it is possible that salt is appended although non standard.
                    $password60 = substr($result->password, 0, 60);

                    if (JCrypt::hasStrongPasswordSupport()) $pass_match = password_verify($credentials['password'], $password60);
                }
                elseif (substr($result->password, 0, 8) == '{SHA256}') {
                    // Check the password
                    $parts     = explode(':', $result->password);
                    $crypt     = $parts[0];
                    $salt      = @$parts[1];
                    $testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'sha256', false);

                    if ($result->password == $testcrypt) $pass_match = true;
                }
                else {
                    // Check the password
                    $parts = explode(':', $result->password);
                    $crypt = $parts[0];
                    $salt  = @$parts[1];

                    $testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt, 'md5-hex', false);

                    if ($crypt == $testcrypt) $pass_match = true;
                }
            }

            if ( (isset($pass_match) && $pass_match === true) || ($credentials['password'] == $result->cb_mellicod) ) {
                $user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
                $response->username = $user->username;
                $response->email = $user->email;
                $response->fullname = $user->name;
                $response->language = JFactory::getApplication()->isAdmin() ? $user->getParam('admin_language') : $user->getParam('language');
                $response->status = JAuthentication::STATUS_SUCCESS;
                $response->error_message = '';
            }
            else {
                $response->status        = JAuthentication::STATUS_FAILURE;
                $response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
                return false;
            }
        }
        else {
            $response->status        = JAuthentication::STATUS_FAILURE;
            $response->error_message = JText::_('JGLOBAL_AUTH_NO_USER'); 
            return false;            
        }
    }

    function CheckMeliCode($meli_code) {
        if (strlen($meli_code) == 10)
        {
            if ($meli_code == '1111111111' ||
                $meli_code == '0000000000' ||
                $meli_code == '2222222222' ||
                $meli_code == '3333333333' ||
                $meli_code == '4444444444' ||
                $meli_code == '5555555555' ||
                $meli_code == '6666666666' ||
                $meli_code == '7777777777' ||
                $meli_code == '8888888888' ||
                $meli_code == '9999999999' ||
                $meli_code == '0123456789' ||
                $meli_code == '9876543210')
            {
                //echo 'كد ملي صحيح نمي باشد';          
                return false;
            }

            $c = substr($meli_code,9,1);
            $n = substr($meli_code,0,1) * 10 +
                 substr($meli_code,1,1) * 9 +
                 substr($meli_code,2,1) * 8 +
                 substr($meli_code,3,1) * 7 +
                 substr($meli_code,4,1) * 6 +
                 substr($meli_code,5,1) * 5 +
                 substr($meli_code,6,1) * 4 +
                 substr($meli_code,7,1) * 3 +
                 substr($meli_code,8,1) * 2;
            $r = $n - (int)($n / 11) * 11; 
            if (($r == 0 && $r == $c) || ($r == 1 && $c == 1) || ($r > 1 && $c == 11 - $r))
            {
                //echo ' کد ملی صحیح است';                
                return true;
            }
            else
            {                
                //echo 'كد ملي صحيح نمي باشد';         
                return false;
            }
        }
        else
        {
            //echo 'طول کد ملی وارد شده باید 10 کاراکتر باشد';           
            return false;       
        }
    }
}
