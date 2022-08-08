<?php
       $request = array();
       $request['username'] = 'info@ttbreservdelar.com';
       $request['password'] = 'OTNOK!!!12354konto';
       $request['carplate_no'] = 'UHG443'; // CARPLATE NO
       $request['web'] = 'swe';
       echo $request['ip'] = '122.160.138.11';
     

       $ch = curl_init('http://94.46.45.189/~livedatano/fcdetails.php');
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
       $response = curl_exec($ch);
       curl_close($ch);
       echo "<pre>";
       print_r($response);
?>

