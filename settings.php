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
 * Local plugin "Boost navigation fumbling" - Settings
 *
 * @package    local_boostnavigation
 * @copyright  2017 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

if ($hassiteconfig) {
    // New settings page.
    $page = new admin_settingpage('local_boostnavigation',
            get_string('pluginname', 'local_boostnavigation', null, true));


    if ($ADMIN->fulltree) {
        // Add remove nodes heading.
        $page->add(new admin_setting_heading('local_boostnavigation/removenodesheading',
                get_string('setting_removenodesheading', 'local_boostnavigation', null, true),
                ''));

        // Create remove myhome node control widget (switch label and description depending on what will really happen on the site).
        if (get_config('core', 'defaulthomepage') == HOMEPAGE_SITE) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removemyhomenode',
                    get_string('setting_removehomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removehomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else if (get_config('core', 'defaulthomepage') == HOMEPAGE_MY) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removemyhomenode',
                    get_string('setting_removedashboardnode', 'local_boostnavigation', null, true),
                    get_string('setting_removedashboardnode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else if (get_config('core', 'defaulthomepage') == HOMEPAGE_USER) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removemyhomenode',
                    get_string('setting_removefirsthomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removefirsthomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else { // This should not happen.
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removemyhomenode',
                    get_string('setting_removehomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removehomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        }

        // Create remove home node control widget (switch label and description depending on what will really happen on the site).
        if (get_config('core', 'defaulthomepage') == HOMEPAGE_SITE) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removehomenode',
                    get_string('setting_removedashboardnode', 'local_boostnavigation', null, true),
                    get_string('setting_removedashboardnode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else if (get_config('core', 'defaulthomepage') == HOMEPAGE_MY) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removehomenode',
                    get_string('setting_removehomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removehomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else if (get_config('core', 'defaulthomepage') == HOMEPAGE_USER) {
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removehomenode',
                    get_string('setting_removesecondhomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removesecondhomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        } else { // This should not happen.
            $page->add(new admin_setting_configcheckbox('local_boostnavigation/removehomenode',
                    get_string('setting_removehomenode', 'local_boostnavigation', null, true),
                    get_string('setting_removehomenode_desc', 'local_boostnavigation', null, true).'<br />'.
                            get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                    0));
        }

        // Create remove calendar node control widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/removecalendarnode',
                get_string('setting_removecalendarnode', 'local_boostnavigation', null, true),
                get_string('setting_removecalendarnode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                0));

        // Create remove privatefiles node control widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/removeprivatefilesnode',
                get_string('setting_removeprivatefilesnode', 'local_boostnavigation', null, true),
                get_string('setting_removeprivatefilesnode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true),
                0));

        // Create remove mycourses node control widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/removemycoursesnode',
                get_string('setting_removemycoursesnode', 'local_boostnavigation', null, true),
                get_string('setting_removemycoursesnode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_removenodestechnicalhint', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_removemycoursesnodeperformancehint', 'local_boostnavigation', null, true),
                0));

        // Add insert nodes heading.
        $page->add(new admin_setting_heading('local_boostnavigation/insertnodesheading',
                get_string('setting_insertnodesheading', 'local_boostnavigation', null, true),
                ''));

        // Create insert custom nodes widget.
        $page->add(new admin_setting_configtextarea('local_boostnavigation/insertcustomnodes',
                get_string('setting_insertcustomnodes', 'local_boostnavigation', null, true),
                get_string('setting_insertcustomnodes_desc', 'local_boostnavigation', null, true),
                '',
                PARAM_RAW));

        // Add collapse nodes heading.
        $page->add(new admin_setting_heading('local_boostnavigation/collapsenodesheading',
                get_string('setting_collapsenodesheading', 'local_boostnavigation', null, true),
                ''));

        // Create my courses node collapse widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/collapsemycoursesnode',
                get_string('setting_collapsemycoursesnode', 'local_boostnavigation', null, true),
                get_string('setting_collapsemycoursesnode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_collapsenodestechnicalhint', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_collapsemycoursesnodeperformancehint', 'local_boostnavigation', null, true).'<br />'.
                        '<strong>'.get_string('setting_collapsenodestemplatehint', 'local_boostnavigation', null, true).'</strong>',
                0));

        // Add insert course nodes heading.
        $page->add(new admin_setting_heading('local_boostnavigation/insertcoursenodesheading',
                get_string('setting_insertcoursenodesheading', 'local_boostnavigation', null, true),
                ''));

        // Create insert course sections course node widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/insertcoursesectionscoursenode',
                get_string('setting_insertcoursesectionscoursenode', 'local_boostnavigation', null, true),
                get_string('setting_insertcoursesectionscoursenode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_insertnodescollapsehint', 'local_boostnavigation', null, true),
                0));

        // Create insert activities course node widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/insertactivitiescoursenode',
                get_string('setting_insertactivitiescoursenode', 'local_boostnavigation', null, true),
                get_string('setting_insertactivitiescoursenode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_insertnodescollapsehint', 'local_boostnavigation', null, true),
                0));

        // Create insert resources course node widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/insertresourcescoursenode',
                get_string('setting_insertresourcescoursenode', 'local_boostnavigation', null, true),
                get_string('setting_insertresourcescoursenode_desc', 'local_boostnavigation', null, true),
                0));

        // Create insert custom course nodes widget.
        $page->add(new admin_setting_configtextarea('local_boostnavigation/insertcustomcoursenodes',
                get_string('setting_insertcustomcoursenodes', 'local_boostnavigation', null, true),
                get_string('setting_insertcustomcoursenodes_desc', 'local_boostnavigation', null, true),
                '',
                PARAM_RAW));

        // Add collapse course nodes heading.
        $page->add(new admin_setting_heading('local_boostnavigation/collapsecoursenodesheading',
                get_string('setting_collapsecoursenodesheading', 'local_boostnavigation', null, true),
                ''));

        // Create course sections course node collapse widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/collapsecoursesectionscoursenode',
                get_string('setting_collapsecoursesectionscoursenode', 'local_boostnavigation', null, true),
                get_string('setting_collapsecoursesectionscoursenode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_collapsenodestechnicalhint', 'local_boostnavigation', null, true).'<br />'.
                        '<strong>'.get_string('setting_collapsenodestemplatehint', 'local_boostnavigation', null, true).'</strong>',
                0));

        // Create activities course node collapse widget.
        $page->add(new admin_setting_configcheckbox('local_boostnavigation/collapseactivitiescoursenode',
                get_string('setting_collapseactivitiescoursenode', 'local_boostnavigation', null, true),
                get_string('setting_collapseactivitiescoursenode_desc', 'local_boostnavigation', null, true).'<br />'.
                        get_string('setting_collapsenodestechnicalhint', 'local_boostnavigation', null, true).'<br />'.
                        '<strong>'.get_string('setting_collapsenodestemplatehint', 'local_boostnavigation', null, true).'</strong>',
                0));
    }

    // Add settings page to the appearance settings category.
    $ADMIN->add('appearance', $page);
}
