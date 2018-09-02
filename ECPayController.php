<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/1
 * Time: 下午 06:31
 */

date_default_timezone_set('Asia/Taipei'); //設定台北時區
require_once 'vendor/autoload.php';
require_once 'lib/toolFunc.php';
require_once 'lib/AllPay.Payment.Integration.php';

header("Content-Type:text/html; charset=utf-8");

use Base17Mai\Transaction;
use function
    Base17Mai\take,
    Base17Mai\writeContent;

$command = '';
$command = isset($_GET['PaymentInfo']) ? 'PaymentInfo' : $command;
$command = isset($_GET['Return']) ? 'Return' : $command;

function GetECBackInformation()
{
    $test = false;
    try {
        $ECPay = new AllInOne();
        /* 服務參數 */
        $ECPay->HashKey    = "8zRmJYvEXQ2HpHqu"; //介接測試 (Hash Key)這是測試帳號專用的不用改它
        $ECPay->HashIV     = "0Pa3jmkAwsErXh6a"; //介接測試 (Hash IV)這是測試帳號專用的不用改它
        $ECPay->MerchantID = "3048337";          //特店編號 (MerchantID)這是測試帳號專用的不用改它
        $ECPay->EncryptType = '1';                 //CheckMacValue加密類型，請固定填入1，使用SHA256加密

        if ($test) {
            //服務參數
            $ECPay->HashKey    = '5294y06JbISpM5x9'; //測試用Hashkey，請自行帶入ECPay提供的HashKey
            $ECPay->HashIV     = 'v77hoKGq4kWxNNIS'; //測試用HashIV，請自行帶入ECPay提供的HashIV
            $ECPay->MerchantID = '2000132';           //測試用MerchantID，請自行帶入ECPay提供的MerchantID
        }
        /* 取得回傳參數 */
        $arFeedback = $ECPay->CheckOutFeedback();

        /* 檢核與變更訂單狀態 */

        // 以付款結果訊息進行相對應的處理
        /**
         * 回傳的綠界科技的付款結果訊息如下:
         * Array
         * (
         *     [MerchantID] =>
         *     [MerchantTradeNo] =>
         *     [StoreID] =>
         *     [RtnCode] =>
         *     [RtnMsg] =>
         *     [TradeNo] =>
         *     [TradeAmt] =>
         *     [PaymentDate] =>
         *     [PaymentType] =>
         *     [PaymentTypeChargeFee] =>
         *     [TradeDate] =>
         *     [SimulatePaid] =>
         *     [CustomField1] =>
         *     [CustomField2] =>
         *     [CustomField3] =>
         *     [CustomField4] =>
         *     [CheckMacValue] =>
         * )
         */
        // 在網頁端回應 1|OK
        if (sizeof($arFeedback) > 0) {
            print '1|OK';
            return $arFeedback;
        } else {
            print '0|Fail';
            return false;
        }
    } catch (Exception $e) {
        // 例外錯誤處理。
        // print '0|' . $e->getMessage();
        return false;
    }
}

$log = false;
if ($log)  writeContent('TestController', 'log start command is ' . $command . "\n");
switch ($command) {
    case 'PaymentInfo':
        PaymentInfo();
        break;
    case 'Return':
        PaymentReturn();
        break;
    default:
        exit();
        break;
}

function PaymentInfo()
{
    $log = false;
    if ($log) writeContent('TestController', 'get into PaymentInfo' . "\n", 'append');
    $ECData = GetECBackInformation();
    if ($ECData === false) exit();
    $RtnCode = $ECData['RtnCode'];
    $status = $RtnCode === '10100073' ? 'true' : 'false';
    if ($log) writeContent('TestController', '$RtnCode === \'10100073\' ? ' . $status . "\n", 'append');
    if ($ECData['PaymentType'] === 'ATM' && $RtnCode !== '2') exit();
    if ($ECData['PaymentType'] === 'CVS' && $RtnCode !== '10100073') exit();
    if ($ECData['PaymentType'] === 'BARCODE' && $RtnCode !== '10100073') exit();
    if ($log) writeContent('TestController', '$ECData:' . "\n" . print_r($ECData, true), 'append');
    // update consumer_order (OrderStatus, OrderOver, TradeNO) where (OrderNO = MerchantTradeNo)
    $Transaction = new Transaction();
    $Transaction->PaymentInfo($ECData);

}

function PaymentReturn()
{
    $log = false;
    if ($log) writeContent('TestController', 'get into PaymentReturn', 'append');
    $ECData = GetECBackInformation();
    if ($ECData === false) exit();
    $Transaction = new Transaction();
    $Transaction->PaymentReturn($ECData);
}