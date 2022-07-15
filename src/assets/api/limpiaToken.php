<?php

$token = json_decode(file_get_contents("../token.json"));
/*
{"access_token":"ya29.A0AVA9y1uTtyx3guoZszWw8jVN4mA2_j4uD7Ja5CUiU7PTcejcPWAzQyQOA-0n8NQaG72S9a8wW_d8GNhWXEQI-7_j_9EjvM0H6QxP3dDupb26hmK5B7MYvf-FdWii_vLQUwoa29JhGXyv3z1alQT366o9Yl3rYUNnWUtBVEFTQVRBU0ZRRTY$0ZRRTY1ZHI4bUJ3MDd5MEF0dnVNQURmUDZKWHRUZw0163",
"expires_in":3599,
"refresh_token":"1\/\/0dwMhSguMofdACgYIARAAGA0SNwF-L9IrcZDDXkV8c3yZzdwj1-KFUVGSyOT13fp58Hk97tuWidJGTQ2h9DcJ8K2-_psPAwdA5WY",
"scope":"https:\/\/mail.google.com\/",
"token_type":"Bearer",
"created":1657839268}
*/
if ((time() - $token["created"]) > $token["expires_in"]) {
}
