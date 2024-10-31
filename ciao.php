<?php
$parse = "HTML";
$api = "331783725:AAHzAxy9glrszSnGI5W6u9wSn_YuBM1O3x4";
$website = "https://api.telegram.org/bot".$api;
$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);
$chatID = $update["message"]["chat"]["id"];
$adminID = 159133513;
$msg = $update["message"]["text"];
$chat = $update["message"]["chat"]["type"];
$nome = $update["message"]["chat"]["first_name"];
$cognome = $update["message"]["chat"]["last_name"];
$username = $update["message"]["chat"]["username"];
$usernames = $update["message"]["chat"]["username"];
$username = "@" . $username;

function sm($chatID,$messaggio,$parse)
{
  $azione = "$GLOBALS[website]/sendChatAction?chat_id=$chatID&action=typing";
  file_get_contents($azione);
  $url = "$GLOBALS[website]/sendMessage?chat_id=$chatID&parse_mode=$parse&text=".urlencode($messaggio);
  file_get_contents($url);
}

$link = mysql_connect('localhost', 'fabiano', 'fabiano');
mysql_select_db("fabiano");

$q = mysql_query("SELECT * FROM test WHERE chat_id='$chatID'");
$u = mysql_fetch_assoc($q);
$admin = $u["admin"];
$ban = $u["ban"];

if($ban == 1){die();}


if($msg == "/start"){
  mysql_query("INSERT INTO test (chat_id, username, nome, ban, admin) VALUES ('$chatID','$username','$nome','0','0')");
  sm($chatID, "ciao");
}

if($chatID != $adminID){
$messaggio = "<a href=\"http://www.t.me/$usernames\">$nome</a> dice:\n\n$msg";
$url = "$GLOBALS[website]/sendMessage?chat_id=$adminID&parse_mode=$parse&text=".urlencode($messaggio);
file_get_contents($url);
}

if(strpos($msg, "/post") !== false){
if($admin == "1"){
  $msgpost = str_replace("/post ","", $msg);
  mysql_query("UPDATE messaggi SET TESTO='$msgpost' WHERE ID='1'");
  $q = mysql_query("SELECT * FROM messaggi WHERE ID = '1' ORDER BY ID DESC");
  $u = mysql_fetch_assoc($q);

  $messaggio = $u["TESTO"];
  mysql_free_result($q);

  $q = mysql_query("SELECT * FROM test WHERE ban = 0");

  $count = "0";
  while ($r = mysql_fetch_assoc($q)) {
  sm($r["chat_id"], $messaggio);
  $count = ++$count;
}
}
sm($chatID, "Messaggio inviato con successo a $count persone!");
die();
}




if($msg == "/utenti"){
  $q = mysql_query("SELECT COUNT(*) as total FROM test");
  $u = mysql_fetch_assoc($q);
  $aq = $u["total"];
  sm($chatID, "<b>Utenti:</b> $aq");
}


if(strpos($msg, "/ban") !== false){
  if($admin == 1){
    $bannato = $msg;
  if(strpos($msg, "/unban") !== false){die();}
  $bannato = str_replace("/ban","", $bannato);
  $bannato = str_replace(" ","", $bannato);
  if($bannato == ""){sm($chatID, "Uso: /ban USERNAME"); die();}
  mysql_query("UPDATE test SET ban='1' WHERE username='$bannato'");
  sm($chatID, "Ho bannato con successo $bannato");
}
}

if(strpos($msg, "/unban") !== false){
  if($admin == 1){
    $bannato = $msg;
  if(strpos($msg, "/ban") !== false){die();}
  $bannato = str_replace("/unban","", $bannato);
  $bannato = str_replace(" ","", $bannato);
  if($bannato == ""){sm($chatID, "Uso: /unban USERNAME"); die();}
  mysql_query("UPDATE test SET ban='0' WHERE username='$bannato'");
  sm($chatID, "Ho sbannato con successo $bannato");
}
}

if($msg == "/admin" && $admin == 1){
  sm($chatID, "Sei un admin");
}
elseif($msg == "/admin"){
  sm($chatID, "Non sei mica un admin!");
}




include("class-http-request.php");

$vuoto = "";

if($update["inline_query"])
{
$inline = $update["inline_query"]["id"];
$msg = $update["inline_query"]["query"];
$userID = $update["inline_query"]["from"]["id"];
$username = $update["inline_query"]["from"]["username"];
$name = $update["inline_query"]["from"]["first_name"];

$json = array(
//prima riga risultati
array(
'type' => 'article',
'id' => 'lelll',
'thumb_url' => 'https://www.brandodev.it/bot/gabboking/img/lel.jpg',
'title' => '( ͡° ͜ʖ ͡°)',
'description' => 'Lel',
'message_text' => "<b>( ͡° ͜ʖ ͡°)</b>",
'parse_mode' => 'HTML'
)
//altre righe eventuali
);



$json = json_encode($json);
$args = array(
'inline_query_id' => $inline,
'results' => $json,
'cache_time' => 5
);
$r = new HttpRequest("post", "https://api.telegram.org/bot$api/answerInlineQuery", $args);
}

?>
