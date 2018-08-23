<?php
session_start();
header("Content-Type:text/html; charset=utf-8");
include_once('admin/mysql.php');
sql();
$dbh = pdo_connect();
include_once('AllPay.Payment.Integration.php');
//這邊寫入consumer_order-----//
if (isset($_SESSION["member_no"])) {
    $member_id = $_SESSION["member_no"];
} else {
    $member_id = $_SESSION["fb_id"];
}

function get_member_id()
{
    $member_id = '';
    if (isset($_SESSION['fb_id'])) $member_id = $_SESSION['fb_id']; // 沒有 member_no 才用 fb_id
    if (isset($_SESSION['member_no'])) $member_id = $_SESSION['member_no']; // 如果有 member_no 用 member_no
    return $member_id;
}

function get_product_info($product = false)
{
    $product_list = array();
    if (is_array($product)) {
        foreach ($product as $k => $v) {
            /*
             * 確認商品狀態, 如狀態為 false 則忽略該商品
             */
            $product['status'] = checkProduct($v);
            $product_list[] = $product;
        }
    }
    return $product_list;
}

function get_amount($products)
{
    if (is_array($products)) {
        $total = 0;
        foreach ($products as $v) {
            if (isset($v['price']) && isset($v['qty']) && isset($v['status']) && $v['status'] === true) {
                $total += (int)$v['price'] * $v['qty'];
            } else {
                return false;
            }
        }
        return $total;
    } else {
        return false;
    }
}

function checkProduct($product)
{
    /*
     * 驗證 product 的 id, name, price 是否與資料庫一致, 避免竄改
     * 如資料不一致, 設置回傳狀態 false
     */
    global $dbh;
    try {
        $pid = isset($product['pid']) ? $product['pid'] : 'error';
        $p_name = isset($product['p_name']) ? $product['p_name'] : 'error';
        $price = isset($product['price']) ? $product['price'] : 'error';
        $quantity = isset($product['qty']) ? $product['qty'] : 'error';
        $sql = '';
        $sql .= "select count(*) from product as a";
        $sql .= " left join price as b on a.id = b.p_id";
        $sql .= " where p_name is not null and price is not null";
        $sql .= " and a.id = :pid and p_name = :p_name and web_price = :web_price and p_qty >= :quantity ;";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':pid', $pid, PDO::PARAM_STR);
        $sth->bindParam(':p_name', $p_name, PDO::PARAM_STR);
        $sth->bindParam(':web_price', $price, PDO::PARAM_STR);
        $sth->bindParam(':quantity', $quantity, PDO::PARAM_STR);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $rst = $sth->fetchAll();
        $returnValue = ($rst[0]['count(*)'] === '1') ? true : false;
        return $returnValue;
    } catch (PDOException $exception) {
        print $exception->getMessage();
        return false;
    }
}

function product_detail($pid)
{
    global $dbh;
    try {
        $sql = '';
        $sql .= "select a.id as pid, p_name, web_price from product as a";
        $sql .= " left join price as b on a.id = b.p_id";
        $sql .= " where p_name is not null and web_price is not null";
        $sql .= " and a.id = :pid ;";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':pid', $pid, PDO::PARAM_STR);
        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $rst = $sth->fetchAll();
        if (count($rst) === 1) {
            return $rst;
        } else {
            return false;
        }
    } catch (PDOException $exception) {
        $exception->getMessage();
    }
}

function prepare_receiver()
{
    $r_name = isset($_POST['addressee_name']) ? $_POST['addressee_name'] : ''; //收件人姓名
    $r_addr = isset($_POST['address']) ? $_POST['address'] : ''; //收件人地址
    $r_call = isset($_POST['cellphone']) ? $_POST['cellphone'] : ''; //收件人電話
    $r_date = isset($_POST['addressee_date']) ? $_POST['addressee_date'] : ''; //收件日期 1=>平日一到五 2=>週六 3=>不指定
    $receiver = array(
        'r_name' => $r_name,
        'r_addr' => $r_addr,
        'r_call' => $r_call,
        'r_date' => $r_date
    );
    foreach ($receiver as $v) {
        if (empty($v)) return false;
    }
    return $receiver;
}

