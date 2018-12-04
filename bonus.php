<?php
/**
 * Created by PhpStorm.
 * User: Xin-an
 * Date: 2018/9/3
 * Time: 上午 03:05
 */

use Base17Mai\Manager,
    Base17Mai\Member;

$Manager = new Manager();
$Member = new Member();
$setting = $Manager->GetSetting();
$threshold = $setting['threshold'];
$angelValue = $setting['angelValue'];
$storeFee = $setting['storeFee'];
$Bonus = $Manager->CheckBonus($threshold);
$BonusList = $Bonus['List'];

$Count = (int)$Bonus['Count'];
$Amount = (int)$Bonus['Amount'];
$average = ($Count > 0) ? $Amount / $Count : 0;
$TotalBonus = $Bonus['Bonus'];

$self = $Member->GetRecord($member_id);
$selfAmount = $self[0]['Amount'];
$rate = ($average > 0) ? $selfAmount / $average : 0;
$rate = $selfAmount >= $angelValue ? 1 : $rate;
$rate = $rate >= 1 ? 1 : $rate;


$Amount = number_format($Amount, 2);
$average = number_format($average, 2);
$selfAmount = number_format($selfAmount, 2);
$rate = number_format($rate, 2);
?>
<!-- 網站位置導覽列 -->
<section id="aa-catg-head-banner">
    <div class="container">
        <br>
        <div class="aa-catg-head-banner-content">
            <ol class="breadcrumb">
                <li><a href="index.php">首頁</a></li>
                <li><a href="index.php?url=member_center">會員專區</a></li>
                <li class="active">獎勵查詢</li>
            </ol>
        </div>
    </div>
</section>
<!-- / 網站位置導覽列 -->
<script>
    function dis_msg(txt) {
        if (typeof(txt) === 'string') {
            if ($('#device').text() === 'mobile') {
                window.javatojs.showInfoFromJs(txt);
            }
            if ($('#device').text() === 'desktop') {
                alert(txt);
            }
        }
    }
</script>
<style>
    /*額外做顏色，沒有什麼意義*/
    tr.tr-only-hide {
        color: #D20B2A;
    }

    @media (max-width: 736px) {
        .table-rwd {
            min-width: 100%;
        }

        /*針對tr去做隱藏*/
        tr.tr-only-hide, .table-rwd th {
            display: none !important;
        }

        /*讓tr變成區塊主要讓他有個區塊*/
        .table-rwd tr {
            display: block;
            border: 1px solid #ddd;
            margin-top: 5px;
        }

        .table-rwd td {
            text-align: left;
            font-size: 15px;
            overflow: hidden;
            width: 100%;
            display: block;
        }

        .table-rwd td:before {
            /*最重要的就是這串*/
            content: attr(data-th) " : ";
            /*最重要的就是這串*/
            display: inline-block;
            text-transform: uppercase;
            font-weight: bold;
            margin-right: 10px;
            color: #D20B2A;
        }

        /*當RWD縮小的時候.table-bordered 會有兩條線，所以針對.table-bordered去做修正*/
        .table-rwd.table-bordered td, .table-rwd.table-bordered th, .table-rwd.table-bordered {
            border: 0;
        }

    }
