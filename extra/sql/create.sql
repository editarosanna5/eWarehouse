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

CREATE TABLE `Rows` (                               -- line penyimpanan
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pallet_count INTEGER NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `Pallets` (                            -- palet
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    po_number INTEGER NOT NULL,
    type_id INTEGER,
    status_id INTEGER DEFAULT 1 NOT NULL,
    bag_count INTEGER DEFAULT 0 NOT NULL,
    row_number INTEGER,
    column_number INTEGER,
    stack_number INTEGER,
    production_date DATE DEFAULT NULL,

    INDEX USING BTREE(row_number),
    INDEX USING BTREE(row_number, column_number, stack_number),
    INDEX USING BTREE(production_date),

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

CREATE TABLE `Groups` (                       -- posisi client (sensor)
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    group_name VARCHAR(255) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE `GroupMembers` (                           -- client (sensor)
    group_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    
    CONSTRAINT PRIMARY KEY (group_id, member_id),
    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `Groups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `ProductionData` (
    group_id INTEGER DEFAULT 1 NOT NULL,
    member_id INTEGER PRIMARY KEY,
    po_number INTEGER NOT NULL,
    type_id INTEGER NOT NULL,
    bag_count INTEGER NOT NULL,
    production_date DATE NOT NULL,

    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `Groups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (group_id, member_id)
        REFERENCES `GroupMembers` (group_id, member_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `OrderStatus` (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_status  VARCHAR(255) NOT NULL UNIQUE,

    INDEX USING BTREE(order_status)
) ENGINE = InnoDB;

CREATE TABLE `OrderData` (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    group_id INTEGER DEFAULT 4 NOT NULL,
    member_id INTEGER NOT NULL,
    do_number INTEGER NOT NULL,
    order_date DATE NOT NULL,
    status_id INTEGER DEFAULT 1 NOT NULL,

    INDEX USING BTREE(member_id),
    INDEX USING BTREE(do_number),

    CONSTRAINT FOREIGN KEY (status_id)
        REFERENCES `OrderStatus` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (group_id, member_id)
        REFERENCES `GroupMembers` (group_id, member_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `OrderDetails` (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER NOT NULL,
    type_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,

    CONSTRAINT FOREIGN KEY (order_id)
        REFERENCES `OrderData` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (type_id)
        REFERENCES `Types` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `DeliveryDetails` (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER NOT NULL,
    pallet_id INTEGER NOT NULL,
    type_id INTEGER NOT NULL,
    bag_count INTEGER DEFAULT 0 NOT NULL,
    production_date DATE NOT NULL,
    picking_line INTEGER NOT NULL,

    CONSTRAINT FOREIGN KEY (order_id)
        REFERENCES `OrderData` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (type_id)
        REFERENCES `Types` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `StorageOptions` (
    group_id INTEGER DEFAULT 2 NOT NULL,
    member_id INTEGER NOT NULL,
    row_id INTEGER NOT NULL,
    pallet_id INTEGER NOT NULL,

    INDEX USING BTREE(member_id),
    INDEX USING BTREE(row_id),
    INDEX USING BTREE(pallet_id),
    
    CONSTRAINT PRIMARY KEY (member_id, row_id),
    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `Groups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (group_id, member_id)
        REFERENCES `GroupMembers` (group_id, member_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (row_id)
        REFERENCES `Rows` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `PickupOptions` (
    id INTEGER NOT NULL,
    pallet_id INTEGER NOT NULL,

    CONSTRAINT PRIMARY KEY (id, pallet_id),
    CONSTRAINT FOREIGN KEY (id)
        REFERENCES `OrderDetails` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (pallet_id)
        REFERENCES `Pallets` (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

CREATE TABLE `LoadingStatus` (
    id INTEGER PRIMARY KEY,
    group_id INTEGER DEFAULT 3 NOT NULL,
    member_id INTEGER NOT NULL,
    available_bag_count INTEGER DEFAULT 0 NOT NULL,
    loaded_bag_count INTEGER DEFAULT 0 NOT NULL,

    INDEX USING BTREE (id),
    INDEX USING BTREE(member_id),

    CONSTRAINT FOREIGN KEY (id)
        REFERENCES `OrderDetails` (id),
    CONSTRAINT FOREIGN KEY (group_id)
        REFERENCES `Groups` (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FOREIGN KEY (group_id, member_id)
        REFERENCES `GroupMembers` (group_id, member_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;

USE eWarehouse;

SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InitTables.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertRow.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertDevice.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/InsertPallet.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/ResetDatabase.sql;
SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/setup.sql;

-- SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/DemoInit.sql;
-- CALL DemoInit();

-- SOURCE C:/xampp/htdocs/eWarehouse/extra/sql/FullStorage.sql;
-- CALL FullStorage();