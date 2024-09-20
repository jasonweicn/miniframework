<h1>Example: Encryption</h1>
<p>密钥：<?php echo $this->key?></p>
<p>明文：<?php echo $this->plaintext;?></p>
<p>密文：<?php echo $this->ciphertext;?></p>
<p>解密：<?php echo $this->decrypted;?></p>
<p>提示：支持自定义加密算法，默认为 AES-256-GCM 算法。</p>
