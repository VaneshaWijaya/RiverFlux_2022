<h2>FORM KONTAK</h2>
<form method="POST" action="">
	Nama : <input type="text" name="nama"><br>
	Email : <input type="text" name="email"><br>
	Pesan : <textarea></textarea><br>

	<input type="submit" name="submit" value="Kirim">
</form>
<?php 

require_once ("mailer.php");
if(isset($_POST['submit']))
{
	$nama = $_POST['nama'];
	$email = $_POST['email'];

  $to = "$email";
  $subject = "TEST MENGIRIM EMAIL";
  $body = "Halooo $nama, Terima kasih sudah mengirim pesan!!
  ";
  
  send_mail($to, $body, $subject);
}

 ?>