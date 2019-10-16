			<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;">API checker:</h3>

	<div style="padding:20px;">		
			<form method="POST">
			<p>Server <select name="server">
				<option value="control">control</option>
				<option value="statistics">statistics</option>
				<option value="accounts">accounts</option>
			</select></p>
			<p>Action: <input name="action" value="<?=$_POST['action']?>"></p>
			<p>Data:<br> <textarea name="data"  value="<?=$_POST['data']?>"></textarea></p>
			<input type="submit" value="send" name="go">
			</form>
			
			<hr>
			
			<?=$responce?>
	</div>
	
	