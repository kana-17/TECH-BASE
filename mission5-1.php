<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<?php
	//【DB接続設定】--------------------------
    $dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	//echo "*/DB接続設定-OK/*";
	
    //【テーブル作成】------------------------
	$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "password char(16)"
	.");";
	$stmt = $pdo->query($sql);
	//echo "*/テーブル作成-OK/*<br>";
	
	//【テーブル削除】------------------------
    //$sql = 'DROP TABLE tbtest';
	//$stmt = $pdo->query($sql);
	//$stmt->execute();
	//echo "テーブル削除-OK";

//--------------------------------------------
    //echo $_POST["mode"];
    //echo $_POST["add_name"];
    //echo $_POST["add_comment"];
    //echo $_POST["add_passwd"];
    //echo $_POST["add_edit_passwd"];
    //echo $_POST["add_edit_num"];
    //echo $_POST["del_num"];
    //echo $_POST["del_passwd"];
    //echo $_POST["edit_num"];
    //echo $_POST["edit_passwd"];
//---------------------------------------------


	//【入力確認】-----------------------------
	//フラグ・データ初期化
	$add_flag = "";
	$delete_flag = "";
	$edit_flag = "";
	$error = "";
	$passwd_error = "";
	$job = "";
	//コメントの追加・編集（入力評価）
	if($_POST["mode"] == "ADD") {
        if(empty($_POST["add_edit_num"])) {  //コメント追加準備
            if(empty($_POST["add_name"])) {
                $error = $error . "*/(ADD)Not existed NAME/*";
            }
            if(empty($_POST["add_comment"])) {
                $error = $error . "*/(ADD)Not existed COMMENT/*";
            }
            if(empty($_POST["add_passwd"])) {
                $error = $error . "*/(ADD)Not existed PASSWORD/*";
            }
            if(empty($error)) {
	            $name = $_POST["add_name"];
                $comment = $_POST["add_comment"]; 
                $date = date("Y年m月d日  H:i:s");
                $password = $_POST["add_passwd"];
                $add_flag = "ON";
            }
	    } else {  //コメント編集準備
            if(empty($_POST["add_name"])) {
                $error = $error . "*/(ADD)Not existed NAME/*";
            }
            if(empty($_POST["add_comment"])) {
                $error = $error . "*/(ADD)Not existed COMMENT/*";
            }
            if(empty($_POST["add_passwd"])) {
                $error = $error . "*/(ADD)Not existed PASSWORD/*";
            }
	        if($_POST["add_passwd"] != $_POST["add_edit_passwd"]) {
	            $error = "*/(EDIT)Invalid PASSWORD/*" . $error ;
	        }
            if(empty($error)) {
                $name = $_POST["add_name"];
                $comment = $_POST["add_comment"]; 
                $date = date("Y年m月d日  H:i:s");
                $password = $_POST["add_passwd"];
                $id = $_POST["add_edit_num"];
                $edit_flag = "ON";
            }
        }
	}
	
	//コメントの削除（入力評価）
	if($_POST["mode"] == "DELETE") {
        if(empty($_POST["del_num"])) {
            $error = $error . "*/(DELETE)Not existed ID#/*";
        }
        if(empty($_POST["del_passwd"])) {
            $error = $error . "*/(DELETE)Not existed PASSWORD/*";
        }
        if(empty($error)) {
            //レコード読み取り
            $id = $_POST["del_num"];
            $del_passwd = $_POST["del_passwd"];
            $exist_id_flag = "";
            $passwd_ok_flag = "";
	        $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	        	//ID#とPASSWORDの確認
	        	if ($row['id'] == $id) {
	        	    $exist_id_flag = "OK";
	        	    if ($row['password'] == $del_passwd) {
	        	        $passwd_ok_flag = "OK";
	        	    }
	        	}
	        }
	        if ($exist_id_flag == "OK" && $passwd_ok_flag == "OK") {
                $delete_flag = "ON";    
	        }
            if(empty($exist_id_flag)) {
                $error = $error . "*/(DELETE)Not found ID#/*";
            }
            if(empty($passwd_ok_flag)) {
                $error = $error . "*/(DELETE)Invalid PASSWORD/*";
            }
        }
	}

	//コメントの編集（入力評価）
	if($_POST["mode"] == "EDIT") {
        if(empty($_POST["edit_num"])) {
            $error = $error . "*/(EDIT)Not existed ID#/*";
        }
        if(empty($_POST["edit_passwd"])) {
            $error = $error . "*/(EDIT)Not existed PASSWORD/*";
        }
        if(empty($error)) {
            //レコード読み取り
            $id = $_POST["edit_num"];
            $edit_passwd = $_POST["edit_passwd"];
            $exist_id_flag = "";
            $passwd_ok_flag = "";
	        $sql = 'SELECT * FROM tbtest';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
	        	//ID#とPASSWORDの確認
	        	if ($row['id'] == $id) {
	        	    $exist_id_flag = "OK";
	        	    if ($row['password'] == $edit_passwd) {
	        	        $passwd_ok_flag = "OK";
	        	        $edit_name = $row['name'];
                        $edit_comment = $row['comment'];
                        $add_edit_num = $_POST["edit_num"];
                        $add_edit_passwd = $_POST["edit_passwd"];
	        	    }
	        	}
	        }
            if(empty($exist_id_flag)) {
                $error = $error . "*/(EDIT)Not found ID#/*";
            }
            if(empty($passwd_ok_flag)) {
                $error = $error . "*/(EDIT)Invalid PASSWORD/*";
            }
        }
	}
    
	//【レコードの挿入】-----------------------
	if($add_flag=="ON") {
	    $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
    	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
    	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
    	$sql -> bindParam(':password', $password, PDO::PARAM_STR);	
    	$sql -> execute();
    	$job = "ADD-OK";
	}
    
	//【レコードを削除】------------------------
	if($delete_flag=="ON") {
        $sql = 'delete from tbtest where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $job = "DELETE-OK";
	}
	
	//【レコードの編集】-----------------------
	if($edit_flag=="ON") {
        $sql = 'UPDATE tbtest SET name=:name,comment=:comment,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    	$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    	//$stmt->bindParam(':password', $password, PDO::PARAM_STR);	
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        //$job = "EDIT-OK" . $id . $name . $comment ;
    }

	?>

    <!--【フォーム作成・表示】--------------------- -->
