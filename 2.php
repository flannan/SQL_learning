<?php


$sqlcode2 = <<<SQL
drop table IF EXISTS employees;

create table employees
(
    id            smallint     auto_increment
        primary key,
    name          varchar(127) null,
    salary        int          null,
    manager_id    smallint     null
);

INSERT INTO employees (name,salary,manager_id)
VALUES ('Joe',70000,3),
       ('Henry',80000,4),
       ('Sam', 60000, NULL),
       ('Max',90000,NULL);

SELECT A.name
from employees A, employees manager
where A.salary>(select salary from employees where id=A.manager_id);

SELECT e.name
FROM employees e
JOIN employees m
ON e.manager_id = m.id
WHERE e.salary > m.salary;

SQL;
