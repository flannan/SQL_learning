<?php

$sqlCodeClear = <<<SQL
drop table IF EXISTS channels;
drop table IF EXISTS channel_names;
drop table IF EXISTS group_contents;
drop table IF EXISTS group_names;
drop table IF EXISTS contacts;
drop table IF EXISTS users;
SQL;


$sqlCodeCreate = <<<SQL
create table IF NOT EXISTS users
(
    id   int unsigned auto_increment primary key,
    name varchar(127) not null
) DEFAULT CHARACTER SET = 'utf8';

create table IF NOT EXISTS contacts
(
    id          int unsigned auto_increment primary key,
    user_id     int unsigned,
    name        varchar(127) null,
    phone       varchar(11)  null,
    email       varchar(127) null,
    calls_count smallint DEFAULT 0,
    foreign key (user_id) references users (id)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET = 'utf8';


create table IF NOT EXISTS group_names
(
    id      int unsigned auto_increment primary key,
    name    varchar(127) not null,
    user_id int unsigned,
    foreign key (user_id) references users (id)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET = 'utf8';

create table IF NOT EXISTS group_contents
(
    group_id   int unsigned,
    contact_id int unsigned,
    foreign key (group_id) references group_names (id)
        ON DELETE CASCADE,
    foreign key (contact_id) references contacts (id)
        ON DELETE CASCADE
);

create table IF NOT EXISTS channel_names
(
    id   tinyint unsigned auto_increment primary key,
    name varchar(127) not null
) DEFAULT CHARACTER SET = 'utf8';

create table IF NOT EXISTS channels
(
    contact_id int unsigned     not null,
    channel_id tinyint unsigned not null,
    token      varchar(127)     NULL,
    constraint PRIMARY KEY (contact_id, channel_id),
    foreign key (contact_id) references contacts (id)
        ON DELETE CASCADE,
    foreign key (channel_id) references channel_names (id)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET = 'utf8';

SQL;

include_once __DIR__ . '/generators.php';
$numberOfUsers = 1000;
$maxContactsPerUser = 10000;
$groupNames = ['все контакты', 'друзья', 'знакомые', 'клиенты'];
$groupProbabilities = [10, 1, 2, 7];
$contactCounter = 0;
$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');
/*
$query = mysqli_multi_query($mysqli, $sqlCodeClear);
if ($query === false) {
    echo "failed clearing database \n";
}
$query = mysqli_multi_query($mysqli, $sqlCodeCreate);
if ($query === false) {
    echo "failed creating tables \n";
}
*/
$channelNames = ['WhatsApp', 'Viber', 'Telegram'];
foreach ($channelNames as $key => $channelName) {
    $sqlAddChannel = <<<SQL
INSERT INTO channel_names (name)
VALUES ('$channelName');
SQL;
    echo $sqlAddChannel . PHP_EOL;
    $query = mysqli_query($mysqli, $sqlAddChannel);
    if ($query === false) {
        echo "failed filling channel names \n";
    }
}


$mysqli->set_charset('utf8');
$mysqli->begin_transaction();
for ($user = 1; $user <= $numberOfUsers; $user++) {
    $username = randomName((string)$user);
    $sqlAddUser = <<<SQL
INSERT INTO users (id, name)
VALUES ($user,'$username');
SQL;
    mysqli_query($mysqli, $sqlAddUser);

    foreach ($groupNames as $groupName) {
        $sqlAddGroup = <<<SQL
INSERT INTO group_names (name,user_id)
VALUES ('$groupName',$user);
SQL;
        $query = mysqli_query($mysqli, $sqlAddGroup);
        if ($query === false) {
            echo "failed adding user group $groupName \n";
        }
        //var_export($query);
    }

    $contactsThisUser = mt_rand(0, $maxContactsPerUser);
    for ($contact = 1; $contact <= $contactsThisUser; $contact++) {
        $contactCounter++;
        $contactName = randomName((string)$contactCounter);
        $mail = 'address' . $contactCounter . '@mail.ru';
        $phone = randomPhoneNumber(11);
        $sqlAddContact = <<<SQL
INSERT INTO contacts (id, user_id, name, phone, email)
VALUES ($contactCounter,$user,'$contactName','$phone','$mail');
SQL;
        mysqli_query($mysqli, $sqlAddContact);

        foreach ($groupNames as $group => $groupName) {
            if (mt_rand(1, 10) <= $groupProbabilities[$group]) {
                $group_id = ($user - 1) * count($groupNames) + $group + 1;
                $sqlAddToGroup = <<<SQL
INSERT INTO group_contents (group_id,contact_id)
VALUES ($group_id,$contactCounter);
SQL;
                mysqli_query($mysqli, $sqlAddToGroup);
            }
        }
    }
    echo "total $contactCounter contacts added \n";
}
$mysqli->commit();
echo "commit successful \n";

//добавляем индекс
$sqlAddIndex=<<<SQL
CREATE INDEX fullName ON contacts(name);
SQL;
mysqli_query($mysqli, $sqlAddIndex);
echo "index based on username added \n";
