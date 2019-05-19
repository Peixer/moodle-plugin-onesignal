<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * onesignal manager class
 *
 * @package    message_onesignal
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */

defined('MOODLE_INTERNAL') || die;

/**
 * onesignal helper manager class
 *
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_onesignal_manager {

    /**
     * Include the relevant javascript and language strings for the device
     * toolbox YUI module
     *
     * @return bool
     */
    public function include_device_ajax() {
        global $PAGE, $CFG;

        $config = new stdClass();
        $config->resturl = '/message/output/onesignal/rest.php';
        $config->pageparams = array();

        // Include toolboxes.
        $PAGE->requires->yui_module('moodle-message_onesignal-toolboxes', 'M.message.init_device_toolbox', array(array(
                'ajaxurl' => $config->resturl,
                'config' => $config,
                ))
        );

        // Required strings for the javascript.
        $PAGE->requires->strings_for_js(array('deletecheckdevicename'), 'message_onesignal');
        $PAGE->requires->strings_for_js(array('show', 'hide'), 'moodle');

        return true;
    }

    /**
     * Return the user devices for a specific app.
     *
     * @param string $appname the app name .
     * @param int $userid if empty take the current user.
     * @return array all the devices
     */
    public function get_user_devices($appname, $userid = null) {
        global $USER, $DB;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $devices = array();

        $params = array('appid' => $appname, 'userid' => $userid);

        // First, we look all the devices registered for this user in the Moodle core.
        // We are going to allow only ios devices (since these are the ones that supports PUSH notifications).
        $userdevices = $DB->get_records('user_devices', $params);
        foreach ($userdevices as $device) {
            if (core_text::strtolower($device->platform)) {
                // Check if the device is known by onesignal.
                if (!$onesignaldev = $DB->get_record('message_onesignal_devices',
                        array('userdeviceid' => $device->id))) {

                    $device->pushid = '';
                    $onesignaldev = new stdClass;
                    $onesignaldev->userdeviceid = $device->id;
                    $onesignaldev->enable = 1;
                    $onesignaldev->id = $DB->insert_record('message_onesignal_devices', $onesignaldev);
                }
                $device->id = $onesignaldev->id;
                $device->enable = $onesignaldev->enable;
                $devices[] = $device;
            }
        }

        return $devices;
    }

    /**
     * Tests whether the onesignal settings have been configured
     * @return boolean true if onesignal is configured
     */
    public function is_system_configured() {
        global $CFG;

        return (!empty($CFG->onesignalappid)
                && !empty($CFG->onesignalrestkey));
    }

    /**
     * Enables or disables a registered user device so it can receive Push notifications
     *
     * @param  int $deviceid the device id
     * @param  bool $enable  true to enable it, false to disable it
     * @return bool true if the device was enabled, false in case of error
     * @since  Moodle 3.2
     */
    public static function enable_device($deviceid, $enable) {
        global $DB, $USER;

        if (!$device = $DB->get_record('message_onesignal_devices', array('id' => $deviceid), '*')) {
            return false;
        }

        // Check that the device belongs to the current user.
        if (!$userdevice = $DB->get_record('user_devices', array('id' => $device->userdeviceid, 'userid' => $USER->id), '*')) {
            return false;
        }

        $device->enable = $enable;
        return $DB->update_record('message_onesignal_devices', $device);
    }

}
