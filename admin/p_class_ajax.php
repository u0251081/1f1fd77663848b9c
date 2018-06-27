<?php
/**
 * AJAX 更新排序
 */
/**
 * PDO 資料庫連結設置
 */

try {
    $dbConn = new PDO(
        "mysql:host=localhost;dbname=first_shop",
        'john',
        'h94ru8rmp4',
        array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8")
    );
}
catch (PDOException $e) {
    echo "PDO 連線失敗，請確認設定！";
}


if (isset($_REQUEST['data'])) {
    // PDO 連結

    $sql = 'UPDATE class SET sort = :sort WHERE id = :id';

    $stmt = $dbConn->prepare($sql);

    // 更新所有資料排序
    foreach ($_REQUEST['data'] as $key => $value) {
        $stmt->execute(array(
            'sort'  => $key,
            'id'    => $value
        ));
    }
}
?>