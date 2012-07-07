<?php include("includes/template_top.php"); ?>
<h1>Please Login</h1>
<form action="" method="post">
	<table>
    	<tr>
        	<td style="vertical-align:middle">
            	Username:
            </td>
            <td>
            	<input type="text" name="username" autocomplete="off" onpaste="return false;" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Password:
            </td>
            <td>
            	<input type="password" name="password" autocomplete="off" onpaste="return false;" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">
            	Packing Key:
            </td>
            <td>
            	<input type="password" name="key" autocomplete="off" onpaste="return false;" />
            </td>
		</tr>
    	<tr>
        	<td style="vertical-align:middle">&nbsp;
            	
            </td>
            <td align="center">
            	<input type="submit" value="Authenticate" />
            </td>
		</tr>
	</table>
</form>
<?php include("includes/template_bot.php"); ?>