<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}

// Получение данных из POST запроса
$data = json_decode(file_get_contents('php://input'), true);
$query = $data['query'] ?? '';

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Пустой запрос']);
    exit;
}

// Ваш API ключ OpenAI
$apiKey = 'your-openai-api-key-here'; // ЗАМЕНИТЕ на свой реальный ключ

try {
    // Вызываем OpenAI API для анализа запроса
    $analysis = analyzeQueryWithOpenAI($query, $apiKey);
    
    // Ищем музыку на основе анализа
    $results = searchMusicBasedOnAnalysis($analysis);
    
    echo json_encode([
        'success' => true,
        'results' => $results,
        'count' => count($results),
        'analysis' => $analysis // Для отладки
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Ошибка при обработке запроса: ' . $e->getMessage()
    ]);
}

// Функция анализа запроса с помощью OpenAI
function analyzeQueryWithOpenAI($query, $apiKey) {
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $prompt = "Пользователь ищет музыку по запросу: \"$query\". 
    Проанализируй запрос и определи:
    1. Основные ключевые слова для поиска
    2. Жанр музыки (если указан)
    3. Настроение/эмоции (грустная, веселая, энергичная и т.д.)
    4. Примерные годы/эпоху (если есть указания)
    5. Возможных исполнителей или похожие треки
    
    Верни ответ в формате JSON:
    {
        \"keywords\": [\"keyword1\", \"keyword2\", ...],
        \"genre\": \"жанр или null\",
        \"mood\": \"настроение или null\", 
        \"era\": \"эпоха или null\",
        \"similar_artists\": [\"artist1\", \"artist2\", ...],
        \"search_query\": \"оптимизированный запрос для поиска\"
    }";

    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Ты помощник для поиска музыки. Анализируй запросы пользователей и возвращай структурированные данные для поиска.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'max_tokens' => 500,
        'temperature' => 0.3
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new Exception('OpenAI API error: ' . curl_error($ch));
    }
    
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('OpenAI API returned error: ' . $httpCode);
    }

    $responseData = json_decode($response, true);
    
    if (!isset($responseData['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response from OpenAI API');
    }

    $analysis = json_decode($responseData['choices'][0]['message']['content'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Если OpenAI вернул не JSON, попробуем извлечь данные из текста
        $analysis = extractAnalysisFromText($responseData['choices'][0]['message']['content']);
    }

    return $analysis;
}

// Функция извлечения анализа из текстового ответа
function extractAnalysisFromText($text) {
    // Простая эвристика для извлечения данных
    $analysis = [
        'keywords' => [],
        'genre' => null,
        'mood' => null,
        'era' => null,
        'similar_artists' => [],
        'search_query' => ''
    ];

    // Извлекаем ключевые слова
    if (preg_match('/keywords?[:\s]*\[([^\]]+)\]/i', $text, $matches)) {
        $keywords = array_map('trim', explode(',', $matches[1]));
        $analysis['keywords'] = array_filter($keywords);
    }

    // Извлекаем жанр
    if (preg_match('/genre[:\s]*["\']?([^"\',\n]+)/i', $text, $matches)) {
        $analysis['genre'] = trim($matches[1]);
    }

    // Извлекаем настроение
    if (preg_match('/mood[:\s]*["\']?([^"\',\n]+)/i', $text, $matches)) {
        $analysis['mood'] = trim($matches[1]);
    }

    // Извлекаем эпоху
    if (preg_match('/era[:\s]*["\']?([^"\',\n]+)/i', $text, $matches)) {
        $analysis['era'] = trim($matches[1]);
    }

    return $analysis;
}

// Функция поиска музыки на основе анализа
function searchMusicBasedOnAnalysis($analysis) {
    // Здесь интегрируем с реальной музыкальной базой или API
    // Пока используем демо-данные с улучшенным поиском
    
    $mockData = getMockMusicData();
    $results = [];
    
    foreach ($mockData as $track) {
        $relevance = calculateAdvancedRelevance($track, $analysis);
        if ($relevance > 0) {
            $track['relevance'] = $relevance;
            $track['match_reason'] = getMatchReason($track, $analysis);
            $results[] = $track;
        }
    }
    
    // Сортируем по релевантности
    usort($results, function($a, $b) {
        return $b['relevance'] - $a['relevance'];
    });
    
    return array_slice($results, 0, 10);
}

// Улучшенный расчет релевантности
function calculateAdvancedRelevance($track, $analysis) {
    $relevance = 0;
    $textToSearch = mb_strtolower($track['title'] . ' ' . $track['artist'] . ' ' . $track['genre'] . ' ' . $track['description'] . ' ' . $track['tags']);
    
    // Поиск по ключевым словам
    foreach ($analysis['keywords'] as $keyword) {
        $keyword = mb_strtolower(trim($keyword));
        if (strpos($textToSearch, $keyword) !== false) {
            $relevance += 15;
            
            // Бонусы за различные типы совпадений
            if (strpos(mb_strtolower($track['title']), $keyword) !== false) $relevance += 25;
            if (strpos(mb_strtolower($track['artist']), $keyword) !== false) $relevance += 20;
            if (strpos(mb_strtolower($track['genre']), $keyword) !== false) $relevance += 18;
        }
    }
    
    // Совпадение по жанру
    if ($analysis['genre'] && stripos($track['genre'], $analysis['genre']) !== false) {
        $relevance += 30;
    }
    
    // Совпадение по настроению
    if ($analysis['mood'] && stripos($track['mood'] ?? '', $analysis['mood']) !== false) {
        $relevance += 25;
    }
    
    // Совпадение по эпохе
    if ($analysis['era'] && stripos($track['era'] ?? '', $analysis['era']) !== false) {
        $relevance += 20;
    }
    
    return $relevance;
}

// Причина совпадения (для отображения пользователю)
function getMatchReason($track, $analysis) {
    $reasons = [];
    
    foreach ($analysis['keywords'] as $keyword) {
        $keyword = mb_strtolower(trim($keyword));
        if (stripos($track['title'], $keyword) !== false) {
            $reasons[] = "совпадение в названии: \"$keyword\"";
        }
        if (stripos($track['artist'], $keyword) !== false) {
            $reasons[] = "исполнитель: \"$keyword\"";
        }
    }
    
    if ($analysis['genre'] && stripos($track['genre'], $analysis['genre']) !== false) {
        $reasons[] = "жанр: " . $analysis['genre'];
    }
    
    return implode(', ', array_slice($reasons, 0, 2));
}

// Обновленная демо-база с дополнительными метаданными
function getMockMusicData() {
    return [
        [
            'id' => 1,
            'title' => 'Bohemian Rhapsody',
            'artist' => 'Queen',
            'genre' => 'рок, опера, прогрессив-рок',
            'mood' => 'эпичная, драматичная, эмоциональная',
            'era' => '1970s',
            'tags' => 'классика рока, опера, фредди меркьюри',
            'description' => 'Легендарная композиция с элементами оперы и рока, считающаяся одним из величайших произведений в истории музыки',
            'image' => 'https://source.unsplash.com/random/300x300/?queen',
            'audio' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'duration' => '5:55'
        ],
        [
            'id' => 2,
            'title' => 'Take Five',
            'artist' => 'Dave Brubeck',
            'genre' => 'джаз, кул-джаз, инструментальный',
            'mood' => 'расслабляющая, стильная, спокойная',
            'era' => '1950s',
            'tags' => 'джазовый стандарт, саксофон, 5/4',
            'description' => 'Классическая джазовая композиция в размере 5/4, одна из самых узнаваемых джазовых мелодий',
            'image' => 'https://source.unsplash.com/random/300x300/?jazz',
            'audio' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
            'duration' => '5:24'
        ],
        // ... остальные треки с добавленными mood, era, tags
    ];
}
?>