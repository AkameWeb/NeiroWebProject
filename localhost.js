const https = require('https');
const fs = require('fs');
const express = require('express');

const app = express();

const options = {
  key: fs.readFileSync('localhost-key.pem'), // укажите путь к ключу
  cert: fs.readFileSync('localhost.pem')     // укажите путь к сертификату
};

https.createServer(options, app).listen(443);