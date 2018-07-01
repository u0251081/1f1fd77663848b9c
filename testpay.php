<?php
session_start();
header("Content-Type:text/html; charset=utf-8");
include_once('admin/mysql.php');
sql();
include_once('AllPay.Payment.Integration.php');

//這邊寫入consumer_order-----//
if(isset($_SESSION["member_no"])) {
    $member_id = $_SESSION["member_no"];
} else {
    $member_id = $_SESSION["fb_id"];
}

//----------從購物車來的資料-------//
$cart_pid = $_POST['cart_pid']; //格式：1,2,3,
$cart_qty = $_POST['cart_qty']; //格式：1,2,3,
$cart_price = $_POST['cart_price']; //格式：1,2,3,
$p_name_ary = substr($_POST['p_name_ary'],0); //從購物車購買
//-------------------------------//

//----------單筆資料--------------//
@$p_id = $_POST['p_id']; //商品id(編號)
@$pay_qty = $_POST['pay_qty']; //購買數量
@$web_price = $_POST['web_price']; //商品單價
@$p_name = $_POST['p_name']; //直接購買單一商品
//------------------------------//

//-----------共用資料-----------//
@$member_no = $member_id; //訂購人(購買商品的會員ID)
$pay_time = date("Y/m/d H:i:s"); //商店交易時間
@$TotalAmount = $_POST['TotalAmount']; //商品總價
$order_id = time(); //訂單編號產生
@$send_addressee_name = $_POST['addressee_name']; //收件人姓名
@$send_address = $_POST['address']; //收件人地址
@$send_cellphone = $_POST['cellphone']; //收件人電話
@$addressee_date = $_POST['addressee_date']; //收件日期 1=>平日一到五 2=>週六 3=>不指定
@$paymentchose = $_POST['paymentchose']; //收件日期 1=>CREDIT 2=>CVS

//---------------------------//

//----------判斷有無分享資料-----//
@$share_manager_no = $_SESSION['share_manager_no']; //導購處理->行銷經理
$share_vip_id = isset($_SESSION['share_vip_id'])? $_SESSION['share_vip_id']:null; //導購處裡->vip分享
if(!empty($_POST['fb_no'])) {
    $fb_no = $_POST['fb_no'];
} else {
    $fb_no = 0;
}
if(!empty($_POST['manager_id']))
{
    $manager_id = $_POST['manager_id']; //代表收到行銷經理分享
} else {
    $manager_id = $share_manager_no; //代表收到行銷經理分享
}
if (!empty($_POST['vip_id'])) {
    $vip_id = $_POST['vip_id']; //代表收到VIP會員再次分享
} else {
    $vip_id = $share_vip_id; //代表收到VIP會員再次分享
}

