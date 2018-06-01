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
 * File : validate_form.php
 * Form file
 */

require_once("$CFG->libdir/formslib.php");

class enrol_demands_validate_form extends moodleform {

    protected $instance;

    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $paramdata = $this->_customdata;

        $mform->addElement('text', 'custommessage', get_string('custommessage', 'enrol_demands'));
        $mform->addElement('hidden', 'reject', $paramdata->reject);
        $mform->addElement('hidden', 'enrol', $paramdata->enrol);
        $mform->addElement('hidden', 'all', $paramdata->all);

        $this->add_action_buttons();

    }
    //Custom validation should be added here
    function validation($data, $files) {

        return array();
    }
}