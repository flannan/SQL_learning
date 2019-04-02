<?php
/** @noinspection ForgottenDebugOutputInspection */
$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
$mysqli->set_charset('utf8');

//Добавление контакта

$user = 11;
$contactName = 'Иван Иванович Иванов';
$phone = '12345678900';
$mail = 'ivanov@yandex.ru';
$sqlAddContact = <<<SQL
INSERT INTO contacts (user_id, name, phone, email)
VALUES ($user,'$contactName','$phone','$mail');
SQL;
echo $sqlAddContact . "\n";
$query = mysqli_query($mysqli, $sqlAddContact);
var_export($query);
echo PHP_EOL;

//изменение контакта (здесь изменяется только его номер телефона)
$contactID = 11;
$phone = '09876543210';
$sqlModifyContact = <<<SQL
update contacts
SET phone='$phone'
WHERE id = $contactID;
SQL;
echo $sqlModifyContact . "\n";
$query = mysqli_query($mysqli, $sqlModifyContact);
var_export($query);
echo PHP_EOL;

//изменение контакта (здесь изменяется токен Телеграмма)
$contactID = 11;
$channelID = 3; //Телеграмм.
$token = 'flannan';
$sqlModifyContact = <<<SQL
REPLACE INTO channels (contact_id,channel_id,token)
VALUES ($contactID,$channelID,'$token');
SQL;
echo $sqlModifyContact . "\n";
$query = mysqli_query($mysqli, $sqlModifyContact);
var_export($query);
echo PHP_EOL;

//удаление контакта
$contactID = 11;
$sqlDeleteContact = <<<SQL
DELETE FROM contacts
WHERE id=$contactID;
SQL;
echo $sqlDeleteContact . "\n";
$query = mysqli_query($mysqli, $sqlDeleteContact);
var_export($query);
echo PHP_EOL;

//добавление контакта в группу
$group_id = 1;
$contactID = 10;
$sqlAddToGroup = <<<SQL
INSERT INTO group_contents (group_id,contact_id)
VALUES ($group_id,$contactID);
SQL;
echo $sqlAddToGroup . "\n";
var_export(mysqli_query($mysqli, $sqlAddToGroup));
echo PHP_EOL;

//удаление контакта из группы
$group_id = 1;
$contactID = 10;
$sqlAddToGroup = <<<SQL
DELETE FROM group_contents
WHERE (group_id=$group_id AND contact_id=$contactID);
SQL;
echo $sqlAddToGroup . "\n";
var_export(mysqli_query($mysqli, $sqlAddToGroup));
echo PHP_EOL;

//Вывод групп с подсчетом количества контактов.
$userID=1;
$sqlQueryGroups=<<<SQL
SELECT group_names.name, COUNT(group_contents.contact_id) as contacts
FROM group_names
         LEFT JOIN group_contents on group_names.id = group_id
WHERE user_id=$userID
GROUP BY group_names.id;
SQL;
echo $sqlQueryGroups . "\n";
$query = mysqli_query($mysqli, $sqlQueryGroups);
var_export(mysqli_fetch_all($query));
echo PHP_EOL;

//Вывод группы “Часто используемые”, где выводятся топ10 контактов, на которые рассылают сообщения.
$userID=11;
$sqlQueryGroups=<<<SQL
SELECT name, calls_count
FROM contacts
WHERE user_id = $userID
ORDER BY calls_count DESC
LIMIT 10;
SQL;
echo $sqlQueryGroups . PHP_EOL;
$query = mysqli_query($mysqli, $sqlQueryGroups);
if ($query === false) {
    var_export($query);
} else {
    echo mysqli_fetch_all($query);
}
echo PHP_EOL;

//Поиск контактов по ФИО/номеру.
$userID=1;
$name='Ivan%';
$sqlFindContact=<<<SQL
SELECT id, name
FROM contacts
WHERE (user_id = $userID AND name LIKE '$name')
SQL;
echo $sqlFindContact . PHP_EOL;
$query = mysqli_query($mysqli, $sqlFindContact);
if ($query === false) {
    var_export($query);
} else {
    echo mysqli_fetch_all($query);
    //var_export();
}
echo PHP_EOL;

//Выборка контактов по группе.
$userID=1;
$group_id=2;
$sqlFindContact=<<<SQL
SELECT id, name
FROM contacts
LEFT JOIN group_contents ON contacts.id = group_contents.contact_id
WHERE group_contents.group_id = $group_id
SQL;
echo $sqlFindContact . PHP_EOL;
$query = mysqli_query($mysqli, $sqlFindContact);
if ($query === false) {
    var_export($query);
} else {
    echo mysqli_fetch_all($query);
}
echo PHP_EOL;
