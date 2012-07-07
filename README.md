PassMan
=======

A secure web based password manager using PHP &amp; MySQL

## Installation instructions
1. upload files to web directory.
2. modify /includes/classes/config.php to represent correct MySQL access information.
3. go to the index.php file of PassMan on your web server and follow setup.

## Security Information
There are two types of encryption methods used with PassMan.
* **Type 1 - Decryptable** This method encrypts the string using SHA1 using the Blowfish cipher and Mcrypt CBC cipher mode. These string are stored in the database but due to HTML rendering applications like PHPMyAdmin can not properly interpret the encrypted string. Also, exporting as .SQL wont properly export it either becuase of how it is rendered. The only way to decrypt is to connect directly to the MySQL database and pass the value into the decrypt function. Also, the salt (the packing key) must be known.

* **Type 2 - Non-Decryptable** This method encrypts the string using SHA1 and base64 encoding. Because of this method the end reslult is not decryptable and the encryption is done with a slat that is not stored.