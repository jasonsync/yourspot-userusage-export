<?php
// if file has been uploaded
if (isset($_FILES) && isset($_FILES['fileupload'])&&isset($_FILES['fileupload']['name'])&&isset($_POST['upload_delimiter'])&&isset($_POST['download_delimiter'])) {
    $output_filename = pathinfo($_FILES['fileupload']['name'], PATHINFO_FILENAME).'_summarized.csv';
    $handle = fopen($_FILES['fileupload']['tmp_name'], "r");
    $headers = fgetcsv($handle, 0, $_POST['upload_delimiter']);
    // var_dump($headers);

    // index columns
    $idx_UserName = null;
    $idx_DeviceMacAddress = null;
    $idx_FirstName = null;
    $idx_SessionSecondsTime = null;
    $idx_BytesOUT = null;
    $idx_BytesIN = null;
    for ($i=0; $i < count($headers); $i++) {
        if (strpos($headers[$i], 'UserName') !== false) {
            $idx_UserName = $i;
        }
        if (strpos($headers[$i], 'DeviceMacAddress') !== false) {
            $idx_DeviceMacAddress = $i;
        }
        if (strpos($headers[$i], 'FirstName') !== false) {
            $idx_FirstName = $i;
        }
        if (strpos($headers[$i], 'SessionSecondsTime') !== false) {
            $idx_SessionSecondsTime = $i;
        }
        if (strpos($headers[$i], 'BytesOUT') !== false) {
            $idx_BytesOUT = $i;
        }
        if (strpos($headers[$i], 'BytesIN') !== false) {
            $idx_BytesIN = $i;
        }
    }

    // make array of all records (but only important fields)
    $arr_sessions_onlyimportantfields = array();
    // for each order...
    while (($row = fgetcsv($handle, 0, $_POST['upload_delimiter'])) !== false) {
        $session_onlyimportantfields = array();
        // get common fields...
        $session_onlyimportantfields['UserName'] = $row[$idx_UserName];
        $session_onlyimportantfields['DeviceMacAddress'] = $row[$idx_DeviceMacAddress];
        $session_onlyimportantfields['FirstName'] = $row[$idx_FirstName];
        $session_onlyimportantfields['SessionSecondsTime'] = $row[$idx_SessionSecondsTime];
        $session_onlyimportantfields['BytesOUT'] = $row[$idx_BytesOUT];
        $session_onlyimportantfields['BytesIN'] = $row[$idx_BytesIN];

        $arr_sessions_onlyimportantfields[] = $session_onlyimportantfields;
    }
    fclose($handle);


    // group fields by UserName, sum usage data
    $arr_grouped_data = array();
    for ($i=0; $i < count($arr_sessions_onlyimportantfields); $i++) {
        $session_record = $arr_sessions_onlyimportantfields[$i];
        $username = $session_record['UserName'];
        $session_mac_address = $session_record['DeviceMacAddress'];
        // if record exists for user, update, else add it.
        if (isset($arr_grouped_data[$username])) {
            $arr_grouped_data[$username]['GigabytesUsed'] = round($arr_grouped_data[$username]['GigabytesUsed'] + (($session_record['BytesOUT']/1024/1024/1024) + ($session_record['BytesOUT']/1024/1024/1024)), 2);
            $arr_grouped_data[$username]['Duration'] = round($arr_grouped_data[$username]['Duration'] +($session_record['SessionSecondsTime']/60/60), 2);
            // calculate number of devices for the user
            if (!in_array($session_mac_address, $arr_grouped_data[$username]['MacAddresses'])) {
                $arr_grouped_data[$username]['MacAddresses'][] = $session_mac_address;
                $arr_grouped_data[$username]['NumDevices'] = $arr_grouped_data[$username]['NumDevices'] + 1;
            }
        } else {
            $arr_grouped_data[$username] = array(
              'FirstName'=>$session_record['FirstName'],
              'Duration'=>round($session_record['SessionSecondsTime']/60/60, 2),
              'GigabytesUsed'=>round((($session_record['BytesOUT']/1024/1024/1024) + ($session_record['BytesIN']/1024/1024/1024)), 2),
              'MacAddresses'=>array($session_record['DeviceMacAddress']),
              'NumDevices'=> 1
          );
        }
    }

    // array to export into csv (only fields we need)
    $export_array = array();
    foreach ($arr_grouped_data as $record) {
        $export_array[] = array($record['FirstName'],$record['NumDevices'],$record['GigabytesUsed'],$record['Duration']);
    }


    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$output_filename.'";');

    $f = fopen('php://output', 'w');
    $columns = array('First Name','Number of Devices','Gigabytes Used','Time Connected (hrs)');

    fputcsv($f, $columns, $_POST['download_delimiter']);
    foreach ($export_array as $line) {
        fputcsv($f, $line, $_POST['download_delimiter']);
    }
}
exit();
