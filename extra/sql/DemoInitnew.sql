DROP PROCEDURE IF EXISTS DemoInitNew;

DELIMITER $$
CREATE PROCEDURE DemoInitNew()
BEGIN
    DECLARE i INT;
    DECLARE j INT;
    DECLARE k INT;
    DECLARE n INT;
    
    -- Tipe A (row 1,2)
    SET n=10;
    SET i=1;
    palletUpdateColA: LOOP
        IF i>2 THEN
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
                UPDATE `Pallets` SET po_number=1000+n, type_id=1, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-02" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=21 WHERE id=1 OR id=2;

    -- Tipe B (row 3)
    palletUpdateColB3: LOOP
        IF i>3 THEN
            LEAVE palletUpdateColB3;
        END IF;

        SET j=1;
        palletUpdateRowB3: LOOP
            IF j>7 THEN
                LEAVE palletUpdateRowB3;
            END IF;

            SET k=1;
            palletUpdateStkB3: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkB3;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=2000+n, type_id=2, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-04" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=21 WHERE id=3;

    -- Tipe B (row 4)
    palletUpdateColB4: LOOP
        IF i>4 THEN
            LEAVE palletUpdateColB4;
        END IF;

        SET j=1;
        palletUpdateRowB4: LOOP
            IF j>6 THEN
                LEAVE palletUpdateRowB4;
            END IF;

            SET k=1;
            palletUpdateStkB4: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkB4;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=2000+n, type_id=2, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-03" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=18 WHERE id=4;

    -- Tipe C (row 5)
    palletUpdateColC5: LOOP
        IF i>5 THEN
            LEAVE palletUpdateColC5;
        END IF;

        SET j=1;
        palletUpdateRowC5: LOOP
            IF j>4 THEN
                LEAVE palletUpdateRowC5;
            END IF;

            SET k=1;
            palletUpdateStkC5: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkC5;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=3000+n, type_id=3, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-03" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    SET n = n+1;
    UPDATE `Pallets` SET po_number=3000+n, type_id=3, status_id=4, bag_count=49, row_number=5, column_number=5, stack_number=1, production_date="2021-08-03" WHERE id=n;
    SET n = n+1;
    UPDATE `Pallets` SET po_number=3000+n, type_id=3, status_id=4, bag_count=49, row_number=5, column_number=5, stack_number=2, production_date="2021-08-03" WHERE id=n;

    UPDATE `Rows` SET pallet_count=14 WHERE id=5;

    -- Tipe C (row 6)
    palletUpdateColC6: LOOP
        IF i>6 THEN
            LEAVE palletUpdateColC6;
        END IF;

        SET j=1;
        palletUpdateRowC6: LOOP
            IF j>2 THEN
                LEAVE palletUpdateRowC6;
            END IF;

            SET k=1;
            palletUpdateStkC6: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkC6;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=3000+n, type_id=3, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-04" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    SET n = n+1;
    UPDATE `Pallets` SET po_number=3000+n, type_id=3, status_id=4, bag_count=49, row_number=6, column_number=3, stack_number=1, production_date="2021-08-04" WHERE id=n;

    UPDATE `Rows` SET pallet_count=7 WHERE id=6;

    -- Tipe D (row 7)
    palletUpdateColD7: LOOP
        IF i>7 THEN
            LEAVE palletUpdateColD7;
        END IF;

        SET j=1;
        palletUpdateRowD7: LOOP
            IF j>7 THEN
                LEAVE palletUpdateRowD7;
            END IF;

            SET k=1;
            palletUpdateStkD7: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkD7;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=4000+n, type_id=4, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-03" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=21 WHERE id=7;

    -- -- Tipe D (row 8)
    -- Tipe D (row 7)
    palletUpdateColD8: LOOP
        IF i>8 THEN
            LEAVE palletUpdateColD8;
        END IF;

        SET j=1;
        palletUpdateRowD8: LOOP
            IF j>6 THEN
                LEAVE palletUpdateRowD8;
            END IF;

            SET k=1;
            palletUpdateStkD8: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkD8;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=4000+n, type_id=4, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-04" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=18 WHERE id=8;
    palletUpdateColD8: LOOP
        IF i>8 THEN
            LEAVE palletUpdateColD8;
        END IF;

        SET j=1;
        palletUpdateRowD8: LOOP
            IF j>6 THEN
                LEAVE palletUpdateRowD8;
            END IF;

            SET k=1;
            palletUpdateStkD8: LOOP
                IF k>3 THEN
                    LEAVE palletUpdateStkD8;
                END IF;

                SET n = n+1;
                UPDATE `Pallets` SET po_number=4000+n, type_id=4, status_id=4, bag_count=49, row_number=i, column_number=j, stack_number=k, production_date="2021-08-04" WHERE id=n;
                SET k = k+1;
            END LOOP;

            SET j = j+1;
        END LOOP;

        SET i = i+1;
    END LOOP;

    UPDATE `Rows` SET pallet_count=18 WHERE id=8;
END $$

DELIMITER ;