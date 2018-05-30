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
 * File : notification.php
 * Send the notifications to student and teachers.
 */

defined('MOODLE_INTERNAL') || die();

class enrol_demands_notification extends \core\message\message {

    public function __construct($to, $from, $type, $subject, $content, $url, $data) {

        $this->component = 'enrol_demands';
        switch ($type) {

            case 'demands':
                $this->name = 'demands';
                $this->smallmessage = get_string('demandsmail', 'enrol_demands', $data);
                break;

            case 'enroled':
                $this->name = 'enroled';
                $this->smallmessage = get_string('succesfulenrolmentmail', 'enrol_demands', $data);
                break;

            case 'rejected':
                $this->name = 'rejected';
                $this->smallmessage = get_string('rejectedenrolmentmail', 'enrol_demands', $data);
                break;

            case 'reminder':
                $this->name = 'reminder';
                $this->smallmessage = get_string('demandsmail', 'enrol_demands', $data);
                break;

            default:
                throw new invalid_parameter_exception('Invalid enrol_demands notification type.');
                break;
        }

        $this->userfrom = $from;
        $this->userto = $to;
        $this->subject = $subject;
        $this->fullmessage = html_to_text($content);
        $this->fullmessageformat = FORMAT_PLAIN;
        $this->fullmessagehtml = $content;
        $this->notification = true;
        $this->contexturl = $url;
        $this->contexturlname = get_string('course');
    }
}