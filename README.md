# broadcast
Запуск тригера окремим аудиторіям контактів Вашого проекту

Інколи є задача запустити розсилку окремій частині контактів Вашого боту по результату дій якогось окремого користувача. Наприклад проекти по типу "дошка оголошень". Коли один з користувачів додає оголошення, яке треба надіслати всім іншим користувачам. Робити розсилку вручну не зручно, бо для цього треба постійно бути за робочим місцем в очікувані нових оголошень. Даний скрипт дозволяє автоматично запускати тригер користувачам, "підписаним" на сегмент розсилки.


Інструкція:
1. Завантажити файли на Ваш хостинг
2. Створити (або використати уже створену) базу даних MYSQL
3. Імпортувати в базу даних файл audience.sql (цей файл створить в базі даних таблицю з необхідною структурою)
4. Вказати в файлі config.php API-ключ Вашого проекту Smart Sender та дані доступу до бази даних
5. Додати в потрібних місцях Ваших воронок "Зовнішній запит" на файл subscribe.php

```
Варіанти зовнішніх запитів:

1. Отримання списку сегментів, до яких доданий користувач
  GET https://example.com/broadcast-main/subscribe.php
  ssId => {{ ssId }}
  https://image.mufiksoft.com/scrin/chrome_sTH8ahOBIW.jpg

2. Перевірка підписки користувача на окремий сегмент
  GET https://example.com/broadcast-main/subscribe.php
  ssId => {{ ssId }}
  segment => Назва сегменту
  https://image.mufiksoft.com/scrin/chrome_qiio1zVKZF.jpg

3. Додавання користувача до сегменту
  PUT https://example.com/broadcast-main/subscribe.php
  {
    "ssId":"{{ userId }}",
    "segment":"Назва сегменту"
  }
  https://image.mufiksoft.com/scrin/chrome_ksvkZh8Wgc.jpg

4. Видалення користувача з сегменту
  DELETE https://example.com/broadcast-main/subscribe.php
  ssId => {{ ssId }}
  segment => Назва сегменту
  https://image.mufiksoft.com/scrin/chrome_haXTDdjjbH.jpg

5. Видалення користувача з усіх сегментів
  DELETE https://example.com/broadcast-main/subscribe.php
  ssId => {{ ssId }}
  segment => ALL_SEGMENTS
  https://image.mufiksoft.com/scrin/chrome_qSiqLh3np6.jpg

6. Запуск події всім користувачам окремого сегменту
  POST https://example.com/broadcast-main/subscribe.php
  {
    "trigger":"Назва тригеру",
    "segment":"Назва сегменту"
  }
  https://image.mufiksoft.com/scrin/chrome_KrCQcBnW5X.jpg

  Цей запит підтримує також обмеження кількості користувачів та пропуск частини користувачів.
  Наприклад, якщо сегмент дуже великий, то можна робити розсилку частинами по 100 користувачів з перервою 2-3хв.
  Параметр limit - Кількість користувачів, яким потрібно запустити тригер
  Параметр offset - Кількість користувачів, яких потрібно пропустити
  https://image.mufiksoft.com/scrin/chrome_fIyLg0Tgkj.jpg
  Такий запит пропустить 300 користувачів і надішле наступним 100 користувачам (тобто користувачам 301-400)

```


Шаблон з прикладами "Зовнішніх запитів" https://messenger.smartsender.com/t/NMLwVvwfmVyyKpoMpJVWVT84ZpdKsbFTde1crldi





