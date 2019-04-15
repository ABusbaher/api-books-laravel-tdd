<?php

namespace Tests\Unit;

use App\Language;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /**
     *@test
     */
    public function it_returns_language_id_either_language_is_just_created_or_already_exists()
    {
        $lang = factory('App\Language')->create(['language' => 'srb']);
        $request = Language::findOrCreateLanguage('srb');
        $this->assertEquals($request, $lang->id);

        $request2 = Language::findOrCreateLanguage('eng');
        $this->assertNotEquals($request2, $lang->id);
        $this->assertEquals($request2, 2);
    }

}