<body>
    <form method="POST" action="">
         【 投稿フォーム 】<br>
        <input type="hidden" name="mode" 
            value="<?php
                echo "ADD" ;
            ?>" >
        名前：　　　<input type="text" name="add_name" 
            value="<?php
                echo $edit_name ;
            ?>" ><br>
        コメント：　<input type="text" name="add_comment" 
            value="<?php
                echo $edit_comment;
            ?>"><br>
        パスワード：<input type="text" name="add_passwd" >
            <input type="hidden" name="add_edit_passwd"
            value="<?php
                echo $add_edit_passwd;
             ?>">
            <input type="hidden" name="add_edit_num"
            value="<?php
                echo $add_edit_num ;
            ?>"> <br>
        <input type="submit" name="submit" value="送信">
    </form>
    <form method="POST" action="">
         【 削除フォーム 】<br>
        <input type="hidden" name="mode" 
            value="<?php
                echo "DELETE" ;
        ?>" >
        投稿番号：　<input type="text" name="del_num" ><br>
        パスワード：<input type="text" name="del_passwd" ><br>
        <input type="submit" name="submit" value="削除">
    </form>
    <form method="POST" action="">
         【 編集フォーム 】<br>
        <input type="hidden" name="mode" 
            value="<?php
                echo "EDIT" ;
        ?>" >
        投稿番号：　<input type="text" name="edit_num" ><br>
        パスワード：<input type="text" name="edit_passwd" ><br>
        <input type="submit" name="submit" value="編集">
    </form>
</body>

    <?php
    //【ステータス表示】-----------------------------
    echo "ERROR: " . $error . "<br><br>" ;
    //echo "JOB  : " . $job . "<br>" ;
	//【レコード全表示】-----------------------------
	$sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'];
		//echo $row['password'].',<br>';
	echo "<hr>";
	}
    ?>

<html>