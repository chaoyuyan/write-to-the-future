<form method="post" onsubmit="encryptContent()">
  <label for="content">交换信息：</label><br>
  <textarea id="content" name="content" rows="8"></textarea><br>
  
  <label for="encryptionKey">设置密码：</label>
  <input type="text" id="encryptionKey" name="encryptionKey"><br>
  
  <input type="submit" name="submit" value="OK">
  </form>
<script>
  function encryptContent() {
    // Get the user input from the textarea
    var content = document.getElementById("content").value;
    
    // Get the user input for the encryption key
    var key = document.getElementById("encryptionKey").value;
    
    // Encrypt the content using AES encryption with the user-provided key
    var encryptedContent = CryptoJS.AES.encrypt(content, key);
    
    // Convert the encrypted content to a string
    var encryptedContentString = encryptedContent.toString();
    
    // Set the encrypted content as the value of the textarea
    document.getElementById("content").value = encryptedContentString;
	document.getElementById("encryptionKey").remove();//密码不会传给服务器

  }
</script>