function prepare_sharer()
{
    $sharer = array();
    //----------判斷有無分享資料-----//
    $share_manager_no = isset($_SESSION['share_manager_no']) ? $_SESSION['share_manager_no'] : ''; //導購處理->行銷經理
    $share_vip_id = isset($_SESSION['share_vip_id']) ? $_SESSION['share_vip_id'] : ''; //導購處裡->vip分享
    if (!isset($_POST['fb_no']) || $_POST['fb_no'] === "") {
        $fb_no = 0;
    } else {
        $fb_no = $_POST['fb_no'];
    }
    if (isset($_POST['manager_id']) && $_POST['manager_id'] !== "") {
        $manager_id = $_POST['manager_id']; //代表收到行銷經理分享
    } else {
        $manager_id = $share_manager_no; //代表收到行銷經理分享
    }

    if (isset($_POST['vip_id']) && $_POST['vip_id'] !== "") {
        $vip_id = $_POST['vip_id']; //代表收到VIP會員再次分享
    } else {
        $vip_id = $share_vip_id; //代表收到VIP會員再次分享
    }
    $sharer['fb_no'] = $fb_no;
    $sharer['manager_id'] = $manager_id;
    $sharer['vip_id'] = $vip_id;
    return $sharer;
}

function process_with_received_data($received)
{
    // 準備 member_id
    $member_id = get_member_id();
    // 購物資料 統一格式 商品 => 詳細資料陣列
    $product = $received['product'];
    // 檢查商品狀態(是否與資料庫內容一致)
    $product = get_product_info($product);
    $totalAmount = get_amount($product);
    $receiver = prepare_receiver();
    $sharer = prepare_sharer();
    $payChoise = isset($_POST['paymentchose']) ? $_POST['paymentchose'] : ''; // 付款方式 1=>CREDIT 2=>CVS
    $page_data = array(
        'member_id' => $member_id, //訂購人(購買商品的會員ID)
        'sharer' => $sharer,
        'products' => $product, //商品資料,一個值為一個商品 (包含 pid, p_name, price 為一個單位)
        'create_pay_time' => date("Y/m/d H:i:s"), //商店交易時間
        'order_time' => date('Y-m-d H:i:s'), //商店交易時間
        'TotalAmount' => $totalAmount, //商品總價,經驗正處理
        'order_id' => time(), //訂單編號產生
        'receiver' => $receiver, // 收貨人相關資訊
        'paymentchose' => $payChoise,
        'insert_id' => '' // 這是資料庫之間的關聯 id 由主表 consumer_order 決定
    );
    return $page_data;
}

// step1 insert order into consumer_order
function insert_into_consumer_order($order_data)
{
    global $dbh;
    try {
        $order_id = $order_data['order_id'];
        $member_no = $order_data['member_id'];
        $fb_no = $order_data['sharer']['fb_no'];
        $paymentchose = $order_data['paymentchose'];
        $TotalAmount = $order_data['TotalAmount'];
        $orderTime = $order_data['order_time'];
        $sql = "";
        $sql .= "INSERT INTO consumer_order SET";
        $sql .= " order_no = :order_id, m_id = :member_no, fb_no = :fb_no,";
        $sql .= " pay_type = :paymentchose, pay_time = :payTime,";
        $sql .= " o_price = :TotalAmount, order_time = :order_time, is_effective='0'";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $sth->bindParam(':member_no', $member_no, PDO::PARAM_STR);
        $sth->bindParam(':fb_no', $fb_no, PDO::PARAM_STR);
        $sth->bindParam(':paymentchose', $paymentchose, PDO::PARAM_STR);
        $sth->bindParam(':payTime', $orderTime, PDO::PARAM_STR);
        $sth->bindParam(':TotalAmount', $TotalAmount, PDO::PARAM_STR);
        $sth->bindParam(':order_time', $oderTime, PDO::PARAM_STR);
        $sth->execute();
        $insert_id = $dbh->lastInsertId();
        return $insert_id;
    } catch (PDOException $exception) {
        print $exception->getMessage();
        exit();
    }
}

