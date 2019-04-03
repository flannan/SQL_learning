drop table IF EXISTS channels;
drop table IF EXISTS channel_names;
drop table IF EXISTS group_contents;
drop table IF EXISTS group_names;
drop table IF EXISTS contacts;
drop table IF EXISTS users;

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

-- расширяемый список дополнительных каналов связи.
INSERT INTO channel_names (name)
VALUES ('WhatsApp'),
       ('Viber'),
       ('Telegram');

-- Добавление контакта
EXPLAIN
    INSERT INTO contacts (user_id, name, phone, email)
    VALUES (11, 'Иван Иванович Иванов', '12345678900', 'ivanov@yandex.ru');

-- изменение контакта (здесь изменяется только его номер телефона)
EXPLAIN
    update contacts
    SET phone='09876543210'
    WHERE id = 11;

-- изменение контакта (здесь изменяется токен Телеграмма)
EXPLAIN
    REPLACE INTO channels (contact_id, channel_id, token)
    VALUES (11, 3, 'flannan');

-- удаление контакта
EXPLAIN
    DELETE
    FROM contacts
    WHERE id = 11;

-- добавление контакта в группу
EXPLAIN
    INSERT INTO group_contents (group_id, contact_id)
    VALUES (1, 10);

-- удаление контакта из группы
EXPLAIN
    DELETE
    FROM group_contents
    WHERE (group_id = 1 AND contact_id = 10);

-- Вывод групп с подсчетом количества контактов.
EXPLAIN
    SELECT group_names.name, COUNT(group_contents.contact_id) as contacts
    FROM group_names
             LEFT JOIN group_contents on group_names.id = group_id
    WHERE user_id = 1
    GROUP BY group_names.id;

-- Вывод группы “Часто используемые”, где выводятся топ10 контактов, на которые рассылают сообщения.
EXPLAIN
    SELECT name, calls_count
    FROM contacts
    WHERE user_id = 11
    ORDER BY calls_count DESC
    LIMIT 10;

-- Оптимизация этой операции с помощью индекса (результатов не дала)
CREATE INDEX calls_count ON contacts (calls_count);
DROP INDEX calls_count ON contacts;

-- Поиск контактов по ФИО (частичному).
explain
    SELECT id, name
    FROM contacts
    WHERE (user_id = 1 AND name LIKE 'Ivan%');

-- Поиск контактов по ФИО (полному).
explain
    SELECT id, name
    FROM contacts
    WHERE (user_id = 1 AND name LIKE 'Ivan 12');

-- Оптимизация этой операции с помощью индекса
CREATE INDEX fullName ON contacts (name);
DROP INDEX fullName ON contacts;

-- Выборка контактов по группе.
EXPLAIN
    SELECT id, name
    FROM contacts
             LEFT JOIN group_contents ON contacts.id = group_contents.contact_id
    WHERE group_contents.group_id = 2
