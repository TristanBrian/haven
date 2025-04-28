# Beauty Parlor Management System (BPMS)

A comprehensive management system for beauty parlors with customer and admin interfaces, appointment booking, and M-Pesa payment integration.

## Features
- Customer registration and login
- Appointment booking system
- Service management
- Invoice generation
- M-Pesa payment processing
- Admin dashboard with reporting

## System Requirements
- PHP 7.0+
- MySQL 5.7+
- Node.js 12+
- Apache/Nginx web server
- Composer (for PHP dependencies)

## Installation

### 1. Clone the repository
```bash
git clone [repository_url]
```

### 2. Setup PHP Environment
1. Copy the `bpms` folder to your web server root:
   - XAMPP: `xampp/htdocs`
   - WAMP: `wamp/www` 
   - LAMP: `/var/www/html`

2. Set proper permissions:
```bash
chmod -R 755 bpms/
```

### 3. Database Setup
1. Create a MySQL database named `bpmsdb`
2. Import the SQL file:
```bash
mysql -u username -p bpmsdb < bpms/bpmsdb.sql
```
or use PHPMyAdmin to import `bpms/bpmsdb.sql`

### 4. Node.js Setup (for M-Pesa payments)
1. Navigate to the project directory:
```bash
cd bpms/assets/js
```
2. Install dependencies:
```bash
npm install
```
3. Configure environment variables in `server.mjs`:
   - Set your M-Pesa API credentials
   - Configure callback URLs

### 5. Configuration
Update database credentials in:
- `bpms/includes/dbconnection.php`
- `bpms/admin/includes/dbconnection.php`

## Running the Application

### PHP Application
Start your web server and access:
- Frontend: http://localhost/bpms
- Admin: http://localhost/bpms/admin

### Node.js Server (for payments)
```bash
node server.mjs ///////////////////node bpms/assets/js/server.mjs
```
The payment API will run on port 3000 by default.
# M-Pesa STK Push Payment Integration (server.mjs)

This repository contains server-side code to facilitate M-Pesa STK push payments using Safaricom's Daraja API. This integration allows you to initiate payments from a web application to a customer's M-Pesa account.

## Prerequisites

Before you start, ensure you have the following:

- Node.js installed on your machine (version 14 or later recommended).
- npm or yarn package manager installed.
- Safaricom Daraja API credentials (Consumer Key and Consumer Secret).
- Business Shortcode and Passkey provided by Safaricom.

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/TristanBrian/haven
   cd haven
   ```

2. Install dependencies:

   ```bash
   npm install
   ```

3. Configure API Credentials:

   Replace the placeholders in `server.mjs` with your actual credentials:
   - `consumerKey`: Your Safaricom Daraja API Consumer Key.
   - `consumerSecret`: Your Safaricom Daraja API Consumer Secret.
   - `businessShortCode`: Your Safaricom Business Shortcode.
   - `passkey`: Your Safaricom STK Push Passkey.

4. Start the Server:

   ```bash
   node --experimental-modules server.mjs
   ```

   This will start the server at `http://localhost:3000` by default.

## Usage

### Generating Access Token

Endpoint: `GET /generate-token`

This endpoint fetches the OAuth access token required for authenticating subsequent API requests.

### Initiating STK Push Payment

Endpoint: `POST /stkpush`

This endpoint initiates an STK push payment request to a customer's M-Pesa account.

#### Request Body

```json
{
  "amount": "100",
  "phone": "2547XXXXXXXX",
  "accountReference": "CompanyXLTD",
  "transactionDesc": "Payment for goods"
}
```

- `amount`: The amount to be paid by the customer.
- `phone`: The customer's phone number in international format (e.g., 2547XXXXXXXX).
- `accountReference`: Optional. A reference to identify the transaction (e.g., an order number).
- `transactionDesc`: Optional. Description of the transaction.

#### Response

Upon successful initiation, the response will provide details of the STK push request.

### Error Handling

Errors are handled both at the API level and within the application logic. Responses include appropriate HTTP status codes and error messages.

## Notes

- Ensure your Node.js version supports ECMAScript modules (ESM). Use Node.js version 14 or later with the `--experimental-modules` flag for `.mjs` files.
- Use environment variables or a secure configuration management strategy to store sensitive credentials.
- For production deployment, ensure your server is accessible via HTTPS to comply with M-Pesa API security requirements.

---

This README provides an overview of how to set up and use the M-Pesa STK Push Payment integration provided in the `server.mjs` file. Adjustments may be needed based on your specific application requirements and environment setup.

## Credentials

### Admin Panel
username: newadmin
Password: NewPass@123 (MD5 hashed)

### Sample User
- Email: johndoe@gmail.com
- Password: Test@123

## Troubleshooting

1. **Payment issues**:
   - Verify Node.js server is running
   - Check M-Pesa API credentials
   - Ensure callback URLs are accessible

2. **Database connection errors**:
   - Verify credentials in dbconnection.php files
   - Check MySQL server is running

3. **File permissions**:
   - Ensure all files are readable by web server
   - Check write permissions for uploads/logs

## Support
For additional help, please contact the development team.
