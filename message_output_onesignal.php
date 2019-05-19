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
 *
 * @package    message_onesignal
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.7
 */


require_once($CFG->dirroot . '/message/output/lib.php');

/**
 * Message processor class
 *
 * @package   message_onesignal
 * @copyright 2012 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_output_onesignal extends message_output {

    /**
     * Processes the message and sends a notification via onesignal
     *
     * @param stdClass $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     * @return true if ok, false if error
     */
    public function send_message($eventdata) {

        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');

        if (!empty($CFG->noemailever)) {
            return true;
        }

        // Skip any messaging suspended and deleted users.
        if ($eventdata->userto->auth === 'nologin' or
            $eventdata->userto->suspended or
            $eventdata->userto->deleted) {
            return true;
        }

        // If username is empty we try to retrieve it, since it's required to generate the siteid.
        if (empty($eventdata->userto->username)) {
            $eventdata->userto->username = $DB->get_field('user', 'username', array('id' => $eventdata->userto->id));
        }

        self::sendMessage($CFG, $eventdata->smallmessage, $eventdata->userto->email);

        return true;
    }

    public function sendMessage($CFG, $mensagem, $emailAluno){
        $content = array(
            "en" => $mensagem
        );

        $fields = array(
            'app_id' => $CFG->onesignalappid,
            'included_segments' => array('All'),
            'data' => array("foo" => "bar"),
            'large_icon' =>"ic_launcher_round.png",
            'contents' => $content,
            'filters' =>  [["field" => "email", "relation" => "=", "value" => $emailAluno]]
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '. $CFG->onesignalrestkey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    public function config_form($preferences) {
        global $CFG, $OUTPUT, $USER, $PAGE;

        $systemcontext = context_system::instance();
        if (!has_capability('message/onesignal:managedevice', $systemcontext)) {
            return get_string('nopermissiontomanagedevices', 'message_onesignal');
        }

        if (!$this->is_system_configured()) {
            return get_string('notconfigured', 'message_onesignal');
        } else {

            $onesignalmanager = new message_onesignal_manager();
            $devicetokens = $onesignalmanager->get_user_devices($CFG->onesignalmobileappname, $USER->id);

            if (!empty($devicetokens)) {
                $output = '';

                foreach ($devicetokens as $devicetoken) {

                    if ($devicetoken->enable) {
                        $hideshowiconname = 't/hide';
                        $dimmed = '';
                    } else {
                        $hideshowiconname = 't/show';
                        $dimmed = 'dimmed_text';
                    }

                    $hideshowicon = $OUTPUT->pix_icon($hideshowiconname, get_string('showhide', 'message_onesignal'));
                    $name = "{$devicetoken->name} {$devicetoken->model} {$devicetoken->platform} {$devicetoken->version}";

                    $output .= html_writer::start_tag('li', array('id' => $devicetoken->id,
                                                                    'class' => 'onesignaldevice ' . $dimmed)) . "\n";
                    $output .= html_writer::label($name, 'deviceid-' . $devicetoken->id, array('class' => 'devicelabel ')) . ' ' .
                        html_writer::link('#', $hideshowicon, array('class' => 'hidedevice', 'alt' => 'show/hide')) . "\n";
                    $output .= html_writer::end_tag('li') . "\n";
                }

                // Include the AJAX script to automatically trigger the action.
                $onesignalmanager->include_device_ajax();

                $output = html_writer::tag('ul', $output, array('class' => 'list-unstyled unstyled',
                    'id' => 'onesignaldevices'));
            } else {
                $output = get_string('nodevices', 'message_onesignal');
            }
            return $output;
        }
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    public function process_form($form, &$preferences) {
        return true;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    public function load_data(&$preferences, $userid) {
        return true;
    }

    /**
     * Tests whether the onesignal settings have been configured
     * @return boolean true if onesignal is configured
     */
    public function is_system_configured() {
        $onesignalmanager = new message_onesignal_manager();
        return $onesignalmanager->is_system_configured();
    }
    
    /**
     * Returns true as message can be sent to internal support user.
     *
     * @return bool
     */
    public function can_send_to_any_users() {
        return true;
    }
}

