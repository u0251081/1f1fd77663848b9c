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

    public function __construct()
    {
        parent::__construct();
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
        $SQL = 'select b.PName, b.unitPrice, c.specification, a.Quantity from shoppingcart as a left join product as b on a.productID = b.id left join productspec as c on a.specCode = c.specCode and b.productID = c.productID where a.member_no = :member_no;';
        $para['member_no'] = $member_no;
        $result = $this->PDOOperator($SQL,$para);
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

}

?>