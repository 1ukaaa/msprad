CREATE TABLE IF NOT EXISTS `users` (
    `idUser` int(11) NOT NULL AUTO_INCREMENT, 
    `mailUser` VARCHAR(255) NOT NULL, 
    `passwordUser` VARCHAR(255) NOT NULL, 
    `ipUser` VARCHAR(255) NOT NULL, 
    `navigatorUser` VARCHAR(255) NOT NULL, 
    `secretUser` VARCHAR(255) NOT NULL, 
    PRIMARY KEY (`idUser`), 
    CONSTRAINT unique_mail UNIQUE (mailUser) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE brutForce ( 
    idBrutForce int(11) NOT NULL AUTO_INCREMENT, 
    idUser int NOT NULL, 
    firstRequest datetime NOT NULL, 
    lastRequest datetime NOT NULL, 
    numberRequest int NOT NULL, 
    blocked boolean, 
    PRIMARY KEY (idBrutForce), 
    FOREIGN KEY (idUser) REFERENCES users (idUser)
);

ALTER TABLE `brutforce` ADD `lastRequest` datetime NOT NULL DEFAULT '1970-01-02' AFTER `firstRequest`;