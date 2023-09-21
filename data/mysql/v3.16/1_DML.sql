DROP TABLE IF EXISTS car;
CREATE TABLE car(
    id int(10) not null PRIMARY KEY AUTO_INCREMENT,
    brand enum('Yamaha', 'Hyundai', 'Toyota', 'Honda', 'Mazda') not null,
    made varchar(50) not null,
    year char(4),
    color char(17) comment 'hex color #952323',
    created_at timestamp DEFAULT now(),
    updated_at timestamp DEFAULT now() ON UPDATE now()
);