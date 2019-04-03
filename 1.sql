create table departments
(
    id   tinyint auto_increment
        primary key,
    name varchar(127) not null
);

INSERT INTO departments (name)
VALUES ('IT'),
       ('Sales');

drop table IF EXISTS employees;

create table employees
(
    id            smallint auto_increment
        primary key,
    name          varchar(127) null,
    salary        int          null,
    department_id tinyint      not null,
    constraint department
        foreign key (department_id) references departments (id)
);


INSERT INTO employees (name, salary, department_id)
VALUES ('Joe', 70000, 1),
       ('Henry', 80000, 2),
       ('Sam', 60000, 2),
       ('Max', 90000, 1);

SELECT departments.name, MAX(employees.salary) as salary
FROM departments
         INNER JOIN employees on departments.id = department_id
GROUP BY departments.id;
