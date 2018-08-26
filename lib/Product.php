<?php
/**
 * Created by PhpStorm.
 * User: andychen
 * Date: 8/24/18
 * Time: 7:16 PM
 */

namespace Base17Mai;

class Product extends Base17mai
{
    private $productID;
    private $productData;

    public function __construct($pid = '')
    {
        parent::__construct();
        if (strlen($pid) === 10 || !empty($pid)) {
            $this->productID = $pid;
        }
    }

    private function UpdateProduct($product = array())
    {
        $update = isset($product['productID']);
        $SQL = '';
        $ParaSpecial = [];
        $headSQL = '';
        $setColumn = '';
        $where = '';
        if (!$update) {
            $SQL .= "insert into product set productID = :productID, registeredDate = :registeredDate, overDate = :overDate,";
            $headSQL .= "insert into product";
            $dateData = $this->NewDateInterval('P4M');
            $ParaSpecial = array(
                'registeredDate' => $dateData['from'],
                'overDate' => $dateData['to']
            );
            $productID = $this->generateProductID();
        } else {
            $headSQL .= "update product";
            $SQL .= "update product set";
            $productID = $product['productID'];
            $QuantityOrigin = $this->getOriginQuantity($productID);
            $product['Quantity'] = $QuantityOrigin + $product['supplement'];
        }
        $SQL .= " vendorID = :vendorID, PName = :PName, youtube = :youtube,";
        $SQL .= " QuantityOrigin = :oquantity, QuantityRemain = :rquantity, unitPrice = :unitPrice, feedBack = :feedBack,";
        $SQL .= " bonus = :bonus, healthResume = :healthResume, description = :description, Prelease = :Prelease,";
        $SQL .= " productInformation = :productInformation";
        if ($update) {
            $SQL .= " where productID = :productID";
            $where .= " where productID = :productID";
        }
        $ParaCommon = array(
            'productID' => $productID,
            'vendorID' => $product['s_id'],
            'PName' => $product['PName'],
            'QuantityOrigin' => $product['Quantity'],
            'QuantityRemain' => $product['Quantity'],
            'unitPrice' => $product['unitPrice'],
            'feedBack' => $product['feedBack'],
            'bonus' => $product['bonus'],
            'healthResume' => $product['healthResume'],
            'description' => $product['description'],
            'youtube' => $product['youtube'],
            'Prelease' => array(
                'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                'VALUE' => $product['Prelease']
            ),
            'productInformation' => $product['p_info']
        );
        $Para = array_merge($ParaSpecial, $ParaCommon);
        $columnArray = [];
        foreach ($Para as $key => $value) {
            $columnArray[] = $key . ' = :' . $key;
        }
        $setColumn .= ' set ' . implode(', ', $columnArray);
        $SQL = $headSQL . $setColumn . $where;
        $rstMan = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);

        // product classify
        $SQL = "delete from product_class where productID = '{$productID}';";
        $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = 'insert into product_class set pid = :pid, productID = :productID;';
        foreach ($product['sclass'] as $key => $value) {
            if ($value !== 'none') {
                $para = array('pid' => $value, 'productID' => $productID);
                $rstCls = $this->PDOOperator($SQL, $para, Base17mai::DO_UPDATE);
            }
        }

