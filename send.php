<?php
function send($order_no,$product,$order_time,$email,$address,$cellphone,$name,$pay_type,$money,$buy_person,$buy){

$msql="SELECT picture FROM product_img WHERE p_id IN(".implode(',',$buy).") AND is_main='1'";
$res=mysql_query($msql);
$i=0;
$list_arr=array();
while($list=mysql_fetch_array($res))
{  //
$list_arr[$i]=$list;
$i++;
}
 


$testpro="";
    for($i=0;$i<count($list_arr);$i++)
    {
        $test=" <tr><td><a href='#m_9036011004944316767_'>
           <img style='margin-right:10px;width:70px;height:70px;float:left;margin-bottom:15px' src='http://www.17mai.com.tw/admin/".$list_arr[$i]['picture']."' alt=''>
          </a>
            <div><p style='margin:0 3px 3px'>".$buy[$i]."</p></div>
          </td>
           <td style='vertical-align:top'>
            <div style='text-align:right;float:right'>
             <span>".$buy[$i]."</span>
             </div>
           </td> </tr>
          ";
        $testpro=$testpro.$test;

    }
    

  $to =$email; //收件者
  $subject ="=?UTF-8?B?".base64_encode("[春燕築巢 地方創生電子商城]  #○○訂單編號○○ :".$order_no."  訂單狀態 更新為: 已完成")."?="; //信件標題
  $headers ="MIME-Version: 1.0\r\n" .
            "Content-type: text/html; charset=UTF-8\r\n" .
            "From:firstshop@17mai.com.tw"; //寄件者firstshop@17mai.com.tw

  $msg = "
<div dir='ltr'><div class='gmail_quote'><br><u></u>

            <div style='margin:0;padding:0' bgcolor='#F0F0F0' marginwidth='0' marginheight='0'>

                <table border='0' width='100%' height='100%' cellpadding='0' cellspacing='0' bgcolor='#F0F0F0'>
                    <tbody><tr>
                            <td align='center' valign='top' bgcolor='#F0F0F0' style='background-color:#f0f0f0'>
                                <br>

                                <table border='0' width='600' cellpadding='0' cellspacing='0' class='m_9036011004944316767container' style='width:600px;max-width:600px'>
                                    <tbody><tr>
                                            <td class='m_9036011004944316767container-padding m_9036011004944316767content' align='left' style='padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px;background-color:#ffffff'>
                                                <div style='min-height:auto;padding:15px;text-align:center;max-height:150px;max-width:100%'>
                                                    <img style='max-height:150px;max-width:100%' src='https://i.imgur.com/BZYXvXB.jpg'></div>
                                                <br>
                                                <div style='height:1px;border-bottom:1px solid #cccccc;clear:both'> </div>
                                                <br>
                                                <p style='font-weight:bold;font-size:18px;line-height:24px;color:#1c2538'>[春燕築巢 地方創生電子商城] #○○訂單編號○○ :".$order_no." 訂單狀態 更新為: 已完成 </p>
                                                <p></p><p>訂單狀態為：已完成 </p><p>付款狀態為：已付款 </p><p>送貨狀態為：備貨中</p><p><span style='font-family: arial, sans-serif;'>有關訂單的查詢或要聯絡春燕築巢 地方創生電子商城。</span></p>
                                                <p style='height:1px;border-bottom:1px solid #cccccc;clear:both'></p>


                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>訂單日期</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>
                                                   ".$order_time."
                                                </div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>訂單號碼</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>".$order_no."</div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>訂單狀態</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>已完成
                                                </div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>商店</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>春燕築巢 地方創生電子商城(<a href='mailto:&#9;firstfictc@gmail.com' target='_blank'>firstfictc@gmail.com</a>)
                                                </div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>顧客名稱</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>".$buy_person."&nbsp;</div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>付款管理</div>

                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>".$pay_type."</div>




                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>付款狀態</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>
                                                    已付款
                                                </div>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>送貨方式</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>宅配</div>


                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>收件地址</div>
                                                <div style='margin-bottom:5px;font-size:14px;color:#555555'>".$address."</div>
                                                <span style='height:10px;display:block'> </span>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>收件人</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>".$name."</div>
                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>收件人電話號碼</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'>".$cellphone."</div>
                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'><br></div>
                                                <span style='height:10px;display:block'> </span>

                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>送貨狀態</div>
                                                <div style='margin-bottom:15px;font-size:14px;color:#555555'><span style='font-family: Helvetica, Arial, sans-serif;'>備貨中</span></div>


                                                <div style='margin-bottom:5px;font-size:12px;color:#333333;text-transform:uppercase'>訂單詳情</div>
                                                <table style='width:100%;font-size:12px;color:#333333'>
                                                    <tbody>
                                                        
                                                        ".$testpro."
                                                        
                                                        <tr>
                                                            <td style='text-align:right;padding-bottom:10px;font-size:12px;color:#333333;text-transform:uppercase'>小計:</td>
                                                            <td style='text-align:right;padding-bottom:10px;font-size:12px;color:#555555'>NT$".$money."</td>
                                                        </tr>


                                                        <tr>
                                                            <td style='text-align:right;padding-bottom:10px;font-size:12px;color:#333333;text-transform:uppercase'>合計:</td>
                                                            <td style='text-align:right;padding-bottom:10px;font-size:12px;color:#555555'>
                                                                NT$".$money."
                                                            </td>
                                                        </tr>

                                                    </tbody></table>

                                                <br>
                                            </td>
                                        </tr>

                                        
                                        <tr>
                                            <td class='m_9036011004944316767container-padding m_9036011004944316767footer-text' align='left' style='line-height: 16px; padding-left: 24px; padding-right: 24px;'>
                                                <br><br><font color='#aaaaaa' face='Helvetica, Arial, sans-serif'><span style='font-size: 12px;'><b>春燕築巢 地方創生電子商城</b>&nbsp;</span></font><br><br><font color='#aaaaaa' face='Helvetica, Arial, sans-serif'><span style='font-size: 12px;'>
                                                    您會收到這封電郵是由於您使用這個電郵地址在春燕築巢 地方創生電子商城下了訂單。
                                                </span></font><br><br>

                                                <br><br>
                                            </td>
                                        </tr>
                                    </tbody></table>

                            </td>
                        </tr>
                    </tbody></table>


            </div>

        </div><br></div>

";



//信件內容
  if(mail("$to", "$subject", "$msg", "$headers")):
   return "信件已經發送成功。";//寄信成功就會顯示的提示訊息
  else:
   return "信件發送失敗！";//寄信失敗顯示的錯誤訊息
  endif;

  }
?>
