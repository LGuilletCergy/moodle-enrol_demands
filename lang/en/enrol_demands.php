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
 * File : en/enrol_demands.php
 * English language file
 */

$string['pluginname'] = "Inscriptions on demand";
$string['hasroleincourse'] = "You are already enroled in this course";
$string['hasapplied'] = "You have already applied to this course";
$string['demandenrolment'] = "Ask to be enroled in this course";
$string['status'] = 'Allow demands for enrolment';
$string['demandsmail'] = "You have {$a} enrolment demands not answered.
    <p>To answer them, please log into CoursUCP
    (<a href='https://cours.u-cergy.fr'>https://cours.u-cergy.fr</a>)
    and consult the bloc \"Enrolment on demands\" on your dashboard. </p>
    <p>You can also go directly to the page
    <a href='https://cours.u-cergy.fr/enrol/demands/requests.php'>
    https://cours.u-cergy.fr/enrol/demands/requests.php</a>.</p>
    <p>On that page you can also ask to no longer receive these emails.</p>
    <p><br>
    CoursUCP, Your pedagogic platform.</p>";
$string['succesfulenrolmentmail'] = "You have been successfully enroled in course {$a}.";
$string['rejectedenrolmentmail'] = "Your application to course {$a} has been rejected.";
$string['subjectaccepted'] = "Your enrolment has been approved";
$string['subjectrejected'] = "Your enrolment has been rejected";
$string['subjectreminder'] = "You have enrolment demands pending";
$string['sendreminder'] = "Send weekly reminder of demands of enrolments";
$string['headermanageenrolments'] = "<br><br>
    <h2>Demands you received</h2>
    Students (or coworkers) have requested that you enrol them in your course :<br><br>
    <a href='requests.php?all=1'><button class='btn btn-secondary'>Accept everyone</button></a>&nbsp;&nbsp;
    <a href='requests.php?all=2'><button class='btn btn-secondary'>Accept only correct vet</button></a><br><br>
    <a href='requests.php?all=3'><button class='btn btn-secondary'>Reject everyone</button></a>&nbsp;&nbsp;
    <a href='requests.php?all=4'><button class='btn btn-secondary'>Reject only if bad vet</button></a><br><br>
    <br>";
$string['buttonstopreminders'] = "<a href='requests.php?nomail=1'>"
        . "<button class='btn btn-secondary'>Do not send mails for these demands.</button></a>";
$string['buttonsendremindersagain'] = "<a href='requests.php?nomail=2'>"
        . "<button class='btn btn-secondary'>Send these demands by mail every monday morning.</button></a>";
$string['coursevet'] = "VET of the course";
$string['coursename'] = "Name of the course";
$string['askedon'] = "Date of the demand";
$string['askedby'] = "Name of the asker";
$string['mailasker'] = "Mail of the asker";
$string['vetasker'] = "VETs of the asker";
$string['answer'] = "Answer";
$string['accept'] = "Accept";
$string['reject'] = "Reject";
$string['headertableyourdemands'] = "<h2>Demands you have made</h2>
    <a href=/course/index.php><button class='btn btn-secondary' type=button>Add a demand</button></a>
    <br><br>";
$string['waitingrequest'] = "Pending requests";
$string['acceptedrequest'] = "Accepted requests";
$string['rejectedrequest'] = "Rejected requests";
$string['subjectnewdemand'] = "New enrolment demand in the course {$a}";
$string['newdemandmail'] = "You have received a new enrolment demand in the course {$a}
    <p>To answer it, please log into CoursUCP
    (<a href='https://cours.u-cergy.fr'>https://cours.u-cergy.fr</a>)
    and consult the bloc \"Enrolment on demands\" on your dashboard. </p>
    <p>You can also go directly to the page
    <a href='https://cours.u-cergy.fr/enrol/demands/requests.php'>
    https://cours.u-cergy.fr/enrol/demands/requests.php</a>.</p>
    <p>On that page you can also ask to no longer receive these emails.</p>
    <p><br>
    CoursUCP, Your pedagogic platform.</p>";