// step2 insert order detail into consumer_order2
function insert_into_consumer_order2($order_id, $products)
{
    if (is_array($products)) {
        global $dbh;
        $sql = '';
        $sql .= "insert into consumer_order2 set";
        $sql .= " order1_id = :order_id, p_id = :product_id, p_name = :product_name, p_web_price = :price, qty = :quantity;";
        foreach ($products as $k => $v) {
            try {
                if ($v['status'] === true) {
                    $product_id = $v['pid'];
                    $product_name = $v['p_name'];
                    $price = $v['price'];
                    $quantity = $v['qty'];
                    $sth = $dbh->prepare($sql);
                    $sth->bindParam(':order_id', $order_id, PDO::PARAM_STR);
                    $sth->bindParam(':product_id', $product_id, PDO::PARAM_STR);
                    $sth->bindParam(':product_name', $product_name, PDO::PARAM_STR);
                    $sth->bindParam(':price', $price, PDO::PARAM_STR);
                    $sth->bindParam(':quantity', $quantity, PDO::PARAM_STR);
                    $sth->execute();
                }
            } catch (PDOException $exception) {
                $exception->getMessage();
                exit();
            }
        }
        return true;
    } else {
        return false;
    }
}

// step3 insert receiver information into addressee_set
function insert_into_addressee_set($order_id, $member_id, $receiver)
{
    global $dbh;
    try {
        $sql = '';
        $sql .= "insert into addressee_set set";
        $sql .= " order_no = :order_id, m_id = :member_id, name = :name, address = :address,";
        $sql .= " cellphone = :call, addressee_date = :addr_date";
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $sth->bindParam(':member_id', $member_id, PDO::PARAM_STR);
        $sth->bindParam(':name', $receiver['r_name'], PDO::PARAM_STR);
        $sth->bindParam(':address', $receiver['r_addr'], PDO::PARAM_STR);
        $sth->bindParam(':call', $receiver['r_call'], PDO::PARAM_STR);
        $sth->bindParam(':addr_date', $receiver['r_date'], PDO::PARAM_STR);
        $sth->execute();
        return true;
    } catch (PDOException $exception) {
        $exception->getMessage();
        exit();
    }
    $sql = "";
    $sql .= "INSERT INTO addressee_set SET";
    $sql .= " order_no = '$order_id', `name` = '$send_addressee_name', address = '$send_address',";
    $sql .= " cellphone = '$send_cellphone', addressee_date = '$addressee_date', m_id = '$member_no'";
    mysql_query($sql);
}

// step4 does it been share with manager or vip
function process_with_share($order_id, $product, $sharer)
{
    $sql = "select count(*) from share where manager_id = :manager_id and p_id = :pid;";
    $para = ['manager_id' => $sharer['manager_id'], 'pid' => $product_id ];
    $result = pdo_select_sql($sql,$para);
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
            $sql4 = ", vip_id = '$vip_id'";
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
        $sql5 = "UPDATE share SET order2_id='$insert_id' WHERE id='" . $row3['id'] . "'";
        mysql_query($sql5);
        $share_id = $row3['id'];
    } else {
        mysql_query($sql4);
        $share_id = mysql_insert_id();
    }
    return $share_id;
}


$order_data = process_with_received_data();
$order_date['insert_id'] = insert_into_consumer_order($order_data);
insert_into_consumer_order2($order_data['insert_id'], $order_date['products']);
insert_into_addressee_set($order_data['insert_id'], $order_data['member_id'], $order_date['receiver']);

