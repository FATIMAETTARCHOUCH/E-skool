<?php

namespace Database\Seeders;

use App\Enums\StudentProgressStatus;
use App\Models\Course;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonVariant;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizRetake;
use App\Models\Result;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemediationFlowSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'admin')->first() ?? User::first();
        $groups = Group::query()->take(2)->get();

        if ($groups->isEmpty()) {
            return;
        }

        $course = Course::firstOrCreate(
            ['title' => 'Math Remediation Demo'],
            [
                'description' => 'Demo course for quiz remediation and retake states.',
                'category' => 'Mathematics',
                'level' => 'Middle School',
                'teacher_id' => $teacher?->id,
            ]
        );

        $course->groups()->syncWithoutDetaching($groups->pluck('id')->all());

        $lesson1 = Lesson::firstOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'Fractions Basics', 'tag' => 'core']
        );

        $lesson1Variant = Lesson::firstOrCreate(
            ['course_id' => $course->id, 'order' => 101],
            ['title' => 'Fractions Basics (Simplified)', 'tag' => 'remediation']
        );

        $lesson2 = Lesson::firstOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'Fractions Addition', 'tag' => 'core']
        );

        LessonVariant::updateOrCreate(
            ['original_lesson_id' => $lesson1->id, 'trigger' => 'quiz_failed'],
            ['variant_lesson_id' => $lesson1Variant->id]
        );

        $this->syncLessonText($lesson1, [
            'Understand numerator and denominator.',
            'Compare fractions with same denominator.',
        ]);
        $this->syncLessonText($lesson1Variant, [
            'Reminder: denominator = total equal parts.',
            'Use pizza slices to visualize fractions.',
        ]);
        $this->syncLessonText($lesson2, [
            'Add fractions with common denominators.',
            'Simplify the final fraction when possible.',
        ]);

        $quiz1 = Quiz::firstOrCreate(
            ['lesson_id' => $lesson1->id],
            ['title' => 'Quiz: Fractions Basics', 'is_active' => true, 'passing_score' => 70]
        );

        $quiz2 = Quiz::firstOrCreate(
            ['lesson_id' => $lesson2->id],
            ['title' => 'Quiz: Fractions Addition', 'is_active' => true, 'passing_score' => 70]
        );

        $quiz1Questions = $this->seedQuizQuestions($quiz1, [
            [
                'content' => 'In 3/5, what is the denominator?',
                'options' => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '5', 'correct' => true],
                    ['text' => '8', 'correct' => false],
                ],
            ],
            [
                'content' => 'Which fraction is equivalent to 1/2?',
                'options' => [
                    ['text' => '2/3', 'correct' => false],
                    ['text' => '3/6', 'correct' => true],
                    ['text' => '4/10', 'correct' => false],
                ],
            ],
        ]);

        $this->seedQuizQuestions($quiz2, [
            [
                'content' => '1/4 + 2/4 = ?',
                'options' => [
                    ['text' => '3/4', 'correct' => true],
                    ['text' => '3/8', 'correct' => false],
                    ['text' => '2/8', 'correct' => false],
                ],
            ],
        ]);

        $students = User::where('role', 'student')->take(3)->get();
        if ($students->isEmpty()) {
            return;
        }

        // Student 1: pass first attempt
        $student1 = $students->get(0);
        $this->seedProgress($student1, $lesson1, StudentProgressStatus::PASSED->value, true);
        $this->seedRetakeAndResult($student1, $quiz1, 1, true, 100, $quiz1Questions, true);

        // Student 2: fail first, then pass second -> passed_with_help
        if ($students->count() > 1) {
            $student2 = $students->get(1);
            $this->seedProgress($student2, $lesson1, StudentProgressStatus::PASSED_WITH_HELP->value, true);
            $this->seedProgress($student2, $lesson1Variant, StudentProgressStatus::IN_PROGRESS->value, false);
            $this->seedRetakeAndResult($student2, $quiz1, 1, false, 50, $quiz1Questions, false);
            $this->seedRetakeAndResult($student2, $quiz1, 2, true, 100, $quiz1Questions, true);
        }

        // Student 3: fail twice -> stuck
        if ($students->count() > 2) {
            $student3 = $students->get(2);
            $this->seedProgress($student3, $lesson1, StudentProgressStatus::STUCK->value, false);
            $this->seedProgress($student3, $lesson1Variant, StudentProgressStatus::IN_REMEDIATION->value, false);
            $this->seedRetakeAndResult($student3, $quiz1, 1, false, 50, $quiz1Questions, false);
            $this->seedRetakeAndResult($student3, $quiz1, 2, false, 50, $quiz1Questions, false);
        }
    }

    private function syncLessonText(Lesson $lesson, array $paragraphs): void
    {
        $order = 1;
        foreach ($paragraphs as $text) {
            LessonContent::updateOrCreate(
                [
                    'lesson_id' => $lesson->id,
                    'type' => 'text',
                    'order' => $order,
                ],
                ['value' => $text]
            );
            $order++;
        }
    }

    private function seedQuizQuestions(Quiz $quiz, array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $question = Question::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'content_text' => $item['content'],
                ],
                []
            );

            $optionMap = [];
            foreach ($item['options'] as $option) {
                $created = Option::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'content_text' => $option['text'],
                    ],
                    ['is_correct' => $option['correct']]
                );
                $optionMap[] = $created;
            }

            $result[] = [
                'question' => $question,
                'options' => $optionMap,
            ];
        }

        return $result;
    }

    private function seedProgress(User $student, Lesson $lesson, string $status, bool $completed): void
    {
        StudentProgress::updateOrCreate(
            ['user_id' => $student->id, 'lesson_id' => $lesson->id],
            [
                'status' => $status,
                'unlocked_at' => now()->subDays(1),
                'completed_at' => $completed ? now() : null,
                'time_spent_seconds' => 420,
            ]
        );
    }

    private function seedRetakeAndResult(User $student, Quiz $quiz, int $attempt, bool $passed, int $score, array $quizQuestions, bool $chooseCorrect): void
    {
        $retake = QuizRetake::updateOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => $student->id, 'attempt_number' => $attempt],
            [
                'status' => 'completed',
                'started_at' => now()->subMinutes(30 - $attempt),
                'completed_at' => now()->subMinutes(25 - $attempt),
            ]
        );

        Result::updateOrCreate(
            ['user_id' => $student->id, 'quiz_id' => $quiz->id, 'attempt_number' => $attempt],
            [
                'score' => $score,
                'is_passed' => $passed,
                'quiz_retake_id' => $retake->id,
            ]
        );

        foreach ($quizQuestions as $entry) {
            $question = $entry['question'];
            $options = collect($entry['options']);

            $option = $chooseCorrect
                ? $options->firstWhere('is_correct', true)
                : $options->firstWhere('is_correct', false);

            if (! $option) {
                continue;
            }

            $match = [
                'user_id' => $student->id,
                'question_id' => $question->id,
                'quiz_retake_id' => $retake->id,
            ];

            $values = ['option_id' => $option->id];

            if (Schema::hasColumn('answers', 'quiz_id')) {
                $match['quiz_id'] = $quiz->id;
                $values['quiz_id'] = $quiz->id;
            }

            DB::table('answers')->updateOrInsert($match, $values + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
