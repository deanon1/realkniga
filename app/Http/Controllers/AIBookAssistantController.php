<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class AIBookAssistantController extends Controller
{
    // Показать страницу AI-ассистента
    public function index()
    {
        return view('ai-assistant.index');
    }
    
    // Обработать запрос пользователя и вернуть рекомендации
    public function recommend(Request $request)
    {
        $userMessage = $request->input('message');

        // Исправляем опечатки в сообщении
        $correctedMessage = $this->correctTypos($userMessage);

        // Анализируем сообщение пользователя
        $recommendations = $this->analyzeAndRecommend($correctedMessage);

        return response()->json([
            'recommendations' => $recommendations,
            'message' => $this->generateResponse($correctedMessage, $recommendations),
            'original_message' => $userMessage,
            'corrected_message' => $correctedMessage !== $userMessage ? $correctedMessage : null
        ]);
    }
    
    // Исправление опечаток
    private function correctTypos($message)
    {
        $messageLower = mb_strtolower($message);

        // Базовые исправления для жанров
        $corrections = [
            'детектев' => 'детектив',
            'дектев' => 'детектив',
            'детективе' => 'детектив',
        ];

        $correctedMessage = $message;

        // Применяем базовые исправления
        foreach ($corrections as $typo => $correct) {
            if (strpos($messageLower, $typo) !== false) {
                $correctedMessage = str_ireplace($typo, $correct, $correctedMessage);
            }
        }

        // Динамически добавляем исправления для авторов из базы данных
        $authors = Book::select('author')->distinct()->pluck('author')->toArray();
        foreach ($authors as $authorName) {
            $nameParts = preg_split('/\s+/', $authorName);
            foreach ($nameParts as $part) {
                if (strlen($part) > 2) {
                    $partLower = strtolower($part);
                    if (strpos($messageLower, $partLower) !== false && strpos(strtolower($correctedMessage), $authorName) === false) {
                        $correctedMessage = str_ireplace($partLower, $authorName, $correctedMessage);
                    }
                }
            }
        }

        // Динамически добавляем исправления для книг из базы данных
        $books = Book::all();
        foreach ($books as $book) {
            $titleLower = strtolower($book->title);
            // Если часть названия книги найдена в сообщении, заменяем на полное название
            if (strlen($book->title) > 5 && strpos($messageLower, substr($titleLower, 0, 10)) !== false) {
                if (strpos(strtolower($correctedMessage), $titleLower) === false) {
                    $correctedMessage = str_ireplace(substr($titleLower, 0, 10), $book->title, $correctedMessage);
                }
            }
        }

        return $correctedMessage;
    }
    
    // Анализ запроса и подбор книг
    private function analyzeAndRecommend($message)
    {
        // Сначала проверяем запрос конкретного автора (без изменения регистра)
        $authorBooks = $this->findBooksByAuthor($message);
        if (!empty($authorBooks)) {
            return [
                'needsClarification' => false,
                'recommendations' => $authorBooks
            ];
        }

        // Проверяем запрос конкретной книги
        $specificBook = $this->findSpecificBook($message);
        if ($specificBook) {
            return [
                'needsClarification' => false,
                'recommendations' => [$specificBook]
            ];
        }

        // Проверяем, нужно ли уточнение
        $clarificationNeeded = $this->needsClarification($message);
        if ($clarificationNeeded) {
            return [
                'needsClarification' => true,
                'clarificationQuestion' => $clarificationNeeded,
                'recommendations' => []
            ];
        }

        $recommendations = [];

        // Получаем все книги для анализа
        $books = Book::all();

        // Ищем упоминания авторов
        foreach ($books as $book) {
            $score = $this->calculateRelevanceScore($book, $message);

            if ($score > 0) {
                $recommendations[] = [
                    'book' => [
                        'id' => $book->id,
                        'title' => $book->title,
                        'author' => $book->author,
                        'price' => $book->price,
                        'cover_image' => $book->cover_image,
                        'description' => $book->description,
                        'is_new' => $book->is_new
                    ],
                    'score' => $score,
                    'reason' => $this->getRecommendationReason($book, $message, $score)
                ];
            }
        }
        
        // Сортируем по релевантности
        usort($recommendations, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Возвращаем топ-3 рекомендации
        return [
            'needsClarification' => false,
            'recommendations' => array_slice($recommendations, 0, 3)
        ];
    }
    
    // Поиск книг по автору
    private function findBooksByAuthor($message)
    {
        $messageLower = mb_strtolower(trim($message));

        // Получаем всех уникальных авторов из базы данных
        $authors = Book::select('author')->distinct()->pluck('author')->toArray();

        foreach ($authors as $authorName) {
            // Разбиваем имя автора на части для поиска
            $nameParts = preg_split('/\s+/', $authorName);

            foreach ($nameParts as $part) {
                $partLower = mb_strtolower(trim($part));
                $translit = $this->transliterate($partLower);

                // Проверяем точное совпадение с частью имени
                if ($messageLower === $partLower || $messageLower === $translit) {
                    $books = Book::where('author', $authorName)
                        ->limit(3)
                        ->get();

                    if ($books->isNotEmpty()) {
                        return $books->map(function($book) {
                            return $this->createBookResult($book, "Вы упомянули автора {$book->author}");
                        })->toArray();
                    }
                }
            }
        }

        return [];
    }

    // Вспомогательный метод для создания результата книги
    private function createBookResult($book, $reason)
    {
        return [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => $book->price,
                'cover_image' => $book->cover_image,
                'description' => $book->description,
                'is_new' => $book->is_new
            ],
            'score' => 100,
            'reason' => $reason
        ];
    }

    // Простая транслитерация для поиска
    private function transliterate($text)
    {
        $map = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd',
            'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i',
            'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
            'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't',
            'У' => 'u', 'Ф' => 'f', 'Х' => 'kh', 'Ц' => 'ts', 'Ч' => 'ch',
            'Ш' => 'sh', 'Щ' => 'shch', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '',
            'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya'
        ];

        return mb_strtolower(strtr($text, $map));
    }
    
    // Поиск конкретной книги
    private function findSpecificBook($message)
    {
        $messageLower = mb_strtolower(trim($message));
        $messageWords = preg_split('/\s+/', $messageLower);

        // Получаем все книги из базы данных
        $books = Book::all();

        foreach ($books as $book) {
            $titleLower = mb_strtolower($book->title);
            $titleWords = preg_split('/\s+/', $titleLower);

            // Проверяем точное совпадение
            if ($messageLower === $titleLower) {
                return $this->createBookResult($book, "Вы ищете конкретную книгу");
            }

            // Проверяем частичное совпадение (только если сообщение достаточно длинное)
            if (strlen($messageLower) >= 5) {
                if (strpos($titleLower, $messageLower) !== false) {
                    return $this->createBookResult($book, "Вы ищете конкретную книгу");
                }
            }

            // Проверяем совпадение по ключевым словам (только для длинных сообщений)
            if (strlen($messageLower) >= 5) {
                $matchedWords = 0;
                foreach ($messageWords as $msgWord) {
                    if (strlen($msgWord) >= 4) {
                        foreach ($titleWords as $titleWord) {
                            if ($msgWord === $titleWord) {
                                $matchedWords++;
                                break;
                            }
                        }
                    }
                }

                // Если совпало более 50% слов
                if ($matchedWords > 0 && $matchedWords >= count($messageWords) * 0.5) {
                    return $this->createBookResult($book, "Возможно, вы ищете эту книгу");
                }
            }
        }

        return null;
    }

    // Проверка, нужно ли уточнение
    private function needsClarification($message)
    {
        $vagueRequests = [
            'что-то интересное',
            'хочу почитать',
            'посоветуй книгу',
            'что почитать',
            'интересная книга',
            'хорошая книга'
        ];

        $messageLower = mb_strtolower($message);
        
        foreach ($vagueRequests as $vagueRequest) {
            if (strpos($messageLower, $vagueRequest) !== false) {
                return "Я могу помочь вам выбрать книгу! Расскажите подробнее:\n\n📚 Какой жанр вас интересует?\n🎭 Детектив, фантастика, роман, классика?\n👥 Каких авторов вы предпочитаете?\n⭐ Может быть, что-то конкретное?";
            }
        }
        
        return null;
    }
    
    // Расчет релевантности
    private function calculateRelevanceScore($book, $message)
    {
        $score = 0;
        $messageLower = mb_strtolower($message);

        // Маппинг русских жанров на английские (как в базе данных)
        $genreMapping = [
            'детектив' => 'detective',
            'фантастика' => 'science',
            'научная фантастика' => 'science',
            'роман' => 'romance',
            'любовный роман' => 'romance',
            'триллер' => 'thriller',
            'ужасы' => 'thriller',
            'классика' => 'fiction',
            'классическая литература' => 'fiction',
            'история' => 'history',
            'исторический' => 'history',
            'биография' => 'biography',
            'автобиография' => 'biography',
            'психология' => 'psychology'
        ];

        // Проверяем жанр книги через маппинг
        $bookGenre = mb_strtolower($book->genre ?? '');
        foreach ($genreMapping as $russianTerm => $englishGenre) {
            if (strpos($messageLower, $russianTerm) !== false) {
                if ($bookGenre === $englishGenre || strpos($bookGenre, $englishGenre) !== false) {
                    $score += 50;
                    break;
                }
            }
        }

        // Также проверяем прямые совпадения с английскими терминами
        $englishKeywords = ['detective', 'science', 'romance', 'thriller', 'fiction', 'history', 'biography', 'psychology'];
        foreach ($englishKeywords as $keyword) {
            if (strpos($messageLower, $keyword) !== false) {
                if (strpos($bookGenre, $keyword) !== false) {
                    $score += 50;
                    break;
                }
            }
        }

        // Настроения
        $moods = [
            'веселое' => ['веселое', 'смешное', 'забавное', 'смешной'],
            'грустное' => ['грустное', 'печальное', 'трагическое', 'грустный'],
            'вдохновляющее' => ['вдохновляющее', 'мотивирующее', 'вдохновляющий'],
            'задумчивое' => ['задумчивое', 'философское', 'философский']
        ];

        foreach ($moods as $mood => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($messageLower, $keyword) !== false) {
                    if (strpos(mb_strtolower($book->title ?? ''), $keyword) !== false ||
                        strpos(mb_strtolower($book->description ?? ''), $keyword) !== false) {
                        $score += 30;
                    }
                }
            }
        }

        return $score;
    }
    
    // Генерируем причину рекомендации
    private function getRecommendationReason($book, $message, $score)
    {
        $reasons = [];
        
        if ($score >= 50) {
            $reasons[] = "Отлично подходит под ваш запрос";
        } elseif ($score >= 30) {
            $reasons[] = "Хорошо соответствует вашим предпочтениям";
        }
        
        if ($book->is_new) {
            $reasons[] = "Новая книга в нашем каталоге";
        }
        
        if (!empty($reasons)) {
            return implode(", ", $reasons);
        }
        
        return "Может вас заинтересовать";
    }
    
    // Генерируем ответное сообщение
    private function generateResponse($message, $recommendations)
    {
        // Если нужно уточнение
        if (isset($recommendations['needsClarification']) && $recommendations['needsClarification']) {
            return $recommendations['clarificationQuestion'];
        }
        
        // Если нет рекомендаций
        if (empty($recommendations['recommendations']) || !isset($recommendations['recommendations'][0]['book'])) {
            return "Извините, я не нашел подходящих книг по вашему запросу. Попробуйте описать более конкретно, какой жанр или автора вы предпочитаете.\n\nНапример:\n📚 'детектив Агаты Кристи'\n🚀 'научная фантастика'\n💕 'любовный роман'\n📖 'классическая литература'";
        }
        
        $count = count($recommendations['recommendations']);
        
        if ($count === 1) {
            return "Я нашел отличную книгу для вас!";
        } elseif ($count === 2) {
            return "Я подобрал две интересные книги для вас.";
        } else {
            return "Я нашел три отличные книги, которые могут вам понравиться.";
        }
    }
}
