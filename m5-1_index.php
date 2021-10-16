<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-1</title>
</head>
<body>
       
    
<?php

//データベース接続設定
$dsn = 'データベース名'; //データベース名・ホスト名を指定し変数に代入。
$user = 'ユーザー名'; //ユーザー名を変数に代入。
$password = 'パスワード'; //パスワードを変数に代入。
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); //PDOを格納 new演算子はクラスから実体を作る演算子
//データベース設定終わり

//テーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tb1" //tbtestがまだデータベースに存在しない場合、tbtestを作成する
//登録する項目
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY," //連続した数値を自動でカラムに格納する
. "name char(32)," //名前を入れる。文字列、半角英数で32字
. "comment TEXT," //コメントを入れる。文字列、長めの文章も入る
. "date TEXT," //日付を入れる
. "password TEXT" //パスワードを入れる。
.");";
$stmt = $pdo->query($sql); //SQLを実行する。データベースを操作できる。
    

//日付データを変数に代入
$date = date("Y/m/d H:i:s");

    //投稿フォーム
    //削除フォームと編集フォームがともに空の場合
    if(empty($_POST["delete"]) && empty($_POST["edit_n"]))
    {
        //新規投稿の場合
        //名前とコメントどちらかに入力がある場合
        if((!empty($_POST["name"]) || !empty($_POST["comment"])) && !empty($_POST["password1"]) 
        && empty($_POST["password2"])
        && empty($_POST["password3"]))
        {
            $name = $_POST["name"]; //名前を変数に代入。htmlspecialchars…ENT_QUOTESは不正なhtmlタグの埋め込みを防止する
            $comment = $_POST["comment"];//コメントを変数に代入。
            $password1 = $_POST["password1"];//パスワードを変数に代入。
            //データベース内のテーブルを読み込み、POSTで受け取った内容を書き込み
            //prepare, bindParam, executeはPDOでデータベースを操作する際に使うメソッド。
            $sql = $pdo -> prepare("INSERT INTO tb1 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            //prepareで実行したいSQL文をセットする。PDOにあるデータを取得し、SQLに代入
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password1, PDO::PARAM_STR);
            $sql -> execute(); //executeで実際にSQLを実行する。
            $password1 = "";//パスワードが空の場合も変数に代入する
        }
    }
    //編集機能として投稿された場合
    //編集フォームだけ入力がある場合
    if(!empty($_POST["edit_n"]) && !empty($_POST["password3"]) && empty($_POST["delete"]))
    {
         //テーブルに登録されたデータを取得し、画面に表示。
         $sql = 'SELECT * FROM tb1';
         $stmt = $pdo->query($sql);//$stmtはPDOStatementオブジェクトを表す。$stmt = $pdo->query($sql);はSQLを実行することを示す。
         $results = $stmt->fetchAll();//fetchAllは全ての結果行を含む配列を繰り返すことを意味する。$stmtの中にある配列を取り出して結果に代入する。

         $edit_n = $_POST["edit_n"]; //編集番号を変数に代入。

         foreach ($results as $row)
         { //結果を行の数だけ繰り返すループ処理
            if($row["id"] == $edit_n && $row["password"] == $_POST["password3"]) //投稿番号と一致したときに画面に表示
            {
                $name = $_POST["name"]; //名前を変数に代入。htmlspecialchars…ENT_QUOTESは不正なhtmlタグの埋め込みを防止する
                $comment = $_POST["comment"];//コメントを変数に代入。
                if(empty($_POST["name"]) && empty($_POST["comment"])) //指定した投稿番号の名前とコメントを変数に代入する
                {
                    $name = $row["name"];
                    $comment = $row["comment"];
                }
            }
            else if($row["id"]==$edit_n && $row["password"]!=$_POST["password3"])//パスワードが一致していなかった場合
            {
                $edit_n = "";
                $name = "";
                $comment = "";
            }
        }
    }
    if((empty($_POST["password1"]) && empty($_POST["passeord2"]) && empty($_POST["password3"]))
    ||(!empty($_POST["delete"]) && empty($_POST["password2"]))
    ||(!empty($_POST["edit_n"]) && empty($_POST["password3"]))
    ||(!empty($_POST["passeord1"]) && !empty($_POST["password3"])))//フォームに必要な情報が入力されていない場合
        {
            $name = "";
            $comment = "";
            $edit_n = "";
        }
 ?>

<form action="" method="post">
     こうもと作の掲示板<br><br>
        【投稿フォーム】<br>
        名前：　　　
        <input type="text" name="name" value="<?php if(!empty($_POST["edit_n"])){echo $name;}?>" placeholder="名前"><br>
        コメント：　
        <input type="text" name="comment" value="<?php if(!empty($_POST["edit_n"])){echo $comment;}?>" placeholder="コメント"><br>
        パスワード：
        <input type="text" name="password1" placeholder="パスワード"><br>
        <input type="submit" name="submit" value="投稿"><br> <br>
        
        【削除フォーム】<br>
        削除番号：　
        <input type="text" name="delete" placeholder="削除番号"><br>
        パスワード：
        <input type="text" name="password2" placeholder="パスワード"><br>
        <input type="submit" name="submit_d" value="削除"><br><br>
        
        【編集フォーム】<br>
        編集番号：　
        <input type="text" name="edit_n" value="<?php if(!empty($_POST["edit_n"])){echo $edit_n;}?>" placeholder="編集対象番号"><br>
        パスワード：
        <input type="text" name="password3" placeholder="パスワード"><br>
        <input type="submit" name="edit" value="編集"><br><br>
       
        --------------------------------------------------------
        </form>
      【 投稿一覧 】<br>

