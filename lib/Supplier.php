<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2019/1/23
 * Time: 上午 11:55
 */

namespace Base17Mai;
require_once 'toolFunc.php';

use Base17Mai\Member;

class Supplier extends Base17mai
{

    private $SupplierID;

    public function __construct($SupplierID = false)
    {
        parent::__construct();
        if ($SupplierID === false) $SupplierID = take('SupplierID', '', 'session');
        $this->SupplierID = $SupplierID;
    }

    public function ReleaseProduct()
    {
        $SQL = 'select id from product where vendorID = :SupplierID and Prelease = true';
        $Parameter = array('SupplierID' => $this->SupplierID);
        $PIDList = $this->PDOOperator($SQL, $Parameter, self::DO_SELECT);
        $PIDs = array();
        foreach ($PIDList as $item) $PIDs[] = $item['id'];
        $result = (new Product())->ListProductsByFront($PIDs);
        /*
        $result['count'] = count($result['content']);
        if ($result['count'] > 0) {
            foreach ($result['content'] as $key => $item) {
                $SQL = 'select picture from productimage where productID = :productID and Cover = true';
                $Parameter = ['productID' => $item['productID']];
                $IMG_rst = $this->PDOOperator($SQL, $Parameter);
                $result['content'][$key]['image'] = isset($IMG_rst[0]) ? $IMG_rst[0]['picture'] : '';
            }
        }
        */
        return $result;
    }

    public function GetSupplierID()
    {
        return $this->SupplierID;
    }

}