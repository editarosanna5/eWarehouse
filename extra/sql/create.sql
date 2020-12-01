-- Pembuatan Database
CREATE DATABASE eWarehouse
    DEFAULT CHARACTER SET utf8;

-- Pindah ke Database eWarehouse
USE eWarehouse;

-- Pembuatan Tabel
CREATE TABLE `Types` (                              -- jenis komoditas
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(255) NOT NULL UNIQUE,

    INDEX USING BTREE (type_name)
) ENGINE = InnoDB;

CREATE TABLE `PalletStatus` (                       -- status palet
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pallet_status VARCHAR(255) NOT NULL UNIQUE,

    INDEX USING BTREE(pallet_status)
) ENGINE = InnoDB;

CREATE TABLE `BagStatus` (                          -- status karung
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    bag_status VARCHAR(255) NOT NULL UNIQUE,

    INDEX USING BTREE(bag_status)
) ENGINE = InnoDB;

CREATE TABLE `Rows` (                               -- line penyimpanan
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pallet_count INTEGER NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `Pallets` (                            -- palet
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type_id INTEGER,
    status_id INTEGER DEFAULT 2 NOT NULL,
    bag_count INTEGER DEFAULT 0 NOT NULL,
    row_number INTEGER,
    column_number INTEGER,
    stack_number INTEGER,
    oldest_bag_timestamp DATETIME NULL DEFAULT NULL,

    INDEX USING BTREE(row_number),
    INDEX USING BTREE(row_number, column_number, stack_number),
    INDEX USING BTREE(oldest_bag_timestamp),

    CONSTRAINT FOREIGN KEY (type_id)
        REFERENCES `Types` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (status_id)
        REFERENCES `PalletStatus` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (row_number)
        REFERENCES `Rows` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `Bags` (                               -- karung
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type_id INTEGER NOT NULL,
    pallet_id INTEGER,
    status_id INTEGER DEFAULT 1,
    po_number VARCHAR(255) NOT NULL UNIQUE,
    production_timestamp DATETIME NOT NULL,

    INDEX USING BTREE(po_number),
    INDEX USING BTREE(production_timestamp),

    CONSTRAINT FOREIGN KEY (type_id)
        REFERENCES `Types` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (status_id)
        REFERENCES `BagStatus` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `DeviceGroups` (                       -- posisi client (sensor)
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    group_name VARCHAR(255) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE `Devices` (                            -- client (sensor)
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    group_id INTEGER NOT NULL,

    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `DeviceGroups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InitDatabase.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InitTables.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InsertRow.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InsertDevice.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InsertPallet.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/InsertBags.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/ResetDatabase.sql;
SOURCE D:/Kuliah/TA 1/System/Database/Design/sql/new/setup.sql;