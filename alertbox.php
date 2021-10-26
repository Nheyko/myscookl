<?php

if (!empty($errorMsg)) {
    echo "
        <div class='alert alert-danger' role='alert'>
        $errorMsg
        </div>
    ";
}

if (!empty($successMsg)) {
    echo "
        <div class='alert alert-success' role='alert'>
        $successMsg
        </div>
    ";
}

?>