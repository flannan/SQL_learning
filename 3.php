<?php

$sqlCodeClear = <<<SQL
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
);

create table IF NOT EXISTS contacts
(
    id            int  unsigned   auto_increment  primary key,
    db_id         int unsigned,
    name          varchar(127) null,
    phone         varchar(11)  null,
    email         varchar(127)  null,
    WhatsApp      boolean  DEFAULT false,
    Viber         boolean  DEFAULT false,
    Telegram      boolean  DEFAULT false,
    calls_count   smallint DEFAULT 0,
    foreign key (db_id) references contactsDB(id)
        ON DELETE CASCADE
);


create table IF NOT EXISTS group_names
(
    id   int unsigned auto_increment primary key,
    name varchar(127) not null,
    user_id int unsigned,
    foreign key (user_id) references users(id)
        ON DELETE CASCADE
);

create table IF NOT EXISTS group_contents
(
    group_id int unsigned,
    contact_id int unsigned,
    foreign key (group_id) references group_names(id)
        ON DELETE CASCADE,
    foreign key (contact_id) references contacts(id)
        ON DELETE CASCADE
);

SQL;

include_once __DIR__ . '/generateNames.php';
$numberOfUsers = 10;
$maxContactsPerUser = 10;
$contactCounter = 0;
$mysqli = new mysqli('localhost', 'stud08', 'stud08', 'test');

for ($user = 1; $user <= $numberOfUsers; $user++) {
    $username = randomName((string)$user);
    $sqlAddUser = <<<SQL
INSERT INTO users (id, name)
VALUES ($user,'$username');

INSERT INTO group_names (name,user_id)
VALUES ('друзья',$user);

SQL;

    mysqli_query($mysqli, $sqlAddUser);
    $contactsThisUser = mt_rand(0, $maxContactsPerUser);
    for ($contact = 1; $contact <= $numberOfUsers; $contact++) {
        $contactCounter++;
        $contactName = randomName((string)$contact);
        $mail = 'address' . (string)$contact . '@mail.ru';
        $phone = randomPhoneNumber(11);
        $sqlAddContact = <<<SQL
INSERT INTO contacts (id, db_id, name, phone, email)
VALUES ($contactCounter,$user,'$contactName','$phone','$mail');
SQL;
        mysqli_query($mysqli, $sqlAddContact);
        if (mt_rand(1, 10) < 3) {
            $sqlAddFriendsGroup = <<<SQL
INSERT INTO group_contents (group_id,contact_id)
VALUES ($user,$contactCounter);
SQL;
            mysqli_query($mysqli, $sqlAddFriendsGroup);
        }
    }
}
