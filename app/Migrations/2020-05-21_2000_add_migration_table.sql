create table if not exists migration (
    filename varchar(128) character set ascii not null primary key,
    created_at timestamp not null
) character set utf8mb4 collate utf8mb4_general_ci;