</style>
<div class="container">
    <div class="row">
        <h3 style="font-family: '微軟正黑體'; font-weight: bold; color: #d62408;">獎勵查詢</h3>
        <hr>
        <ul class="nav nav-tabs">
            <li class="active">
                <a data-toggle="tab" href="#record">本月成員消費金額</a>
            </li>
            <li>
                <a data-toggle="tab" href="#self">本月家長消費金額</a>
            </li>
            <li>
                <a data-toggle="tab" href="#conclusion">本月獎金總結</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="record" class="tab-pane fade in active">
                <h4>成員消費金額</h4>
                <p>目前系統設置<span style="color: red">有效消費額</span>為&nbsp;&nbsp;&nbsp;<?= $threshold ?>&nbsp;&nbsp;&nbsp;元。
                </p>
                <table class="dataTable table table-bordered table-responsive table-condensed" id="pay_check_div">
                    <thead>
                    <tr>
                        <th colspan="5"></th>
                    </tr>
                    <tr style="background: #DDDDDD;">
                        <th style="text-align: center;">姓名</th>
                        <th style="text-align: center;">會員編號</th>
                        <th style="text-align: center;">記錄月份</th>
                        <th style="text-align: center;">消費金額</th>
                        <th style="text-align: center;">累計點數</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($BonusList as $item) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $item['m_name'] ?></td>
                            <td style="text-align: center;"><?= $item['member_no'] ?></td>
                            <td style="text-align: center;"><?= $item['ReMonth'] ?></td>
                            <td style="text-align: right;"><?= $item['Amount'] ?></td>
                            <td style="text-align: right;"><?= $item['bonus'] ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <table class="table table-responsive table-condensed table-rwd">
                    <tr>
                        <th colspan="2">總計</th>
                    </tr>
                    <tr>
                        <th>有效消費人數</th>
                        <td data-th="有效消費人數"><?= $Count ?></td>
                        <th>有效消費總額</th>
                        <td data-th="有效消費總額"><?= $Amount ?></td>
                        <th>有效平均消費</th>
                        <td data-th="有效平均消費"><?= $average ?></td>
                        <th>有效累計點數</th>
                        <td data-th="有效累計點數"><?= $TotalBonus ?></td>
                    </tr>
                </table>
            </div>
            <div id="self" class="tab-pane fade">
                <h3>本月家長消費金額</h3>
                <p>目前系統設置<span style="color: red">天使金額</span>為&nbsp;&nbsp;&nbsp;<?= $angelValue ?>&nbsp;&nbsp;&nbsp;元。
                </p>
                <table class="table table-responsive table-condensed table-rwd">
                    <tr>
                        <th colspan="2">總計</th>
                    </tr>
                    <tr>
                        <th>家長消費總額</th>
                        <td data-th="家長消費總額"><?= $selfAmount ?></td>
                        <th>成員平均消費</th>
                        <td data-th="有效平均消費"><?= $average ?></td>
                        <th>貢獻比率</th>
                        <td data-th="貢獻比率"><?= $rate ?></td>
                    </tr>
                </table>
            </div>
            <div id="conclusion" class="tab-pane fade">
                <h3>本月獎金總結</h3>
                <p>實得點數為有效消費總計點數 x 家長自身貢獻比率 + 店家營業額 x 店家回饋比率</p>
                <table class="table table-responsive table-condensed table-striped table-rwd">
                    <tr>
                        <th colspan="2">成員消費總結</th>
                    </tr>
                    <tr>
                        <th>有效消費人數</th>
                        <td data-th="有效消費人數"><?= $Count ?></td>
                        <th>有效消費總額</th>
                        <td data-th="有效消費總額"><?= $Amount ?></td>
                        <th>有效平均消費</th>
                        <td data-th="有效平均消費"><?= $average ?></td>
                        <th>有效累計點數</th>
                        <td data-th="有效累計點數"><?= $TotalBonus ?></td>
                    </tr>
                </table>
                <table class="table table-responsive table-condensed table-striped table-rwd">
                    <tr>
                        <th colspan="2">點數總結</th>
                    </tr>
                    <tr>
                        <th>家長消費總額</th>
                        <td data-th="家長消費總額"><?= $selfAmount ?></td>
                        <th>貢獻比率</th>
                        <td data-th="貢獻比率"><?= $rate ?></td>
                        <th>家長實得點數</th>
                        <td data-th="家長實得點數"><?= (int)($TotalBonus * $rate) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="container">
            <div class="row" style="text-align: right;">
                <input type="button" class="btn btn-default" value="返回" onclick="history.go(-1);">&nbsp;&nbsp;
                <!--                            <input type="button" class="btn btn-primary" value="兌換" id="pay_btn">-->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("html,body").scrollTop(750);
        $("#aa-slider").hide();
    });
</script>
<div style="height: 20vh;"></div>