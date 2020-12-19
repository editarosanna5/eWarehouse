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
    id INTEGER NOT NULL,
    group_id INTEGER NOT NULL,
    
    CONSTRAINT PRIMARY KEY (id, group_id),
    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `DeviceGroups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `StorageOptions` (
    device_number INTEGER NOT NULL,
    row_id INTEGER NOT NULL,
    pallet_id INTEGER NOT NULL,

    INDEX USING BTREE(device_number),
    INDEX USING BTREE(row_id),
    INDEX USING BTREE(pallet_id),
    
    CONSTRAINT PRIMARY KEY (device_number,row_id),
    CONSTRAINT FOREIGN KEY (device_number)
        REFERENCES `Devices` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (row_id)
        REFERENCES `Rows` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `PickupOptions` (
    device_number INTEGER NOT NULL,
    pallet_id INTEGER NOT NULL,

    INDEX USING BTREE(device_number),
    INDEX USING BTREE(pallet_id),

    CONSTRAINT PRIMARY KEY (device_number,pallet_id),
    CONSTRAINT FOREIGN KEY (device_number)
        REFERENCES `Devices` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `PickupStatus` (
    device_number INTEGER NOT NULL,
    required_pallet_count INTEGER NOT NULL,

    INDEX USING BTREE(device_number),
    INDEX USING BTREE(required_pallet_count),

    CONSTRAINT PRIMARY KEY (device_number),
    CONSTRAINT FOREIGN KEY (device_number)
        REFERENCES `Devices` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `LoadingStatus` (
    device_number INTEGER NOT NULL,
    required_bag_count INTEGER NOT NULL,

    INDEX USING BTREE(device_number),
    INDEX USING BTREE(required_bag_count),

    CONSTRAINT PRIMARY KEY (device_number),
    CONSTRAINT FOREIGN KEY (device_number)
        REFERENCES `Devices` (id)
        ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE = InnoDB;

SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InitDatabase.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InitTables.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertRow.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertDevice.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertPallet.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/ResetDatabase.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/setup.sql;