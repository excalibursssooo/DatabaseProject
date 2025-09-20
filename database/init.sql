-- 创建数据库
DROP DATABASE IF EXISTS car_auction;
CREATE DATABASE car_auction;
USE car_auction;

-- 客户表
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(2) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 用户表
CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
customer_id INT NOT NULL,
is_admin TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

CREATE TABLE blockchain (
    block_id INT AUTO_INCREMENT PRIMARY KEY,
    previous_hash VARCHAR(64) NOT NULL,
    hash VARCHAR(64) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data TEXT NOT NULL,
    nonce INT NOT NULL,
    auto_id INT,
    customer_id INT,
    INDEX idx_block_hash (hash),
    INDEX idx_block_auto (auto_id),
    FOREIGN KEY (auto_id) REFERENCES autos(auto_id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL
);

-- 车辆表
CREATE TABLE autos (
    auto_id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(10) NOT NULL,
    year INT NOT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    type VARCHAR(100) NOT NULL,
    mileage INT NOT NULL,
    vin VARCHAR(17) NOT NULL UNIQUE,
    is_available BOOLEAN DEFAULT TRUE,
    added_date DATE NOT NULL
);

-- 投标表
CREATE TABLE bids (
    bid_id INT AUTO_INCREMENT PRIMARY KEY,
    auto_id INT NOT NULL,
    customer_id INT NOT NULL,
    bid_amount DECIMAL(10, 2) NOT NULL,
    bid_date DATETIME NOT NULL,
    is_winner BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (auto_id) REFERENCES autos(auto_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

-- 创建索引
CREATE INDEX idx_autos_make ON autos(make);
CREATE INDEX idx_autos_year ON autos(year);
CREATE INDEX idx_bids_auto ON bids(auto_id);
CREATE INDEX idx_bids_customer ON bids(customer_id);
CREATE INDEX idx_bids_date ON bids(bid_date);



-- 存储过程: MaxBid
CREATE PROCEDURE MaxBid()
BEGIN
    SELECT 
        a.auto_id AS 'Auto ID',
        a.location AS 'Location',
        a.year AS 'Year',
        CONCAT('$', FORMAT(COALESCE(MAX(b.bid_amount), 0), 2)) AS 'Maximum Bid'
    FROM autos a
    LEFT JOIN bids b ON a.auto_id = b.auto_id
    WHERE a.is_available = TRUE
    GROUP BY a.auto_id, a.location, a.year
    ORDER BY a.auto_id;
END //

-- 存储过程: WinnersAndLosers
CREATE PROCEDURE WinnersAndLosers(IN p_auto_id INT)
BEGIN
    DECLARE max_bid DECIMAL(10, 2);
    
    SELECT COALESCE(MAX(bid_amount), 0) INTO max_bid 
    FROM bids 
    WHERE auto_id = p_auto_id;
    
    SELECT 
        b.auto_id AS 'Auto ID',
        c.last_name AS 'Last Name',
        CONCAT('$', FORMAT(b.bid_amount, 2)) AS 'Bid',
        CONCAT('$', FORMAT(max_bid, 2)) AS 'Maximum Bid',
        CASE 
            WHEN b.bid_amount = max_bid AND max_bid > 0 THEN 'Yes' 
            ELSE 'No' 
        END AS 'Won Bid?'
    FROM bids b
    JOIN customers c ON b.customer_id = c.customer_id
    WHERE b.auto_id = p_auto_id
    ORDER BY b.bid_amount DESC;
END //

-- 存储过程: AddCustomer
CREATE PROCEDURE AddCustomer(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_address VARCHAR(255),
    IN p_city VARCHAR(50),
    IN p_state VARCHAR(2),
    IN p_zip_code VARCHAR(10),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(15)
)
BEGIN
    INSERT INTO customers (
        first_name, last_name, address, city, state, zip_code, email, phone
    ) VALUES (
        p_first_name, p_last_name, p_address, p_city, p_state, p_zip_code, p_email, p_phone
    );
END //

-- 存储过程: AddAuto
CREATE PROCEDURE AddAuto(
    IN p_location VARCHAR(10),
    IN p_year INT,
    IN p_make VARCHAR(50),
    IN p_model VARCHAR(50),
    IN p_type VARCHAR(100),
    IN p_mileage INT,
    IN p_vin VARCHAR(17)
)
BEGIN
    INSERT INTO autos (
        location, year, make, model, type, mileage, vin, added_date
    ) VALUES (
        p_location, p_year, p_make, p_model, p_type, p_mileage, p_vin, CURDATE()
    );
END //

-- 存储过程: MaxBid
CREATE PROCEDURE MaxBid()
BEGIN
    SELECT 
        a.auto_id AS 'Auto ID',
        a.location AS 'Location',
        a.year AS 'Year',
        CONCAT('$', FORMAT(COALESCE(MAX(b.bid_amount), 0), 2)) AS 'Maximum Bid',
        a.auction_end_date AS 'Auction End Date',
        CASE 
            WHEN a.auction_end_date < CURDATE() THEN 'Completed'
            ELSE 'Ongoing'
        END AS 'Auction Status'
    FROM autos a
    LEFT JOIN bids b ON a.auto_id = b.auto_id
    WHERE a.is_available = TRUE
    GROUP BY a.auto_id, a.location, a.year, a.auction_end_date
    ORDER BY a.auto_id;
END //

-- 存储过程: WinnersAndLosers
CREATE PROCEDURE WinnersAndLosers(IN p_auto_id INT)
BEGIN
    DECLARE v_auction_end_date DATE;
    DECLARE v_max_bid DECIMAL(10, 2);
    DECLARE v_winner_customer_id INT;
    
    -- 获取拍卖结束日期
    SELECT auction_end_date INTO v_auction_end_date 
    FROM autos WHERE auto_id = p_auto_id;
    
    -- 获取最高投标金额
    SELECT COALESCE(MAX(bid_amount), 0) INTO v_max_bid 
    FROM bids WHERE auto_id = p_auto_id;
    
    -- 如果拍卖已结束，获取胜者ID
    IF v_auction_end_date < CURDATE() THEN
        SELECT customer_id INTO v_winner_customer_id 
        FROM bids 
        WHERE auto_id = p_auto_id AND bid_amount = v_max_bid 
        LIMIT 1;
        
        -- 更新胜者状态
        UPDATE bids SET is_winner = TRUE 
        WHERE auto_id = p_auto_id AND customer_id = v_winner_customer_id;
        
        -- 更新车辆状态为不可用
        UPDATE autos SET is_available = FALSE WHERE auto_id = p_auto_id;
    END IF;
    
    SELECT 
        b.auto_id AS 'Auto ID',
        c.last_name AS 'Last Name',
        CONCAT('$', FORMAT(b.bid_amount, 2)) AS 'Bid',
        CONCAT('$', FORMAT(v_max_bid, 2)) AS 'Maximum Bid',
        CASE 
            WHEN v_auction_end_date < CURDATE() THEN
                CASE 
                    WHEN b.customer_id = v_winner_customer_id THEN 'Winner'
                    ELSE 'Loser'
                END
            ELSE
                CASE 
                    WHEN b.bid_amount = v_max_bid AND v_max_bid > 0 THEN 'Current Highest'
                    ELSE 'Outbid'
                END
        END AS 'Status',
        CASE 
            WHEN v_auction_end_date < CURDATE() THEN 'Auction Completed'
            ELSE CONCAT('Ends: ', DATE_FORMAT(v_auction_end_date, '%Y-%m-%d'))
        END AS 'Auction Info'
    FROM bids b
    JOIN customers c ON b.customer_id = c.customer_id
    WHERE b.auto_id = p_auto_id
    ORDER BY b.bid_amount DESC;
END //


-- 触发器: 投标验证
CREATE TRIGGER before_bid_insert
BEFORE INSERT ON bids
FOR EACH ROW
BEGIN
    DECLARE first_bid_date DATE;
    DECLARE current_max_bid DECIMAL(10, 2);
    DECLARE vehicle_available BOOLEAN;
    
    -- 检查车辆是否可用
    SELECT is_available INTO vehicle_available FROM autos WHERE auto_id = NEW.auto_id;
    IF NOT vehicle_available THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'This vehicle is no longer available for bidding.';
    END IF;
    
    -- 获取该车辆的首次投标日期
    SELECT MIN(DATE(bid_date)) INTO first_bid_date FROM bids WHERE auto_id = NEW.auto_id;
    
    -- 获取当前最高投标
    SELECT COALESCE(MAX(bid_amount), 0) INTO current_max_bid FROM bids WHERE auto_id = NEW.auto_id;
    
    -- 检查投标是否超过一个月
    IF first_bid_date IS NOT NULL AND DATEDIFF(NEW.bid_date, first_bid_date) > 30 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Bid date is overdue. Auction for this vehicle has closed.';
    END IF;
    
    -- 检查投标是否低于当前最高投标
    IF NEW.bid_amount <= current_max_bid THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Bid amount must be higher than the current maximum bid.';
    END IF;
END //

DELIMITER ;

-- 插入示例数据
-- 客户数据
INSERT INTO customers (first_name, last_name, address, city, state, zip_code, email, phone) VALUES
('John', 'Doe', '123 Main St', 'Anytown', 'CA', '12345', 'john.doe@email.com', '555-1234'),
('Jane', 'Smith', '456 Oak Ave', 'Somewhere', 'NY', '67890', 'jane.smith@email.com', '555-5678'),
('Mike', 'Johnson', '789 Pine Rd', 'Otherville', 'TX', '11223', 'mike.johnson@email.com', '555-9012'),
('Sarah', 'Williams', '321 Elm St', 'Newcity', 'FL', '44556', 'sarah.williams@email.com', '555-3456'),
('Tom', 'Brown', '654 Maple Ave', 'Yourtown', 'IL', '77889', 'tom.brown@email.com', '555-7890'),
('Lisa', 'Davis', '987 Cedar Ln', 'Theirtown', 'WA', '99001', 'lisa.davis@email.com', '555-2345'),
('David', 'Miller', '159 Birch Dr', 'Mytown', 'AZ', '22334', 'david.miller@email.com', '555-6789'),
('Amy', 'Wilson', '753 Spruce Ct', 'Hertown', 'GA', '55667', 'amy.wilson@email.com', '555-0123'),
('Chris', 'Moore', '426 Walnut Way', 'Histown', 'NC', '88990', 'chris.moore@email.com', '555-4567'),
('Karen', 'Taylor', '864 Cherry Blvd', 'Yourcity', 'VA', '11223', 'karen.taylor@email.com', '555-8901');

-- 车辆数据
INSERT INTO autos (location, year, make, model, type, mileage, vin, added_date) VALUES
('L234', 2006, 'FORD', 'F150', 'FORD F150 XL 6A ASBTW', 22, '1FTRF12216NB66324', '2023-10-01'),
('C211', 2005, 'FORD', 'FOCUS', 'FORD FOCUS ZX4 4A ASB', 45, '1FAFP34N45W254872', '2023-10-01'),
('L211', 2005, 'FORD', 'F150', 'FORD F150 XL 4X2 6M ASBTW', 49, '1FTRF122XSNB81984', '2023-10-01'),
('B196', 2004, 'FORD', 'F150', 'FORD F150 OFFROAD 4X4 XCAB 8', 60, '1FTPX14504FA15423', '2023-10-01'),
('Z095', 2002, 'FORD', 'EXPLORER', 'FORD EXPLORER EB 4X4 8A ASBW', 82, '1FMDU74W02UB72009', '2023-10-01'),
('D135', 2002, 'FORD', 'EXPLORER', 'FORD EXPLORER XLS 4X4 6A ASB', 110, '1FMZU72E42UA13223', '2023-10-01'),
('F195', 2002, 'FORD', 'F150', 'FORD F150XLT SPORT 4X2 6A A', 81, '1FTRX17282NA98776', '2023-10-01'),
('A123', 2022, 'CHEVROLET', 'MALIBU', 'CHEVROLET MALIBU LT', 15, '1G1ZD5ST5NF123456', '2023-10-01'),
('B234', 2021, 'CHEVROLET', 'EQUINOX', 'CHEVROLET EQUINOX PREMIER', 25, '2GNAXKEV5L6312345', '2023-10-01'),
('C345', 2020, 'CHEVROLET', 'SILVERADO', 'CHEVROLET SILVERADO 1500 LTZ', 35, '3GCUKREC0LG345678', '2023-10-01'),
('O765', 2007, 'TOYOTA', 'CAMRY', 'TOYOTA CAMRY LE', 18, '4T1BF1FK7HU765432', '2023-10-01'),
('C173', 2007, 'HONDA', 'CIVIC', 'HONDA CIVIC EX', 22, '2HGFA16507H173456', '2023-10-01');

-- 投标数据
INSERT INTO bids (auto_id, customer_id, bid_amount, bid_date) VALUES
(1, 1, 12000.00, '2023-10-05 10:30:00'),
(1, 2, 12500.00, '2023-10-06 14:20:00'),
(1, 3, 13000.00, '2023-10-07 11:45:00'),
(2, 4, 8500.00, '2023-10-05 09:15:00'),
(2, 5, 9000.00, '2023-10-06 16:30:00'),
(3, 6, 11000.00, '2023-10-05 13:40:00'),
(3, 7, 11500.00, '2023-10-07 10:20:00'),
(4, 8, 9500.00, '2023-10-06 15:10:00'),
(5, 9, 7500.00, '2023-10-05 12:25:00'),
(6, 10, 6500.00, '2023-10-07 14:50:00');


INSERT INTO users (username, password, customer_id, is_admin) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 0),
('johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0),
('janesmith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 0),
('mikejohnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 0),
('sarahwilliams', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 0),
('tombrown', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, 0),
('lisadavis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, 0),
('davidmiller', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 7, 0),
('amywilson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 8, 0),
('chrismoore', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 9, 0),
('karentaylor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 10, 0);
