<?php
// Fonctions utilitaires
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function display_message($message, $type = 'success') {
    echo '<div class="message ' . $type . '">' . $message . '</div>';
}

function format_date($date_string) {
    if (empty($date_string) || $date_string == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date('d/m/Y H:i', strtotime($date_string));
}
?>