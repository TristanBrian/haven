-- Create payments table
CREATE TABLE IF NOT EXISTS tblpayments (
    ID int(11) NOT NULL AUTO_INCREMENT,
    AppointmentID int(11) NOT NULL,
    Amount decimal(10,2) NOT NULL,
    PaymentMethod varchar(50) NOT NULL,
    TransactionID varchar(100) DEFAULT NULL,
    PhoneNumber varchar(20) DEFAULT NULL,
    PaymentStatus varchar(20) NOT NULL DEFAULT 'pending',
    PaymentDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID),
    FOREIGN KEY (AppointmentID) REFERENCES tblbook(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for faster queries
ALTER TABLE tblpayments ADD INDEX (AppointmentID);
ALTER TABLE tblpayments ADD INDEX (TransactionID);
ALTER TABLE tblpayments ADD INDEX (PaymentStatus);
