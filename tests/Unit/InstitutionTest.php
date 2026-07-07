<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Institution;
use Database\Factories\AccountFactory;
use Database\Factories\InstitutionFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $fillable = (new Institution())->getFillable();

        $this->assertEquals([
            'name',
            'type',
            'color',
            'slug',
            'is_active',
        ], $fillable);
    }

    public function test_casts()
    {
        $casts = (new Institution())->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
    }

    public function test_accounts_relationship()
    {
        $institution = InstitutionFactory::new()->create();
        $account = AccountFactory::new()->create(['institution_id' => $institution->id]);

        $this->assertInstanceOf(HasMany::class, $institution->accounts());
        $this->assertInstanceOf(Account::class, $institution->accounts->first());
        $this->assertTrue($institution->accounts->first()->is($account));
    }
}
