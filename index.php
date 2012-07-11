<?php

require_once "betable-oauth-php-sdk.php";

$betable = new Betable(
    "YOUR_CLIENT_ID",
    "YOUR_CLIENT_SECRET",
    "YOUR_REDIRECT_URI"
);
$access_token = $betable->token();
if (!$access_token) {
    $betable->authorize();
}

?><!DOCTYPE html>
<meta charset="utf8">
<body>
<pre>
<?php

print_r($access_token);
print_r($betable->account());
print_r($betable->wallet());

?>
</pre>
</body>
