<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/1
 * Time: 下午 10:49
 */

namespace Base17Mai;
require_once 'toolFunc.php';

use function
    Base17Mai\writeContent;

class Transaction extends Base17mai
{
    public function PaymentInfo($Parameter = [])
    {
        $PaymentType = $Parameter['PaymentType'];     // 付款方式
        writeContent('TransactionLog', 'PaymentType: ' . $PaymentType, 'append');
        $this->PaymentInformationCommon($Parameter);
        preg_match('/(Credit)/', $PaymentType, $CreTest);
        preg_match('/(ATM)/', $PaymentType, $ATMTest);
        preg_match('/(CVS)/', $PaymentType, $CVSTest);
        preg_match('/(BARCODE)/', $PaymentType, $BARTest);
        if (isset($CreTest[0])) $PaymentType = 'Credit';
        if (isset($ATMTest[0])) $PaymentType = 'ATM';
        if (isset($CVSTest[0])) $PaymentType = 'CVS';
        if (isset($BARTest[0])) $PaymentType = 'BARCODE';
        $OrderStatus = 0;
        switch ($PaymentType) {
            case 'ATM':
                writeContent('TransactionLog', 'Get to ATM' . "\n", 'append');
                $result = $this->PaymentInformationInATM($Parameter);
                break;
            case 'BARCODE':
            case 'CVS':
                writeContent('TransactionLog', 'Get to CVS and Barcode' . "\n", 'append');
                $result = $this->PaymentInformationInCVSAndBarcode($Parameter);
                break;
            case 'Credit':
                $result = $this->PaymentInformationInCredit($Parameter);
                break;
            default:
                writeContent('TransactionLog', 'Get to default', 'append');
                $result = false;
                break;
        }
        return $result;
    }

    private function PaymentInformationCommon($Parameter = [])
    {
        # (OrderStatus, OrderOver, TradeNO) where (OrderNO = MerchantTradeNo)
        $Para = array(
            'OrderNO' => $Parameter['MerchantTradeNo'],
            'OrderStatus' => array(
                'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                'VALUE' => 0
            ),
            'OrderOver' => $Parameter['ExpireDate'],
            'TradeNO' => $Parameter['TradeNo']
        );
        $SQL = 'update consumer_order set OrderStatus = :OrderStatus, OrderOver = :OrderOver, TradeNO = :TradeNO where OrderNO = :OrderNO;';
        $result = $this->PDOOperator($SQL, $Para);
        return $result;
    }

    private function PaymentInformationInCredit($Parameter = [])
    {
        return false;
    }

    private function PaymentInformationInCVSAndBarcode($Parameter = [])
    {
        $Para = array(
            'OrderNO' => $Parameter['MerchantTradeNo'],
            'TradeNO' => $Parameter['TradeNo'],
            'PaymentNo' => $Parameter['PaymentNo'],
            'ExpireDate' => $Parameter['ExpireDate'],
            'Barcode1' => $Parameter['Barcode1'],
            'Barcode2' => $Parameter['Barcode2'],
            'Barcode3' => $Parameter['Barcode3']
        );
        $setColumns = $this->GenerateSQLColumn(', ', $Para);
        $SQL = "insert into order_cvs_information set {$setColumns};";
        $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        return $result;
    }

    private function PaymentInformationInATM($Parameter = [])
    {
        $Para = array(
            'OrderNO' => $Parameter['MerchantTradeNo'], // 店家送到綠界的交易序號
            'TradeNO' => $Parameter['TradeNo'],         // 綠界送到店家的交易序號
            'ExpireDate' => $Parameter['ExpireDate'],
            'BankCode' => $Parameter['BankCode'],
            'vAccount' => $Parameter['vAccount']
        );
        $setColumns = $this->GenerateSQLColumn(', ', $Para);
        $SQL = "insert into order_atm_information set {$setColumns}";
        $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        return $result;
    }

    public function PaymentReturn($Parameter = [])
    {
        writeContent('TransactionLog', 'Get to PaymentReturn', 'append');
        $Para = array(
            'OrderNO' => $Parameter['MerchantTradeNo'],        // 特店交易編號
            'TradeNO' => $Parameter['TradeNo'],                // 綠界的交易編號
            'ReturnCode' => $Parameter['RtnCode'],             // 交易狀態，1 為付款成功
            'PaymentDate' => $Parameter['PaymentDate'],        // 付款時間 yyyy/MM/dd HH:mm:ss
            'ChargeFee' => $Parameter['PaymentTypeChargeFee'], // 通路費
            'SimulatePaid' => array(
                'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                'VALUE' => $Parameter['SimulatePaid']          // 模擬交易flag，1為模擬交易，0為真實交易
            )
        );
        $setColumns = $this->GenerateSQLColumn(', ',$Para);
        $SQL = "insert into order_return_information set {$setColumns}";
        $rst1 = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        $rst2 = $this->UpdateConsumerOrder($Parameter['RtnCode'], $Parameter['MerchantTradeNo']);
        $result = $rst1 && $rst2;
        return $result;

    }

    private function UpdateConsumerOrder($RtnCode, $OrderNO)
    {
        $status = $RtnCode === '1' ? true : false;
        $SQL = 'update consumer_order set OrderStatus = :OrderStatus where OrderNO = :OrderNO;';
        $Para = array(
            'OrderNO' => $OrderNO,
            'OrderStatus' => array(
                'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                'VALUE' => $status ? 1 : null
            )
        );
        $rst = $this->PDOOperator($SQL, $Para);
        return $rst;
    }
}