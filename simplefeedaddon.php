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

        protected function feed_list_columns() {
            return array(
                'feedName' => __('Name', 'simplefeedaddon'),
                'mytextbox' => __('My Textbox', 'simplefeedaddon')
            );
        }

        // customize the value of mytextbox before it's rendered to the list
        public function get_column_value_mytextbox($feed){
            return "<b>" . $feed["meta"]["mytextbox"] ."</b>";
        }

        public function settings_my_custom_field_type(){
            ?>
            <div>
                My custom field contains a few settings:
            </div>
            <?php
                $this->settings_text(
                    array(
                        "label" => "A textbox sub-field",
                        "name" => "subtext",
                        "default_value" => "change me"
                    )
                );
                $this->settings_checkbox(
                    array(
                        "label" => "A checkbox sub-field",
                        "choices" => array(
                            array(
                                "label" => "Activate",
                                "name" => "subcheck",
                                "default_value" => true
                            )

                        )
                    )
                );
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

    }

    new GFSimpleFeedAddOn();
}