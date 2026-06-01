<?php

namespace Database\Seeders;

use App\Models\ChatPrompt;
use App\Support\ChatSpecialist;
use Illuminate\Database\Seeder;

class ChatPromptSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ChatSpecialist::keys() as $specialist) {
            $this->seedIntroPrompts($specialist, 'en');
            $this->seedIntroPrompts($specialist, 'es');
        }
    }

    private function seedIntroPrompts(string $specialist, string $locale): void
    {
        $answer = ChatSpecialist::introAnswer($specialist, $locale);

        $questions = $locale === 'es'
            ? [
                '¿Cuál es tu nombre?',
                '¿Cómo te llamas?',
                '¿Quién eres?',
                'Hola, ¿con quién hablo?',
                '¿Con quién estoy chateando?',
            ]
            : [
                "What's your name?",
                'What is your name?',
                'Who are you?',
                'Hi, who am I speaking with?',
                'Who am I chatting with?',
                'Introduce yourself',
            ];

        foreach ($questions as $index => $question) {
            ChatPrompt::updateOrCreate(
                [
                    'locale' => $locale,
                    'specialist' => $specialist,
                    'question' => $question,
                ],
                [
                    'answer' => $answer,
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