//---------------------//
$order_data = array(
    'order_id' => $order_id,
    'manager_id' => $manager_id,
    'member_no' => $member_no,
    'pay_time' => $pay_time,
    'vip_id' => $vip_id,
    'fb_no' => $fb_no,
    'TotalAmount' => $TotalAmount,
    'paymentchose' => $paymentchose,
    'cart_info' => array(
        'cart_pid' => $cart_pid,
        'p_name_ary' => $p_name_ary,
        'cart_price' => $cart_price,
        'cart_qty' => $cart_qty
    ),
    'receiver_info' => array(
        'send_addressee_name' => $send_addressee_name,
        'send_address' => $send_address,
        'send_cellphone' => $send_cellphone,
        'addressee_data' => $addressee_date
    )
);
// step1 insert order into consumer_order
function insert_into_consumer_order($order_data) {
    $order_id = $order_data['order_id'];
    $member_no = $order_data['member_no'];
    $fb_no = $order_data['fb_no'];
    $paymentchose = $order_data['paymentchose'];
    $TotalAmount = $order_data['TotalAmount'];
    $sql = "";
    $sql .= "INSERT INTO consumer_order SET";
    $sql .= " order_no='$order_id', m_id='$member_no',fb_no='$fb_no',";
    $sql .= " pay_type='$paymentchose', pay_time='".date('Y-m-d H:i:s')."',";
    $sql .= " o_price='$TotalAmount', order_time='".date('Y-m-d H:i:s')."', is_effective='0'";
    mysql_query($sql);
    $insert_id = mysql_insert_id();
    return $insert_id;
}
// step2 insert order detail into consumer_order2
function insert_into_consumer_order2($order_data) {
    $insert_id = $order_data['insert_id'];
    $cart_pid = $order_data['cart_info']['cart_pid'];
    $p_name_ary = $order_data['cart_info']['p_name_ary'];
    $cart_price = $order_data['cart_info']['cart_price'];
    $cart_qty = $order_data['cart_info']['cart_qty'];
    $cart_pid2 = explode(',',$cart_pid); //商品id的陣列
    $p_name_ary2 = explode(',',$p_name_ary); //商品名稱的陣列
    $cart_price2 = explode(',',$cart_price); //商品金額的陣列
    $cart_qty2 = explode(',',$cart_qty); //商品數量的陣列
    if (is_array($cart_pid2)) {
        foreach ($cart_pid2 as $k => $v ) {
            $sql2 = "";
            $sql2 .= "INSERT INTO consumer_order2 SET";
            $sql2 .= "   order1_id = '$insert_id', p_id = '$cart_pid2[$k]', p_name = '$p_name_ary2[$k]',";
            $sql2 .= " p_web_price = '$cart_price2[$k]',  qty = '$cart_qty2[$k]'";
            mysql_query($sql2);
        }
        $rst = (mysql_affected_rows() > 0)? true:false;
        return $rst;
    }
    return false;
}
// step3 insert receiver information into addressee_set
function insert_into_addressee_set($order_data) {
    $order_id = $order_data['order_id'];
    $member_no = $order_data['member_no'];
    $send_addressee_name = $order_data['receiver_info']['send_addressee_name'];
    $send_address = $order_data['receiver_info']['send_address'];
    $send_cellphone = $order_data['receiver_info']['send_cellphone'];
    $addressee_date = $order_data['receiver_info']['addressee_data'];
    $sql = "";
    $sql .= "INSERT INTO addressee_set SET";
    $sql .= " order_no = '$order_id', `name` = '$send_addressee_name', address = '$send_address',";
    $sql .= " cellphone = '$send_cellphone', addressee_date = '$addressee_date', m_id = '$member_no'";
    mysql_query($sql);
}
// step4 does it been share with manager or vip
function process_with_share( $order_data, $status = FALSE) {
    $insert_id = $order_data['insert_id'];
    $manager_id = $order_data['manager_id'];
    $member_no = $order_data['member_no'];
    $vip_id = $order_data['vip_id'];
    $cart_pid = $order_data['cart_info']['cart_pid'];
    $sql3 = "SELECT * FROM share WHERE manager_id='$manager_id' AND p_id='$cart_pid'";
    $sql4 = "INSERT INTO share SET manager_id='$manager_id', p_id='$cart_pid', is_effective='0', order2_id='$insert_id'";
    switch ($status) {
        case 1:
            $sql3 .= " and vip_id='$vip_id'";
            $sql4  = ", vip_id = '$vip_id'";
        case 2:
            $sql3 .= " and member_id='$member_no'";
            $sql4 .= ", member_id = '$member_no'";
            break;
        case 3:
            $sql3 .= " and member_id='$manager_id'";
            $sql4 .= ", member_id = '$manager_id'";
            break;
        default:
            return null;
            break;
    }
    $res3 = mysql_query($sql3);
    $row3 = mysql_fetch_array($res3);
    if ($row3['id'] != "" && $row3['is_effective'] == 0) {
        $sql5 = "UPDATE share SET order2_id='$insert_id' WHERE id='".$row3['id']."'";
        mysql_query($sql5);
        $share_id = $row3['id'];
    } else {
        mysql_query($sql4);
        $share_id = mysql_insert_id();
    }
    return $share_id;
}
// step5 create order
function process_create_order( $order_data, $status = FALSE) {
    $cart_pid = $order_data['cart_info']['cart_pid'];
    $p_name_ary = $order_data['cart_info']['p_name_ary'];
    $cart_price = $order_data['cart_info']['cart_price'];
    $cart_qty = $order_data['cart_info']['cart_qty'];
    $order_id = $order_data['order_id'];
    $TotalAmount = $order_data['TotalAmount'];
    $pay_time = $order_data['pay_time'];
    $paymentchose = $order_data['paymentchose'];
    $member_no = $order_data['member_no'];
    $manager_id = $order_data['manager_id'];
    $share_id = $order_data['share_id'];
    switch ($status) {
        case 1:
        case 2:
            create_order2($cart_pid,$p_name_ary,$pay_time,$TotalAmount,$cart_price,$cart_qty,$order_id,$member_no,$share_id,$paymentchose);
        case 3:
            create_order2($cart_pid,$p_name_ary,$pay_time,$TotalAmount,$cart_price,$cart_qty,$order_id,$manager_id,$share_id,$paymentchose);
        default:
    }
}