die();
/*
 * //----------從購物車來的資料-------//
 * $cart_pid = $_POST['cart_pid']; //格式：1,2,3,
 * $cart_qty = $_POST['cart_qty']; //格式：1,2,3,
 * $cart_price = $_POST['cart_price']; //格式：1,2,3,
 * $p_name_ary = substr($_POST['p_name_ary'], 0); //從購物車購買
 * //-------------------------------//
 *
 * //----------單筆資料--------------//
 * @$p_id = $_POST['p_id']; //商品id(編號)
 * @$pay_qty = $_POST['pay_qty']; //購買數量
 * @$web_price = $_POST['web_price']; //商品單價
 * @$p_name = $_POST['p_name']; //直接購買單一商品
 * //------------------------------//
 *
 * //-----------共用資料-----------//
 * @$member_no = $member_id; //訂購人(購買商品的會員ID)
 * $pay_time = date("Y/m/d H:i:s"); //商店交易時間
 * @$TotalAmount = $_POST['TotalAmount']; //商品總價
 * $order_id = time(); //訂單編號產生
 * @$send_addressee_name = $_POST['addressee_name']; //收件人姓名
 * @$send_address = $_POST['address']; //收件人地址
 * @$send_cellphone = $_POST['cellphone']; //收件人電話
 * @$addressee_date = $_POST['addressee_date']; //收件日期 1=>平日一到五 2=>週六 3=>不指定
 * @$paymentchose = $_POST['paymentchose']; //收件日期 1=>CREDIT 2=>CVS
 * //---------------------------//
 *
 * //----------判斷有無分享資料-----//
 * @$share_manager_no = $_SESSION['share_manager_no']; //導購處理->行銷經理
 * $share_vip_id = isset($_SESSION['share_vip_id']) ? $_SESSION['share_vip_id'] : null; //導購處裡->vip分享
 * if (!empty($_POST['fb_no'])) {
 *     $fb_no = $_POST['fb_no'];
 * } else {
 *     $fb_no = 0;
 * }
 * if (!empty($_POST['manager_id'])) {
 *     $manager_id = $_POST['manager_id']; //代表收到行銷經理分享
 * } else {
 *     $manager_id = $share_manager_no; //代表收到行銷經理分享
 * }
 * if (!empty($_POST['vip_id'])) {
 *     $vip_id = $_POST['vip_id']; //代表收到VIP會員再次分享
 * } else {
 *     $vip_id = $share_vip_id; //代表收到VIP會員再次分享
 * }
 *
 * //---------------------//
 * $order_data = array(
 *     'order_id' => $order_id,
 *     'manager_id' => $manager_id,
 *     'member_no' => $member_no,
 *     'pay_time' => $pay_time,
 *     'vip_id' => $vip_id,
 *     'fb_no' => $fb_no,
 *     'TotalAmount' => $TotalAmount,
 *     'paymentchose' => $paymentchose,
 *     'cart_info' => array(
 *         'cart_pid' => $cart_pid,
 *         'p_name_ary' => $p_name_ary,
 *         'cart_price' => $cart_price,
 *         'cart_qty' => $cart_qty
 *     ),
 *     'receiver_info' => array(
 *         'send_addressee_name' => $send_addressee_name,
 *         'send_address' => $send_address,
 *         'send_cellphone' => $send_cellphone,
 *         'addressee_data' => $addressee_date
 *     )
 * );
 * // step1 insert order into consumer_order
 * function insert_into_consumer_order($order_data)
 * {
 *     $order_id = $order_data['order_id'];
 *     $member_no = $order_data['member_no'];
 *     $fb_no = $order_data['fb_no'];
 *     $paymentchose = $order_data['paymentchose'];
 *     $TotalAmount = $order_data['TotalAmount'];
 *     $sql = "";
 *     $sql .= "INSERT INTO consumer_order SET";
 *     $sql .= " order_no='$order_id', m_id='$member_no',fb_no='$fb_no',";
 *     $sql .= " pay_type='$paymentchose', pay_time='" . date('Y-m-d H:i:s') . "',";
 *     $sql .= " o_price='$TotalAmount', order_time='" . date('Y-m-d H:i:s') . "', is_effective='0'";
 *     mysql_query($sql);
 *     $insert_id = mysql_insert_id();
 *     return $insert_id;
 * }
 *
 * // step2 insert order detail into consumer_order2
 * function insert_into_consumer_order2($order_data)
 * {
 *     $p_name_ary = $order_data['cart_info']['p_name_ary'];
 *     $cart_price = $order_data['cart_info']['cart_price'];
 *     $cart_qty = $order_data['cart_info']['cart_qty'];
 *     $cart_pid2 = explode(',', $cart_pid); //商品id的陣列
 *     $p_name_ary2 = explode(',', $p_name_ary); //商品名稱的陣列
 *     $cart_price2 = explode(',', $cart_price); //商品金額的陣列
 *     $cart_qty2 = explode(',', $cart_qty); //商品數量的陣列
 *     if (is_array($cart_pid2)) {
 *         foreach ($cart_pid2 as $k => $v) {
 *             $sql2 = "";
 *             $sql2 .= "INSERT INTO consumer_order2 SET";
 *             $sql2 .= "   order1_id = '$insert_id', p_id = '$cart_pid2[$k]', p_name = '$p_name_ary2[$k]',";
 *             $sql2 .= " p_web_price = '$cart_price2[$k]',  qty = '$cart_qty2[$k]'";
 *             mysql_query($sql2);
 *         }
 *         $rst = (mysql_affected_rows() > 0) ? true : false;
 *         return $rst;
 *     }
 *     return false;
 * }
 *
 * // step3 insert receiver information into addressee_set
 * function insert_into_addressee_set($order_data)
 * {
 *     $order_id = $order_data['order_id'];
 *     $member_no = $order_data['member_no'];
 *     $send_addressee_name = $order_data['receiver_info']['send_addressee_name'];
 *     $send_address = $order_data['receiver_info']['send_address'];
 *     $send_cellphone = $order_data['receiver_info']['send_cellphone'];
 *     $addressee_date = $order_data['receiver_info']['addressee_data'];
 *     $sql = "";
 *     $sql .= "INSERT INTO addressee_set SET";
 *     $sql .= " order_no = '$order_id', `name` = '$send_addressee_name', address = '$send_address',";
 *     $sql .= " cellphone = '$send_cellphone', addressee_date = '$addressee_date', m_id = '$member_no'";
 *     mysql_query($sql);
 * }
 */


