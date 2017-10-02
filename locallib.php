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
 * Local plugin "Boost navigation fumbling" - Local Library
 *
 * @package    local_boostnavigation
 * @copyright  2017 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Until MDL-58165 was integrated, Moodle core did not add a key to the privatefiles node when adding it to the navigation,
 * so we need to find it with some overhead on previous versions.
 *
 * @param global_navigation $navigation
 * @return navigation_node
 */
function local_boostnavigation_find_privatefiles_node(global_navigation $navigation) {
    // Get front page course node.
    if ($coursenode = $navigation->find('1', null)) {
        // Get children of the front page course node.
        $coursechildrennodeskeys = $coursenode->get_children_key_list();

        // Get text string to look for.
        $needle = get_string('privatefiles');

        // Check all children to find the privatefiles node.
        foreach ($coursechildrennodeskeys as $k) {
            // Get child node.
            $childnode = $coursenode->get($k);
            // Check if we have found the privatefiles node.
            if ($childnode->text == $needle) {
                // If yes, return the node.
                return $childnode;
            }
        }
    }

    // This should not happen.
    return false;
}


/**
 * Moodle core does not have a built-in functionality to get all keys of all children of a navigation node,
 * so we need to get these ourselves.
 *
 * @param navigation_node $navigationnode
 * @return array
 */
function local_boostnavigation_get_all_childrenkeys(navigation_node $navigationnode) {
    // Empty array to hold all children.
    $allchildren = array();

    // No, this node does not have children anymore.
    if (count($navigationnode->children) == 0) {
        return array();

        // Yes, this node has children.
    } else {
        // Get own own children keys.
        $childrennodeskeys = $navigationnode->get_children_key_list();
        // Get all children keys of our children recursively.
        foreach ($childrennodeskeys as $ck) {
            $allchildren = array_merge($allchildren, local_boostnavigation_get_all_childrenkeys($navigationnode->get($ck)));
        }
        // And add our own children keys to the result.
        $allchildren = array_merge($allchildren, $childrennodeskeys);

        // Return everything.
        return $allchildren;
    }
}



/**
 * This function takes one the plugin's custom nodes setting, builds the custom nodes and adds them to the given navigation_node.
 *
 * @param string $customnodes
 * @param navigation_node $node
 * @param string $beforekey
 * @param bool $showinflatnavigation
 * @return void
 */
function local_boostnavigation_build_custom_nodes(string $customnodes,
                                                  navigation_node $node,
                                                  string $beforekey=null,
                                                  bool $showinflatnavigation=true)
{
    global $OUTPUT;

    // Initialize counter which is later used for the node IDs.
    $nodecount = 0;

    // Make a new array on delimiter "new line".
    $lines = explode("\n", $customnodes);
    // Parse node settings.
    foreach ($lines as $line) {
        $line = trim($line);
        if (strlen($line) == 0) {
            continue;
        }
        $nodeicon = null;
        $nodeurl = null;
        $nodetitle = null;
        $nodevisible = false;
        // Make a new array on delimiter "|".
        $settings = explode('|', $line);
        // Check for the mandatory conditions first.
        // If array contains too less or too many settings, do not proceed and therefore do not create the node.
        // Furthermore check it at least the first three mandatory params are not an empty string.
        if (count($settings) >= 3 && count($settings) <= 4 &&
            $settings[0] !== '' && $settings[1] !== '' && $settings[2] !== '') {
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if (!empty($setting)) {
                    switch ($i) {
                        // Check for the mandatory first param: icon.
                        case 0:
                            $moodlepixpattern = '~^[a-z]/[\w\d-_]+$~';
                            $faiconpattern = '~^fa-[\w\d-]+$~';
                            // Check if it's a Moodle pix icon.
                            if (preg_match($moodlepixpattern, $setting) > 0) {
                                $nodeicon = $OUTPUT->pix_icon($setting, '');
                                $nodevisible = true;
                            }
                            else if (preg_match($faiconpattern, $setting) > 0) { // Check if it's a Font Awesome icon.
                                $nodeicon = '<i class="fa ' . $setting . '"></i>';
                                $nodevisible = true;
                            }
                            break;
                        // Check for the mandatory second param: URL.
                        case 1:
                            // Get the URL.
                            try {
                                $nodeurl = new moodle_url($setting);
                                $nodevisible = true;
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL. We just hide the icon in this case.
                                $nodeurl = null;
                                $nodevisible = false;
                            }
                            break;
                        // Check for the mandatory third param: text for title and alt attribute.
                        case 2:
                            $nodetitle = $setting;
                            $nodevisible = true;
                            break;
                        // Check for the optional fourth param: language support.
                        case 3:
                            // Only proceed if something is entered here. This parameter is optional.
                            // If no language is given the icon will be displayed in the navbar by default.
                            $nodelanguages = array_map('trim', explode(',', $setting));
                            $nodevisible &= in_array(current_language(), $nodelanguages);
                            break;
                    }
                }
            }
        }

        // Add a custom node to the given navigation_node.
        // This is if all mandatory params are set and the node matches the optional given language setting.
        if ($nodevisible) {
            // Create custom node.
            $customnode = navigation_node::create($nodetitle,
                    $nodeurl,
                    global_navigation::TYPE_CUSTOM,
                    null,
                    'localboostnavigationcustom'.++$nodecount,
                    new pix_icon('icon', '', 'mod_page')); // Boost ignores a navigation node's icon currently,
                                                           // but we set it for future-proofness.
                                                           // TODO
            // Show the custom node in Boost's nav drawer if requested
            if ($showinflatnavigation) {
                $customnode->showinflatnavigation = true;
            }

            // Add the custom node to the given navigation_node.
            $node->add_node($customnode, $beforekey);
        }
    }
}
