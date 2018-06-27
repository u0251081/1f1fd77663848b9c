<?php
//------------------------------
// Payload data you want to send 
// to Android device (will be
// accessible via intent extras)
//------------------------------

$REDID2 = "APA91bGAnvIfI5hBv3R4XfsfVmVygG0cbK9OPjhyb3nWRpwAjjJSA1b0YhxghOUQhYO-4G8aHxnctRcGbrL_IwNmZD5unOyS24Du-hH_bv3tJOnO1iJMYB3V23XWcCRUWv0dUdXTaeyw";
$REDID = array($REDID2);


$data = array( "messages" => "安安");
sendGoogleCloudMessage($data, $REDID);


//------------------------------
// Define custom GCM function
//------------------------------

function sendGoogleCloudMessage($data, $REDID)
{
    $apiKey = 'AIzaSyAO_U3znBn5JnIFXpjzNBs0WIN2MLJKPDY'; //api key
    $url = 'https://android.googleapis.com/gcm/send';  // $url為傳送之目的地之GCM Server
    $post = array(
                    'registration_ids'  => $REDID,  //user的gcmID
                    'data'              => $data, //發送內容
                  );
					
    $headers = array( 
                        'Authorization:key='.$apiKey, //api key
                        'Content-Type:application/json' //發送格式為json
                    );

    //------------------------------
    // Initialize curl handle
    //------------------------------

    $ch = curl_init();

    //------------------------------
    // Set URL to GCM endpoint
    //------------------------------

    curl_setopt( $ch, CURLOPT_URL, $url );

    //------------------------------
    // Set request method to POST
    //------------------------------

    curl_setopt( $ch, CURLOPT_POST, true );

    //------------------------------
    // Set our custom headers
    //------------------------------

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

    //------------------------------
    // Get the response back as 
    // string instead of printing it
    //------------------------------

    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    //------------------------------
    // Set post data as JSON
    //------------------------------

    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

    //------------------------------
    // Actually send the push!
    //------------------------------

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec( $ch ); // 送出 post, 並接收回應, 存入 $result

    //------------------------------
    // Error? Display it!
    //------------------------------

    if ( curl_errno( $ch ) )
    {
        echo 'GCM error: ' . curl_error( $ch );
    }

    //------------------------------
    // Close curl handle
    //------------------------------

    curl_close( $ch );	//關閉連接

    //------------------------------
    // Debug GCM response
    //------------------------------

    echo $result;
}

?>