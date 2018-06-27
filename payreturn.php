<?php
header("Content-Type:text/html; charset=utf-8");
include_once('AllPay.Payment.Integration.php');
try
{
    $oPayment = new AllInOne();
    /* 服務參數 */
    $oPayment->HashKey = "5294y06JbISpM5x9";
    $oPayment->HashIV = "v77hoKGq4kWxNNIS";
    $oPayment->MerchantID = "2000132";
    /* 取得回傳參數 */
    $arFeedback = $oPayment->CheckOutFeedback();
    /* 檢核與變更訂單狀態 */
    if (sizeof($arFeedback) > 0)
    {
        foreach ($arFeedback as $key => $value)
        {
            switch ($key)
            {
                /* 支付後的回傳的基本參數 */
                case "MerchantID":
                    $szMerchantID = $value;
                    break;
                case "MerchantTradeNo":
                    $szMerchantTradeNo = $value;
                    break;
                case "PaymentDate":
                    $szPaymentDate = $value;
                    break;
                case "PaymentType":
                    $szPaymentType = $value;
                    break;
                case "PaymentTypeChargeFee":
                    $szPaymentTypeChargeFee = $value;
                    break;
                case "RtnCode":
                    $szRtnCode = $value;
                    break;
                case "RtnMsg":
                    $szRtnMsg = $value;
                    break;
                case "SimulatePaid":
                    $szSimulatePaid = $value;
                    break;
                case "TradeAmt":
                    $szTradeAmt = $value;
                    break;
                case "TradeDate":
                    $szTradeDate = $value;
                    break;
                case "TradeNo":
                    $szTradeNo = $value;
                    break;
                default:
                    break;
            }
        }
        // 其他資料處理。
        print '1|OK';
    }
    else
    {
        print '0|Fail';
    }
}
catch (Exception $e)
{
    // 例外錯誤處理。
    print '0|' . $e->getMessage();
}