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

    public function CheckBonus($threshold = 0)
    {
        $SQL = 'select m_name, member_no, ReMonth,Amount,record_member.bonus from member left join record_member using(member_no) where parent_no = :member_no;';
        $Para['member_no'] = $this->MemberNO;
        $rst = $this->PDOOperator($SQL, $Para);
        foreach ($rst as $key => $value) {
            $rst[$key]['m_name'] = $this->MaskSecret($value['m_name']);
            $rst[$key]['ReMonth'] = substr($value['ReMonth'], 0, 7);
            $rst[$key]['member_no'] = $this->MaskSecret($value['member_no']);
        }
        $result['List'] = $rst;
        $SQL = 'select count(*) as Count, sum(Amount) as Amount, sum(record_member.bonus) as Bonus from member left join record_member using(member_no) where parent_no = :member_no and Amount >= :threshold;';
        $Para['threshold'] = $threshold;
        $rst = $this->PDOOperator($SQL, $Para);
        $result['Count'] = isset($rst[0]['Count']) ? $rst[0]['Count'] : '';
        $result['Amount'] = isset($rst[0]['Amount']) ? $rst[0]['Amount'] : '';
        $result['Bonus'] = isset($rst[0]['Bonus']) ? $rst[0]['Bonus'] : '';
        return $result;
    }

    public function GetSetting()
    {
        $result['angelValue'] = 20000; // 如果超過這個數字，消費額不增加
        $result['threshold'] = 500;    // 如果低語着個數字，消費者不計算
        $result['storeFeedBack'] = 1;  // 店家要直接將營業額乘以這個數字處以100作為家長獎金
        return $result;
    }
}

?>