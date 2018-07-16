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
 * File : provider.php
 * RGPD file
 */

defined('MOODLE_INTERNAL') || die();

namespace enrol_demands\privacy;
use core_privacy\local\metadata\collection;

class provider implements
        // This plugin does store personal user data.
        \core_privacy\local\metadata\provider {

    public static function get_metadata(collection $collection) : collection {

        // Here you will add more items into the collection.

        $collection->add_database_table(
            'enrol_demands',
            [
                'enrolid' => 'privacy:metadata:enrol_demands:enrolid',
                'studentid' => 'privacy:metadata:enrol_demands:studentid',
                'askedat' => 'privacy:metadata:enrol_demands:askedat',
                'answeredat' => 'privacy:metadata:enrol_demands:answeredat',
                'answer' => 'privacy:metadata:enrol_demands:answer',
                'answererid' => 'privacy:metadata:enrol_demands:answererid',
                'mailedat' => 'privacy:metadata:enrol_demands:mailedat',

            ],
            'privacy:metadata:enrol_demands'
        );

        $collection->add_user_preference('"message_provider_enrol_demands_demands_',
        'privacy:metadata:preference:message_provider_enrol_demands_demands_');
        $collection->add_user_preference('"message_provider_enrol_demands_enroled_',
        'privacy:metadata:preference:message_provider_enrol_demands_enroled_');
        $collection->add_user_preference('"message_provider_enrol_demands_rejected_',
        'privacy:metadata:preference:message_provider_enrol_demands_rejected_');
        $collection->add_user_preference('"message_provider_enrol_demands_reminder_',
        'privacy:metadata:preference:message_provider_enrol_demands_reminder_');

        return $collection;
    }

    public static function get_contexts_for_userid(int $userid) : contextlist {

        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT c.id FROM {context} WHERE (contextlevel = :contextlevel AND instanceid IN
            (SELECT courseid FROM {enrol} WHERE id IN
                (SELECT enrolid FROM {enrol_demand} WHERE studentid = :userid)
            )
        )";

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        $sqlanswer = "SELECT c.id FROM {context} WHERE (contextlevel = :contextlevel AND instanceid IN
            (SELECT courseid FROM {enrol} WHERE id IN
                (SELECT enrolid FROM {enrol_demand} WHERE answererid = :userid)
            )
        )";

        $contextlist->add_from_sql($sqlanswer, $params);
    }

    public static function export_user_data(approved_contextlist $contextlist) {

        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {

            $sql = "SELECT * FROM {enrol_demands} WHERE studentid = $userid  AND enrolid IN "
                    . "(SELECT id FROM {enrol} WHERE enrol LIKE 'cohort' AND courseid IN "
                    . "(SELECT instanceid FROM {context} WHERE id = $context->id))";

            $results = $DB->get_records_sql($sql);

            foreach ($results as $result) {
                $data = (object) [
                    'studentid' => $result->studentid,
                ];

                \core_privacy\local\request\writer::with_context(
                        $context)->export_data([
                            get_string('pluginname', 'enrol_demands')], $data);
            }

            $sqlanswer = "SELECT * FROM {enrol_demands} WHERE answerer = $userid  AND enrolid IN "
                    . "(SELECT id FROM {enrol} WHERE enrol LIKE 'cohort' AND courseid IN "
                    . "(SELECT instanceid FROM {context} WHERE id = $context->id))";

            $resultsanswer = $DB->get_records_sql($sqlanswer);

            foreach ($resultsanswer as $result) {
                $data = (object) [
                    'studentid' => $result->studentid,
                ];

                \core_privacy\local\request\writer::with_context(
                        $context)->export_data([
                            get_string('pluginname', 'enrol_demands')], $data);
            }
        }
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {

        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {

            $sql = "SELECT * FROM {enrol_demands} WHERE studentid = $userid  AND enrolid IN "
                    . "(SELECT id FROM {enrol} WHERE enrol LIKE 'cohort' AND courseid IN "
                    . "(SELECT instanceid FROM {context} WHERE id = $context->id))";

            $DB->delete_records_sql($sql);

            $sqlanswer = "SELECT * FROM {enrol_demands} WHERE answererid = $userid  AND enrolid IN "
                    . "(SELECT id FROM {enrol} WHERE enrol LIKE 'cohort' AND courseid IN "
                    . "(SELECT instanceid FROM {context} WHERE id = $context->id))";

            $DB->delete_records_sql($sqlanswer);
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $sql = "SELECT * FROM {enrol_demands} WHERE enrolid IN "
                    . "(SELECT id FROM {enrol} WHERE enrol LIKE 'cohort' AND courseid IN "
                    . "(SELECT instanceid FROM {context} WHERE id = $context->id))";

        $DB->delete_records_sql($sql);
    }
}