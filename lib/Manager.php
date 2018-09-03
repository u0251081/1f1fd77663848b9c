<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/3
 * Time: 上午 01:54
 */

namespace Base17Mai;
require_once 'toolFunc.php';

class Manager extends Base17mai
{
    private $MemberNO;
    private $ManagerNO;

    public function __construct()
    {
        parent::__construct();
        $this->MemberNO = take('member_no', '', 'session');
        $this->ManagerNO = take('manager_no', '', 'session');
    }

    public function ListCrewMember()
    {
        $SQL = 'select city, area, m_name, id_card, email, born, address, cellphone from member left join city on city_id = city.id left join area on area_id = area.id where parent_no = :parent_no;';
        $Para['parent_no'] = $this->MemberNO;
        $rst = $this->PDOOperator($SQL, $Para);
        foreach ($rst as $key => $item) {
            $rst[$key]['id_card'] = $this->MaskSecret($item['id_card']);
            if (isset($rst[$key]['bank_no'])) $rst[$key]['bank_no'] = $this->MaskSecret($item['bank_no']);
            $rst[$key]['email'] = $this->MaskSecret($item['email']);
            $rst[$key]['m_name'] = $this->MaskSecret($item['m_name']);
            $rst[$key]['born'] = $this->MaskSecret($item['born']);
            $rst[$key]['address'] = $item['city'] . ' ' . $item['area'] . ' ' . $this->MaskSecret($item['address']);
            $rst[$key]['cellphone'] = $this->MaskSecret($item['cellphone']);
        }
        return $rst;
    }

    public function CheckBonus()
    {
        $SQL = 'select * from record_manager where manager_no = :manager_no;';
        $Para['manager_no'] = $this->ManagerNO;
        $rst = $this->PDOOperator($SQL, $Para);
        return $rst;
    }
}

?>