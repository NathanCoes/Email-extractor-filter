<?php
//A simple script to clean emails from a CSV document 

$input_name_file = "document";
$exclude = [
    'contabilidad', 'gefran', 'drivemotionandcontrol', 'support', 'technical', 'helpdesk', 'help@sleepcentral.com',
    'drivemotion', 'facturacion', 'italiantextil', 'italian', 'facturas', 'direccion', 'ayuda', 'login',
    'asistencia', 'no-reply', 'reply', 'account', 'notice', 'notification', 'interjet', 'mailengine.mx', 'no-responder',
    'info@', 'mantenimiento', 'security', 'facebook', 'twitter', 'youtube', 'latinbits', 'soporte', 'tecnico', 'test',
    'netsuite'
    ];
$debug = false;

include_once("functions.php");

date_default_timezone_set('America/Chihuahua');
$date = date('dmYhi');
$date2 = date('dmY');
$files_folder = "files/uploaded/$date2";
$count_emails = 0;
$count_emails_excluded = 0;
$count_noemails = 0;
$content_emails = "";

if ($_FILES){
    if ($_FILES[$input_name_file]['type'] != "text/csv"){
        return "El documento cargado no es de tipo .csv";
    }
    
    $file_name = $_FILES[$input_name_file]['name'];
    
    $explode = explode(".", $file_name);
    
    if (count($explode) > 2 ) return "Ops!!! Asegurese que el documento no tenga más de dos '.' en el nombre de esté.";
    
    $file_name = $explode[0]."_".$date.".".$explode[1];
    
    $file_dir = $_FILES[$input_name_file]['tmp_name'];
    
    $folders = ['db_emails','db_emails/csv','db_emails/txt', "db_emails/csv/$date2",
                "db_emails/txt/$date2", 'files', 'files/uploaded', $files_folder];

    validate_folders($folders);
    
    if (move_uploaded_file($file_dir, "$files_folder/$file_name")){
        $file_dir = "$files_folder/$file_name";
        
        $document_csv = fopen("db_emails/csv/$date2/clear_emails_$date.csv", "w+");
        $document_txt = fopen("db_emails/txt/$date2/clear_emails_$date.txt", "w+");
        
    
        $emails = file_get_contents($file_dir);
        $emails = str_replace([';',','],"\n", $emails);
        $explode = explode("\n", $emails);
    
        foreach ($explode as $mkey => $email) {
    
            $found = false;
            $email = strtolower(trim($email));

            $valid_reg_emails = email_registered($email,$content_emails);
    
            // var_dump(email_registered($email,$content_emails));
            // die;
            if (!$valid_reg_emails){

                if (is_email($email)){
                    $count_emails++;
                }else{
                    $count_noemails++;
                }

                foreach ($exclude as $xkey => $exclution) {
                    $exc = strpos($email, $exclution);
                    $filter = is_email($email);
                    
                    if ($debug){echo "exc found: $found || $email | $exclution -> $exc | Is email? $filter | Is registered? $valid_reg_emails";
                        echo "<br><br>";}
                    if ($exc != ""){
                        if ($found == false){
                            $count_emails_excluded++;
                        }
                        $found = true;
                    }
                }
                if ($found === false && is_email($email)){
                    if ($debug){echo "================<br>
                        $email<br>";
                        echo "====included====<br>";}

                    $content_emails .= $email."\n";
                }
            }
        }
        if ($count_emails < 0){
            fwrite($document_csv, $content_emails);
            fwrite($document_txt, $content_emails); 
        } 
        fclose($document_csv);
        fclose($document_txt);

        echo "<table>
                <thead>
                    <th>Emails founded</th>
                    <th>Emails excluded</th>
                    <th>No emails detected</th>
                </thead>
                <tbody>
                    <td>$count_emails</td>
                    <td>$count_emails_excluded</td>
                    <td>$count_noemails</td>
                </tbody>
            </table>";
    }else{
        echo "Error al subir el documento";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clean Emails by Ing. Jonathan Alcocer</title>
</head>
<body>
    <h2>Clean Emails</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="document">Document</label>
        <br>
        <input type="file" name="document" accept=".csv">
        <br>
        <input type="submit" value="Enviar">
    </form>
</body>
</html>