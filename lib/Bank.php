<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/8/17
 * Time: 下午 08:41
 */

namespace Base17Mai;
class Bank extends Base17mai
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getBankList()
    {
        $SQL = 'select * from bankidlist';
        $result = $this->PDOOperator($SQL);
        return $result;
    }

    public function checkBankIDExists($targetID = false)
    {
        if ($targetID !== false) {
            $targetID = addslashes($targetID);
            $Parameter = ['code' => $targetID];
            $Table = 'bankidlist';
            $result = $this->checkExistsDataInTable($Parameter,$Table);
            return $result;
        } else {
            return false;
        }
        /*
        if ($targetID !== false) {
            $targetID = addslashes($targetID);
            $SQL = "select count(*) as chk from bankidlist where code = :targetID;";
            $Parameter = ['targetID' => $targetID];
            $resultArray = $this->PDOOperator($SQL, $Parameter);
            if (isset($resultArray[0]['chk']) && $resultArray[0]['chk'] === '1') $result = true;
            else $result = false;
            return $result;
        }
        */
    }

}

?>