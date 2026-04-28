<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; }
    .header { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; padding: 24px; border-radius: 8px 8px 0 0; }
    .body   { background: #f9f9f9; padding: 24px; border-radius: 0 0 8px 8px; }
    .field  { margin-bottom: 16px; }
    .label  { font-weight: bold; color: #764ba2; }
    .message-box { background: #fff; border-left: 4px solid #764ba2; padding: 12px 16px; border-radius: 4px; white-space: pre-wrap; }
  </style>
</head>
<body>
  <div class="header">
    <h2 style="margin:0">TaskForge — jauns kontaktformas ziņojums</h2>
  </div>
  <div class="body">
    <div class="field">
      <div class="label">Vārds</div>
      <div>{{ $senderName }}</div>
    </div>
    <div class="field">
      <div class="label">E-pasts</div>
      <div><a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></div>
    </div>
    <div class="field">
      <div class="label">Ziņojums</div>
      <div class="message-box">{{ $body }}</div>
    </div>
  </div>
</body>
</html>
