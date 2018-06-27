<?php
include_once("mysql.php");
sql();
switch(@$_POST['type'])
{
    case 'get_area':
        get_area();
        break;
    case'sclass':
        sclass();
        break;
    case 'push_action':
        push_action();
        break;

    case 'admin_update_order':
        admin_update_order();
        break;
}

function get_area()
{
    $city_id = $_POST['city_id'];
    $sql = "SELECT * FROM area WHERE city_id = '$city_id'";
    $res = mysql_query($sql);
    while($row = mysql_fetch_array($res))
    {
        echo "<option value=".$row['id'].">".$row['area']."</option>";
    }
}

function sclass()
{
    $arr = array();
    $sql = "SELECT * FROM class ORDER BY sort";
    $res = mysql_query($sql);
    while($row = mysql_fetch_object($res))
    {
        $arr[] = $row;
    }
    echo json_encode($arr);
}

function push_action()
{
    @$reg_id_val = $_POST['reg_id_val'];
    @$push_msg = $_POST['push_msg'];

    $REDID = $reg_id_val;
    $data = array( "messages" => $push_msg);
    $res = sendGoogleCloudMessage($data, $REDID);
    $r = json_decode($res,true);
    echo $r['success'];
}

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

    curl_close( $ch );  //關閉連接

    //------------------------------
    // Debug GCM response
    //------------------------------

    return $result;
}

function admin_update_order()
{
    $order_no = $_POST['order_no'];
    $order_m_id = $_POST['order_m_id'];

    $is_effective = $_POST['is_effective'];

    $addressee_name = $_POST['addressee_name'];
    $addressee_cellphone = $_POST['addressee_cellphone'];
    $addressee_address = $_POST['addressee_address'];
    $addressee_date = $_POST['addressee_date'];

    $update_addressee_set = "UPDATE addressee_set SET `name`='$addressee_name', cellphone='$addressee_cellphone', addressee_date='$addressee_date', address='$addressee_address' WHERE order_no='$order_no' AND m_id='$order_m_id'";
    mysql_query($update_addressee_set);
    $update_consumer_order = "UPDATE consumer_order SET is_effective='$is_effective' WHERE order_no='$order_no' AND m_id='$order_m_id'";
    mysql_query($update_consumer_order);
    echo 1;
}