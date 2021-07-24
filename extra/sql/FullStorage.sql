DROP PROCEDURE IF EXISTS FullStorage;

DELIMITER $$
CREATE PROCEDURE FullStorage()
BEGIN
    DECLARE i INT;
    DECLARE j INT;
    DECLARE k INT;
    DECLARE n INT;
    
    -- Tipe A (row 1,2)
    SET n=10;
    SET i=1;
    palletUpdateColA: LOOP
        IF i>20 THEN
            LEAVE palletUpdateColA;
        END IF;

        SET j=1;
        palletUpdateRowA: LOOP
            IF j>7 THEN
                LEAVE palletUpdateRowA;
            END IF;

            SET k=1;
            palletUpdateStkA: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkA;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=1000+n, type_id=1, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-07-13" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=21;
END $$
DELIMITER ;