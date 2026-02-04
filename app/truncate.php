<?php
// Truncate posts
function truncate($text, $length = 200, $id = 1)
{
    if (strlen($text) <= $length) {
        return $text;
    } else {
        return substr($text, 0, $length) . '<a href="show.php?id=' . $id . '"> Read More...</a>';
    }
}
