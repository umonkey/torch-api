CREATE TABLE IF NOT EXISTS `pages` (
    `id` TEXT NOT NULL,
    `created` INT NOT NULL,
    `updated` INT NOT NULL,
    `text` TEXT NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `users` (
    `id` TEXT NOT NULL,
    `created_at` INT NOT NULL,
    `login_at` INT NOT NULL,
    `email` TEXT NOT NULL,
    `password` TEXT NOT NULL,
    PRIMARY KEY(`id`)
);
