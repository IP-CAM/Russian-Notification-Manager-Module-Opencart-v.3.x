<?php
// Heading
$_['heading_title']    = 'Оповещение клиентов';

// Text
$_['text_extension']   = 'Модули';
$_['text_success']     = 'Настройки модуля обновлены!';
$_['text_edit']        = 'Редактирование модуля';
$_['text_provider']    = 'Провайдер';
$_['text_selects']     = 'Выберите провайдера';

$_['text_customer']     = 'Уведомления клиенту';
$_['text_admin']     = 'Уведомления админу';

$_['text_admin_phones'] = 'Номера администраторов через запятую';

$_['notify_about'] = 'Оповещать о:';

$_['text_open_whatsapp'] = 'Открыть чат';

$_['text_register'] = 'Регистрация клиента';

$_['text_new_order'] = 'Новый заказ';

$_['replace_default_name'] = 'Иван Иванович';
$_['replace_default_status'] = 'Сделка завершена';
$_['replace_default_comment'] = 'Комментарий';

$_['text_copied'] = 'Скопировано!';

$_['short_codes'] = [
    '{ID}' => 'Номер заказа',
    '{DATE}' => 'Дата',
    '{TIME}' => 'Время',
    '{SUM}' => 'Сумма',
    '{NAME}' => 'Имя клиента',
    '{COMMENT}' => 'Комментарий',
    '{STATUS}' => 'Статус'
];

$_['short_codes_customer'] = [
    '{PASSWORD}' => 'Пароль',
    '{DATE}' => 'Дата',
    '{TIME}' => 'Время',
    '{EMAIL}' => 'Email',
    '{PHONE}' => 'Телефон',
    '{NAME}' => 'Имя клиента'
];

// Entry
$_['entry_status']     = 'Статус';

// Error
$_['error_permission'] = 'У вас нет прав для управления этим модулем!';

$_['provider_form'] = [
    'default' => [
        'login' => 'Логин',
        'password' => 'Пароль'
    ],
    'whatsapp' => [
        'url' => 'API Url',
        'token' => 'Токен',
    ],
    'alphasms' => [
        'name' => 'Имя отправителя/Sign',
        'login' => 'Логин',
        'password' => 'Пароль',
    ],
    'smsaero' => [
        'name' => 'Имя отправителя/Sign',
        'email' => 'Email',
        'api_key' => 'API KEY',
    ],
    'smsc' => [
        'login' => 'Логин',
        'password' => 'Пароль',
    ],
    'smsint' => [
        'name' => 'Имя отправителя/Sign',
        'login' => 'Логин',
        'password' => 'Пароль',
    ],
    'smsru' => [
        'api_id' => 'API ID'
    ]
];