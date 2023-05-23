drop table if exists items;

###

create table items
(
    id         INTEGER constraint items_pk  primary key autoincrement,
    name       TEXT,
    phone      TEXT,
    key        TEXT,
    created_at TEXT,
    updated_at TEXT
);

###

insert into main.items (id, name, phone, key, created_at, updated_at)
values  (1, 'name1', 'phone1', 'key1', '2023-05-21T23:24:09+03:00', '2023-05-22T00:20:16+03:00'),
        (2, 'name2', 'phone2', 'key2', '2023-05-21T23:24:09+03:00', '2023-05-21T23:24:09+03:00'),
        (3, 'name3', 'phone3', 'key3', '2023-05-21T23:27:24+03:00', '2023-05-21T23:27:24+03:00');