// server.mjs

import express from 'express';
import fetch from 'node-fetch';
import cors from 'cors';

const app = express();
const port = process.env.PORT || 3005;

// Serve static files from the current directory
app.use(express.static('.'));


app.use(cors());
app.use(express.json());

const consumerKey = 'qyrgffVu1C1xdKTcT9bJ5mesXBrWqNZDUV6R6dqODWARGcyI'; //  consumer key
const consumerSecret = 'GdAD6NuKPGTteUeQSEhKDuL9SFZRK2azchU8V5bUOFWjw6ADKG9fd1JCN3N8t2hV'; // consumer secret
const auth = Buffer.from(`${consumerKey}:${consumerSecret}`).toString('base64');

// Generating  timestamp in the format required by Safaricom API
function generateTimestamp() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');

  return `${year}${month}${day}${hours}${minutes}${seconds}`;
}

app.post('/stkpush', async (req, res) => {
  try {
    const { amount, phone } = req.body;

    // phone number is in the correct format (e.g., 254712345678)
    const formattedPhone = phone.startsWith('0') ? `254${phone.substring(1)}` : phone;

    const tokenResponse = await fetch('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials', {
      method: 'GET',
      headers: {
        'Authorization': `Basic ${auth}`
      }
    });

    const tokenData = await tokenResponse.json();
    const accessToken = tokenData.access_token;

    const timestamp = generateTimestamp();
    const businessShortCode = '174379'; //  Business Shortcode
    const passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; //  passkey
    const password = Buffer.from(`${businessShortCode}${passkey}${timestamp}`).toString('base64');

    const requestBody = {
      "BusinessShortCode": businessShortCode,
      "Password": password,
      "Timestamp": timestamp,
      "TransactionType": "CustomerPayBillOnline",
      "Amount": amount,
      "PartyA": formattedPhone,
      "PartyB": businessShortCode,
      "PhoneNumber": formattedPhone,
      "CallBackURL": "https://example.com/callback", // Placeholder URL php 
      "AccountReference": "CompanyXLTD",
      "TransactionDesc": "Payment for goods"
    };

    const stkResponse = await fetch('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${accessToken}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestBody)
    });

    const stkData = await stkResponse.json();
    res.json(stkData);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});
