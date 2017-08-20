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
 * Local plugin "Boost navigation fumbling" - Library
 *
 * @package    local_boostnavigation
 * @copyright  2017 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fumble with Moodle's global navigation by leveraging Moodle's *_extend_navigation() hook.
 *
 * @param global_navigation $navigation
 */
function local_boostnavigation_extend_navigation(global_navigation $navigation) {
    global $CFG;

    // Fetch config.
    $config = get_config('local_boostnavigation');

    // Include local library.
    require_once(dirname(__FILE__) . '/locallib.php');

    // Check if admin wanted us to remove the myhome node from Boost's nav drawer.
    // We have to check explicitely if the configurations are set because this function will already be
    // called at installation time and would then throw PHP notices otherwise.
    if (isset($config->removemyhomenode) && $config->removemyhomenode == true) {
        // If yes, do it.
        // Hide myhome node (which is basically the $navigation global_navigation node).
        $navigation->showinflatnavigation = false;
    }

    // Check if admin wanted us to remove the home node from Boost's nav drawer.
    if (isset($config->removehomenode) && $config->removehomenode == true) {
        // If yes, do it.
        if ($homenode = $navigation->find('home', global_navigation::TYPE_ROOTNODE)) {
            // Hide home node.
            $homenode->showinflatnavigation = false;
        }
    }

    // Check if admin wanted us to remove the calendar node from Boost's nav drawer.
    if (isset($config->removecalendarnode) && $config->removecalendarnode == true) {
        // If yes, do it.
        if ($calendarnode = $navigation->find('calendar', global_navigation::TYPE_CUSTOM)) {
            // Hide calendar node.
            $calendarnode->showinflatnavigation = false;
        }
    }

    // Check if admin wanted us to remove the privatefiles node from Boost's nav drawer.
    if (isset($config->removeprivatefilesnode) && $config->removeprivatefilesnode == true) {
        // If yes, do it.
        // We have to support Moodle core 3.2 and 3.3 versions with MDL-58165 not yet integrated.
        if (moodle_major_version() == '3.2' && $CFG->version < 2016120503.05 ||
                moodle_major_version() == '3.3' && $CFG->version < 2017051500.02) {
            if ($privatefilesnode = local_boostnavigation_find_privatefiles_node($navigation)) {
                // Hide privatefiles node.
                $privatefilesnode->showinflatnavigation = false;
            }
        } else {
            if ($privatefilesnode = $navigation->find('privatefiles', global_navigation::TYPE_SETTING)) {
                // Hide privatefiles node.
                $privatefilesnode->showinflatnavigation = false;
            }
        }
    }

    // Check if admin wanted us to remove the mycourses node from Boost's nav drawer.
    if (isset($config->removemycoursesnode) && $config->removemycoursesnode == true) {
        // If yes, do it.
        if ($mycoursesnode = $navigation->find('mycourses', global_navigation::TYPE_ROOTNODE)) {
            // Hide mycourses node.
            $mycoursesnode->showinflatnavigation = false;

            // Hide all courses below the mycourses node.
            $mycourseschildrennodeskeys = $mycoursesnode->get_children_key_list();
            foreach ($mycourseschildrennodeskeys as $k) {
                // If the admin decided to display categories, things get slightly complicated.
                if ($CFG->navshowmycoursecategories) {
                    // We need to find all children nodes first.
                    $allchildrennodes = local_boostnavigation_get_all_childrenkeys($mycoursesnode->get($k));
                    // Then we can hide each children node.
                    // Unfortunately, the children nodes have navigation_node type TYPE_MY_CATEGORY or navigation_node type
                    // TYPE_COURSE, thus we need to search without a specific navigation_node type.
                    foreach ($allchildrennodes as $cn) {
                        $mycoursesnode->find($cn, null)->showinflatnavigation = false;
                    }

                    // Otherwise we have a flat navigation tree and hiding the courses is easy.
                } else {
                    $mycoursesnode->get($k)->showinflatnavigation = false;
                }
            }
        }
    }

    // Check if admin wanted us to remove the badge node from Boost's nav drawer (only if there are no badges).
    if ($CFG->enablebadges == true && isset($config->removebadgenode) && $config->removebadgenode == true) {
        // Check if we are inside a course and should have a course navigation, otherwise the badge node isn't shown anyway.
        global $PAGE;
        if ($PAGE->context->contextlevel == CONTEXT_COURSE || $PAGE->context->contextlevel == CONTEXT_MODULE) {
            // Check if there is any badge in the course.
            require_once($CFG->dirroot . '/lib/badgeslib.php');
            $totalbadges = count(badges_get_badges(BADGE_TYPE_COURSE, $PAGE->course->id, '', '' , 0, 0));

            // If there are no badges, hide the node.
            if ($totalbadges == 0) {
                if ($badgenode = $navigation->find('badgesview', global_navigation::TYPE_SETTING)) {
                    $badgenode->remove();
                }
            }
        }
    }

    // Check if admin wanted us to remove the competency node from Boost's nav drawer (only if there are no competencies).
    if (get_config('core_competency', 'enabled') == true && isset($config->removecompetencynode) && $config->removecompetencynode == true) {
        // Check if we are inside a course and should have a course navigation, otherwise the competency node isn't shown anyway.
        global $PAGE;
        if ($PAGE->context->contextlevel == CONTEXT_COURSE || $PAGE->context->contextlevel == CONTEXT_MODULE) {
            // Check if there is any competency in the course.
            require_once($CFG->dirroot . '/competency/classes/course_competency.php');
            $totalcompetencies = core_competency\course_competency::count_competencies($PAGE->course->id);

            // If there are no competencies, hide the node.
            if ($totalcompetencies == 0) {
                if ($competencynode = $navigation->find(2, global_navigation::TYPE_SETTING)) {
                    $competencynode->remove();
                }
            }
        }
    }
}


/**
 * Fumble with Moodle's global navigation by leveraging Moodle's *_extend_navigation_course() hook.
 * Removed nodes are still available from the "Plus ..." menu item.
 *
 * @param global_navigation $navigation
 */
function local_boostnavigation_extend_navigation_course($navigation) {

    global $PAGE;

    // Fetch config.
    $config = get_config('local_boostnavigation');

    // Add competency page in complete settings page (after clicking on "Plus ..." menu item).
    if (isset($config->addcompetencynode) && $config->addcompetencynode == true) {
        if (stripos($PAGE->bodyclasses, 'path-course-view') === false) {
            // Just a link to course competency.
            $courseid = $PAGE->course->id;
            $title = get_string('competencies', 'core_competency');
            $path = new moodle_url("/admin/tool/lp/coursecompetencies.php", array('courseid' => $courseid));
            $navigation->add($title, $path, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/competencies', ''));
        }
    }

    // Hiding "advanced" functionnalities. They are still available in complete course settings page.
    if (stripos($PAGE->bodyclasses, 'path-course-view') !== false) {
        // Remove gradebook setup
        if (isset($config->removegradebooksetupnode) && $config->removegradebooksetupnode == true) {
            if ($gradebooksetupnode = $navigation->find('gradebooksetup', navigation_node::TYPE_SETTING)) {
                $gradebooksetupnode->hide();
            }
        }
    }
}
