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

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class enrol_demands_validate_form extends moodleform {

    protected $instance;

    // Add elements to form.
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('editor', 'custommessage', get_string('custommessage', 'enrol_demands'));
        $mform->setType('reject', PARAM_TEXT);
        $mform->addElement('hidden', 'reject');
        $mform->setType('reject', PARAM_INT);
        $mform->setDefault('reject', 0);
        $mform->addElement('hidden', 'enrol');
        $mform->setType('enrol', PARAM_INT);
        $mform->setDefault('enrol', 0);
        $mform->addElement('hidden', 'all');
        $mform->setType('all', PARAM_INT);
        $mform->setDefault('all', 0);

        $this->add_action_buttons();

    }

    // Custom validation should be added here.
    public function validation($data, $files) {

        return array();
    }
}