if ( (!empty($p_id) && !empty($pay_qty)) || (!empty($cart_pid) && !empty($cart_qty)) ) {
    // print_r($order_data);
    if (!empty($p_id) && !empty($pay_qty)) {
        // 如果商品為單品，將其放入推車資訊中
        $order_data['cart_info'] = array(
            'cart_pid' => $p_id,
            'p_name_ary' => $p_name,
            'cart_price' => $web_price,
            'cart_qty' => $pay_qty
        );

    } else {
    }
    // print_r($_POST);
    // print_r($order_data);
    // die();
    // 新增 consumer_order 的資料，並取得 id 放入 insert_id 中
    $order_data['insert_id'] = insert_into_consumer_order($order_data);
    // 新增 consumer_order2 的資料，並確認是否成功，成功才往下
    $step2 = insert_into_consumer_order2($order_data);
    if ($step2) {
        // 如果順利輸入交易資料，開始輸入收件人資料
        insert_into_addressee_set($order_data);
        // 確認交易是否為分享交易，如果 manager_id 有被設定，即是
        $step4 = !empty($manager_id);
        $status = null;
        if ($step4) {
            // 如果又有 vip_id 表示會員再次分享
            if (!empty($vip_id)) {
                $status = 1;
            } else {
                // 如果分享人不是自己
                if ($manager_id != $_SESSION['manager_no']) {
                    $status = 2;
                } else {
                    // 如果分享人是自己
                    $status = 3;
                }
            }
        }
        $order_data['share_id'] = process_with_share( $order_data, $status);
        process_create_order($order_data, $status);
    }
}


//從商品詳細頁面直接購買產生歐付寶訂單function
function create_order($p_name,$pay_time,$TotalAmount,$web_price,$order_id,$share_id,$p_id,$member_no,$pay_qty,$paymentchose)
{
    /*產生訂單範例*/
    try
    {
        $oPayment = new AllInOne();
        /* 服務參數 */
        $oPayment->ServiceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5"; //測試環境網址，正式版的話要改為正式環境
        $oPayment->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $oPayment->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $oPayment->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它

        $BackUrl="http://".$_SERVER['HTTP_HOST']."/17mai/payment_info.php";
        $BackUrl2="http://".$_SERVER['HTTP_HOST']."/index.php?url=order_search"; //超商繳費用

        if(@$share_id != "")
        {
            $BackUrl3="http://".$_SERVER['HTTP_HOST']."/payment_info.php?id=".$order_id."&p_id=".$p_id."&member_no=".$member_no."&pay_qty=".$pay_qty."&share_id=".$share_id; //超商繳費用
        }
        else
        {
            $BackUrl3="http://".$_SERVER['HTTP_HOST']."/payment_info.php?id=".$order_id."&p_id=".$p_id."&member_no=".$member_no."&pay_qty=".$pay_qty; //超商繳費用
        }

        /* 基本參數 */
        $oPayment->Send['MerchantTradeNo'] = $order_id;//這邊是店家端所產生的訂單編號
        $oPayment->Send['MerchantTradeDate'] = $pay_time; //商店交易時間
        $oPayment->Send['TotalAmount'] = (int) $TotalAmount;//付款總金額，超商最低30元起
        $oPayment->Send['TradeDesc'] = "一起購商城購物";//交易敘述
        if($paymentchose=="CVS")
        {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] =$BackUrl3;
        }
        else
        {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::Credit;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] = "http://www.17mai.com.tw/index.php?url=order_search";
        }

        $oPayment->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::No; //若不回傳額外的付款資訊時，參數值請傳：Ｎ
        $oPayment->Send['Remark'] = "無備註"; //廠商後台訂單備註

        $oPayment->SendExtend['PaymentInfoURL'] = $BackUrl3;
        $oPayment->Send['ReturnURL'] = $BackUrl3; //當消費者付款完成後，歐付寶會將付款結果參數回傳到該網址。
        $oPayment->Send['ClientBackURL'] = $BackUrl2; //消費者點選此按鈕後，會將頁面導回到此設定的網址
        //為付款完成後，歐付寶將頁面導回到會員網址，並將付款結果帶回
        //請填入你主機要接受訂單付款後狀態 回傳的程式名稱 記住 該網址需能對外
        //接受訂單狀態 回傳程式名稱 可在此程式內將付款方式寫入你的訂單中 payment_info.php 與 return.php 程式內容一樣

        $oPayment->Send['IgnorePayment'] = "Alipay";//把不的付款方式取消掉
        //$oPayment->Send['DeviceSource'] ="M";//參數M表示使用行動版的頁面 不設定此參數 預設就是電腦版顯示

        // 加入選購商品資料。
        array_push($oPayment->Send['Items'], array('Name' => $p_name, 'Price' => (int)$web_price,
            'Currency' => "元", 'Quantity' => (int)$pay_qty, 'URL' => "無"));

        /* 產生訂單 */
        $oPayment->CheckOut();
        /* 產生產生訂單 Html Code 的方法 */
        $szHtml = $oPayment->CheckOutString();

    }
    catch (Exception $e)
    {
        // 例外錯誤處理。
        throw $e;
    }
}

