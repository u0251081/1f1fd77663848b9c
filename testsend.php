<?php
include "send.php";

$order_no='12344565';
$product="哈密瓜";
$order_time='2018-3-1';
$email='u0551815@nkfust.edu.tw';
$address='不知道路';
$cellphone="098123156";
$name='不知道名';
$pay_type='信用卡';
$money='1000';
$buy_person='測試';
$buy=array("14","25","26");

// $msql="SELECT picture FROM product_img WHERE p_id IN(".implode(',', $buy).") AND is_main='1'";
// $res=mysql_query($msql);
// $i=0;
// $list_arr=array();
// while($list=mysql_fetch_array($res))
// {  //
// $list_arr[$i]=$list;
// $i++;
// }
 
 


// $testpro="";
//     for($i=0;$i<count($list_arr);$i++)
//     {
//         $test=" <tr><td><a href='#m_9036011004944316767_'>
//            <img style='margin-right:10px;width:70px;height:70px;float:left;margin-bottom:15px' src='http://www.17mai.com.tw/admin/".$list_arr[$i]['picture']."' alt=''>
//           </a>
//             <div><p style='margin:0 3px 3px'>".$buy[$i]."</p></div>
//           </td>
//            <td style='vertical-align:top'>
//             <div style='text-align:right;float:right'>
//              <span>".$buy[$i]."</span>
//              </div>
//            </td> </tr>
//           ";
//         $testpro=$testpro.$test;

//     }


//     echo $testpro;


echo send($order_no,$product,$order_time,$email,$address,$cellphone,$name,$pay_type,$money,$buy_person,$buy);






?>