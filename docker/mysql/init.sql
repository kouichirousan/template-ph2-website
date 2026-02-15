-- 既にデータベースが存在する場合は削除
DROP DATABASE IF EXISTS posse;

-- MySQLのデータベースを作成
CREATE DATABASE posse;

-- 作成したデータベースを選択
USE posse;

-- テーブルの作成
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    content VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    supplement VARCHAR(255)
);

-- データの追加
INSERT INTO questions (content, image, supplement) VALUES
('日本のIT人材が2030年には最大どれくらい不足すると言われているでしょうか？', 'img-quiz01.png', '経済産業省 2019年3月 － IT 人材需給に関する調査'),
('既存業界のビジネスと、先進的なテクノロジーを結びつけて生まれた、新しいビジネスのことをなんと言うでしょう？', 'img-quiz02.png', NULL),
('IoTとは何の略でしょう？', 'img-quiz03.png', NULL);


CREATE TABLE choices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    valid INT
    -- supplement VARCHAR(255)
);

-- データの追加
INSERT INTO choices (question_id, name, valid) VALUES
(1, '28万人', 1),
(1, '79万人', 0),
(1, '183万人', 0),
(2, 'INTEC', 1),
(2, 'bizTEC', 0),
(2, 'XーTECH', 0),
(3, 'IoT', 1),
(3, 'AI', 0),
(3, 'Blockchain', 0);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
    -- supplement VARCHAR(255)
);

INSERT INTO users (name, email, password)VALUES
('honda', 'honda@example.com', '$2y$10$dp9jPey/aBUuKlh27hUPru2IWk7b9q5eJjAddhcxj4adFcurZANGq');