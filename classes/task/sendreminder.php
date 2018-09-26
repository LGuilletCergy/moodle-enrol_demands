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
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Create the enrolment on demand method.
 *
 * @package   enrol_demands
 * @copyright 2018 Laurent Guillet <laurent.guillet@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : sendreminder.php
 * Task file
 */

namespace enrol_demands\task;

defined('MOODLE_INTERNAL') || die();

class sendreminder extends \core\task\scheduled_task {

    public function get_name() {

        return get_string('sendreminder', 'enrol_demands');
    }

    public function execute() {

        global $DB;

        $teachers = array();

        $askedenrolments = $DB->get_recordset('enrol_demands', array('answererid' => 0));

        foreach ($askedenrolments as $askedenrolment) {

            $enrol = $DB->get_record('enrol', array('id' => $askedenrolment->enrolid));

            $courseid = $enrol->courseid;

            if ($DB->record_exists('context',
                    array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid))) {

                $coursecontext = \context_course::instance($courseid);

                $courseteachers = get_users_by_capability($coursecontext,
                        'enrol/demands:managecourseenrolment');

                foreach ($courseteachers as $courseteacher) {

                    if (is_enrolled($coursecontext, $courseteacher)) {

                        if (isset($teachers[$courseteacher->id])) {

                            $teachers[$courseteacher->id]++;
                        } else {

                            $teachers[$courseteacher->id] = 1;
                        }
                    }
                }
            }
        }

        $askedenrolments->close();

        foreach ($teachers as $teacherid => $nbrequests) {

            $teacher = \core_user::get_user($teacherid);

            send_reminder_message($teacher, $nbrequests);
        }
    }
}

function send_reminder_message($teacher, $nbrequests) {

    global $CFG;

    require_once($CFG->dirroot.'/enrol/demands/notification.php');

    $contact = \core_user::get_support_user();

    $subject = get_string('subjectreminder', 'enrol_demands');
    $content = get_string('demandsmail', 'enrol_demands', $nbrequests);

    $message = new \enrol_demands_notification($teacher, $contact, 'reminder', $subject,
            $content, null, $nbrequests);

    message_send($message);
}