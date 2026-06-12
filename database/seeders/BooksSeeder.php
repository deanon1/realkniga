<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BooksSeeder extends Seeder
{
    public function run()
    {
        // Удаляем существующие книги
        DB::table('books')->delete();
        
        $books = [
            [
                'title' => 'Война и мир',
                'author' => 'Лев Толстой',
                'genre' => 'fiction',
                'price' => 29.99,
                'description' => 'Роман-эпопея, описывающий русское общество в эпоху войн против Наполеона.',
                'year' => 1869,
                'pages' => 1225,
                'isbn' => '978-5-17-070490-1',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Преступление и наказание',
                'author' => 'Федор Достоевский',
                'genre' => 'fiction',
                'price' => 19.99,
                'description' => 'Психологический роман о теории Раскольникова и ее последствиях.',
                'year' => 1866,
                'pages' => 671,
                'isbn' => '978-5-17-080312-3',
                'publisher' => 'Эксмо',
                'language' => 'Русский'
            ],
            [
                'title' => 'Мастер и Маргарита',
                'author' => 'Михаил Булгаков',
                'genre' => 'fiction',
                'price' => 24.99,
                'description' => 'Мистический роман о визите дьявола в сталинскую Москву.',
                'year' => 1967,
                'pages' => 480,
                'isbn' => '978-5-17-058432-1',
                'publisher' => 'Азбука',
                'language' => 'Русский'
            ],
            [
                'title' => 'Евгений Онегин',
                'author' => 'Александр Пушкин',
                'genre' => 'fiction',
                'price' => 14.99,
                'description' => 'Роман в стихах о дуэли, любви и разочаровании.',
                'year' => 1833,
                'pages' => 224,
                'isbn' => '978-5-17-012345-6',
                'publisher' => 'Просвещение',
                'language' => 'Русский'
            ],
            [
                'title' => 'Анна Каренина',
                'author' => 'Лев Толстой',
                'genre' => 'romance',
                'price' => 22.99,
                'description' => 'Трагическая история любви и супружеской неверности.',
                'year' => 1877,
                'pages' => 864,
                'isbn' => '978-5-17-098765-4',
                'publisher' => 'Росмэн',
                'language' => 'Русский'
            ],
            [
                'title' => 'Шерлок Холмс. Полное собрание',
                'author' => 'Артур Конан Дойл',
                'genre' => 'detective',
                'price' => 34.99,
                'description' => 'Все приключения величайшего детектива мира.',
                'year' => 1892,
                'pages' => 1056,
                'isbn' => '978-5-17-034567-8',
                'publisher' => 'Амфора',
                'language' => 'Русский'
            ],
            [
                'title' => 'Убийство в Восточном экспрессе',
                'author' => 'Агата Кристи',
                'genre' => 'detective',
                'price' => 16.99,
                'description' => 'Эркюль Пуаро расследует убийство в поезде.',
                'year' => 1934,
                'pages' => 256,
                'isbn' => '978-5-17-045678-9',
                'publisher' => 'Инфра',
                'language' => 'Русский'
            ],
            [
                'title' => 'Десять негритят',
                'author' => 'Агата Кристи',
                'genre' => 'detective',
                'price' => 18.99,
                'description' => 'Классический детектив об убийствах на острове.',
                'year' => 1939,
                'pages' => 208,
                'isbn' => '978-5-17-056789-0',
                'publisher' => 'Центрполиграф',
                'language' => 'Русский'
            ],
            [
                'title' => '1984',
                'author' => 'Джордж Оруэлл',
                'genre' => 'science',
                'price' => 21.99,
                'description' => 'Антиутопия о тоталитарном обществе и слежке.',
                'year' => 1949,
                'pages' => 328,
                'isbn' => '978-5-17-067890-1',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Дюна',
                'author' => 'Фрэнк Герберт',
                'genre' => 'science',
                'price' => 26.99,
                'description' => 'Эпическая научная фантастика о планете Арракис.',
                'year' => 1965,
                'pages' => 688,
                'isbn' => '978-5-17-078901-2',
                'publisher' => 'Эксмо',
                'language' => 'Русский'
            ],
            [
                'title' => 'Пикник на обочине',
                'author' => 'Братья Стругацкие',
                'genre' => 'science',
                'price' => 17.99,
                'description' => 'Мистическая научная фантастика о Зоне и сталкерах.',
                'year' => 1972,
                'pages' => 288,
                'isbn' => '978-5-17-089012-3',
                'publisher' => 'Терра',
                'language' => 'Русский'
            ],
            [
                'title' => 'Метро 2033',
                'author' => 'Дмитрий Глуховский',
                'genre' => 'science',
                'price' => 23.99,
                'description' => 'Постапокалиптический роман о жизни в московском метро.',
                'year' => 2005,
                'pages' => 416,
                'isbn' => '978-5-17-090123-4',
                'publisher' => 'Попурри',
                'language' => 'Русский'
            ],
            [
                'title' => 'Стивен Джобс',
                'author' => 'Уолтер Айзексон',
                'genre' => 'biography',
                'price' => 32.99,
                'description' => 'Биография основателя Apple и гения технологий.',
                'year' => 2011,
                'pages' => 656,
                'isbn' => '978-5-17-012345-7',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Думай и богатей',
                'author' => 'Наполеон Хилл',
                'genre' => 'psychology',
                'price' => 19.99,
                'description' => 'Классическая книга о достижении успеха.',
                'year' => 1937,
                'pages' => 320,
                'isbn' => '978-5-17-023456-8',
                'publisher' => 'Попурри',
                'language' => 'Русский'
            ],
            [
                'title' => 'Психология влияния',
                'author' => 'Роберт Чалдини',
                'genre' => 'psychology',
                'price' => 25.99,
                'description' => 'Почему люди говорят "да" и как это использовать.',
                'year' => 1984,
                'pages' => 352,
                'isbn' => '978-5-17-034567-9',
                'publisher' => 'Манн',
                'language' => 'Русский'
            ],
            [
                'title' => 'Код да Винчи',
                'author' => 'Дэн Браун',
                'genre' => 'thriller',
                'price' => 20.99,
                'description' => 'Триллер о тайнах Святого Грааля.',
                'year' => 2003,
                'pages' => 489,
                'isbn' => '978-5-17-045678-0',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Молчание ягнят',
                'author' => 'Томас Харрис',
                'genre' => 'thriller',
                'price' => 22.99,
                'description' => 'Психологический триллер о Ганнибале Лектере.',
                'year' => 1988,
                'pages' => 368,
                'isbn' => '978-5-17-056789-1',
                'publisher' => 'Эксмо',
                'language' => 'Русский'
            ],
            [
                'title' => 'Шоугirlen',
                'author' => 'Гillian Flynn',
                'genre' => 'thriller',
                'price' => 24.99,
                'description' => 'Психологический триллер о исчезнувшей жене.',
                'year' => 2012,
                'pages' => 432,
                'isbn' => '978-5-17-067890-2',
                'publisher' => 'Росмэн',
                'language' => 'Русский'
            ],
            [
                'title' => 'История России',
                'author' => 'Василий Ключевский',
                'genre' => 'history',
                'price' => 45.99,
                'description' => 'Фундаментальный труд по истории России.',
                'year' => 1911,
                'pages' => 1024,
                'isbn' => '978-5-17-078901-3',
                'publisher' => 'Мысль',
                'language' => 'Русский'
            ],
            [
                'title' => 'Римская империя',
                'author' => 'Эдвард Гиббон',
                'genre' => 'history',
                'price' => 38.99,
                'description' => 'История падения Римской империи.',
                'year' => 1776,
                'pages' => 1312,
                'isbn' => '978-5-17-089012-4',
                'publisher' => 'Центрполиграф',
                'language' => 'Русский'
            ],
            [
                'title' => 'Вторая мировая война',
                'author' => 'Энтони Бивор',
                'genre' => 'history',
                'price' => 42.99,
                'description' => 'Полная история Второй мировой войны.',
                'year' => 2012,
                'pages' => 880,
                'isbn' => '978-5-17-090123-5',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Алиса в Стране чудес',
                'author' => 'Льюис Кэрролл',
                'genre' => 'fiction',
                'price' => 15.99,
                'description' => 'Волшебная история о девочке Алисе.',
                'year' => 1865,
                'pages' => 192,
                'isbn' => '978-5-17-001234-5',
                'publisher' => 'Азбука',
                'language' => 'Русский'
            ],
            [
                'title' => 'Гарри Поттер и философский камень',
                'author' => 'Джоан Роулинг',
                'genre' => 'fiction',
                'price' => 27.99,
                'description' => 'История о юном волшебнике и его приключениях.',
                'year' => 1997,
                'pages' => 320,
                'isbn' => '978-5-17-002345-6',
                'publisher' => 'Росмэн',
                'language' => 'Русский'
            ],
            [
                'title' => 'Властелин колец',
                'author' => 'Джон Толкин',
                'genre' => 'fiction',
                'price' => 49.99,
                'description' => 'Эпическое фэнтези о Средиземье.',
                'year' => 1954,
                'pages' => 1216,
                'isbn' => '978-5-17-003456-7',
                'publisher' => 'АСТ',
                'language' => 'Русский'
            ],
            [
                'title' => 'Хоббит',
                'author' => 'Джон Толкин',
                'genre' => 'fiction',
                'price' => 21.99,
                'description' => 'Приключения Бильбо Бэггинса.',
                'year' => 1937,
                'pages' => 310,
                'isbn' => '978-5-17-004567-8',
                'publisher' => 'Эксмо',
                'language' => 'Русский'
            ]
        ];
        
        foreach ($books as $book) {
            Book::create($book);
        }
        
        $this->command->info('Добавлено ' . count($books) . ' книг в базу данных!');
    }
}
