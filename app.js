const express = require('express');
const bodyParser = require('body-parser');
const { sendStkPush } = require('./bpms/assets/js/stk_push.js');

const app = express();
app.use(bodyParser.json());

// STK Push endpoint
app.post('/stkpush', async (req, res) => {
  try {
    const { amount, phone } = req.body;
    
    if (!amount || !phone) {
      return res.status(400).json({ error: 'Amount and phone number are required' });
    }

    const result = await sendStkPush(amount, phone);
    res.json(result);
  } catch (error) {
    console.error('STK Push API Error:', error);
    res.status(500).json({ 
      error: 'Failed to process STK push',
      details: error.message 
    });
  }
});

// Start server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
