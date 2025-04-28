-- Add BookingFee and Duration columns to tblservices
ALTER TABLE tblservices
ADD COLUMN BookingFee INT DEFAULT 0 AFTER Cost,
ADD COLUMN Duration INT DEFAULT 30 COMMENT 'Duration in minutes' AFTER BookingFee;
