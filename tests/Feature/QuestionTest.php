<?php

use App\Models\User;
use App\Role;

it('shows questions below quiz detail', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $kelas = createMateriKelasKuliah();
    $quiz = $kelas->quizzes()->create([
        'nama_quiz' => 'Quiz Soal',
        'uploaded_by' => $admin->id,
    ]);
    $quiz->questions()->create([
        'question_text' => 'Apa kepanjangan CPU?',
        'question_type' => 'single_choice',
        'question_option' => [
            ['text' => 'A', 'is_correct' => true],
            ['text' => 'B', 'is_correct' => false],
            ['text' => 'C', 'is_correct' => false],
        ],
        'points' => 10,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.kelas-kuliah.quiz.show', [$kelas, $quiz]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/QuizShow')
            ->has('quiz.questions', 1)
            ->where('quiz.questions.0.question_text', 'Apa kepanjangan CPU?')
            ->where('quiz.questions.0.question_type', 'single_choice')
            ->where('quiz.questions.0.points', 10));
});

it('stores multiple questions at once', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $kelas = createMateriKelasKuliah();
    $quiz = $kelas->quizzes()->create([
        'nama_quiz' => 'Quiz Bulk',
        'uploaded_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.kelas-kuliah.quiz.questions.store', [$kelas, $quiz]), [
            'questions' => [
                [
                    'question_text' => 'Pilih satu jawaban benar',
                    'question_type' => 'single_choice',
                    'question_option' => [
                        ['text' => 'A', 'is_correct' => true],
                        ['text' => 'B', 'is_correct' => false],
                        ['text' => 'C', 'is_correct' => false],
                    ],
                    'points' => 5,
                ],
                [
                    'question_text' => 'Jelaskan konsep OOP',
                    'question_type' => 'essay',
                    'question_option' => null,
                    'points' => 15,
                ],
                [
                    'question_text' => 'PHP adalah bahasa pemrograman?',
                    'question_type' => 'true_false',
                    'question_option' => [
                        ['text' => 'True', 'is_correct' => true],
                        ['text' => 'False', 'is_correct' => false],
                    ],
                    'points' => 2,
                ],
            ],
        ])
        ->assertRedirect(route('admin.kelas-kuliah.quiz.show', [$kelas, $quiz]));

    expect($quiz->questions()->count())->toBe(3)
        ->and($quiz->questions()->where('question_type', 'essay')->first()->question_option)->toBeNull()
        ->and($quiz->questions()->where('question_type', 'true_false')->first()->question_option)->toBe([
            ['text' => 'True', 'is_correct' => true],
            ['text' => 'False', 'is_correct' => false],
        ]);
});

it('rejects bulk questions from another class', function () {
    $admin = User::factory()->create(['role' => Role::Admin]);
    $kelas = createMateriKelasKuliah();
    $otherKelas = createMateriKelasKuliah();
    $quiz = $otherKelas->quizzes()->create([
        'nama_quiz' => 'Quiz Lain',
        'uploaded_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.kelas-kuliah.quiz.questions.store', [$kelas, $quiz]), [
            'questions' => [
                [
                    'question_text' => 'Soal invalid',
                    'question_type' => 'essay',
                    'points' => 5,
                ],
            ],
        ])
        ->assertNotFound();
});
