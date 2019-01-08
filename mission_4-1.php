<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>Web掲示板</title>
</head>
<body>

	<?php

		/**********mySQLの接続処理開始**********/

		$dsn='データベース名';//$dsnの式にはスペースを入れない！
		$user='ユーザー名';
		$password='パスワード';
		$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

		//接続に失敗したときの処理
		if(!$pdo){
			die('接続失敗です。'.mysql_error());
		}

		echo'<p>接続に成功しました。</p>';

		/**********mySQLの接続処理完了**********/



		//MySQLに対する処理

		$name=$_POST["name"];//名前を変数に格納
		$comment=$_POST["comment"];//コメントを変数に格納
		$pass=$_POST["pass"];//パスワードを変数に格納

		$delete=$_POST["delete"];//削除対象番号を変数に格納
		$delpass=$_POST["delpass"];//削除パスワードを変数に格納

		$edit_number=$_POST["edit_number"];//送信された編集対象番号を変数に格納
		$edit=$_POST["edit"];//編集対象番号を変数に格納
		$edipass=$_POST["edipass"];//編集パスワードを変数に格納

		$date=date("Y/m/d G:i:s");//投稿日時を変数に格納



		/**********編集内容が送信されたとき**********/

		if(!empty($edit_number)){

			/**********編集開始**********/

			$sql='SELECT*FROM tbtest ORDER BY id ASC';
			$stmt=$pdo->query($sql);
			$results=$stmt->fetchAll();

			foreach($results as $row){
				if($row['id'] == $edit_number){
					$id=$edit_number;
					$name=$_POST["name"];
					$comment=$_POST["comment"];
					$date=date("Y/m/d G:i:s");

					$sql='update tbtest set name=:name,comment=:comment, date=:date where id=:id';
					$stmt=$pdo->prepare($sql);
					$stmt->bindParam(':name',$name,PDO::PARAM_STR);
					$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
					$stmt->bindParam(':date',$date,PDO::PARAM_STR);
					$stmt->bindParam(':id',$id,PDO::PARAM_INT);
					$stmt->execute();
				}
			}

			/**********編集完了**********/

		/**********名前とコメントが送信されたときの処理終了**********/



		}else{

			/**********新規投稿処理開始**********/

			if(!empty($name) && !empty($comment) && !empty($pass)){
				$sql=$pdo->prepare("INSERT INTO tbtest (id,name,comment,pass,date) VALUES (:id,:name,:comment,:pass,:date)");
				$sql->bindParam(':id',$id,PDO::PARAM_INT);
				$sql->bindParam(':name',$name,PDO::PARAM_STR);
				$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
				$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
				$sql->bindParam(':date',$date,PDO::PARAM_STR);
				$sql->execute();

			/**********新規投稿処理完了**********/



			/**********削除対象番号が送信されたとき**********/

			}elseif(!empty($delete)){

				/**********削除処理開始**********/

				$sql='SELECT*FROM tbtest ORDER BY id ASC';
				$stmt=$pdo->query($sql);
				$results=$stmt->fetchAll();

				foreach($results as $row){
					if($row['id'] == $delete){
						if($row['pass'] == $delpass){
							$id=$delete;
							$sql='delete from tbtest where id=:id';
							$stmt=$pdo->prepare($sql);
							$stmt->bindParam(':id',$id,PDO::PARAM_INT);
							$stmt->execute();
						}elseif($row['pass'] != $delpass){
							echo "パスワードが違います。";
						}
					}
				}

				/**********削除処理完了**********/

			/**********削除対象番号が送信されたときの処理終了**********/



			/**********編集対象番号が送信されたとき**********/

			}elseif(!empty($edit)){

				/**********編集準備**********/

				$sql='SELECT*FROM tbtest ORDER BY id ASC';
				$stmt=$pdo->query($sql);
				$results=$stmt->fetchAll();

				foreach($results as $row){
					if($row['id'] == $edit){
						if($row['pass'] == $edipass){
							$data0=$row['id'];
							$data1=$row['name'];
							$data2=$row['comment'];
							$data3=$row['date'];
							$data4=$row['pass'];
						}elseif($row['pass'] != $edipass){
							echo "パスワードが違います。";
						}
					}

				}

				/**********編集準備終了**********/
			}

			/**********編集対象番号が送信されたときの処理終了**********/

			}
	?>



	<form action="mission_4-1.php" method="POST">
		<input type="text" name="name" value="<?php echo $data1;?>" placeholder="名前"><br>
		<input type="text" name="comment" value="<?php echo $data2;?>" placeholder="コメント"><br>
		<input type="text" name="pass" value="" placeholder="パスワード">
		<input type="hidden" name="edit_number" value="<?php echo $data0;?>">
		<input type="submit" value="送信"><br>
	</form>

	<br>

	<form action="mission_4-1.php" method="POST">
		<input type="text" name="delete" value="" placeholder="削除対象番号"><br>
		<input type="text" name="delpass" value="" placeholder="パスワード">
		<input type="submit" value="削除"><br>
	</form>

	<br>

	<form action="mission_4-1.php" method="POST">
		<input type="text" name="edit" value="" placeholder="編集対象番号"><br>
		<input type="text" name="edipass" value="" placeholder="パスワード">
		<input type="submit" value="編集"><br><br>
	</form>

	<?php



		/**********ブラウザに表示させる処理開始**********/

		$sql='SELECT*FROM tbtest ORDER BY id ASC';
		$stmt=$pdo->query($sql);
		$results=$stmt->fetchAll();

		foreach($results as $row){
			echo $row['id'].',';//投稿番号出力
			echo $row['name'].',';//名前出力
			echo $row['comment'].',';//コメント出力
			echo $row['date'].'<br>';//投稿日時出力
		}
		/*$result=$pdo->query($sql);を利用する方法もあるが、変数の値を直接SQL文に埋め込むのはとても危険なのでやめよう！
		詳しくはSQLインジェクションで検索。*/

		/**********ブラウザに表示させる処理完了**********/

	?>
</body>
</html>
