<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>智慧門市列表</title>
</head>

<body>
    <center>
        <table class="table responsive table-bordered">
            <tr>
                <h4 class="widgettitle">智慧門市列表</h4>
            </tr>
            <thead>
                <tr>
                    <th align="center">編號</th>
                    <th align="center">名稱</th>
                    <th align="center">負責人</th>
                    <th align="center">聯絡電話</th>
                    <th align="center">註冊日期</th>
                    <th align="center">狀態</th>
                    <th align="center">操作</th>
                </tr>
            </thead>
            <?php
                $sql = "SELECT * FROM store";
                $res = $pdo -> prepare($sql);
                $res -> execute();
                while($row = $res -> fetch())
                {
                    ?>
                    <tbody>
                    <tr align="center">
                      <td width="3%" align="center"><?php echo $row['id'];?></td>
                        <td width="8%" align="center"><?php echo $row['store_name']; ?></td>
                        <td width="10%"><?php echo $row['ceo']; ?></td>
                        <td width="12%"><?php echo $row['cellphone']; ?></td>
                        <td width="12%"><?php echo $row['registration_time']; ?></td>
                        <td width="5%">
                            <?php
                                switch($row['status'])
                                {
                                    case 0:
                                        echo "<center><span style='font-size:15px; padding:8px; height:10px;' class='label label-important'>停用</span></center>";
                                    break;
                                        
                                    case 2:
                                        echo "<center><span style='font-size:15px; padding:8px; height:10px;' class='label label-warning'>待審核</span></center>";
                                    break;
                                        
                                    case 1:
                                        echo "<center><span style='font-size:15px; padding:8px; height:10px;' class='label label-success'>啟用</span></center>";
                                    break;
                                }
                            ?>
                        </td>
                        <td width="12%">
                           <a href="#" class="btn btn-primary">
                            <i class="iconsweets-magnifying iconsweets-white"></i>查看
                           </a>
                        </td>
                    </tr>
                    </tbody>
                    <?php
                }
            ?>
        </table>
    </center>
</body>
</html>