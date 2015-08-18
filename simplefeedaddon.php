<?php
/*
Plugin Name: Gravity Forms Simple Feed Add-On
Plugin URI: http://www.gravityforms.com
Description: A simple add-on to demonstrate the use of the Add-On Framework
Version: 1.0
Author: Rocketgenius
Author URI: http://www.rocketgenius.com

------------------------------------------------------------------------
Copyright 2012-2013 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_feed_addon_framework();

    class GFSimpleFeedAddOn extends GFFeedAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "simplefeedaddon";
        protected $_path = "simplefeedaddon/simplefeedaddon.php";
        protected $_full_path = __FILE__;
        protected $_title = "Gravity Forms Simple Feed Add-On";
        protected $_short_title = "Simple Feed Add-On";

        public function plugin_page() {
            ?>
            This page appears in the Forms menu
            <?php
            
            //loading Gravity Forms tooltips
            require_once(GFCommon::get_base_path() . "/tooltips.php");
            
            // Show default feed list
            self::feed_list_page();
        }

        public function feed_settings_fields() {
            return array(
                array(
                    "title"  => "Simple Feed Settings",
                    "fields" => array(
                        array(
                            "label"   => "Feed name",
                            "type"    => "text",
                            "name"    => "feedName",
                            "tooltip" => "This is the tooltip",
                            "class"   => "small"
                        ),
                        array(
                            "label"   => "Textbox",
                            "type"    => "text",
                            "name"    => "mytextbox",
                            "tooltip" => "This is the tooltip",
                            "class"   => "small"
                        ),
                        array(
                            "label"   => "My checkbox",
                            "type"    => "checkbox",
                            "name"    => "mycheckbox",
                            "tooltip" => "This is the tooltip",
                            "choices" => array(
                                array(
                                    "label" => "Enabled",
                                    "name"  => "mycheckbox"
                                )
                            )
                        ),
                        array(
                            "name" => "mappedFields",
                            "label" => "Map Fields",
                            "type" => "field_map",
                            "field_map" => array(   array("name" => "email", "label" => "Email", "required" => 0),
                                                    array("name" => "name", "label" => "Name", "required" => 0)
                            )
                        ),
                        array(
                            "name" => "condition",
                            "label" => __("Condition", "simplefeedaddon"),
                            "type" => "feed_condition",
                            "checkbox_label" => __('Enable Condition', 'simplefeedaddon'),
                            "instructions" => __("Process this simple feed if", "simplefeedaddon")
                        ),
                    )
                )
            );
        }

        public function feed_list_columns() {
            return array(
                'feedName' => __('Name', 'simplefeedaddon'),
                'mytextbox' => __('My Textbox', 'simplefeedaddon')
            );
        }

        // customize the value of mytextbox before it's rendered to the list
        public function get_column_value_mytextbox($feed){
            return "<b>" . $feed["meta"]["mytextbox"] ."</b>";
        }

        public function plugin_settings_fields() {
            return array(
                array(
                    "title"  => "Simple Add-On Settings",
                    "fields" => array(
                        array(
                            "name"    => "textbox",
                            "tooltip" => "This is the tooltip",
                            "label"   => "This is the label",
                            "type"    => "text",
                            "class"   => "small"
                        )
                    )
                )
            );
        }

        public function scripts() {
            $scripts = array(
                array("handle"  => "my_script_js",
                      "src"     => $this->get_base_url() . "/js/my_script.js",
                      "version" => $this->_version,
                      "deps"    => array("jquery"),
                      "strings" => array(
                          'first'  => __("First Choice", "simplefeedaddon"),
                          'second' => __("Second Choice", "simplefeedaddon"),
                          'third'  => __("Third Choice", "simplefeedaddon")
                      ),
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "simplefeedaddon"
                          )
                      )
                ),

            );

            return array_merge(parent::scripts(), $scripts);
        }

        public function styles() {

            $styles = array(
                array("handle"  => "my_styles_css",
                      "src"     => $this->get_base_url() . "/css/my_styles.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array("field_types" => array("poll"))
                      )
                )
            );

            return array_merge(parent::styles(), $styles);
        }

        public function process_feed($feed, $entry, $form){
            $feedName = $feed["meta"]["feedName"];
            $mytextbox = $feed["meta"]["mytextbox"];
            $checkbox = $feed["meta"]["mycheckbox"];
            $mapped_email = $feed["meta"]["mappedFields_email"];
            $mapped_name = $feed["meta"]["mappedFields_name"];

            $email = $entry[$mapped_email];
            $name = $entry[$mapped_name];
        }
    }

    new GFSimpleFeedAddOn();
}