//從購物車購買產生歐付寶訂單function
function create_order2($cart_pid,$p_name_ary,$pay_time,$TotalAmount,$cart_price,$cart_qty,$order_id,$member_no,$share_id,$paymentchose)
{
    $cart_pid2 = explode(',',$cart_pid); //商品id的陣列
    $p_name_ary2 = explode(',',$p_name_ary); //商品名稱的陣列
    $cart_price2 = explode(',',$cart_price); //商品金額的陣列
    $cart_qty2 = explode(',',$cart_qty); //商品數量的陣列

    /*產生訂單範例*/
    try
    {
        $oPayment = new AllInOne();
        /* 服務參數 */
        $oPayment->ServiceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5"; //測試環境網址，正式版的話要改為正式環境
        $oPayment->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $oPayment->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $oPayment->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它

        $dir = "17mai";
        $BaseUrl = "http://www.17mai.com.tw/";
        $BaseUrl = "http://".$_SERVER['HTTP_HOST'].'/'.$dir.'/';
        $BackUrl1 = $BaseUrl."payment_info.php";
        $BackUrl2 = $BaseUrl."index.php?url=order_search"; //超商繳費用

        $BackUrl3 = "";
        $BackUrl3 .= $BackUrl1."?";
        $BackUrl3 .= "id=$order_id&p_id=$cart_pid&member_no=$member_no&pay_qty=$cart_qty"; //超商繳費用

        if(isset($share_id) && $share_id != "") {
            $BackUrl3 .= "&share_id=$share_id";
            //超商繳費用
        } else {
        }

        /* 基本參數 */
        $oPayment->Send['MerchantTradeNo'] = $order_id;//這邊是店家端所產生的訂單編號
        $oPayment->Send['MerchantTradeDate'] = $pay_time; //商店交易時間
        $oPayment->Send['TotalAmount'] = (int) $TotalAmount;//付款總金額，超商最低30元起
        $oPayment->Send['TradeDesc'] = "一起購商城購物";//交易敘述
        if($paymentchose=="CVS")
        {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] =$BackUrl3;
        }
        else
        {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::Credit;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] = $BackUrl2;
        }

        $oPayment->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::No; //若不回傳額外的付款資訊時，參數值請傳：Ｎ
        $oPayment->Send['Remark'] = "無備註"; //廠商後台訂單備註

        $oPayment->SendExtend['PaymentInfoURL'] = $BackUrl3;
        $oPayment->Send['ReturnURL'] = $BackUrl3; //當消費者付款完成後，歐付寶會將付款結果參數回傳到該網址。
        $oPayment->Send['ClientBackURL'] = $BackUrl2; //消費者點選此按鈕後，會將頁面導回到此設定的網址
        //為付款完成後，歐付寶將頁面導回到會員網址，並將付款結果帶回
        //請填入你主機要接受訂單付款後狀態 回傳的程式名稱 記住 該網址需能對外
        //接受訂單狀態 回傳程式名稱 可在此程式內將付款方式寫入你的訂單中 payment_info.php 與 return.php 程式內容一樣

        $oPayment->Send['IgnorePayment'] = "Alipay";//把不的付款方式取消掉
        //$oPayment->Send['DeviceSource'] ="M";//參數M表示使用行動版的頁面 不設定此參數 預設就是電腦版顯示

        // 加入選購商品資料。
        for($i=0; $i<count($cart_pid2); $i++)
        {
            array_push(
                $oPayment->Send['Items'],
                array(
                    'Name' => $p_name_ary2[$i],
                    'Price' => (int)$cart_price2[$i],
                    'Currency' => "元",
                    'Quantity' => (int)$cart_qty2[$i],
                    'URL' => "無"
                )
            );
        }

        /* 產生訂單 */
        $oPayment->CheckOut();
        /* 產生產生訂單 Html Code 的方法 */
        $szHtml = $oPayment->CheckOutString();

    }
    catch (Exception $e)
    {
        // 例外錯誤處理。
        throw $e;
    }
}