<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/27/18
 * Time: 11:18 PM
 */

namespace Base17Mai;
require_once 'toolFunc.php';

class Consumer extends Base17mai
{
    private $MemberID;

    public function __construct($MemberID = '')
    {
        parent::__construct();
        if (isset($MemberID)) $this->MemberID = $MemberID;
    }

    private function AddToCart($member_id, $productID, $Quantity, $spec = -1)
    {
        $status = $this->checkCartStatus($member_id, $productID, $spec);
        if ($status) {
            $Specification = ($spec === '') ? 'specCode is :specCode' : 'specCode = :specCode';
            $SQL = 'update shoppingcart set Quantity = Quantity + :Quantity where member_no = :member_id and productID = :productID and ' . $Specification . ';';

        } else {
            $SQL = 'insert into shoppingcart set member_no = :member_id, productID = :productID, Quantity = :Quantity, specCode = :specCode;';
        }
        $para = array(
            'member_id' => $member_id,
            'productID' => $productID,
            'Quantity' => ['PARAM_TYPE' => Base17mai::PDO_PARSE_INT, 'VALUE' => $Quantity],
            'specCode' => ['PARAM_TYPE' => Base17mai::PDO_PARSE_INT, 'VALUE' => ($spec === '') ? null : $spec]);
        if ($status) {
            $result = $this->PDOOperator($SQL, $para, Base17mai::DO_UPDATE);
        } else {
            $result = $this->PDOOperator($SQL, $para, Base17mai::DO_INSERT_NORMAL);
        }
        return $result;
    }

    private function checkCartStatus($member_id, $productID, $specCode)
    {
        $para = array(
            'member_no' => $member_id,
            'productID' => $productID,
            'specCode' => array(
                'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                'VALUE' => ($specCode === '') ? -1 : $specCode
            )
        );
        $table = 'shoppingcart';
        $result = $this->checkExistsDataInTable($para, $table);
        return $result;
    }

    private function getInventory($productID, $specCode = -1)
    {
        $Product = new Product();
        $productID = $Product->IDToProductID($productID);
        $SQL = 'select Quantity from productspec where productID = :productID and specCode = :specCode;';
        $para['productID'] = $productID;
        $para['specCode'] = $specCode;
        $rst = $this->PDOOperator($SQL, $para);
        $result = isset($rst[0]) ? $rst[0]['Quantity'] : 0;
        return $result;
    }

    private function checkSpecification($productID, $specCode)
    {
        $SQL = 'select specCode from productspec right join product using(productID) where product.id = :productID;';
        $para['productID'] = $productID;
        $rst = $this->PDOOperator($SQL, $para);
        if (!isset($rst[0]) && $specCode === '') return true;
        else {
            $collection = [];
            foreach ($rst as $value) {
                $collection[] = $value['specCode'];
            }
            return in_array($specCode, $collection);
        }
    }

    private function getProductInCart($member_no, $productID, $specCode)
    {
        $SQL = 'select Quantity from shoppingcart where member_no = :member_no and productID = :productID and specCode = :specCode;';
        $para = ['member_no' => $member_no, 'productID' => $productID, 'specCode' => $specCode];
        $rst = $this->PDOOperator($SQL, $para);
        $result = isset($rst[0]) ? $rst[0]['Quantity'] : 0;
        return $result;
    }

    public function ajaxAddToCart($get, $post)
    {
        $member_id = addslashes(take('member_no', '', 'session'));
        $productID = addslashes($post['productID']);
        $Quantity = addslashes($post['Quantity']);
        $specCode = addslashes($post['spec']);
        $specCode = ($specCode === 'none') ? -1 : $specCode;
        $error = false;
        $result['javascript'] = '';
        $Remain = $this->getInventory($productID, $specCode);
        $InCart = $this->getProductInCart($member_id, $productID, $specCode);
        if ($member_id === '') {
            $error = true;
            $result['javascript'] .= 'showMessage("請先登入會員方可使用本功能");';
        }
        if ($InCart + $Quantity > $Remain || 0 === (Int)$Quantity) {
            $error = true;
            $result['javascript'] .= 'showMessage("欲加入商品數量有誤");';
        }
        if (!$this->checkSpecification($productID, $specCode)) {
            $error = true;
            $result['javascript'] .= 'showMessage("請選擇正確規格");';
        }
        if ($error) $this->PAE($result);
        $rst = $this->AddToCart($member_id, $productID, $Quantity, $specCode);
        $InCart = $this->getProductInCart($member_id, $productID, $specCode);
        if ($rst) $result['javascript'] = 'showMessage("成功加入購物車，目前購物車已有 ' . $InCart . ' 件此商品");';
        else $result['javascript'] = 'showMessage("加入購物車失敗");';
        $this->PAE($result);
    }

    private function checkCartInput($cartitem = [])
    {
        if (!isset($cartitem['productID'])) return false;
        if (!isset($cartitem['Quantity'])) return false;
        if (!isset($cartitem['specCode'])) return false;
        return true;
    }