// step4 does it been share with manager or vip
function process_with_share($order_data, $status = FALSE)
{
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
            $sql4 = ", vip_id = '$vip_id'";
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
        $sql5 = "UPDATE share SET order2_id='$insert_id' WHERE id='" . $row3['id'] . "'";
        mysql_query($sql5);
        $share_id = $row3['id'];
    } else {
        mysql_query($sql4);
        $share_id = mysql_insert_id();
    }
    return $share_id;
}

// step5 create order
function process_create_order($order_data, $status = FALSE)
{
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
            create_order2($cart_pid, $p_name_ary, $pay_time, $TotalAmount, $cart_price, $cart_qty, $order_id, $member_no, $share_id, $paymentchose);
        case 3:
            create_order2($cart_pid, $p_name_ary, $pay_time, $TotalAmount, $cart_price, $cart_qty, $order_id, $manager_id, $share_id, $paymentchose);
        default:
    }
}

if ((!empty($p_id) && !empty($pay_qty)) || (!empty($cart_pid) && !empty($cart_qty))) {
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
        $order_data['share_id'] = process_with_share($order_data, $status);
        process_create_order($order_data, $status);
    }
}


//從商品詳細頁面直接購買產生歐付寶訂單function
function create_order($p_name, $pay_time, $TotalAmount, $web_price, $order_id, $share_id, $p_id, $member_no, $pay_qty, $paymentchose)
{
    /*產生訂單範例*/
    try {
        $oPayment = new AllInOne();
        /* 服務參數 */
        $oPayment->ServiceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5"; //測試環境網址，正式版的話要改為正式環境
        $oPayment->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $oPayment->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $oPayment->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它

        $BackUrl = "http://" . $_SERVER['HTTP_HOST'] . "/17mai/payment_info.php";
        $BackUrl2 = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?url=order_search"; //超商繳費用

        if (@$share_id != "") {
            $BackUrl3 = "http://" . $_SERVER['HTTP_HOST'] . "/payment_info.php?id=" . $order_id . "&p_id=" . $p_id . "&member_no=" . $member_no . "&pay_qty=" . $pay_qty . "&share_id=" . $share_id; //超商繳費用
        } else {
            $BackUrl3 = "http://" . $_SERVER['HTTP_HOST'] . "/payment_info.php?id=" . $order_id . "&p_id=" . $p_id . "&member_no=" . $member_no . "&pay_qty=" . $pay_qty; //超商繳費用
        }

        /* 基本參數 */
        $oPayment->Send['MerchantTradeNo'] = $order_id;//這邊是店家端所產生的訂單編號
        $oPayment->Send['MerchantTradeDate'] = $pay_time; //商店交易時間
        $oPayment->Send['TotalAmount'] = (int)$TotalAmount;//付款總金額，超商最低30元起
        $oPayment->Send['TradeDesc'] = "一起購商城購物";//交易敘述
        if ($paymentchose == "CVS") {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] = $BackUrl3;
        } else {
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

    } catch (Exception $e) {
        // 例外錯誤處理。
        throw $e;
    }
}

