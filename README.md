### 加载本扩展
在 `config/app.php` 的 `providers` 部分加入
```
Imvkmark\L5Sms\L5SmsServiceProvider::class
```

### 生成配置
- 配置config
如果是需要强制生成配置, 在后边加入 `--force` 选项
```
php artisan vendor:publish --tag=sour-lemon
```

### 将以及支持的类型填入 `l5-sms.php`
