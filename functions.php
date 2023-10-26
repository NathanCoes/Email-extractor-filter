<?php
    function is_email($string){
        if (filter_var($string, FILTER_VALIDATE_EMAIL)){
            return true;
        }

        return;
    }

    function email_registered($email, $content_emails){
        $check = strstr($content_emails, $email);

        if ($check != ""){
            return true;
        }else{
            return false;
        }
    }

    function validate_folders(array $folders){
        foreach ($folders as $key => $value) {
            if (!is_dir($value)){
                if(!mkdir($value)){
                    return "Fail in create the folder '$value'";
                }
            }
        }
    }
?>