//從購物車購買產生綠界訂單function
function create_ecpay_order($order_id, $order_time, $order_amount, $paymentChoice, $products)
{
    try {
        $Ecpay = new AllInOne();
        /* 服務參數 */
        $Ecpay->ServiceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5"; // 測試環境網址，正式版的話要改為正式環境
        $Ecpay->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $Ecpay->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $Ecpay->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它

        $dir = '17mai'; // 如專案不是在伺服器的根目錄才要用
        $BaseURL = $_SERVER['REQUEST_SCHEME'] . $_SERVER['HTTP_HOST'] . '/' . $dir . '/';
        $BakeURL1 = $BaseURL . 'payment_info.php'; // 返回資訊用
        $BakeURL2 = $BaseURL . "index.php?url=order_search&order_id={$order_id}"; // 返回跳轉用

        /* 基本參數 */
        $Ecpay->Send['MerchantTradeNo'] = $order_id;//這邊是店家端所產生的訂單編號
        $Ecpay->Send['MerchantTradeDate'] = $order_time; //商店交易時間
        $Ecpay->Send['TotalAmount'] = (int)$order_amount;//付款總金額，超商最低30元起
        $Ecpay->Send['TradeDesc'] = "一起購商城購物";//交易敘述

        if ($paymentChoice === "CVS") {
            $Ecpay->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
        }
        if ($paymentChoice === 'Credit') {
            $Ecpay->Send['ChoosePayment'] = PaymentMethod::Credit;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
        }
        $Ecpay->Send['IgnorePayment'] = "Alipay";//把不的付款方式取消掉
        $Ecpay->Send['OrderResultURL'] = $BakeURL2; // 跳轉回訂單查詢確認

        $Ecpay->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::No; //若不回傳額外的付款資訊時，參數值請傳：Ｎ
        $Ecpay->Send['Remark'] = "無備註"; //廠商後台訂單備註

        //id=".$order_id."&p_id=".$cart_pid."&member_no=".$member_no."&pay_qty=".$cart_qty."&share_id=".$share_id; //超商繳費用

        $Ecpay->SendExtend['PaymentInfoURL'] = $BakeURL1 . '?' . "order_id={$order_id}";
        $Ecpay->Send['ReturnURL'] = $BakeURL1; //當消費者付款完成後，歐付寶會將付款結果參數回傳到該網址。
        $Ecpay->Send['ClientBackURL'] = $BakeURL2; //消費者點選此按鈕後，會將頁面導回到此設定的網址
        //為付款完成後，歐付寶將頁面導回到會員網址，並將付款結果帶回
        //請填入你主機要接受訂單付款後狀態 回傳的程式名稱 記住 該網址需能對外
        //接受訂單狀態 回傳程式名稱 可在此程式內將付款方式寫入你的訂單中 payment_info.php 與 return.php 程式內容一樣

        //$Ecpay->Send['DeviceSource'] ="M";//參數M表示使用行動版的頁面 不設定此參數 預設就是電腦版顯示

        // 加入選購商品資料。
        foreach ($products as $k => $v) {
            if ($v['status'] === true) {
                $product = array(
                    'Name' => $v['p_name'],
                    'Price' => $v['price'],
                    'Currency' => '元',
                    'Quantity' => $v['qty'],
                    'URL' => '無'
                );
                array_push($Ecpay->Send['Items'], $product);
            }
        }

        /* 產生訂單 */
        $Ecpay->CheckOut();
        /* 產生產生訂單 Html Code 的方法 */
        $szHtml = $Ecpay->CheckOutString();

    } catch (Exception $e) {
        exit();
    }
}

