-- Write your models here

CREATE TABLE IF NOT EXISTS CUSTOMER (
    cusId      VARCHAR(255)  NOT NULL PRIMARY KEY,
    firstName  VARCHAR(25)   NOT NULL,
    lastName   VARCHAR(25)   NOT NULL,
    phoneNum   VARCHAR(15)   NOT NULL,
    blockTransfer CHAR(1)    NOT NULL DEFAULT '0',
    transferDisplay TEXT,
    email      VARCHAR(50)   NOT NULL UNIQUE,
    zipCode    VARCHAR(5)    NOT NULL,
    gender     ENUM('M','F') NOT NULL,
    occupation VARCHAR(50)   NOT NULL,
    address    VARCHAR(100)  NOT NULL,
    state      VARCHAR(15)   NOT NULL,
    city       VARCHAR(15)   NOT NULL,
    country    VARCHAR(25)   NOT NULL,
    dob        DATE          NOT NULL,
    picName    VARCHAR(255)  NOT NULL,
    password   VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ACCESS_LOGS(
    logId          BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cusId          VARCHAR(255) NOT NULL,
    ip             VARCHAR(255) NOT NULL,
    activity       ENUM('login','logout') NOT NULL,
    dateOfActivity TIMESTAMP NOT NULL DEFAULT NOW(),

    FOREIGN KEY(cusId) REFERENCES CUSTOMER(cusId)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ACCOUNT(
    accId       VARCHAR(255) NOT NULL,
    userId      VARCHAR(255) NOT NULL,
    accNumber   VARCHAR(255) NOT NULL UNIQUE,
    pin         VARCHAR(10)  NOT NULL UNIQUE,
    accType     ENUM('PERSONAL','BUSINESS')    NOT NULL,
    accTypeType ENUM('SAVINGS','CURRENT')      NOT NULL,
    accStatus   ENUM('active','notactive')     NOT NULL DEFAULT 'active',
    balance     DECIMAL(12,2)                  NOT NULL DEFAULT 0.0,
    accCurrency ENUM('POUNDS','EURO','DOLLAR','YUAN') NOT NULL,
    
    PRIMARY KEY(accId,userId),
    FOREIGN KEY(userId) REFERENCES CUSTOMER(cusId)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS TRANSFERS(
    transfId       VARCHAR(255)  NOT NULL PRIMARY KEY,
    accId          VARCHAR(255)  NOT NULL,
    bankName       VARCHAR(100)  NOT NULL,
    bankAddress    TINYTEXT      NOT NULL,
    accountName    VARCHAR(255)  NOT NULL,
    accountNumber  VARCHAR(255)  NOT NULL,
    routingNumber  VARCHAR(255) NOT NULL,
    dateTransfered TIMESTAMP     NOT NULL DEFAULT NOW(),
    amount         DECIMAL(12,2) NOT NULL,
    description    VARCHAR(255)  NOT NULL,

    FOREIGN KEY(accId) REFERENCES ACCOUNT(accId)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS TRANSACTION_HISTORY(
    tranHist    VARCHAR(255) NOT NULL PRIMARY KEY,
    accId       VARCHAR(255) NOT NULL,
    tranType    ENUM('CREDIT','DEBIT') NOT NULL,
    amount      DECIMAL(12,2) NOT NULL,
    tranDescription TEXT,
    dateOfTran  TIMESTAMP     NOT NULL DEFAULT NOW(),

    FOREIGN KEY(accId) REFERENCES ACCOUNT(accId)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);


CREATE TABLE IF NOT EXISTS ADMIN(
    username    VARCHAR(255) NOT NULL PRIMARY KEY,
    password    VARCHAR(255) NOT NULL
);

INSERT INTO ADMIN(username,password)
VALUES ('admin_bank','$2y$10$FCMRYNC5nslf9tp6NBVuK.MwRZhMcJgp.BKL4adM9DHnanNYjJyOe');


-- 'admin_bank'.
-- 'bank10101'