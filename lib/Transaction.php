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
        $setColumns = $this->GenerateSQLColumn(', ', $Para);
        $SQL = "insert into order_return_information set {$setColumns}";
        $rst1 = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        $rst2 = $this->UpdateConsumerOrder($Parameter['RtnCode'], $Parameter['MerchantTradeNo']);
        $rst3 = $this->UpdateMemberRecord($Parameter['RtnCode'], $Parameter['MerchantTradeNo']);
        $rst4 = $this->UpdateManagerRecord($Parameter['RtnCode'], $Parameter['MerchantTradeNo']);
        $result = $rst1 && $rst2 && $rst3 && $rst4;
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

    private function GetOrderByOrderNO($OrderNO = '')
    {
        $SQL = 'select * from consumer_order where OrderNO = :OrderNO;';
        $Para['OrderNO'] = $OrderNO;
        $rst = $this->PDOOperator($SQL, $Para);
        return isset($rst[0]) ? $rst[0] : false;
    }

    private function UpdateMemberRecord($RtnCode, $OrderNO)
    {
        $status = $RtnCode === '1' ? true : false;
        if ($status === false) return false;
        $Order = $this->GetOrderByOrderNO($OrderNO);
        if ($Order === false) return false;
        // check record exists
        $member_no = $Order['member_no'];
        $ReMonth = substr($Order['PayTime'], 0, 7) . '-00';
        $Para = array(
            'member_no' => $member_no,
            'ReMonth' => $ReMonth
        );
        $Table = 'record_member';
        $check = $this->checkExistsDataInTable($Para, $Table);
        // if record not exists, then create one
        if ($check === false) {
            $SQL = 'insert into record_member set member_no = :member_no, ReMonth = :ReMonth;';
            $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        }
        // get origin record
        $SQL = 'select * from record_member where member_no = :member_no and ReMonth = :ReMonth;';
        $rst = $this->PDOOperator($SQL, $Para);
        if (!isset($rst[0])) return false;
        $ReAmount = (int)$rst[0]['Amount'];
        $ReBonus = (int)$rst[0]['bonus'];
        // setup new record
        $newAmount = (int)$ReAmount + (int)$Order['Total'];
        $newBonus = (int)$ReBonus + (int)$Order['bonus'];
        $Para['Amount'] = array(
            'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
            'VALUE' => $newAmount
        );
        $Para['bonus'] = array(
            'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
            'VALUE' => $newBonus
        );
        $SQL = 'update record_member set Amount = :Amount, bonus = :bonus where member_no = :member_no and ReMonth = :ReMonth;';
        $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_UPDATE);
        return $rst;
    }

    private function UpdateManagerRecord($RtnCode, $OrderNO)
    {
        $status = $RtnCode === '1' ? true : false;
        if ($status === false) return false;
        $Order = $this->GetOrderByOrderNO($OrderNO);
        if ($Order === false) return false;
        // get manager_no by parent_no
        $SQL = 'select manager_no from member left join seller_manager on parent_no = seller_manager.member_id where member_no = :member_no;';
        $Para['member_no'] = $Order['member_no'];
        $rst = $this->PDOOperator($SQL, $Para);
        $manager_no = isset($rst[0]) ? $rst[0]['manager_no'] : '';
        // check record exists
        if ($manager_no === '') return false;
        $ReMonth = substr($Order['PayTime'], 0, 7) . '-00';
        $Para = array(
            'manager_no' => $manager_no,
            'ReMonth' => $ReMonth
        );
        $Table = 'record_manager';
        $check = $this->checkExistsDataInTable($Para, $Table);
        // if record not exists, then create one
        if ($check === false) {
            $SQL = 'insert into record_manager set manager_no = :manager_no, ReMonth = :ReMonth;';
            $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        }
        // get origin record
        $SQL = 'select * from record_manager where manager_no = :manager_no and ReMonth = :ReMonth;';
        $rst = $this->PDOOperator($SQL, $Para);
        if (!isset($rst[0])) return false;
        $ReAmount = (int)$rst[0]['Amount'];
        $ReBonus = (int)$rst[0]['bonus'];
        // setup new record
        $newAmount = (int)$ReAmount + (int)$Order['Total'];
        $newBonus = (int)$ReBonus + (int)$Order['bonus'];
        $Para['Amount'] = array(
            'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
            'VALUE' => $newAmount
        );
        $Para['bonus'] = array(
            'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
            'VALUE' => $newBonus
        );
        $SQL = 'update record_manager set Amount = :Amount, bonus = :bonus where manager_no = :manager_no and ReMonth = :ReMonth;';
        $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_UPDATE);
        return $rst;
    }

}