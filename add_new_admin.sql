-- SQL to add a new admin user with username 'newadmin' and password 'NewPass@123' (MD5 hashed)
INSERT INTO tbladmin (AdminName, UserName, MobileNumber, Email, Password) VALUES
('New Admin', 'newadmin', 1234567890, 'newadmin@example.com', 'd34cc97480f71a28c4cf4e6997d38418');