    private function UpdateCart($item)
    {
        $member_id = take('member_no', '', 'session');
        $item['member_no'] = $member_id;
        $productID = $item['productID'];
        $Remain = $this->getInventory($productID, $item['specCode']);
        if ($item['Quantity'] <= $Remain) {
            $SQL = 'update shoppingcart set Quantity = :Quantity where member_no = :member_no and productID = :productID and specCode = :specCode;';
            $result = $this->PDOOperator($SQL, $item, Base17mai::DO_UPDATE);
            return $result;
        } else {
            return false;
        }
    }

    public function ajaxUpdateCart($get, $post)
    {
        $cnt = 0;
        if (is_array($post['cartItem'])) {
            foreach ($post['cartItem'] as $item) {
                if ($this->checkCartInput($item)) {
                    $result = $this->UpdateCart($item);
                    if ($result) $cnt++;
                } else continue;
            }
        }
        if ($cnt === 0) $javascript = 'showMessage("沒有更新");';
        else $javascript = "showMessage('購物車中有{$cnt}件商品更新');";
        $this->PAE(['javascript' => $javascript]);
    }

    private function RemoveFromCart($member_no, $cartID)
    {
        $SQL = 'delete from shoppingcart where member_no = :member_no and id = :id;';
        $Para = ['id' => $cartID, 'member_no' => $member_no];
        $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_DELETE);
        return $result;
    }

    public function ajaxRemoveFromCart($get, $post)
    {
        $CartID = addslashes($post['CID']);
        $member_no = take('member_no', '', 'session');
        $result = $this->RemoveFromCart($member_no, $CartID);
        if ($result) $javascript = 'showMessage("刪除成功");$(\'tr#' . $CartID . '\').remove();';
        else $javascript = 'showMessage("刪除失敗，請嘗試刷新頁面");';
        $this->PAE(['javascript' => $javascript]);
    }

    private function GetListInCart($member_no)
    {
        $SQL = 'select b.PName, b.unitPrice, c.productID, c.specCode, c.specification, a.Quantity, b.bonus, b.feedBack from shoppingcart as a left join product as b on a.productID = b.id left join productspec as c on a.specCode = c.specCode and b.productID = c.productID where a.member_no = :member_no;';
        $para['member_no'] = $member_no;
        $result = $this->PDOOperator($SQL, $para);
        if (!isset($result)) return false;
        return $result;
    }

    public function ListProductInCart()
    {
        $member_no = take('member_no', '', 'session');
        $result = $this->GetListInCart($member_no);
        if ($result === false) return false;
        else return $result;
    }

    public function CommitCart($Recipient = [])
    {
        $member_no = isset($this->MemberID) ? $this->MemberID : '';
        if ($member_no === '') {
            return false;
        } else {
            $rst = $this->CreateOrder17mai();
            $OrderID = $rst['OrderID'];
            $OrderNO = $rst['OrderNO'];
            $Total = $rst['Total'];
            $paymentChose = $rst['PayType'];
            $PayTime = $rst['PayTime'];
            if ($OrderID === false) return false;
            $products = $this->GetListInCart($member_no);
            $this->UpdateOrder17mai($OrderID, $products);
            $this->SetOrderRecipient($OrderID, $Recipient);
            $result['OrderNO'] = $OrderNO;
            $result['MemberNO'] = $member_no;
            $result['Total'] = $Total;
            $result['PayType'] = $paymentChose;
            $result['PayTime'] = $PayTime;
            $result['Products'] = $products;
            return $result;
        }
    }

    private function GetAmountInCart()
    {
        $SQL = 'select Quantity, unitPrice, bonus, feedBack from shoppingcart as a left join product as b on a.productID = b.id where member_no = :member_no;';
        $para['member_no'] = $this->MemberID;
        $rst = $this->PDOOperator($SQL, $para);
        $result = ['Total' => 0, 'bonus' => 0, 'feedBack' => 0];
        foreach ($rst as $key => $value) {
            $tmp1 = (int)$value['Quantity'] * (int)$value['unitPrice'];
            $tmp2 = (int)$value['Quantity'] * (int)$value['bonus'];
            $tmp3 = (int)$value['Quantity'] * (int)$value['unitPrice'] * (int)$value['feedBack'] / 100;
            $result['Total'] += $tmp1;
            $result['bonus'] += $tmp2;
            $result['feedBack'] += $tmp3;
        }
        return $result;
    }

    private function CreateOrder17mai()
    {
        $member_id = $this->MemberID;
        $paymentChose = addslashes(take('paymentChose', '', 'post'));
        $now = new \DateTime('now');
        $PayTime = $now->format('Y-m-d H:i:s');
        $OrderTime = $now->format('Y-m-d H:i:s');
        $OrderNO = 'B17MA' . time();
        $Total = $this->GetAmountInCart();
        $Para = array(
            'OrderNO' => $OrderNO,
            'member_no' => $member_id,
            'PayType' => $paymentChose,
            'PayTime' => $PayTime,
            'Total' => $Total['Total'],
            'bonus' => $Total['bonus'],
            'feedBack' => $Total['feedBack'],
            'OrderTime' => $OrderTime
        );
        $setColumn = $this->GenerateSQLColumn(', ', $Para);
        $SQL = "insert into consumer_order set {$setColumn};";
        $result['OrderID'] = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_WITHID);
        $result['OrderNO'] = $OrderNO;
        $result['Total'] = $Total['Total'];
        $result['PayType'] = $paymentChose;
        $result['PayTime'] = $now->format('Y/m/d H:i:s');
        return $result;
    }

    private function UpdateOrder17mai($OrderID, $products)
    {
        foreach ($products as $value) {
            $Para = array(
                'OrderID' => $OrderID,
                'PName' => $value['PName'],
                'productID' => $value['productID'],
                'specification' => $value['specification'],
                'specCode' => $value['specCode'],
                'unitPrice' => $value['unitPrice'],
                'Quantity' => $value['Quantity'],
                'bonus' => $value['bonus'],
                'feedBack' => $value['feedBack']
            );
            $setColumns = $this->GenerateSQLColumn(', ', $Para);
            $SQL = "insert into consumer_order_detail set {$setColumns};";
            $rst = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
            if ($rst === false) return false;
        }
        return true;
    }

    private function SetOrderRecipient($OrderID, $Recipient)
    {
        $columns = ['OrderID', 'Recipient', 'CellPhone', 'Address', 'DateType'];
        $Para = [];
        foreach ($columns as $key => $value) {
            if ($value === 'OrderID') $Para[$value] = $OrderID;
            else $Para[$value] = $Recipient[$value];
        }
        $setColumns = $this->GenerateSQLColumn(', ', $Para);
        $SQL = "insert into order_address set {$setColumns};";
        $result = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
        return $result;
    }

    public function ListOrder()
    {
        $member_no = $this->MemberID;
        $SQL = 'select OrderNO, OrderTime, Total, PayType, OrderStatus from consumer_order where member_no = :member_no;';
        $Para['member_no'] = $member_no;
        $rst = $this->PDOOperator($SQL, $Para);
        foreach ($rst as $key => $item) {
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === null ? '訂單無效' : $rst[$key]['OrderStatus'];
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === '0' ? '尚未付款' : $rst[$key]['OrderStatus'];
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === '1' ? '完成付款' : $rst[$key]['OrderStatus'];
            $rst[$key]['PayType'] = $item['PayType'] === 'Credit' ? '信用卡付款' : $rst[$key]['PayType'];
            $rst[$key]['PayType'] = $item['PayType'] === 'CVS' ? '超商代碼付款' : $rst[$key]['PayType'];
        }
        return $rst;
    }

    public function OrderDetail($OrderNO)
    {
        $member_no = $this->MemberID;
        $SQL = 'select id, OrderNO, OrderTime, PayType, Total, OrderOver, OrderStatus from consumer_order where member_no = :member_no and OrderNO = :OrderNO;';
        $Para['member_no'] = $member_no;
        $Para['OrderNO'] = $OrderNO;
        $rst = $this->PDOOperator($SQL, $Para);
        if (!isset($rst[0])) return false;
        foreach ($rst as $key => $item) {
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === null ? '訂單無效' : $rst[$key]['OrderStatus'];
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === '0' ? '尚未付款' : $rst[$key]['OrderStatus'];
            $rst[$key]['OrderStatus'] = $item['OrderStatus'] === '1' ? '完成付款' : $rst[$key]['OrderStatus'];
            $rst[$key]['PayType'] = $item['PayType'] === 'Credit' ? '信用卡付款' : $rst[$key]['PayType'];
            $rst[$key]['PayType'] = $item['PayType'] === 'CVS' ? '超商代碼付款' : $rst[$key]['PayType'];
            $rst[$key]['Detail'] = $this->GetOrderItem($item['id']);
            $rst[$key]['Recipient'] = $this->GetRecipient($item['id']);
            if ($item['PayType'] === 'CVS') $rst[$key]['PaymentInfo'] = $this->GetPaymentInfo($item['OrderNO']);
        }
        return $rst[0];
    }

    private function GetOrderItem($id)
    {
        $SQL = 'select * from consumer_order_detail where OrderID = :OrderID;';
        $Para['OrderID'] = $id;
        $rst = $this->PDOOperator($SQL, $Para);
        return $rst;
    }

    private function GetRecipient($OrderID = '')
    {
        $SQL = 'select * from order_address where OrderID = :OrderID;';
        $Para['OrderID'] = $OrderID;
        $rst = $this->PDOOperator($SQL, $Para);
        return isset($rst[0]) ? $rst[0] : false;
    }

    private function GetPaymentInfo($OrderNO = '')
    {
        $SQL = 'select PaymentNo from order_cvs_information where OrderNO = :OrderNO;';
        $Para['OrderNO'] = $OrderNO;
        $rst = $this->PDOOperator($SQL, $Para);
        $result = isset($rst[0]) ? $rst[0]['PaymentNo'] : '訂單無效，沒有代碼';
        return $result;
    }

}

?>