function create_order2($cart_pid, $p_name_ary, $pay_time, $TotalAmount, $cart_price, $cart_qty, $order_id, $member_no, $share_id, $paymentchose, $page)
{
    $cart_pid2 = explode(',', $cart_pid); //商品id的陣列
    $p_name_ary2 = explode(',', $p_name_ary); //商品名稱的陣列
    $cart_price2 = explode(',', $cart_price); //商品金額的陣列
    $cart_qty2 = explode(',', $cart_qty); //商品數量的陣列

    /*產生訂單範例*/
    try {
        $oPayment = new AllInOne();
        /* 服務參數 */
        $oPayment->ServiceURL = "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5"; //測試環境網址，正式版的話要改為正式環境
        $oPayment->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $oPayment->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $oPayment->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它

        $BackUrl2 = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?url=order_search&page=" . $page; //超商繳費用

        if (@$share_id != "") {
            @$BackUrl3 = "http://" . $_SERVER['HTTP_HOST'] . "/payment_info.php?id=" . $order_id . "&p_id=" . $cart_pid . "&member_no=" . $member_no . "&pay_qty=" . $cart_qty . "&share_id=" . $share_id; //超商繳費用
        } else {
            @$BackUrl3 = "http://" . $_SERVER['HTTP_HOST'] . "/payment_info.php?id=" . $order_id . "&p_id=" . $cart_pid . "&member_no=" . $member_no . "&pay_qty=" . $cart_qty; //超商繳費用
        }

        /* 基本參數 */
        $oPayment->Send['MerchantTradeNo'] = $order_id;//這邊是店家端所產生的訂單編號
        $oPayment->Send['MerchantTradeDate'] = $pay_time; //商店交易時間
        $oPayment->Send['TotalAmount'] = (int)$TotalAmount;//付款總金額，超商最低30元起
        $oPayment->Send['TradeDesc'] = "一起購商城購物";//交易敘述
        if ($paymentchose == "CVS") {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] = $BackUrl3;
        } else {
            $oPayment->Send['ChoosePayment'] = PaymentMethod::Credit;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
            $oPayment->Send['OrderResultURL'] = "http://www.17mai.com.tw/index.php?url=order_search&page=" . $page;
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
        for ($i = 0; $i < count($cart_pid2); $i++) {
            array_push($oPayment->Send['Items'], array('Name' => $p_name_ary2[$i], 'Price' => (int)$cart_price2[$i], 'Currency' => "元", 'Quantity' => (int)$cart_qty2[$i], 'URL' => "無"));
        }

        /* 產生訂單 */
        $oPayment->CheckOut();
        /* 產生產生訂單 Html Code 的方法 */
        $szHtml = $oPayment->CheckOutString();

    } catch (Exception $e) {
        // 例外錯誤處理。
        throw $e;
    }
}