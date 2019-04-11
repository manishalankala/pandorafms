<?php

/**
 * Pandora FMS - http://pandorafms.com
 * ==================================================
 * Copyright (c) 2005-2010 Artica Soluciones Tecnologicas
 * Please see http://pandorafms.org for full contribution list
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation for version 2.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * Load global vars

 * @package    category
 * @subpackage category
 */

global $config;

// Includes.
require_once $config['homedir'].'/include/functions_notifications.php';

// Load the header.
require $config['homedir'].'/operation/users/user_edit_header.php';

if (get_parameter('change_label', 0)) {
    $label = get_parameter('label', '');
    $source = get_parameter('source', 0);
    $user = get_parameter('user', '');
    $value = get_parameter('value', 0) ? 1 : 0;

    // Update the label value.
    ob_clean();
    echo json_encode(
        [
            'result' => notifications_set_user_label_status(
                $source,
                $user,
                $label,
                $value
            ),
        ]
    );
    return;
}


echo '<div id="user-notifications-wrapper" class="white_box table_div table_three_columns">
        <div class="table_thead">
            <div class="table_th"></div>
            <div class="table_th">'.__('Enable').'</div>
            <div class="table_th">'.__('Also receive an email').'</div>
        </div>';

        $sources = notifications_get_all_sources();
foreach ($sources as $source) {
    echo '<div class="table_tbody">';
    $table_content = [
        $source['description'],
        notifications_print_user_switch($source, $id, 'enabled'),
        notifications_print_user_switch($source, $id, 'also_mail'),
    ];
    echo '<div class="table_td">'.$source['description'].'</div>';
    echo '<div class="table_td">'.notifications_print_user_switch($source, $id, 'enabled').'</div>';
    echo '<div class="table_td">'.notifications_print_user_switch($source, $id, 'also_mail').'</div>';
    echo '</div>';
}

echo '</div>';

// Print id user to handle it on js.
html_print_input_hidden('id_user', $id);

?>
<script>
// Encapsulate the code
(function() {
    function notifications_change_label(event) {
        event.preventDefault();
        var check = document.getElementById(event.target.id);
        if (check === null) return;

        var match = /notifications-user-([0-9]+)-label-(.*)/
            .exec(event.target.id);

        jQuery.post ("ajax.php",
            {
                "page" : "operation/users/user_edit_notifications",
                "change_label" : 1,
                "label" : match[2],
                "source" : match[1],
                "user" : document.getElementById('hidden-id_user').value,
                "value": check.checked ? 1 : 0
            },
            function (data, status) {
                if (!data.result) {
                    console.error("Error changing configuration in database.");
                } else {
                    check.checked = !check.checked;
                }
            },
            "json"
        ).done(function(m){})
        .fail(function(xhr, textStatus, errorThrown){
            console.error(
                "Cannot change configuration in database. Server error.",
                xhr.responseText
            );
        });

    }
    var all_labels = document.getElementsByClassName(
        'notifications-user-label_individual'
    );
    for (var i = 0; i < all_labels.length; i++) {
        all_labels[i].addEventListener(
            'click', notifications_change_label, false
        );
    }
}());
</script>