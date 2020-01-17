# Проект Trader

###cron задачи

    * * * * * php /var/www/[dir_name]/yii auto
    * * * * * php /var/www/[dir_name]/yii auto/
    0 * * * * php /var/www/[dir_name]/yii auto/create-hour-tasks
    * * * * * php /var/www/[dir_name]/yii auto/possibility-task

    * * * * * php /var/www/[dir_name]/yii auto/trader2new
    0 * * * * php /var/www/[dir_name]/yii auto/campaign-close-outdated

## Немного о проекте

### Задачи

Трейдер - бот, торгующий на различных крипто-биржах, необходимый для получения выгоды.    
Основные возможности: заработок денег, накрутка обьема торгов, регуляция курса валют определенных монет.  



### Проект состоит из 3х частей:
Сервер Управления  
Сервер Аккаунтов  
Сервер Статистики

### Общая идеология концепта:

На сервере аккаунтов лежат аккаунты подклчюения к биржам, с настроенными входами на различные биржи, функциями создания/отмены ордеров на этих биржах, а также возможность запроса/передачи баланса бирж, данные которых приватны.

Сервер статистики собирает данные о изменениях балансов, курсах валют на различных биржах, и, в удобном виде передает остальным серверам

На сервере управления находится ИИ, регулирующее ордера на биржах, оно запрашивает статистику у сервера стистики, в результате чего производит расчеты, согласно текущим задачам, и обращается к серверу аккаунтов на создание/отмену ордеров.

В результате - мы имеем 3 отдельных части системы, на компоненты которых мы можем привлекать разработчиков, для выполнения небольших задач.

-------

## Основные задачи Сервера Статистики:

Хранить и выводить историю балансов наших аккаунтов-трейдеров

Кроме того нужно учитывать подключение различных биржи, множество монет, все это нужно хранить, чтобы затем выдавать в отчетах в API

### Основные данные, которые будет выдавать сервер:

Данные о текущем состоянии одного аккаунта  
Данные о текущем состоянии аккаунтов сгруппированных по параметрам (биржам, валютам, прочее)  
Данные о общей ценности всех аккаунтов на данный момент


#### а также:

История изменений балансов одного аккаунта  
История изменения балансов аккаунтов сгруппированных по параметрам  
История общей ценности

Это будет выводиться в виде графика и/или таблице на Сервере Управления

Все это будет выводиться в виде API, доступного только для наших серверов

----

Дополнительно будет храниться история курса валют (в минимальном виде)

Некоторые валюты (как, например, наша, или другие партнеры) могут потребовать дополнительной отчетности по обьемам, чекам, прочему

----

Некоторые данные о балансов аккаунтов будут передаваться с Сервера Аккаунтов, некоторые - парситься с блокчейнов, таких как Трон, возможно Эфир, Биткоин, прочее..

----

### Дополнительная информация о хранении общих данных:

Список прокси - храниться и проверяется на сервере статистики, после чего дублируется на другие сервера  
Список аккаунтов - храниться на сервере аккаунтов, обрезанная версия пересылается на другие сервера

## Общие компоненты системы

В системе будут разработаны компоненты, общие для всех частей, а именно:  
- единая авторизация серверов  
- единая структура использования API  
- единые компоненты подключения к Блокчейн-сетям