<?php
session_start();
header("Content-Type:text/html; charset=utf-8");
require_once 'vendor/autoload.php';
require_once 'lib/toolFunc.php';
require_once 'AllPay.Payment.Integration.php';

use Base17Mai\Consumer;
use function Base17Mai\take;

$MemberID = take('member_no', '', 'session');

$Consumer = new Consumer($MemberID);

function checkInput($receive)
{
    if (is_array($receive)) {
        if (!isset($receive['Recipient']) || $receive['Recipient'] === '') return false;
        if (!isset($receive['CellPhone']) || $receive['CellPhone'] === '') return false;
        if (!isset($receive['Address']) || $receive['Address'] === '') return false;
        if (!isset($receive['DateType']) || $receive['DateType'] === '') return false;
        if (!isset($receive['paymentChose']) || $receive['paymentChose'] === '') return false;
        return true;
    } else {
        return false;
    }
}

if (checkInput($_POST)) {
    $Recipient = array(
        'Recipient' => take('Recipient', '', 'post'),
        'CellPhone' => take('CellPhone', '', 'post'),
        'Address' => take('Address', '', 'post'),
        'DateType' => take('DateType', '', 'post')
    );
    $stag1 = $Consumer->CommitCart($Recipient);
    try {
        CreateECOrder($stag1);
    } catch (Exception $e) {
        print_r($e);
    }
    exit();
}

function CreateECOrder($options)
{
    $OrderID = $options['OrderNO'];
    $PayTime = $options['PayTime'];
    $PayType = $options['PayType'];
    $Total = $options['Total'];
    $productList = $options['Products'];

    /*產生訂單範例*/
    try {
        $ECPay = new AllInOne();
        /* 服務參數 */
        $testURL = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';
        $realURL = 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5';
        $ECPay->ServiceURL = $testURL;           //測試環境網址，正式版的話要改為正式環境
        $ECPay->HashKey = "8zRmJYvEXQ2HpHqu"; //介接測試 (Hash Key)這是測試帳號專用的不用改它
        $ECPay->HashIV = "0Pa3jmkAwsErXh6a"; //介接測試 (Hash IV)這是測試帳號專用的不用改它
        $ECPay->MerchantID = "3048337";          //特店編號 (MerchantID)這是測試帳號專用的不用改它

        $dir = '17mai';
        $dir = ($dir === '') ? '' : $dir . '/';
        $BaseURL = "http://{$_SERVER['HTTP_HOST']}/{$dir}";
        $BackURL = "{$BaseURL}ECPayController.php"; // 處理回傳訊息用

        /* 基本參數 */
        $ECPay->Send['MerchantTradeNo'] = $OrderID;//這邊是店家端所產生的訂單編號
        $ECPay->Send['MerchantTradeDate'] = $PayTime; //商店交易時間
        $ECPay->Send['TotalAmount'] = (int)$Total;//付款總金額，超商最低30元起
        $ECPay->Send['TradeDesc'] = "一起購商城購物";//交易敘述

        if ($PayType === 'CVS') {
            $ECPay->Send['ChoosePayment'] = PaymentMethod::CVS;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
        }
        if ($PayType === 'Credit') {
            $ECPay->Send['ChoosePayment'] = PaymentMethod::Credit;//付自行選款方式 這邊是開啟所有付款方式讓消費者擇
        }

        // + PaymentInfoURL: 訂單建立後，綠界即送出消費者付款方式相關資訊(期限、方式、帳號...等)
        // * ReturnURL:      當消費者付款完成後，綠界會將付款結果參數以幕後(Server POST)回傳到該網址。
        //   ClientBackURL:  消費者點選返回商店後的導向網址
        //   OrderResultURL: 付款結果導向與傳遞參數的網址，若不設定則停留在綠界的付款完成

        # ATM、CVS 或 BARCODE 的取號結果通知
        $ECPay->SendExtend['PaymentInfoURL'] = "{$BackURL}?PaymentInfo";
        $ECPay->Send['ReturnURL'] = "{$BaseURL}?Return";
        $ECPay->Send['ClientBackURL'] = "{$BaseURL}index.php?url=order_search";
        $ECPay->Send['OrderResultURL'] = "{$BaseURL}index.php?url=order_search";

        $ECPay->Send['NeedExtraPaidInfo'] = ExtraPaymentInfo::No; //若不回傳額外的付款資訊時，參數值請傳：Ｎ
        $ECPay->Send['Remark'] = "無備註"; //廠商後台訂單備註


        // 當消費者付款完成後，歐付寶會將付款結果參數回傳到該網址。
        // 消費者點選此按鈕後，會將頁面導回到此設定的網址
        // 為付款完成後，歐付寶將頁面導回到會員網址，並將付款結果帶回
        // 請填入你主機要接受訂單付款後狀態 回傳的程式名稱 記住 該網址需能對外
        // 接受訂單狀態 回傳程式名稱 可在此程式內將付款方式寫入你的訂單中 payment_info.php 與 return.php 程式內容一樣

        $ECPay->Send['IgnorePayment'] = "Alipay";//把不的付款方式取消掉
        //$ECPay->Send['DeviceSource'] ="M";//參數M表示使用行動版的頁面 不設定此參數 預設就是電腦版顯示

        // 加入選購商品資料。
        foreach ($productList as $item) {
            $product = array(
                'Name' => $item['PName'] . '/' . $item['specification'],
                'Price' => (int)$item['unitPrice'],
                'Currency' => '元',
                'Quantity' => (int)$item['Quantity'],
                'URL' => '無'
            );
            array_push($ECPay->Send['Items'], $product);
        }

        /* 產生訂單 */
        $ECPay->CheckOut();
        /* 產生產生訂單 Html Code 的方法 */
        $szHtml = $ECPay->CheckOutString();

    } catch (Exception $e) {
        // 例外錯誤處理。
        throw $e;
    }
}