<?php

    $output = fopen("submissions.txt", "a+");
    fwrite($output, "=======================================\n");
    fwrite($output, date(DATE_RFC2822, time()) . "\n");
    fwrite($output, "=======================================\n");

    fwrite($output, print_r($_POST, true));
    
    fwrite($output, "\n\n");
    fclose($output);

    echo "success";
?>
