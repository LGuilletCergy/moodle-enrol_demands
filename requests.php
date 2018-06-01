<?php

require('../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$PAGE->set_url('/enrol/demands/requests.php');
$PAGE->set_pagelayout('report');

require_login();

$title = get_string('pluginname', 'enrol_demands');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$inscriptionnode = $PAGE->navigation->add('Demandes d\'inscription',
        new moodle_url('/enrol/demands/requests.php'));
$inscriptionnode->make_active();

$paramnomail = optional_param('nomail', 0, PARAM_INT);
$paramreject = optional_param('reject', 0, PARAM_INT);
$paramenrol = optional_param('enrol', 0, PARAM_INT);
$paramall = optional_param('all', 0, PARAM_INT);
// 1 : Accepter tous,
// 2 : Accepter tous si bonne VET,
// 3 : Refuser tous,
// 4 : Refuser tous si mauvaise VET.

// NOTIFICATIONS OU PAS
if ($paramnomail == 1 || $paramnomail == 2) {

    change_notification_status($paramnomail);
}

echo $OUTPUT->header();

maketabledemands();
echo "<br><br>";
maketableyourdemands();

echo $OUTPUT->footer();

function change_notification_status($paramnomail) {

    global $USER, $DB;

    if ($paramnomail == 1) {

        $newvalue = 'none';
    } else if ($paramnomail == 2) {

        $newvalue = 'popup,email';
    }

    $preferencenameloggedin = "message_provider_enrol_demands_demands_loggedin";

    if ($DB->record_exists('user_preferences',
            array('userid' => $USER->id, 'name' => $preferencenameloggedin))) {

        $userpreferenceloggedin = $DB->get_record('user_preferences',
            array('userid' => $USER->id, 'name' => $preferencenameloggedin));

        $userpreferenceloggedin->value = $newvalue;

        $DB->update_record('user_preferences', $userpreferenceloggedin);
    } else {

        $userpreferenceloggedin = new stdClass();
        $userpreferenceloggedin->userid = $USER->id;
        $userpreferenceloggedin->name = $preferencenameloggedin;
        $userpreferenceloggedin->value = $newvalue;

        $DB->insert_record('user_preferences', $userpreferenceloggedin);
    }

    $preferencenameloggedoff = "message_provider_enrol_demands_demands_loggedoff";

    if ($DB->record_exists('user_preferences',
            array('userid' => $USER->id, 'name' => $preferencenameloggedoff))) {

        $userpreferenceloggedoff = $DB->get_record('user_preferences',
            array('userid' => $USER->id, 'name' => $preferencenameloggedoff));

        $userpreferenceloggedoff->value = $newvalue;

        $DB->update_record('user_preferences', $userpreferenceloggedoff);
    } else {

        $userpreferenceloggedoff = new stdClass();
        $userpreferenceloggedoff->userid = $USER->id;
        $userpreferenceloggedoff->name = $preferencenameloggedoff;
        $userpreferenceloggedoff->value = $newvalue;

        $DB->insert_record('user_preferences', $userpreferenceloggedoff);
    }
}



function maketabledemands() {

    global $DB, $CFG, $USER;

    echo get_string('headermanageenrolments', 'enrol_demands');
    $preferencenameloggedin = "message_provider_enrol_demands_demands_loggedin";

    if ($DB->record_exists('user_preferences',
            array('userid' => $USER->id, 'name' => $preferencenameloggedin, 'value' => 'none'))) {

        echo get_string('buttonsendremindersagain', 'enrol_demands');
    } else {

        echo get_string('buttonstopreminders', 'enrol_demands');
    }

    $table = new html_table();
    $table->head  = array(get_string('coursevet', 'enrol_demands'),
        get_string('coursename', 'enrol_demands'), get_string('askedon', 'enrol_demands'),
        get_string('askedby', 'enrol_demands'), get_string('mailasker', 'enrol_demands'),
        get_string('vetasker', 'enrol_demands'), get_string('answer', 'enrol_demands'));
    $table->colclasses = array('leftalign coursevet', 'leftalign coursename', 'leftalign askedon',
        'leftalign askedby', 'leftalign mailasker', 'leftalign vetasker', 'leftalign answer');
    $table->id = 'tabledemands';
    $table->attributes['class'] = 'admintable generaltable';

    $listdemands = $DB->get_records('enrol_demands');

    foreach ($listdemands as $demand) {

        if ($demand->answererid == 0) {

            $enrol = $DB->get_record('enrol', array('id' => $demand->enrolid));

            $coursecontext = context_course::instance($enrol->courseid);

            if (has_capability('enrol/demands:managecourseenrolment', $coursecontext)
                    && is_enrolled($coursecontext)) {

                $course = $DB->get_record('course', array('id' => $enrol->courseid));
                $student = $DB->get_record('user', array('id' => $demand->studentid));

                $stringvets = "";

                $listvets = $DB->get_records('local_usercreation_vet', array('studentid' => $student->id));

                foreach ($listvets as $vet) {

                    if (substr($stringvets, 0, 5) == $CFG->yearprefix) {

                        if ($stringvets != "") {

                            $stringvets .= ", ";
                        }

                        $stringvets .= $vet->vetcode;
                    }
                }

                $linebutton = "<td><a href='validate.php?enrol=$demand->id'>".
                        get_string('accept', 'enrol_demands')."</a></td>"
                        . "<td><a href='validate.php?reject=$demand->id'>".
                        get_string('reject', 'enrol_demands')."</a></td>";

                $line = array();
                $line[] = $course->idnumber;
                $line[] = $course->fullname;
                $line[] = date("d/m/Y", $demand->askedat);
                $line[] = "<a href='$CFG->wwwroot/user/view.php?id=$student->id'>"
                        . "$student->firstname $student->lastname</a>";
                $line[] = $student->email;
                $line[] = $stringvets;
                $line[] = $linebutton;
                $data[] = $row = new html_table_row($line);
            }
        }
    }

    $table->data  = $data;
    echo html_writer::table($table);
}

function maketableyourdemands() {

    echo get_string('headertableyourdemands', 'enrol_demands');

    checkdemands('', 'askedat', get_string('waitingrequest', 'enrol_demands'));
    checkdemands('Oui', 'answeredat', get_string('acceptedrequest', 'enrol_demands'));
    checkdemands('Non', 'answeredat', get_string('rejectedrequest', 'enrol_demands'));
}

function checkdemands($answer, $orderby, $label) {
    global $DB, $USER;
    $sql = "SELECT id, enrolid, askedat, answer, answeredat, answererid "
            . "FROM mdl_enrol_demands "
            . "WHERE studentid = $USER->id "
            . "AND answer = '$answer' "
            . "ORDER BY $orderby DESC";
    $demands = $DB->get_records_sql($sql);
    if ($demands) {
        echo "<h3>$label</h3>";
        asked_enrolments_table($demands, $answer);
    }
}

function asked_enrolments_table($askedenrolments, $answer) {
    global $CFG, $USER, $DB;

    $table = new html_table();
    $table->head  = array(get_string('coursevet', 'enrol_demands'),
        get_string('coursename', 'enrol_demands'), get_string('askedon', 'enrol_demands'),
        get_string('answeredon', 'enrol_demands'), get_string('answeredby', 'enrol_demands'));
    $table->colclasses = array('leftalign coursevet', 'leftalign coursename', 'leftalign askedon',
        'leftalign answeredon', 'leftalign answeredby');
    $table->id = 'tableyourdemands'.$answer;
    $table->attributes['class'] = 'admintable generaltable';

    foreach ($askedenrolments as $askedenrolment) {

        if ($DB->record_exists('course', array('id' => $askedenrolment->courseid))) {

            $course = $DB->get_record('course', array('id' => $askedenrolment->courseid));
            $coursecategory = $DB->get_record('coursecategories', array('id' => $course->category));

            $line = array();
            $line[] = $coursecategory->name;
            $line[] = $course->fullname;
            $line[] = $askedenrolment->askedat;
            $line[] = $askedenrolment->answeredat;

            if ($DB->record_exists('user', array('id' => $askedenrolment->answererid))) {

                $user = $DB->get_record('user', array('id' => $askedenrolment->answererid));
                $line[] = $user->firstname." ".$user->lastname;
            } else {

                $line[] = "";
            }
            $data[] = $row = new html_table_row($line);
        }
    }

    if (isset($data)) {

        $table->data  = $data;
        echo html_writer::table($table);
    }
}