        // product specification
        $SQL = "delete from productspec where productID = '{$productID}';";
        $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = 'insert into productspec set productID = :productID, specCode = :specCode, specification = :specification;';
        $cnt = 0;
        foreach ($product['p_spec'] as $key => $value) {
            if (strlen($value) > 0) {
                $Para = array(
                    'productID' => $productID,
                    'specCode' => $cnt,
                    'specification' => $value
                );
                $rstSpec = $this->PDOOperator($SQL, $Para, Base17mai::DO_INSERT_NORMAL);
                $cnt++;
            }
        }
        $result = $rstMan || $rstCls || $rstSpec;
        return $result;
    }

    private function deleteProduct($productID = '')
    {
        $productID = addslashes($productID);
        $SQL = "delete from product where productID = '{$productID}';";
        $rstM = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = "delete from product_class where productID = '{$productID}';";
        $rstC = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = "delete from productspec where productID = '{$productID}';";
        $rstS = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = "delete from product_img where productID = '{$productID}';";
        $rstI = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $SQL = "delete from product_track where productID = '{$productID}';";
        $rstT = $this->PDOOperator($SQL, [], Base17mai::DO_DELETE);
        $result = $rstC || $rstI || $rstM || $rstS || $rstT;
        return $result;
    }

    private function getOriginQuantity($productID = '')
    {
        $SQL = 'select QuantityOrigin from product where productID = :productID';
        $Para = ['productID' => $productID];
        $rst = $this->PDOOperator($SQL, $Para);
        $result = isset($rst[0]) ? $rst[0]['QuantityOrigin'] : 0;
        return $result;
    }

    private function generateProductID()
    {
        do {
            $result = $this->generateRandom(10);
        } while ($this->productExists($result));
        return $result;
    }

    private function productExists($ProductID = '')
    {
        if (strlen($ProductID) > 0) {
            $result = $this->checkExistsDataInTable(['productID' => $ProductID], 'product');
            return $result;
        }
        return false;
    }

    private function CheckInputForNewProduct(&$receive)
    {
        $checkFlag = true;
        $javascript = '';
        if (count($receive['sclass']) === 1 && $receive['sclass'][0] === 'none') {
            $javascript .= 'showMessage(\'請選擇至少一個分類\');';
            $checkFlag = false;
        }
        if (strlen($receive['PName']) < 1) {
            $javascript .= 'showMessage(\'商品名稱必須填寫\');';
            $checkFlag = false;
        }
        if (!isset($receive['productID'])) {
            if (!isset($receive['Quantity']) || $receive['Quantity'] < 1) {
                $javascript .= 'showMessage(\'商品數量填寫有誤\');';
                $checkFlag = false;
            }
        } else {
            $QuantityOrigin = $this->getOriginQuantity($receive['productID']);
            $quantity = $QuantityOrigin + $receive['supplement'];
            if ($quantity < 0) {
                $javascript .= 'showMessage(\'商品數量填寫有誤\');';
                $checkFlag = false;
            }
        }
        if (!isset($receive['unitPrice']) || $receive['unitPrice'] < 1) {
            $javascript .= 'showMessage(\'商品價格填寫有誤\');';
            $checkFlag = false;
        }
        if (!isset($receive['feedBack']) || $receive['feedBack'] < 1) {
            $javascript .= 'showMessage(\'回饋比率填寫有誤\');';
            $checkFlag = false;
        }
        if (!isset($receive['bonus']) || $receive['bonus'] < 1) {
            $javascript .= 'showMessage(\'紅利點數填寫有誤\');';
            $checkFlag = false;
        }
        if (!$checkFlag) print json_encode(['javascript' => $javascript]);
        return $checkFlag;
    }

    public function ajaxUpdateProduct($get, $post)
    {
        if ($this->CheckInputForNewProduct($post)) {
            $result = $this->UpdateProduct($post);
            $javascript = '';
            if ($result) {
                if (isset($post['productID'])) {
                    $javascript .= 'showMessage("修改成功");';
                    $javascript .= 'location.href="home.php?url=product";';
                } else {
                    $javascript .= 'showMessage("新增成功");';
                    $javascript .= 'location.href="home.php?url=product";';
                }
            } else {
                if (isset($post['productID'])) {
                    $javascript .= 'showMessage("沒有修改");';
                } else {
                    $javascript .= 'showMessage("新增失敗");';
                }

            }
            $this->PAE(['javascript' => $javascript]);
        }
        exit();
    }

    public function ajaxDeleteProduct($get, $post)
    {
        if (isset($post['productID'])) $productID = addslashes($post['productID']);
        $result = $this->deleteProduct($productID);
        if ($result) $this->PAE(['javascript' => 'showMessage("刪除成功");']);
        else $this->PAE(['javascript' => 'showMessage("刪除失敗，詳情請恰開發人員");']);
    }

    public function ajaxUpdateImage($get, $post)
    {
        $productID = addslashes($post['productID']);
        if ($productID === '') $this->PAE(['javascript' => 'showMessage("更新失敗");']);
        $oldFile = $this->getImage($productID);
        $filedir = "images/product/";//指定上傳資料
        $tmpFiles = [];
        $newFiles = [];
        $result = false;
        $realpath = $this->RootDir . 'admin/';
        foreach ($_FILES as $key => $value) {
            if ($value['error'] === 4) continue;
            if ($key === 'Cover') {
                $tmpFiles['Cover'] = $value['tmp_name'];
                $newFiles['Cover'] = $this->generateFileName($realpath . $filedir, date('Ymd'));
            } else {
                $tmpFiles[] = $value['tmp_name'];
                $newFiles[] = $this->generateFileName($filedir, date('Ymd'));
            }
        }
        foreach ($newFiles as $key => $value) {
            if ($value !== false) {
                if ($key === 'Cover') $cover = 1;
                else $cover = 0;
                // if (file_exists($realpath . $oldFile[$key])) unlink($realpath . $oldFile[$key]);
                move_uploaded_file($tmpFiles[$key], $realpath . $filedir . $value);
                $SQL = '';
                if (isset($oldFile[$key])) $SQL .= "delete from productimage where picture = '{$oldFile[$key]}';";
                $SQL .= "insert into productimage set productID = :productID, picture = :picture, cover = :cover;";
                $Para = array(
                    'productID' => $productID,
                    'picture' => $filedir . $value,
                    'cover' => array(
                        'PARAM_TYPE' => Base17mai::PDO_PARSE_INT,
                        'VALUE' => $cover
                    )
                );
                $this->PDOOperator($SQL, $Para, Base17mai::DO_SELECT);
                $result = true;
            }
        }
        if ($result) $javascript = 'showMessage("更新成功");location.href="home.php?url=product_img";';
        else $javascript = 'showMessage("沒有更新");';
        $this->PAE(['javascript' => $javascript]);
    }

    public function setEditorData()
    {
        // initial
        $SQL = 'desc product;';
        $rst = $this->PDOOperator($SQL);
        $productData = [];
        foreach ($rst as $value)
            $productData[$value['Field']] = '';
        $productData['p_spec'] = [];
        $productData['class'] = [];

        // get Basic Data
        $SQL = "select * from product where productID = '{$this->productID}';";
        $rst = $this->PDOOperator($SQL);
        if (isset($rst[0])) {
            foreach ($rst[0] as $key => $value) {
                $productData[$key] = $value;
            }
        }

        // get specification data
        $SQL = "select * from productspec where productID = '{$productData['productID']}';";
        $rst = $this->PDOOperator($SQL);
        if (isset($rst[0])) {
            foreach ($rst as $key => $value) {
                $productData['p_spec'][$key]['specCode'] = $value['specCode'];
                $productData['p_spec'][$key]['specification'] = $value['specification'];
            }
        }

        // get classify data
        $SQL = "select * from product_class where productID = '{$productData['productID']}';";
        $rst = $this->PDOOperator($SQL);
        if (isset($rst[0])) {
            foreach ($rst as $key => $value) {
                $productData['class'][] = $value['pid'];
            }
        }
        $this->productData = $productData;
        return true;
    }

    public function getEditorProductTitle()
    {
        $result = (empty($this->productData['productID'])) ? '新增商品資料' : '修改商品資料';
        return $result;
    }

    public function getProductID()
    {
        $productID = $this->productData['productID'];
        $result = (empty($productID)) ? '新商品ID將於刊登後自動產生' : $productID . "<input type=\"hidden\" name=\"productID\" value=\"{$productID}\">";
        return $result;
    }

    public function getClass()
    {
        $set = $this->productData['class'];
        $result = $this->getClassChildHtml($set);
        return $result;
    }

    private function getClassChildHtml($set = [], $parentid = '')
    {
        if ($parentid === '') {
            $SQL = 'select * from class where id = parent_id';
            $Para = [];
        } else {
            $SQL = 'select * from class where id != parent_id and parent_id = :parent_id';
            $Para = ['parent_id' => $parentid];
        }
        $order = ' order by sort;';
        $class = $this->PDOOperator($SQL . $order, $Para);
        // 平日那天
        $result = '';
        if (count($class) > 0) {
            $targetID = '';
            $result .= '<select name="sclass[]" class="sclass span2">';
            $result .= '<option value="none">請選擇分類</option>';
            foreach ($class as $key => $value) {
                $status = in_array($value['id'], $set);
                if ($status === true) $targetID = $value['id'];
                $select = ($status) ? 'selected' : '';
                $result .= "<option value=\"{$value['id']}\" {$select}>{$value['name']}</option>";
            }
            $result .= '</select>';
            print $select;
            if ($targetID !== '') {
                $result .= $this->getClassChildHtml($set, $targetID);
            }
        }
        return $result;
    }

    public function getSpecification()
    {
        $productID = $this->productData['productID'];
        $productID = (empty($productID)) ? false : $productID;
        if ($productID === false) {
            $result = '';
        } else {
            $SQL = "select * from productspec where productID = :productID;";
            $para = ['productID' => $productID];
            $specification = $this->PDOOperator($SQL, $para);
            $result = '';
            foreach ($specification as $key => $value) {
                $spec = $value['specification'];
                $input = "<input type=\"text\" name=\"p_spec[" . $key . "]\" class=\"input-large\" placeholder = \"請輸入商品規格\" value=\"{$spec}\" />";
                $code = "<span>" . str_pad((String)($key + 1), 2, '0', STR_PAD_LEFT) . "&nbsp;:&nbsp;&nbsp;</span>";
                $remove = "<a href=\"javascript:void(0);\" class=\"btn_RemoveSpec\" value=\"" . $key . "\" >&#x2715;</a>";
                $br = "<br id=\"{$key}\">";
                $div = "<div>{$code}{$input}{$remove}</div>";
                if ($key > 0) $result .= $br . $div;
                else $result = $div;
            }
        }
        return $result;
    }

    public function getPName()
    {
        $result = $this->productData['PName'];
        return $result;
    }

    public function getQuantity()
    {
        $quantity = $this->productData['QuantityRemain'];
        if ($quantity === '') {
            $result = "<input type=\"number\" name=\"Quantity\" min=\"0\" class=\"input-large\" placeholder=\"請輸入商品數量\">";
        } else {
            $result = "商品已登記剩餘數量：" . $quantity . "<br>補充：" . "<input type=\"number\" name=\"supplement\" class=\"input-large\" placeholder=\"請輸入商品數量\">";
        }
        return $result;

    }

    public function getUnitPrice()
    {
        $result = $this->productData['unitPrice'];
        return $result;

    }

    public function getFeedBack()
    {
        $result = $this->productData['feedBack'];
        return $result;

    }

    public function getBonus()
    {
        $result = $this->productData['bonus'];
        return $result;

    }

    public function getResume()
    {
        $result = $this->productData['healthResume'];
        return $result;

    }

    public function getDescription()
    {
        $result = $this->productData['description'];
        return $result;

    }

    public function getInformation()
    {
        $result = $this->productData['productInformation'];
        return $result;

    }

    public function getYoutube()
    {
        $result = $this->productData['youtube'];
        return $result;

    }

    public function getRelease()
    {
        $status = $this->productData['Prelease'];
        $result = ($status === '1') ? 'checked' : '';
        return $result;

    }

    public function listAllProducts()
    {
        $column = ['productID', 'PName', 'QuantityOrigin', 'QuantityRemain', 'registeredDate', 'Prelease'];
        $SQLColumn = implode(', ', $column);
        $SQL = 'select ' . $SQLColumn . ' from product';
        $result = $this->PDOOperator($SQL);
        foreach ($result as $key => $value) {
            if ($value['Prelease'] === '1') $result[$key]['Prelease'] = '上架';
            else $result[$key]['Prelease'] = '下架';
        }
        return $result;
    }

    public function listAllProductsWithImage()
    {
        $column = ['productID', 'PName', 'null as Image', 'registeredDate'];
        $SQLColumn = implode(', ', $column);
        $SQL = 'select ' . $SQLColumn . ' from product;';
        $rst = $this->PDOOperator($SQL);
        $result = [];
        foreach ($rst as $key => $value) {
            $value['Image'] = $this->getImage($value['productID']);
            $result[$key] = $value;
        }
        return $result;
    }

    public function getImage($productID = '')
    {
        $productID = ($productID === '') ? $this->productData['productID'] : $productID;
        $SQL = "select picture, Cover from productImage where productID = '{$productID}'";
        $rst = $this->PDOOperator($SQL);
        $result = [];
        foreach ($rst as $key => $value) {
            if ($value['Cover'] === '1') $result['Cover'] = $value['picture'];
            else $result[] = $value['picture'];
        }
        if (empty($result)) $result['Cover'] = '';
        return $result;
    }
}