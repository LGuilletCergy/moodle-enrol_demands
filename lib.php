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
 * File : lib.php
 * Library file
 */

defined('MOODLE_INTERNAL') || die();

class enrol_demands_plugin extends enrol_plugin {

    /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {

        $fields = $this->get_instance_defaults();
        return $this->add_instance($course, $fields);
    }

    public function allow_manage(stdClass $instance) {

        return true;
    }
    public function allow_unenrol(stdClass $instance) {

        return true;
    }

    public function enrol_page_hook(stdClass $instance) {

        global $DB, $USER, $OUTPUT, $CFG;

        $hasroleincourse = false;
        $hasapplied = false;

        if ($DB->record_exists('user_enrolments',
                array('userid' => $USER->id, 'enrolid' => $instance->id))) {


            $hasroleincourse = true;
        }

        if ($DB->record_exists('enrol_demands',
                array('enrolid' => $instance->id, 'studentid' => $USER->id))) {

            $hasapplied = true;
        }

        if ($hasroleincourse) {

            return get_string('hasroleincourse', 'enrol_demands');
        } else if ($hasapplied) {

            return get_string('hasapplied', 'enrol_demands');
        } else {

            require_once("$CFG->dirroot/enrol/demands/demands_form.php");

            $form = new enrol_demands_demands_form(null, $instance);

            if ($data = $form->get_data()) {

                $askedenrolment = new stdClass();
                $askedenrolment->enroldid = $instance->id;
                $askedenrolment->studentid = $USER->id;
                $askedenrolment->askedat = time();
                $askedenrolment->answer = null;

                $DB->insert_record('enrol_demands', $askedenrolment);

                $coursecontext = context_course::instance($instance->courseid);

                $listteachers = get_users_by_capability($coursecontext,
                        'enrol/demands:managecourseenrolment');

                foreach ($listteachers as $teacher) {

                    send_demand_notification($instance, $teacher);
                }

                redirect("$CFG->wwwroot/enrol/index.php?id=$instance->id");
            }

            $output = $form->render();

            return $OUTPUT->box($output);
        }
    }

    /**
     * Gets a list of roles that this user can assign for the course as the default for demands-enrolment.
     *
     * @param context $context the context.
     * @param integer $defaultrole the id of the role that is set as the default for demands-enrolment
     * @return array index is the role id, value is the role name
     */
    public function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id' => $defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }

    /**
     * Return an array of valid options for the status.
     *
     * @return array
     */
    protected function get_status_options() {
        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        return $options;
    }

    public function use_standard_editing_ui() {

        return true;
    }

    /**
     * Add elements to the edit instance form.
     *
     * @param stdClass $instance
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return bool
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_demands'), $options);

        if ($instance->id) {

            $roles = $this->extend_assignable_roles($context, $instance->roleid);
        } else {

            $roles = $this->extend_assignable_roles($context, $this->get_config('roleid'));
        }

        $mform->addElement('select', 'roleid', get_string('defaultrole', 'role'), $roles);
        $mform->setDefault('roleid', $this->get_config('roleid'));
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {

        return true;
    }

    /**
     * Return true if we can add a new instance to this course.
     *
     * @param int $courseid
     * @return boolean
     */
    public function can_add_instance($courseid) {

        $coursecontext = context_course::instance($courseid);

        if (has_capability('enrol/demands:config', $coursecontext)) {

            return true;
        } else {

            return false;
        }
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */

    public function can_delete_instance($instance) {

        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/demands:config', $context);
    }

    public function send_answer_notification($user, $instance, $type, $custommessage) {

        global $CFG, $USER;

        require_once($CFG->dirroot.'/enrol/demands/notification.php');
        // Required for course_get_url() function.
        require_once($CFG->dirroot.'/course/lib.php');

        $course = get_course($instance->courseid);

        $contact = core_user::get_support_user();

        $stringdata = new stdClass();
        $stringdata->coursename = $course->fullname;
        $stringdata->userfirstname = $USER->firstname;
        $stringdata->userlastname = $USER->lastname;
        $stringdata->useremail = $USER->email;

        if ($type == 'enroled') {

            $subject = get_string('subjectaccepted', 'enrol_demands', $course->fullname);

            if (isset($custommessage)) {

                $content = $custommessage;
            } else {

                $content = get_string('succesfulenrolmentmail', 'enrol_demands', $stringdata);
            }
        } else if ($type == 'rejected') {

            $subject = get_string('subjectrejected', 'enrol_demands', $course->fullname);
            if (isset($custommessage)) {

                $content = $custommessage;
            } else {

                $content = get_string('rejectedenrolmentmail', 'enrol_demands', $stringdata);
            }
        }

        $courseurl = new moodle_url('course/view.php', array('id' => $course->id));

        $message = new enrol_demands_notification($user, $contact, $type, $subject,
                $content, $courseurl, $course);

        message_send($message);
    }

    public function send_demand_notification($instance, $user) {

        global $CFG;

        require_once($CFG->dirroot.'/enrol/demands/notification.php');
        // Required for course_get_url() function.
        require_once($CFG->dirroot.'/course/lib.php');

        $course = get_course($instance->courseid);

        $contact = core_user::get_support_user();


        $subject = get_string('subjectnewdemand', 'enrol_demands', $course->fullname);


        $content = get_string('newdemandmail', 'enrol_demands', $course->fullname);

        $courseurl = new moodle_url('course/view.php', array('id' => $course->id));

        $message = new enrol_demands_notification($user, $contact, 'demands', $subject,
                $content, $courseurl, $course);

        message_send($message);
    }
}

function enrol_demands_extend_navigation_course($navigation, $course, $context) {

    $requestsurl = new moodle_url('/enrol/demands/requests.php');
    $requeststext = get_string('pluginname', 'enrol_demands');

    $settingsnode = navigation_node::create($requeststext, $requestsurl,
            navigation_node::TYPE_SETTING, null, null, new pix_icon('i/settings', ''));

    $navigation->add_node($settingsnode);
}