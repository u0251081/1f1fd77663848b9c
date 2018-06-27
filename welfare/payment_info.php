<?php

include_once('admin/mysql.php');
sql();
include_once('AllPay.Payment.Integration.php');

@$share_id = $_GET['share_id']; //判斷是否透過行銷經理或VIP會員分享
@$p_id = explode(',',$_GET['p_id']); //購買的商品的id，當收到付款訊號後，透過此id來計算紅利及分潤，格式是陣列
@$member_id = $_GET['member_no'];//訂購人
@$pay_qty = explode(',',$_GET['pay_qty']); //購買數量格式是陣列
@$order_id = $_GET['id'];//資料庫訂單的id
$over_time = date("Y/m/d H:i:s");
if(isset($_GET['id']))//這裡的id為訂單id
{
    try
    {
        $oPayment = new AllInOne();
        /* 服務參數 */
        $oPayment->HashKey = "8zRmJYvEXQ2HpHqu";//介接測試 (Hash Key)這是測試帳號專用的不用改它
        $oPayment->HashIV = "0Pa3jmkAwsErXh6a";//介接測試 (Hash IV)這是測試帳號專用的不用改它
        $oPayment->MerchantID = "3048337";//特店編號 (MerchantID)這是測試帳號專用的不用改它
        /* 取得回傳參數 */
        $arFeedback = $oPayment->CheckOutFeedback();
        /* 檢核與變更訂單狀態 */
        if (sizeof($arFeedback) > 0)
        {
            foreach ($arFeedback as $key => $value)
            {
                /* 支付後的回傳的基本參數 */
                /*switch ($key)
                {
                    case "MerchantID": $szMerchantID = $value; break; //會員編號
                    case "MerchantTradeNo": $szMerchantTradeNo = $value; break; //訂單產生時傳送給O’Pay的會員交易編號。
                    case "PaymentDate": $szPaymentDate = $value; break;
                    case "PaymentType": $szPaymentType = $value; break;
                    case "PaymentTypeChargeFee": $szPaymentTypeChargeFee = $value; break;
                    case "RtnCode": $szRtnCode = $value; break;
                    case "RtnMsg": $szRtnMsg = $value; break;
                    case "SimulatePaid": $szSimulatePaid = $value; break;
                    case "TradeAmt": $szTradeAmt = $value; break;
                    case "TradeDate": $szTradeDate = $value; break;
                    case "TradeNo": $szTradeNo = $value; break;
                    case "PaymentNo": $szPaymentNo = $value; break;//超商代碼
                    case "vAccount": $szVirtualAccount = $value; break;//ATM 虛擬碼
                    default: break;
                }*/

                /* 使用 CVS/BARCODE 交易時回傳的參數 */
                switch ($key)
                {
                    case "MerchantID": $szMerchantID = $value; break;
                    case "MerchantTradeNo": $szMerchantTradeNo = $value; break;
                    case "RtnCode": $szRtnCode = $value; break;
                    case "RtnMsg": $szRtnMsg = $value; break;
                    case "TradeNo": $szTradeNo = $value; break;
                    case "TradeAmt": $szTradeAmt = $value; break;
                    case "PaymentType": $szPaymentType = $value; break;
                    case "TradeDate": $szTradeDate = $value; break;
                    case "ExpireDate":$szExpireDate = $value; break;
                    case "PaymentNo": $szPaymentNo = $value; break;
                    case "Barcode1": $szBarcode1 = $value; break;
                    case "Barcode2": $szBarcode2 = $value; break;
                    case "Barcode3": $szBarcode3 = $value; break;
                    default: break;
                }
            }

            /*echo "商家編號".$szMerchantID."<br>";
            echo "訂單編號".$szMerchantTradeNo."<br>";
            echo "付款時間".$szPaymentDate."<br>";
            echo "付款方式".$szPaymentType."<br>";
            echo "通路費".$szPaymentTypeChargeFee."<br>";
            echo "付款狀態".$szRtnCode."<br>";
            echo "交易訊息".$szRtnMsg."<br>";
            echo "模擬付款".$szSimulatePaid."<br>";
            echo "交易金額".$szTradeAmt."<br>";
            echo "訂單成立時間".$szTradeDate."<br>";
            echo "AllPay交易編號".$szTradeNo."<br>";*/

            // 其他資料處理。
            if(substr($szPaymentType, 0, 3)=='CVS')
            {
                //若付款方式為 超商代碼
                //在這裡把超商代碼存進你的訂單資料表中

                /*echo "廠商編號".$szMerchantID."<br>";
                echo "訂單編號".$szMerchantTradeNo."<br>";
                echo "交易狀態".$szRtnCode."<br>";
                echo "交易訊息".$szRtnMsg."<br>";
                echo "O'Pay的交易編號".$szTradeNo."<br>";
                echo "交易金額".$szTradeAmt."<br>";
                echo "會員選擇的付款方式".$szPaymentType."<br>";
                echo "訂單成立時間".$szTradeDate."<br>";
                echo "繳費期限".$szExpireDate."<br>";
                echo "條碼1".$szBarcode1."<br>";
                echo "條碼2".$szBarcode2."<br>";
                echo "條碼3".$szBarcode3."<br>";*/

                if(isset($szExpireDate))
                {
                    //處理付款截止日期
                    $sql ="UPDATE consumer_order SET order_over='$szExpireDate',tradeno='$szPaymentNo' WHERE order_no='".$_GET['id']."'";
                    mysql_query($sql);
                }

                if($szRtnCode == 1)
                {
                    //付款完成之後修改訂單結束
                    $sql = "UPDATE consumer_order SET order_type_id='$szRtnCode', is_effective='1' WHERE order_no='".$_GET['id']."'";
                    mysql_query($sql);
                    //代表此交易有多筆商品=>從購物車交易
                    if(count($p_id)>1)
                    {
                        if(isset($share_id) && $share_id != "")
                        {
                            //多筆商品有分享處理
                            //如果有分享，修改分享成立
                            $sql2 = "UPDATE share SET is_effective='1' WHERE id='$share_id'";
                            mysql_query($sql2);
                            //查詢分享表，判斷分享結構
                            $share_sql = "SELECT * FROM share WHERE id='$share_id'";
                            $share_res = mysql_query($share_sql);
                            $share_row = mysql_fetch_array($share_res);
                            if($share_row['manager_id'] != "" && $share_row['vip_id'] == "" && $share_row['member_id'] != "")
                            {
                                if($share_row['manager_id'] != $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id，有訂購人id，代表一層分享架構=>行銷經理轉發給VIP會員
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    multi_one_share_pay($_GET['p_id'],$share_row['manager_id'],$share_row['member_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                                else if($share_row['manager_id'] == $share_row['member_id'])
                                {
                                    //如果分享的行銷經理id和購買者id相等，即為經理自己購買，只能得分潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    multi_share_myself($_GET['p_id'],$share_row['manager_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                            }
                            else if($share_row['manager_id'] != "" && $share_row['vip_id'] != "" && $share_row['member_id'] != "")
                            {
                                //如果有經理id，有vip_id，有訂購人id，代表二層分享架構=>VIP會員轉發行銷經理url給朋友
                                //計算消費者購買的商品點數及經理可獲得的利潤
                                //-------------------------------------//
                                $file="pay_log/".$share_row['member_id'].".json";
                                $txt = array('id',$share_id);
                                $json_string = json_encode($txt);
                                file_put_contents($file, $json_string);
                                //--------------------------//
                                multi_two_share_pay($_GET['p_id'],$share_row['manager_id'],$share_row['vip_id'],$share_row['member_id'],$pay_qty);
                                update_qty($_GET['p_id'],$pay_qty);
                            }
                        }
                        else
                        {
                            //多筆商品無分享處理
                            no_share_pay($_GET['p_id'],$member_id,$pay_qty);
                            update_qty($_GET['p_id'],$pay_qty);
                        }
                    }
                    else
                    {
                        //代表此交易只有一筆商品=>直接購買
                        if(isset($share_id) && $share_id != "")
                        {
                            //單筆商品有分享處理
                            //如果有分享，修改分享成立
                            $sql2 = "UPDATE share SET is_effective='1' WHERE id='$share_id'";
                            mysql_query($sql2);

                            //查詢分享表，判斷分享結構
                            $share_sql = "SELECT * FROM share WHERE id='$share_id'";
                            $share_res = mysql_query($share_sql);
                            $share_row = mysql_fetch_array($share_res);
                            if($share_row['manager_id'] != "" && $share_row['vip_id'] == "" && $share_row['member_id'] != "")
                            {
                                if($share_row['manager_id'] != $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id，有訂購人id，代表一層分享架構=>行銷經理轉發給VIP會員
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    one_share_pay($share_row['p_id'],$share_row['manager_id'],$share_row['member_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                                else if($share_row['manager_id'] == $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id,有訂購人id，且經理id等於訂購人id，為自己分享給自己，獲得分潤無點數
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    share_myself($share_row['p_id'],$share_row['manager_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                            }
                            else if($share_row['manager_id'] != "" && $share_row['vip_id'] != "" && $share_row['member_id'] != "")
                            {
                                //如果有經理id，有vip_id，有訂購人id，代表二層分享架構=>VIP會員將行銷經理的url再次轉發給他朋友
                                //--------------------------//
                                $file="pay_log/".$share_row['member_id'].".json";
                                $txt = array('id',$share_id);
                                $json_string = json_encode($txt);
                                file_put_contents($file, $json_string);
                                //--------------------------//
                                two_share_pay($share_row['p_id'],$share_row['manager_id'],$share_row['vip_id'],$share_row['member_id'],$pay_qty);
                                update_qty($_GET['p_id'],$pay_qty);
                            }
                        }
                        else
                        {
                            //單筆商品無分享處理
                            no_share_pay($_GET['p_id'],$member_id,$pay_qty);
                            update_qty($_GET['p_id'],$pay_qty);
                        }
                    }

                    echo '1|OK';
                }
            }
            else if(substr($szPaymentType, 0, 3)=='Cre')
            {
                //若付款方式為ATM 虛擬碼
                //在這裡把ATM虛擬碼存進你的訂單資料表中
            
                if($szRtnCode == 1)
                {
                    $sqlover ="UPDATE consumer_order SET order_over='$over_time' WHERE order_no='".$_GET['id']."'";
                    mysql_query($sqlover);
                    //付款完成之後修改訂單結束
                    $sql = "UPDATE consumer_order SET order_type_id='$szRtnCode', is_effective='1' WHERE order_no='".$_GET['id']."'";
                    mysql_query($sql);
                    //代表此交易有多筆商品=>從購物車交易
                    if(count($p_id)>1)
                    {
                        if(isset($share_id) && $share_id != "")
                        {
                            //多筆商品有分享處理
                            //如果有分享，修改分享成立
                            $sql2 = "UPDATE share SET is_effective='1' WHERE id='$share_id'";
                            mysql_query($sql2);
                            //查詢分享表，判斷分享結構
                            $share_sql = "SELECT * FROM share WHERE id='$share_id'";
                            $share_res = mysql_query($share_sql);
                            $share_row = mysql_fetch_array($share_res);
                            if($share_row['manager_id'] != "" && $share_row['vip_id'] == "" && $share_row['member_id'] != "")
                            {
                                if($share_row['manager_id'] != $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id，有訂購人id，代表一層分享架構=>行銷經理轉發給VIP會員
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    multi_one_share_pay($_GET['p_id'],$share_row['manager_id'],$share_row['member_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                                else if($share_row['manager_id'] == $share_row['member_id'])
                                {
                                    //如果分享的行銷經理id和購買者id相等，即為經理自己購買，只能得分潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    multi_share_myself($_GET['p_id'],$share_row['manager_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                            }
                            else if($share_row['manager_id'] != "" && $share_row['vip_id'] != "" && $share_row['member_id'] != "")
                            {
                                //如果有經理id，有vip_id，有訂購人id，代表二層分享架構=>VIP會員轉發行銷經理url給朋友
                                //計算消費者購買的商品點數及經理可獲得的利潤
                                //-------------------------------------//
                                $file="pay_log/".$share_row['member_id'].".json";
                                $txt = array('id',$share_id);
                                $json_string = json_encode($txt);
                                file_put_contents($file, $json_string);
                                //--------------------------//
                                multi_two_share_pay($_GET['p_id'],$share_row['manager_id'],$share_row['vip_id'],$share_row['member_id'],$pay_qty);
                                update_qty($_GET['p_id'],$pay_qty);
                            }
                        }
                        else
                        {
                            //多筆商品無分享處理
                            no_share_pay($_GET['p_id'],$member_id,$pay_qty);
                            update_qty($_GET['p_id'],$pay_qty);
                        }
                    }
                    else
                    {
                        //代表此交易只有一筆商品=>直接購買
                        if(isset($share_id) && $share_id != "")
                        {
                            //單筆商品有分享處理
                            //如果有分享，修改分享成立
                            $sql2 = "UPDATE share SET is_effective='1' WHERE id='$share_id'";
                            mysql_query($sql2);

                            //查詢分享表，判斷分享結構
                            $share_sql = "SELECT * FROM share WHERE id='$share_id'";
                            $share_res = mysql_query($share_sql);
                            $share_row = mysql_fetch_array($share_res);
                            if($share_row['manager_id'] != "" && $share_row['vip_id'] == "" && $share_row['member_id'] != "")
                            {
                                if($share_row['manager_id'] != $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id，有訂購人id，代表一層分享架構=>行銷經理轉發給VIP會員
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    one_share_pay($share_row['p_id'],$share_row['manager_id'],$share_row['member_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                                else if($share_row['manager_id'] == $share_row['member_id'])
                                {
                                    //如果有經理id，無vip_id,有訂購人id，且經理id等於訂購人id，為自己分享給自己，獲得分潤無點數
                                    //計算消費者購買的商品點數及經理可獲得的利潤
                                    //-------------------------------------//
                                    $file="pay_log/".$share_row['member_id'].".json";
                                    $txt = array('id',$share_id);
                                    $json_string = json_encode($txt);
                                    file_put_contents($file, $json_string);
                                    //--------------------------//
                                    share_myself($share_row['p_id'],$share_row['manager_id'],$pay_qty);
                                    update_qty($_GET['p_id'],$pay_qty);
                                }
                            }
                            else if($share_row['manager_id'] != "" && $share_row['vip_id'] != "" && $share_row['member_id'] != "")
                            {
                                //如果有經理id，有vip_id，有訂購人id，代表二層分享架構=>VIP會員將行銷經理的url再次轉發給他朋友
                                //--------------------------//
                                $file="pay_log/".$share_row['member_id'].".json";
                                $txt = array('id',$share_id);
                                $json_string = json_encode($txt);
                                file_put_contents($file, $json_string);
                                //--------------------------//
                                two_share_pay($share_row['p_id'],$share_row['manager_id'],$share_row['vip_id'],$share_row['member_id'],$pay_qty);
                                update_qty($_GET['p_id'],$pay_qty);
                            }
                        }
                        else
                        {
                            //單筆商品無分享處理
                            no_share_pay($_GET['p_id'],$member_id,$pay_qty);
                            update_qty($_GET['p_id'],$pay_qty);
                        }
                    }

                    echo '1|OK';
                }
            }
            else
            {
                //寫入付款方式
            }
        }
        else
        {
            echo '0|Fail';
        }
    }
    catch (Exception $e)
    {
        // 例外錯誤處理。
        echo '0|' . $e->getMessage();
    }
}

function no_share_pay($p_id,$member_id,$pay_qty)
{
    //這裡是都沒有分享，直接購買的處理。
    $last_bonus = 0;
    $i = 0;
    $b_sql = "SELECT p_bonus FROM product WHERE id IN($p_id) ORDER BY find_in_set(id ,'{$p_id}')";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        //這裡直接計算商品購買後的總點數
        $last_bonus += $b_row['p_bonus'] * $pay_qty[$i];
        $i++;
    }

    //這裡判斷是什麼會員，再把上面的總點數加上原本會員的剩餘點數
    if(strlen($member_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='$member_id'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if($fb_row['bonus'] != "")
        {
            $last_bonus = $fb_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='$member_id'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='$member_id'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $last_bonus = $member_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='$member_id'";
            mysql_query($update);
        }
    }
}

function one_share_pay($p_id,$manager_id,$member_id,$pay_qty)
{
    //取出消費者購買的商品點數及經理可獲得的利潤
    $last_bonus = 0;
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_bonus,p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id)";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        $last_bonus += $b_row['p_bonus'] * $pay_qty[$i];
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }

    if(strlen($member_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='" . $member_id . "'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if ($fb_row['bonus'] != "")
        {
            $last_bonus = $fb_row['bonus'] + $last_bonus;
        }

        if ($last_bonus)
        {
            $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='" . $member_id . "'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$member_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $last_bonus = $member_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='".$member_id."'";
            mysql_query($update);
        }
    }
}

function two_share_pay($p_id,$manager_id,$vip_id,$member_id,$pay_qty)
{
    //這邊處理vip會員再次分享，接收者購買
    $last_bonus = 0;
    $vip_bonus = 0;
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_bonus,p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id)";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        $last_bonus += $b_row['p_bonus'] * $pay_qty[$i]; //vip朋友購買的點數
        $vip_bonus += $b_row['p_bonus'] * $pay_qty[$i]; //vip再次獲得點數
        //第二層行銷經理的實際分潤為，行銷經理的利潤-vip的點數*會員購買數量 ex:100 - 50 *2個 = 100利潤
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    $last_profit = $last_profit - $vip_bonus;

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }

    if(strlen($vip_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$vip_id."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if($fb_row['bonus'] != "")
        {
            $vip_bonus = $fb_row['bonus'] + $vip_bonus;
        }

        if($vip_bonus)
        {
            $update = "UPDATE fb SET bonus='$vip_bonus' WHERE fb_id='".$vip_id."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$vip_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $vip_bonus = $member_row['bonus'] + $vip_bonus;
        }

        if($vip_bonus)
        {
            $update = "UPDATE member SET bonus='$vip_bonus' WHERE member_no='".$vip_id."'";
            mysql_query($update);
        }
    }

    if(strlen($member_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$member_id."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if($fb_row['bonus'] != "")
        {
            $last_bonus = $fb_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='".$member_id."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$member_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $last_bonus = $member_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='".$member_id."'";
            mysql_query($update);
        }
    }
}

function share_myself($p_id,$manager_id,$pay_qty)
{
    //取出消費者購買的商品點數及經理可獲得的利潤
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id)";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
}

function multi_one_share_pay($p_id,$manager_id,$member_id,$pay_qty)
{
    //多筆商品第一層分享架構
    $last_bonus = 0;
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_bonus,p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id) ORDER BY find_in_set(product.id ,'{$p_id}')";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        //這裡直接計算商品購買後的總點數
        $last_bonus += $b_row['p_bonus'] * $pay_qty[$i];
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }

    if(strlen($member_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='" . $member_id . "'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if ($fb_row['bonus'] != "")
        {
            $last_bonus = $fb_row['bonus'] + $last_bonus;
        }

        if ($last_bonus)
        {
            $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='" . $member_id . "'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$member_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $last_bonus = $member_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='".$member_id."'";
            mysql_query($update);
        }
    }
}

function multi_two_share_pay($p_id,$manager_id,$vip_id,$member_id,$pay_qty)
{
    //這邊處理vip會員再次分享，接收者購買
    $last_bonus = 0;
    $vip_bonus = 0;
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_bonus,p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id) ORDER BY find_in_set(product.id ,'{$p_id}')";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        $last_bonus += $b_row['p_bonus'] * $pay_qty[$i]; //vip朋友購買的點數
        $vip_bonus += $b_row['p_bonus'] * $pay_qty[$i]; //vip再次獲得點數
        //第二層行銷經理的實際分潤為，行銷經理的利潤-vip的點數*會員購買數量 ex:100 - 50 *2個 = 100利潤
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    $last_profit = $last_profit - $vip_bonus;

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }

    if(strlen($vip_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$vip_id."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if($fb_row['bonus'] != "")
        {
            $vip_bonus = $fb_row['bonus'] + $vip_bonus;
        }

        if($vip_bonus)
        {
            $update = "UPDATE fb SET bonus='$vip_bonus' WHERE fb_id='".$vip_id."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$vip_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $vip_bonus = $member_row['bonus'] + $vip_bonus;
        }

        if($vip_bonus)
        {
            $update = "UPDATE member SET bonus='$vip_bonus' WHERE member_no='".$vip_id."'";
            mysql_query($update);
        }
    }

    if(strlen($member_id) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$member_id."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        if($fb_row['bonus'] != "")
        {
            $last_bonus = $fb_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE fb SET bonus='$last_bonus' WHERE fb_id='".$member_id."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$member_id."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['bonus'] != "")
        {
            $last_bonus = $member_row['bonus'] + $last_bonus;
        }

        if($last_bonus)
        {
            $update = "UPDATE member SET bonus='$last_bonus' WHERE member_no='".$member_id."'";
            mysql_query($update);
        }
    }
}

function multi_share_myself($p_id,$manager_id,$pay_qty)
{
    //多筆商品第一層分享架構
    $last_profit = 0;
    $i = 0;
    $b_sql = "SELECT p_profit,web_price FROM product,price WHERE product.id=price.p_id AND product.id IN($p_id) ORDER BY find_in_set(product.id ,'{$p_id}')";
    $b_res = mysql_query($b_sql);
    while($b_row = mysql_fetch_array($b_res))
    {
        //這裡直接計算商品購買後的總點數
        $last_profit += (int)floor($b_row['p_profit'] * $b_row['web_price'] /100  ) * $pay_qty[$i];
        $i++;
    }

    //查詢行銷經理看是FB的會員還是一般會員
    $manager_sql = "SELECT * FROM seller_manager WHERE manager_no='".$manager_id."'";
    $manager_res = mysql_query($manager_sql);
    $manager_row = mysql_fetch_array($manager_res);

    if($manager_row['member_id'] != "" && strlen($manager_row['member_id']) > 10)
    {
        $fb_sql = "SELECT * FROM fb WHERE fb_id='".$manager_row['member_id']."'";
        $fb_res = mysql_query($fb_sql);
        $fb_row = mysql_fetch_array($fb_res);
        //如果該經理的點數/利潤為空代表第一次分享，就直接寫入，否則先取出原本的在加上新的在寫回
        if($fb_row['profit'] != "")
        {
            $last_profit = $fb_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE fb SET profit='$last_profit' WHERE fb_id='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
    else
    {
        $member_sql = "SELECT * FROM member WHERE member_no='".$manager_row['member_id']."'";
        $member_res = mysql_query($member_sql);
        $member_row = mysql_fetch_array($member_res);
        if($member_row['profit'] != "")
        {
            $last_profit = $member_row['profit'] + $last_profit;
        }

        if($last_profit)
        {
            $update = "UPDATE member SET profit='$last_profit' WHERE member_no='".$manager_row['member_id']."'";
            mysql_query($update);
        }
    }
}

function update_qty($p_id,$pay_qty)
{
    //這段是將使用者購買數量加到到資料表的sell_qty
    $last_qty = 0;
    $rem_qty = 0;
    $i = 0;
    $update_qty = "SELECT id,sell_qty,rem_qty FROM product WHERE id IN($p_id) ORDER BY FIND_IN_SET(id,'{$p_id}')";
    $update_qty_res = mysql_query($update_qty);
    while($update_qty_row = mysql_fetch_array($update_qty_res))
    {
        $last_qty = $update_qty_row['sell_qty'] + $pay_qty[$i];
        if($rem_qty<0)
        {
            $rem_qty=0;
        }
        else
        {
             $rem_qty = $update_qty_row['rem_qty'] - $pay_qty[$i];
        }
       
        if($last_qty != "")
        {
            $qty_sql = "UPDATE product SET sell_qty='$last_qty',rem_qty='$rem_qty' WHERE id='".$update_qty_row['id']."'";
            mysql_query($qty_sql);
        }
        $i++;
    }
}