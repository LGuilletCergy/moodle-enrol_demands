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
 * File : validate.php
 * Type the custommessage enrol and redirect
 */

require('../../config.php');
require_once($CFG->dirroot.'/enrol/demands/validate_form.php');
require_once($CFG->dirroot.'/enrol/demands/lib.php');

$paramreject = optional_param('reject', 0, PARAM_INT);
$paramenrol = optional_param('enrol', 0, PARAM_INT);
$paramall = optional_param('all', 0, PARAM_INT);

$PAGE->set_url('/enrol/demands/validate.php');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());

require_login();

$title = get_string('pluginname', 'enrol_demands');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$mform = new enrol_demands_validate_form();

$redirecturl = new moodle_url('requests.php');

if ($mform->is_cancelled()) {

    redirect($redirecturl);
} else if ($fromform = $mform->get_data()) {

    if ($fromform->reject != 0) {

        rejectenroldemand($fromform->reject, $fromform->custommessage['text']);
    } else if ($fromform->enrol != 0) {

        acceptenroldemand($fromform->enrol, $fromform->custommessage['text']);
    } else if ($fromform->all != 0) {

        // Le tri sur le droit d'acceptation ou de refus se fait dans la fonction.

        $askedenrolments = $DB->get_records('enrol_demands');

        foreach ($askedenrolments as $askedenrolment) {

            switch ($fromform->all) {

                case 1: // Accepter tous.

                    acceptenroldemand($askedenrolment->id, $fromform->custommessage['text']);
                    break;

                case 2: // Accepter tous si bonne VET.

                    $correctvet = has_correct_vet($askedenrolment);

                    if ($correctvet) {
                        acceptenroldemand($askedenrolment->id, $fromform->custommessage['text']);
                    }

                    break;

                case 3: // Refuser tous.

                    rejectenroldemand($askedenrolment->id, $fromform->custommessage['text']);
                    break;

                case 4: // Refuser tous si mauvaise VET.

                    $correctvet = has_correct_vet($askedenrolment);

                    if (!$correctvet) {
                        rejectenroldemand($askedenrolment->id, $fromform->custommessage['text']);
                    }

                    break;

                default:
                    break;
            }
        }
    }

    redirect($redirecturl);
} else {

    echo $OUTPUT->header();

    echo get_string('replacedefaultmessage', 'enrol_demands');

    $defaultdata = new stdClass();
    $defaultdata->reject = $paramreject;
    $defaultdata->enrol = $paramenrol;
    $defaultdata->all = $paramall;
    $mform->set_data($defaultdata);

    $mform->display();
}

echo $OUTPUT->footer();

function rejectenroldemand($paramreject, $custommessage) {

    global $DB, $USER;

    $demanddata = $DB->get_record('enrol_demands', array('id' => $paramreject));

    if ($demanddata->answererid == 0) {

        $enrol = $DB->get_record('enrol', array('id' => $demanddata->enrolid));

        $coursecontext = context_course::instance($enrol->courseid);

        if (has_capability('enrol/demands:managecourseenrolment', $coursecontext)
                && is_enrolled($coursecontext)) {

            $student = $DB->get_record('user', array('id' => $demanddata->studentid));

            $now = time();
            $sql = "UPDATE mdl_enrol_demands SET answeredat = $now,"
                    . " answer = 'Non', answererid = $USER->id WHERE id = $paramreject";
            $DB->execute($sql);

            // Send mail.

            send_answer_notification($student, $enrol, 'rejected', $custommessage);

            return 0;
        } else {

            return -1;
        }
    }
}

function acceptenroldemand($paramenrol, $custommessage) {

    global $DB, $USER;

    $demanddata = $DB->get_record('enrol_demands', array('id' => $paramenrol));

    if ($demanddata->answererid == 0) {

        $enrol = $DB->get_record('enrol', array('id' => $demanddata->enrolid));

        $coursecontext = context_course::instance($enrol->courseid);

        if (has_capability('enrol/demands:managecourseenrolment', $coursecontext)
                && is_enrolled($coursecontext)) {

            $student = $DB->get_record('user', array('id' => $demanddata->studentid));

            $enrolplugin = new enrol_demands_plugin();
            $enrolplugin->enrol_user($enrol, $student->id, $enrol->roleid);

            // On note que la demande est acceptée.
            $now = time();
            $sql = "UPDATE mdl_enrol_demands SET answeredat = $now, answer = 'Oui',"
                    . " answererid = $USER->id WHERE id = $paramenrol";
            $DB->execute($sql);

            // Send mail.

            send_answer_notification($student, $enrol, 'enroled', $custommessage);

            return 0;
        } else {

            return -1;
        }
    }
}

function has_correct_vet($askedenrolment) {

    global $DB;

    $correctvet = false;

    // Tester si la table des vets existe.

    $dbman = $DB->get_manager();

    if ($dbman->table_exists('local_usercreation_vet')) {

        $correctvet = false;

        $enrol = $DB->get_record('enrol', array('id' => $askedenrolment->enrolid));
        $course = $DB->get_record('course', array('id' => $enrol->courseid));
        $categoryvet = $DB->get_record('course_categories',
                array('id' => $course->category))->idnumber;

        $studentvets = $DB->get_records('local_usercreation_vet',
                array('studentid' => $askedenrolment->studentid));

        foreach ($studentvets as $studentvet) {

            if ($studentvet->vetcode == $categoryvet) {

                $correctvet = true;
                break;
            }
        }
    }

    return $correctvet;
}