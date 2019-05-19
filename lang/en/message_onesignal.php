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
 * Strings for component 'message_onesignal', language 'en'
 *
 * @package    message_onesignal
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['onesignalappid'] = 'APP ID One Signal';
$string['onesignalrestkey'] = 'Rest Key One Signal';
$string['onesignalappname'] = 'onesignal app name';
$string['onesignalmobileappname'] = 'Mobile app name';
$string['onesignalport'] = 'onesignal port';
$string['onesignalurl'] = 'onesignal URL';
$string['configonesignalurl'] = 'The server url to connect to to send push notifications.';
$string['configonesignalport'] = 'The port to use when connecting to the onesignal server.';
$string['configonesignalappid'] = '';
$string['configonesignalrestkey'] = '';
$string['configonesignalappname'] = 'The app name identifier in onesignal.';
$string['configonesignalmobileappname'] = 'The Mobile app unique identifier (usually something like com.moodle.moodlemobile).';
$string['deletecheckdevicename'] = 'Delete your device: {$a->name}';
$string['deletedevice'] = 'Delete the device. Note that an app can register the device again. If the device keeps reappearing, disable it.';
$string['devicetoken'] = 'Device token';
$string['errorretrievingkey'] = 'An error occurred while retrieving the access key. Your site must be registered to use this service. If your site is already registered, please try updating your registration.';
$string['keyretrievedsuccessfully'] = 'Key retrieved successfully';
$string['nodevices'] = 'No registered devices. Devices will automatically appear after you install the Moodle app and add this site.';
$string['nopermissiontomanagedevices'] = 'You don\'t have permission to manage devices.';
$string['notconfigured'] = 'The onesignal server hasn\'t been configured so onesignal messages cannot be sent';
$string['pluginname'] = 'One Signal';
$string['modulename'] = 'One Signal';
$string['privacy:appiddescription'] = 'This is an identifier to the application being used.';
$string['privacy:enableddescription'] = 'If this device is enabled for onesignal.';
$string['privacy:metadata:enabled'] = 'Whether the onesignal device is enabled.';
$string['privacy:metadata:date'] = 'The date that the message was sent.';
$string['privacy:metadata:externalpurpose'] = 'This information is sent to an external site to be ultimately delivered to the mobile device of the user.';
$string['privacy:metadata:fullmessage'] = 'The full message.';
$string['privacy:metadata:notification'] = 'If this message is a notification.';
$string['privacy:metadata:smallmessage'] = 'A section of the message.';
$string['privacy:metadata:subject'] = 'The subject line of the message.';
$string['privacy:metadata:tableexplanation'] = 'onesignal device information is stored here.';
$string['privacy:metadata:userdeviceid'] = 'The ID linking to the user\'s mobile device';
$string['privacy:metadata:userfromfullname'] = 'The full name of the user who sent the message.';
$string['privacy:metadata:userfromid'] = 'The user ID of the author of the message.';
$string['privacy:metadata:userid'] = 'The ID of the user who sent the message.';
$string['privacy:metadata:username'] = 'The username of the user.';
$string['privacy:metadata:usersubsystem'] = 'This plugin is connected to the user subsystem.';
$string['privacy:subcontext'] = 'Message onesignal';
$string['sitemustberegistered'] = 'In order to use the public onesignal instance you must register your site with Moodle.net';
$string['showhide'] = 'Enable/disable the device.';
$string['requestaccesskey'] = 'Request access key';
$string['unknowndevice'] = 'Unknown device';
$string['onesignal:managedevice'] = 'Manage devices';