<?php
    //削除フォームだけに入力があるとき
    if(!empty($_POST["delete"]) && empty($_POST["edit_n"]) && 
    !empty($_POST["password2"]) && empty($_POST["password1"]))
    {
        $id = $_POST["delete"]; //削除番号を変数に代入
        $password = $_POST["password2"]; //削除パスワードを変数に代入
        
        //入力した投稿番号のデータを削除
        $sql = 'delete from tb1 where id=:id AND password=:password'; //指定した投稿を削除
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        //データベースのテーブル一覧を表示
        $sql = 'SELECT * FROM tb1';
        $stmt = $pdo->query($sql);//$stmtはPDOStatementオブジェクトを表す。$stmt = $pdo->query($sql);はSQLを実行することを示す。
        $results = $stmt->fetchAll();//fetchAllは全ての結果行を含む配列を繰り返すことを意味する。$stmtの中にある配列を取り出して結果に代入する。
        
        //テーブルを削除する
        $sql = 'DROP TABLE tb1';
        $stmt = $pdo->query($sql);

        //削除した上でテーブルを作り直し、削除更新後の画面を表示する
        $sql = "CREATE TABLE IF NOT EXISTS tb1"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY," //連続した数値を自動でカラムに格納する
        . "name char(32)," //名前を入れる。文字列、半角英数で32字
        . "comment TEXT," //コメントを入れる。文字列、長めの文章も入る
        . "date TEXT,"
        . "password TEXT" //パスワードを入れる。
        .");";
        $stmt = $pdo->query($sql); //SQLを実行する。データベースを操作できる。

        $id = 1;
        foreach($results as $row)
        {
            $name = $row["name"];
            $comment = $row["comment"];
            $date = $row["date"];
            $password = $row["password"];

            //prepare, bindParam, executeはPDOでデータベースを操作する際に使うメソッド。
            $sql = $pdo -> prepare("INSERT INTO tb1 (id, name, comment, date, password) VALUES (:id, :name, :comment, :date, :password)");
            //preoareで実行したいSQL文をセットする。PDOにあるデータを取得し、SQLに代入
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql ->bindParam(':id', $id, PDO::PARAM_INT); //INTの意味
            $sql -> execute(); //executeで実際にSQLを実行する。
            //更新するときは前の投稿番号に+1していく
            $id = $id+1;
        
        }
    }

    //編集フォームから取得した、名前とコメントを編集して書き込む
    if(!empty($_POST["edit_n"]) && empty($_POST["delete"]) && 
    (!empty($_POST["name"]) || !empty($_POST["comment"])) && 
    !empty($_POST["password3"]) && empty($_POST["password1"]))
    {
        //データベースのテーブル一覧を表示
        $sql = 'SELECT * FROM tb1';
        $stmt = $pdo->query($sql);//$stmtはPDOStatementオブジェクトを表す。$stmt = $pdo->query($sql);はSQLを実行することを示す。
        $results = $stmt->fetchAll();//fetchAllは全ての結果行を含む配列を繰り返すことを意味する。$stmtの中にある配列を取り出して結果に代入する。
        $idmax = count($results);//結果を数えたら？？

        $id = $_POST["edit_n"];

        //
        if($idmax>=$id)
        {
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password3"];

            //データベースの編集
            $sql = 'UPDATE tb1 SET name=:name,comment=:comment,date=:date, password=:password WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();   
        }
        //含まない場合
        else
        {
            $name = $row["name"];
            $comment = $row["comment"];
            $date = $row["date"];
            $password = $row["password"];

            //データベースへの書き込み
            $sql = $pdo -> prepare("INSERT INTO tb1 (id,name, comment , date , password) VALUES (:id,:name, :comment, :date, :password )");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $sql ->bindParam(':id', $id, PDO::PARAM_INT);
            $sql -> execute();
            $name="";
            $comment="";
            $date="";
            $password="";
        }
    }

//表示機能
//フォームに入力されなかった時
if(empty($_POST["name"]) && empty($_POST["comment"]))
{
    echo "データが入力されていません。<br>";   
}
//入力された時
else if(!empty($_POST["password1"]) && empty ($_POST["edit_n"]) && empty($_POST["delete"]) && empty($_POST["password2"]))
{
    echo $_POST["name"]. $_POST["comment"]. "を受け付けました<br>";
}
//パスワードが入力されなかった時
if ((empty($_POST["password1"]) && empty($_POST["password2"]) && empty($_POST["password3"]))
||((!empty($_POST["name"]) || !empty($_POST["comment"])) && empty($_POST["password1"]))
||(!empty($_POST["delete"]) && empty($_POST["password2"]))
||(!empty($_POST["edit_n"]) && empty($_POST["password3"])))
{
    echo "パスワードが入力されていません。<br>";
}
//入力と編集が同時に行われた時
if((!empty($_POST["name"])) || !empty($_POST["comment"]) && !empty($_POST["password1"]) && !empty($_POST["password3"]))
{
    echo "入力と編集は同時に行えません<br>";
}

//データベースの表示
//テーブルに登録されたデータを取得し、画面に表示。
$sql = 'SELECT * FROM tb1';
$stmt = $pdo->query($sql);//$stmtはPDOStatementオブジェクトを表す。$stmt = $pdo->query($sql);はSQLを実行することを示す。
$results = $stmt->fetchAll();//fetchAllは全ての結果行を含む配列を繰り返すことを意味する。$stmtの中にある配列を取り出して結果に代入する。
foreach ($results as $row)
{
    echo $row["id"].",";
    echo $row["name"].",";
    echo $row["comment"].",";
    echo $row["date"].",";
    echo $row["password"]."<br>";
    echo "<hr>";
}

?>
</body>
</html>