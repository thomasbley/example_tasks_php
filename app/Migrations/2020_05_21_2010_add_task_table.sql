create table if not exists task (
    id bigint(20) unsigned not null auto_increment primary key,
    customer_id binary(16) not null,
    title varchar(255) not null,
    duedate date not null,
    completed tinyint(3) not null,
    index(customer_id, completed)
) character set utf8mb4 collate utf8mb4_general_ci;