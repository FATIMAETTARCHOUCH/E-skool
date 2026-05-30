<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class TestValidatePostSize extends \Illuminate\Foundation\Http\Middleware\ValidatePostSize
{
    protected function getPostMaxSize()
    {
        return 100;
    }
}

class PostTooLargeTest extends TestCase
{
    public function test_post_too_large_exception_is_handled_gracefully(): void
    {
        Route::post('/_test_post_size', function () {
            return 'ok';
        })->middleware(TestValidatePostSize::class);

        $response = $this->withServerVariables([
            'CONTENT_LENGTH' => 200,
        ])->post('/_test_post_size');

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);

        $errors = session('errors')->get('file');
        $this->assertStringContainsString('dépassent la limite autorisée', $errors[0]);
    }
}
