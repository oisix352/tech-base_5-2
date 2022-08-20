<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title> 
</head>
<body>

<?php
    // ・データベース名：tb240142db・ユーザー名：tb-240142・パスワード：x4KeSzyyZG

    // データベース接続設定(4-1、ここはいじらない)
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,);
    
    // テーブルの作成（4-2、idは自動で登録されているナンバリング、nameは名前を入れる場所で半角英数32文字、commentはコメントを入れる場所）
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "pass char(32),"
    . "date TIMESTAMP"
    .");";
    $stmt = $pdo->query($sql);
    $date = date("Y/m/d H:i:s");

    
    //　新規データの入力（4-5、名前とコメントは後で好きなものに）
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["edit"])){
        $name = $_POST["name"];
        $comment = $_POST["comment"]; 
        $pass = $_POST["pass"];
        $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> execute();
    }
    //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう

    //　編集（4-7、bindParamの引数（:nameなど）は4-2でどんな名前のカラムを設定したかで変える必要がある。）
    if(!empty($_POST["editnumber"]) && !empty($_POST["editpassword"])){        //編集対象番号を入力するフォーム
        $editnumber = $_POST["editnumber"];
        $editpassword = $_POST["editpassword"];

        $sql = 'SELECT * FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $editnumber, PDO::PARAM_INT); 
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            if($editpassword == $row['pass']) {
                $editname = $row['name'];
                $editcomment = $row['comment'];
                $editnumber_form = $editnumber;
                $editpassword_form = $editpassword;
            } else { 
                echo "パスワードが違います";
            }
        }
    }

    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && !empty($_POST["edit"])){  
        $name = $_POST["name"];
        $comment = $_POST["comment"]; 
        $pass = $_POST["pass"];
        $edit = $_POST["edit"];

        $sql = 'UPDATE tbtest SET name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->execute();
    }

    //　削除（4-8）
    if(!empty($_POST["deletenumber"]) && !empty($_POST["deletepassword"])){ 
        $deletenumber = $_POST["deletenumber"];       
        $deletepassword = $_POST["deletepassword"];  
        $sql = 'SELECT * FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $deletenumber, PDO::PARAM_INT); 
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
            if ($deletepassword == $row['pass']){
                $sql = 'DELETE FROM tbtest WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $deletenumber, PDO::PARAM_INT);
                $stmt->execute();
            }else{
                echo "パスワードが違います<br>";
            }
        }  
    }

?>
    <form action="" method="POST">
        <input type="text" name="name" placeholder="名前" value=<?php if(!empty($editname)){echo $editname;} ?>>
        <input type="text" name="comment" placeholder="コメント" value=<?php if(!empty($editcomment)){echo $editcomment;} ?>>
        <input type="text" name="pass" placeholder="パスワードを設定" value=<?php if(!empty($editpassword_form)){echo $editpassword_form;} ?>>
        <input type="hidden" name="edit" value=<?php if(!empty($editnumber_form)){echo $editnumber_form;} ?>>
        <input type="submit" name="submit"><br>
        <input type="text" name="deletenumber" placeholder="削除対象番号">
        <input type="text" name="deletepassword" placeholder="パスワード">
        <input type="submit" name="deletesubmit" value="削除"><br>
    </form>
    <form action="" method="POST">
        <input type="text" name="editnumber" placeholder="編集対象番号">
        <input type="text" name="editpassword" placeholder="パスワード">
        <input type="submit" name="editsubmit" value="編集">

    </form>

<?php

    //　データの抽出・表示（4-6、$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる）
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
    echo "<hr>";
    }


?>


</body>
</html>