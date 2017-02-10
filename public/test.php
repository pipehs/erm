<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<textarea rows="20" cols="200" name="string" maxlength="10000000000"></textarea><br/>
<input type="submit" value="submit">
</form>

<?php

if (isset($_POST['string']))
{
	echo base64_encode($_POST['string']);
}
?>